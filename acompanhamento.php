<?

require_once('header.php');
require_once('auth.php');

$_pes = new pessoa(loggedUser());

if($_GET['servicos'])
{
	$ids_servicos_ativos = explode("-", $_GET['servicos']);
}
?>

<script>
		activeMainNav('acompanhamento');
</script>

<div class='row' id="wrapper-acompanhamento">
	<div class='large-12 columns'>

		<?
			if($_GET['sucesso'])
			{	
				if(sizeof($ids_servicos_ativos) > 1)
				{
					$plural = 's';
				}
				?>
				<div data-alert class="alert-box radius success">
					Serviço<?= $plural ?> enviado<?= $plural ?> com sucesso. Você pode acompanhar o andamento pela lista abaixo.
					<a href="#" class="close">&times;</a>
				</div>
				<?
			}
		?>

		<div class="section-container auto" data-section>
			<div class="helper arrow-bottom hide-for-small" style="position: absolute; top: 0; right: 0;">Clique nas linhas para visualizar os detalhes do serviço</div>
			<section id='section-abertos'>
				<p class="title" data-section-title><a href="">Serviços Abertos</a></p>
				<div class="content" data-section-content id='content-abertos'>
					<?
					$sql = "
						SELECT
							requisicao_item.*
						FROM
							requisicao,
							requisicao_item
						WHERE
							requisicao.id = requisicao_item.requisicao AND
							requisicao_item.status <= 6 AND 
							requisicao_item.inativo = 0 AND 
							requisicao.email_requisitante = '".$_pes->email."'
						ORDER BY 
							created_on DESC
					";
					$serv = new requisicao_item();
					$serv->query($sql);
					if($serv->size())
					{
						?>
						<table class="acompanhamento">
							<thead>
								<tr>
									<th>Nº</th>
									<th>Data</th>
									<th>Tipo de Serviço</th>
									<th class="hide-for-small">Status</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
							<?
								do {
									$loc = new local($serv->local);
									?>
									<tr class="handler <?= (($_GET['id'] == $serv->id)?('active'):('')) ?>" id='servico-<?= $serv->id ?>'>
										<td><?= $serv->requisicao ?>/<?= $serv->numero ?></td>
										<td>
											<div class="show-for-small"><?= date('d/m', strtotime($serv->created_on)) ?></div>
											<div class="help hide-for-small" title='<?= date('d/m/Y H:i', strtotime($serv->created_on)) ?>'><?= ago($serv->created_on) ?></div>
										</td>
										<td><?= $serv->___tipo_servico___nome ?></td>
										<td class="hide-for-small"><div class="status-<?= $serv->status ?>"><?= $serv->getValue('status', $serv->status) ?></div></td>
										<td class="hide-for-small">
											<?= (($serv->status == STATUS_CONCLUIDO)?('<span class="button disabled tiny no-margin expand radius">FALTA SUA AVALIAÇÃO</span>'):('')) ?>
											<?= (($serv->status == STATUS_AGUARDANDO_REQUISITANTE)?('<a href="#" class="button tiny disabled no-margin expand radius alert">INFORME '.(($_conf->genero == 'm')?('O'):('A')).' '.$_conf->nome_curto_secao.'</a>'):('')) ?>
										</td>
										<td class="small-alert-status">
											<?= (($serv->status == STATUS_CONCLUIDO)?('<span class="label round">!</span>'):('')) ?>
											<?= (($serv->status == STATUS_AGUARDANDO_REQUISITANTE)?('<span class="label round alert">!</span>'):('')) ?>
										</td>
									</tr>
									<tr class="detalhes" style="<?= (($_GET['id'] == $serv->id)?('display: table-row'):('')) ?>">
										<td colspan='5'>
											<div class="wrapper-detalhes">
												<div class='row'>
													<div class='large-2 hide-for-small columns text-right'><p>Descrição</p></div>
													<div class='large-10 small-12 columns'><p><?= $serv->descricao ?></p></div><!-- col -->
												</div><!-- row -->
												<div class='row'>
													<div class='large-2 hide-for-small columns text-right'><p>Local</p></div>
													<div class='large-10 small-12 columns'><p><?= $loc->getSmartLocal() ?></p></div><!-- col -->
												</div><!-- row -->
												<div class='row'>
													<div class='large-2 hide-for-small columns text-right'><p>Status</p></div>
													<div class='large-10 small-12 columns'><?= $serv->getStatusChart(); ?></div><!-- col -->
												</div><!-- row -->
												<div class='row'>
													<div class='large-2 hide-for-small columns text-right'><p>Histórico</p></div>
													<div class='large-10 small-12 columns'>
														<a href='#' class="trigger-historico button expand radius secondary">+ Mostrar Histórico</a>
														<?= $serv->getHistorico(); ?>
														<?= (($serv->status == STATUS_CONCLUIDO)?("<a href='#' class='trigger-avaliacao button expand radius' data-servico='".$serv->id."'>+ Avaliar Serviço</a>"):('')) ?>
														<?= (($serv->status == STATUS_AGUARDANDO_REQUISITANTE)?("<a href='#' class='trigger-informar button expand radius alert' data-servico='".$serv->id."'>Informar ".(($_conf->genero == 'm')?('o'):('a'))." ".$_conf->nome_curto_secao."</a>"):('')) ?>
													</div><!-- col -->
												</div><!-- row -->
											</div>
											<div class="wrapper-avaliacao" id='avaliacao-<?= $serv->id ?>' name='avaliacao-<?= $serv->id ?>'>
												<?
													$aval = new avaliacao("WHERE requisicao_item = '".$serv->id."'");
													echo $aval->getFormAvaliacao();
												?>
											</div>
										</td>
									</tr>
									<?
								}while($serv->fetch());
							?>
							</tbody>
						</table>
						<?
					}
					else
					{
						?>- Não há serviços abertos no momento -<?
					}
					?>
				</div>
			</section>
			<section id='section-encerrados'>
				<p class="title" data-section-title><a href="#panel2">Serviços Encerrados</a></p>
				<div class="content" data-section-content id='content-encerrados'>
					<?
					$sql = "
						SELECT
							requisicao_item.*
						FROM
							requisicao,
							requisicao_item
						WHERE
							requisicao.id = requisicao_item.requisicao AND
							requisicao_item.status > 6 AND 
							requisicao_item.inativo = 0 AND 
							requisicao.email_requisitante = '".$_pes->email."'
						ORDER BY 
							created_on DESC
					";
					$serv = new requisicao_item();
					$serv->query($sql);
					if($serv->size())
					{
						?>
						<table class="acompanhamento">
							<thead>
								<tr>
									<th>Nº</th>
									<th>Data</th>
									<th>Tipo de Serviço</th>
									<th class="hide-for-small">Status</th>
								</tr>
							</thead>
							<tbody>
							<?
								do {
									$loc = new local($serv->local);
									?>
									<tr class="handler" id='servico-<?= $serv->id ?>'>
										<td><?= $serv->requisicao ?>/<?= $serv->numero ?></td>
										<td>
											<div class="show-for-small"><?= date('d/m', strtotime($serv->created_on)) ?></div>
											<div class="help hide-for-small" title='<?= date('d/m/Y H:i', strtotime($serv->created_on)) ?>'><?= ago($serv->created_on) ?></div>
										</td>
										<td><?= $serv->___tipo_servico___nome ?></td>
										<td class="hide-for-small"><div class="status-<?= $serv->status ?>"><?= $serv->getValue('status', $serv->status) ?></div></td>
									</tr>
									<tr class="detalhes">
										<td colspan='5'>
											<div class="wrapper-detalhes">
												<div class='row'>
													<div class='large-2 hide-for-small columns text-right'><p>Descrição</p></div>
													<div class='large-10 small-12 columns'><p><?= $serv->descricao ?></p></div><!-- col -->
												</div><!-- row -->
												<div class='row'>
													<div class='large-2 hide-for-small columns text-right'><p>Local</p></div>
													<div class='large-10 small-12 columns'><p><?= $loc->getSmartLocal() ?></p></div><!-- col -->
												</div><!-- row -->
												<div class='row'>
													<div class='large-2 hide-for-small columns text-right'><p>Status</p></div>
													<div class='large-10 small-12 columns'><?= $serv->getStatusChart(); ?></div><!-- col -->
												</div><!-- row -->
												<div class='row'>
													<div class='large-2 hide-for-small columns text-right'><p>Histórico</p></div>
													<div class='large-10 small-12 columns'>
														<a href='#' class="trigger-historico button expand radius secondary">+ Mostrar Histórico</a>
														<?= $serv->getHistorico(); ?>
													</div><!-- col -->
												</div><!-- row -->
											</div>
										</td>
									</tr>
									<?
								}while($serv->fetch());
							?>
							</tbody>
						</table>
						<?
					}
					else
					{
						?>- Não há serviços encerrados -<?
					}

					?>
				</div>
			</section>
		</div>		

	</div>
