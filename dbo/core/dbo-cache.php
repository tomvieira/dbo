<?php

	global $dbo_cache_file;
	global $hooks;

	//definindo o hook para limpar o cache do sistema
	function button_clear_dbo_cache()
	{
		?>
		<li><a href="" class="color light pointer" title="Limpar o cache das páginas do site" data-tooltip><i class="fa fa-fw fa-refresh"></i></a></li>
		<?php
	}
	if(is_object($hooks))
	{
		$hooks->add_action('dbo_top_dock', 'button_clear_dbo_cache');
	}

	//define o path para os arquivos de path, se ja não definido no defines.php
	if(!defined(DBO_CACHE_PATH))
	{
		define(DBO_CACHE_PATH, dirname(dirname(__FILE__))."/cache");
	}

	function noCacheUrl() 
	{
		parse_str($_SERVER['QUERY_STRING'], $query_string);
		$query_string['dbo_no_cache'] = 1;
		
		$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		$sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
		$protocol = substr($sp, 0, strpos($sp, "/")) . $s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		return $protocol . "://" . $_SERVER['HTTP_HOST'] . $port . $_SERVER['PHP_SELF'].'?'.http_build_query($query_string);
	}


	//começa a capturar o output para o cache
	function dboCacheStart()
	{
		ob_start();
	}

	//verifica se o arquivo de cache existe
	function dboCached()
	{
		global $dbo_cache_file;
		return file_exists(DBO_CACHE_PATH."/".$dbo_cache_file);
	}

	//inclui o arquivo cacheado
	function dboCacheLoad()
	{
		global $dbo_cache_file;
		include(DBO_CACHE_PATH.'/'.$dbo_cache_file);
	}

	//captura o output e salva no arquivo. da proxima vez, estará cacheado.
	function dboCacheEnd()
	{
		global $dbo_cache_file;
		$f = fopen(DBO_CACHE_PATH.'/'.$dbo_cache_file, 'w');
		$content = ob_get_contents();
		fwrite($f, $content);
		fclose($f);
	}

	//-----------------------------------------------------------------------------------------------
	//tratando cache para paginas inteiras ----------------------------------------------------------
	//-----------------------------------------------------------------------------------------------

	function dboCachePage()
	{
		if(!$_GET['dbo_no_cache'])
		{
			parse_str($_SERVER['QUERY_STRING'], $query_string);

			//construindo o nome do arquivo de cache
			unset($query_string['dbo_no_cache']);
			$cache_file = DBO_CACHE_PATH."/".($_GET['slug'] ? $_GET['slug'].'-' : '').str_replace("/", "", base64_encode($_SERVER['PHP_SELF'].'?'.http_build_query($query_string)));

			//se o arquivo de cache existe, inclui ele e encerra
			if(file_exists($cache_file))
			{
				include($cache_file);
				exit();
			}
			else
			{
				$content = file_get_contents(noCacheUrl());
				$f = fopen($cache_file, 'w');
				fwrite($f, $content);
				fclose($f);
			}
		}
	}


?>