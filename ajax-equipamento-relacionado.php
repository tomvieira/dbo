<?
	require_once('lib/includes.php');

	CSRFCheckJson();

	$json_result = array();

	if(secureUrl())
	{
		$serv = new requisicao_item($_GET['requisicao_item_id']);
		if($serv->size())
		{
			//relacionando o equipamento ao servico.
			if($_GET['action'] == 'relacionar-equipamento')
			{
				$rel = new requisicao_item_equipamento();
				$rel->requisicao_item = $serv->id;
				$rel->equipamento = $_POST['equipamento_id'];
				$rel->situacao = $rel->null();
				$rel->data = dboNow();
				$rel->created_by = loggedUser();
				$rel->save();
				$json_result['message'] = '<div class="success">Equipamento relacionado à requisição com sucesso.</div>';
				$json_result['eval'] = 'reloadEquipamentosRelacionados();';
			}
			//desrelaciona o equipamento do servico
			elseif($_GET['action'] == 'desrelacionar-equipamento')
			{
				$rel = new requisicao_item_equipamento("WHERE equipamento = '".$_GET['equipamento_id']."' AND requisicao_item = '".$serv->id."'");
				if($rel->size())
				{
					do {
						$rel->delete();
					}while($rel->fetch());
				}
				$json_result['message'] = '<div class="success">Relacionamento removido com sucesso.</div>';
				$json_result['eval'] = 'reloadEquipamentosRelacionados()';
			}
			//atualizando a situação do equipamento
			elseif($_GET['action'] == 'update-situacao-equipamento')
			{
				if(checkSubmitToken())
				{
					$eq = new equipamento($_GET['equipamento_id']);

					//atualiza o tipo de equipamento, se for o caso.
					if($_POST['tipo_equipamento'])
					{
						$eq->tipo_equipamento = $_POST['tipo_equipamento'];
						$eq->update();
					}

					$rel = new requisicao_item_equipamento();
					$rel->requisicao_item = $serv->id;
					$rel->equipamento = $eq->id;
					$rel->situacao = $_POST['situacao'];
					$rel->data = dboNow();
					$rel->created_by = loggedUser();
					$rel->comentario = $_POST['comentario'];
					$rel->local = $_POST['local'];
					$rel->save();

					//se houve movimentação, criar a movimentação do equipamento.
					if(
						$_POST['situacao'] == 'entrada' ||
						$_POST['situacao'] == 'saida' ||
						$_POST['situacao'] == 'assistencia_tecnica'
					)
					{

						$eq->local = $_POST['local'];
						$eq->local_detalhe = '';
						$eq->created_by_requisicao_item_equipamento = $rel->id;

						if($_POST['situacao'] == 'entrada')
						{
							$eq->responsavel = $_pes->nome;
							$eq->justificativa = "Entrada para manutenção. Requisição ".$serv->getSmartNumber().".";
							$eq->status = STATUS_EQUIPAMENTO_EM_MANUTENCAO;
						}
						elseif($_POST['situacao'] == 'saida')
						{
							$eq->responsavel = $serv->_requisicao->nome_requisitante.' ('.$serv->_requisicao->telefone_requisitante.')';
							$eq->justificativa = "Devolução do equipamento ao usuário após manutenção. Requisição ".$serv->getSmartNumber().".";
							$eq->status = STATUS_EQUIPAMENTO_EM_USO;
						}
						elseif($_POST['situacao'] == 'assistencia_tecnica')
						{
							$eq->responsavel = $_POST['responsavel_assistencia_tecnica'];
							$eq->justificativa = "Equipamento enviado à assistência técnica. Requisição ".$serv->getSmartNumber().".";
							$eq->status = STATUS_EQUIPAMENTO_NA_ASSISTENCIA_TECNICA;
						}
						
						//dando baixa no equipamento se a situação for esta
						if($_POST['situacao'] == 'baixa')
						{
							$eq->status = STATUS_EQUIPAMENTO_BAIXA;
						}

						$eq->update();


					}
					$json_result['message'] = '<div class="success">Status atualizado com sucesso.</div>';
					$json_result['eval'] = ' $.colorbox.close(); reloadEquipamentosRelacionados();';
				}
				else
				{
					$json_result['message'] = '<div class="success">Aguarde o envio das informações...</div>';
				}
			}
			//pegando a subtabela de detalhes
			elseif($_GET['action'] == 'get-tabela-detalhes')
			{
				ob_start();
				?>
				<tr class="sublist" style="display: none;">
					<td colspan="4">
					<?
						echo requisicao_item_equipamento::getTabelaDetalhes(array('requisicao_item_id' => $serv->id, 'equipamento_id' => $_GET['equipamento_id']));
					?>
					</td>
				</tr>
				<?
				$json_result['tabela'] = ob_get_clean();
			}
			//delete uma interacao
			if($_GET['action'] == 'delete-situacao-equipamento')
			{
				$rel = new requisicao_item_equipamento($_GET['requisicao_item_equipamento_id']);
				$rel->delete();
				$json_result['message'] = '<div class="success">Item deletado com sucesso.</div>';
				$json_result['eval'] = 'reloadEquipamentosRelacionados();';
			}
			//recarregando a lista de equipamentos relacionados
			elseif($_GET['action'] == 'reload-list')
			{
				$json_result['html']['#wrapper-tabela-equipamentos-relacionados'] = $serv->getTabelaEquipamentosRelacionados();
			}
		}
		else
		{
			$json_result['message'] = '<div class="error">Erro: O serviço não existe.</div>';
		}
	}
	else
	{
		$json_result['message'] = '<div class="error">Erro: Tentativa de acesso insegura</div>';
	}

	echo json_encode($json_result);

?>