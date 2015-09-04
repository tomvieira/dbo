<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'estoque' ========================================== AUTO-CREATED ON 04/03/2015 09:43:32 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('estoque'))
{
	class estoque extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct('estoque');
			if($foo != '')
			{
				if(is_numeric($foo))
				{
					$this->id = $foo;
					$this->load();
				}
				elseif(is_string($foo))
				{
					$this->loadAll($foo);
				}
			}
		}

		//your methods here

		function getTabelaEstoque($params = array())
		{
			extract($params);

			$sql = "
				SELECT * FROM estoque WHERE material = '".$material_id."' ORDER BY id ASC
			";
			$this->query($sql);
			ob_start();
			if($this->size())
			{
				?>
				<div id="wrapper-tabela-estoque">
					<div class="row">
						<div class="large-12 columns">
							<table class="list no-margin">
								<thead>
									<tr>
										<th>Data</th>
										<th>Operação</th>
										<th><span class="help" title="Número do serviço que utilizou este equipamento">Serv.</span></th>
										<th class="text-center"><span class="help" title="Vínculo com equipamento do inventário da seção">Equip.</span></th>
										<th class="text-center"><span class="help" title="Comentário do servidor sobre esta operação">Coment.</span></th>
										<th class="text-right help" title="Custo unitário do material">Custo Uni. (R$)</th>
										<th class="text-right">Qtd. (<?= $this->_material->unidade ?>)</th>
										<th style="width: 1px;"></th>
									</tr>
								</thead>
								<tbody>
									<?
										$total = 0;
										do {
											if($this->operacao == 'saida')
											{
												$total -= $this->quantidade;
											}
											else
											{
												$total += $this->quantidade;
											}
											?>
											<tr title="Operação realizada por <?= $this->_created_by->getShortName(); ?>">
												<td data-title="Data"><span class="help" title="<?= dboDate('j --- F --- Y, H:i', strtotime($this->data)) ?>"><?= dboDate('d/m/Y', strtotime($this->data)) ?></span></td>
												<td data-title="Operação"><i class="fa-<?= (($this->operacao == 'entrada')?('arrow-circle-left color primary'):('spacer')) ?> fa-fw font-14"></i><?= $this->getValue('operacao', $this->operacao) ?><i class="fa-<?= (($this->operacao == 'saida')?('arrow-circle-right color light'):('spacer')) ?> fa-fw font-14"></i></td>
												<td data-title="Requisição"><?= (($this->requisicao_item)?('<a href="javascript:void(null)" onClick="gerenciarRequisicao('.$this->_requisicao_item->requisicao.', '.$this->_requisicao_item->id.')">'.$this->_requisicao_item->requisicao.'/'.$this->_requisicao_item->numero.'</a>'):('')) ?></td>
												<td data-title="Equipamento" class="text-center-for-medium-up"><?= (($this->equipamento)?($this->_equipamento->getLinkTag()):('')) ?></td>
												<td data-title="Comentário" class="text-center-for-medium-up"><?= ((strlen(trim($this->comentario)))?('<span class="font-14 has-tip tip-top" data-tooltip title="'.$this->comentario.'"><i class="fa-comment-o fa-fw"></i></span>'):('')) ?></td>
												<td data-title="Custo unitário" class="text-right-for-medium-up"><?= reais($this->custo_unitario) ?></td>
												<td data-title="Quantidade" class="text-right-for-medium-up"><span class="color <?= $this->getOperacaoColor(); ?>"><?= $this->quantidade ?> <?= $this->getOperacaoSinal() ?></span></td>
												<td class="control-icons text-right">
													<?
														if(!$this->equipamento && !$this->requisicao_item && hasPermission('delete', 'estoque'))
														{
															?><a href="<?= secureUrl('ajax-cadastro-estoque.php?action=remover-estoque&material_id='.$this->material.'&estoque_id='.$this->id.'&'.CSRFVar()) ?>" class="trigger-remover-estoque peixe-json" data-confirm="Tem certeza que deseja excluir esta movimentação de estoque?"><i class="fa-times single"></i></a><?
														}
													?>
												</td>
											</tr>
											<?
										}while($this->fetch());
									?>
									<tfoot>
										<tr>
											<td colspan="6" class="text-right" style="border-top: 2px solid #000;"><strong>Estoque Atual</strong></td>
											<td class="text-right" style="border-top: 2px solid #000;"><strong><span class="color <?= (($total > 0)?(''):('alert')) ?>"><?= $total ?></span></strong></td>
											<td style="border-top: 2px solid #000;"></td>
										</tr>
									</tfoot>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<?
			}
			else
			{
				?>
				<div id="wrapper-tabela-estoque">
					<p class="text-center no-margin">- Não há estoque cadastrado deste material -</p>
				</div>
				<?				
			}
			return ob_get_clean();
		}

		function getOperacaoSinal()
		{
			if($this->operacao == 'saida')
			{
				return '-';
			}
			return '+';
		}

		function getOperacaoColor()
		{
			if($this->operacao == 'saida')
			{
				return 'alert';
			}
		}

		function save()
		{
			$ret = parent::save();
			if($this->operacao == 'entrada')
			{
				$this->quantidade_disponivel = $this->quantidade;
			}
			$this->update();
			$this->_material->updateQtdEstoque();
			return $ret;
		}

		function update()
		{
			$ret = parent::update();
			$this->_material->updateQtdEstoque();
			//trocou de um material para o outro... precisa atualizar os dois estoques.
			if($this->pocket('material_anterior') && $this->pocket('material_anterior') != $this->material)
			{
				$mat = new material($this->pocket('material_anterior'));
				$mat->updateQtdEstoque();
			}
			return $ret;
		}

		function delete()
		{
			$ret = parent::delete();

			//devolver a quantidade para o lote de entrada
			if($this->lote_entrada)
			{
				$lote_entrada = new estoque($this->lote_entrada);
				$lote_entrada->quantidade_disponivel += $this->quantidade;
				$lote_entrada->update();
			}
			$this->_material->updateQtdEstoque();
			if($this->equipamento && $this->requisicao_item)
			{
				$this->_equipamento->status = STATUS_EQUIPAMENTO_NO_ESTOQUE;
				$this->_equipamento->update();
				$mov = new equipamento_movimentacao('WHERE created_by_requisicao_item = "'.$this->requisicao_item.'"');
				$mov->delete();
			}
			return $ret;
		}

		function forceDelete()
		{
			$ret = parent::forceDelete();
			$this->_material->updateQtdEstoque();
			return $ret;
		}

		static function saidaRecursiva($material_id, $quantidade, $params = array())
		{
			extract($params);

			//pegando de qual lote este material irá sair
			$lote_entrada = new estoque("WHERE material = '".$material_id."' AND quantidade_disponivel > 0 AND equipamento ".(($equipamento)?(" = '".$equipamento."' "):(" IS NULL "))." ORDER BY id LIMIT 1");

			$quantidade_transacao = min($quantidade, $lote_entrada->quantidade_disponivel);
			$quantidade_restante = $quantidade - $quantidade_transacao;

			//criando a saida
			$est = new estoque();
			$est->material = $material_id;
			$est->operacao = 'saida';
			$est->quantidade = $quantidade_transacao;
			$est->data = (($data)?($data):(dboNow()));
			$est->equipamento = (($equipamento)?($equipamento):($est->null()));
			$est->created_by = loggedUser();
			$est->comentario = $comentario;
			$est->requisicao_item = (($requisicao_item)?($requisicao_item):($est->null()));
			$est->custo_unitario = $lote_entrada->custo_unitario;
			$est->lote_entrada = $lote_entrada->id;
			$est->save();

			//atualizando a quantidade disponivel no lote de entrada
			$lote_entrada->quantidade_disponivel -= $quantidade_transacao;
			$lote_entrada->update();

			//se ainda soubrou, fazer a transacao novamente.
			if($quantidade_restante > 0)
			{
				estoque::saidaRecursiva($material_id, $quantidade_restante, $params);
			}
		}

	} //class declaration
} //if ! class exists

?>