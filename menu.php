<? require_once('lib/includes.php'); ?>

<ul class="left">
	<li id='menu-cadastros'><a href='cadastros.php'>Cadastros</a></li>
</ul>
<ul class="right">
<?
	if($_pes->id)
	{
		?>
		<li class="has-dropdown">
			<a>Olá, <?= $_pes->user; ?>.</a>
			<ul class="dropdown">
				<li><label>Opções</label></li>
				<li><a href='logout.php'>Sair</a></li>
			</ul>
		</li>
		<?
	}
	else
	{
		if(PAGINA_ATUAL != 'login.php')
		{
		?>
			<li><a href="login.php">Faça seu login</a></li>
		<?
		}
	}
?>
</ul>