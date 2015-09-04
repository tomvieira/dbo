<? require_once('lib/includes.php'); ?>
<!doctype html>
<html dir="ltr" lang="pt-BR">
<head>
	<meta name="robots" content="noindex">
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>Impressão de Serviços</title>
	<meta name="description" content="">

	<link rel="stylesheet" href="css/style-relatorio.css">

	<style>

		* { box-sizing: border-box; -ms-box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; }

		@media screen {
			body { padding: 20px; }
		}

		@media print {
			html, body { padding: 0; margin: 0; }
		}

		body { font-family:  Arial; }

		ul { padding-left: 1em; }
		
		.panel-left { float: left; }
		.panel-right { float: right; }

	</style>

	<!--[if lt IE 9]>
	<script src="js/html5shiv.js"></script>
	<![endif]-->
</head>
<body>
<?
	$count = 0;
	foreach($_GET['imprimir'] as $key => $value)
	{
		$count++;
		$serv = new requisicao_item($value);
		$req = new requisicao($serv->requisicao);
		$loc = new local($serv->local);
		?>
		<div class="item-impressao cf <?= (($count%2 == 0)?('page-break-after'):('')) ?>">
			<div class="panel-left fleft">
				<ul class="dados-requisicao">
					<li><span class="numero"><?= $serv->requisicao ?>/<?= $serv->numero ?></span><strong style='vertical-align: top;'>Responsáveis:<br /><em><?= implode(', ', $serv->getNomesAtribuidos()) ?></em></strong></li>
					<li><span>Tipo de Seviço:</span><strong><?= $serv->___tipo_servico___nome ?></strong></li>
					<li><span>Local: </span><strong><?= $loc->getSmartLocal(); ?></strong></li>
					<li><span>Descrição: </span><strong><?= maxString($serv->descricao, 650) ?></strong></li>
					<li><span>Prioridade</span><strong><?= $serv->getValue('prioridade', $serv->prioridade); ?></strong></li>
				</ul>
				<ul class="dados-requisitante cf">
					<li><span>Requisitante: </span><strong><?= $req->nome_requisitante ?></strong></li>
					<li><span>Telefone: </span><strong><?= $req->telefone_requisitante ?></strong></li>
					<li><span>Email: </span><strong><?= $req->email_requisitante ?></strong></li>
				</ul>
			</div>
			<div class="paine-right">
				<strong>Anotações:</strong>
			</div>
		</div>
		<?
		if($count%2 != 0)
		{
			?>
			<div class="cutter"></div>
			<?
		}
	}
?>

<a onClick="window.print();" class="no-print button-print" style="top: 5px !important;"><i class="icon-print"></i>clique para<br />imprimir</a>

</body>
</html>