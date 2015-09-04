<?
	require_once('lib/includes.php');

	$pagina = $_SERVER['HTTP_REFERER'];
	$query_string = array();
	$anchor = '';

	/* acessar o calendario */
	if($_GET['action'] == 'go_painel_novos')
	{
		$painel = new Painel();
		$painel->setParam('status', 'novos');
		$pagina = 'painel.php';
	}

	if($_GET['action'] == 'go_painel_aguardando')
	{
		$painel = new Painel();
		$painel->setParam('status', 'aguardando');
		$pagina = 'painel.php';
	}

	if($_GET['action'] == 'go_painel_atribuidos')
	{
		$painel = new Painel();
		$painel->setParam('status', 'atribuidos');
		$pagina = 'painel.php';
	}

	if($_GET['action'] == 'go_painel_feedbacks')
	{
		$painel = new Painel();
		$painel->setParam('status', 'feedbacks-atrasados');
		$pagina = 'painel.php';
	}

	if($_GET['action'] == 'go_calendario')
	{
		$painel = new Painel();
		$pagina = 'calendario.php';
	}

	if($_GET['action'] == 'go_painel_estoque')
	{
		$painel = new Painel();
		$painel->setParam('painel_material_modulo', 'material');
		$pagina = 'painel-materiais.php';
	}

	if($_GET['action'] == 'go_painel_inventario')
	{
		$painel = new Painel();
		$painel->setParam('painel_material_modulo', 'equipamento');
		$pagina = 'painel-materiais.php';
	}

	if($_GET['action'] == 'go_painel_equipamentos_manutencao_abertos')
	{
		$painel = new Painel();
		$painel->setParam('painel_material_modulo', 'manutencao');
		$painel->clearParamMulti('painel_material_show_list_manutencao');
		$painel->setParamMulti('painel_material_show_list_manutencao', 'requisicoes_abertas');
		$pagina = 'painel-materiais.php';
	}

	if($_GET['action'] == 'go_avaliacao_pendente')
	{
		$painel = new Painel();
		$painel->setParam('status', 'atribuidos');
		$pagina = 'acompanhamento.php';
		$query_string[] = 'id='.$_GET['id'];
		$anchor = '#servico-'.$_GET['id'];
	}

	if($_GET['action'] == 'show_modal_requisicao')
	{
		dboAuth();
		$_SESSION[sysId()]['show_modal_requisicao']['requisicao_id'] = $_GET['requisicao_id'];
		$_SESSION[sysId()]['show_modal_requisicao']['requisicao_item_id'] = $_GET['requisicao_item_id'];
		$painel = new Painel();
		$painel->setParam('status', 'novos');
		$pagina = 'painel.php';
	}

	header("Location: ".$pagina.((sizeof($query_string))?("?"):('')).implode("&", $query_string).$anchor);

?>