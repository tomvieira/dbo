<? require_once('lib/includes.php'); ?>
<? require_once('auth.php'); ?>
<!doctype html>
<html dir="ltr" lang="pt-BR">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?= $_conf->nome_secao ?></title>
	<meta name="description" content="">
	<meta name="author" content="José Eduardo Biasioli">
	<!-- <meta name="viewport" content="width=device-width" /> -->

	<link rel="shortcut icon" href="images/favicon.ico"><!-- 16x16 -->
	<link rel="icon" href="images/icon.png" sizes="32x32"><!-- do other sizes if necessary -->
	<link rel="apple-touch-icon" href="images/apple-touch-icon-iphone.png" /><!-- 57x57 -->
	<link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-ipad.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-iphone4.png" />

	<!-- remeber to update when 2.0 is out -->
	<script src="js/jquery.js"></script>
	<script src="js/peixelaranja.js"></script>

	<?= dboHead(); ?>
	
	<link rel="stylesheet" media="screen" href="css/common.css">
	<link rel="stylesheet" media="screen" href="css/style-sistema.css">
	<link rel="stylesheet" media="screen" href="css/style-sistema-modal.css">
	<!-- <link rel="stylesheet" media="screen and (max-width: 970px) and (min-width: 729px) " href="style-medium.css"> -->
	<!-- <link rel="stylesheet" media="screen and (max-width: 730px)" href="style-small.css"> -->
</head>
<body>
