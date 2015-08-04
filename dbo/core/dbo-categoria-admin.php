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
	extract($params);
	ob_start();
	echo '<ul class="no-bullet '.($children ? 'children' : '').'">';
	foreach($array as $data)
	{
		?>
		<li class="font-12"><input type="checkbox" <?= in_array($data['id'], $checked) ? 'checked' : '' ?> name="categoria[]" id="categoria-<?= $data['id'] ?>" value="<?= $data['id'] ?>" class="top-2"/><label for="categoria-<?= $data['id'] ?>"><?= $data['nome'] ?></label>
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

?> 