<?

require('header.php');	

dboLogin();

if(!loggedUser())
{
	?>
	<div class="row">
		<div class="large-8 columns large-centered">
			<div class="panel radius">
				<h3>Atenção</h3>
				<p>
					Você precisa estar <strong>logado</strong> para fazer uma requisição.<br />
					Utilize os dados do seu <strong>e-mail</strong> para logar.
				</p>
			</div>
		</div>
	</div>

	<div class='row'>
		<div class='large-8 columns large-centered'>

			<fieldset class="radius">
				<legend>Login</legend>
				<? getLoginForm(getDboContext()); ?>
			</fieldset>

		</div>
	</div><!-- row -->
	<?
}
require('footer.php');
?>