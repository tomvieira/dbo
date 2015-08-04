<?php

function renderCategoriaPaginaFormWidget($pag = null, $pagina_tipo, $params = array())
{
	//monta a árvore de categorias
	$tree = categoria::getCategoryStructure($pagina_tipo);

	//tenta localizar as categorias da página atual
	$checked = $pag->id ? $pag->getCategoryIds() : array();

	ob_start();
	?>
	<div class="panel font-13 radius" id="wrapper-categorias">
		<div class="row">
			<div class="large-12 columns">
				<strong>Categorias</strong>
				<hr class="small">
				<div id="wrapper-categorias-da-pagina">
					<?php
						if(!sizeof($tree))
						{
							?><p class="text-center">- sem categorias -</p><?php
						}
						else
						{
							echo renderCategoryCheckboxes($tree, $checked);
						}
					?>
				</div>
				<p class="text-right no-margin">
					&nbsp;<a href="#" class="trigger-pub-option"><i class="fa fa-plus-circle font-14"></i> <span class="underline">Cadastrar nova</span></a>
				</p>
				<div class="row wrapper-pub-option" id="form-nova-categoria" style="display: none;">
					<div class="large-12 columns">
						<label for="">Categoria mãe</label>
						<select name="categoria_mae" id="categoria_mae">
							<option value="">- nenhuma -</option>
							<?php
								if(sizeof($tree))
								{
									echo renderCategoryOptions($tree, '');
								}
							?>
						</select>
					</div>
					<div class="large-12 columns">
						<label for="">Nome</label>
						<input type="text" name="categoria_nome" id="categoria_nome" value="" class="margin-bottom"/>
					</div>
					<div class="large-6 columns">
						<span class="form-height-fix"><a href="#" class="trigger-cancel-pub-option underline cancel-pub-categorias">cancelar</a></span>
					</div>
					<div class="large-6 columns text-right">
						<span class="button radius no-margin trigger-quick-cadastrar-nova">Cadastrar</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>

		function checkCategoriesUpHill(c) {
			wrapper = c.closest('ul');
			if(wrapper.hasClass('children')){
				wrapper.closest('li').find('input[type="checkbox"]').first().prop('checked', true).trigger('change');
			}
		}

		function uncheckCategoriesDownHill(c) {
			c.closest('li').find('input[type="checkbox"]').prop('checked', false);
		}

		$(document).ready(function(){

			$(document).on('change', '#wrapper-categorias-da-pagina input[type="checkbox"]', function(){
				c = $(this);
				if(c.is(':checked')){
					checkCategoriesUpHill(c);
				}
				else {
					uncheckCategoriesDownHill(c);
				}
			});

			$(document).on('click', '.trigger-quick-cadastrar-nova', function(){
				peixeJSON('dbo/core/dbo-categoria-ajax.php?action=quick-cadastrar-nova&pagina_tipo=<?= $pagina_tipo ?>', {
					nome: $('#categoria_nome').val(),
					mae: $('#categoria_mae').val(),
					DBO_CSRF_token: '<?= CSRFGetToken() ?>',
					checados: $('#wrapper-categorias-da-pagina input[type="checkbox"]:checked').map(function(){ return $(this).val(); }).get()
				}, null, true);
				return false;
			});
		}) //doc.ready
	</script>
	<?php
	return ob_get_clean();
}

function renderCategoryOptions($array, $prefix = '')
{
	ob_start();
	foreach($array as $data)
	{
		?>
		<option value="<?= $data['id'] ?>"><?= $prefix.$data['nome'] ?></option>
		<?php
		if(is_array($data['children']))
		{
			echo renderCategoryOptions($data['children'], trim($prefix).'&#8212; ');
		}
	}
	return ob_get_clean();
}

function renderCategoryCheckboxes($array, $checked = array(), $params = array())
{
	/*
	* @params
	*  menu_structure: se setado como true, faz uma listagem de checkboxes para o menu maker
	*  full_tree: a arvore completa para pegar a slug por recursão
	*/
	extract($params);
	ob_start();
	echo '<ul class="no-bullet '.($children ? 'children' : '').'">';
	foreach($array as $data)
	{
		?>
		<li class="font-12"><input type="checkbox" <?= in_array($data['id'], $checked) ? 'checked' : '' ?> name="<?= $menu_structure ? 'item-' : '' ?>categoria[]" id="categoria-<?= $data['id'] ?>" value="<?= $data['id'] ?>" class="top-2" <?= $menu_structure ? getCategoryMenuDataAttrs($full_tree, $data, $params) : '' ?>/><label for="categoria-<?= $data['id'] ?>"><?= $data['nome'] ?></label>
		<?php
		if(is_array($data['children']))
		{
			$params['children'] = true;
			echo renderCategoryCheckboxes($data['children'], $checked, $params);
		}
		?>
		</li>
		<?php
	}
	echo '</ul>';
	return ob_get_clean();
}

