<?

class dboUI
{
	static function field($field_type, $operation, $obj = false, $params = array())
	{
		global $dbo;
		//normalização das variaveis
		$params['name'] = $params['name'] ? $params['name'] : $params['coluna'];
		extract($params);
		$value = (($value)?($value):(((is_object($obj))?($obj->{$coluna}):(null))));
		$required = isset($required) ? $required : $valida;

		ob_start();

		//---------------------------------------------------------------------------------------------
		// TEXT ---------------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		if($field_type == 'text')
		{
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
					<input type="text" name="<?= $name ?>" id="" value="" class="<?= (($required)?('required'):('')) ?> <?= $classes ?>" <?= (($required)?('required'):('')) ?>/>
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
					<input type="text" name="<?= $name ?>" id="" value="<?= (($edit_function)?($edit_function(htmlSpecialChars($value))):(htmlSpecialChars($value))) ?>" class="<?= (($required)?('required'):('')).' '.$classes ?>" data-name="<?= $titulo ?>" <?= (($required)?('required'):('')) ?>/>
					<?
				}
			}
		}
		//---------------------------------------------------------------------------------------------
		// PASSWORD -----------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		elseif($field_type == 'password')
		{
			?>
			<input type="password" name="<?= $name ?>" id="" data-name="<?= $titulo ?>" class="<?= (($required)?('required'):(''))." ".$classes ?>" <?= (($required)?('required'):('')) ?> value="<?= (($edit_function)?($edit_function(htmlSpecialChars($value))):(htmlSpecialChars($value))) ?>"/>
			<?
		}
		//---------------------------------------------------------------------------------------------
		// TEXTAREA -----------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		elseif($field_type == 'textarea')
		{
			?>
			<textarea rows="<?= (($rows)?($rows):('5')) ?>" name="<?= $name ?>" data-name="<?= $titulo ?>" class="<?= (($required)?('required'):(''))." ".$classes ?>" <?= (($required)?('required'):('')) ?> <?= $placeholder ? 'placeholder="'.$placeholder.'"' : '' ?> <?= $styles ? 'style="'.$styles.'"' : '' ?>><?= (($edit_function)?($edit_function(htmlSpecialChars($value))):(htmlSpecialChars($value))) ?></textarea>
			<?
		}
		//---------------------------------------------------------------------------------------------
		// TEXTAREA-RICH ------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		elseif($field_type == 'textarea-rich')
		{
			?>
			<textarea rows="<?= (($rows)?($rows):('5')) ?>" name="<?= $name ?>" class="<?= (($required)?('required'):(''))." ".(($classes)?($classes):('tinymce')) ?>" id="<?= $input_id ? $input_id : $operation.'-'.$name ?>" data-name="<?= $titulo ?>" style="<?= $styles ? $styles : 'opacity: 0; margin-bottom: 179px;' ?>"><?= (($edit_function)?($edit_function(htmlSpecialChars($value))):(htmlSpecialChars($value))) ?></textarea>
			<?
			if($init_js !== false)
			{
				dboUi::fieldJS('textarea-rich');
			}
		}
		//---------------------------------------------------------------------------------------------
		// RADIO --------------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		elseif($field_type == 'radio')
		{
			?>
			<span class="form-height-fix list-radio-checkbox" style="display: block">
				<?
					foreach($valores as $chave2 => $valor2)
					{
						?>
						<span style="white-space: nowrap;">
							<input type="radio" name="<?= $name ?>" id="radio-<?= $name."-".makeSlug($chave2) ?>" value="<?= $chave2 ?>" data-name="<?= $titulo ?>" class="<?= (($required)?('required'):(''))." ".$classes ?>" <?= (($required)?('required'):('')) ?> <?= (($value == $chave2)?('checked'):('')) ?>/><label for="radio-<?= $name."-".makeSlug($chave2) ?>"><?= $valor2 ?></label>
						</span>
						<?
					}
				?>
			</span>
			<input type="hidden" name="__dbo_ui_flag[empty][<?= $name ?>]" value="<?= $name ?>"/>
			<?
		}
		//---------------------------------------------------------------------------------------------
		// CHECKBOX -----------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		elseif($field_type == 'checkbox')
		{
			//na presente data ainda não existe uma forma padrão de validação de grupos de checkbox com o html5 "required".
			//por isso vamos deixar isso sem validação por enquanto.
			$database_checkbox_values = explode("\n", $value);
			?>
			<span class="form-height-fix list-radio-checkbox" style="display: block">
				<?
					foreach($valores as $chave2 => $valor2)
					{
						?>
						<span style="display: block; white-space: nowrap;" data-name="<?= $titulo ?>">
							<input type="checkbox" name="<?= $name ?>[]" id="checkbox-<?= $name."-".makeSlug($chave2) ?>" value="<?= $chave2 ?>" class="<?= (($required)?('required'):(''))." ".$classes ?>" <?= ((in_array($chave2, $database_checkbox_values))?('checked'):('')) ?>/><label for="checkbox-<?= $name."-".makeSlug($chave2) ?>"><?= $valor2 ?></label>
						</span>
						<?
					}
				?>
			</span>		
			<input type="hidden" name="__dbo_ui_flag[empty][<?= $name ?>]" value="<?= $name ?>"/>
			<?
		}
		//---------------------------------------------------------------------------------------------
		// PRICE --------------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		elseif($field_type == 'price')
		{
			//especifico do campo
			$value = (($value != null && $value != '')?(number_format($value, 2, '', '.')):(null));
			?>
			<div class="row collapse">
				<div class="small-10 columns">
					<input type="text" name="<?= $name ?>" id="" data-name="<?= $titulo ?>" value="<?= (($edit_function)?($edit_function(htmlSpecialChars($value))):(htmlSpecialChars($value))) ?>" class="<?= (($required)?('required'):(''))." price price-".$formato." text-right ".$classes ?>" <?= (($required)?('required'):('')) ?>/>
				</div>
				<div class="small-2 columns"><span class="postfix radius pointer trigger-clear-closest-input" title="Limpar o valor do preço"><i class="fa-times"></i></span></div>
			</div>
			<?
			dboUI::jsSnippet('trigger-clear-closest-input');
			dboUI::fieldJS('price');
		}
		//---------------------------------------------------------------------------------------------
		// SELECT -------------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		elseif($field_type == 'select')
		{
			?>
			<select name="<?= $name ?>" id="" class="<?= (($required)?('required'):(''))." ".$classes ?>" data-name="<?= $titulo ?>" <?= (($required)?('required'):('')) ?>>
				<option value="">...</option>
				<?
					foreach($valores as $chave2 => $valor2)
					{
						?>
						<option value="<?= $chave2 ?>" <?= (($value == $chave2)?('selected'):('')) ?>><?= (($edit_function)?($edit_function($valor2)):($valor2)) ?></option>
						<?
					}
				?>
			</select>
			<?
		}
		//---------------------------------------------------------------------------------------------
		// DATE ---------------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		elseif($field_type == 'date')
		{
			if($value)
			{
				list($ano,$mes,$dia) = explode("-", $value);
				if($dia == '00') { $val = ''; }
				else { $val = $dia."/".$mes."/".$ano; }
			}
			?>
			<input type="text" name="<?= $name ?>" id="" value="<?= $val ?>" class="<?= (($required)?('required'):(''))." ".(($classes)?($classes):('datepick')) ?>" data-name="<?= $titulo ?>" <?= (($required)?('required'):('')) ?>/>
			<?
			dboUI::fieldJS('date');
		}
		//---------------------------------------------------------------------------------------------
		// DATETIME -----------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		elseif($field_type == 'datetime')
		{
			$datetime_valor = '';
			if(strlen(trim($value)))
			{
				$datetime_valor = dateTimeNormal($value);
			}
			?>
			<div class="row collapse">
				<div class="small-10 columns">
					<input type='text' <?= (($required)?('required'):('')) ?> name="<?= $name ?>" class='<?= (($required)?('required'):('')) ?> <?= (($classes)?($classes):('datetimepick')) ?>' data-name="<?= $titulo ?>" value="<?= $datetime_valor ?>" placeholder="<?= $placeholder ?>" id="<?= $input_id ? $input_id : $operation.'-'.$name ?>"/>
				</div>
				<div class="small-2 columns"><span class="postfix radius trigger-clear-closest-input pointer" title="Limpar data e hora"><i class="fa-times"></i></span></div>
			</div>
			<?
			dboUI::jsSnippet('trigger-clear-closest-input');
			dboUI::fieldJS('datetime');
		}
		//---------------------------------------------------------------------------------------------
		// PLUGIN -------------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		elseif($field_type == 'plugin')
		{
			$plugin = (array)$plugin;
			$plugin_path = DBO_PATH."/plugins/".$plugin['name']."/".$plugin['name'].".php";
			$plugin_class = "dbo_".$plugin['name'];

			//checa se o plugin existe, antes de mais nada.
			if(file_exists($plugin_path))
			{
				include_once($plugin_path); //inclui a classe
				$plug = new $plugin_class($plugin['params']); //instancia com os parametros
				if($operation == 'update')
				{
					$plug->setData($value);
				}
				$method_name = 'get'.ucfirst($operation).'Form';
				?>
				<div class="wrapper-plugin"><?= $plug->$method_name($name) ?></div>
				<?
			}
			else { //senão, avisa que não está instalado.
				?>
				O Plugin <strong><?= $plugin['name'] ?> não está instalado</strong>.
				<?
			}
		}
		//---------------------------------------------------------------------------------------------
		// SINGLE JOIN --------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		elseif($field_type == 'join')
		{
			$join = (array)$join;
			$mod_aux = $join['modulo'];
			$obj = new $mod_aux();

			//verifica se o campo é fixo para sumir com ele.
			if(in_array($name, array_keys($fixos)))
			{
				?>
				<input type="hidden" name="<?= $name ?>" id="" value="<?= $fixos[$name] ?>"/><span class="dbo_fixo"><?= $obj->{$join['valor']} ?></span>
				<?
				$foo .= '$("input[name='.$name.']").closest(".item").hide();';
				dboRegisterDocReady($foo);
			}
			else
			{
				if($join['ajax'])
				{

					$metodo_retorno = (($join['metodo_retorno'])?($join['metodo_retorno']):(false));

					//pegando o item "selected"
					$join_key_2 = '';
					$join_key_2 = (($join['chave2_pk'])?($join['chave2_pk']):('id'));
					$obj->$join_key_2 = $value;
					$obj->loadAll();

					$join_retorno = $join_label ? $join_label : (($metodo_retorno)?($obj->$metodo_retorno()):($obj->{$join['valor']}));

					$mod_selected = $join['modulo'];
					$mod_selected = new $mod_selected();
				
					//handler para o ID do campo
					$id_handler = uniqid();

					//variaveis necessárias para o javascript
					$url_dbo_ui_joins_ajax = DBO_URL."/core/dbo-ui-joins-ajax.php";
					$tamanho_minimo = (($join['tamanho_minimo'])?($join['tamanho_minimo']):(3));
					
					?>
						<input type="text" name="<?= $name ?>_select2_aux" value="<?= htmlSpecialChars($join_retorno) ?>" data-name="<?= $titulo ?>" data-target="#<?= $id_handler ?>" class="<?= (($required)?('required'):('')) ?> <?= $classes ?>"/>
						<input type="hidden" name="<?= $name ?>" id="<?= $id_handler ?>" value="<?= $value ?>" class="<?= $classes ?>"/>
					<?
					$params['name'] = $name;
					$params['modulo'] = $modulo;
					$params['tamanho_minimo'] = $tamanho_minimo;
					$params['url_dbo_ui_joins_ajax'] = $url_dbo_ui_joins_ajax;
					dboUI::fieldJS('join', $params);
				}
				else
				{
					//setando restricoes...
					$rest = '';
					if($restricao) { eval($restricao.";"); }

					//seta deleted_by = 0, se for o caso
					$rest .= (($obj->hasDeletionEngine())?(((strlen($rest))?(" AND "):(" WHERE "))." deleted_by = 0 "):(''));

					//seta inativo = 0 caso o modulo externo se enquadre, e depois o order by
					$rest .= (($obj->hasInativo())?(((strlen($rest))?(" AND "):(" WHERE "))." inativo = 0 "):(''))." ORDER BY ".(($join['order_by'])?($join['order_by']):($join['valor']))." ";

					//caso o modulo externo tenha inativos, precisamos certificar que o valor previamente existente não é um inativo.
					//deverá ser adicionado à listagem em caso positivo.
					$inativo_atual = false;
					if($obj->hasInativo())
					{
						$obj_inativo = new Dbo($join['modulo']);
						$obj_inativo->{$join['chave']} = $value;
						$obj_inativo->load();
						if($obj_inativo->inativo > 0)
						{
							$inativo_atual[chave] = $value;
							$inativo_atual[valor] = $obj_inativo->{$join['valor']};
						}
					}

					//depois de descobrirmos se era inativo, fazemos um loadAll.
					$obj->loadAll($rest);

					//checando para ver se há metodo de retorno
					$metodo_retorno = (($join['metodo_retorno'])?($join['metodo_retorno']):(false));
					if($join['tipo'] == 'select') //se o join for do tipo select
					{
						?>
						<select name="<?= $name ?>" id="" class="<?= (($required)?(' required '):('')).(($join['select2'])?('select2'):(''))." ".$classes ?>" data-name="<?= $titulo ?>" <?= (($join['tamanho_minimo'])?(' data-tamanho_minimo="'.$join['tamanho_minimo'].'" '):('')) ?> <?= (($required)?(' required '):('')) ?>>
							<option value="">...</option>
								<?
									if($obj->size())
									{
										//se o atual for inativo, colocar no começo, e em destaque...
										if($inativo_atual)
										{
											?>
											<option value="<?= $inativo_atual[chave] ?>" SELECTED class="flag-inativo"><?= $inativo_atual[valor] ?> (inativo)</option>
											<?
										}
										do {
											$join_retorno = '';
											$join_retorno = (($metodo_retorno)?($obj->$metodo_retorno()):($obj->{$join['valor']}));
											?>
											<option value="<?= $obj->{$join['chave']} ?>" <?= (($obj->{$join['chave']} == $value)?(" SELECTED "):('')) ?>><?= (($edit_function)?($edit_function($join_retorno)):($join_retorno)) ?></option>
											<?
										}while($obj->fetch());
									}
								?>
						</select>
						<?
					}
					elseif($join['tipo'] == 'radio') //se o join for do tipo select
					{
						?>
						<span class="form-height-fix list-radio-checkbox" style="display: block;">
							<?
								do {
									$join_retorno = '';
									$join_retorno = (($metodo_retorno)?($obj->$metodo_retorno()):($obj->{$join['valor']}));
									if($inativo_atual)
									{
										?>
										<span style="white-space: nowrap;"><input checked type="radio" name="<?= $name ?>" id="radio-<?= $name."-".makeSlug((($edit_function)?($edit_function($inativo_atual[valor])):($inativo_atual[valor]))) ?>" value="<?= $inativo_atual[chave] ?>"/><label for="radio-<?= $name."-".makeSlug((($edit_function)?($edit_function($inativo_atual[valor])):($inativo_atual[valor]))) ?>" class="flag-inativo"><?= (($edit_function)?($edit_function($inativo_atual[valor])):($inativo_atual[valor])) ?> (inativo)</label></span>
										<?
									}
									?>
									<span style="white-space: nowrap;"><input type="radio" name="<?= $name ?>" id="radio-<?= $name."-".makeSlug((($edit_function)?($edit_function($join_retorno)):($join_retorno))) ?>" <?= (($obj->{$join['chave']} == $value)?(" CHECKED "):('')) ?> value="<?= $obj->{$join['chave']} ?>" class="<?= (($required)?('required'):(''))." ".$classes ?>" data-name="<?= $titulo ?>"/><label for="radio-<?= $name."-".makeSlug((($edit_function)?($edit_function($join_retorno)):($join_retorno))) ?>"><?= (($edit_function)?($edit_function($join_retorno)):($join_retorno)) ?></label></span>
									<?
								}while($obj->fetch());
							?>
						</span>
						<?
					}
				}
			}
		}
		//---------------------------------------------------------------------------------------------
		// MULTI JOIN --------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		elseif($field_type == 'joinNN')
		{
			$join = (array)$join;
			$mod_aux = $join['modulo'];
			$obj_join = new $mod_aux();
			
			$cadastrados_array = array();

			//setando restricoes...
			$rest = '';
			if($restricao) { eval($restricao.";"); }

			//seta deleted_by = 0, se for o caso
			$rest .= (($obj_join->hasDeletionEngine())?(((strlen($rest))?(" AND "):(" WHERE "))." deleted_by = 0 "):(''));

			//seta inativo = 0 caso o modulo externo se enquadre, e depois o order by
			$rest .= (($obj_join->hasInativo())?(((strlen($rest))?(" AND "):(" WHERE "))." inativo = 0 "):(''))." ORDER BY ".(($join['order_by'])?($join['order_by']):($join['valor']))." ";

			$obj_join->loadAll($rest);

			$cadastrados = new Dbo($join['tabela_ligacao']);
			$cadastrados->{$join['chave1']} = (($join['chave1_pk'])?($obj->{$join['chave1_pk']}):($obj->id));
			$cadastrados->loadAll();
			do {
				$cadastrados_array[] = $cadastrados->{$join['chave2']};
			}while($cadastrados->fetch());

			//caso o modulo externo tenha inativos, precisamos certificar que os valores previamente existentes não são inativos.
			//deverá ser adicionado à listagem em caso positivo.
			$inativo_atual = false;
			if($obj_join->hasInativo())
			{
				foreach($cadastrados_array as $key => $value)
				{
					$obj_inativo = new Dbo($join['modulo']);
					$obj_inativo->{$join['chave']} = $value;
					$obj_inativo->load();
					if($obj_inativo->inativo > 0)
					{
						$inativo_atual[$obj->{$name}][chave] = $value;
						$inativo_atual[$obj->{$name}][valor] = $obj_inativo->{$join['valor']};
					}
				}
			}

			$metodo_retorno = (($join['metodo_retorno'])?($join['metodo_retorno']):(false));
			if($join['tipo'] == 'select') //se o join for do tipo select
			{
				?>
				<select name="<?= $name ?>[]" multiple <?= (($join['tamanho_minimo'])?(' data-tamanho_minimo="'.$join['tamanho_minimo'].'" '):('')) ?> class="<?= (($join['select2'])?('select2'):('multiselect'))." ".(($valida)?('required'):(''))." ".$classes ?>" size="5" data-name="<?= $titulo ?>">
					<?
						if($inativo_atual)
						{
							foreach($inativo_atual as $inativo_value)
							{
								?>
								<option value="<?= $inativo_value['chave'] ?>" selected class="flag-inativo"><?= $inativo_value[valor] ?> (inativo)</option>
								<?
							}
						}
						do {
							$join_retorno = '';
							$join_retorno = (($metodo_retorno)?($obj_join->$metodo_retorno()):($obj_join->{$join['valor']}));
							$join_key_2 = '';
							$join_key_2 = (($join['chave2_pk'])?($obj_join->{$join['chave2_pk']}):($obj_join->{$join['chave']}));
							?>
							<option <?= ((in_array($join_key_2, $cadastrados_array))?('selected'):('')) ?> value="<?= $join_key_2 ?>"><?= $join_retorno ?></option>
							<?
						}while($obj_join->fetch());
					?>
				</select>
				<?
				//select2 ou multiselect?
				dboUI::fieldJS('joinNN', $params);
			}
			elseif($join['tipo'] == 'checkbox') //se o join for do tipo checkbox
			{
				?>
				<span class="form-height-fix list-radio-checkbox">
					<?
						if($inativo_atual)
						{
							foreach($inativo_atual as $key => $inativo_value)
							{
								?>
								<span style="display: block; white-space: nowrap;" data-name="<?= $titulo ?>" class="<?= (($valida)?('required'):(''))." ".$classes ?>"><input type="checkbox" name="<?= $name ?>[]" checked id="checkbox-<?= $name."-".makeSlug($obj_join->{$join['valor']}) ?>" value="<?= $inativo_value['chave'] ?>" class="<?= (($valida)?('required'):(''))." ".$classes ?>" data-name="<?= $titulo ?>"/><label for="checkbox-<?= $name."-".makeSlug($obj_join->{$join['valor']}) ?>" class="flag-inativo"><?= (($edit_function)?($edit_function($inativo_value[valor])):($inativo_value[valor])) ?> (inativo)</label></span>
								<?
							}
						}
						do {
							$join_retorno = '';
							$join_retorno = (($metodo_retorno)?($obj_join->$metodo_retorno()):($obj_join->{$join['valor']}));
							$join_key_2 = '';
							$join_key_2 = (($join['chave2_pk'])?($obj_join->{$join['chave2_pk']}):($obj_join->{$join['chave']}));
							?>
							<span style="display: block; white-space: nowrap;" data-name="<?= $titulo ?>" class="<?= (($valida)?('required'):(''))." ".$classes ?>"><input type="checkbox" name="<?= $name ?>[]" id="checkbox-<?= $name."-".makeSlug($join_retorno) ?>" <?= ((in_array($join_key_2, $cadastrados_array))?('CHECKED'):('')) ?> value="<?= $join_key_2 ?>"/><label for="checkbox-<?= $name."-".makeSlug($join_retorno) ?>"><?= $join_retorno ?></label></span>
							<?
						}while($obj_join->fetch());
					?>
				</span>
				<?
			}
			?>
			<input type="hidden" name="__dbo_ui_flag[empty][<?= $name ?>]" value="<?= $name ?>"/>
			<?
		}
		//---------------------------------------------------------------------------------------------
		// IMAGE -------------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		elseif($field_type == 'image')
		{
			?>
			<div id="wrapper-imagem-<?= $name ?>">
				<?
					$file_exists = file_exists(DBO_PATH."/upload/images/".$value) && strlen($value);
					if($file_exists)
					{
						?>
						<a rel="lightbox[album]" href="<?= DBO_URL."/upload/images/".$value ?>"><img src="<?= DBO_URL."/upload/images/".$value ?>" alt="" class="thumb-lista" style="<?= $full ? 'max-width: 100%;' : '' ?>"></a>
						<?
					}
				?>
			</div>
			<div class="row">
				<div class="<?= (($full)?('small-12'):('large-6 small-9')) ?> columns">
					<?
						echo peixeAjaxFileUploadInput($name, 'input-imagem-'.$name, (($required && !$file_exists)?('required'):('')), $value."\n".$value, array(
							'classes' => (($required)?('required'):(''))." ".$classes,
							'icon' => 'fa fa-fw fa-cloud-upload',
							'text' => 'clique para fazer o upload',
							'data_attributes' => array(
								'titulo' => $titulo,
							)
						));
						dboUI::fieldJS('image', $params);
					?>
				</div>
			</div>
			<?
		}
		//---------------------------------------------------------------------------------------------
		// FILE --------------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		elseif($field_type == 'file')
		{
			$file_exists = strlen(trim($value));
			?>
			<div class="row">
				<div class="large-6 small-9 columns">
					<?
						echo peixeAjaxFileUploadInput($name, 'input-file-'.$name, (($required && !$file_exists)?('required'):('')), $value."\n".$value, array(
							'classes' => (($required)?('required'):(''))." ".$classes,
							'icon' => 'fa fa-fw fa-cloud-upload',
							'text' => 'clique para fazer o upload',
							'data_attributes' => array(
								'titulo' => $titulo,
							)
						));
					?>
				</div>
				<div class="small-3 columns end" id="wrapper-file-<?= $name ?>">
					<?
						if($file_exists)
						{
							echo $dbo->getDownloadLink($value, '<i class="fa fa-cloud-download fa-fw"></i>', array(
								'classes' => 'button radius no-margin secondary',
								'title' => 'Fazer download do arquivo',
								'styles' => 'height: 36.125px; padding-top: 0; padding-bottom: 0; line-height: 36px;'
							));
						}
						dboUI::fieldJS('file', $params);
					?>
				</div>
			</div>
			<?
		}
		//---------------------------------------------------------------------------------------------
		// MEDIA --------------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------
		elseif($field_type == 'media')
		{
			?>
			<div class="row">
				<div class="large-12 columns" id="wrapper-midia-<?= $name ?>">
					<input type="hidden" name="<?= $name ?>" id="" value="<?= $value ?>"/>
					<div class="wrapper-field-media">
						<img src="<?= $value ? DBO_URL.'/upload/dbo-media-manager/thumbs/'.($size ? $size : 'medium').'-'.$value : DBO_IMAGE_PLACEHOLDER ?>" style="max-height: <?= $max_height ? $max_height : '220px' ?>; max-width: <?= $max_width ? $max_width : '550px' ?>; margin-bottom: 7px;" class="th">
					</div>
					<div class="media-controls-insert margin-bottom" style="<?= $value ? 'display: none;' : '' ?>">
						<span class="button small secondary trigger-colorbox-modal radius" data-width="100%" data-height="100%" data-url="dbo-media-manager.php?dbo_modal=1&modulo=<?= $modulo ?>&modulo_id=<?= $pag->id ?>&destiny=field&wrapper_id=wrapper-midia-<?= $name ?>" data-transition="none" data-fadeout="1" style="margin-bottom: 5px;"><i class="fa fa-fw fa-image top-1"></i> Adicionar mídia</span>
					</div>
					<div class="media-controls-update margin-bottom" style="<?= $value ? '' : 'display: none;' ?>">
						<span class="button small secondary trigger-colorbox-modal radius button-media-update" data-width="100%" data-height="100%" data-url="dbo-media-manager.php?dbo_modal=1&modulo=<?= $modulo ?>&modulo_id=<?= $pag->id ?>&destiny=field&wrapper_id=wrapper-midia-<?= $name ?>&file=<?= $value ?>" data-transition="none" data-fadeout="1" style="margin-bottom: 5px;"><i class="fa fa-fw fa-image top-1"></i> Alterar mídia</span> &nbsp; <a href="#" class="underline font-12 trigger-remover-midia">remover mídia</a>
					</div>
				</div>
			</div>
			<?
			dboUI::fieldJS('media', $params);
		}
		return ob_get_clean();
	}
	
	static function jsSnippet($foo)
	{
		if($foo == 'trigger-clear-closest-input')
		{
			ob_start();
			?>
				$(document).on('click', '.trigger-clear-closest-input', function(e){
					e.preventDefault();
					$(this).closest('.item').find('input').val('').trigger('clear');
				});
			<?
			$ob_result = ob_get_clean();
			dboRegisterDocReady(singleLine($ob_result), true, 'trigger-clear-closest-input');
		}
	}

	static function fieldJS($field_type, $params = array())
	{
		extract($params);
		if($field_type == 'textarea-rich')
		{
			ob_start();
			?>
			$("textarea.tinymce").each(function(){
				$(this).tinymce({
					height: (($(this).attr('rows'))?($(this).attr('rows')*19):('300')),
					theme: 'dbo',
					resize: false,
					object_resizing: false,
					autoresize: true,
					autoresize_max_height: 700,
					language: 'pt_BR',
					autofocus: false,
					entity_encoding: 'raw',
					extended_valid_elements: 'div[media-manager-element|class|id],img[media-manager-element|src|alt|class|id]',

					plugins: [
						"advlist autolink lists link image charmap preview hr anchor pagebreak",
						"searchreplace wordcount visualblocks visualchars code fullscreen",
						"media nonbreaking save table contextmenu directionality",
						"emoticons template paste textcolor dbo_media_manager dbo_column_manager autoresize"
					],
					toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | dbo_column_manager | link media dbo_media_manager | code | fullscreen"
				});
			});
			<?
			dboRegisterDboInit(singleLine(ob_get_clean()), true, 'field_textarea_rich');
		}
		elseif($field_type == 'price')
		{
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
			<?
			dboRegisterDboInit(singleLine(ob_get_clean()), true, 'field_price');
		}
		elseif($field_type == 'date')
		{
			ob_start();
			?>
			$('.datepick').each(function(){
				$(this).datepicker();
			});
			<?
			dboRegisterDboInit(singleLine(ob_get_clean()), true, 'field_date');
		}
		elseif($field_type == 'datetime')
		{
			ob_start();
			?>
			$('.datetimepick').each(function(){
				$(this).datetimepicker({
					dateFormat: 'dd/mm/yy',
					timeFormat: 'HH:mm',
					stepMinute: 5,
					onClose: function(d, field){
						$('#'+field.id).trigger('update', { date: d });
					}
				});
			});

			$('.datetimepick').each(function(){
				$(this).mask('99/99/9999 99:99');
			});
			<?
			dboRegisterDboInit(singleLine(ob_get_clean()), true, 'field_datetime');
		}
		elseif($field_type == 'join')
		{
			ob_start();
			?>
			/*pegando responsavels por ajax*/
			$('input[name="<?= $name ?>_select2_aux"]').select2({
				placeholder: "...",
				minimumInputLength: <?= $tamanho_minimo ?>,
				allowClear: <?= $required ? 'false' : 'true' ?>,
				ajax: { /*instead of writing the function to execute the request we use Select2's convenient helper*/
					url: "<?= $url_dbo_ui_joins_ajax ?>",
					dataType: 'json',
					data: function (term, page) {
						return {
							module: '<?= $modulo; ?>',
							field: '<?= $name; ?>',
							term: term
						};
					},
					results: function (data, page) {
						return { results: data };
					}
				},
				formatResult: function (data) {
					return data.valor;
				},
				formatSelection: function (data, container) {
					return data.valor;
				},
				initSelection: function (element, callback) {
					if($('input[name="<?= $name ?>"').val()){
						callback({ valor: element.val() });
					}
					else {
						callback({ valor: '...' });
					}
				}
			});
			$('input[name="<?= $name ?>_select2_aux"]').on('change', function(e){
				target = $($(this).data('target'));
				if(e.val > 0){
					target.val(e.val);
				}
				else {
					target.val('');
				}
			});
			<?
			dboRegisterDboInit(singleLine(ob_get_clean()));
		}
		elseif($field_type == 'joinNN')
		{
			?>
			<script src="">alert('aqui')</script>
			<?
			$join = (array)$join;
			ob_start();
			if($join['select2'])
			{
				?>
				$('select.select2').each(function(){
					$(this).select2({
						minimumInputLength: (($(this).data('tamanho_minimo'))?($(this).data('tamanho_minimo')):(0))
					})
				});
				<?
			}
			else
			{
				?>
				$(".multiselect").each(function(){
					$(this).multiselect({sortable: false, searchable: true});
				})
				<?
			}
			dboRegisterDocReady(singleLine(ob_get_clean()), true, 'field_joinNN');
		}
		elseif($field_type == 'image')
		{
			ob_start();
			?>
			$(document).on('fileRemoved', '#input-imagem-<?= $name ?>', function(){
				$('#wrapper-imagem-<?= $name ?>').slideUp();
			});
			<?
			dboRegisterDocReady(singleLine(ob_get_clean()));
		}
		elseif($field_type == 'file')
		{
			ob_start();
			?>
			$(document).on('fileRemoved', '#input-file-<?= $name ?>', function(){
				$('#wrapper-file-<?= $name ?>').hide();
			});
			<?
			dboRegisterDocReady(singleLine(ob_get_clean()));
		}
		elseif($field_type == 'media')
		{
			ob_start();
			?>
			$(document).on('click', '.trigger-remover-midia', function(e){
				e.preventDefault();
				var ans = confirm("Tem certeza que deseja remover esta mídia?");
				if (ans==true) {
					wrapper = $(this).closest('[id^="wrapper-midia-"]');
					wrapper.find('input[type="hidden"]').val('');
					wrapper.find('img').attr('src', '<?= DBO_IMAGE_PLACEHOLDER ?>');
					wrapper.find('.media-controls-update').fadeOut('fast', function(){
						wrapper.find('.media-controls-insert').fadeIn('fast');
					});
				} 
			});
			<?
			dboRegisterDocReady(singleLine(ob_get_clean()), true, 'field_media'); 
		}
	}

	static function smartSet($data_array = array(), &$obj, $params = array())
	{
		//tratando os valores do usuário
		foreach($data_array as $field => $value)
		{
			if($field == 'id')
			{
				//smartSet não pode ser usada com ID. o ID deve ser setado antes da execução, pois é o que vai diferenciar um update de um insert na função.
				continue;
			}

			//depois refina
			if($obj->hasField($field))
			{
				$details = (array)$obj->getDetails($field);
				
				//setando os parametros
				foreach($params as $chave_parametro => $valor_parametro)
				{
					$details[$chave_parametro] = $valor_parametro;
				}

				//os joins NxN não armazenam nenuma informação na tabela atual.
				if($details['tipo'] == 'joinNN')
				{
					dboUI::fieldSQL($details['tipo'], $value, $obj, $details, $data_array);
				}
				else
				{
					$obj->{$field} = dboUI::fieldSQL($details['tipo'], $value, $obj, $details, $data_array);
				}
			}
		}

		//resolvendo flags do dboUI
		dboUI::resolveFlags($data_array, $obj);
	}

	static function fieldSQL($field_type, $raw_value = null, &$obj = false, $params = array(), &$data_array = array())
	{
		global $dbo;

		extract($params);

		//coloca os valores default
		if(
			strlen(trim($default_value)) && $obj && !$obj->id &&
			(
				$raw_value == '' ||
				$raw_value == null
			)
		)
		{
			return $default_value;
		}

		//senão, trata as informações.
		if($field_type == 'text')
		{
			return strlen(trim($raw_value)) ? $raw_value : (($isnull)?($dbo->null()):(''));
		}
		elseif($field_type == 'password')
		{
			//fazer logica do encrypt de password com hash512, dependendo do tamanho
			return strlen(trim($raw_value)) ? $raw_value : (($isnull)?($dbo->null()):(''));
		}
		elseif($field_type == 'textarea')
		{
			return strlen(trim($raw_value)) ? $raw_value : (($isnull)?($dbo->null()):(''));
		}
		elseif($field_type == 'textarea-rich')
		{
			return strlen(trim($raw_value)) ? $raw_value : (($isnull)?($dbo->null()):(''));
		}
		elseif($field_type == 'radio')
		{
			return strlen(trim($raw_value)) ? $raw_value : (($isnull)?($dbo->null()):(''));
		}
		elseif($field_type == 'checkbox')
		{
			if(is_array($raw_value) && sizeof($raw_value))
			{
				return implode("\n", $raw_value);
			}
			else
			{
				return $isnull ? $dbo->null() : '';
			}
		}
		elseif($field_type == 'price')
		{			
			if(!$formato || $formato == 'real' || $formato == 'generico')
			{
				$replace_from = array('R$ ', '$ ', '.', ',');
				$replace_to = array('', '', '', '.');
				$valor_price = str_replace($replace_from, $replace_to, $raw_value);
			}
			elseif($formato == 'dolar')
			{
				$replace_from = array('US$ ', ',');
				$replace_to = array('', '');
				$valor_price = str_replace($replace_from, $replace_to, $raw_value);
			}
			return $isnull && !strlen(trim($valor_price)) ? $dbo->null() : $valor_price;
		}
		elseif($field_type == 'select')
		{
			return $isnull && $raw_value == '' ? $dbo->null() : $raw_value;
		}
		elseif($field_type == 'date')
		{
			return $isnull && !strlen(trim($raw_value)) ? $dbo->null() : dataSQL($raw_value);
		}
		elseif($field_type == 'datetime')
		{
			return $isnull && !strlen(trim($raw_value)) ? $dbo->null() : dataHoraSQL($raw_value);
		}
		elseif($field_type == 'plugin')
		{
			$plugin = (array)$plugin;
			$plugin_path = DBO_PATH."/plugins/".$plugin['name']."/".$plugin['name'].".php";
			$plugin_class = "dbo_".$plugin['name'];
			//checa se o plugin existe, antes de mais nada.
			if(file_exists($plugin_path))
			{
				include_once($plugin_path); //inclui a classe
				$plug = new $plugin_class($plugin['params']); //instancia com os parametros
				$plug->setFormData($coluna); //seta os dados que vem do formulário para o plugin processar.
				return $plug->getData(); //pega os dados processados para o banco de dados.
			}
			else { //senão, avisa que não está instalado.
				die("O Plugin <b>'".$plugin['name']."'</b> não está instalado");
			}
		}
		elseif($field_type == 'join')
		{
			return $isnull && $raw_value == '' ? $dbo->null() : $raw_value;
		}
		elseif($field_type == 'joinNN')
		{
			if(!$obj)
			{
				trigger_error("Field type joinNN can only be used with a DBO object.", E_USER_ERROR);
			}
			else
			{
				$join = (array)$join;

				//insert
				if(!$obj->id)
				{

					if(!$obj->__dbo_ui_flag['temp_id'])
					{
						//cria um ID temporário para as inserções nas tabelas NxN
						$obj->__dbo_ui_flag['temp_id'] = rand(100000000, 999999999);
					}

					//marca as tabelas que precisam ser acertadas depois da operação.
					$obj->__dbo_ui_flag['pending_join_table'][$join['tabela_ligacao']]['key'] = $join['chave1'];

					foreach($raw_value as $chave2 => $valor2)
					{
						$obj_nn = new Dbo($join['tabela_ligacao']);
						$obj_nn->{$join['chave1']} = $obj->__dbo_ui_flag['temp_id'];
						$obj_nn->{$join['chave2']} = $valor2;
						$obj_nn->save();
					}
				//update
				} else {
					//remove os que já estavam cadastrados
					$cadastrados = new dbo($join['tabela_ligacao']);
					$cadastrados->{$join['chave1']} = $obj->id;
					$cadastrados->loadAll();
					do {
						$cadastrados->delete();
					}while($cadastrados->fetch());
					//insere os novos
					foreach($raw_value as $atualizado)
					{
						$obj_nn = new dbo($join['tabela_ligacao']);
						$obj_nn->{$join['chave1']} = $obj->id;
						$obj_nn->{$join['chave2']} = $atualizado;
						$obj_nn->save();
					}
				}
			}
		}
		elseif($field_type == 'image')
		{
			//checa se a imagem está em branco
			if(!strlen(trim($raw_value)))
			{
				return $isnull ? $dbo->null() : '';
			}

			//depois checamos se a imagem não é a mesma.
			$same_image = false;

			if($obj && $obj->{$coluna} == $raw_value)
			{
				$same_image = true;
			}

			//se não é a mesma imagem, faz o tratamento.
			if(!$same_image)
			{
				//setando o caminho do arquivo no server
				$hosted_file_path = DBO_PATH.'/upload/files/'.$raw_value;
				
				//classe para fazer resize das imagens
				include_once(DBO_PATH."/core/classes/simpleimage.php"); 

				foreach($image as $chave2 => $valor2) //processando o resize para todos os tamanhos das imagens
				{
					$valor2 = (array)$valor2;
					$w = $valor2['width'];
					$h = $valor2['height'];
					$q = $valor2['quality'];
					$image_prefix = $valor2['prefix'];
					$image = new SimpleImage();
					$image->load($hosted_file_path);
					if($w && !$h)
					{
						$image->resizeToWidth($w);
					}
					elseif ($h && !$w)
					{
						$image->resizeToHeight($h);
					}
					else
					{
						if($w >= $h) {
							$image->resizeToWidth($w);
						} else {
							$image->resizeToHeight($h);
						}
					}
					$file_path = DBO_PATH."/upload/images/".$image_prefix.$raw_value;
					$image->save($file_path, $q); //salvando o arquivo no server
				}
			}

			return $raw_value;
		}
		elseif($field_type == 'file')
		{
			if(strlen(trim($raw_value)))
			{
				$file_path = DBO_PATH.'/upload/files/'.$raw_value;
				$file_name = ${$coluna."_file_name"} ? dboFileName(${$coluna."_file_name"}.dboGetExtension($file_path), array('file_path' => $file_path)) : $raw_value;
				if(!file_exists($file_path))
				{
					die('O arquivo do campo <strong>'.$titulo.'</strong> informado no formulário não existe no servidor. Tente enviar o formulário novamente.');
				}
				else
				{
					//nao tem como pegar o mime type... colocar "mime"
					return $file_name."\n".$raw_value."\nmime\n".filesize($file_path);
				}
			}
		}
		elseif($field_type == 'media')
		{
			return strlen(trim($raw_value)) ? $raw_value : (($isnull)?($dbo->null()):(''));
		}
	}

	static function resolveFlags($data_array = array(), &$obj = false)
	{
		global $dbo;
		if(sizeof($data_array) && sizeof($data_array['__dbo_ui_flag']))
		{
			foreach($data_array['__dbo_ui_flag'] as $dbo_ui_flag => $dbo_ui_flag_info)
			{
				//flag para limpar os dados caso não sejam setados em campos do tipo radio, checkbox
				if($dbo_ui_flag == 'empty')
				{
					foreach($dbo_ui_flag_info as $field_name)
					{
						$details = (array)$obj->getDetails($field_name);
						// -----------------------------------------------
						// RADIO E CHECKBOX ------------------------------
						// -----------------------------------------------
						if(
							($details['tipo'] == 'checkbox' || $details['tipo'] == 'radio') &&
							!isset($data_array[$field_name])
						)
						{
							$obj->{$field_name} = $details['isnull'] ? $dbo->null() : '';
						}
						// -----------------------------------------------
						// JOIN NxN --------------------------------------
						// -----------------------------------------------
						elseif($details['tipo'] == 'joinNN' && !isset($data_array[$field_name]) && $obj->id)
						{
							$join = (array)$details['join'];
							$cadastrados = new dbo($join['tabela_ligacao']);
							$cadastrados->{$join['chave1']} = $obj->id;
							$cadastrados->loadAll();
							do {
								$cadastrados->delete();
							}while($cadastrados->fetch());
						}
					}
				}
			}
		}
	}

} //class declaration

?>