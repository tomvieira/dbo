<?
	require_once('lib/includes.php');

	$serv = new requisicao_item("WHERE id = '".addslashes($_GET['id'])."' AND token = '".addslashes($_GET['token'])."'");
	if(!$serv->size())
	{
		criticalError('O serviço não existe');
	}
	$loc = new local($serv->local);
	$req = new requisicao($serv->requisicao);

	if($_GET['token_avaliacao'])
	{
		$sql = "WHERE requisicao_item = '".$serv->id."' AND token = '".addslashes($_GET['token_avaliacao'])."'";
		$aval = new avaliacao($sql);
		$avaliacao = true;
	}

?>
<!doctype html>
<html dir="ltr" lang="pt-BR">
<head>
	<!-- <meta name="robots" content="noindex"> -->
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>Serviço <?= $serv->requisicao ?>/<?= $serv->numero ?></title>
	<meta name="description" content="">

	<link rel="shortcut icon" href="images/favicon.ico"><!-- 16x16 -->
	<link rel="icon" href="images/icon.png" sizes="32x32"><!-- do other sizes if necessary -->
	<link rel="apple-touch-icon" href="images/apple-touch-icon-iphone.png" /><!-- 57x57 -->
	<link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-ipad.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-iphone4.png" />

	<!-- Foundation 4.3.0 -->
	<link rel="stylesheet" href="css/foundation.css" />
	<link rel="stylesheet" href="css/common.css" />
	<script src="js/vendor/custom.modernizr.js"></script>
	<!-- <script>document.write('<script src=' +	('__proto__' in {} ? 'js/vendor/zepto' : 'js/vendor/jquery') +	'.js><\/script>')</script> -->
	<script src="js/vendor/jquery.js"></script>
	<script src="js/foundation.min.js"></script>

	<script type="text/javascript" charset="utf-8" src='js/colorbox/jquery.colorbox.js'></script>
	<script type="text/javascript" charset="utf-8" src='js/colorbox/jquery.colorbox.scrollfix.js'></script>
	<link rel="stylesheet" media="screen" href="js/colorbox/colorbox.css">
	
	<!-- <script src="js/foundation/foundation.js"></script> -->
	<!-- <script src="js/foundation/foundation.alerts.js"></script> -->
	<!-- <script src="js/foundation/foundation.clearing.js"></script> -->
	<!-- <script src="js/foundation/foundation.cookie.js"></script> -->
	<!-- <script src="js/foundation/foundation.dropdown.js"></script> -->
	<!-- <script src="js/foundation/foundation.forms.js"></script> -->
	<!-- <script src="js/foundation/foundation.joyride.js"></script> -->
	<!-- <script src="js/foundation/foundation.magellan.js"></script> -->
	<!-- <script src="js/foundation/foundation.orbit.js"></script> -->
	<!-- <script src="js/foundation/foundation.reveal.js"></script> -->
	<!-- <script src="js/foundation/foundation.section.js"></script> -->
	<!-- <script src="js/foundation/foundation.tooltips.js"></script> -->
	<!-- <script src="js/foundation/foundation.topbar.js"></script> -->
	<!-- <script src="js/foundation/foundation.interchange.js"></script> -->
	<!-- <script src="js/foundation/foundation.placeholder.js"></script> -->
	<!-- <script src="js/foundation/foundation.abide.js"></script> -->

	<!-- Peixe Laranja JSFW -->
	<script src="js/peixelaranja.js"></script>
	<script src='js/sistema.js'></script>
	<script src='js/jquery.starsrating.js'></script>
	<script src='js/dbo.js'></script>

	<meta name="viewport" content="width=device-width" />
	<link rel="stylesheet" media="screen" href="css/style-sistema.css">
	<link rel="stylesheet" media="screen" href="css/style-sistema-view.css">

