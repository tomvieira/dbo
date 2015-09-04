<? require('header.php') ?>
<? require_once('auth.php') ?>
<?

	$painel = new Painel();

	if($_GET['clear_detalhes'])
	{
		$painel->clearParam('painel_material_show_list_equipamentos');
		$painel->clearParam('painel_material_show_list_manutencao');
		$painel->clearParam('painel_material_show_list_movimentacao');
		header("Location: painel-materiais.php");
		exit();
	}

	if(!hasPermission('painel-inventario') && !hasPermission('painel-estoque'))
	{
		setMessage('<div class="error">Erro: Permissão de acesso ao Estoque/Inventário negada.</div>');
		header("Location: requisicao.php");
		exit();
	}

	$painel->initParam('painel_material_modulo', 'manutencao');
?>

<style>
	#main-header { margin-bottom: 1px !important; }
</style>

<div class="main-tabs cf contain-to-grid">
	<nav class="top-bar">
		<section class="top-bar-section">
			<ul class="title-area">
				<li class="name"></li>
				<li class="toggle-topbar menu-icon"><a href="#"><span>Opc.</span></a></li>
			</ul>
			<ul class="left" id="nav-list">
				<?
					if(hasPermission('painel-estoque'))
					{
						?><li class="<?= (($painel->getParam('painel_material_modulo') == 'material')?('active'):('')) ?>"><a data-painel_material_modulo='material' href='#'>Estoque<?= $_sistema->addNotificationContainer('estoque') ?></a></li><?
					}
					if(hasPermission('painel-inventario'))
					{
						?><li class="<?= (($painel->getParam('painel_material_modulo') == 'equipamento')?('active'):('')) ?>"><a data-painel_material_modulo='equipamento' href='#'>Inventário<?= $_sistema->addNotificationContainer('inventario') ?></a></li><?
					}
					if(hasPermission('equipamentos-relacionados-painel'))
					{
						?><li class="<?= (($painel->getParam('painel_material_modulo') == 'manutencao')?('active'):('')) ?>"><a data-painel_material_modulo='manutencao' href='#'>Manutenção<?= $_sistema->addNotificationContainer('equipamentos_manutencao') ?></a></li><?
					}
				?>
			</ul>
			<ul class="right">
				<li>
					<form method="get" id='quick-search-form' action="ajax-painel-actions.php" style="display: none;">
						<div class='row collapse'>
							<div class='small-8 columns'>
								<input type='search' onsearch="submitSearch()" id='painel-search-field' placeholder='digite sua busca...' name='term' value="<?= htmlSpecialChars($painel->getParam('term')); ?>"/>
								<div class="helper arrow-bottom hidden">Busca ativa</div>
							</div>
							<div class='small-4 columns'>
								<ul class="button-group expand">
									<li><input type='submit' class="button secondary" name='' value="Buscar" style='font-weight: normal;'/></li>
									<!-- <li><input type='button' class="button secondary" name='' value="+" title='Mais opções de filtro'/></li> -->
								</ul>
							</div><!-- col -->
						</div><!-- row -->
					</form>
				</li>
			</ul>
		</section>
	</nav>
</div><!-- main-tabs -->

<div id="painel" style='padding: 10px;'>
	<div id="wrapper-list" class="materiais">
	<?
		$modulo = $painel->getParam('painel_material_modulo');
		if($modulo == 'manutencao')
		{
			echo equipamento::getPainelListManutencao($painel->getParams());
		}
		else
		{
			echo $modulo::getPainelList($painel->getParams());
		}
	?>
	</div><!-- wrapper-list -->
</div><!-- painel -->

