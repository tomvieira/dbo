<? require('header.php') ?>
<? require_once('auth.php') ?>
<?
	if(logadoNoPerfil('Servidor'))
	{
		header("Location: servidores.php");
		exit();
	}
	if(hasPermission('gerenciar-servicos'))
	{
		header("Location: painel.php");
		exit();
	}
	header("Location: requisicao.php");
	exit();
?>
<? require('footer.php') ?>