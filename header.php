<? require_once('lib/includes.php'); ?>
<?
	/* se não existir o custom header, usar o header padrão. */
	if(!file_exists('custom-header.php'))
	{
		?>

<!doctype html>
<html dir="ltr" lang="pt-BR">
<head>
	<!-- <meta name="robots" content="noindex"> -->
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?= SYSTEM_NAME ?> - <?= SYSTEM_DESCRIPTION ?></title>
	<meta name="description" content="">
	<meta name="author" content="Peixe Laranja">

	<link rel="shortcut icon" href="images/favicon.ico"><!-- 16x16 -->
	<link rel="icon" href="images/icon.png" sizes="32x32"><!-- do other sizes if necessary -->
	<link rel="apple-touch-icon" href="images/apple-touch-icon-iphone.png" /><!-- 57x57 -->
	<link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-ipad.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-iphone4.png" />

	<!-- Foundation 4.3.0 -->
	<link rel="stylesheet" href="css/foundation.css" />
	<script src="js/vendor/custom.modernizr.js"></script>
	<!-- <script>document.write('<script src=' +	('__proto__' in {} ? 'js/vendor/zepto' : 'js/vendor/jquery') +	'.js><\/script>')</script> -->
	<script src="js/vendor/jquery.js"></script>
	<script src="js/foundation.min.js"></script>
	<!-- <script src="js/foundation/foundation.js"></script> -->
	<!-- <script src="js/foundation/foundation.alerts.js"></script> -->
	<!-- <script src="js/foundation/foundation.clearing.js"></script> -->
	<!-- <script src="js/foundation/foundation.cookie.js"></script> -->
	<script src="js/foundation/foundation.dropdown.js"></script>
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

	<?= dboHead(); ?>

	<meta name="viewport" content="width=device-width" />
	<link rel="stylesheet" media="screen" href="css/common.css">
	<link rel="stylesheet" media="screen" href="css/style-dbo.css">
	<link rel="stylesheet" media="screen" href="fonts/museo-sans/stylesheet.css">
	<link rel="stylesheet" media="screen" href="css/style.css">

	<script type="text/javascript" charset="utf-8" src='js/colorbox/jquery.colorbox.js'></script>
	<script type="text/javascript" charset="utf-8" src='js/colorbox/jquery.colorbox.scrollfix.js'></script>
	<link rel="stylesheet" media="screen" href="js/colorbox/colorbox.css">

	<script src='js/jquery.autosize.js'></script>
	<script src='js/jquery.scrollto.js'></script>

	<style>
		<?
			if(!logadoNoPerfil('Desenv')) {
				echo ".dev { display: none !important; }";
			}
		?>
	</style>

</head>
<body class="dbo <?= (($_GET['dbo_modal'])?('modal'):((($_GET['dbo_modal_no_fixos'])?('modal no-fixos'):('')))) ?>">

	<?= browserWarning(); ?>

	<?= dboBody(); ?>

	<div id="main-header">

		<div class='row first-row hide-for-small'>
			<div class='large-10 columns'>
				<ul class="bread-crumb">
				</ul>
			</div>
			<div class='large-2 columns text-right'></div>
		</div><!-- row -->
		
		<div class="nome-sistema">
			<div class='row'>
				<div class='large-7 columns'>
					<h2><?= SYSTEM_NAME ?></h2>
				</div>
				<div class='large-5 columns hide-for-small tar'>
					<span><?= SYSTEM_DESCRIPTION ?></span>
				</div>
			</div><!-- row -->
		</div>

		<div class="main-tabs cf contain-to-grid">
			<nav class="top-bar">
				<section class="top-bar-section">
					<ul class="title-area">
						<li class="name"></li>
						<li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
					</ul>
					<?
						include(((file_exists('custom-menu.php'))?('custom-menu.php'):('menu.php')));
					?>
				</section>
			</nav>
		</div><!-- main-tabs -->
	</div><!-- main-header -->

	<div id="main-wrap">

		<?
	}
	else
	{
		include('custom-header.php');
	}
?>