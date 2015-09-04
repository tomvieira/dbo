<?

	require_once('lib/includes.php');

	$json_result = array();

	if($_REQUEST['action'] == 'set-logged-option')
	{
		if(loggedUser())
		{
			$opt = new opcao();
			$opt->set($_REQUEST[option], $_REQUEST[value], loggedUser());
			$json_result['ok'] = 1;
		}
		else
		{
			$json_result['message'] = '<div class="error">Erro: Usuário não logado</div>';
		}
	}

	echo json_encode($json_result);

?>