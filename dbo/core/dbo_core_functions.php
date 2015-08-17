<?
	
	include_once('dbo-shortcodes.php');
	include_once('dbo-formatting.php');

	//defines padrão, se não setados pelo usuário.
	@define(DBO_TERM_CADASTROS, 'Cadastros');

	//variáveis de sistema

	$_system['media_manager']['default_image_sizes'] = array(
		'small' => array(
			'name' => 'Miniatura',
			'max_width' => '200',
			'max_height' => '200',
			'quality' => '90'
		),
		'medium' => array(
			'name' => 'Médio',
			'max_width' => '400',
			'max_height' => '400',
			'quality' => '80'
		),
		'large' => array(
			'name' => 'Grande',
			'max_width' => '1200',
			'max_height' => '1200',
			'quality' => '80'
		)
	);

	// ----------------------------------------------------------------------------------------------------------------

	function dboImportJs($libs = array(), $params = array())
	{
		extract($params);
		
		$libs = (array)$libs;

		$js_url = DBO_URL.'/../js';

		//impedindo o import duplo de JS
		global $_system;
		if(!is_array($_system['dbo_imported_js'])) $_system['dbo_imported_js'] = array();

		$return = "<!-- DBO IMPORTED JS -->\n";

		if(!in_array("modernizr", $_system['dbo_imported_js']) && (in_array("modernizr", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/vendor/custom.modernizr.js\"></script>\n";
			$_system['dbo_imported_js'][] = "modernizr";
		}
		if(!in_array("jquery", $_system['dbo_imported_js']) && (in_array("jquery", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/vendor/jquery.js\"></script>\n";
			$_system['dbo_imported_js'][] = "jquery";
		}
		if(!in_array("jquery-ui", $_system['dbo_imported_js']) && (in_array("jquery-ui", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery-ui/jquery-ui.js\"></script>\n";
			$return .= "<script src=\"".$js_url."/jquery-ui/ui.datepicker-pt-BR.js\"></script>\n";
			$return .= "<link rel=\"stylesheet\" href=\"".$js_url."/jquery-ui/jquery-ui.css\">\n";
			$_system['dbo_imported_js'][] = "jquery-ui";
		}
		if(!in_array("easing", $_system['dbo_imported_js']) && (in_array("easing", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.easing.js\"></script>\n";
			$_system['dbo_imported_js'][] = "easing";
		}
		if(!in_array("peixelaranja", $_system['dbo_imported_js']) && (in_array("peixelaranja", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/peixelaranja.js\"></script>\n";
			$_system['dbo_imported_js'][] = "peixelaranja";
		}
		if(!in_array("browser", $_system['dbo_imported_js']) && (in_array("browser", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/browser.js\"></script>\n";
			$_system['dbo_imported_js'][] = "browser";
		}
		if(!in_array("dbo", $_system['dbo_imported_js']) && (in_array("dbo", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/dbo.js\"></script>\n";
			$_system['dbo_imported_js'][] = "dbo";
		}
		if(!in_array("foundation", $_system['dbo_imported_js']) && (in_array("foundation", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/foundation.min.js\"></script>\n";
			$_system['dbo_imported_js'][] = "foundation";
		}
		if(!in_array("html5shiv", $_system['dbo_imported_js']) && (in_array("html5shiv", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/html5shiv.js\"></script>\n";
			$_system['dbo_imported_js'][] = "html5shiv";
		}
		if(!in_array("autosize", $_system['dbo_imported_js']) && (in_array("autosize", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.autosize.js\"></script>\n";
			$_system['dbo_imported_js'][] = "autosize";
		}
		if(!in_array("color", $_system['dbo_imported_js']) && (in_array("color", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.color.js\"></script>\n";
			$_system['dbo_imported_js'][] = "color";
		}
		if(!in_array("hoverflow", $_system['dbo_imported_js']) && (in_array("hoverflow", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.hoverflow.js\"></script>\n";
			$_system['dbo_imported_js'][] = "hoverflow";
		}
		if(!in_array("maskedinput", $_system['dbo_imported_js']) && (in_array("maskedinput", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.maskedinput.js\"></script>\n";
			$_system['dbo_imported_js'][] = "maskedinput";
		}
		if(!in_array("quicktags", $_system['dbo_imported_js']) && (in_array("quicktags", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/quicktags.js\"></script>\n";
			$_system['dbo_imported_js'][] = "quicktags";
		}
		if(!in_array("mousewheel", $_system['dbo_imported_js']) && (in_array("mousewheel", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.mousewheel.js\"></script>\n";
			$_system['dbo_imported_js'][] = "mousewheel";
		}
		if(!in_array("parallax", $_system['dbo_imported_js']) && (in_array("parallax", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.parallax.min.js\"></script>\n";
			$_system['dbo_imported_js'][] = "parallax";
		}
		if(!in_array("perfectscrollbar", $_system['dbo_imported_js']) && (in_array("perfectscrollbar", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.perfectscrollbar.min.js\"></script>\n";
			$return .= "<link rel=\"stylesheet\" href=\"".$js_url."/jquery.perfectscrollbar.min.css\">\n";
			$_system['dbo_imported_js'][] = "perfectscrollbar";
		}
		if(!in_array("placeholder", $_system['dbo_imported_js']) && (in_array("placeholder", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.placeholder.js\"></script>\n";
			$_system['dbo_imported_js'][] = "placeholder";
		}
		if(!in_array("scrolllock", $_system['dbo_imported_js']) && (in_array("scrolllock", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.scrolllock.js\"></script>\n";
			$_system['dbo_imported_js'][] = "scrolllock";
		}
		if(!in_array("preloader", $_system['dbo_imported_js']) && (in_array("preloader", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.preloader.js\"></script>\n";
			$_system['dbo_imported_js'][] = "preloader";
		}
		if(!in_array("priceformat", $_system['dbo_imported_js']) && (in_array("priceformat", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.priceformat.js\"></script>\n";
			$_system['dbo_imported_js'][] = "priceformat";
		}
		if(!in_array("scrollto", $_system['dbo_imported_js']) && (in_array("scrollto", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.scrollto.js\"></script>\n";
			$_system['dbo_imported_js'][] = "scrollto";
		}
		if(!in_array("starsrating", $_system['dbo_imported_js']) && (in_array("starsrating", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.starsrating.js\"></script>\n";
			$_system['dbo_imported_js'][] = "starsrating";
		}
		if(!in_array("sticky-kit", $_system['dbo_imported_js']) && (in_array("sticky-kit", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.sticky-kit.js\"></script>\n";
			$_system['dbo_imported_js'][] = "sticky-kit";
		}
		if(!in_array("timepicker", $_system['dbo_imported_js']) && (in_array("timepicker", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.timepicker.js\"></script>\n";
			$_system['dbo_imported_js'][] = "timepicker";
		}
		if(!in_array("colorbox", $_system['dbo_imported_js']) && (in_array("colorbox", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/colorbox/jquery.colorbox.js\"></script>\n";
			$return .= "<script src=\"".$js_url."/colorbox/jquery.colorbox.scrollfix.js\"></script>\n";
			$return .= "<link rel=\"stylesheet\" href=\"".$js_url."/colorbox/colorbox.css\">\n";
			$_system['dbo_imported_js'][] = "colorbox";
		}
		if(!in_array("multiselect", $_system['dbo_imported_js']) && (in_array("multiselect", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/multiselect/js/ui.multiselect.js\"></script>\n";
			$return .= "<link rel=\"stylesheet\" href=\"".$js_url."/multiselect/css/ui.multiselect.css\">\n";
			$_system['dbo_imported_js'][] = "multiselect";
		}
		if(!in_array("nestable", $_system['dbo_imported_js']) && (in_array("nestable", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/nestable/jquery.nestable.js\"></script>\n";
			$return .= "<link rel=\"stylesheet\" href=\"".$js_url."/nestable/jquery.nestable.css\">\n";
			$_system['dbo_imported_js'][] = "nestable";
		}
		if(!in_array("tablesorter", $_system['dbo_imported_js']) && (in_array("tablesorter", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/tablesorter/js/jquery.tablesorter.combined.min.js\"></script>\n";
			$_system['dbo_imported_js'][] = "tablesorter";
		}
		if(!in_array("smooth-scroll", $_system['dbo_imported_js']) && (in_array("smooth-scroll", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/smooth-scroll.js\"></script>\n";
			$_system['dbo_imported_js'][] = "smooth-scroll";
		}
		if(!in_array("stellar", $_system['dbo_imported_js']) && (in_array("stellar", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.stellar.min.js\"></script>\n";
			$_system['dbo_imported_js'][] = "stellar";
		}
		if(!in_array("tinymce", $_system['dbo_imported_js']) && (in_array("tinymce", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/tinymce/tinymce.min.js\"></script>\n";
			$return .= "<script src=\"".$js_url."/tinymce/jquery.tinymce.min.js\"></script>\n";
			$_system['dbo_imported_js'][] = "tinymce";
		}
		if(!in_array("select2", $_system['dbo_imported_js']) && (in_array("select2", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/select2/jquery.select2.min.js\"></script>\n";
			$return .= "<script src=\"".$js_url."/select2/jquery.select2.pt-BR.js\"></script>\n";
			$return .= "<link rel=\"stylesheet\" href=\"".$js_url."/select2/select2.css\">\n";
			$_system['dbo_imported_js'][] = "select2";
		}
		if(!in_array("waypoints", $_system['dbo_imported_js']) && (in_array("waypoints", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/jquery.waypoints.min.js\"></script>\n";
			$_system['dbo_imported_js'][] = "waypoints";
		}
		if(!in_array("inview", $_system['dbo_imported_js']) && (in_array("inview", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/shortcuts/jquery.inview.min.js\"></script>\n";
			$_system['dbo_imported_js'][] = "inview";
		}
		if(!in_array("infinite", $_system['dbo_imported_js']) && (in_array("infinite", $libs) || $import_all))
		{
			$return .= "<script src=\"".$js_url."/shortcuts/jquery.infinite.min.js\"></script>\n";
			$_system['dbo_imported_js'][] = "infinite";
		}

		$return .= "<!-- DBO_IMPORTED_JS (END) -->\n";

		return $return;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function dboRegisterJS($code, $once = false, $id)
	{
		global $_system;
		if($once)
		{
			if(!$_system['dbo_registered_js'][$id])
			{
				$_system['dbo_registered_js'][$id] = $id;
				$_system['dbo_registered_js_code'] .= $code;
			}
		}
		else
		{
			$_system['dbo_registered_js_code'] .= $code;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------

	function imagemAjustada($image, $params = array())
	{
		/**
		* @params:
		*  height 
		*  max_width 
		*  classes 
		*  styles
		*  size: small | medium | large
		*/
		extract($params);

		$height = $height !== null ? $height : '65%';
		$max_width = $max_width !== null ? $max_width : '100%';

		if($image)
		{
			return '<div style="background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url('.$image.'); padding-top: '.$height.'; max-width: '.$max_width.';'.$styles.'" class="'.$classes.'"><img src="'.$image.'" style="display: none;" alt=""></div>';
		}
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function listYoutubeThumb($obj, $field)
	{
		return youtubeThumb($obj->{$field}, array(
			'classes' => 'thumb-lista',
		));
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function youtubeThumbUrl($url, $params = array())
	{
		extract($params);
		parse_str(parse_url($url, PHP_URL_QUERY), $array);
		return 'http://img.youtube.com/vi/'.$array['v'].'/0.jpg';
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function youtubeThumb($url, $params = array())
	{
		extract($params);
		return '<img src="'.youtubeThumbUrl($url, $params).'" alt="" class="'.$classes.'">';
	}
	
	function youtubeEmbedUrl($url, $params = array())
	{
		extract($params);
		parse_str(parse_url($url, PHP_URL_QUERY), $array);
		return 'http://www.youtube.com/embed/'.$array['v'].'?rel=0&wmode=transparent';
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboRegisterDocReady($code, $once = false, $id = null)
	{
		global $_system;
		if($once)
		{
			if(!$_system['dbo_registered_doc_ready'][$id])
			{
				$_system['dbo_registered_doc_ready'][$id] = $id;
				$_system['dbo_registered_doc_ready_code'] .= $code;
			}
		}
		else
		{
			$_system['dbo_registered_doc_ready_code'] .= $code;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------

	function dboRegisterDboInit($code, $once = false, $id = null)
	{
		global $_system;
		if($once)
		{
			if(!$_system['dbo_registered_dbo_init'][$id])
			{
				$_system['dbo_registered_dbo_init'][$id] = $id;
				$_system['dbo_registered_dbo_init_code'] .= $code;
			}
		}
		else
		{
			$_system['dbo_registered_dbo_init_code'] .= $code;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------

	function dboGetRegisteredJS()
	{
		global $_system;
		ob_start();
		if(!empty($_system['dbo_registered_js_code']) || !empty($_system['dbo_registered_doc_ready_code']) || !empty($_system['dbo_registered_dbo_init_code']))
		{
			?>
			<!-- DBO REGISTERED JS -->
			<script>
				
				function dboInit() {
					<?= $_system['dbo_registered_dbo_init_code'] ?>	
				}

				<?= $_system['dbo_registered_js_code'] ?>
				$(document).ready(function(){
					<?= $_system['dbo_registered_doc_ready_code'] ?>
				}) //doc.ready
			</script>
			<!-- DBO REGISTERED JS (END) -->
			<?
		}
		return ob_get_clean();
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function getPrettyHeaderSetting($setting)
	{
		global $_system;
		return $_system['pretty_header'][$setting];
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function hasPrettyHeader()
	{
		global $_system;
		return is_array($_system['pretty_header']);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function prettyHeaderAtts()
	{
		global $_system;
		if(hasPrettyHeader())
		{
			extract($_system['pretty_header']);
			$classes .= ' pretty-header';
			if($theme) $classes .= ' '.$theme.'-theme';
			if($hide_menu) $classes .= ' hide-menu';
			if($height) $styles .= ' height: '.$height.'px;';
		}
		return ' class="'.trim($classes).'" style="'.trim($styles).'"';
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function prettyHeaderLogo()
	{
		ob_start();
		if(hasPrettyHeader())
		{
			?>
			<div class="row">
				<div class="large-12 columns">
					<a href="index.php" id="pretty-logo" style="position: absolute; top: <?= getPrettyHeaderSetting('logo_offset') ?>px; left: 15px; z-index: 50;"><img src="images/admin-logo.png" alt="" style="max-height: <?= getPrettyHeaderSetting('logo_height') ?>px;"></a>
				</div>
			</div>
			<?php
		}
		return ob_get_clean();
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function getGravatar( $email, $s = 200, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img ) {
			$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function marca ($hailshack, $needle)
	{
		return str_replace($needle, "<span class='marca'>".$needle."</span>", $hailshack);
	}

	// ----------------------------------------------------------------------------------------------------------------

	function safeArrayKey($key, $array)
	{
		$key = $key ? $key : 1;
		if(@array_key_exists($key, $array))
		{
			return safeArrayKey($key+100, $array);
		}
		return $key;
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboContent($content)
	{
		global $hooks;
		$content = $hooks->apply_filters('dbo_content', $content);
		return $content;
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function filterMediaManagerUrl($content)
	{
		return str_replace("dbo/upload/dbo-media-manager/", DBO_URL."/upload/dbo-media-manager/", $content);
	}
	$hooks->add_filter('dbo_content', 'filterMediaManagerUrl');

	// ----------------------------------------------------------------------------------------------------------------

	function filterMediaManagerPath($content)
	{
		return str_replace("dbo/upload/dbo-media-manager/", DBO_PATH."/upload/dbo-media-manager/", $content);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function cleanupEditorCode($content)
	{
		//removendo height do style das imagens
		$content = preg_replace('/(<img.+style=".*)(height: .*px;)(".*[^>].*\/>)/im', '$1$3', $content);

		return $content;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function paginaCleanupPreSave($pag)
	{
		$pag->texto = cleanupEditorCode($pag->texto);
		return $pag;
	}
	$hooks->add_filter('dbo_pagina_pre_save', 'paginaCleanupPreSave');

	// ----------------------------------------------------------------------------------------------------------------

	function dboAuth($tipo = '')
	{
		if($tipo == '')
		{
			if(!loggedUser())
			{
				header("Location: login.php?dbo_redirect=".base64_encode(fullUrl()));
				exit();
			}
		}
		elseif($tipo == 'ajax' || $tipo == 'json')
		{
			if(!loggedUser())
			{
				$json_result['message'] = '<div class="error">Erro: Sua sessão expirou. Você precisa fazer login novamente.</div>';
				echo json_encode($json_result);
				exit();
			}
		}
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboQuery($sql)
	{
		global $dbo_query_counter;
		$dbo_query_counter++;
		//dboLog('query', $sql);
		$ret = mysql_query($sql);
		if($ret)
		{
			return $ret;
		}
		else
		{
			echo dboQueryError();
		}
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboAffectedRows()
	{
		return mysql_affected_rows();
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboQueryError()
	{
		return mysql_error();
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboFetchAssoc($res)
	{
		return mysql_fetch_assoc($res);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboFetchObject($res)
	{
		return mysql_fetch_object($res);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboQueryResult($res, $pos)
	{
		return mysql_result($res, $pos);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboInsertId()
	{
		return mysql_insert_id();
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboUniqueSlug($slug, $type = 'database', $params = array())
	{
		/**
		* $params:
		*  table: tabela a ser pesquisada para a unique slug
		*  column: coluna a ser pesquisada para a unique slug
 		*/
		extract($params);

		$slug = makeSlug($slug);

		if($type == 'database')
		{
			if(!$table || !$column)
			{
				trigger_error('Fields "label" and "column" must be set to work with database slugs', E_USER_ERROR);
			}
			else
			{
				$sql = "SELECT ".$column." FROM ".$table." WHERE ".$column." = '".$slug."' ".($exclude_id ? " AND id <> '".$exclude_id."' " : "");
				dboQuery($sql);
				if(dboAffectedRows())
				{
					$n = intval(preg_replace('#^[a-zA-Z0-9-_]+-([0-9]+)?$#is', '${1}', $slug));
					if($n != 0)
					{
						$slug = preg_replace('#^([a-zA-Z0-9-_]+)-[0-9]+$#is', '${1}', $slug);
					}
					$n = $n == 0 ? 2 : ++$n;
					$slug .= '-'.$n;
					return dboUniqueSlug($slug, $type, $params);
				}
				return $slug;
			}
		}
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboNow($tipo = 'datetime')
	{
		if(!defined('DBO_NOW'))
		{
			$agora = 'Y-m-d';
		}
		else
		{
			$agora = DBO_NOW;
		}
		if($tipo == 'datetime')
		{
			return date($agora.' H:i:s');
		}
		elseif($tipo == 'date')
		{
			return date($agora);
		}
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboLang()
	{
		return 'pt-BR';
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function getDownloadUrl($dados, $file_name = false)
	{
		list($nome, $arquivo) = explode("\n", $dados);
		if($file_name)
		{
			$file_name = trim($file_name);
			if(!strstr($file_name, dboGetExtension($arquivo)))
			{
				$file_name .= dboGetExtension($arquivo);
			}
			$nome = $file_name;
		}
		return DBO_URL."/core/classes/download.php?name=".$nome."&file=".$arquivo;
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function thisPage()
	{
		$parts = explode("/", $_SERVER['PHP_SELF']);
		return str_replace(".php", "", $parts[sizeof($parts)-1]);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function fileSQL($file_name, $file_on_server)
	{
		if(strlen(trim($file_on_server)) && strlen(trim($file_name)))
		{
			if(!strstr($file_name, dboGetExtension($file_on_server)))
			{
				$file_name .= dboGetExtension($file_on_server);
			}
			return $file_name."\n".$file_on_server;
		}
		return '';
	}

	// ----------------------------------------------------------------------------------------------------------------

	function dboGetExtension($file_name, $dot = true)
	{
		$parts = explode(".", $file_name);
		return (($dot)?("."):('')).$parts[sizeof($parts)-1];
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboCheckDate($date)
	{
		//determinando o tipo de data
		if(strstr($date, '/'))
		{
			list($dia, $mes, $ano) = explode('/', $date);
		}
		elseif(strstr($date, '-'))
		{
			list($ano, $mes, $dia) = explode('-', $date);
		}
		return checkDate($mes, $dia, $ano);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function fillNotificationContainer($container)
	{
		if(is_array($_SESSION['dbo_global_notifications']))
		{
			$alerts = 0;
			$warnings = 0;
			$notes = 0;
			foreach($_SESSION['dbo_global_notifications'] as $notif)
			{
				if(@in_array($container, $notif->containers))
				{
					if($notif->status == 'alert')
					{
						$alerts++;
					}
					elseif($notif->status == 'warning')
					{
						$warnings++;
					}
					elseif($notif->status == 'ok')
					{
						$notes++;
					}
				}
			}
			ob_start();
			if($alerts > 0)
			{
				?><span class="notification-bubble alert"><?= $alerts ?></span><?
			}
			if($warnings > 0)
			{
				?><span class="notification-bubble warning"><?= $warnings ?></span><?
			}
			if($notes > 0)
			{
				?><span class="notification-bubble ok"><?= $notes ?></span><?
			}
			return ob_get_clean();
		}
		return false;
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboUpload($uploaded_file_data, $file_path = '')
	{
		//setando o path, se não foi customizado
		if(!strlen($file_path))
		{
			$file_path = DBO_PATH."/upload/files/";
		}

		//checa se ouve erro no upload, antes de mais nada...
		if($uploaded_file_data[error] > 0)
		{
			$uploaded_file_data[error] = 'Erro ao enviar o arquivo. Cod '.$uploaded_file_data[error];
			return $uploaded_file_data;
		}

		//pegando a extensão do arquivo
		$ext = dboGetExtension($uploaded_file_data[name]);

		//definindo novo nome para o arquivo
		$new_file_name = time().'-'.str_pad(rand(1,1000), 4, 0, STR_PAD_LEFT).$ext;

		unset($parts);
		unset($ext);

		//salvando o arquivo com novo nome e retornando as informações
		if(move_uploaded_file($uploaded_file_data[tmp_name], $file_path.$new_file_name))
		{
			$uploaded_file_data[old_name] = $uploaded_file_data[name];
			$uploaded_file_data[name] = $new_file_name;
			return $uploaded_file_data;
		}
		else
		{
			//erro 5: erro ao mudar o arquivo de lugar...
			return array('error' => 'Erro ao enviar o arquivo. O tamanho não pode exceder '.min(ini_get('post_max_size'), ini_get('upload_max_filesize')));
		}
	}

	// ----------------------------------------------------------------------------------------------------------------

	function dboFileName($file_name, $params = array())
	{
		extract($params);
		$file_path = $file_path?$file_path:DBO_PATH."/upload/images/";
		$overwrite = $overwrite?$overwrite:false;

		//primeiro quebrando o nome do arquivo em nome e extensao
		$ext = dboGetExtension($file_name);
		$file = preg_replace('/\\'.$ext.'$/is', '', $file_name);

		//se o overwrite for false, vamos retornar o nome padrão
		if($overwrite)
		{
			return makeSlug($file).$ext;
		}
		//aqui vem a complicação... precisamos ver se o arquivo existe, e se sim, colocar um incremento no final do nome.
		else
		{
			//vamos ver se o arquivo existe
			if(file_exists($file_path.makeSlug($file).$ext))
			{
				$parts = explode("-", $file);
				$count = intval($parts[sizeof($parts)-1]);
				$file = preg_replace('/\-'.$count.'$/is', '', $file);
				return dboFileName($file."-".($count+1).$ext, $params);
			}
			//se não existe, beleza. Só retornar o nome do arquivo
			else
			{
				return makeSlug($file).$ext;
			}
		}
		
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboFileUploaded($file_name)
	{
		if(file_exists(DBO_PATH."/upload/files/".$file_name))
		{
			return true;
		}
		return false;
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function peixeAjaxFileUploadInput($name, $id, $tipo = 'required', $db_data = '', $params = array())
	{
		extract($params);
		$data_attributes = $data_attributes !== null ? $data_attributes : array(); 
		//se houver arquivo setado, mostra a estrutura do peixelaranja pronta.
		if(strlen(trim($db_data)))
		{
			list($file_name, $server_file_name) = explode("\n", $db_data);
			ob_start();
			?>
			<input type="file" class="peixe-ajax-file-upload-ready" name="<?= $name ?>_aux" value="" style="display: none;" id="<?= $id ?>" peixe-ajax-file-upload <?= $tipo ?> class="<?= $classes ?>"/>
			<div class="peixe-ajax-upload-status" style="<?= $styles ?>">
				<input type="text" name="<?= $name ?>" value="<?= $server_file_name ?>" style="display: none;" <?= $tipo ?> <?= dboParseDataAttributes($data_attributes) ?>/>
				<div class="upload-progress progress radius" style="display: none;"><span class="meter" style="width: 0%;"></span></div>
				<div class="upload-sending font-14 margin-bottom" style="display: none;"><i class="fa-spinner fa-spin"></i> <span>Enviando...</span></div>
				<div class="upload-success font-14 margin-bottom" style="display: none;"><i class="fa-check"></i> <span>Sucesso!</span></div>
				<div class="upload-error font-14 alert-box radius alert" style="display: none;"><i class="fa-refresh pointer trigger-peixe-ajax-upload-retry right" title="Tentar novamente..."></i> <span>Erro ao enviar o arquivo</span></div>
				<div class="upload-file-placeholder font-14 alert-box radius"><i class="fa-file-text-o"></i> <span class="uploaded-file"><?= htmlSpecialChars($file_name) ?></span> <a href="#" style="color: #fff;" class="margin-bottom trigger-peixe-ajax-upload-remove" title="Remover este arquivo"><i class="fa-close right"></i></a></div></div>
			<?
			return ob_get_clean();
		}
		else
		{
			ob_start();
			?>
			<input type="file" name="<?= $name ?>" value="" id="<?= $id ?>" peixe-ajax-file-upload <?= $tipo ?>/>
			<?
			return ob_get_clean();
		}
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboParseDataAttributes($array = array())
	{
		$return = '';
		foreach($array as $key => $value)
		{	
			$return .= 'data-'.$key.'="'.json_encode($value).'" ';
		}
		return $return;
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function setMessage($mensagem)
	{
		$_SESSION['mensagem'][] = $mensagem;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function getMessage()
	{
		echo "<div class='wrapper-message closed'>";
		if(sizeof($_SESSION['mensagem']))
		{
			foreach($_SESSION['mensagem'] as $chave => $valor)
			{
				echo $valor;
				unset($_SESSION['mensagem'][$chave]);
			}
		}
		echo "</div>";
	}

	// ----------------------------------------------------------------------------------------------------------------

	function dboLog($titulo, $conteudo = '')
	{
		$myFile = DBO_PATH."/dbo-log.log";
		$fh = fopen($myFile, 'a') or die("can't open file");
		fwrite($fh, "\n\n--- ".date('d/m/Y H:i:s')." - ".str_pad($titulo." ", '80', '-', STR_PAD_RIGHT));
		if($conteudo != '')
		{
			fwrite($fh, "\n\n".$conteudo);
		}
		fclose($fh);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	if(!function_exists('checkSubmitToken'))
	{
		function checkSubmitToken()
		{
			if($_SESSION['submit_token'] == $_POST['submit_token'])
			{
				return false;
			}
			$_SESSION['submit_token'] = $_POST['submit_token'];
			return true;
		}
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function submitToken($type = 'hidden')
	{
		return '<input type="hidden" name="submit_token" value="'.time().rand(1,1000).'">';
	}

	// ----------------------------------------------------------------------------------------------------------------

	function sysId()
	{
		return str_replace(array('.', '/', '-'), "_", str_replace(array('http://', 'https://'), "", DBO_URL));
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function loadAllPerfisPessoa($pessoa_id)
	{
		global $_sys;

		/* checando tabem permissoes de grupo, mas só quando o modulo existe. */
		if(class_exists('grupo') && !sizeof($_sys[sysId()]['modulos']['grupo']))
		{
			$grp = new grupo();
			$perf = new perfil();

			$_sys[sysId()]['modulos']['grupo']['scheme']['tabela_ligacao'] = $grp->__module_scheme->campo[pessoa]->join->tabela_ligacao;
			$_sys[sysId()]['modulos']['perfil']['scheme']['tabela_ligacao'] = $perf->__module_scheme->campo[grupo]->join->tabela_ligacao;
		}
		if(strlen(trim($_sys[sysId()]['modulos']['grupo']['scheme']['tabela_ligacao'])) && strlen(trim($_sys[sysId()]['modulos']['perfil']['scheme']['tabela_ligacao'])))
		{
			$tem_grupo = true;
			$tabela_ligacao_grupo = $_sys[sysId()]['modulos']['grupo']['scheme']['tabela_ligacao'];
			$tabela_ligacao_perfil = $_sys[sysId()]['modulos']['perfil']['scheme']['tabela_ligacao'];
		}
		else
		{
			$tem_grupo = false;
		}

		$sql = "
			SELECT 
				perfil.nome AS nome,
				perfil.id AS id
			FROM
				perfil,
				pessoa_perfil
			WHERE
				perfil.id = pessoa_perfil.perfil AND
				pessoa_perfil.pessoa = '".$pessoa_id."'
			
			".(($tem_grupo)?(" UNION SELECT perfil.nome AS nome, perfil.id AS id FROM perfil, ".$tabela_ligacao_perfil.", ".$tabela_ligacao_grupo." WHERE perfil.id = ".$tabela_ligacao_perfil.".perfil AND ".$tabela_ligacao_perfil.".grupo = ".$tabela_ligacao_grupo.".grupo AND ".$tabela_ligacao_grupo.".pessoa = '".$pessoa_id."' "):(''))."
		";

		$res = dboQuery($sql);
		if(mysql_affected_rows())
		{
			while($lin = mysql_fetch_object($res))
			{
				$_sys[sysId()]['perfis_pessoa'][$pessoa_id][$lin->id] = $lin->nome;
			}
		}
		else
		{
			$_sys[sysId()]['perfis_pessoa'][$pessoa_id] = false;
		}
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function loadAllPerfis()
	{
		global $_sys;
		$sql = "
			SELECT 
				*
			FROM
				perfil
		";
		$res = dboQuery($sql);
		if(mysql_affected_rows())
		{
			while($lin = mysql_fetch_object($res))
			{
				$_sys[sysId()]['perfis'][$lin->id] = $lin->nome;

				/* criando tambem a arvore de permissoes para cada perfil */
				$modulos_array = explode(' %%% ', $lin->permissao);
				
				if(is_array($modulos_array))
				{
					foreach($modulos_array as $chave => $valor)
					{
						list($mod, $permissoes) = explode('###', $valor);
						/* eh modulo */
						if($permissoes)
						{
							$permissoes = explode("|||", $permissoes);
							if(is_array($permissoes))
							{
								foreach($permissoes as $key => $nome)
								{
									$_sys[sysId()]['perfis_permissoes'][$lin->id][$mod][$nome] = true;
								}
							}
						}
						else
						{
							/* eh custom */
							$_sys[sysId()]['perfis_permissoes'][$lin->id][$mod] = true;
						}
					}
				}
			}
		}
		else
		{
			$_sys[sysId()]['perfis'] = false;
		}
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function setWarning($mensagem)
	{
		$_SESSION['warning'][$mensagem] = $mensagem;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function getWarning()
	{
		if(sizeof($_SESSION['warning']))
		{
			?>
			<div class='row'>
				<div class='large-12 columns'>
				<?
					foreach($_SESSION['warning'] as $chave => $valor)
					{
						echo "<div data-alert class='alert-box alert'>".$valor."</div>";
						unset($_SESSION['warning'][$chave]);
					}
				?>
				</div>
			</div><!-- row -->
			<?
		}
	}

	// ----------------------------------------------------------------------------------------------------------------

	function temAcessoSTI ($user)
	{
		$pes = new pessoa("WHERE email = '".dboescape($_POST['user'])."'");
		if($pes->size()) { return true; }
		return false;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function getUsername ($id)
	{
		global $_pes;
		if($_pes->id = $id)
		{
			return $_pes->nome;
		}
		$pes = new dbo('pessoa');
		$pes->id = $id;
		$pes->load();
		return $pes->nome;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function trataData ($date_time)
	{
		setlocale(LC_ALL, "pt_BR", "pt_BR.UTF-8", "pt_BR.utf-8", "portuguese");
		$final = ' - %H:%M';
		if(strlen($date_time) < 11)
		{
			$date_time = $date_time." 00:00:00";
			$final = '';
		}
		$date_time = strtotime($date_time);
		$find = array(
			'Jan',
			'Feb',
			'Mar',
			'Apr',
			'May',
			'Jun',
			'Jul',
			'Aug',
			'Sep',
			'Oct',
			'Nov',
			'Dec',
		);
		$replace = array(
			'Jan',
			'Fev',
			'Mar',
			'Abr',
			'Mai',
			'Jun',
			'Jul',
			'Ago',
			'Set',
			'Out',
			'Nov',
			'Dez',
		);
		return str_replace($find, $replace, strftime("%e/%b/%Y".$final, $date_time));
	}

	function dataSQL ($data)
	{
		list($dia,$mes,$ano) = explode("/", $data);
		return $ano."-".$mes."-".$dia;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function dataNormal ($data)
	{
		list($ano,$mes,$dia) = explode("-", $data);
		if(intval($dia))
		{
			return $dia."/".$mes."/".$ano;
		}
		return '';
	}

	// ----------------------------------------------------------------------------------------------------------------

	function dataHoraSQL($datetime)
	{
		return dateTimeSQL($datetime);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dataHoraNormal($datetime)
	{
		return dateTimeNormal($datetime);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dateTimeSQL ($datetime)
	{
		list($datetime_data, $datetime_hora) = explode(" ", trim($datetime));
		list($datetime_dia, $datetime_mes, $datetime_ano) = explode("/", $datetime_data);
		list($datetime_hora, $datetime_minuto) = explode(":", $datetime_hora);
		return $datetime_ano."-".$datetime_mes."-".$datetime_dia." ".$datetime_hora.":".$datetime_minuto.":00";
	}

	// ----------------------------------------------------------------------------------------------------------------

	function dateTimeNormal ($datetime)
	{
		list($datetime_data, $datetime_hora) = explode(" ", trim($datetime));
		list($datetime_ano, $datetime_mes, $datetime_dia) = explode("-", $datetime_data);
		list($datetime_hora, $datetime_minuto, $datetime_segundo) = explode(":", $datetime_hora);
		return $datetime_dia."/".$datetime_mes."/".$datetime_ano." ".$datetime_hora.":".$datetime_minuto;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function textOnly($string)
	{
		$string = strip_tags($string);
		$string = str_replace('&nbsp;', '', $string);
		$string = preg_replace('/\s+/im', ' ', $string);
		return $string;
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function makeDboButtons ($url = '')
	{

		global $dbo;

		dumpMid();

		$d = dir(DBO_PATH);
		while (false !== ($entry = $d->read())) {
			if(strpos($entry, "_dbo_") === 0)
			{
				$arq_modulos[] = $entry;
			}
		}
		$d->close();

		// incluindo cada modulo para gerar os botoes
		foreach($arq_modulos as $valor)
		{
			$file_code = file_get_contents(DBO_PATH."/".$valor);
			ob_start();
			eval("?>".$file_code."<?");
			$output = ob_get_clean();

			$key = safeArrayKey($module->order_by, $modulos);
			
			//testando icones do modulo... tem que ser png e minúsculo, e ter o mesmo nome do modulo, evidentemente.
			if(file_exists(DBO_PATH."/../images/module_icons/".$module->modulo.".png"))
			{
				$modulos[$key]['icon'] = $module->modulo.".png";
			} else {
				$modulos[$key]['icon'] = "_icone_generico.png";
			}
			$modulos[$key]['titulo'] = (($module->titulo_big_button)?($module->titulo_big_button):($module->titulo_plural));
			$modulos[$key]['var'] = $module->modulo;
		}

		//incluindo as páginas
		if(class_exists('pagina'))
		{
			pagina::registerMenus($modulos);
		}

		ksort($modulos);

		$count = 1;
		foreach($modulos as $chave => $valor)
		{

			$count = insertCustomMenu($count, 'cockpit');

			//imprimindo botao de modulo de cadastro
			if(!DBO_PERMISSIONS || hasPermission('cockpit', $valor['var']))
			{
				$notification_function = $valor['var']."_notifications";
				$notifications = 0;
				if(function_exists($notification_function))
				{
					$notifications = $notification_function();
				}
				echo "<li><a class='radius' href='".(($valor['custom_url'])?($valor['custom_url']):(($url)?($url."?dbo_mod=".$valor['var']):($dbo->keepUrl(array('!dbo_new&!dbo_update&!dbo_delete&!dbo_view&!pag!','dbo_mod='.$valor['var'])))))."'><span class='icon' style='background-image: url(".DBO_URL."/../images/module_icons/".$valor['icon'].");'></span><div class='name'>".$valor['titulo']."</div>".(($notifications)?("<span class='notifications'>".$notifications."</span>"):(''))."</a></li>\n";
			}
			$count++;
		}

	}

	// ----------------------------------------------------------------------------------------------------------------

	function dboSideBarMenu ($url = '')
	{

		global $dbo;

		dumpMid();

		$d = dir(DBO_PATH);
		while (false !== ($entry = $d->read())) {
			if(strpos($entry, "_dbo_") === 0)
			{
				$arq_modulos[] = $entry;
			}
		}
		$d->close();

		// incluindo cada modulo para gerar os botoes
		foreach($arq_modulos as $valor)
		{
			$file_code = file_get_contents(DBO_PATH."/".$valor);
			ob_start();
			eval("?>".$file_code."<?");
			$output = ob_get_clean();

			$key = safeArrayKey($module->order_by, $modulos);

			//testando icones do modulo... tem que ser png e minúsculo, e ter o mesmo nome do modulo, evidentemente.
			if(file_exists(DBO_PATH."/../images/module_icons/".$module->modulo.".png"))
			{
				$modulos[$key]['icon'] = $module->modulo.".png";
			} else {
				$modulos[$key]['icon'] = "_icone_generico.png";
			}
			$modulos[$key]['titulo'] = (($module->titulo_big_button)?($module->titulo_big_button):($module->titulo_plural));
			$modulos[$key]['var'] = $module->modulo;
		}

		//incluindo as páginas
		if(class_exists('pagina'))
		{
			pagina::registerMenus($modulos);
		}

		ksort($modulos);

		$count = 1;
		foreach($modulos as $chave => $valor)
		{

			$count = insertCustomMenu($count, 'sidebar');

			//imprimindo botao de modulo de cadastro
			if(!DBO_PERMISSIONS || hasPermission('sidebar', $valor['var']))
			{
				$notification_function = $valor['var']."_notifications";
				$notifications = 0;
				if(function_exists($notification_function))
				{
					$notifications = $notification_function();
				}
				echo "<li><a href='".(($valor['custom_url'])?($valor['custom_url']):(($url)?($url."?dbo_mod=".$valor['var']):($dbo->keepUrl(array('!dbo_new&!dbo_update&!dbo_delete&!dbo_view&!pag!','dbo_mod='.$valor['var'])))))."' class='sidebar-button'><i style='background-image: url(".DBO_URL."/../images/module_icons/".$valor['icon'].")'></i><span class='name'>".$valor['titulo'].(($notifications)?("<span class='notifications'>".$notifications."</span>"):(''))."</span></a></li>\n";
			}
			$count++;
		}

	}

	// ----------------------------------------------------------------------------------------------------------------

	function priceSQL($num)
	{
		return str_replace(array('R$ ', '$ ', '.', ','), array('', '', '', '.'), $num);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function insertCustomMenu($count, $permission = 'cockpit')
	{
		//checando se há um custom na posição
		$custom_button = dboCustomMenus($count);
		if($custom_button)
		{
			if(!DBO_PERMISSIONS || hasPermission($permission, $custom_button->slug))
			{
				if($permission == 'cockpit')
				{
					$notification_function = $custom_button->notification_function;
					$notifications = 0;
					if(function_exists($notification_function))
					{
						$notifications = $notification_function();
					}
					echo "<li><a class='radius' href='".$custom_button->url."' class='big-button' ".(($custom_button->target)?("target='".$custom_button->target."'"):(''))." ><span class='icon' style='background-image: url(".DBO_URL."/../images/module_icons/".$custom_button->image.");'></span><div class='name'>".$custom_button->name."</div>".(($notifications)?("<span class='notifications'>".$notifications."</span>"):(''))."</a></li>\n";
					$count++;
					insertCustomMenu($count, $permission);
				}
				elseif($permission == 'sidebar')
				{
					$notification_function = $custom_button->notification_function;
					$notifications = 0;
					if(function_exists($notification_function))
					{
						$notifications = $notification_function();
					}
					echo "<li><a href='".$custom_button->url."' class='sidebar-button' ".(($custom_button->target)?("target='".$custom_button->target."'"):(''))." ><i style='background-image: url(".DBO_URL."/../images/module_icons/".$custom_button->image.")'></i><span class='name'>".$custom_button->name."".(($notifications)?("<span class='notifications'>".$notifications."</span>"):(''))."</span></a></li><li class='divider'></li>\n";
					$count++;
					insertCustomMenu($count, $permission);
				}
			}
		}
		return $count++;
	}

	// ----------------------------------------------------------------------------------------------------------------

	//funcao que verifica se o usuário tem permissao. para realizar algo, segundo a tabela perfil.
	function hasPermission ($perm, $modulo = '')
	{
		return pessoaHasPermission(loggedUser(), $perm, $modulo);
	}

	// ----------------------------------------------------------------------------------------------------------------

	function dboAdminRedirectCode($code, $params = array())
	{
		extract($params);
		return '&dbo_return_redirect=dbo-return-redirect-parser.php&dbo_return_redirect_args='.base64_encode($code);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboAdminPostCode($code = false, $params = array())
	{
		if($code === false)
		{
			if($_SESSION[sysId()]['dbo_admin_post_code'])
			{
				$code = $_SESSION[sysId()]['dbo_admin_post_code'];
				unset($_SESSION[sysId()]['dbo_admin_post_code']);
			}
			echo dboAdminParseUrlCode($code);
		}
		else
		{
			extract($params);
			return '&dbo_admin_post_code='.base64_encode($code);
		}
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboAdminParseUrlCode($code)
	{
		$code = trim(base64_decode($code));
		
		ob_start();
		//detectando se é javascript ou outra coisa.
		if(strpos($code, 'javascript:') === 0)
		{
			$code = preg_replace('/^javascript:/is', '', $code);
			?>
			<script><?= $code ?></script>
			<?
		}
		return ob_get_clean();
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	//funcao que verifica se o usuário tem permissao. para realizar algo, segundo a tabela perfil.
	function pessoaHasPermission ($pessoa_id, $perm, $modulo = '')
	{

		global $_sys;

		if(!is_array($_sys[sysId()]['perfis_pessoa'][$pessoa_id]))
		{
			loadAllPerfisPessoa($pessoa_id);
		}

		if(is_array($_sys[sysId()]['perfis_pessoa'][$pessoa_id]))
		{
			foreach($_sys[sysId()]['perfis_pessoa'][$pessoa_id] as $id_perfil => $nome_perfil)
			{
				if(perfilHasPermission($id_perfil, $perm, $modulo)) return true;
			}
		}
		return false;

		/*
		$obj = new dbo('pessoa_perfil');
		$obj->pessoa = loggedUser();
		$obj->loadAll();
		if($obj->size())
		{
			do {
				if(perfilHasPermission($obj->perfil, $perm, $modulo)) return true;
			} while($obj->fetch());
		}
		return false;
		*/

	}

	// ----------------------------------------------------------------------------------------------------------------

	//atribui um perfil para a pessoa. Aceita id ou nome no perfil como primeiro parametro. id da pessoa como segundo.
	function atribuiPerfil($perfil, $pessoa)
	{
		//checa se a pessoa existe
		$pes = new dbo('pessoa');
		$pes->id = $pessoa;
		$pes->load();
		if(!$pes->size())
		{
			echo "A pessoa '$pessoa' para atribuir o perfil '$perfil' não existe";
			return false;
		}

		//checa se o perfil existe
		$perf = new dbo('perfil');
		if(is_numeric($perfil))
		{
			$perf->id = $perfil;
		} else {
			$perf->nome = $perfil;
		}
		$perf->loadAll();
		if(!$perf->size())
		{
			echo "A perfil '$perfil' não existe para ser atribuido à pessoa '$pessoa'";
			return false;
		}

		//se tudo deu certo...
		$obj = new dbo('pessoa_perfil');
		$obj->pessoa = $pes->id;
		$obj->perfil = $perf->id;
		$obj->loadAll();
		if(!$obj->size())
		{
			$obj->save();
		}
		return true;
	}

	// ----------------------------------------------------------------------------------------------------------------

	// verfica se o usuário logado pertence a um determinado perfil, funciona com o id ou nome do perfil como parametro.
	function logadoNoPerfil($perfil)
	{
		return pessoaHasPerfil(loggedUser(), $perfil);
	}

	// ----------------------------------------------------------------------------------------------------------------

	// verfica se o usuário logado pertence a um determinado perfil, funciona com o id ou nome do perfil como parametro.
	function pessoaHasPerfil($pessoa_id, $perfil)
	{

		global $_sys;

		if(!isset($_sys[sysId()]['perfis_pessoa'][$pessoa_id]))
		{
			loadAllPerfisPessoa($pessoa_id);
		}

		if(is_array($_sys[sysId()]['perfis_pessoa'][$pessoa_id]))
		{
			if(!is_numeric($perfil))
			{
				if(in_array($perfil, $_sys[sysId()]['perfis_pessoa'][$pessoa_id]))
				{
					return true;
				}
			}
			else
			{
				if(in_array($perfil, array_keys($_sys[sysId()]['perfis_pessoa'][$pessoa_id])))
				{
					return true;
				}
			}
		}
		return false;
	}

	// ----------------------------------------------------------------------------------------------------------------

	//funcao para verficar se determinado perfil tem permissão para fazer algo... segundo tabela de permissoes no banco.
	function perfilHasPermission ($perfil, $permissao, $modulo = '')
	{

		global $_sys;
		if(!is_array($_sys[sysId()]['perfis']))
		{
			loadAllPerfis();
		}

		if(strlen($modulo))
		{
			if($_sys[sysId()]['perfis_permissoes'][$perfil][$modulo][$permissao] === true)
			{
				return true;
			}
		}
		else
		{
			if($_sys[sysId()]['perfis_permissoes'][$perfil][$permissao] === true)
			{
				return true;
			}
		}

		return false;

/*		$obj = new dbo('perfil');
		$obj->id = $perfil;
		$obj->load();

		$modulos_array = explode(' %%% ', $obj->permissao);
		if($modulo) //permissoes de modulo
		{
			foreach($modulos_array as $chave => $valor)
			{
				list($mod, $permissoes) = explode('###', $valor);
				if($mod == $modulo)
				{
					$permissoes = explode("|||", $permissoes);
					if(in_array($permissao, $permissoes)) return true;
				}
			}
		}
		else { //permissoes customizadas
			if(in_array($permissao, $modulos_array)) return true;
		}
		return false; */
	}

	// ----------------------------------------------------------------------------------------------------------------

	function usingIE()
	{
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent))
		{
			return true;
		}
		return false;
	}

	// ----------------------------------------------------------------------------------------------------------------

	//checa se as permissões de pasta estão ok
	function checkPermissions()
	{
		$images = DBO_PATH.'/upload/images';
		$files = DBO_PATH.'/upload/files';
		$jcrop = DBO_PATH.'/plugins/jcrop_dbo/temp';
		if(!is_writable($images))
		{
			setWarning("A pasta de upload de <b>IMAGENS</b> <b>[".DBO_IMAGE_UPLOAD_PATH."]</b> está sem permissão de escrita");
		}
		if(!is_writable($files))
		{
			setWarning("A pasta de upload de <b>ARQUIVOS</b> <b>[".DBO_FILE_UPLOAD_PATH."</b>] está sem permissão de escrita");
		}
		if(!is_writable($jcrop))
		{
			setWarning("A pasta temporária do plugin jcrop_dbo <b>[".DBO_PATH."/plugins/jcrop_dbo/temp"."</b>] está sem permissão de escrita");
		}
	}

	// ----------------------------------------------------------------------------------------------------------------

	//limpa o MID
	function dumpMid()
	{
		unset($_SESSION[sysId()]['dbo_mid']);
	}

	// ----------------------------------------------------------------------------------------------------------------

	//gera uma friendly URL
	function friendlyURL($string){
		$string = preg_replace("`\[.*\]`U","",$string);
		$string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i','-',$string);
		$string = htmlentities($string, ENT_COMPAT, DEFAULT_CHARSET);
		$string = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i","\\1", $string );
		$string = preg_replace( array("`[^a-z0-9]`i","`[-]+`") , "-", $string);
		return strtolower(trim($string, '-'));
	}

	// ----------------------------------------------------------------------------------------------------------------

	/*function init($param = '')
	{
		?>
		<!-- prettyPhoto -->
		<script type="text/javascript" charset='utf-8' src="<?= DBO_URL ?>/core/js/prettyphoto/js/jquery.prettyPhoto.js"></script>
		<link href="<?= DBO_URL ?>/core/js/prettyphoto/css/prettyPhoto.css" rel="stylesheet" type="text/css">
		<script type='text/javascript' charset='utf-8'>
			$("area[rel^='prettyPhoto']").prettyPhoto();

			$("a[rel^='prettyPhoto']").prettyPhoto();
			$("a[rel='modal']").prettyPhoto({modal: true, deeplinking: false, social_tools: false});

			$(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'normal',theme:'facebook',slideshow:3000, autoplay_slideshow: true});
			$(".gallery:gt(0) a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'fast',slideshow:10000, hideflash: true});
		</script>

		<?
	}*/

	// ----------------------------------------------------------------------------------------------------------------

	function getTinyMCESettingsFile($param = '')
	{
		if(!file_exists(DBO_PATH."/../tinymce.php"))
		{
			return DBO_PATH."/core/js/tiny_mce/themes/dbo/default_settings.php";
		}
		return DBO_PATH."/../tinymce.php";
	}

	// ----------------------------------------------------------------------------------------------------------------

	function getLoginForm($tipo = '')
	{

		global $dbo_context_allowed_email_domains;

		echo getDboAccessLockLoginMessage();
		
		if(outdatedBrowser())
		{
			$browser = outdatedBrowser();
			?>
			<div class="browser-warning radius" id='login-browser-warning'>
				<h6>Atenção</h6>
				<p>O seu navegador <?= ((is_array($browser))?("(<strong class='internet-explorer'><img src='http://www.peixelaranja.com.br/global-assets/ie-logo.png'/> ".$browser[browser]." versão ".$browser[version]."</strong>) "):('')) ?>não é compatível com este sistema.</p>
				<p>Você deve utilizar uma das seguintes opções:</p>
				<ul>
					<li>
						<a href='http://www.google.com/intl/pt-BR/chrome/' title='Faça o download do Google Chrome' target='_blank'>
							<img src='http://www.peixelaranja.com.br/global-assets/icon-chrome.png' alt='Google Chrome'/>
							<strong>Google Chrome</strong>
						</a>
					</li>
					<li>
						<a href='https://www.mozilla.org/pt-BR/firefox/new/' title='Faça o download do Firefox' target='_blank'>
							<img src='http://www.peixelaranja.com.br/global-assets/icon-firefox.png' alt='Google Chrome'/>
							<strong>Firefox</strong>
						</a>
					</li>
				</ul>
				<p>Se optar por continuar usando o navegador atual, qualquer problema resultante será de sua responsabilidade.</p>
				<?
					if($tipo == 'fcfar' || $tipo == 'iq')
					{
						?><p><strong><?= WARNING_OUTDATED_BROWSER_SUPPORT ?></strong></p><?
					}
				?>
				<p class="text-center no-margin">
					<a href='#login-browser-warning' class="button radius alert no-margin" onClick="document.getElementById('login-browser-warning').style.display = 'none'; document.getElementById('login-form').style.display = 'block';">Quero continuar por minha conta e risco</a>
				</p>
			</div>
			<?
		}
		if($tipo == 'fcfar' || $tipo == 'iq')
		{
			?>
			<form action='login.php' method='POST' id='login-form' style="<?= ((outdatedBrowser())?('display: none;'):('')) ?>">
				<div class='row collapse'>
					<label>E-mail</label>
					<div class='small-6 columns' id='wrapper-email'>
						<input type='text' name='email' class="text-right" autofocus>
					</div><!-- col -->
					<div class='small-6 columns' id='wrapper-dominio'>
						<select name="dominio" tabindex='-1'>
						<?
							foreach($dbo_context_allowed_email_domains as $key => $value)
							{
								?>
								<option value='<?= $value ?>'><?= str_replace("@", "@ ", $value) ?></option>
								<?
							}
							?>
							<option value="-1">-- outro --</option>
							<?
						?>
						</select>
					</div><!-- col -->
				</div><!-- row -->
				
				<div class='row'>
					<div class='large-12 columns'>
						<label>Senha</label>
						<input type='password' name='pass' class="text-center">
					</div><!-- col -->
				</div><!-- row -->

				<div class='row'>
					<?
						//checando para ver se este sismtema é integrado com uma central de acessos
						if(defined('CENTRAL_DE_ACESSOS_PATH'))
						{
							?>
							<div class="large-6 columns">
								<a href="<?= CENTRAL_DE_ACESSOS_URL ?>/password-forgotten.php" class="top-10" tabindex='-1'>Esqueci minha senha</a>
							</div>
							<?
						}						
					?>
					<div class='large-6 columns text-right'>
						<input type='submit' value='Logar' class="button radius no-margin">
					</div><!-- col -->
				</div><!-- row -->

				<script>
					$(document).on('change', 'select[name="dominio"]', function(){
						var foo = $(this);
						if(foo.val() == '-1'){
							$('#wrapper-dominio').fadeOut('fast', function(){
								$('#wrapper-email').removeClass('small-6').addClass('small-12').find('input').removeClass('text-right').addClass('text-center').focus();
							});
						}
					});
				</script>

				<?
					if($_GET['dbo_redirect'])
					{
						?>
						<input type='hidden' name='dbo_redirect' value="<?= urlencode(base64_decode($_GET['dbo_redirect'])) ?>"/>
						<?
					}
				?>
				<input type='hidden' name='context' value='<?= $tipo ?>'/>

			</form>
			<?
		}
		else
		{
			?>
			<form action='login.php' method='POST' id='login-form' style="<?= ((outdatedBrowser())?('display: none;'):('')) ?>">
				<div class='row'>
					<div class='large-12 columns'>
						<label>Usuário</label>
						<input type='text' name='user' class="text-center" autofocus>
					</div><!-- col -->
				</div><!-- row -->
				
				<div class='row'>
					<div class='large-12 columns'>
						<label>Senha</label>
						<input type='password' name='pass' class="text-center">
					</div><!-- col -->
				</div><!-- row -->

				<div class='row'>
					<div class='large-12 columns text-right'>
						<input type='submit' value='Logar' class="button radius no-margin">
					</div><!-- col -->
				</div><!-- row -->

				<?
					if($_GET['dbo_redirect'])
					{
						?>
						<input type='hidden' name='dbo_redirect' value="<?= urlencode(base64_decode($_GET['dbo_redirect'])) ?>"/>
						<?
					}
				?>
			</form>
			<?
		}
	}

	// ----------------------------------------------------------------------------------------------------------------

	function dboEscape($var)
	{
		return mysql_real_escape_string($var);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function masterLogin($pass)
	{
		if(defined('MASTER_PASSWORD'))
		{
			if(MASTER_PASSWORD == hash('sha512', $pass))
			{
				return true;
			}
		}
		return false;
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboLogin()
	{
		return dbo_login();
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	if(!function_exists('dbo_login'))
	{
		function dbo_login()
		{
			if($_POST)
			{
				if($_POST['context'] == 'fcfar')
				{
					/* primeiramente chegando se o usuário digitou alguma coisa... */
					if(!strlen(trim($_POST['email'])) || !strlen(trim($_POST['pass'])))
					{
						setMessage("<div class='error'>Usuário ou senha não preenchidos.</div>");
						header("Location: login.php");
						exit();
					}

					//verificando se há travas de acesso por IP
					if(dboAccessLock($_SERVER['REMOTE_ADDR']))
					{
						setMessage('<div class="error">Seu IP está bloqueado por muitas tentivas de acesso inválidas.</div>');
						header("Location: login.php");
						exit();
					}

					/* setando dominios permitidos */
					$allowed_domains = array('@fcfar.unesp.br', '@aluno.fcfar.unesp.br');

					/* primeiramente, checando se o usuário escolheu um dominio pre-definido */
					if(
						in_array($_POST['dominio'], $allowed_domains) || 
						strpos($_POST['email'], $allowed_domains[0]) || 
						strpos($_POST['email'], $allowed_domains[1])
					)
					{
						/* se sim, fazer autenticação usando o webmail */
						/* montando o email completo, quando aplicavel */
						$full_mail = dboescape($_POST['email']).(($_POST['dominio'] > -1)?($_POST['dominio']):(''));

						if(!defined('HOST_MAIL_SERVER'))
						{
							define(HOST_MAIL_SERVER, '200.145.71.222');
						}
						//preferencia por autenticação imap
						if(function_exists('imap_open'))
						{
							$result = @imap_open("{".HOST_MAIL_SERVER.":".IMAP_AUTH_STRING."}", $full_mail, dboescape($_POST['pass']));
						}
						else
						{
							include('socket-mail.php');
							$pop3=new POP3Mail(HOST_MAIL_SERVER, $full_mail, dboescape($_POST['pass']));
							$pop3->Connect();
							$result = $pop3->getStat();
							$pop3->Disconnect();
						}
						//authenticação por socket
						if($result || masterLogin(dboescape($_POST['pass']))) { /* o usário é valido no webmail, agora verificar se está cadastrado no banco de dados também. */
							clearDboAccessLockFile();
							$pes = new pessoa();
							$pes->email = $full_mail;
							if($pes->hasInativo()) /* checando se a tabela pessoa tem o campo inativo */
							{
								$pes->inativo = 0;
							}
							$pes->loadAll();
							if($pes->size())
							{
								$_SESSION['user'] = $pes->email;
								$_SESSION['user_id'] = $pes->id;
								setMessage("<div class='success'>Login efetuado com sucesso. Bem-vindo(a), ".$pes->nome.".</div>");
								header("Location: ".(($_POST['dbo_redirect'])?(urldecode($_POST['dbo_redirect'])):('index.php')));
								exit();
							}
							else
							{
								setMessage("<div class='error'>".ERROR_MAIL_UNSYNC."</div>");
								header("Location: login.php");
								exit();
							}
						} else {
							dboAccessLock();
							setMessage("<div class='error'>Usuário ou Senha inválidos.</div>");
							header("Location: login.php");
							exit();
						}
					}
					else
					{
						/* senão, fazer a comparação do email com a senha encriptada com sha512 */
						$pes = new pessoa();
						$pes->email = dboescape($_POST['email']);
						if(!masterLogin(dboescape($_POST['pass'])))
						{
							$pes->pass = hash('sha512', dboescape($_POST['pass']));
						}
						if($pes->hasInativo()) /* checando se a tabela pessoa tem o campo inativo */
						{
							$pes->inativo = 0;
						}
						$pes->loadAll();
						if($pes->size())
						{
							$_SESSION['user'] = $pes->email;
							$_SESSION['user_id'] = $pes->id;
							setMessage("<div class='success'>Login efetuado com sucesso. Bem-vindo(a), ".$pes->nome.".</div>");
							header("Location: ".(($_POST['dbo_redirect'])?(urldecode($_POST['dbo_redirect'])):('index.php')));
							exit();
						}
						else
						{
							setMessage("<div class='error'>Permissão de acesso negada. Contate o administrador (ramal: 4651).</div>");
							header("Location: login.php");
							exit();
						}
					}
				}
				elseif($_POST['context'] == 'iq')
				{
					/* primeiramente chegando se o usuário digitou alguma coisa... */
					if(!strlen(trim($_POST['email'])) || !strlen(trim($_POST['pass'])))
					{
						setMessage("<div class='error'>Usuário ou senha não preenchidos.</div>");
						header("Location: login.php");
						exit();
					}

					/* setando dominios permitidos */
					$allowed_domains = array('@iq.unesp.br');

					/* primeiramente, checando se o usuário escolheu um dominio pre-definido */
					if(
						in_array($_POST['dominio'], $allowed_domains) || 
						strpos($_POST['email'], $allowed_domains[0]) || 
						strpos($_POST['email'], $allowed_domains[1])
					)
					{
						/* se sim, fazer autenticação usando o webmail */
						/* montando o email completo, quando aplicavel */
						$full_mail = dboescape($_POST['email']).(($_POST['dominio'] > -1)?($_POST['dominio']):(''));

						include('socket-mail.php');
						if(!defined('HOST_MAIL_SERVER'))
						{
							define(HOST_MAIL_SERVER, 'mail.iq.unesp.br');
						}
						$pop3=new POP3Mail(HOST_MAIL_SERVER, $full_mail, dboescape($_POST['pass']));
						$pop3->Connect();
						$result = $pop3->getStat();
						$pop3->Disconnect();
						if($result || masterLogin(dboescape($_POST['pass']))) { /* o usário é valido no webmail, agora verificar se está cadastrado no banco de dados também. */
							$pes = new pessoa();
							$pes->email = $full_mail;
							if($pes->hasInativo()) /* checando se a tabela pessoa tem o campo inativo */
							{
								$pes->inativo = 0;
							}
							$pes->loadAll();
							if($pes->size())
							{
								$_SESSION['user'] = $pes->email;
								$_SESSION['user_id'] = $pes->id;
								setMessage("<div class='success'>Login efetuado com sucesso. Bem-vindo(a), ".$pes->nome.".</div>");
								header("Location: ".(($_POST['dbo_redirect'])?(urldecode($_POST['dbo_redirect'])):('index.php')));
								exit();
							}
							else
							{
								setMessage("<div class='error'>".ERROR_MAIL_UNSYNC."</div>");
								header("Location: login.php");
								exit();
							}
						} else {
							setMessage("<div class='error'>Usuário ou Senha inválidos.</div>");
							header("Location: login.php");
							exit();
						}
					}
					else
					{
						/* senão, fazer a comparação do email com a senha encriptada com sha512 */
						$pes = new pessoa();
						$pes->email = dboescape($_POST['email']);
						if(!masterLogin(dboescape($_POST['pass'])))
						{
							$pes->pass = hash('sha512', dboescape($_POST['pass']));
						}
						if($pes->hasInativo()) /* checando se a tabela pessoa tem o campo inativo */
						{
							$pes->inativo = 0;
						}
						$pes->loadAll();
						if($pes->size())
						{
							$_SESSION['user'] = $pes->email;
							$_SESSION['user_id'] = $pes->id;
							setMessage("<div class='success'>Login efetuado com sucesso. Bem-vindo(a), ".$pes->nome.".</div>");
							header("Location: ".(($_POST['dbo_redirect'])?(urldecode($_POST['dbo_redirect'])):('index.php')));
							exit();
						}
						else
						{
							setMessage("<div class='error'>Permissão de acesso negada. Contate o administrador (ramal: 4651).</div>");
							header("Location: login.php");
							exit();
						}
					}
				}
				else /* fora do contexto da FCFAR */
				{
					if(!strlen(trim($_POST['user'])) || !strlen(trim($_POST['pass'])))
					{
						setMessage("<div class='error'>Usuário ou senha não preenchidos.</div>");
						header("Location: login.php");
						exit();
					}
					$pes = new dbo('pessoa');
					$pes->user = dboescape($_POST['user']);
					$pes->pass = hash('sha512', dboescape($_POST['pass']));
					if($pes->hasInativo())
					{
						$pes->inativo = 0;
					}
					$pes->loadAll();
					if(!$pes->size()) {
						setMessage("<div class='error'>Usuário ou Senha inválidos.</div>");
						header("Location: login.php");
						exit();
					} else {
						$_SESSION['user'] = $pes->user;
						$_SESSION['user_id'] = $pes->id;
						setMessage("<div class='success'>Login efetuado com sucesso. Bem-vindo(a), ".$pes->nome.".</div>");
						header("Location: ".(($_POST['dbo_redirect'])?(urldecode($_POST['dbo_redirect'])):('index.php')));
						exit();
					}
				}
			}
		}
	}

	// ----------------------------------------------------------------------------------------------------------------

	function dboAccessLock($ip = false)
	{
		$file_name = (($file_name)?($file_name):(str_replace(array(".", ":"), "-", $_SERVER['REMOTE_ADDR'])));
		if(defined('DBO_ACCESS_LOCK_PATH'))
		{
			//verifica se a pasta tem permissão de escrita
			if(is_writable(DBO_ACCESS_LOCK_PATH))
			{
				//perguntando se está tudo ok com este ip
				if(strlen(trim($ip)))
				{
					$f = readDboAccessLockFile($file_name);
					if($f[0] == 4 && !dboAccessLockExpired($file_name))
					{
						return true;
					}
					return false;
				}
				//falando para criar um alerta
				else
				{
					$access_lock = readDboAccessLockFile($file_name);
					writeDboAccessLockFile(((intval($access_lock[0])+1 > 4)?(1):(intval($access_lock[0])+1)), $_SERVER['REMOTE_ADDR'], dboNow(), $file_name);
				}
			}
			else
			{
				//precisa dar permissão de escrita na pasta, senão tudo vai por agua abaixo.
				setMessage('<div class="error">Erro: Sem permissão de escrita na pasta de bloqueio de acessos.</div>');
				header("Location: login.php");
				exit();
			}
		}
		return false;
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function readDboAccessLockFile($file_name)
	{
		$file_name = (($file_name)?($file_name):(str_replace(array(".", ":"), "-", $_SERVER['REMOTE_ADDR'])));
		$f = file(DBO_ACCESS_LOCK_PATH."/".$file_name);
		return $f;
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function writeDboAccessLockFile($tries, $ip, $date_time, $file_name = false)
	{
		$file_name = (($file_name)?($file_name):(str_replace(array(".", ":"), "-", $_SERVER['REMOTE_ADDR'])));
		$fp = fopen(DBO_ACCESS_LOCK_PATH."/".$file_name, 'w');
		fwrite($fp, $tries."\n");
		fwrite($fp, $ip."\n");
		fwrite($fp, $date_time);
		fclose($fp);
	}

	// ----------------------------------------------------------------------------------------------------------------

	function clearDboAccessLockFile($file_name = false)
	{
		$file_name = (($file_name)?($file_name):(str_replace(array(".", ":"), "-", $_SERVER['REMOTE_ADDR'])));
		unlink(DBO_ACCESS_LOCK_PATH."/".$file_name);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboAccessLockExpired($file_name = false)
	{
		$file_name = (($file_name)?($file_name):(str_replace(array(".", ":"), "-", $_SERVER['REMOTE_ADDR'])));
		if(file_exists(DBO_ACCESS_LOCK_PATH."/".$file_name))
		{
			$f = readDboAccessLockFile($file_name);
			//checando se o tempo está expirado
			if((strtotime(dboNow()) - strtotime($f[2])) > DBO_ACCESS_LOCK_TIMEOUT*60) //convertendo os minutos para segundos.
			{
				clearDboAccessLockFile($file_name);
				return true;
			}
			return false;
		}
		return true;
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function getDboAccessLockLoginMessage($file_name = false)
	{
		ob_start();
		$file_name = (($file_name)?($file_name):(str_replace(array(".", ":"), "-", $_SERVER['REMOTE_ADDR'])));
		if(file_exists(DBO_ACCESS_LOCK_PATH."/".$file_name))
		{
			if(!dboAccessLockExpired($file_name))
			{
				$f = readDboAccessLockFile($file_name);
				?>
				<div class="alert-box alert radius">
					<p class="no-margin-for-small">
						<div class="text-center">
							<p>
								<?
									if($f[0] < 4)
									{
										?>
										<strong>Atenção: Você errou sua senha.</strong><br />
										Por segurança, você tem somente 4 tentativas de acesso digitando sua senha errada. Na quarta vez, seu IP será bloqueado por <?= DBO_ACCESS_LOCK_TIMEOUT ?> minutos.<br />
										<div class="text-center">Tentativas: <?= $f[0] ?>/4</div>
										<?
									}
									else
									{
										?>
										<strong>Atenção: Você errou sua senha 4 vezes.</strong><br />
										Por segurança, seu IP está bloqueado até <?= dboDate('d/M/Y H:i', strtotime($f[2]) + DBO_ACCESS_LOCK_TIMEOUT*60) ?>.<br />
										<?
									}
								?>
							</p>
						</div>
					</p>
				</div>
				<?
			}
		}
		return ob_get_clean();
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboHead()
	{
		global $hooks;
		?>
			<script>
				var DBO_URL = '<?= DBO_URL ?>';
			</script>
			<?= dboImportJs(array(
				'modernizr',
				'jquery',
				'jquery-ui',
				'foundation',
				'peixelaranja',
				'select2',
				'priceformat',
				'autosize',
				'scrollto',
				'maskedinput',
				'timepicker',
				'tinymce',
				'multiselect',
				'dbo',
				'colorbox',
			)); ?>
		<?
		if(getPrettyHeaderSetting('parallax'))
		{
			echo dboImportJs(array(
				'smooth-scroll',
				'stellar',
			));
			?>
			<script>
				$(document).ready(function(){
					$(window).stellar({
						horizontalScrolling: false
					});
				}) //doc.ready
			</script>
			<?php
		}
		$hooks->do_action('dbo_head');
	}

	// ----------------------------------------------------------------------------------------------------------------

	function dboBody()
	{
		global $dbo;
		global $hooks;
		if(isSuperAdmin() || $hooks->has_action('dbo_top_dock'))
		{
			?>
			<div id="wrapper-dbo-top-dock" class="hide-for-small" style="<?= (($_GET['dbo_modal'])?('display: none;'):('')) ?>">
				<div class="row" style="height: 0;">
					<div class="large-12 columns" style="height: 0;">
						<ul id="dbo-top-dock">
							<?= isSuperAdmin() ? '<li><a href="'.DBO_URL.'/dbomaker/?reffered=1" target="dbomaker" class="color light pointer" title="Gerador de módulos do DBO" data-tooltip><i class="fa fa-fw fa-cube"></i></a></li>' : '' ?>
							<?php $hooks->do_action('dbo_top_dock'); ?>
						</ul>
					</div>
				</div>
			</div>
			<?php
		}
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboFooter()
	{
		global $start_time;
		global $dbo_query_counter;
		global $hooks;
		global $_system;
		echo dboGetRegisteredJS();
		$hooks->do_action('dbo_footer');
		if(!$_GET['dbo_mod'] && !$_GET['dbo_modal']) { dumpMid(); }
		$end_time = (float) array_sum(explode(' ',microtime()));
		if(!$_GET['dbo_modal'])
		{
			echo "<span class='processing-time' style='color: #FFF;' class='no-print'>Processing time: ". sprintf("%.4f", ($end_time-$start_time))." seconds</span>";
			echo " <span class='dbo-queries-number' style='color: #FFF;'> - Queries: ".$dbo_query_counter."</span>";
			echo " <span class='dbo-memory-usage' style='color: #FFF;'> - Memory peak: ".humanFilesize(memory_get_peak_usage(false))."</span>";
		}
		getMessage();
	}

	// ----------------------------------------------------------------------------------------------------------------

	function inlineStyles ()
	{
	?>
		<style>
		.bg-menu { background-color: <?= COLOR_MENU ?>; }
		.bg-header { background-color: <?= COLOR_HEADER ?>; }
		#wrapper-titulo span { color: <?= COLOR_DESCRIPTION ?>; }
		h2 { color: <?= COLOR_TITLE ?>; }
		label { color: <?= COLOR_TITLE ?>; }
		.wrapper-dbo-auto-admin h1 a.main-link:hover { color: <?= COLOR_TITLE ?>; }
		.big-button span:hover { background-color: rgba(<?= HEADER_R ?>, <?= HEADER_G ?>, <?= HEADER_B ?>, 0.2); color: <?= COLOR_HEADER ?>; }

		.big-button span span.notifications { background: <?= COLOR_TITLE ?>; }
		.sidebar-button span span.notifications { background: <?= COLOR_TITLE ?>; }
		.wrapper-dbo-auto-admin .notifications-tag { background: <?= COLOR_TITLE ?>; }
		.wrapper-dbo-auto-admin .wrapper-notifications { background: <?= COLOR_TITLE ?>; }

		.row-buttons input,
		.row-buttons a { background-color: rgba(<?= HEADER_R ?>, <?= HEADER_G ?>, <?= HEADER_B ?>, 0.8); text-shadow: 0px -1px 0px <?= COLOR_HEADER ?>; }
		.row-buttons input:hover,
		.row-buttons a:hover { background-color: rgba(<?= HEADER_R ?>, <?= HEADER_G ?>, <?= HEADER_B ?>, 1); text-shadow: 0px -1px 0px <?= COLOR_HEADER ?>; color: #FFF; text-decoration: none; }
		.wrapper-permissions .row-permission .header:hover { color: <?= COLOR_TITLE ?>; }
		.dbo-element div.fieldset table tbody tr.active { border: 1px solid <?= COLOR_TITLE ?>; color: <?= COLOR_TITLE ?>; text-shadow: 0px 1px 0px #FFF; }

		.dbo-element div.fieldset h3.section { color: <?= COLOR_HEADER ?>; border-bottom: 2px solid <?= COLOR_HEADER ?>; line-height: 30px; margin-top: 20px; margin-bottom: 15px; font-size: 17px; border-left: 10px solid <?= COLOR_HEADER ?>; padding-left: 15px; clear: both; }
		.dbo-element div.viewset h3.section { color: <?= COLOR_HEADER ?>; border-bottom: 2px solid <?= COLOR_HEADER ?>; line-height: 40px; font-size: 17px; border-left: 10px solid <?= COLOR_HEADER ?>; padding-left: 15px; clear: both; margin: 0; border-top: 1px solid #DDD; }

		.wrapper-dbo-auto-admin .dbo-header ul.dbo-bread-crumb li .arrow { border-color: white white white <?= COLOR_TITLE ?>; }
		.wrapper-dbo-auto-admin .dbo-header ul.dbo-bread-crumb li a:hover { color: <?= COLOR_TITLE ?>; text-decoration: underline }

		.wrapper-dbo-auto-admin .ui-sortable-helper { border: 1px solid <?= COLOR_TITLE ?> !important; }
		.wrapper-dbo-auto-admin .ui-sortable-helper td { background: rgba(<?= TITLE_R ?>, <?= TITLE_G ?>, <?= TITLE_B ?>, .9) !important; color: #FFF !important; text-shadow: 0px -1px 0px rgba(1,1,1,.3) !important; -webkit-box-shadow: 3px 3px 0px rgba(1,1,1,.1); -moz-box-shadow: 3px 3px 0px rgba(1,1,1,.1); box-shadow: 3px 3px 0px rgba(1,1,1,.1); }
		.auto-order-place-holder { background: rgba(<?= HEADER_R ?>, <?= HEADER_G ?>, <?= HEADER_B ?>, .5); }
		.wrapper-dbo-auto-admin .ui-sortable-helper td { background: #CCC; }

		</style>

		<!--[if lt IE 9 ]>
			<style>
				h1 { color: #BBB; text-shadow: none; }
				.wrapper-dbo-auto-admin h1 { color: #BBB; text-shadow: none; }
				.wrapper-dbo-auto-admin h1 a.main-link { color: #BBB; text-shadow: none; }
				.wrapper-dbo-auto-admin h1 .button-new a { color: #BBB; text-shadow: none; }
				.big-button span:hover { background-color: <?= COLOR_HEADER ?>; color: #FFF; }
				.big-button:hover { margin-top: 0px; margin-bottom: 10px; }
				#browser-warning { display: block; }
				.row-buttons input,
				.row-buttons input:hover,
				.row-buttons a,
				.row-buttons a:hover { background-color: <?= COLOR_HEADER ?>; text-shadow: none; }
				#browser-warning { display: block !important; }
			</style>
		<![endif]-->
	<?
	}

	// ----------------------------------------------------------------------------------------------------------------

	function isSuperAdmin()
	{
		global $SUPER_ADMINS;
		global $_SESSION;
		if(in_array($_SESSION['user'], $SUPER_ADMINS))
		{
			return true;
		}
		return false;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function systemTitle()
	{
		if(!file_exists(DBO_PATH."/../images/logo.png"))
		{
			?>
			<h1><?= SYSTEM_NAME ?></h1>
			<span><?= SYSTEM_DESCRIPTION ?></span>
			<?
		}
		else
		{
			?><img src='<?= DBO_URL ?>/../images/logo.png' class='admin-logo'/><?
		}
	}

	// ----------------------------------------------------------------------------------------------------------------

	function checkDomain()
	{
		$parts = explode("/", DBO_URL);
		$protocol = $parts[0];
		$domain = $parts[2];

		if($_SERVER['SERVER_NAME'] != $domain || (strstr($protocol, 'https') && !$_SERVER['HTTPS']))
		{
			header('Location: '.$protocol.'//'.$domain.$_SERVER['SCRIPT_NAME'].((strlen($_SERVER['QUERY_STRING']))?("?".$_SERVER['QUERY_STRING']):('')));
			exit();
		}
	}

	// ----------------------------------------------------------------------------------------------------------------

	function getImage($image, $prefix = '')
	{
		return DBO_URL."/upload/images/".$prefix.$image;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function getFile($file, $what = '')
	{
		return DBO_URL."/upload/images/".$prefix.$image;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function getLogoutLink()
	{
		?>
			<div class='wrapper-user'>
			<?
				if($_SESSION['user']) // USUARIO AUTENTICADO -------------------------------------------------------------------------------------
				{
					?>
					<a href='#' class='trigger-user-box'><?= getUsername(loggedUser()) ?> &#9660;</a> | <a href='logout.php'>Logout</a>
					<div class='user-box'>
						<h2>Alterar Senha</h2>
						<form method="POST" action="ajax-change-password.php" class='smallform' id='form-change-password'>
							<div class='row cf'>
								<div class='item'>
									<label>Senha Atual</label>
									<div class='input'><input type='password' name='current_pass' value=""/></div>
								</div><!-- item -->
							</div><!-- row -->
							
							<div class='row cf'>
								<div class='item'>
									<label>Nova Senha</label>
									<div class='input'><input type='password' name='new_pass' value=""/></div>
								</div><!-- item -->
							</div><!-- row -->
							
							<div class='row cf'>
								<div class='item'>
									<label>Confirmar</label>
									<div class='input'><input type='password' name='new_pass_check' value=""/></div>
								</div><!-- item -->
							</div><!-- row -->
							
							<div class='row cf'>
								<div class='item item-submit'>
									<div class='input'><input type='submit' name='Enviar' value="Enviar"/></div>
								</div><!-- item -->
							</div><!-- row -->
							
						</form>
					</div>
					<?
				}
			?>
			</div>
		<?
	}

	// ----------------------------------------------------------------------------------------------------------------

	function includeClasses()
	{
		global $_system;
		if(DBO_AUTOLOAD_CLASSES === true)
		{
			spl_autoload_register("dboAutoload");
		}
		else
		{
			$d = dir(DBO_PATH);
			while (false !== ($entry = $d->read())) {
				if(strpos($entry, "_class_") === 0)
				{
					$arq_classes[] = $entry;
				}
			}
			$d->close();
			if(is_array($arq_classes))
			{
				sort($arq_classes);
				foreach($arq_classes as $value)
				{
					//não inclui os módulos na blacklist
					if(is_array($_system['module_blacklist']))
					{
						$module = '';
						$module = preg_replace('#^_class_(.*)\.php$#is', '${1}', $value);
						if(in_array($module, $_system['module_blacklist']))
						{
							continue;
						}
					}
					require_once(DBO_PATH."/".$value);
				}
			}
		}
	}

	// ----------------------------------------------------------------------------------------------------------------

	function dboAutoload ($class) {
		include_once(DBO_PATH."/_class_".$class.".php");
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function removeDuplicates($sSearch, $sReplace, $sSubject)
	{
		$i=0;
		do{
			$sSubject=str_replace($sSearch, $sReplace, $sSubject);         
			$pos=strpos($sSubject, $sSearch);
			$i++;
			if($i>100) { die('removeDuplicates() loop error'); }
		}while($pos!==false);
		return $sSubject;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function noDiacritics($string)
	{
		//cyrylic transcription
		$cyrylicFrom = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
		$cyrylicTo   = array('A', 'B', 'W', 'G', 'D', 'Ie', 'Io', 'Z', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'Ch', 'C', 'Tch', 'Sh', 'Shtch', '', 'Y', '', 'E', 'Iu', 'Ia', 'a', 'b', 'w', 'g', 'd', 'ie', 'io', 'z', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'ch', 'c', 'tch', 'sh', 'shtch', '', 'y', '', 'e', 'iu', 'ia'); 

		$from = array("Á", "À", "Â", "Ä", "Ă", "Ā", "Ã", "Å", "Ą", "Æ", "Ć", "Ċ", "Ĉ", "Č", "Ç", "Ď", "Đ", "Ð", "É", "È", "Ė", "Ê", "Ë", "Ě", "Ē", "Ę", "Ə", "Ġ", "Ĝ", "Ğ", "Ģ", "á", "à", "â", "ä", "ă", "ā", "ã", "å", "ą", "æ", "ć", "ċ", "ĉ", "č", "ç", "ď", "đ", "ð", "é", "è", "ė", "ê", "ë", "ě", "ē", "ę", "ə", "ġ", "ĝ", "ğ", "ģ", "Ĥ", "Ħ", "I", "Í", "Ì", "İ", "Î", "Ï", "Ī", "Į", "Ĳ", "Ĵ", "Ķ", "Ļ", "Ł", "Ń", "Ň", "Ñ", "Ņ", "Ó", "Ò", "Ô", "Ö", "Õ", "Ő", "Ø", "Ơ", "Œ", "ĥ", "ħ", "ı", "í", "ì", "i", "î", "ï", "ī", "į", "ĳ", "ĵ", "ķ", "ļ", "ł", "ń", "ň", "ñ", "ņ", "ó", "ò", "ô", "ö", "õ", "ő", "ø", "ơ", "œ", "Ŕ", "Ř", "Ś", "Ŝ", "Š", "Ş", "Ť", "Ţ", "Þ", "Ú", "Ù", "Û", "Ü", "Ŭ", "Ū", "Ů", "Ų", "Ű", "Ư", "Ŵ", "Ý", "Ŷ", "Ÿ", "Ź", "Ż", "Ž", "ŕ", "ř", "ś", "ŝ", "š", "ş", "ß", "ť", "ţ", "þ", "ú", "ù", "û", "ü", "ŭ", "ū", "ů", "ų", "ű", "ư", "ŵ", "ý", "ŷ", "ÿ", "ź", "ż", "ž");
		$to   = array("A", "A", "A", "A", "A", "A", "A", "A", "A", "AE", "C", "C", "C", "C", "C", "D", "D", "D", "E", "E", "E", "E", "E", "E", "E", "E", "G", "G", "G", "G", "G", "a", "a", "a", "a", "a", "a", "a", "a", "a", "ae", "c", "c", "c", "c", "c", "d", "d", "d", "e", "e", "e", "e", "e", "e", "e", "e", "g", "g", "g", "g", "g", "H", "H", "I", "I", "I", "I", "I", "I", "I", "I", "IJ", "J", "K", "L", "L", "N", "N", "N", "N", "O", "O", "O", "O", "O", "O", "O", "O", "CE", "h", "h", "i", "i", "i", "i", "i", "i", "i", "i", "ij", "j", "k", "l", "l", "n", "n", "n", "n", "o", "o", "o", "o", "o", "o", "o", "o", "o", "R", "R", "S", "S", "S", "S", "T", "T", "T", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "W", "Y", "Y", "Y", "Z", "Z", "Z", "r", "r", "s", "s", "s", "s", "B", "t", "t", "b", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "w", "y", "y", "y", "z", "z", "z");

		$from = array_merge($from, $cyrylicFrom);
		$to   = array_merge($to, $cyrylicTo);

		$newstring=str_replace($from, $to, $string);   
		return $newstring;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function makeSlug($string, $maxlen=0)
	{
		$newStringTab=array();
		$string=strtolower(noDiacritics($string));
		if(function_exists('str_split'))
		{
			$stringTab=str_split($string);
		}
		else
		{
			$stringTab=my_str_split($string);
		}

		$numbers=array("0","1","2","3","4","5","6","7","8","9","-");
		//$numbers=array("0","1","2","3","4","5","6","7","8","9");

		foreach($stringTab as $letter)
		{
			if(in_array($letter, range("a", "z")) || in_array($letter, $numbers))
			{
				$newStringTab[]=$letter;
			//print($letter);
			}
			elseif($letter==" ")
			{
				$newStringTab[]="-";
			}
		}

		if(count($newStringTab)) {
			$newString=implode($newStringTab);
			if($maxlen>0) {
				$newString=substr($newString, 0, $maxlen);
			}
			$newString = removeDuplicates('--', '-', $newString);
		}
		else
		{
			$newString='';
		}      

		return $newString;
	}

	
	// ----------------------------------------------------------------------------------------------------------------

	function maxString($string, $max_size, $params = array())
	{
		extract($params);
		$more = $more ? $more : '...';
		$max = min($max_size, strlen($string));
		return iconv('UTF-8', "UTF-8//IGNORE", substr($string, 0, $max)).((strlen($string) > $max_size)?($more):(''));
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function getDboContext()
	{

		$context = '';

		//variavel com os dominios de autenticação de webmail permitidos para este contexto
		global $dbo_context_allowed_email_domains; 

		// --------------------------------------------------------------------------------------------------------
		// este bloco só serve para login dentro da FCFar. Se estiver fora, pode deletar, ou deixe ai. como queira.
		// --------------------------------------------------------------------------------------------------------
		if(strstr($_SERVER['SERVER_NAME'], '.fcfar.unesp.br'))
		{
			$context = 'fcfar';
			$dbo_context_allowed_email_domains = array('@fcfar.unesp.br', '@aluno.fcfar.unesp.br');
			define(DBO_ACCESS_LOCK_PATH, '/www/portal2/central/access_locks');
			define(DBO_ACCESS_LOCK_TIMEOUT, 60); //em minutos
		}
		elseif(strstr($_SERVER['SERVER_NAME'], '.iq.unesp.br'))
		{
			$context = 'iq';
			$dbo_context_allowed_email_domains = array('@iq.unesp.br');
		}

		return $context;

	}

	// ----------------------------------------------------------------------------------------------------------------

	function generateToken()
	{
		return uniqid()."-".generatePassword();
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function generatePassword($length=9, $strength=4) {
		$vowels = 'aeuy';
		$consonants = 'bdghjmnpqrstvz';
		if ($strength & 1) {
			$consonants .= 'BDGHJLMNPQRSTVWXZ';
		}
		if ($strength & 2) {
			$vowels .= "AEUY";
		}
		if ($strength & 4) {
			$consonants .= '23456789';
		}
		if ($strength & 8) {
			$consonants .= '@#$%';
		}

		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function loggedUser()
	{

		global $_pes;

		/* se o usuario já está instanciado no sistema, retorna ele. */
		if($_pes->id)
		{
			return $_pes->id;
		}

		$context = getDboContext();
		if($context == 'fcfar' || $context == 'iq')
		{
			if($_SESSION['user'])
			{
				$pes = new pessoa();
				$pes->email = $_SESSION['user'];
				if($pes->hasInativo())
				{
					$pes->inativo = 0;
				}
				$pes->loadAll();
				if($pes->size())
				{
					/* instancia o usuario */
					$_pes = clone $pes;
					return $pes->id;
				}
				//setMessage("<div class='error'>Usuário '".$lin_comum->nome_servidor."' sem permissão de acesso a este sistema. Consulte o administrador. (4651)</div>");
				return false;
			}
			return false;
		}
		elseif($_SESSION['user_id'])
		{
			/* instancia o usuario */
			$_pes = new pessoa($_SESSION['user_id']);
			return $_SESSION['user_id'];
		}
		return false;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function fullUrl() 
	{
		$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		$sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
		$protocol = substr($sp, 0, strpos($sp, "/")) . $s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		return $protocol . "://" . $_SERVER['HTTP_HOST'] . $port . $_SERVER['REQUEST_URI'];
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function getUsersPerfil($perfil_name)
	{

		//tratando grupos
		if(class_exists('grupo') && !sizeof($_sys[sysId()]['modulos']['grupo']))
		{
			$grp = new grupo();
			$perf = new perfil();

			$_sys[sysId()]['modulos']['grupo']['scheme']['tabela_ligacao'] = $grp->__module_scheme->campo[pessoa]->join->tabela_ligacao;
			$_sys[sysId()]['modulos']['perfil']['scheme']['tabela_ligacao'] = $perf->__module_scheme->campo[grupo]->join->tabela_ligacao;
		}
		if(strlen(trim($_sys[sysId()]['modulos']['grupo']['scheme']['tabela_ligacao'])) && strlen(trim($_sys[sysId()]['modulos']['perfil']['scheme']['tabela_ligacao'])))
		{
			$tem_grupo = true;
			$tabela_ligacao_grupo = $_sys[sysId()]['modulos']['grupo']['scheme']['tabela_ligacao'];
			$tabela_ligacao_perfil = $_sys[sysId()]['modulos']['perfil']['scheme']['tabela_ligacao'];
		}
		else
		{
			$tem_grupo = false;
		}

		$perf = new perfil("WHERE nome = '".$perfil_name."'");

		//tratando as pessoas
		$sql = "SELECT pessoa FROM pessoa_perfil WHERE perfil = '".$perf->id."'";
		$res = dboQuery($sql);

		$result = array();

		if(mysql_affected_rows())
		{
			while($lin = mysql_fetch_object($res))
			{
				$result[$lin->pessoa] = $lin->pessoa;
			}
		}

		//tratando os grupos
		if($tem_grupo)
		{
			$sql = "
				SELECT 
					".$tabela_ligacao_grupo.".pessoa AS pessoa
				FROM
					".$tabela_ligacao_grupo.",
					".$tabela_ligacao_perfil."
				WHERE
					".$tabela_ligacao_perfil.".perfil = '".$perf->id."' AND
					".$tabela_ligacao_perfil.".grupo = ".$tabela_ligacao_grupo.".grupo
			";
			$res = dboQuery($sql);
			if(dboAffectedRows())
			{
				while($lin = mysql_fetch_object($res))
				{
					$result[$lin->pessoa] = $lin->pessoa;
				}
			}
		}		

		return $result;
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function getPessoasPerfil($perfil_name)
	{
		return getUsersPerfil($perfil_name);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function outdatedBrowser()
	{
		global $outdated_ie_range;
		if(!$outdated_ie_range)
		{
			$range = '[5-8]';
		}
		else
		{
			$range = $outdated_ie_range;
		}

		// check what browser the visitor is using
		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		// This bit differentiates IE from Opera
		if(preg_match('/(?i)(MSIE) ('.$range.')/',$user_agent) && !preg_match('/Opera/i',$user_agent)) {
		// if IE<=8
			preg_match_all('/(?i)(MSIE) ('.$range.')/',$user_agent, $matches);
			$browser = $matches[1][0];
			$version = $matches[2][0];
			if($browser == 'MSIE')
			{
				$data['browser'] = "Internet Explorer";
				$data['version'] = number_format($version, 1, '.', '');
				return $data;
			}
			return true;
		}
		else {
			return false;
		}
 
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function browserWarning()
	{
		if(outdatedBrowser())
		{
		?>
		<link rel="stylesheet" media='screen' href="http://www.peixelaranja.com.br/global-assets/style-top-browser-warning.css">
		<div id="top-browser-warning">
			<u><strong>ATENÇÃO!</strong></u> Seu navegador não é compatível com este sistema. Utilize uma das opções: <a href='http://www.google.com/intl/pt-BR/chrome/' title='Faça o download do Google Chrome' target='_blank'><img src='http://www.peixelaranja.com.br/global-assets/icon-chrome.png' alt='Google Chrome'/><strong>Google Chrome</strong></a>, <a href='https://www.mozilla.org/pt-BR/firefox/new/' title='Faça o download do Firefox' target='_blank'><img src='http://www.peixelaranja.com.br/global-assets/icon-firefox.png' alt='Google Chrome'/><strong>Firefox</strong></a>, ou continue a seu próprio risco.
		</div>
		<?
		}
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function hex2rgb($hex) {
		$hex = str_replace("#", "", $hex);

		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array('r' => $r, 'g' => $g, 'b' => $b);
		//return implode(",", $rgb); // returns the rgb values separated by commas
		return $rgb; // returns an array with the rgb values
	}	

	// ----------------------------------------------------------------------------------------------------------------

	function secureUrl($url = '')
	{
		/*
		$s = ((empty($_SERVER["HTTPS"]))?(''):((($_SERVER["HTTPS"] == "on")?("s"):(''))));
		$sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
		$protocol = substr($sp, 0, strpos($sp, "/")) . $s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		*/

		$sal = ((!defined('DBO_SECURE_URL_SALT'))?('--##--NaCl--@@@-!'):(DBO_SECURE_URL_SALT));
		$hash_var = 'dbo_secure_hash';
		
		//pega url pasada pelo usuário ou página atual.
		$foo = urldecode(((strlen($url))?($url):($_SERVER['REQUEST_URI'])));

		//separando nome de arquivo de variaveis
		$todo = explode("/", $foo);
		$aux = $todo[sizeof($todo)-1];
		unset($todo[sizeof($todo)-1]);
		list($arquivo, $vars) = explode('?', $aux);

		//retirando a variavel hash da url
		if(strlen($vars))
		{
			$vars = explode("&", $vars);
			foreach($vars as $key => $value)
			{
				if(strpos($value, $hash_var) === 0)
				{
					unset($vars[$key]);
				}
			}
			$clean_vars = implode("&", $vars);
		}

		//criando arquivo final
		$clean_url = $arquivo.((strlen($clean_vars))?('?'.$clean_vars):(''));

		//caso queira encriptar uma url
		if(strlen($url))
		{
			$hash = md5($sal.$clean_url.$sal);
			$file = $clean_url.((strlen($clean_vars))?("&"):("?")).$hash_var."=".$hash;
			$todo[] = $file;
			return implode('/', $todo);
		}
		//caseo queira checar se a url é valida
		else
		{
			if($_REQUEST[$hash_var] == md5($sal.$clean_url.$sal))
			{
				return true;
			}
			return false;
		}
	}

	// ----------------------------------------------------------------------------------------------------------------

	function singleLine($var)
	{
		$var = preg_replace("/\s+/", " ", $var);
		return $var;
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function CSRFStart()
	{
		if(defined('CSRF_context') && CSRF_context == 'system')
		{
			if(!$_SESSION[sysId()]['DBO_CSRF_token'])
			{
				$_SESSION[sysId()]['DBO_CSRF_token'] = md5('salcisne@!'.rand(1,999999));
			}
		}
		else
		{
			if(!$_SESSION['DBO_CSRF_token'])
			{
				$_SESSION['DBO_CSRF_token'] = md5('salcisne@!'.rand(1,999999));
			}
		}
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function CSRFGetToken()
	{
		if(defined('CSRF_context') && CSRF_context == 'system')
		{
			return $_SESSION[sysId()]['DBO_CSRF_token'];
		}
		else
		{
			return $_SESSION['DBO_CSRF_token'];
		}
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function CSRFCheck()
	{
		if(!CSRFGetToken() || CSRFGetToken() != $_REQUEST['DBO_CSRF_token'])
			return false;
		return true;
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function CSRFInput()
	{
		return '<input type="hidden" name="DBO_CSRF_token" value="'.CSRFGetToken().'">';
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function CSRFVar()
	{
		return "DBO_CSRF_token=".CSRFGetToken();
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function CSRFCheckJson()
	{
		//global $json_result; 
		if(!CSRFCheck())
		{
			$json_result['message'] = "<div class='error'>Erro: CSRF - O token fornecido não é compatível com a sessão.</div>";
			echo json_encode($json_result);
			exit();
			//return false;
		}
		//return true;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function CSRFCheckRequest()
	{
		global $json_result; 
		if(!CSRFCheck())
		{
			setMessage("<div class='error'>Erro: CSRF: O token fornecido não é compatível com a sessão.</div>");
			header("Location: ".$_SERVER['HTTP_REFERER']);
			exit();
			return false;
		}
		return true;
	}

	// ----------------------------------------------------------------------------------------------------------------

	function ago($datetime, $show_tense = true) {

		$time = strtotime($datetime);

		$periods = array("segundo", "minuto", "hora", "dia", "semana", "mês", "ano", "década");
		$lengths = array("60","60","24","7","4.35","12","10");

		if(function_exists('dboNow'))
		{
			$now = strtotime(dboNow());
		}
		else
		{
			$now = time();
		}

		$difference = $now - $time;
		$tense = "há";

		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
		   $difference /= $lengths[$j];
		}

		$difference = round($difference);

		if($difference != 1) {
			if($j == 5) //mes
			{
				$periods[$j] = "meses";
			}
			else
			{
				$periods[$j].= "s";
			}
		}

		if($difference <= 0) { return 'agora'; }

		return (($show_tense)?($tense." "):('')).abs($difference)." ".$periods[$j];
	}	

	// ----------------------------------------------------------------------------------------------------------------

	function goToUpdateForm($obj, $params = array())
	{
		extract($params);

		if(!$pagina)
		{
			$pagina = $obj->keepUrl(array("!dbo_new&!dbo_view&!dbo_delete", "dbo_update=".$obj->id));
		}

		//setMessage('<div class="success">Sucesso!</div>');

		header("Location: ".$pagina);
		exit();
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboStrToLower($string)
	{
		if(function_exists(mb_strtolower))
		{
			return(mb_strtolower($string));
		}
		return strtolower($string);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function dboInit()
	{
	}

	// ----------------------------------------------------------------------------------------------------------------

	function getPerfisPessoa($pessoa_id)
	{
		global $_sys;
		return array_keys($_sys[sysId()]['perfis_pessoa'][loggedUser()]);
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function getItemsSidebar()
	{
		global $_sys;
		//checando se os perfis estão carregados
		if(!is_array($_sys[sysId()]['perfis_permissoes']))
		{
			loadAllPerfis();
		}
		
		$perfis_pessoa = getPerfisPessoa(loggedUser());

		if(is_array($_sys[sysId()]['perfis_permissoes']))
		{
			$total = 0;
			foreach($_sys[sysId()]['perfis_permissoes'] as $perfil_id => $perfil)
			{
				if(in_array($perfil_id, $perfis_pessoa))
				{
					foreach($perfil as $modulo)
					{
						if(is_array($modulo))
						{
							if(array_key_exists('sidebar', $modulo))
							{
								$total++;
							}
						}
					}
				}
			}
			return $total;
		}
		return false;
	}

	// ----------------------------------------------------------------------------------------------------------------
	function dboDate($d, $timestamp = false)
	{
		/* meses completo */
		$months = array(
			'January' => 'Janeiro',
			'February' => 'Fevereiro',
			'March' => 'Março',
			'April' => 'Abril',
			'May' => 'Maio',
			'June' => 'Junho',
			'July' => 'Julho',
			'August' => 'Agosto',
			'September' => 'Setembro',
			'October' => 'Outubro',
			'November' => 'Novembro',
			'December' => 'Dezembro'
		);

		/* meses_curto */
		$months_short = array(
			'Jan' => 'Jan',
			'Feb' => 'Fev',
			'Mar' => 'Mar',
			'Apr' => 'Abr',
			'May' => 'Mai',
			'Jun' => 'Jun',
			'Jul' => 'Jul',
			'Aug' => 'Ago',
			'Sep' => 'Set',
			'Oct' => 'Out',
			'Nov' => 'Nov',
			'Dec' => 'Dez'
		);

		/* semana completo */
		$week = array(
			'Monday' => 'Segunda',
			'Tuesday' => 'Terça',
			'Wednesday' => 'Quarta',
			'Thursday' => 'Quinta',
			'Friday' => 'Sexta',
			'Saturday' => 'Sábado',
			'Sunday' => 'Domingo'
		);

		/* semana completo */
		$week_short = array(
			'Mon' => 'Seg',
			'Tue' => 'Ter',
			'Wed' => 'Qua',
			'Thu' => 'Qui',
			'Fri' => 'Sex',
			'Sat' => 'Sáb',
			'Sun' => 'Dom'
		);

		$template = $d;
	
		$d = (($timestamp)?(date($d, $timestamp)):(date($d)));

		/* Por causa do maldito mes de maio, precisa verificar se foi pedido mes completo ou mes curto antes de executar os replaces. */
		if(strstr($template, 'F')) /* mes completo */
		{
			$d = str_replace(array_keys($months), $months, $d);
		}
		else
		{
			$d = str_replace(array_keys($months_short), $months_short, $d);
		}
		
		$d = str_replace(array_keys($week), $week, $d);
		$d = str_replace(array_keys($week_short), $week_short, $d);

		/* colocando a palavra "de" */
		$d = str_replace('---', 'de', $d);

		return $d;
	
	}
	
	// ----------------------------------------------------------------------------------------------------------------

	function humanFilesize($bytes, $dec = 2) 
	{
		$size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$factor = floor((strlen($bytes) - 1) / 3);

		return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . @$size[$factor];
	}
	
	// ----------------------------------------------------------------------------------------------------------------

?>