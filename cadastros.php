<? require('header.php') ?>
<? require_once('auth.php') ?>
<?
	if(!logadoNoPerfil('Desenv') && !hasPermission('painel-cadastros'))
	{
		header("Location: index.php");
		exit();
	}

	$nro_itens_sidebar = getItemsSidebar();
?>

<div class="row">
	<div class="large-12 columns">
		<div class="breadcrumb">
			<ul class="no-margin">
				<li><a href="cadastros.php"><?= DBO_TERM_CADASTROS ?></a></li>
			</ul>
		</div>
	</div>
</div>

<hr>

<div class='row'>
	<div class='large-12 columns'>
	<?
		//mensagem para usar em desenv.
		//setWarning('<b>O sistema está em manutenção no momento. Consulte o desenvolvedor para se informar sobre limitações de uso.</b>');
		checkPermissions();
		getWarning();
	?>
	</div>
</div><!-- row -->

<div class='row'>
	<div class='large-<?= (($nro_itens_sidebar)?(9):(12)) ?> columns'>
		<ul class="large-block-grid-<?= (($nro_itens_sidebar)?(4):(5)) ?> small-block-grid-2" id='cockpit-big-buttons'>
			<? makeDboButtons('dbo_admin.php'); ?>
		</ul>
	</div>
	<?
		if($nro_itens_sidebar)
		{
			?>
			<hr class="show-for-small">
			<div class='large-3 columns'>
				<? include('sidebar.php'); ?>
			</div><!-- col -->
			<?
		}
	?>
</div><!-- row -->

<script>
	$(document).ready(function(){
		activeMainNav('cadastros');
		activeMainNav('sistema');
	}) //doc.ready
</script>

<? require('footer.php') ?>