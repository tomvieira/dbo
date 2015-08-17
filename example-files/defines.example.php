<?php
//sets the site url
//define(SITE_URL, preg_replace('#/admin/dbo$#is', '', DBO_URL));

/* sets the main word defining the admin cockpit */
//define(DBO_TERM_CADASTROS, 'Cadastros');

//definições de um header customizado para o admin 
//colocar as imagens admin-bg.jpg e admin-logo.png dentro da pasta admin/images
//$_system['pretty_header'] = array(
	//'theme' => 'dark',
	//'hide_menu' => true,
	//'height' => 210, //valor inteiro, sem px
	//'logo_height' => 140,
	//'logo_offset' => 85,
	//'parallax' => true,
	//'styles' => 'background-size: cover;',
//);

/* definindo tipos especiais de páginas no sistema */
//$_system['pagina_tipo']['animal'] = array(
	//'tipo' => 'animal',
	//'titulo' => 'animal',
	//'titulo_plural' => 'animais',
	//'titulo_big_button' => 'plantel',
	//'genero' => 'o',
	//'extension_module' => 'pagina_animal',
	//'slug_prefix' => 'plantel', //incluido antes do slug no permalink
	//'default_list_view' => 'gallery', //list | details | gallery
	//'paginacao' => 20
	//'hidden_fields' => array( //array contendo o campos que não devem ser exibidos por padrão no formulário.
		//'slug',
		//'subtitulo',
		//'texto',
		//'autor',
		//'atributos',
		//'categorias',
		//'imagem_destaque',
		//'categorias',
	//), 
//);

//tamanhos cutomizados de imagens do sistema
//$_system['media_manager']['image_sizes'] = array(
	//'gigante' => array(
		//'name' => 'Gigante',
		//'max_width' => '3000',
		//'max_height' => '3000',
		//'quality' => '90'
	//),
	//'wide' => array(
		//'name' => 'Widescreen',
		//'max_width' => '1920',
		//'max_height' => '1080',
		//'quality' => '80'
	//)
//);

//define se as páginas devem ser cacheadas ou não
//define(DBO_CACHE_PAGES, false);

//definições de duração do cache para as páginas (unidades: s - segundos, m - minutos, h - horas, d - dias, w - semanas, false - desativa o cache
//$_system['cache_settings']['expire']['global'] = '1h'; //definição global de cache que sera aplicada se a fornecida for null
//$_system['cache_settings']['expire']['slug']['home'] = '1h'; //definição especifica para esta slug
//$_system['cache_settings']['expire']['block']['sidebar'] = '30s'; //definição especifica para este bloco

//blacklist de módulos. Os módulos listados aqui não será carregados no sistema.
//$_system['module_blacklist'] = array(
	//'pagina',
	//'menu',
	//'categoria',
//);

?>