</div><!-- row -->

<script src='js/jquery.starsrating.js'></script>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){

		$(document).on('click', 'tr.handler:not(.active)', function(){
			var clicado = $(this);
			clicado.addClass('active');
			//clicado.next('tr.detalhes').fadeIn('fast');
			clicado.next('tr.detalhes').fadeIn('fast');
		});

		$(document).on('click', 'tr.handler.active', function(){
			var clicado = $(this);
			clicado.removeClass('active');
			clicado.next('tr.detalhes').fadeOut('fast');
		});

		$(document).on('click', '.trigger-historico', function(e){
			e.preventDefault();
			$(this).next('table').fadeIn('fast');
			$(this).remove();
		});

		$(document).on('click', '.trigger-avaliacao', function(e){
			e.preventDefault();
			var clicado = $(this);
			clicado.closest('.wrapper-detalhes').hide().next('.wrapper-avaliacao').fadeIn('fast', function(){
				$(window).scrollTo('#servico-'+clicado.attr('data-servico'), 250);
			});
		});

		/* controles da avaliacao */

		/* acende os botoes do servico realizado */
		$(document).on('click', '.trigger-servico-realizado', function(){
			var clicado = $(this);
			if(clicado.attr('data-real-value') == 'sim'){
				clicado.removeClass('secondary').addClass('success');
				clicado.closest('.columns').find('[data-real-value="nao"]').removeClass('alert').addClass('secondary');
				clicado.closest('.columns').find('input[type="hidden"]').val('sim');
			}
			else if(clicado.attr('data-real-value') == 'nao') {
				clicado.removeClass('secondary').addClass('alert');
				clicado.closest('.columns').find('[data-real-value="sim"]').removeClass('success').addClass('secondary');
				clicado.closest('.columns').find('input[type="hidden"]').val('nao');
			}
		});

		/* botão para informar a manutenção */
		$(document).on('click', '.trigger-informar', function(){
			alert("<?= MESSAGE_AVISE_SECAO ?>");
		});

		/* submissão do formulario */
		$(document).on('submit', '.form-avaliacao', function(){
			//usar peixePost() com o peixelaranja JSFW
			peixePost(
				$(this).attr('action'),
				$(this).serialize(),
				function(data) {
					console.log(data);
					var result = $.parseJSON(data);
					if(result.message){
						if(result.success){
							setPeixeMessage("<div class='success'>"+result.message+"</div>");
						}
						else if(result.error){
							setPeixeMessage("<div class='error'>"+result.message+"</div>");
						}
						else {
							setPeixeMessage(result.message);
						}
						showPeixeMessage();
					}
					if(result.success){
						peixeGet(document.URL, function(d){
							d = $.parseHTML(d);
							var encerrados = $(d).find('#content-encerrados').html();
							console.log(encerrados);
							$('#content-encerrados').html(encerrados);
							$('#servico-'+result.id_requisicao_item).fadeOut().next('tr.detalhes').fadeOut();
						})
					}
				}
			)
			return false;
		});
	}) //doc.ready
</script>

<? require_once('footer.php'); ?>