<script type="text/javascript" charset="utf-8">

	function reloadList() {
		peixeGet(
			'ajax-painel-actions.php',
			{ 
				painel_material_show_list: 1
			},
			function(data) {
				var result = $.parseJSON(data);
				updatePainel(result);
				getNotifications();
			}
		)
		return false;
	}

	function updatePainel(result) {
		if(result.message){
			setPeixeMessage(result.message);
			showPeixeMessage();
		}
		if(result.reload){
			$.get(document.URL, function(d){
				$(result.reload).fadeHtml($(d).find(result.reload).html());
			})
		}
		if(result.html){
			$(result.html).each(function(){
				$(this.selector).html(this.content);
			})
		}
		if(result.list){
			$('#wrapper-list').fadeHtml(result.list);
		}
		if(result.append){
			//implementar
		}
		if(result.addClass){
			//implementar
		}
		if(result.removeClass){
			//implementar
		}
	}

	function changeTab(modulo) {
		$('#nav-list a').closest('li').removeClass('active');
		$('#nav-list a[data-painel_material_modulo="'+modulo+'"]').closest('li').addClass('active');
		peixeGet(
			'ajax-painel-actions.php',
			{ 
				painel_material_modulo: modulo,
				painel_material_show_list: 1,
				painel_material_show_all: 0
			},
			function(data) {
				var result = $.parseJSON(data);
				updatePainel(result);
			}
		)
		return false;
	}

	$(document).ready(function(){

		$(document).on('click', '#nav-list a', function(e){
			e.preventDefault();
			changeTab($(this).attr('data-painel_material_modulo'));
		});

		$(document).on('click', '.trigger-list-equipamentos', function(){
			clicado = $(this);
			if(clicado.hasClass('loaded')){
				if(clicado.hasClass('active')){
					clicado.removeClass('active');
					clicado.next('tr').hide();
					$.get('ajax-painel-actions.php',{ painel_material_hide_list_equipamentos: clicado.data('tipo_equipamento_id') });
				}
				else {
					clicado.addClass('active');
					clicado.next('tr').fadeIn();
					$.get('ajax-painel-actions.php',{ painel_material_set_list_equipamentos: clicado.data('tipo_equipamento_id') });
				}
			}
			else {
				clicado.addClass('active loaded');
				peixeGet(
					'ajax-painel-actions.php',
					{ 
						painel_material_show_list_equipamentos: clicado.data('tipo_equipamento_id')
					},
					function(data) {
						var result = $.parseJSON(data);
						//console.log(result);
						$(result.detalhe).css('display', 'none').insertAfter(clicado).fadeIn();
					}
				)
			}
			return false;
		});

		$(document).on('click', '.trigger-list-manutencao', function(){
			clicado = $(this);
			if(clicado.hasClass('loaded')){
				if(clicado.hasClass('active')){
					clicado.removeClass('active');
					clicado.next('tr').hide();
					$.get('ajax-painel-actions.php',{ painel_material_hide_list_manutencao: clicado.data('situacao') });
				}
				else {
					clicado.addClass('active');
					clicado.next('tr').fadeIn();
					$.get('ajax-painel-actions.php',{ painel_material_set_list_manutencao: clicado.data('situacao') });
				}
			}
			else {
				clicado.addClass('active loaded');
				peixeGet(
					'ajax-painel-actions.php',
					{ 
						painel_material_show_list_manutencao: clicado.data('situacao')
					},
					function(data) {
						var result = $.parseJSON(data);
						//console.log(result);
						$(result.detalhe).css('display', 'none').insertAfter(clicado).fadeIn();
					}
				)
			}
			return false;
		});

		$(document).on('click', '.trigger-list-movimentacao', function(){
			clicado = $(this);
			if(clicado.hasClass('loaded')){
				if(clicado.hasClass('active')){
					clicado.removeClass('active');
					clicado.next('tr').hide();
					$.get('ajax-painel-actions.php',{ painel_material_hide_list_movimentacao: clicado.data('material_id') });
				}
				else {
					clicado.addClass('active');
					clicado.next('tr').fadeIn();
					$.get('ajax-painel-actions.php',{ painel_material_set_list_movimentacao: clicado.data('material_id') });
				}
			}
			else {
				clicado.addClass('active loaded');
				peixeGet(
					'ajax-painel-actions.php',
					{ 
						painel_material_show_list_movimentacao: clicado.data('material_id')
					},
					function(data) {
						var result = $.parseJSON(data);
						//console.log(result);
						$(result.detalhe).css('display', 'none').insertAfter(clicado).fadeIn();
					}
				)
			}
			return false;
		});

		$(document).on('click', '.trigger-order-by', function(){
			peixeGet(
				'ajax-painel-actions.php',
				{ 
					painel_material_show_list: 1,
					painel_material_order_by: $(this).data('order_by')
				},
				function(data) {
					var result = $.parseJSON(data);
					updatePainel(result);
				}
			)
		});

		activeMainNav('painel-materiais');

	}) //doc.ready

</script>
<? require('footer.php') ?>