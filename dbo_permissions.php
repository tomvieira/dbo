<? require('header.php') ?>
<? require('auth.php') ?>
<?

	//checa para ver se esse usuário pode estar aqui....
	if(DBO_PERMISSIONS)
	{
		if(!hasPermission('Permissões', 'perfil'))
		{
			setMessage("<div class='error'>Seu usuário não tem permissão de acesso à essa página.</div>");
			$dbo->myHeader("Location: index.php");
		}
	}

	if($_POST['flag_update'])
	{
		CSRFCheckRequest();

		if(is_array($_POST['permission']))
		{
			foreach($_POST['permission'] as $mod => $perm_array)
			{
				if(is_array($perm_array))
				{
					$permission_array[] = $mod."###".@implode("|||", $perm_array);
				} else {
					$permission_array[] = $mod;
				}
			}
			$permission_string = @implode(" %%% ", $permission_array);
		}

		$obj = new dbo('perfil');
		$obj->id = $_POST['flag_update'];
		$obj->load();
		$obj->permissao = $permission_string;
		$obj->update();
		setMessage("<div class='success'>Permissões atualizadas com sucesso.</div>");
		$dbo->myHeader("Location: ".$dbo->keepURL());
	}
?>

<?
	if($_GET['perfil'])
	{
		$obj = new dbo('perfil');
		$obj->id = $_GET['perfil'];
		$obj->load();
	}

	$d = dir(DBO_PATH);
	while (false !== ($entry = $d->read())) {
		if(strpos($entry, "_dbo_") === 0)
		{
			$arq_modulos[] = $entry;
		}
	}
	$d->close();

	function getButtons ($arq)
	{
		global $modulos_array;
		include(DBO_PATH."/".$arq);
		$modulos_array[$module->order_by][$module->modulo]['Cockpit'] = 'cockpit';
		$modulos_array[$module->order_by][$module->modulo]['Sidebar'] = 'sidebar';
		$modulos_array[$module->order_by][$module->modulo]['Acesso'] = 'access';
		$modulos_array[$module->order_by][$module->modulo]['Inserir'] = 'insert';
		$modulos_array[$module->order_by][$module->modulo]['Editar'] = 'update';
		$modulos_array[$module->order_by][$module->modulo]['Excluir'] = 'delete';
		$modulos_array[$module->order_by][$module->modulo]['Visualizar'] = 'view';
		if(is_array($module->button))
		{
			foreach($module->button as $chave => $valor)
			{
				$modulos_array[$module->order_by][$module->modulo][$valor->value] = $valor->value;
			}
		}
	}

	$modulos_array = array();

	foreach($arq_modulos as $arq)
	{
		getButtons($arq);
	}

	if(sizeof($modulos_array))
	{
		ksort($modulos_array);
		?>
		<div class='wrapper-permissions'>

			<div class='row full'>
				<div class='large-12 columns'>
					<h2><a href="dbo_admin.php?dbo_mod=perfil">Permissões de Perfil:</a> <?= $obj->nome ?></h2>
				</div><!-- col -->
			</div><!-- row -->

			<div class='row full'>
				<div class='large-12 columns'>
					<h3>Módulos</h3>
				</div><!-- col -->
			</div><!-- row -->
			
			<form method='POST' action='?perfil=<?= $_GET['perfil'] ?>'>
				<div class='row full'>
					<div class='large-12 columns'>
						<table>
							</thead>
							<tbody>
							<?
								foreach($modulos_array as $modulo)
								{
									foreach($modulo as $chave => $valor)
									{
									?>
										<tr>
											<td style='width: 15%;'><h6 class="no-margin inline header" id='<?= $chave ?>'><?= $chave ?></h6></td>
											<td>
											<?
												foreach($valor as $item_chave => $item_permissao)
												{
													$perm = false;
													$perm = perfilHasPermission($_GET['perfil'], $item_permissao, $chave);
												?>
													<span class='item <?= (($perm)?(''):('off')) ?>'><input rel='<?= $chave ?>' type='checkbox' <?= $perm ? 'CHECKED' : '' ?> value='<?= $item_permissao ?>' name='permission[<?= $chave ?>][<?= $item_chave ?>]'> <span><?= $item_chave ?></span></span>
												<?
												}
											?>
											</td>
										</tr>
									<?
									}
								}
							?>
							</tbody>
						</table>
					</div><!-- col -->
				</div><!-- row -->
			<?

			//custom menus
			$cm = dboCustomMenus();
			if(sizeof($cm))
			{
				?>
				<div class='row full'>
					<div class='large-12 columns'>
						<h3>Menus Custom</h3>
					</div><!-- col -->
				</div><!-- row -->
				<div class='row full'>
					<div class='large-12 columns'>
						<table>
							<tbody>
								<tr>
								<?
									foreach($cm as $key => $item_cm)
									{
										$perm_cockpit = false;
										$perm_sidebar = false;
										$perm_cockpit = perfilHasPermission($_GET['perfil'], 'cockpit', $item_cm->slug);
										$perm_sidebar = perfilHasPermission($_GET['perfil'], 'sidebar', $item_cm->slug);
									?>
									<tr>
										<td style='width: 15%;'><h6 class="header no-margin inline" id='<?= $item_cm->slug ?>'><?= $item_cm->slug ?></h6></td>
										<td>
											<span class='item <?= (($perm_cockpit)?(''):('off')) ?>'><input type='checkbox' rel='<?= $item_cm->slug ?>' <?= $perm_cockpit ? 'CHECKED' : '' ?> value='cockpit' name='permission[<?= $item_cm->slug ?>][Cockpit]'> <span>Cockpit</span></span>
											<span class='item <?= (($perm_sidebar)?(''):('off')) ?>'><input type='checkbox' rel='<?= $item_cm->slug ?>' <?= $perm_sidebar ? 'CHECKED' : '' ?> value='sidebar' name='permission[<?= $item_cm->slug ?>][Sidebar]'> <span>Sidebar</span></span>
										</td>
									</tr>
									<?
									}
								?>
								</tr>
							</tbody>
						</table>
					</div><!-- col -->
				</div><!-- row -->
				<?
			}

			$obj = new dbo('permissao');
			$obj->loadAll('ORDER BY nome');
			if($obj->size())
			{
				?>
				<div class='row full'>
					<div class='large-12 columns'>
						<h3>Permissões Custom</h3>
					</div><!-- col -->
				</div><!-- row -->
				<div class='row full'>
					<div class='large-12 columns'>
						<table>
							<tbody>
							<?
								do {
									$perm = false;
									$perm = perfilHasPermission($_GET['perfil'], $obj->nome);
									?>
									<tr>
										<td style="width: 15%"><h6 class="header inline no-margin" id='permissao-custom-<?= $obj->nome ?>'><?= $obj->nome ?></h6></td>
										<td>
											<span class='item <?= ((perfilHasPermission($_GET['perfil'], $obj->nome))?(''):('off')) ?>'><input type='checkbox' rel='permissao-custom-<?= $obj->nome ?>' <?= $perm ? 'CHECKED' : '' ?> value='<?= $obj->nome ?>' name='permission[<?= $obj->nome ?>]'></span>
										</td>
									</tr>
									<?
								} while($obj->fetch());
							?>
							</tbody>
						</table>
					</div>
				</div><!-- row -->
				<?
			}
			?>
			<div class='row full'>
				<div class='large-12 columns'>
					<input type='submit' class="button radius" value='Atualizar Permissões'>
				</div>
			</div><!-- row -->
			<input type='hidden' name='flag_update' value='<?= $_GET['perfil'] ?>'>
			<?= CSRFInput() ?>
			</form>
		</div><!-- wrapper-permissions -->
		<?
	}

?>

<script>
	$('.header').click(function(){
		var id = $(this).attr('id');
		$('input[rel='+id+']').each(function(){
			$(this).trigger('click');
		})
	})

	$(document).on('change', 'input[type="checkbox"]', function(){
		if($(this).is(':checked')){
			$(this).closest('span').removeClass('off');
		}
		else {
			$(this).closest('span').addClass('off');
		}
	});

	$(document).on('click', '.item span', function(){
		$(this).closest('.item').find('input[type="checkbox"]').trigger('click');
	});

	$(document).on('submit', 'form', function(){
		//usar peixePost() com o peixelaranja JSFW
		peixePost(
			$(this).attr('action'),
			$(this).serialize(),
			function(data) {
				setPeixeMessage("<div class='success'>Permissões atualizadas com sucesso!</div>");
				showPeixeMessage();
			}
		)
		return false;
	});

</script>

<? require('footer.php') ?>