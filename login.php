<?
if(!file_exists('custom-login.php'))
{
	require('header.php');	
	
	dbo_login();

	if(!loggedUser())
	{
		?>
		<div class='row'>
			<div class='large-8 large-offset-2 columns'>

				<fieldset class="radius">
					<legend>Login</legend>
					<? getLoginForm(getDboContext()); ?>
				</fieldset>

			</div>
		</div><!-- row -->
		<?
		
	}
	require('footer.php');
}
else
{
	include('custom-login.php');
}

?>