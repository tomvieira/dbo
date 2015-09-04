<? require_once('modal-header.php'); ?>
<?

if(!hasPermission('gerenciar-servicos'))
{
	criticalError('Você não tem permissão para gerenciar serviços');
}

$req = new requisicao("WHERE id = '".addslashes($_GET['id'])."' AND inativo = 0");
if(!$req->size())
{
	criticalError("Requisição não existe ou foi deletada");
}

//checando quais itens deve mostrar
$ativo = false;
if($_GET['item'])
{
	$ativo = new requisicao_item(addslashes($_GET['item']));
}

$definir_feedback = hasPermission('definir-dias-feedback');

?>

<div id="todo">
	<div id="content-requisicao">
		<div id="wrapper-requisicao">
			<div class="numero-requisicao">
				<label>Requisição nº <?= $req->id ?></label>
			</div>
			<div class="data">
				<label class="help" title="Data de abertura da requisição"><?= date("d/m H:i", strtotime($req->data)) ?> (<?= ago($req->data) ?>)</label>
			</div>
			<div class="requisitante">
				<label><span class="help" title='Requisitante'><?= $req->nome_requisitante ?></span> <span class="help" title='Telefone'>(<?= $req->telefone_requisitante ?>)</span></label>
			</div>

			<form method="POST" action="ajax-modal-requisicao-actions.php" id='form-requisicao-admin'>
			<?
				$item = new requisicao_item("WHERE requisicao = '".$req->id."' AND inativo = 0 ORDER BY numero");

				if($_GET['item'] && $item->size() > 1)
				{
				?>
					<div class="trigger-mostrar-todos">
						Mostrar todos os itens
					</div>
				<?
				}
				
				if($item->size())
				{
					$local = '';
					do {
						if($item->local != $local)
						{
							$local = $item->local;
							$local_aux = new local($local);
							?><h3 class="<?= ((!$ativo || $ativo->local == $local_aux->id)?(''):('hidden')) ?>">Local: <?= $local_aux->getSmartLocal(); ?></h3><?
						}
						?>
						<div class="item-requisicao <?= ((!$ativo || $ativo->id == $item->id)?(''):('hidden')) ?>" data_id='<?= $item->id ?>'>
							<ul class="info">
								<li>
									<span title="<?= $item->id ?>"><?= $req->id ?>/<?= $item->numero ?></span>
								</li><li class="servico">
									<select name="tipo_servico[<?= $item->id ?>]">
										<?= $item->getOptionsServicos($item->tipo_servico) ?>
									</select><br />
									<p><?= $item->descricao ?></p>
								</li><li class="actions">
									<span title='Status' class="help">S:</span>
									<select name="status[<?= $item->id ?>]">
										<?= $item->getMulti('status', 'select', $item->status) ?>
									</select>
									<span title='Prioridade' class="help">P:</span>
									<select name="prioridade[<?= $item->id ?>]">
										<?= $item->getMulti('prioridade', 'select', $item->prioridade) ?>
									</select><br />
								</li><li class="reabrir" style="<?= (($item->finalizado_servidor == 0)?('display: none'):('')) ?>">
									<div>
										Finalizado pelo servidor. Reabrir? &nbsp;&nbsp;&nbsp; 
										<input type='radio' name='finalizado_servidor[<?= $item->id ?>]' value="0" <?= (($item->finalizado_servidor == 0)?('checked'):('')) ?> /> Sim &nbsp;&nbsp;&nbsp; 
										<input type='radio' name='finalizado_servidor[<?= $item->id ?>]' value="1" <?= (($item->finalizado_servidor == 1)?('checked'):('')) ?> /> Não
									</div>
								</li><li class="comentario">
									<input type='text' name='comentario[<?= $item->id ?>]' value="" placeholder="Comentário do Supervisor para o histórico..."/>
								</li><li class="agendamento">
									<label>Agendar Para</label>
									<input type="text" class="datepick" name="data_agendada[<?= $item->id ?>]" value="<?= (($item->data_agendada)?(date('d/m/Y', strtotime($item->data_agendada))):('')) ?>" placeholder='Selecione...'/>
								</li><li class="followup">
									<label class="help" title='Número máximo de dias que a requisição pode ficar parada, sem ao menos um feedback para o usuário.'>Feedback</label>
									<?
										if($definir_feedback)
										{
											?>
											<input type="text" name="dias_feedback[<?= $item->id ?>]" value="<?= $item->dias_feedback ?>"/><span class="dias"> dias</span>		
											<?
										}
										else
										{
											?>
											<span><?= intval($item->dias_feedback) ?> dias</span>
											<?
										}
									?>
									
								</li><li class="observacao">
									<label>Observação da Seção</label>
									<input type="text" name="observacao[<?= $item->id ?>]" value="<?= htmlSpecialChars($item->observacao) ?>" placeholder="Invisível para o usuário..."/>
								</li><li class="materiais">
									<label>Materiais de Estoque</label>
								</li><li class="servidores-atribuidos">
									<label>Reponsáveis Atribuidos</label>
									<div class="inputs">
									<?
										$sql = "SELECT * FROM requisicao_item_servidor WHERE item = '".$item->id."' ORDER BY id";
										$res = dboQuery($sql);
										if(dboAffectedRows())
										{
											while($lin = dboFetchObject($res))
											{
												$serv = new servidor($lin->servidor);
												?>
												<div><input type='hidden' name='servidor[<?= $item->id ?>][]' value='<?= $serv->id ?>'><span class='tag tag-servidor <?= ((in_array($serv->id, $global_ids_prestadores))?('color-prestador'):('')) ?>' data_id='<?= $serv->id ?>'><?= $serv->getShortName(); ?><span class='deleter'></span></span></div>
												<?
											}
										}
									?>
									</div>
								</li><li class="historico">
									<?
										$hist = new historico("WHERE requisicao_item = '".$item->id."' ORDER BY data");
										if($hist->size() > 1)
										{
										?>
											<label class="pointer open trigger-historico">Histórico do serviço</label>
											<table id='historico-<?= $item->id ?>' class="list-historico">
												<thead>
													<tr>
														<th>Data</th>
														<th>Prior.</th>
														<th>Status</th>
														<th>Comentário</th>
													</tr>
												</thead>
												<tbody>
												<?
													$prioridade_anterior = '';
													$status_anterior = '';
													do {
														$pes = new servidor($hist->created_by);
														?>
														<tr title='<?= $pes->nome ?>'>
															<td style='white-space: nowrap;'><span class="help" title='<?= date('d/m/Y H:i', strtotime($hist->data)) ?>'><?= ago($hist->data) ?></span></td>
															<td><?= (($hist->prioridade != $prioridade_anterior)?($item->getValue('prioridade', $hist->prioridade)):('')) ?></td>
															<td style='white-space: nowrap;'><?= (($hist->status != $status_anterior)?($item->getValue('status', $hist->status)):('')) ?></td>
															<td><?= $hist->comentario ?></td>
														</tr>
														<?
														$prioridade_anterior = $hist->prioridade;
														$status_anterior = $hist->status;
													}while($hist->fetch());
												?>
												</tbody>
											</table>
										<?
										}
									?>
								</li>
							</ul>

						</div>
						<?
					}while($item->fetch());
				}
			?>

				<div id="wrapper-actions">
					<a href='#' class="trigger-save-requisicao save button-modal" style='display: none;'>Salvar</a>
					<a href='#' class="trigger-close-requisicao close button-modal">Fechar</a>
				</div>

				<input type='hidden' name='requisicao' value="<?= $req->id ?>"/>
				<input type='hidden' name='action' value="update-requisicao"/>

			</form>
				
		</div><!-- wrapper-requisicao -->

		<div id="wrapper-lista-estoque">
			<div class="tac"><br /><br /><br />Reservado para o <br />Módulo de Estoque</div>
		</div><!-- wrapper-lista-estoque -->

		<div id="wrapper-lista-servidores">
			<dl class="tipo-servidor-switcher">
				<dd class="active" data-show='.lista-servidores' data-hide='.lista-prestadores'>Servid.</dd>
				<dd data-show='.lista-prestadores' data-hide='.lista-servidores'>Prest. Serv.</dd>
			</dl>
			<ul class="lista-servidores">
			<?
				$serv = new servidor("WHERE id IN (".implode(',', getIdsServidores()).") ORDER BY nome");
				if($serv->size())
				{
					do {
						?>
						<li><?= $serv->getBadge(); ?></li>
						<?
					}while($serv->fetch());
				}
			?>
			</ul>
			<ul class="lista-prestadores" style='display: none;'>
			<?
				$serv = new servidor("WHERE id IN (".implode(',', getIdsPrestadores()).") ORDER BY apelido");
				if($serv->size())
				{
					do {
						?>
						<li><?= $serv->getBadge('small'); ?></li>
						<?
					}while($serv->fetch());
				}
			?>
			</ul>
		</div>
	</div><!-- content -->
