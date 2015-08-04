<?php 
	require_once('../../lib/includes.php'); 
	require_once(DBO_PATH.'/core/dbo-categoria-backend.php');
	dboAuth('json');
	CSRFCheckJson();

	$json_result = array();

	if($_GET['action'] == 'quick-cadastrar-nova')
	{
		if(!strlen(trim($_POST['nome'])))
		{
			$json_result['message'] = '<div class="error">Erro: Por favor, preencha um nome para a categoria.</div>';
		}
		else
		{
			$cat = new categoria();
			$cat->nome = $_POST['nome'];
			$cat->mae = strlen(trim($_POST['mae'])) ? $_POST['mae'] : $cat->null();
			$cat->pagina_tipo = $_GET['pagina_tipo'];
			$cat->folha = 1;
			$cat->slug = dboUniqueSlug($_POST['nome'], 'database', array(
				'table' => $cat->getTable(),
				'column' => 'slug',
			));
			$cat->save();

			$checked = (array)$_POST['checados'];

			$tree = categoria::getCategoryStructure($_GET['pagina_tipo']);

			$json_result['message'] = '<div class="success">Categoria cadastrada com sucesso!</div>';
			$json_result['html']['#wrapper-categorias-da-pagina'] = renderCategoryCheckboxes($tree, $checked);
			$json_result['html']['#categoria_mae'] = '<option value="">- nenhuma -</option>'.renderCategoryOptions($tree);
			$json_result['eval'] = '$("#categoria_nome, #categoria_mae").val(""); $(".cancel-pub-categorias").trigger("click"); ';
		}
	}

	echo json_encode($json_result);

?>