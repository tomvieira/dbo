<?
	require_once('lib/includes.php');

	$dbo_media_manager_image_sizes = array_merge($dbo_media_manager_image_sizes_default, (array)$dbo_media_manager_image_sizes);

	//função que cria os thumbs da imagem em questão
	function resampleThumbs($file_name, $file_path)
	{
		require_once(DBO_PATH."/core/classes/simpleimage.php");
		global $dbo_media_manager_image_sizes;

		foreach($dbo_media_manager_image_sizes as $slug => $data)
		{
			$image = new SimpleImage();
			$image->load($file_path.$file_name);
			if($data['max_width'] >= $data['max_height']) {
				$image->resizeToWidth($data['max_width']);
			} else {
				$image->resizeToHeight($data['max_height']);
			}
			$caminho_arquivo = $file_path.'thumbs/'.$slug."-".$file_name;
			$image->save($caminho_arquivo, $data['quality']); //salvando o arquivo no server
		}
	}

	if(!secureUrl())
	{
		$json_result['error'] = 'Erro: tentativa de acesso insegura';
		echo json_encode($json_result);
		exit();
	}

	CSRFCheckJson();

	if(isset($_GET['file']) && strstr($_GET['file'], '..'))
	{
		$json_result['message'] = '<div class="error">Erro: tentativa de acesso de arquivo insegura.</div>';
		echo json_encode($json_result);
		exit();
	}

	$json_result = array();

	$file_path = DBO_PATH."/upload/dbo-media-manager/";

	if($_GET['action'] == 'upload-file')
	{

		$uploaded_file_data = $_FILES['peixe_ajax_file_upload_file'];

		//checa se ouve erro no upload, antes de mais nada...
		if($uploaded_file_data[error] > 0)
		{
			$uploaded_file_data[error] = 'Erro ao enviar o arquivo. Cod '.$uploaded_file_data[error];
			return $uploaded_file_data;
		}

		//pegando a extensão do arquivo
		$new_file_name = dboFileName($uploaded_file_data[name], array('file_path' => $file_path));

		//salvando o arquivo com novo nome e retornando as informações
		if(move_uploaded_file($uploaded_file_data[tmp_name], $file_path.$new_file_name))
		{
			$uploaded_file_data[old_name] = $uploaded_file_data[name];
			$uploaded_file_data[name] = $new_file_name;
			$json_result = $uploaded_file_data;

			//aqui temos que fazer os resamples das imagens, baseado nos tamanhos definidos no sistema.
			resampleThumbs($new_file_name, $file_path);
		}
		else
		{
			//erro 5: erro ao mudar o arquivo de lugar...
			$json_result = array('error' => 'Erro ao enviar o arquivo. O tamanho não pode exceder '.min(ini_get('post_max_size'), ini_get('upload_max_filesize')));
		}
	}
	//deletando uma imagem
	elseif($_GET['action'] == 'delete-media')
	{
		//impedindo espertinhos de apagar o que não devem
		if(unlink($file_path.$_GET['file']))
		{
			//deletando thumbs
			foreach($dbo_media_manager_image_sizes as $slug => $data)
			{
				@unlink($file_path.'thumbs/'.$slug.'-'.$_GET['file']);
			}

			$json_result['message'] = '<div class="success">Arquivo excluido com sucesso.</div>';
			$json_result['reload'][] = '#block-media-list';
			$json_result['reload'][] = '#block-details';
			$json_result['callback'][] = 'mediaManagerInit';
			$json_result['eval'] = 'setTimeout(function(){ showFormUpload(); }, 500)';
		}
	}
	//fazendo o crop da imagem
	elseif($_GET['action'] == 'do-crop')
	{
		//setando o src
		$src = $file_path.$_GET['file'];

		//arredondando valores
		$x = round($_POST['c-x']);
		$y = round($_POST['c-y']);
		$w = round($_POST['c-w']);
		$h = round($_POST['c-h']);

		//descobrindo o tipo de arquivo
		define(IMAGETYPE_GIF, 1);
		define(IMAGETYPE_JPEG, 2);
		define(IMAGETYPE_JPEG, 3);

		$image_info = getimagesize($src);
		$image_type = $image_info[2];

		if( $image_type == IMAGETYPE_JPEG ) {
			$img_r = imagecreatefromjpeg($src);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			$img_r = imagecreatefromgif($src);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			$img_r = imagecreatefrompng($src);
		}

		$targ_w = $w;
		$targ_h = $h;
		$jpeg_quality = 90;

		$dst_r = ImageCreateTrueColor($targ_w,$targ_h);

		//transparencia do PNG
		if($image_type == IMAGETYPE_PNG)
		{
			imagealphablending($dst_r, false);
			imagesavealpha($dst_r,true);
			$transparent = imagecolorallocatealpha($dst_r, 255, 255, 255, 127);
			imagefilledrectangle($dst_r, 0, 0, $targ_w, $targ_h, $transparent);
		}

		imagecopyresampled($dst_r,$img_r,0,0,$x,$y,$targ_w,$targ_h,$w,$h);

		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($dst_r, $file_path.$_GET['file'], $jpeg_quality);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($dst_r, $file_path.$_GET['file']);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($dst_r, $file_path.$_GET['file']);
		}

		//criando thumbs apos o crop
		resampleThumbs($_GET['file'], $file_path);

		$json_result['eval'] = 'stopCrop(); setTimeout(function(){ reloadAfterCrop(); }, 100)';
		
	}

	echo json_encode($json_result);

?>