</div><!-- todo -->

<script type="text/javascript" charset="utf-8">

	var id_requisicao = '<?= addslashes($_GET['id']) ?>';
	var id_item = '<?= addslashes($_GET['item']) ?>';

	function init() {

		//servidores arrastáveis
		$('ul.lista-servidores li, ul.lista-prestadores li').draggable({
			helper: "clone"
		});

		//itens droppable para os servidores/estoque
		$('.item-requisicao').droppable({
			accept: 'ul.lista-servidores li, ul.lista-prestadores li',
			hoverClass: 'drop-here',
			drop: function(event, ui) {
				var modulo = $(ui.draggable).find('.servidor-badge').attr('data_modulo');
				var id_item = $(this).closest('.item-requisicao').attr('data_id');

				//dropou um servidor no item
				if(modulo == 'servidor'){
					var id = $(ui.draggable).find('.servidor-badge').attr('data_id');
					var nome = $(ui.draggable).find('.servidor-badge').attr('data_nome');
					var classe = ($(ui.draggable).find('.servidor-badge').hasClass('tipo-prestador'))?('color-prestador'):('');
					atribuiServidor(id, nome, id_item, classe);
					triggerSaveButton();
				}
			}
		})

		/* datepicker */
		$('.datepick').each(function(){
			$(this).datepicker();
			$(this).mask('99/99/9999');
		});
	}

	function checkAtribuido(id_item) {
		$('.item-requisicao[data_id="'+id_item+'"]:has(.servidores-atribuidos .inputs .tag-servidor)').find('select[name^=status]:has(option[value=0]:selected), select[name^=status]:has(option[value=2]:selected)').val('3');
	}

	function atribuiServidor(id_serv, nome_serv, id_item, classe) {
		var tag = "<div><input type='hidden' name='servidor["+id_item+"][]' value='"+id_serv+"'><span class='tag tag-servidor "+classe+"' data_id='"+id_serv+"'>"+nome_serv+"<span class='deleter'></span></span></div>";
		console.log(tag);
		$('.item-requisicao[data_id='+id_item+']:not(:has(.tag-servidor[data_id='+id_serv+']))').find('.inputs').append(tag);
		checkAtribuido(id_item);
	}

	function reloadList() {
		<?
			if($_GET['outside'] != 1){
				?>
				parent.reloadList();
				<?
			}	
		?>
	}

	function triggerSaveButton() {
		$('.trigger-close-requisicao:visible').hide();
		$('.trigger-save-requisicao:hidden').show();
	}

	$(document).ready(function(){

		init();

		$(document).on('click', '.trigger-mostrar-todos', function(){
			id_item = 0;
			$(this).hide();
			$('.item-requisicao.hidden, h3.hidden').slideDown('fast');
		});

		$(document).on('click', '.trigger-historico', function(){
			var clicado = $(this);
			if(clicado.hasClass('open')){ clicado.addClass('closed'); clicado.removeClass('open'); }
			else { clicado.addClass('open'); clicado.removeClass('closed'); }
			clicado.next('table').fadeToggle('fast');
		});

		//deletando servidores
		$(document).on('click', '.tag-servidor .deleter', function(){
			$(this).closest('div').fadeOut('fast', function(){ $(this).remove(); triggerSaveButton(); });
		});

		//botao para salvar requisicao
		$(document).on('click', '.trigger-save-requisicao', function(e){
			e.preventDefault();
			$(this).closest('form').submit();
		});

		//botao para fechar o modal
		$(document).on('click', '.trigger-close-requisicao', function(){
			<?
				if($_GET['outside'] != 1){
					?>
					parent.$.fn.colorbox.close();
					<?
				}
				else {
					?>
					window.close();
					<?
				}
			?>
		});

		$(document).on('change', 'select:visible', function(){
			triggerSaveButton();
		});

		$(document).on('change', 'input[name^="finalizado_servidor"], input[name^="data_agendada"], input[name^="observacao"], input[name^="comentario"]', function(){
			triggerSaveButton();
		});

		$(document).on('keyup', 'input[name^="observacao"], input[name^="comentario"], input[name^="dias_feedback"]', function(){
			triggerSaveButton();
		});

		$(document).on('click', '.tipo-servidor-switcher dd:not(.active)', function(){
			clicado = $(this);
			clicado.closest('dl').find('dd.active').removeClass('active');
			clicado.addClass('active');
			$(clicado.attr('data-hide')+':visible').fadeOut(100, function(){
				$(clicado.attr('data-show')+':hidden').fadeIn(100);
			})
		});

		$(document).on('submit', '#form-requisicao-admin', function(){
			peixePost(
				$(this).attr('action'),
				$(this).serialize(),
				function(data) {
					console.log(data);
					var result = $.parseJSON(data);
					if(result.message){
						setPeixeMessage(result.message);
						showPeixeMessage();
					}
					if(result.reload){
						//não pode ser o primeiro nó depois do <body>
						var url = 'modal-requisicao.php?id='+id_requisicao+((id_item > 0)?('&item='+id_item):(''));
						$.get(url, function(d){
							d = $.parseHTML(d);
							$(result.reload).fadeHtml($(d).find(result.reload).html(), function(){ init(); });
						})
					}
					if(result.reload_list) {
						reloadList();
					}
				}
			)
			return false;
		})

	}) //doc.ready
</script>

<? require_once('modal-footer.php'); ?>