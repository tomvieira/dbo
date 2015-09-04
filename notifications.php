<?
require_once('lib/includes.php');

$json_result = array();

if($_GET['hash'] > 0)
{
	$_sistema->startNotifications();

	//instanciando a pessoa
	$pessoa = new pessoa($_pes->unMaskId($_GET['hash']));

	//notificacoes especificas para servidores e estagiarios
	if(pessoaHasPermission($pessoa->id, 'notifications'))
	{

		/* definindo o contexto de notificacoes */
		$aux = $_opt->get('contexto_areas', $pessoa->id);
		$_system['contexto_areas'] = (($aux)?(explode("-", $aux)):(false));
		$aux = $_opt->get('notificacoes_areas', $pessoa->id);
		$_system['notificacoes_areas'] = (($aux)?(explode("-", $aux)):(false));

		if(pessoaHasPermission($pessoa->id, 'gerenciar-servicos'))
		{
			/* pegando primeiro as requisições novas */
			$novas = $_serv->getServicosStatus(STATUS_NOVO, $pessoa->id);
			if($novas !== false)
			{
				while($lin = dboFetchObject($novas))
				{

					$tipo_servico = new tipo_servico($lin->tipo_servico);

					$_sistema->notification->setStatus('alert');
					$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_painel_novos');
					$_sistema->notification->setTitle('Novo chamado - '.$tipo_servico->nome);
					$_sistema->notification->setDescription($lin->descricao);
					$_sistema->notification->setId($_sistema->slug.'-novo-chamado-'.$lin->id);
					$_sistema->notification->addContainer('pendente');
					$_sistema->notification->addContainer('novos');
					$_sistema->addNotification();
				}
			}

			/* pegando as requisições aguardando */
			$novas = $_serv->getServicosStatus(STATUS_AGUARDANDO_APROVACAO_DIRETORIA, $pessoa->id);
			if($novas !== false)
			{
				while($lin = dboFetchObject($novas))
				{

					$tipo_servico = new tipo_servico($lin->tipo_servico);

					$_sistema->notification->setStatus('warning');
					$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_painel_aguardando');
					$_sistema->notification->setTitle('Chamado aguardando - '.$tipo_servico->nome);
					$_sistema->notification->setDescription($lin->descricao);
					$_sistema->notification->setId($_sistema->slug.'-chamado-aguardando-'.$lin->id);
					$_sistema->notification->addContainer('aguardando');
					$_sistema->addNotification();
				}
			}

			/* pegando servicos finalizados */
			if(pessoaHasPermission($pessoa->id, 'finalizar-servicos'))
			{
				$novas = $_serv->getFinalizados();
				if($novas !== false)
				{
					while($lin = dboFetchObject($novas))
					{
						$_sistema->notification->setStatus('alert');
						$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_painel_atribuidos');
						$_sistema->notification->setTitle('Serviço finalizado');
						$_sistema->notification->setDescription($lin->descricao);
						$_sistema->notification->setId($_sistema->slug.'-servico-finalizado-'.$lin->id);
						$_sistema->notification->addContainer('pendente');
						$_sistema->notification->addContainer('atribuidos');
						$_sistema->addNotification();
					}
				}
			}

			/* feedbacks atrasados */
			$feeds = new requisicao_item();
			$feeds->getTotalFeedbacksAtrasados();
			if($feeds->size())
			{
				do {
					$_sistema->notification->setStatus('alert');
					$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_painel_feedbacks');
					$_sistema->notification->setTitle('Feedback atrasado ('.$feeds->dias_passados.' dia'.(($feeds->dias_passados > 1)?('s'):('')).')');
					$_sistema->notification->setDescription($feeds->descricao);
					$_sistema->notification->setId($_sistema->slug.'-servico-feedback-atrasado-'.$feeds->id);
					$_sistema->notification->addContainer('pendente');
					$_sistema->notification->addContainer('feedbacks');
					$_sistema->addNotification();
				}while($feeds->fetch());
			}
		}

		//notificacoes do calendário
		if(pessoaHasPermission($pessoa->id, 'calendario'))
		{
			//pegando manutenções de equipamento avisadas
			$obj = new equipamento_manutencao();
			$obj->getManutencoesAvisadas();
			if($obj->size())
			{
				do {
					$_sistema->notification->setStatus('warning');
					$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_calendario&equipamento_manutencao_id='.$obj->id);
					$_sistema->notification->setTitle('Manutenção programada');
					$_sistema->notification->setDescription($obj->tipo_equipamento_nome." ".$obj->marca_nome.(($obj->codigo)?(" - Código: ".$obj->codigo):('')));
					$_sistema->notification->setId($_sistema->slug.'-manutencao-programada-'.$obj->id);
					$_sistema->notification->addContainer('calendario');
					$_sistema->addNotification();
				}while($obj->fetch());
			}

			//pegando manutenções de equipamento atrasadas
			$obj = new equipamento_manutencao();
			$obj->getManutencoesAtrasadas();
			if($obj->size())
			{
				do {
					$_sistema->notification->setStatus('alert');
					$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_calendario&equipamento_manutencao_id='.$obj->id);
					$_sistema->notification->setTitle('Manutenção atrasada');
					$_sistema->notification->setDescription($obj->tipo_equipamento_nome." ".$obj->marca_nome.(($obj->codigo)?(" - Código: ".$obj->codigo):('')));
					$_sistema->notification->setId($_sistema->slug.'-manutencao-atrasada-'.$obj->id);
					$_sistema->notification->addContainer('calendario');
					$_sistema->addNotification();
				}while($obj->fetch());
			}

			//pegando servicos avisados
			$obj = new requisicao_item();
			$obj->getAvisadosPessoa($pessoa->id);
			if($obj->size())
			{
				do {
					$_sistema->notification->setStatus('warning');
					$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_calendario&requisicao_item_id='.$obj->id);
					$_sistema->notification->setTitle('Serviço agendado - '.(($obj->data_agendada == date('Y-m-d'))?('Hoje'):(dboDate('l, d --- F', strtotime($obj->data_agendada)))));
					$_sistema->notification->setDescription($obj->descricao);
					$_sistema->notification->setId($_sistema->slug.'-servico-agendado-'.$obj->id);
					$_sistema->notification->addContainer('calendario');
					$_sistema->addNotification();
				}while($obj->fetch());
			}

			//pegando servicos atrasados
			$obj = new requisicao_item();
			$obj->getAtrasadosPessoa($pessoa->id);
			if($obj->size())
			{
				do {
					$_sistema->notification->setStatus('alert');
					$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_calendario&requisicao_item_id='.$obj->id);
					$_sistema->notification->setTitle('Serviço '.(($obj->data_agendada == date('Y-m-d'))?('agendado - Hoje'):(' atrasado '.ago($obj->data_agendada))));
					$_sistema->notification->setDescription($obj->descricao);
					$_sistema->notification->setId($_sistema->slug.'-servico-atrasado-'.$obj->id);
					$_sistema->notification->addContainer('calendario');
					$_sistema->addNotification();
				}while($obj->fetch());
			}
		}

		//notificacoes de inventario
		if(pessoaHasPermission($pessoa->id, 'gerenciar-inventario'))
		{
			if(ALERTAR_EQUIPAMENTOS_SEM_CODIGO)
			{
				//pegando todos os equipamentos sem codigo
				$sql = "
					SELECT
						equipamento.*,
						tipo_equipamento.nome as equipamento_nome
					FROM
						equipamento
					INNER JOIN tipo_equipamento ON
						tipo_equipamento.id = equipamento.tipo_equipamento
					WHERE 
						equipamento.codigo IS NULL AND
						equipamento.externo = 0
					ORDER BY 
						equipamento_nome
				";
				$obj = new equipamento();
				$obj->query($sql);
				if($obj->size())
				{
					do {
						$_sistema->notification->setStatus('warning');
						$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_painel_inventario');
						$_sistema->notification->setTitle('Equipamento sem código de inventário');
						$_sistema->notification->setDescription($obj->getSmartName());
						$_sistema->notification->setId($_sistema->slug.'-equip-sem-cod-'.$obj->id);
						$_sistema->notification->addContainer('materiais');
						$_sistema->notification->addContainer('inventario');
						$_sistema->addNotification();
					}while($obj->fetch());
				}
			}

			//pegando todos os equipamentos emprestados
			$sql = "
				SELECT
					equipamento.*,
					tipo_equipamento.nome as equipamento_nome
				FROM
					equipamento
				INNER JOIN tipo_equipamento ON
					tipo_equipamento.id = equipamento.tipo_equipamento
				WHERE 
					equipamento.status = ".STATUS_EQUIPAMENTO_NAO_LOCALIZADO."
				ORDER BY 
					equipamento_nome
			";
			$obj = new equipamento();
			$obj->query($sql);
			if($obj->size())
			{
				do {
					$_sistema->notification->setStatus('warning');
					$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_painel_inventario');
					$_sistema->notification->setTitle('Equipamento não localizado');
					$_sistema->notification->setDescription($obj->getSmartName());
					$_sistema->notification->setId($_sistema->slug.'-equip-n-loc-'.$obj->id);
					$_sistema->notification->addContainer('materiais');
					$_sistema->notification->addContainer('inventario');
					$_sistema->addNotification();
				}while($obj->fetch());
			}
		}

		//notificacoes de estoque
		if(pessoaHasPermission($pessoa->id, 'gerenciar-estoque'))
		{
			$obj = new material("WHERE quantidade_alerta > 0 AND quantidade_estoque < quantidade_alerta");
			if($obj->size())
			{
				do {
					if($obj->quantidade_estoque == 0)
					{
						$_sistema->notification->setStatus('alert');
						$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_painel_estoque');
						$_sistema->notification->setTitle('Material acabou do estoque');
						$_sistema->notification->setDescription($obj->nome.'.');
						$_sistema->notification->setId($_sistema->slug.'-mat-est-acabando-'.$obj->id);
						$_sistema->notification->addContainer('materiais');
						$_sistema->notification->addContainer('estoque');
						$_sistema->addNotification();
					}
					else
					{
						$_sistema->notification->setStatus('warning');
						$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_painel_estoque');
						$_sistema->notification->setTitle('Material acabando do estoque');
						$_sistema->notification->setDescription($obj->nome.' - resta'.(($obj->quantidade_estoque > 1)?('m'):('')).' '.$obj->quantidade_estoque.' '.$obj->unidade);
						$_sistema->notification->setId($_sistema->slug.'-mat-est-acabando-'.$obj->id);
						$_sistema->notification->addContainer('materiais');
						$_sistema->notification->addContainer('estoque');
						$_sistema->addNotification();
					}
				}while($obj->fetch());
			}
		}

		//notificacoes de equipamento indefinido
		if(pessoaHasPermission($pessoa->id, 'equipamentos-relacionados-notifications'))
		{
			//pegando equipamentos sem interação
			$obj = equipamento::getEquipamentosSemInteracao(array('return' => 'object'));
			if($obj->size())
			{
				do {
					$_sistema->notification->setStatus('warning');
					$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_painel_equipamentos_manutencao_abertos');
					$_sistema->notification->setTitle('Equipamento sem interação');
					$_sistema->notification->setDescription($obj->getSmartName(array('color' => false)).' - Req. '.$obj->requisicao.'/'.$obj->requisicao_item_numero);
					$_sistema->notification->setId($_sistema->slug.'-equip-manut-sem-int-'.$obj->id);
					$_sistema->notification->addContainer('materiais');
					$_sistema->notification->addContainer('equipamentos_manutencao');
					$_sistema->addNotification();
				}while($obj->fetch());
			}

			//pegando equipamentos indefinidos
			$obj = equipamento::getEquipamentosIndefinidos(array('return' => 'object'));
			if($obj->size())
			{
				do {
					$_sistema->notification->setStatus('alert');
					$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_painel_equipamentos_manutencao_abertos');
					$_sistema->notification->setTitle('Equipamento indefinido');
					$_sistema->notification->setDescription($obj->getSmartName(array('color' => false)));
					$_sistema->notification->setId($_sistema->slug.'-equip-indef-'.$obj->id);
					$_sistema->notification->addContainer('materiais');
					$_sistema->notification->addContainer('equipamentos_manutencao');
					$_sistema->addNotification();
				}while($obj->fetch());
			}
		}
	}

	//notificacoes de avaliação pendente, por exemplo. não precisa ter a permissão custom "notifications"
	//avalicoes concluidas que não foram avaliadas
	$res = $_serv->getItensUsuario(STATUS_CONCLUIDO);
	if($res !== false)
	{
		while($lin = dboFetchObject($res))
		{
			$_sistema->notification->setStatus(((ALERTAR_AVALIACOES_PENDENTES)?('alert'):('warning')));
			$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_avaliacao_pendente&id='.$lin->id);
			$_sistema->notification->setTitle('Avaliação pendente');
			$_sistema->notification->setDescription($lin->descricao);
			$_sistema->notification->setId($_sistema->slug.'-avaliacao-pendente-'.$lin->id);
			$_sistema->notification->addContainer('acompanhamento');
			$_sistema->addNotification();
		}
	}

	//avaliacoes que precisam de um feedback para a seção
	$res = $_serv->getItensUsuario(STATUS_AGUARDANDO_REQUISITANTE);
	if($res !== false)
	{
		while($lin = dboFetchObject($res))
		{
			$_sistema->notification->setStatus('alert');
			$_sistema->notification->setUrl($_sistema->url.'/intent.php?action=go_avaliacao_pendente&id='.$lin->id);
			$_sistema->notification->setTitle('Serviço aguardando seu contato');
			$_sistema->notification->setDescription($lin->descricao);
			$_sistema->notification->setId($_sistema->slug.'-servico-aguardando-'.$lin->id);
			$_sistema->notification->addContainer('acompanhamento');
			$_sistema->addNotification();
		}
	}

	$json_result = $_sistema->getNotificationsArray();
}

echo json_encode($json_result);

?>