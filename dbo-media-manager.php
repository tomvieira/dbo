<? require_once("header.php"); ?>
<script src="<?= DBO_URL ?>/plugins/jcrop_dbo/js/jquery.Jcrop.min.js"></script>
<link rel="stylesheet" href="<?= DBO_URL ?>/plugins/jcrop_dbo/css/jquery.Jcrop.css" type="text/css" />
<style>
	html, body { height: 100%; }
	.processing-time, .dbo-queries-number { display: none; }
	#main-wrap { height: 100%; }
	.peixe-ajax-loader { width: 60px; height: 60px; font-size: 30px; text-align: center; line-height: 50px; border-radius: 1000px; background-color: rgba(1,1,1,.8); top: 50%; left: 50%; margin-left: -30px; margin-top: -30px; }
	.peixe-ajax-loader span { display: none; }
</style>
<?

	//verificando definições de tamanhos de crop e tamanhos de imagem
	if(!isset($dbo_media_manager_custom_crops))
	{
		$dbo_media_manager_custom_crops = array(
			'quadradro' => array(
				'name' => 'Recorte quadrado',
				'width' => 1,
				'height' => 1,
				'force_resize' => false
			),
			'livre' => array(
				'name' => 'Recorte livre',
				'width' => '',
				'height' => '',
				'force_resize' => false
			)
		);
	}

	//setando tamanhos das imagens
	$dbo_media_manager_image_sizes = array_merge($dbo_media_manager_image_sizes_default, (array)$dbo_media_manager_image_sizes);

	//primeiro checando se o cidadão pode fazer upload de imagens
	if(!hasPermission('media-manager'))
	{
		?>
		<h3 class="text-center"><br /><br /><br />Erro: Você não tem permissão para fazer upload de imagens (media-manager)</h3>
		<?
	}
	else
	{
		$img_token = generateToken();
		$media_folder_path = DBO_PATH.'/upload/dbo-media-manager/';
		$media_folder_url = DBO_URL.'/upload/dbo-media-manager/';
		$selected_file = ((file_exists($media_folder_path.$_GET['file']))?($_GET['file']):(false));
		if(!is_writable($media_folder_path))
		{
			?>
			<h3 class="text-center"><br /><br /><br />Erro: a pasta de upload não tem permissão de escrita</h3>
			<?
		}
		else
		{
			?>
			<div class="row full" id="dbo-media-manager">
				<div class="large-7 columns">
					<div id="block-media-list">
						<?
							$handle = opendir($media_folder_path); //abrindo a diretorio para leitura, retorna array $arquivos
							while (false !== ($opendirfiles = readdir($handle)))
							{
								if(
									$opendirfiles != '.' && 
									$opendirfiles != '..' && 
									$opendirfiles != 'thumbs' && 
									$opendirfiles != 'Thumbs.db' 
								)
								{
									$files[] = $opendirfiles;
								}
							}
							closedir($handle); //fechando diretorio
							if(!sizeof($files))
							{
								?><h3 class="text-center"><br /><br /><br />Ainda não há imagens cadastradas</h3><?					
							}
							else
							{
								?>
								<ul class="large-block-grid-4">
									<?
										foreach($files as $key => $value)
										{
											?>
											<li class="wrapper-media-item <?= (($selected_file == $value)?('active'):('')) ?>">
												<div class="media-item <?= (($selected_file == $value)?('active'):('')) ?>" style="background-image: url('<?= $media_folder_url.'thumbs/medium-'.$value.(($selected_file == $value)?('?='.$img_token):('')) ?>')" data-file="<?= $value ?>">
													<span class="trigger-delete" data-file="<?= $value ?>" data-url="<?= secureUrl('ajax-dbo-media-manager-actions.php?action=delete-media&file='.$value.'&'.CSRFVar()) ?>"><i class="fa-close"></i></span>
													<span class="legenda"><?= $value ?></span>
												</div>
											</li>
											<?
										}
									?>
								</ul>
								<?
							}
						?>
					</div>
				</div>
				<div class="large-5 columns">
					<div id="block-details">
						<?
							if($selected_file)
							{
								list($width, $height, $lixo, $lixo) = getimagesize($media_folder_path.$selected_file);
								?>
								<div id="detalhes">
									<div class="row">
										<div class="large-6 columns"><h6>Detalhes</h6></div>
										<div class="large-6 columns text-right"><small id="main-pic-size"><?= $width ?> x <?= $height ?> px</small></div>
									</div>
									<div class="text-center">
										<div id="main-pic">
											<img src="<?= $media_folder_url.$selected_file ?>?=<?= $img_token ?>" id="selected-image" data-width="<?= $width ?>" data-height="<?= $height ?>" data-file="<?= $selected_file ?>"/>
										</div>
									</div>
									<ul id="drop-crop" class="f-dropdown" data-dropdown-content aria-hidden="true" tabindex="-1">
										<?
											if(is_array($dbo_media_manager_custom_crops) && sizeof($dbo_media_manager_custom_crops))
											{
												foreach($dbo_media_manager_custom_crops as $slug => $settings)
												{
													?>
													<li><a href="#" data-w="<?= $settings['width'] ?>" data-h="<?= $settings['height'] ?>" data-force_resize="<?= (($settings['force_resize'])?('true'):('false')) ?>"><?= htmlSpecialChars($settings['name']) ?></a></li>
													<?
												}
											}
										?>
									</ul>
									<span class="button-crop" data-dropdown="drop-crop" title="Recortar" data-tooltip><i class="fa-crop"></i></span>
									<div id="cropper-controls">
										<form method="post" action="<?= secureUrl('ajax-dbo-media-manager-actions.php?action=do-crop&file='.$selected_file.'&'.CSRFVar()) ?>" class="no-margin peixe-json" id="form-crop" peixe-log>
											<input type="hidden" name="c-x" id="c-x" value=""/>
											<input type="hidden" name="c-y" id="c-y" value=""/>
											<input type="hidden" name="c-w" id="c-w" value=""/>
											<input type="hidden" name="c-h" id="c-h" value=""/>
											<input type="hidden" name="force_resize" id="force_resize" value="false"/>
											<span class="button radius large" onClick="doCrop();">Recortar</span>
											<span class="button radius secondary large" onClick="stopCrop();">Cancelar</span>
										</form>
									</div>
									<table class="tools">
										<tbody>
											<tr>
												<td>Alinhamento</td>
												<td style="position: relative;">
													<div id="position-selector" class="selector">
														<span class="active"><i class="fa-fw fa-align-left" title="Esquerda" data-tooltip data-value="text-left"></i></span>
														<span><i class="fa-fw fa-align-center" title="Centro" data-tooltip data-value="text-center"></i></span>
														<span><i class="fa-fw fa-align-right" title="Direita" data-tooltip data-value="text-right"></i></span>
														|
														<span><i class="fa-fw fa-outdent" title="Flutuando à esquerda" data-tooltip data-value="float-left"></i></span>
														<span><i class="fa-fw fa-outdent flip-horizontal" title="Flutuando à direita" data-tooltip data-value="float-right"></i></span>
													</div>
												</td>
											</tr>
											<tr>
												<td>Tamanho</td>
												<td>
													<div id="size-selector" class="selector">
														<?
															foreach($dbo_media_manager_image_sizes as $slug => $data)
															{
																?>
																<span data-slug="<?= $slug ?>" data-value="thumbs/<?= $slug ?>-" class="<?= (($slug == 'medium')?('active'):('')) ?>"><?= $data['name'] ?></span>
																<?
															}
														?>
														<span data-slug="original" data-value="">Original</span>
													</div>
												</td>
											</tr>
											<tr>
												<td>Legenda</td>
												<td>
													<input type="text" name="legenda" id="legenda" value="" placeholder="Digite a legenda para a imagem" class="no-margin"/>
												</td>
											</tr>
										</tbody>
									</table>
									<div class="text-right">
										<input type="button" name="" id="inserir-imagem" value="Inserir a imagem no texto" class="button small no-margin radius"/>
									</div>
								</div>
								<?
							}
						?>
					</div>
					<div id="block-upload">
						<h4 id="title-upload" class="pointer"><strong><i class="fa-plus-circle"></i> Enviar imagem</strong></h4>
						<form method="post" action="" class="no-margin" id="form-upload" enctype="multipart/form-data" style="<?= (($selected_file)?('display: none;'):('')) ?>">
							<label for="file">Selecione a imagem para upload</label>
							<input type="file" name="arquivo" id="arquivo" peixe-ajax-file-upload data-action="<?= secureUrl('ajax-dbo-media-manager-actions.php?action=upload-file&'.CSRFVar()) ?>"/>
						</form>
					</div>
				</div>
			</div>
			<?
		}
	}
