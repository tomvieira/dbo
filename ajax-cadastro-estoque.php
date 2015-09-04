<?
	require_once('lib/includes.php');

	CSRFCheckJson();

	$json_result = array();

	if(secureUrl())
	{
		$mat = new material($_GET['material_id']);
		if($mat->size())
		{
			if($_GET['action'] == 'cadastro')
			{
				if(hasPermission('insert', 'estoque'))
				{
					if($_POST['operacao'] == 'saida' && $_POST['quantidade'] > $mat->getQtdEstoqueGenerico())
					{
						$json_result['message'] = '<div class="error">Erro: A quantidade solicitada é maior que a disponível em estoque não inventoriado.</div>';
						echo json_encode($json_result);
						exit();
					}
					else
					{
						if(checkSubmitToken())
						{
							if($_POST['operacao'] == 'saida')
							{
								estoque::saidaRecursiva($mat->id, $_POST['quantidade'], array('comentario' => $_POST['comentario']));
							}
							else
							{
								$est = new estoque();
								$est->smartSet($_POST);
								$est->material = $mat->id;
								$est->data = $mat->now();
								$est->created_by = loggedUser();
								$est->custo_unitario = priceSQL($_POST['custo_unitario']);
								$est->save();
							}
							$json_result['message'] = '<div class="success">Estoque atualizado com sucesso!</div>';
							$json_result['eval'] = '$("#modal-dbo-small").foundation("reveal", "close"); reloadList();';
						}
						else
						{
							$json_result['message'] = '<div class="success">Aguarde o envio das informações...</div>';
						}
					}
				}
				else
				{
					$json_result['message'] = '<div class="error">Erro: permissão negada.</div>';
				}
			}
			elseif($_GET['action'] == 'remover-estoque')
			{
				if(hasPermission('delete', 'estoque'))
				{
					$est = new estoque($_GET['estoque_id']);
					$est->delete();
					$json_result['message'] = '<div class="success">Movimentação de estoque excluída com sucesso.</div>';
					$json_result['eval'] = 'reloadList()';
				}
				else
				{
					$json_result['message'] = '<div class="error">Erro: permissão negada.</div>';
				}
			}
		}
		else
		{
			$json_result['message'] = '<div class="error">Material inexistente.</div>';
		}
	}
	else
	{
		$json_result['message'] = '<div class="error">Tentativa de acesso insegura</div>';
	}

	echo json_encode($json_result);

?>
