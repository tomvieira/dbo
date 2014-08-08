<? require('header.php') ?>
<? require('auth.php') ?>

<?
	$class_name = dboescape($_GET['dbo_mod']);
	$obj = new $class_name();
	$obj->autoAdmin();
?>

<script>
	$(document).ready(function(){
		activeMainNav('cadastros');
		activeMainNav('sistema');
	}) //doc.ready
</script>

<? require('footer.php') ?>