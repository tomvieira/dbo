<?php
//sets the site url
//define(SITE_URL, preg_replace('#/admin/dbo$#is', '', DBO_URL));

/* sets the main word defining the admin cockpit */
//define(DBO_TERM_CADASTROS, 'Cadastros');

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