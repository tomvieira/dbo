<? require_once('lib/includes.php'); ?>
<!doctype html>
<html dir="ltr" lang="pt-BR">
<head>
	<!-- <meta name="robots" content="noindex"> -->
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?= SYSTEM_DESCRIPTION ?></title>
	<meta name="description" content="">
	<meta name="author" content="Peixe Laranja">

	<link rel="icon" href="images/notification-icons/0.png" sizes="16x16">
	<link rel="manifest" href="<?= DBO_URL ?>/manifest.json">

	<!-- Foundation 4.3.0 -->
	<link rel="stylesheet" href="css/foundation.css" />

	<?= dboHead(); ?>

	<meta name="viewport" content="width=device-width" />
	<link rel="stylesheet" media="screen" href="css/common.css">
	<link rel="stylesheet" media="screen" href="css/style-dbo.css">
	<link rel="stylesheet" media="screen" href="fonts/museo-sans/stylesheet.css">
	<link rel="stylesheet" media="screen" href="fonts/font-awesome/css/font-awesome.css">
	<link rel="stylesheet" media="screen" href="<?= CENTRAL_DE_ACESSOS_URL ?>/css/style-central.css">
	<link rel="stylesheet" media="screen" href="css/style-sistema.css">
	<link rel="stylesheet" media="screen" href="css/csshake.css">

	<?= dboImportJs(array('colorbox')) ?>

	<script src='<?= CENTRAL_DE_ACESSOS_URL ?>/js/central.js'></script>
	<script src='js/sistema.js'></script>

	<style>
	<?
		if(!logadoNoPerfil('Desenv'))
		{
			echo '.dev { display: none; }';
		}
	?>
	</style>

	<?= ((loggedUser() == 1)?('<script>var dev = true;</script>'):('')) ?>

	<?= $_sistema->htmlHead(); ?>

</head>
<body class="dbo <?= $_GET['body_class'] ?> <?= (($_GET['dbo_modal'])?('modal'):((($_GET['dbo_modal_no_fixos'])?('modal no-fixos'):('')))) ?>">

	<?= browserWarning(); ?>
	<?= dboBody(); ?>

	<div id="main-header">

		<div class='row first-row hide-for-small'>
			<div class='large-10 columns'>
				<div class="breadcrumb">
					<ul class="main-breadcrumb"></ul>
				</div>
			</div>
			<div class='large-2 columns text-right'>
				<?php
					if(!logadoNoPerfil('Desenv')) echo '<img src="'.CENTRAL_DE_ACESSOS_URL.'/images/logo-unesp-header-color.png">';
				?>
			</div>
		</div><!-- row -->
		
		<div class="nome-sistema">
			<div class='row'>
				<div class='large-7 columns'>
					<h2><i></i> <?= HEADER_NAME ?></h2>
				</div>
				<div class='large-5 columns hide-for-small tar'>
					<span><?= UNIDADE_NAME ?></span>
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
