<? require('header.php') ?>
<? require_once('auth.php') ?>
<?
	if(!hasPermission('painel-principal'))
	{
		header("Location: requisicao.php");
		exit();
	}
	$painel = new Painel();
	$painel->initParam('status', 'novos');
?>

<style>
	#main-header { margin-bottom: 1px !important; }
</style>

<div class="main-tabs cf contain-to-grid">
	<nav class="top-bar">
		<section class="top-bar-section">
			<ul class="title-area">
				<li class="name"></li>
				<li class="toggle-topbar menu-icon"><a href="#"><span>Serv</span></a></li>
			</ul>
			<ul class="left" id="nav-list">
				<li class="<?= (($painel->getParam('status') == 'novos')?('active'):('')) ?>"><a data_status='novos' href='#'>Novos<?= $_sistema->addNotificationContainer('novos') ?></a></li>
				<li class="<?= (($painel->getParam('status') == 'atribuidos')?('active'):('')) ?>"><a href='#' data_status='atribuidos'>Atribuídos<?= $_sistema->addNotificationContainer('atribuidos') ?></a></li>
				<li class="<?= (($painel->getParam('status') == 'aguardando')?('active'):('')) ?>"><a href='#' data_status='aguardando'>Aguardando<?= $_sistema->addNotificationContainer('aguardando') ?></a></li>
				<li class="<?= (($painel->getParam('status') == 'feedbacks-atrasados')?('active'):('')) ?>" title="Feedbacks atrasados" id='menu-feedbacks-atrasados'><a href='#' data_status='feedbacks-atrasados'><span class="hide-for-small"><i class="fa fa-fw fa-refresh"></i></span><span class="show-for-small inline">Feedbacks Atrasados</span><?= $_sistema->addNotificationContainer('feedbacks') ?></a></li>
				<li class="<?= (($painel->getParam('status') == 'encerrados')?('active'):('')) ?>"><a href='#' data_status='encerrados'>Encerrados</a></li>
				<li class="<?= (($painel->getParam('status') == 'todos')?('active'):('')) ?>"><a href='#' data_status='todos'>Todos</a></li>
			</ul>
			<ul class="right">
				<li>
					<form method="get" id='quick-search-form' action="ajax-painel-actions.php">
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

	<div id="wrapper-list">
	<?

		$item = new requisicao_item();
		$item->getPainelList($painel->getParams());

	?>
	</div><!-- wrapper-list -->
</div><!-- painel -->

<script type="text/javascript" charset="utf-8">

	var global_term = '<?= $painel->getParam('term') ?>';

	function showRequisicao(id, id_item) {
		$.colorbox({
			href:"modal-requisicao.php?&id="+id+"&item="+id_item,
			iframe: true,
			width: 1000,
			height: '98%',
			overlayClose: false,
			escKey: false,
			fixed: true
		});
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

	function submitSearch() {
		$('#quick-search-form').submit();
	}

	function reloadList() {
		peixeGet(
			'ajax-painel-actions.php',
			{ 
				show_list: 1
			},
			function(data) {
				var result = $.parseJSON(data);
				updatePainel(result);
				getNotifications();
			}
		)
		return false;
	}

	function changeTab(status) {
		$('#nav-list a').closest('li').removeClass('active');
		$('#nav-list a[data_status="'+status+'"]').closest('li').addClass('active');
		peixeGet(
			'ajax-painel-actions.php',
			{ 
				status: status,
				show_list: 1,
				show_all: 0
			},
			function(data) {
				var result = $.parseJSON(data);
				updatePainel(result);
			}
		)
		return false;
	}

	function showAll(status) {
		peixeGet(
			'ajax-painel-actions.php',
			{ 
				status: status,
				show_list: 1,
				show_all: 1
			},
			function(data) {
				var result = $.parseJSON(data);
				updatePainel(result);
			}
		)
		return false;
	}

	function checkTerm() {
		target = $('#painel-search-field');
		if($.trim(target.val()) != ''){
			target.addClass('highlight');
		} else {
			target.removeClass('highlight');
		}
	}

	$(document).ready(function(){

		checkTerm();

		$(document).on('click', '#nav-list a', function(e){
			e.preventDefault();
			changeTab($(this).attr('data_status'));
		});

		$(document).on('click', '.status-filter', function(){
			var status = $(this).attr('data-status');
			$('#painel-requisicoes tbody tr').show();
			if(status == 'total'){ return true; }
			$('#painel-requisicoes tbody tr:not([data-status="'+status+'"])').hide();
		});

		$(document).on('click', '.trigger-show-all', function(e){
			e.preventDefault();
			showAll($(this).attr('data-status'));
		});

		$(document).on('submit', '#quick-search-form', function(){
			var term = $(this).find('#painel-search-field').val();
			global_term = term;
			peixeGet(
				'ajax-painel-actions.php',
				{ 
					term: term,
					show_list: 1
				},
				function(data) {
					var result = $.parseJSON(data);
					updatePainel(result);
					checkTerm();
				}
			)
			return false;
		});
	
		activeMainNav('painel-principal');

	}) //doc.ready

</script>
<? require('footer.php') ?>