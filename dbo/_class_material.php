<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'material' ========================================= AUTO-CREATED ON 11/12/2012 09:48:00 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('material'))
{
	class material extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct(get_class($this));
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

		function getQtdEstoque($params = array())
		{
			extract($params);

			$return = $this->quantidade_estoque;

			if($unidade)
			{
				$return .= ' '.$this->unidade;
			}

			if($color) {
				$return  = '<span class="color '.(($this->quantidade_estoque < $this->quantidade_alerta)?('alert'):('')).'">'.$return.'</span>';
			}

			return $return;
		}

		function getQtdEstoqueGenerico()
		{
			$sql = "
				SELECT
					sum( IF(operacao='entrada',quantidade,quantidade*-1)) AS total
				FROM 
					estoque 
				WHERE 
					material = '".$this->id."' AND
					equipamento IS NULL
			";
			$res = dboQuery($sql);
			$lin = dboFetchObject($res);
			return $lin->total;
		}

		function getQtdEstoqueInventoriado()
		{
			$sql = "
				SELECT
					sum( IF(operacao='entrada',quantidade,quantidade*-1)) AS total
				FROM 
					estoque 
				WHERE 
					material = '".$this->id."' AND
					equipamento IS NOT NULL
			";
			$res = dboQuery($sql);
			$lin = dboFetchObject($res);
			return $lin->total;
		}

		function delete()
		{
			$est = new estoque("WHERE material = '".$this->id."'");
			if($est->size())
			{
				do {
					$est->forceDelete();
				}while($est->fetch());
			}
			return parent::delete();
		}

		function updateQtdEstoque()
		{
			$this->quantidade_estoque = $this->calcEstoque();
			$this->update();
		}

		function calcEstoque()
		{
			$sql = "
				SELECT
					sum( IF(operacao='entrada',quantidade,quantidade*-1)) AS total
				FROM 
					estoque 
				WHERE 
					material = '".$this->id."'
			";
			$res = dboQuery($sql);
			$lin = dboFetchObject($res);
			return $lin->total;
		}

		static function getPainelList($params = array())
		{
			extract($params);

			$painel_material_show_list_movimentacao = (array)$painel_material_show_list_movimentacao;

			$mat = new material();

			$sql = "
				SELECT * FROM material ORDER BY nome
			";
			$mat->query($sql);

			ob_start();
			$existe_endereco = material::existeEndereco();
			?>
			<div class="row full">
				<div class="large-8 columns">
					<table class="list responsive">
						<thead>
							<tr>
								<th>Material</th>
								<th title="Quantidade disponível em estoque help" class="text-right">Qtde.</th>
								<th></th>
								<?= (($existe_endereco)?('<th>Endereço</th>'):('')) ?>
								<th style="width: 1%;" class="text-right nowrap">
									<?
										if(hasPermission('insert', 'material'))
										{
											?>
											<span class="button tiny radius no-margin trigger-colorbox-modal" data-url="dbo_admin.php?dbo_mod=material&dbo_new=1&dbo_modal=1&body_class=hide-breadcrumb<?= dboAdminPostCode('javascript: parent.reloadList();') ?>"><i class="fa-plus"></i> Novo</span>
											<?
										}
									?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?
								if($mat->size())
								{
									$total_warning = 0;
									$total_alert = 0;
									do {
										if($mat->quantidade_alerta > 0)
										{
											if($mat->quantidade_estoque == 0)
											{
												$total_alert++;
											}
											elseif($mat->quantidade_estoque < $mat->quantidade_alerta)
											{
												$total_warning++;
											}
										}
										?>
										<tr class="pointer trigger-list-movimentacao <?= ((in_array($mat->id, $painel_material_show_list_movimentacao))?('active loaded'):('')) ?>" data-material_id="<?= $mat->id ?>">
											<td data-title="Nome">
												<?= $mat->nome ?> 
												<?
													$class_color = '';
													if($mat->quantidade_alerta > 0)
													{
														if($mat->quantidade_estoque == 0)
														{
															$class_color = 'alert';
															?><i class="fa-exclamation-circle color alert help font-14" title="Este material acabou, reponha o estoque com urgência."></i><?
														}
														elseif($mat->quantidade_estoque < $mat->quantidade_alerta)
														{
															$class_color = 'warning';
															?><i class="fa-exclamation-circle color warning help font-14" title="Fique atento, este material está acabando."></i><?
														}
													}
												?>
											</td>
											<td data-title="Quantidade" class="nowrap text-right-for-medium-up color <?= (($class_color == '' && $mat->quantidade_estoque == 0)?('light'):('')) ?>"><?= $mat->quantidade_estoque ?></td>
											<td class="hide-for-small color <?= (($class_color == '' && $mat->quantidade_estoque == 0)?('light'):('')) ?>"><?= $mat->unidade ?></td>
											<?= (($existe_endereco)?('<td data-title="Endereço">'.$mat->endereco.'</td>'):('')) ?>
											<td class="text-right control-icons nowrap">
												<?
													if(hasPermission('update', 'material'))
													{
														?><a href="#" title="Editar material" data-url="dbo_admin.php?dbo_mod=material&dbo_update=<?= $mat->id ?>&dbo_modal=1&body_class=hide-breadcrumb<?= dboAdminPostCode('javascript: if(typeof parent["reloadList"] == "function"){ parent.reloadList(); }') ?>" class="trigger-colorbox-modal stop-propagation"><i class="fa-fw fa-pencil"></i></a> <?
													}
													if(hasPermission('insert', 'estoque'))
													{
														?><span class="button no-margin tiny radius trigger-dbo-modal nowrap stop-propagation" data-url="<?= secureUrl('modal-cadastro-estoque.php?material_id='.$mat->id.'&'.CSRFVar()) ?>" data-tamanho="small" data-callback="formInit();"><i class="fa-refresh"></i> Entrada / Saída</span><?										
													}
												?>
											</td>
										</tr>
										<?
										if(in_array($mat->id, $painel_material_show_list_movimentacao))
										{
											?>
											<tr class="sublist">
												<td colspan="10"><?= material::getPainelListDetalhe($mat->id, $params) ?></td>
											</tr>
											<?
										}
									}while($mat->fetch());
								}
								else
								{
									?>
									<tr>
										<td colspan='10' class="text-center">- Não há materiais de estoque cadastrados -</td>
									</tr>
									<?
								}
							?>
						</tbody>
					</table>
				</div>
				<div class="large-4 columns">
					<h3>Informações</h3>
					<fieldset>
						<legend>Alertas</legend>
						<?
							if($total_alert > 0 || $total_warning > 0)
							{
								if($total_alert > 0)
								{
									?>
									<p class="font-14 no-margin-for-small"><i class="fa-exclamation-circle fa-fw color alert font-14"></i> <?= $total_alert ?> materia<?= (($total_alert > 1)?('is'):('l')) ?> acab<?= (($total_alert > 1)?('aram'):('ou')) ?> do estoque.</p>
									<?
								}
								if($total_warning > 0)
								{
									?>
									<p class="font-14 no-margin-for-small"><i class="fa-exclamation-circle fa-fw color warning font-14"></i> <?= $total_warning ?> materia<?= (($total_warning > 1)?('is'):('l')) ?> est<?= (($total_warning > 1)?('ão'):('á')) ?> acabando do estoque.</p>
									<?
								}
							}
							else
							{
								?>
								<p class="no-margin-for-small color light text-center font-14">- Não há alertas -</p>
								<?
							}
						?>
					</fieldset>
					<fieldset>
						<legend>Últimas movimentações</legend>
						<?
							$sql = "
								SELECT
									estoque.*,
									material.nome AS material_nome,
									material.unidade AS material_unidade
								FROM
									estoque
								INNER JOIN material ON 
									estoque.material = material.id
								ORDER BY
									estoque.data DESC
								LIMIT 10
							";
							$est = new estoque();
							$est->query($sql);
							if($est->size())
							{
								?>
								<table class="list no-margin font-14">
									<tbody>
										<?
											do {
												?>
												<tr>
													<td><i class="fa-clock-o fa-fw color light font-14 help has-tip tip-top" title="<?= ago($est->data) ?>, <?= dboDate('d/m/Y H:i', strtotime($est->data)) ?>" style="border-bottom: 0;" data-tooltip></i></td>
													<td><i class="fa-fw color fa-arrow-circle-<?= (($est->operacao == 'saida')?('right light'):('left primary')) ?> font-14 help" title="<?= (($est->operacao == 'entrada')?('Entrada em'):('Saída de')) ?> estoque"></i></td>
													<td class="no-wrap">
														<?= ((!$est->requisicao_item)?('<span class="font-14 has-tip tip-top" data-tooltip title="'.$est->comentario.'"><i class="fa-comment-o fa-fw"></i></span>'):($est->_requisicao_item->getTagModal())) ?>
														<?= (($est->equipamento)?('<span class="font-14">'.$est->_equipamento->getLinkTag().'</span>'):('')) ?>
													</td>
													<td class="text-right help" title="Quantidade"><?= $est->quantidade ?></td>
													<td><?= $est->material_unidade; ?></td>
													<td><?= $est->material_nome; ?></td>
												</tr>
												<?
											}while($est->fetch());
										?>
									</tbody>
								</table>
								<?
							}
							else
							{
								?>
								<p class="no-margin color light text-center font-14">- Não há movimentações -</p>
								<?
							}
							/*$mov = new equipamento_movimentacao("ORDER BY created_on DESC LIMIT 3");
							if($mov->size())
							{
								do {
									?>
									<p class="font-14 <?= (($mov->getIterator() == 3)?('no-margin'):('')) ?>">
										<label for="" class="color light"><i class="fa-clock-o fa-fw"></i> <?= dboDate('d/m/Y H:i', strtotime($mov->created_on)) ?></label>
										<a href="<?= $mov->_equipamento->getModalPermalink(array('append' => dboAdminPostCode('javascript: parent.reloadList();'))); ?>" rel="modal"><i class="fa-tag fa-fw"></i> <span style="text-decoration: underline;"><?= $mov->_equipamento->getSmartName(); ?></span></a><br />
										<i class="fa-fw fa-map-marker color light"></i> <?= $mov->_local_destino->getSmartLocal().((strlen(trim($mov->local_detalhe)))?(' ('.$mov->local_detalhe.')'):('')) ?><br />
										<i class="fa-user fa-fw color light"></i> <?= $mov->responsavel ?>
									</p>
									<?
								}while($mov->fetch());
							}*/
						?>
					</fieldset>
				</div>
			</div>
			<?
			return ob_get_clean();
		}

		static function getPainelListDetalhe($material_id, $params = array()) {
			$est = new estoque();
			return $est->getTabelaEstoque(array('material_id' => $material_id));
		}

		static function existeEndereco()
		{
			$sql = "SELECT COUNT(*) AS total FROM material WHERE endereco <> '';";
			$res = dboQuery($sql);
			$lin = dboFetchObject($res);
			if($lin->total > 0)
			{
				return true;
			}
			return false;
		}

	} //class declaration
} //if ! class exists

