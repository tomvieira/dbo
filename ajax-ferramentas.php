<?
	require_once('lib/includes.php');

	$json_result = array();

	if(hasPermission('painel-ferramentas'))
	{
		if($_GET['action'] == 'reenviar-avaliacao')
		{
			disparaEmail('usuario-servico-concluido', array('id_avaliacao' => $_GET['avaliacao_id']));
			$json_result['message'] = '<div class="success">Avaliação reenviada com sucesso.</div>';
			$json_result['html']['#button-reenviar-'.$_GET['avaliacao_id']] = '<span class="button tiny radius no-margin secondary"><i class="fa-check fa-fw single"></i></span>';
		}
		if($_GET['action'] == 'arquivar-requisicao')
		{
			if(!strlen(trim($_REQUEST['arquivo_morto'])))
			{
				$json_result['message'] = '<div class="error">Erro: Preencha uma justificativa válida.</div>';
			}
			else
			{
				$serv = new requisicao_item($_GET['requisicao_item_id']);
				if($serv->size())
				{
					$serv->arquivo_morto = $_REQUEST['arquivo_morto'];
					$serv->update();
					$json_result['message'] = '<div class="success">Sucesso!</div>';
					$json_result['eval'] = '$("tr[data-requisicao_item_id=\"'.$_GET['requisicao_item_id'].'\"]").fadeOut();';
				}
				else
				{
					$json_result['message'] = '<div class="error">Erro: Requisição inválida.</div>';
				}
			}
		}
	}
	else
	{
		$json_result['message'] = '<div class="error">Erro: permissão negada.</div>';
	}

	echo json_encode($json_result);

?>