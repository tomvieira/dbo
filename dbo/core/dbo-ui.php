<?

class dboUI
{
	//---------------------------------------------------------------------------------------------
	// TEXT ---------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------
	static function field_text($operation, $obj = false, $params = array())
	{
		extract($params);
		$value = (($value)?($value):(((is_object($obj))?($obj->{$coluna}):(null))));
		$name = $name ? $name : $coluna;

		ob_start();
		// INSERT ---------------------------------------------------------------------------------
		if($operation == 'insert')
		{
			if($interaction == 'readonly' || $interaction == 'updateonly')
			{
				?>
				<input type="text" class="readonly" readonly placeholder="- indisponível na inserção -">
				<?
			}
			else
			{
				?>
				<input type="text" name="<?= $name ?>" id="" value="" class="<?= (($valida)?('required'):('')) ?> <?= $classes ?>" <?= (($valida)?('required'):('')) ?>/>
				<?
			}
		}
		// UPDATE ---------------------------------------------------------------------------------
		elseif($operation == 'update')
		{
			if($interaction == 'insertonly' || $interaction == 'readonly')
			{
				?>
				<div class="form-height-fix"><strong><?= (($edit_function)?($edit_function(htmlSpecialChars($value))):(htmlSpecialChars($value))) ?></strong></div>
				<?
			}
			else
			{
				?>
				<input type="text" name="<?= $name ?>" id="" value="<?= (($edit_function)?($edit_function(htmlSpecialChars($value))):(htmlSpecialChars($value))) ?>" class="<?= (($valida)?('required'):(''))." ".$classes."' data-name='".$titulo ?>" <?= (($valida)?('required'):('')) ?>/>
				<?
			}
		}
		return ob_get_clean();
	}

	//---------------------------------------------------------------------------------------------
	// PASSWORD -----------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------
	static function field_password($operation, $obj = false, $params = array())
	{
		extract($params);
		$value = (($value)?($value):(((is_object($obj))?($obj->{$coluna}):(null))));
		$name = $name ? $name : $coluna;

		ob_start();
		?>
		<input type="password" name="<?= $name ?>" id="" data-name="<?= $titulo ?>" class="<?= (($valida)?('required'):(''))." ".$classes ?>" <?= (($valida)?('required'):('')) ?> value="<?= (($edit_function)?($edit_function(htmlSpecialChars($value))):(htmlSpecialChars($value))) ?>"/>
		<?
		return ob_get_clean();
	}

	//---------------------------------------------------------------------------------------------
	// TEXTAREA -----------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------
	static function field_textarea($operation, $obj = false, $params = array())
	{
		extract($params);
		$value = (($value)?($value):(((is_object($obj))?($obj->{$coluna}):(null))));
		$name = $name ? $name : $coluna;

		ob_start();
		?>
		<textarea rows="<?= (($rows)?($rows):('5')) ?>" name="<?= $name ?>" data-name="<?= $titulo ?>" class="<?= (($valida)?('required'):(''))." ".$classes ?>" <?= (($valida)?('required'):('')) ?>><?= (($edit_function)?($edit_function(htmlSpecialChars($value))):(htmlSpecialChars($value))) ?></textarea>
		<?
		return ob_get_clean();
	}

	//---------------------------------------------------------------------------------------------
	// TEXTAREA-RICH ------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------
	static function field_textarea_rich($operation, $obj = false, $params = array())
	{
		extract($params);
		$value = (($value)?($value):(((is_object($obj))?($obj->{$coluna}):(null))));
		$name = $name ? $name : $coluna;

		ob_start();
		?>
		<textarea rows="<?= (($rows)?($rows):('5')) ?>" name="<?= $name ?>" class="<?= (($valida)?('required'):(''))." ".(($classes)?($classes):('tinymce')) ?>" id="<?= $operation.'-'.$name ?>" data-name="<?= $titulo ?>"><?= (($edit_function)?($edit_function($value)):($value)) ?></textarea>
		<?
		return ob_get_clean();
	}