function form_material_append($tipo, $obj)
{
	ob_start();
	?>
	<script>
		$(document).ready(function(){
			$(document).on('change', 'select[name="unidade"]', function(){
				$('.qtd-aux').text($(this).find('option:selected').text());
			});
		}) //doc.ready
	</script>
	<?
	return ob_get_clean();
}

function field_material_quantidade_alerta($tipo, $obj)
{
	ob_start();
	?>
	<div class="row collapse">
		<div class="large-6 columns">
			<input type="number" name="quantidade_alerta" id="" value="<?= $obj->quantidade_alerta ?>" class="text-right"/>
		</div>
		<div class="large-6 columns"><span class="postfix radius qtd-aux"><?= $obj->getValue('unidade', $obj->unidade) ?></span></div>
	</div>
	<?
	return ob_get_clean();
}

function form_material_after($tipo, $obj)
{
	ob_start();
	if($tipo == 'update')
	{
		?>
		<hr class="small">
		<div class="row">
			<div class="large-6 columns">
				<h3>Estoque</h3>
			</div>
			<div class="large-6 columns text-right">
				<span class="button no-margin small radius top-3 trigger-dbo-modal" data-url="<?= secureUrl('modal-cadastro-estoque.php?material_id='.$obj->id.'&'.CSRFVar()) ?>" data-tamanho="small" data-callback="formInit();"><i class="fa-refresh"></i> Entrada / Saída</span>
			</div>
		</div>
		<?
			$est = new estoque();
			echo $est->getTabelaEstoque(array('material_id' => $obj->id));
	}
	?>
	<script>
		function reloadList() {
			
			//se estiver em um modal, recarregar o pai também.
			if(typeof parent["reloadList"] == "function"){
				parent.reloadList();
			}

			peixeGet(document.URL, function(d) {
				var html = $.parseHTML(d);
				/* item 1 */
				handler = '#wrapper-tabela-estoque';
				content = $(html).find(handler).html();
				if(typeof content != 'undefined'){
					$(handler).fadeHtml(content);
				}
			})
			return false;
		}
	</script>
	<?
	return ob_get_clean();
}

?>