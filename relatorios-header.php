<?
	require_once('lib/includes.php');

	$data_i = (($_GET['data_i'])?(dboescape($_GET['data_i'])):('2013-09-01'));
	$data_f = (($_GET['data_f'])?(dboescape($_GET['data_f'])):(date('Y-m-d')));
?>
<!doctype html>
<html dir="ltr" lang="pt-BR">
<head>
	<!-- <meta name="robots" content="noindex"> -->
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?= $_conf->nome_secao ?></title>
	<meta name="description" content="">
	<meta name="author" content="">

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
	<link rel="stylesheet" href="css/style-dbo-relatorios.css">
	<link rel="stylesheet" href="css/style-relatorio.css">

</head>
<body>

<a onClick="window.print();" class="no-print button-print" style="top: 5px;">clique para<br />imprimir</a>