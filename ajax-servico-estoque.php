<?
	require_once('lib/includes.php');

	$json_result = array();

	CSRFCheckJson();

	if(secureUrl())
	{
		$serv = new requisicao_item($_GET['requisicao_item_id']);
		if($serv->size())
		{
			//atribuindo material ao servico
			if($_GET['action'] == 'atribuir-material')
			{
				if(hasPermission('atribuir-estoque') || hasPermission('atribuir-inventario'))
				{
					//validando tudo!
					if($_POST['material_id'] && strlen(trim($_POST['material_nome']))) 
					{
						if(intval($_POST['quantidade']) > 0)
						{
							$mat = new material($_POST['material_id']);
							if($mat->size())
							{
								//antes de mais nada, checar se não há problema de estouro de estoque conjugado
								//se pedir mais do que o estoque generico e não estiver marcado um equipamento no pedido
								if($_POST['quantidade'] > $mat->getQtdEstoqueGenerico() && !$_POST['equipamento_id'])
								{
									if($mat->getQtdEstoqueInventoriado() > 0)
									{
										$json_result['eval'] = 'alert("Permissão negada:\n\n'.$mat->nome.' ('.$_POST['quantidade'].' '.$mat->unidade.')\n\nA quantidade solicitada não pode ser atribuída à requisição pois 1 ou mais itens estão inventoriados.\n\nPara atribuir itens inventoriados utilize o campo \"Equipamento de invetário\".")';
									}
									else
									{
										$json_result['eval'] = 'alert("Permissão negada:\n\n'.$mat->nome.' ('.$_POST['quantidade'].' '.$mat->unidade.')\n\nA quantidade solicitada não pode ser atribuída à requisição pois não está disponível em estoque.")';
									}
								}
								else
								{
									$params = array(
										'data' => dboNow(),
										'comentario' => 'Utilizado no serviço '.$serv->_requisicao->id.'/'.$serv->numero,
										'requisicao_item' => $serv->id
									);
									if($_POST['equipamento_id'] && strlen(trim($_POST['equipamento_nome'])))
									{
										$eq = new equipamento($_POST['equipamento_id']);
										$eq->data = dboNow();
										$eq->local = $serv->local;
										$eq->local_detalhe = '';
										$eq->justificativa = 'Utilizado no serviço '.$serv->_requisicao->id.'/'.$serv->numero;
										$eq->responsavel = $serv->_requisicao->nome_requisitante.' ('.$serv->_requisicao->telefone_requisitante.')';
										$eq->status = STATUS_EQUIPAMENTO_CONSUMIDO;
										$eq->created_by_requisicao_item = $serv->id;
										$eq->update();
										
										$params['equipamento'] = $eq->id;
									}
									estoque::saidaRecursiva($mat->id, $_POST['quantidade'], $params);

									$json_result['message'] = '<div class="success">Material atribuído com sucesso ao serviço.</div>';
									$json_result['html']['#wrapper-tabela-materiais-utilizados'] = $serv->getTabelaMateriaisUtilizados();
									$json_result['eval'] = '$("#estoque_material, #estoque_material_aux, #estoque_quantidade, #estoque_equipamento, #estoque_equipamento_aux").val(\'\').prop("readonly", false).removeClass("ok"); $("#estoque_unidade_aux").html(""); setTimeout(function(){ formInit(); }, 500);';
								}
							}
							else
							{
								$json_result['message'] = '<div class="error">Erro: Material inexistente.</div>';
							}
						}
						else
						{
							$json_result['message'] = '<div class="error">Erro: Digite uma quantidade.</div>';
						}
					}
					else
					{
						$json_result['message'] = '<div class="error">Erro: Selecione um material.</div>';
					}
				}
				else
				{
					$json_result['message'] = '<div class="error">Erro: permissão negada.</div>';
				}
			}
			elseif($_GET['action'] == 'desatribuir-material')
			{
				if(hasPermission('atribuir-estoque') || hasPermission('atribuir-inventario'))
				{
					$est = new estoque($_GET['estoque_id']);
					$est->delete();
					$json_result['message'] = '<div class="success">Material removido do serviço com sucesso.</div>';
					$json_result['html']['#wrapper-tabela-materiais-utilizados'] = $serv->getTabelaMateriaisUtilizados();
					$json_result['eval'] = 'setTimeout(function(){ formInit(); }, 500)';
				}
				else
				{
					$json_result['message'] = '<div class="error">Erro: permissão negada.</div>';
				}
			}
			elseif($_GET['action'] == 'incluir-custo-adicional')
			{
				//cheganco se o cara digitou o que precisa
				if(!strlen(trim($_POST['descricao'])))
				{
					$json_result['message'] = '<div class="error">Erro: Preencha a descrição do custo adicional.</div>';
					$json_result['eval'] = '$(".trigger-waiting").removeClass(".trigger-waiting").addClass("trigger-incluir-custo-adicional")';
				}
				else
				{
					$sql = "INSERT INTO requisicao_item_custo_adicional (requisicao_item, descricao, custo) VALUES ('".$serv->id."', '".dboescape($_POST['descricao'])."', '".priceSQL($_POST['custo'])."');";
					dboQuery($sql);
					$json_result['message'] = '<div class="success">Custo adicional incluído com sucesso.</div>';
					$json_result['html']['#wrapper-tabela-materiais-utilizados'] = $serv->getTabelaMateriaisUtilizados();
					$json_result['eval'] = 'setTimeout(function(){ formInit(); }, 500)';
				}
			}
			elseif($_GET['action'] == 'remover-custo-adicional')
			{
				$sql = "DELETE FROM requisicao_item_custo_adicional WHERE id = '".dboescape($_GET['custo_adicional_id'])."';";
				dboQuery($sql);
				$json_result['message'] = '<div class="success">Custo adicional removido com sucesso.</div>';
				$json_result['html']['#wrapper-tabela-materiais-utilizados'] = $serv->getTabelaMateriaisUtilizados();
				$json_result['eval'] = 'setTimeout(function(){ formInit(); }, 500)';
			}
		}
		else
		{
			$json_result['message'] = '<div class="error">Serviço inexistente.</div>';
		}
	}
	else
	{
		$json_result['message'] = '<div class="error">Tetantiva de acesso insegura.</div>';
	}
	
	echo json_encode($json_result);

?>