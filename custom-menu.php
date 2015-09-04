<? require_once('lib/includes.php'); ?>

<?
	if($_pes->size())
	{
		?>
		<ul class="left">
			<?= equipamento::getMenuEquipamentosSemFoto(array('hide_acoes' => true)); ?>
			<?= ((hasPermission('painel-principal'))?("<li id='menu-painel-principal'><a href='painel.php'>Serviços".$_sistema->addNotificationContainer('pendente')."</a></li>"):('')) ?>
			<?= ((hasPermission('painel-estoque') || hasPermission('painel-inventario'))?('<li id="menu-painel-materiais"><a href="painel-materiais.php?clear_detalhes=1">Materiais'.$_sistema->addNotificationContainer('materiais').'</a></li>'):('')) ?>
			<?= ((hasPermission('painel-servidores'))?("<li id='menu-painel-servidores'><a href='servidores.php'>Responsáveis</a></li>"):('')) ?>
			<?= ((hasPermission('calendario'))?("<li id='menu-calendario'><a href='calendario.php'>Calendário".$_sistema->addNotificationContainer('calendario')."</a></li>"):('')) ?>
			<li id="menu-nova-requisicao"><a href="requisicao.php" title="<?= ((hasPermission('painel-principal'))?('Nova Requisição'):('')) ?>"><?= ((hasPermission('painel-principal'))?('<span class="show-for-small inline">Nova Requisição</span><span class="hide-for-small"><i class="fa fa-fw fa-plus-circle"></i></span>'):("Nova Requisição")) ?></a></li>
			<li id="menu-acompanhamento"><a href="acompanhamento.php" title="<?= ((hasPermission('painel-principal'))?('Acompanhamento'):('')) ?>"><?= ((hasPermission('painel-principal'))?('<span class="show-for-small inline">Acompanhamento</span><span class="hide-for-small"><i class="fa fa-fw fa-book"></i></span>'):('Acompanhamento')) ?><?= $_sistema->addNotificationContainer('acompanhamento') ?></a></li>
			<?
				if(hasPermission('menu-sistema'))
				{
					?>
					<li class="has-dropdown" id="menu-sistema">
						<a href='#' title="Sistema"><span class="show-for-small inline">Sistema</span><span class="hide-for-small"><i class="fa fa-fw fa-gear"></i></span></a>
						<ul class="dropdown">
							<li><label>Opções</label></li>
							<?= ((hasPermission('painel-cadastros'))?("<li id='menu-painel-cadastros'><a href='cadastros.php'>Cadastros</a></li>"):('')) ?>
							<?= ((hasPermission('painel-ferramentas'))?('<li id="menu-ferramentas"><a href="ferramentas.php">Ferramentas</a></li>'):('')) ?>
							<?= ((hasPermission('painel-relatorios'))?("<li id='menu-painel-cadastros'><a href='relatorios.php'>Relatórios</a></li>"):('')) ?>
							<?= ((hasPermission('configuracoes'))?("<li id='menu-painel-cadastros'><a href='dbo_admin.php?dbo_mod=config&dbo_update=1'>Configurações</a></li>"):('')) ?>
						</ul>						
					</li>
					<?
				}	
			?>
		</ul>
		<?
	}
?>
<ul class="right">
<?
	if($_pes->id)
	{
		if(method_exists($_pes, 'contextMenu'))
		{
			echo $_pes->contextMenu();
		}
		else
		{
			?>
			<li class="has-dropdown">
				<a>Olá, <?= $_pes->getShortName(); ?>.</a>
				<ul class="dropdown">
					<li><label>Opções</label></li>
					<li></li>
					<li><a href='logout.php'>Sair</a></li>
				</ul>
			</li>
			<?
		}
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