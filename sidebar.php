<?
	if(file_exists('custom-sidebar.php'))
	{
		include('custom-sidebar.php');
	}
	else
	{
		?>
		<h6>Módulos</h6>
		<ul class="side-nav" id='cockpit-side-nav'>
			<li class="divider"></li>
			<? dboSideBarMenu('dbo_admin.php'); ?>
		</ul>		
		<?
	}
?>