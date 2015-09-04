<? 
	require_once('lib/includes.php'); 
	if(!$_GET['servidor'])
	{
		criticalError("Nenhum servidor especificado");
	}
	else
	{
		$servidor = new servidor(addslashes($_GET['servidor']));
	}
?>
<!doctype html>
<html dir="ltr" lang="pt-BR">
<head>
	<!-- <meta name="robots" content="noindex"> -->
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>Manutenção - Serviços em andamento - <?= $servidor->getShortName() ?></title>
	<meta name="description" content="">
	<meta name="author" content="Peixe Laranja">

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

	<meta name="viewport" content="width=device-width" />
	<link rel="stylesheet" media="screen" href="css/style-view.css">

	<style>
		.caixa-anotacoes { height: 100%; }
	</style>

</head>
<body>
	<?
		$serv = new requisicao_item("WHERE status = '".STATUS_EM_ANDAMENTO."' AND id IN (SELECT item FROM requisicao_item_servidor WHERE servidor = '".$servidor->id."')");
		if($serv->size())
		{
			?>
			<h3>Manutenção <small>Serviços em andamento: <?= $servidor->getShortName() ?></small></h3>
			<div id="wrapper-servicos-em-andamento">
			<table>
				<thead>
					<tr>
						<th colspan='2'><h6>Informações</h6></th>
						<th><h6>Anotações</h6></th>
					</tr>
				</thead>
				<tbody>
				<?
					do {
						$loc = new local($serv->local);
						$req = new requisicao($serv->requisicao);
						?>
						<tr>
							<td>
								<ul class="itens-servico">
									<li>
										<label>Local</label>
										<?= $loc->getSmartLocal() ?>
									</li>
									<li>
										<label>Serviço</label>
										<?= $serv->___tipo_servico___nome ?>
									</li>
									<li>
										<label>Descrição</label>
										<?= $serv->___tipo_servico___nome ?>
									</li>
								</ul>
							</td>
							<td><p><?= $serv->descricao ?></p></td>
							<td>
								<div class="caixa-anotacoes"></div>
							</td>
						</tr>
						<?
					}while($serv->fetch());				
				?>
				</tbody>
			</table>
			</div><!-- wrapper-servicos-em-andamento -->
			<?
		}
		else
		{
			?>
			<div class='row full'>
				<div class='large-8 columns large-offset-2' style='padding-top: 5em;'>
					<div class="panel radius text-center">
						<p>O servidor <strong><?= $servidor->getShortName() ?></strong> não possui serviços em andamento.</p>
					</div>
				</div><!-- col -->
			</div><!-- row -->
			<?
		}
	?>

	<script>
		$(document).foundation();
	</script>
</body>
</html>