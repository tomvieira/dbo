<? require('header.php') ?>
<? require('auth.php') ?>

<?

	$obj = new Dbo($_GET['dbo_mod']);
	$obj->autoAdmin();

?>

<? require('footer.php') ?>