function getCategoryMenuDataAttrs($tree, $current, $params)
{
	global $_system;
	extract($params);
	return ' data-titulo="'.$current['nome'].'" data-slug="'.$current['full_slug'].'" data-categoria_id="'.$current['id'].'" data-tipo="categoria" ';
}

/*function getCategoriaFullSlug($tree, $current, $prefix)
{
	return $prefix.'/categorias/'.getFullSlug($tree, $current['slug']);
}

function getFullSlug($array, $key) {
	foreach($array as $node) {
		if($key == $node['slug']) { //Found it on this node
			return $node['slug'];
		}
		else //Search depth first
		{
			if (isset($node['children']) && is_array($node['children']) && !empty($node['children']) ) {
				$subSlug = getFullSlug($node['children'], $key);
				if(!empty($subSlug)) //If it was found
				{
					return $node['slug'].'/'.$subSlug;
				}
				else //Look at the next sibling
				{
					continue;
				}
			} else { // This is a leaf, and it wasn't found
				continue;
			}
		}
	}
	return ''; //Failed to find it.
}*/

function renderCategoriaMenuAdminStructure()
{
	global $_system;
	ob_start();
	?>
	<li class="accordion-navigation">
		<a href="#acc-categorias">Categorias</a>
		<div id="acc-categorias" class="content">
			<div class="row">
				<div class="large-12 columns">
					<?php
						if(sizeof($_system['pagina_tipo']))
						{
							?>
							<dl class="sub-nav" id="seletor-categorias">
								<?php
									foreach($_system['pagina_tipo'] as $tipo => $data)
									{
										if($tipo == 'pagina') continue;
										?>
										<dd class="<?= !$active ? 'active' : '' ?>"><a href="#" data-target="#wrapper-categoria-<?= $tipo ?>"><?= ucfirst($data['titulo_plural']) ?></a></dd>
										<?php
										if(!$active) $active = true;
									}
								?>
							</dl>
							<?php
							$active = false;
							foreach($_system['pagina_tipo'] as $tipo => $data)
							{
								if($tipo == 'pagina') continue;
								$tree = categoria::getCategoryStructure($tipo);
								?>
								<div id="wrapper-categoria-<?= $tipo ?>" class="wrapper-categoria-list" style="<?= $active ? 'display: none;' : '' ?>">
									<?= renderCategoryCheckboxes($tree, array(), array(
										'menu_structure' => true,
										'tipo' => $tipo,
										'full_tree' => $tree,
									)); ?>
								</div>
								<?php
								if(!$active) $active = true;
							}
						}
					?>
				</div>
			</div>
			<hr class="small">
			<div class="row">
				<div class="large-12 columns text-right"><span class="button radius small no-margin trigger-adicionar-categorias secondary">Adicionar ao menu <i class="fa-arrow-right fa"></i></span></div>
			</div>
		</div>
	</li>
	<script>
		$(document).ready(function(){
			$(document).on('click', '#seletor-categorias dd:not(.active) a', function(e){
				e.preventDefault();
				c = $(this);
				c.closest('dl').find('dd.active').removeClass('active');
				c.closest('dd').addClass('active');
				$('.wrapper-categoria-list:visible').fadeOut('fast', function(){
					$(this).find('input[type="checkbox"]').prop('checked', false);
					$(c.data('target')).fadeIn('fast');
				})
			});

			$(document).on('click', '.trigger-adicionar-categorias', function(){
				if($('#nav-menus-disponiveis dd.active').length){
					pags = $('input[name^="item-categoria"]:checked');
					if(pags.length){
						pags.each(function(){
							adicionarDDItem($(this).data());
						})
					}
					else {
						alert('Selecione um ou mais itens da lista para adicionar ao menu ativo');
					}
				}
				else {
					alert('Erro: Não há nenhum menu cadastrado');
				}
			});
		
		}) //doc.ready	
	</script>
	<style>
		.wrapper-categoria-list ul li ul { margin-left: 20px; }
		.wrapper-categoria-list input[type="checkbox"] { margin-bottom: 8px; }
	</style>
	<?php
	return ob_get_clean();
}

?> 