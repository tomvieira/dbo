
	</div><!-- main-wrap -->

	<? requisicao_item::showRequestedModal(); ?>

	<div id="modal-gerenciar-requisicao" class="reveal-modal smart medium scrollable section-small-toggle" data-reveal></div>
	<div id="modal-dbo-medium" class="reveal-modal smart medium" data-reveal></div>
	<div id="modal-dbo-small" class="reveal-modal smart small" data-reveal></div>

	<script>
		var gerenciar_servicos = <?= ((hasPermission('gerenciar-servicos'))?('true'):('false')); ?>
	</script>

	<?
		if(hasPermission('dashboard') && !$_GET['dbo_modal'])
		{
			echo dashboard();
		}
	?>

	<script>$(document).foundation();</script>
	<?= getMessage(); ?>
	<? dboFooter(); ?>

	<script>
		$(document).ready(function(){
			breadCrumbPush('<a href="<?= CENTRAL_DE_ACESSOS_URL ?>">Central de Acessos</a>');
			breadCrumbPush('<a href="painel.php"><?= BREAD_CRUMB_NAME ?></a>');
		}) //doc.ready
	</script>	

</body>
</html>