?>
<script>

	var jcrop_api;
	var scale;

	function startCrop(w, h) {
		$('#drop-crop').css('left', '-99999px').removeClass('open');
		img = $('#selected-image');
		scale = img.data('width')/img.width();
		if(typeof w == 'number' && typeof h == 'number'){
			img.Jcrop({
				setSelect: [ 10, 10, 150, 150 ],
				onSelect: setCoords,
				onChange: setCoords,
				aspectRatio: w / h
			}, function(){
				jcrop_api = this;					
			});
		}
		else {
			img.Jcrop({
				setSelect: [ 10, 10, 120, 120 ],
				onSelect: setCoords,
				onChange: setCoords,
				aspectRatio: null
			}, function(){
				jcrop_api = this;	
			});
		}
		$('#cropper-controls').addClass('active');
	}

	function setCoords(c) {
		$('#c-x').val(c.x*scale);
		$('#c-y').val(c.y*scale);
		$('#c-w').val(c.w*scale);
		$('#c-h').val(c.h*scale);
	}

	function stopCrop() {
		jcrop_api.destroy();
		$('#cropper-controls').removeClass('active');
	}

	function mediaManagerInit() {
		peixeAjaxFileUploadInit();
	}

	function doCrop() {
		$('#form-crop').submit();
	}

	function selectItem(media_item) {
		peixeGet(((media_item)?(document.URL+'&file='+media_item.data('file')):('dbo-media-manager.php?dbo_modal=1')), function(d) {
			var html = $.parseHTML(d);
			/* item 1 */
			handler = '#block-details';
			content = $(html).find(handler).html();
			if(typeof content != 'undefined'){
				$(handler).fadeHtml(content);
			}
			/* item 2 */
			handler = '#block-details';
			content = $(html).find(handler).html();
			if(typeof content != 'undefined'){
				$(handler).fadeHtml(content);
			}
			mediaManagerInit();
		})
		$('#form-upload:visible').hide();
		return false;
	}
	function deselectItem(media_item) {
		selectItem(false);
		setTimeout(function(){
			$('#form-upload').slideDown();
		}, 500);
	}

	function reloadAfterCrop() {
		peixeGet(document.URL+'&file='+$('#selected-image').data('file'), function(d) {
			var html = $.parseHTML(d);
			/* item 1 */
			handler = '#block-media-list .wrapper-media-item.active';
			content = $(html).find(handler).html();
			if(typeof content != 'undefined'){
				$(handler).fadeHtml(content);
			}
			/* item 1 */
			handler = '#main-pic';
			content = $(html).find(handler).html();
			if(typeof content != 'undefined'){
				$(handler).fadeHtml(content);
			}
			/* item 1 */
			handler = '#main-pic-size';
			content = $(html).find(handler).html();
			if(typeof content != 'undefined'){
				$(handler).fadeHtml(content);
			}
		})
		return false;
	}

	function showFormUpload() {
		$('#form-upload').slideDown();
	}

	function inserirImagemAtiva() {
		
		//variaveis para montar a tag da imagem
		var file_name = $('#main-pic img').data('file');
		var align = $('#position-selector .active i').data('value');
		var size = $('#size-selector .active').data('value');
		var caption = $('#legenda').val();

		var       img = '<div media-manager-element="image-container" class="'+align+'">';
		var img = img + '<dl><dt>';
		var img = img + '<img media-manager-element="image" src="dbo/upload/dbo-media-manager/'+size+file_name+'">';

		//verificando se tem caption
		if(caption.length){
			img = img + '<dd class="text-left">'+caption+'</dd>';
		}

		var img = img + '</dt></dl>';
		var img = img + '</div>';

		parent.tinyMCE.activeEditor.insertContent(img);
		parent.tinyMCE.activeEditor.nodeChanged();
		parent.tinyMCE.activeEditor.windowManager.close();
	}

	$(document).ready(function(){

		mediaManagerInit();

		//event handler para submissão automatica do form de upload.
		//document.querySelector('#arquivo').addEventListener('uploadDone', function(e){
		$(document).on('uploadDone', '#arquivo', function(e, detail){
			peixeGet(document.URL+'&file='+detail.new_file_name, function(d) {
				var html = $.parseHTML(d);
				/* item 1 */
				handler = '#block-media-list';
				content = $(html).find(handler).html();
				if(typeof content != 'undefined'){
					$(handler).fadeHtml(content);
				}
				/* item 2 */
				handler = '#block-details';
				content = $(html).find(handler).html();
				if(typeof content != 'undefined'){
					$(handler).fadeHtml(content);
				}
				/* item 3 */
				handler = '#block-upload';
				content = $(html).find(handler).html();
				if(typeof content != 'undefined'){
					$(handler).fadeHtml(content);
				}
				mediaManagerInit();
			})
			return false;
		});

		//clicks nos itens da listagem
		$(document).on('click', '.media-item', function(){
			clicado = $(this);
			if(clicado.hasClass('active')){
				clicado.removeClass('active').closest('li').removeClass('active');
				deselectItem(clicado);
			}
			else {
				$('.media-item').removeClass('active').closest('li').removeClass('active');
				clicado.addClass('active').closest('li').addClass('active');
				selectItem(clicado);
			}
		});

		//deletendo os itens da listagem
		$(document).on('click', '#dbo-media-manager .trigger-delete', function(e){
			e.stopPropagation();
			clicado = $(this);
			var ans = confirm("Tem certeza que deseja excluir a imagem \""+clicado.data('file')+"\"?");
			if (ans==true) {
				peixeJSON(clicado.data('url'), '', '', true);
			} 
		});

		$(document).on('click', '.selector span', function(){
			clicado = $(this);
			clicado.closest('.selector').find('span').removeClass('active');
			clicado.addClass('active');
		});

		$(document).on('click', '#title-upload', function(){
			if($('#detalhes:visible').length){
				$('#detalhes:visible').slideUp(function(){
					$('#form-upload').slideDown();
					$('#block-media-list .active').removeClass('active');
				})
			}			
		});
		
		//dando trigger no selector dos crops
		$(document).on('click', '#drop-crop a', function(e){
			e.preventDefault();
			clicado = $(this);
			startCrop(clicado.data('w'), clicado.data('h'));
		});

		//tratando a inserção das imagens no editor
		$(document).on('click', '#inserir-imagem', function(){
			inserirImagemAtiva();
		});

	}) //doc.ready
</script>
<? require_once("footer.php"); ?>