	//---------------------------------------------------------------------------------------------
	// RADIO --------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------
	static function field_radio($operation, $obj = false, $params = array())
	{
		extract($params);
		$value = (($value)?($value):(((is_object($obj))?($obj->{$coluna}):(null))));
		$name = $name ? $name : $coluna;

		ob_start();
		?>
		<span class="form-height-fix list-radio-checkbox" style="display: block">
			<?
				foreach($valores as $chave2 => $valor2)
				{
					?>
					<span style="white-space: nowrap;">
						<input type="radio" name="<?= $name ?>" id="radio-<?= $name."-".makeSlug($chave2) ?>" value="<?= $chave2 ?>" data-name="<?= $titulo ?>" class="<?= (($valida)?('required'):(''))." ".$classes ?>" <?= (($valida)?('required'):('')) ?> <?= (($value == $chave2)?('checked'):('')) ?>/><label for="radio-<?= $name."-".makeSlug($chave2) ?>"><?= $valor2 ?></label>
					</span>
					<?
				}
			?>
		</span>
		<?
		return ob_get_clean();
	}

	//---------------------------------------------------------------------------------------------
	// CHECKBOX -----------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------
	static function field_checkbox($operation, $obj = false, $params = array())
	{
		//na presente data ainda não existe uma forma padrão de validação de grupos de checkbox com o html5 "required".
		//por isso vamos deixar isso sem validação por enquanto.
		extract($params);
		$value = (($value)?($value):(((is_object($obj))?($obj->{$coluna}):(null))));
		$name = $name ? $name : $coluna;

		ob_start();
		$database_checkbox_values = explode("\n", $value);
		?>
		<span class="form-height-fix list-radio-checkbox" style="display: block">
			<?
				foreach($valores as $chave2 => $valor2)
				{
					?>
					<span style="display: block; white-space: nowrap;" data-name="<?= $titulo ?>">
						<input type="checkbox" name="<?= $name ?>[]" id="checkbox-<?= $name."-".makeSlug($chave2) ?>" value="<?= $chave2 ?>" class="<?= (($valida)?('required'):(''))." ".$classes ?>" <?= ((in_array($chave2, $database_checkbox_values))?('checked'):('')) ?>/><label for="checkbox-<?= $name."-".makeSlug($chave2) ?>"><?= $valor2 ?></label>
					</span>
					<?
				}
			?>
		</span>		
		<?
		return ob_get_clean();
	}

	//---------------------------------------------------------------------------------------------
	// PRICE --------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------
	static function field_price($operation, $obj = false, $params = array())
	{
		extract($params);
		$value = (($value)?($value):(((is_object($obj))?($obj->{$coluna}):(null))));
		$name = $name ? $name : $coluna;

		//especifico do campo
		$value = (($value != null)?(number_format($value, 2, '', '.')):(null));

		ob_start();
		?>
		<div class="row collapse">
			<div class="small-10 columns">
				<input type="text" name="<?= $name ?>" id="" data-name="<?= $titulo ?>" value="<?= (($edit_function)?($edit_function(htmlSpecialChars($value))):(htmlSpecialChars($value))) ?>" class="<?= (($valida)?('required'):(''))." price price-".$formato." text-right ".$classes ?>" <?= (($valida)?('required'):('')) ?>/>
			</div>
			<div class="small-2 columns"><span class="postfix radius pointer trigger-clear-price" title="Limpar o valor do preço"><i class="fa-times"></i></span></div>
		</div>
		<?

		//javascript
		ob_start();
		?>
		$('.price.price-real').priceFormat({
			prefix: 'R$ ',
			centsSeparator: ',',
			thousandsSeparator: '.'
		});
		
		$('.price.price-generico').priceFormat({
			prefix: '$ ',
			centsSeparator: ',',
			thousandsSeparator: '.'
		});
		
		$('.price.price-dolar').priceFormat({
			prefix: 'US$ ',
			centsSeparator: '.',
			thousandsSeparator: ','
		});

		/* limpando o valor do preco */
		$(document).on('click', '.trigger-clear-price', function(){
			clicado = $(this);
			clicado.closest('.item').find('.price').val('');
		});
		<?
		dboRegisterDocReady(singleLine(ob_get_clean()), true, 'field_price');

		return ob_get_clean();
	}

} //class declaration

?>