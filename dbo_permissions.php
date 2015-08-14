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
		global $_system;

		include(DBO_PATH."/".$arq);
		$modulos_array[$module->order_by][$module->modulo]['Cockpit'] = 'cockpit';
		$modulos_array[$module->order_by][$module->modulo]['Sidebar'] = 'sidebar';
		$modulos_array[$module->order_by][$module->modulo]['Acesso'] = 'access';
		$modulos_array[$module->order_by][$module->modulo]['Inserir'] = 'insert';
		$modulos_array[$module->order_by][$module->modulo]['Editar'] = 'update';
		$modulos_array[$module->order_by][$module->modulo]['Excluir'] = 'delete';
		$modulos_array[$module->order_by][$module->modulo]['Visualizar'] = 'view';

		//instanciando botoes
		if(is_array($module->button))
		{
			foreach($module->button as $chave => $valor)
			{
				$modulos_array[$module->order_by][$module->modulo][$valor->value] = $valor->value;
			}
		}

		//instanciando permissoes custom
		if(strlen(trim($module->permissoes_custom)))
		{
			$permissoes_custom = trim($module->permissoes_custom);
			$permissoes_custom = explode("\n", $permissoes_custom);
			foreach($permissoes_custom as $permissao_custom)
			{
				$_system['module_permissoes_custom'][trim($permissao_custom)] = trim($permissao_custom);
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
		<div class="wrapper-permissions">

			<div class="row full">
				<div class="large-7 columns">
					<h2><a href="dbo_admin.php?dbo_mod=perfil">Permissões de perfil:</a> <?= $obj->nome ?></h2>
				</div><!-- col -->
				<div class="large-5 columns text-right">
					<span class="top-15">Copiar permissões de &nbsp;</span> 
					<select id="copiar" class="no-margin top-15" style="width: auto; display: inline-block">
						<option value="">...</option>
						<?
							$perf = new perfil('ORDER BY nome');
							do {
								?>
								<option value="<?= $perf->id ?>"><?= $perf->nome ?></option>								
								<?
							}while($perf->fetch());
						?>
					</select>
				</div>
			</div><!-- row -->

			<hr class="small">

			<div class="row full">
				<div class="large-12 columns">
					<h3>Módulos</h3>
					<p class="color medium font-14">As permissões desta seção dão acesso aos formulários de cadastro automáticos dos módulos sistema.<br />Elas também valem para módulos que são extensões de páginas.</p>
				</div><!-- col -->
			</div><!-- row -->
			
			<form method="POST" action="dbo_permissions.php?perfil=<?= $_GET['perfil'] ?>">
				<div id="wrapper-all-permissions">
					<div class="row full">
						<div class="large-12 columns">
							<table>
								<thead>
									<th colspan="2"></th>
								</thead>
								<tbody>
								<?
									foreach($modulos_array as $modulo)
									{
										foreach($modulo as $chave => $valor)
										{
										?>
											<tr>
												<td style="width: 20%;"><span class="no-margin inline header" id="<?= $chave ?>"><?= $chave ?></span></td>
												<td>
												<?
													foreach($valor as $item_chave => $item_permissao)
													{
														$perm = false;
														$perm = perfilHasPermission($_GET["perfil"], $item_permissao, $chave);
													?>
														<span class="item <?= (($perm)?(''):('off')) ?>"><input rel="<?= $chave ?>" type="checkbox" <?= $perm ? 'CHECKED' : '' ?> value="<?= $item_permissao ?>" name="permission[<?= $chave ?>][<?= $item_chave ?>]"> <span><?= $item_chave ?></span></span>
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
						//paginas
						if(class_exists('pagina') && sizeof($_system['pagina_tipo']))
						{
							
							$items = array(
								'cockpit' => 'Cockpit',
								'sidebar' => 'Sidebar',
								'insert' => 'Inserir',
								'update' => 'Editar',
								'delete' => 'Excluir',
								'publicar' => 'Publicar',
								'admin' => 'Gerenciar',
								'all' => 'Todas'
							);

							?>
							<div class="row full">
								<div class="large-12 columns">
									<h3>Tipos de página</h3>
									<p class="color medium font-14">As permissões desta seção dão acesso aos tipos específicos de página. Por padrão somente o tipo "Página" está definido.<br />Os demais tipos devem ser configurados no arquivo de defines.</p>
								</div>
							</div>
							<div class="row full">
								<div class="large-12 columns">
									<table>
										<thead>
											<th colspan="2"></th>
										</thead>
										<tbody>
											<?
												foreach($_system['pagina_tipo'] as $pagina_tipo)
												{
													?>
													<tr>
														<td style="width: 20%;"><span class="header no-margin inline" id="pagina-<?= $pagina_tipo['tipo'] ?>"><?= $pagina_tipo['tipo'] ?></span></td>
														<td>
															<?
																foreach($items as $key => $value)
																{
																	$perm = perfilHasPermission($_GET['perfil'], $key, 'pagina-'.$pagina_tipo['tipo']);
																	?>
																	<span class="item <?= (($perm)?(""):("off")) ?>"><input type="checkbox" rel="pagina-<?= $pagina_tipo['tipo'] ?>" <?= $perm ? "checked" : "" ?> value="<?= $key ?>" name="permission[pagina-<?= $pagina_tipo['tipo'] ?>][<?= $key ?>]"> <span><?= $value ?></span></span>
																	<?
																}
															?>
														</td>
													</tr>
													<?
												}
											?>
										</tbody>
									</table>
								</div>
							</div>
							<?
						}

						//custom menus
						$cm = dboCustomMenus();
						if(sizeof($cm) && 1==2)
						{
							?>
							<div class="row full">
								<div class="large-12 columns">
									<h3>Menus custom</h3>
								</div><!-- col -->
							</div><!-- row -->
							<div class="row full">
								<div class="large-12 columns">
									<table>
										<thead>
											<th colspan="2"></th>
										</thead>
										<tbody>
											<tr>
											<?
												foreach($cm as $key => $item_cm)
												{
													$perm_cockpit = false;
													$perm_sidebar = false;
													$perm_cockpit = perfilHasPermission($_GET["perfil"], "cockpit", $item_cm->slug);
													$perm_sidebar = perfilHasPermission($_GET["perfil"], "sidebar", $item_cm->slug);
												?>
												<tr>
													<td style="width: 20%;"><span class="header no-margin inline" id="<?= $item_cm->slug ?>"><?= $item_cm->slug ?></span></td>
													<td>
														<span class="item <?= (($perm_cockpit)?(""):("off")) ?>"><input type="checkbox" rel="<?= $item_cm->slug ?>" <?= $perm_cockpit ? "CHECKED" : "" ?> value="cockpit" name="permission[<?= $item_cm->slug ?>][Cockpit]"> <span>Cockpit</span></span>
														<span class="item <?= (($perm_sidebar)?(""):("off")) ?>"><input type="checkbox" rel="<?= $item_cm->slug ?>" <?= $perm_sidebar ? "CHECKED" : "" ?> value="sidebar" name="permission[<?= $item_cm->slug ?>][Sidebar]"> <span>Sidebar</span></span>
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

						$perms_raw = (array)($_system['module_permissoes_custom']);
						foreach($perms_raw as $value)
						{
							list($permissao, $ajuda) = explode(" | ", $value);
							$perms[$permissao] = $permissao;
							$perms_ajuda[$permissao] = $ajuda;
						}

						$obj = new dbo('permissao');
						$obj->loadAll('ORDER BY nome');
						if($obj->size())
						{
							do {
								$perms[$obj->nome] = $obj->nome;
								$perms_ajuda[$obj->nome] = $obj->ajuda;
							} while($obj->fetch());
						}
						?>
						<div class="row full">
							<div class="large-12 columns">
								<h3>Permissões custom</h3>
								<p class="color medium font-14">Permissões customizadas podem ser usadas cadastradas no sistema ou definidas por módulos específicos.<br />São usadas para diversos fins na lógica do sistema.</p>
							</div><!-- col -->
						</div><!-- row -->
						<div class="row full">
							<div class="large-12 columns">
								<table>
									<thead>
										<th colspan="2"></th>
									</thead>
									<tbody>
										<?
											if(is_array($perms))
											{
												sort($perms);
												foreach($perms as $key => $value)
												{
													$perm = false;
													$perm = perfilHasPermission($_GET["perfil"], $value);
													?>
													<tr>
														<td style="width: 20%"><span class="header inline no-margin" id="permissao-custom-<?= $value ?>"><?= $value ?></span></td>
														<td>
															<span class="item <?= ((perfilHasPermission($_GET['perfil'], $value))?(''):('off')) ?>"><input type="checkbox" rel="permissao-custom-<?= $value ?>" <?= $perm ? "CHECKED" : "" ?> value="<?= $value ?>" name="permission[<?= $value ?>]"></span>
															<span class="color medium font-12"><?= $perms_ajuda[$value] ?></span>
														</td>
													</tr>
													<?
												}
											}
										?>
									</tbody>
								</table>
							</div>
						</div><!-- row -->
				</div>
				<div class="row full">
					<div class="large-12 columns">
						<input type="submit" class="button radius" value="Atualizar Permissões">
					</div>
				</div><!-- row -->
				<input type="hidden" name="flag_update" value="<?= $_GET['perfil'] ?>">
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

	$(document).on('change', '#copiar', function(){
		var mudado = $(this);
		if(mudado.val() > 0){
			peixeGet('dbo_permissions.php?perfil='+mudado.val(), function(d) {
				var html = $.parseHTML(d);
				/* item 1 */
				handler = '#wrapper-all-permissions';
				content = $(html).find(handler).html();
				if(typeof content != 'undefined'){
					$(handler).fadeHtml(content);
				}
			})
			return false;
		}
	});

</script>

<? require('footer.php') ?>