</head>
<body>

	<?= browserWarning(); ?>

	<div class='row' style='margin-top: 2em;'>
		<div class='large-4 columns nowrap'>
			<h2 class="subheader">Serviço nº <?= $serv->requisicao ?>/<?= $serv->numero ?></h2>
		</div>
		<div class='large-8 columns text-right'>
			<p>
				Aberto em: <strong><?= date('d/m/Y H:i', strtotime($req->created_on)) ?></strong><br />
				Requisitante: <strong><?= $req->nome_requisitante ?> &bull; tel: <?= $req->telefone_requisitante ?></strong>
			</p>
		</div><!-- col -->
		<hr>
	</div><!-- row -->

	<?
		if($avaliacao == true)
		{
			if($aval->size() && intval($aval->respondido_em) == 0)
			{
				?>
				<div class='row' id='wrapper-aviso-usuario'>
					<div class='large-10 large-offset-1 columns'>
						<div class="panel radius">
							<p>Caro(a) <strong><?= $req->nome_requisitante ?></strong>,</p>
							<p>Este serviço foi informado como <strong>concluído</strong> pel<?= (($_conf->genero == 'm')?('o'):('a')) ?> <?= $_conf->nome_secao ?>.</p>
							<p>Para podermos constantemente melhorar a qualidade de nosso atendimento, pedimos que você faça a <strong>avaliação do serviço</strong>. Bastam <strong>alguns clicks</strong>.</p>
							<p class="text-right"><input type='button' name='' value="Iniciar avaliação" class="button radius no-margin trigger-avaliacao"/></p>
						</div>
					</div><!-- col -->
					<hr>
				</div><!-- row -->
				<div class='row hidden' id='wrapper-avaliacao'>
					<div class='large-10 large-offset-1 columns'>
						<div class="panel radius">
							<?= $aval->getFormAvaliacao() ?>
						</div>
					</div><!-- col -->
					<hr>
				</div><!-- row -->
				<div class='row hidden' id='wrapper-obrigado'>
					<div class='large-6 large-offset-3 columns'>
						<div class="panel radius text-center">
							<h3>Muito obrigado!</h3>
							<p>Sua colaboração é essencial.</p>
						</div>
					</div><!-- col -->
					<hr>
				</div><!-- row -->
				<?
			}	
		}
	?>

	<div>
		<div class='row' id='servico-info'>
			<div class='large-12 columns'>
				<div class='row'>
					<div class='large-3 columns'><p class="no-margin-for-small"><strong>Tipo de Serviço:</strong></p></div>
					<div class='large-9 columns'><p><?= $serv->___tipo_servico___nome ?> - Prioridade <?= $serv->getValue('prioridade', $serv->prioridade) ?></p></div><!-- col -->
				</div><!-- row -->
				<div class='row'>
					<div class='large-3 columns'><p class="no-margin-for-small"><strong>Local:</strong></p></div>
					<div class='large-9 columns'><p><?= $loc->getSmartLocal() ?></p></div><!-- col -->
				</div><!-- row -->
				<div class='row'>
					<div class='large-3 columns'><p class="no-margin-for-small"><strong>Descrição:</strong></p></div>
					<div class='large-9 columns'><p><?= $serv->descricao ?></p></div><!-- col -->
				</div><!-- row -->
				<?
					$rel = new requisicao_item_equipamento("WHERE requisicao_item = '".$serv->id."' AND situacao IS NOT NULL GROUP BY equipamento");
					if($rel->size())
					{
						?>
						<div class='row'>
							<div class='large-3 columns'><p><strong>Equipamento<?= (($rel->size() > 1)?('s'):('')) ?>:</strong></p></div>
							<div class='large-9 columns'>
								<?
									$sql = "
										SELECT equipamento.* FROM equipamento
										JOIN requisicao_item_equipamento rie ON
											rie.equipamento = equipamento.id
										WHERE rie.requisicao_item = '".$serv->id."'
										GROUP BY id
									";
									$eq = new equipamento();
									$eq->query($sql);
									if($eq->size())
									{
										?>
										<ul class="large-block-grid-2 small-block-grid-1">
											<?
												do {
													?>
													<li class="text-center">
														<a href="<?= (($eq->foto)?(DBO_URL.'/upload/images/'.$eq->foto):('images/sem-foto.png')) ?>" rel="lightbox[album]">
															<img class="th" src="<?= (($eq->foto)?(DBO_URL.'/upload/images/t_'.$eq->foto):('images/sem-foto.png')) ?>"/>
															<p class="font-14 text-center"><?= $eq->getSmartName(); ?></p>
														</a>
													</li>
													<?
												}while($eq->fetch());
											?>
										</ul>
										<?
									}
								?>
							</div><!-- col -->
						</div>					
						<?
					}
				?>
				<h4>Status</h4>
				<div class='row'>
					<div class='large-12 columns'><p><?= $serv->getStatusChart(); ?></p></div><!-- col -->
				</div><!-- row -->
				<?
					if($serv->temMateriaisAtribuidos())
					{
						?>
						<h4>Materiais e custos</h4>
						<div class='row'>
							<div class='large-12 columns'><?= $serv->getTabelaMateriaisUtilizados(array('table_class' => 'historico margin-bottom', 'view_only' => true)); ?></div><!-- col -->
						</div><!-- row -->
						<?
					}
				?>
				<h4>Histórico</h4>
				<div class='row'>
					<div class='large-12 columns' style="padding-bottom: 150px;"><?= $serv->getHistorico('complete'); ?></div><!-- col -->
				</div><!-- row -->
			</div><!-- col -->
		</div>
	</div><!-- row -->
	
	<script>

		$(document).foundation();

		$(document).ready(function(){

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

			$(document).on('click', '.trigger-avaliacao', function(){
				$('#wrapper-aviso-usuario').fadeOut(function(){
					$('#wrapper-avaliacao').fadeIn();
				})
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
							$('#wrapper-avaliacao').fadeOut(function(){
								$('#wrapper-obrigado').fadeIn();
							})
							//usar peixePost() com o peixelaranja JSFW
							peixeGet(
								document.URL,
								function(data) {
									var result = $.parseHTML(data);
									$('#servico-info').replaceWith($(result).find('#servico-info'));
								}
							)
							return false;
						}
					}
				)
				return false;
			});


		}) //doc.ready

	</script>
</body>
</html>