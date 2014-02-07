<? require('header.php') ?>
<? require_once('auth.php') ?>
<?
	if(!logadoNoPerfil('Desenv') && !hasPermission('painel-cadastros'))
	{
		header("Location: index.php");
		exit();
	}
?>

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
	<div class='large-9 columns'>
		<h3>Cadastros</h3>
		<ul class="large-block-grid-4 small-block-grid-2" id='cockpit-big-buttons'>
			<? makeDboButtons('dbo_admin.php'); ?>
		</ul>
	</div>
	<div class='large-3 columns'>
		<? include('sidebar.php'); ?>
	</div><!-- col -->
</div><!-- row -->

<script>
	$(document).ready(function(){
		activeMainNav('cadastros');
		activeMainNav('sistema');
	}) //doc.ready
</script>

<? require('footer.php') ?>