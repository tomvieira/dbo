<?
	require_once('lib/includes.php');

	if(!secureUrl())
	{
		$json_result['message'] = '<div class="error">Erro: Tentativa de acesso inválida.</div>';
		echo json_encode($json_result);
		exit();
	}

	if(!hasPermission('gerenciar-servicos'))
	{
		$json_result['message'] = '<div class="error">Erro: Você não tem permissão para esta operação.</div>';
		echo json_encode($json_result);
		exit();
	}

	//instanciando e verificando o serviço
	$serv = new requisicao_item($_GET['servico_id']);
	
	if(!$serv->size())
	{
		$json_result['message'] = '<div class="error">Erro: O serviço solicitado não existe ou foi apagado</div>';
		echo json_encode($json_result);
		exit();
	}

	//clonando o servico para realizar comparações
	$last_status = $serv->status;

	$error = false;
	$json_result = array();

	//tentou atribuir, mas não atribuiu ninguém.
	if(
		(
			$_POST['status'] == STATUS_ATRIBUIDO ||
			$_POST['status'] == STATUS_EM_ANDAMENTO ||
			$_POST['status'] == STATUS_AGUARDANDO_REQUISITANTE
		) &&
		!sizeof($_POST['responsaveis_atribuidos'])
	)
	{
		$json_result['message'] = '<div class="error">Erro: É necessário atribuir ao menos 1 responsável para este serviço.</div>';
		$error = true;
	}
	//não setou o local da requisição
	elseif($_POST['local'] <= 0)
	{
		$json_result['message'] = '<div class="error">Erro: Selecione um local para o serviço.</div>';
		$error = true;
	}
	//está tentando fechar uma requisição com equipamentos relacionados pendentes
	elseif($_POST['status'] == STATUS_CONCLUIDO && $serv->temEquipamentosRelacionadosPendentes())
	{
		$error = true;
		$json_result['eval'] = 'alert("Erro: Operação não permitida.\n\nVocê não pode concluir este serviço pois existe(m) 1 ou mais equipamento(s) relacionado(s) com status pendente(s). Todos os equipamentos relacionados precisam estar em uma das seguintes situações:\n\n- Devolvidos ao usuário\n- Com serviço realizado no local\n- Com baixa\n\nPor favor, acerte os status dos equipamentos antes de concluir este serviço.")';
	}
	//está tentando agendar o fechamento da requisição, mas há equipamentos relacionados pendentes.
	elseif(strlen(trim($_POST['data_agendada_conclusao'])) && $serv->temEquipamentosRelacionadosPendentes())
	{
		$error = true;
		$json_result['eval'] = 'alert("Erro: Operação não permitida.\n\nVocê não pode agendar a conclusão deste serviço pois existe(m) 1 ou mais equipamento(s) relacionado(s) com status pendente(s). Todos os equipamentos relacionados precisam estar em uma das seguintes situações:\n\n- Devolvidos ao usuário\n- Com serviço realizado no local\n- Com baixa\n\nPor favor, acerte os status dos equipamentos antes de agendar a conclusão deste serviço.")';
	}

	//processando o chamado
	if(!$error)
	{
		//variaveis para controlas as atribuições
		$novos_atribuidos = array();
		$novos_desatribuidos = array();

		//alocando as informações do POST ao chamado
		$serv->smartSet($_POST);
		$serv->data_agendada = ((strlen(trim($_POST['data_agendada'])))?(dataSQL($_POST['data_agendada'])):($serv->null()));
		$serv->data_agendada_conclusao = ((strlen(trim($_POST['data_agendada_conclusao'])))?(dataHoraSQL($_POST['data_agendada_conclusao'])):($serv->null()));
		$serv->updated_on = $serv->now();
		$serv->update();

		//pegando todo mundo já atribuido
		$servidores_ja_atribuidos = (array)$serv->getServidoresAtribuidos();
		$prestadores_ja_atribuidos = (array)$serv->getPrestadoresAtribuidos();
		$responsaveis_ja_atribuidos = array_filter(array_merge($servidores_ja_atribuidos, $prestadores_ja_atribuidos));

		$responsaveis_atribuidos_post = (array)$_POST['responsaveis_atribuidos'];

		//descobrindo os novos atribuidos
		foreach($responsaveis_atribuidos_post as $id)
		{
			if(!in_array($id, $responsaveis_ja_atribuidos))
			{
				$novos_atribuidos[] = dboescape($id);
			}
		}

		//descobrindo os desatribuidos
		foreach($responsaveis_ja_atribuidos as $id)
		{
			if(!in_array($id, $responsaveis_atribuidos_post))
			{
				$novos_desatribuidos[] = dboescape($id);
			}
		}

		//criando as atribuições para os novos, e disparando os emails.
		if(sizeof($novos_atribuidos))
		{
			foreach($novos_atribuidos as $id)
			{
				$sql = "INSERT INTO requisicao_item_servidor (item, servidor) VALUES ('".$serv->id."', '".$id."')";
				dboQuery($sql);
				//disparando emails (não faz sentido disparar para si mesmo, só para os outros
				if($id != loggedUser())
				{
					disparaEmail('servidor-nova-atribuicao', array('id_servico' => $serv->id, 'id_servidor' => $id, 'nome_atribuidor' => $_pes->getShortName()));
				}
			}
		}

		//removendo as atribuições dos desatribuidos
		if(sizeof($novos_desatribuidos))
		{
			$sql = "DELETE FROM requisicao_item_servidor WHERE item = '".$serv->id."' AND servidor IN (".implode(',', $novos_desatribuidos).")";
			dboQuery($sql);
		}

		//agora podemos controlar os historicos
		$last_hist = new historico("WHERE requisicao_item = '".$serv->id."' ORDER BY data DESC LIMIT 1");
		if($last_hist->size())
		{
			if(
				$serv->status != $last_hist->status ||               //mudou o status
				$serv->prioridade != $last_hist->prioridade ||       //mudou a prioridade
				strlen(trim($_POST['comentario'])) > 0 || //o supervisor adicionou um comentario
				sizeof($novos_desatribuidos) ||                      //alguem foi desatribuido
				sizeof($novos_atribuidos)                            //alguem foi atribuido
			)
			{
				$hist = new historico();
				$hist->requisicao_item = $serv->id;
				$hist->created_by = loggedUser();
				$hist->data = $hist->now();
				$hist->prioridade = $serv->prioridade;
				$hist->status = $serv->status;
				$hist->comentario = $_POST['comentario'];
				$hist->encodeAtribuicoes($novos_atribuidos, $novos_desatribuidos);

				//verificando se já foi atribuido/aprovado, antes de salvar
				$nunca_aprovado = $serv->nuncaAntes(STATUS_APROVADO);
				$nunca_atribuido = $serv->nuncaAntes(STATUS_ATRIBUIDO);
				
				//salvando
				$hist->save();

				//finalmente, disparando os emails. Isso só faz sentido quando um histórico é criado.
				if($_POST['enviar_email'] == 1)
				{
					//se o chamado foi aprovado, e nunca foi aprovado ou atribuido antes
					if($serv->status == STATUS_APROVADO && $last_status != STATUS_APROVADO && $nunca_aprovado && $nunca_atribuido )
					{
						disparaEmail('usuario-servico-aprovado', array('id_servico' => $serv->id));
					}
					//se o chamado foi atribuido, e nunca antes foi atribuido
					elseif($serv->status == STATUS_ATRIBUIDO && $last_status != STATUS_ATRIBUIDO && $nunca_atribuido)
					{
						disparaEmail('usuario-servico-atribuido', array('id_servico' => $serv->id));
					}
					//chamado foi colocado em aguardando
					elseif($serv->status == STATUS_AGUARDANDO_REQUISITANTE)
					{
						disparaEmail('usuario-servico-aguardando-requisitante', array('id_servico' => $serv->id));
					}
					//
					elseif($serv->status == STATUS_CONCLUIDO && $last_status != STATUS_CONCLUIDO)
					{
						$id_avaliacao = $serv->createAvaliacao();
						disparaEmail('usuario-servico-concluido', array('id_avaliacao' => $id_avaliacao));
					}
					//qualquer outro caso
					else
					{
						disparaEmail('usuario-servico-atualizado', array('id_servico' => $serv->id));
					}
				}
			}
		}

		$json_result['message'] = '<div class="success">Requisição '.$serv->requisicao.'/'.$serv->numero.' salva com sucesso!</div>';
		$json_result['eval'] = 'gerenciarRequisicao('.$serv->requisicao.', '.$serv->id.')';
	}
	
	echo json_encode($json_result);

?>