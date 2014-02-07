<? require('header.php') ?>
<? require('auth.php') ?>

<?

	$obj = new Dbo($_GET['dbo_mod']);
	$obj->autoAdmin();

?>

<script>
	$(document).ready(function(){
		activeMainNav('cadastros');
		activeMainNav('sistema');
	}) //doc.ready
</script>

<? require('footer.php') ?>