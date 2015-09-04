<?

/* ================================================================================================================== */
/* DBO DEFINITION FILE FOR MODULE 'requisicao_item_equipamento' ================= AUTO-CREATED ON 31/03/2015 12:49:14 */
/* ================================================================================================================== */



/* GENERAL MODULE DEFINITIONS ======================================================================================= */

$module = new Obj();
$module->modulo = 'requisicao_item_equipamento';
$module->tabela = 'requisicao_item_equipamento';
$module->titulo = 'Item de Requisição - Equi.';
$module->titulo_plural = 'Itens de Requisição - Equip.';
$module->genero = 'a';
$module->paginacao = '20';
$module->update = true;
$module->delete = true;
$module->insert = 'Nova Item de Requisição - Equi.';
$module->preload_insert_form = true;
$module->auto_view = true;
$module->order_by = '14';

/* FIELDS =========================================================================================================== */

$field = new Obj();
$field->titulo = 'Id';
$field->coluna = 'id';
$field->pk = true;
$field->isnull = false;
$field->add = false;
$field->valida = false;
$field->edit = false;
$field->view = false;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'INT NOT NULL auto_increment';
$field->interaction = '';
$field->tipo = 'pk';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Item de requisição';
$field->coluna = 'requisicao_item';
$field->pk = false;
$field->isnull = true;
$field->add = false;
$field->valida = false;
$field->edit = false;
$field->view = false;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'INT(11)';
$field->interaction = '';
$field->tipo = 'join';
	$join = new Obj();
	$join->modulo = 'requisicao_item';
	$join->chave = 'id';
	$join->valor = 'id';
	$join->tipo = 'select';
	$join->order_by = 'id';
$field->join = $join;
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Equipamento';
$field->coluna = 'equipamento';
$field->pk = false;
$field->isnull = true;
$field->add = false;
$field->valida = false;
$field->edit = false;
$field->view = false;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'INT(11)';
$field->interaction = '';
$field->tipo = 'join';
	$join = new Obj();
	$join->modulo = 'equipamento';
	$join->chave = 'id';
	$join->valor = 'id';
	$join->tipo = 'select';
	$join->order_by = 'id';
$field->join = $join;
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Situação';
$field->coluna = 'situacao';
$field->pk = false;
$field->isnull = true;
$field->add = false;
$field->valida = false;
$field->edit = false;
$field->view = false;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'VARCHAR(255)';
$field->interaction = '';
$field->tipo = 'select';
$field->valores = array(
	'entrada' => 'Entrada na seção',
	'saida' => 'Devolução ao usuário',
	'resolvido_local' => 'Problema resolvido no local',
	'assistencia_tecnica' => 'Enviado à assistência técnica',
	'baixa' => 'Baixa',
);
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Data';
$field->coluna = 'data';
$field->pk = false;
$field->isnull = true;
$field->add = false;
$field->valida = false;
$field->edit = false;
$field->view = false;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'DATETIME';
$field->interaction = '';
$field->classes = 'datetimepick';
$field->tipo = 'datetime';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Criado Por';
$field->coluna = 'created_by';
$field->pk = false;
$field->isnull = false;
$field->add = false;
$field->valida = false;
$field->edit = false;
$field->view = false;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'INT(11)';
$field->interaction = '';
$field->tipo = 'join';
	$join = new Obj();
	$join->modulo = 'pessoa';
	$join->chave = 'id';
	$join->valor = 'nome';
	$join->tipo = 'select';
	$join->order_by = 'nome';
$field->join = $join;
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Comentário';
$field->coluna = 'comentario';
$field->pk = false;
$field->isnull = false;
$field->add = false;
$field->valida = false;
$field->edit = false;
$field->view = false;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'TEXT';
$field->interaction = '';
$field->tipo = 'textarea';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Local';
$field->coluna = 'local';
$field->pk = false;
$field->isnull = true;
$field->add = false;
$field->valida = false;
$field->edit = false;
$field->view = false;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'INT(11)';
$field->interaction = '';
$field->tipo = 'join';
	$join = new Obj();
	$join->modulo = 'local';
	$join->chave = 'id';
	$join->valor = 'id';
	$join->tipo = 'select';
	$join->order_by = 'id';
$field->join = $join;
$module->campo[$field->coluna] = $field;

/*==========================================*/

/* GRID FOR THE FORM LAYOUT ========================================================================================= */

$grid = array();


$module->grid = $grid;

/* MODULE LIST BUTTONS ============================================================================================== */

/* FUNÇÕES AUXILIARES =============================================================================================== */

if(!function_exists('requisicao_item_equipamento_pre_insert'))
{
	function requisicao_item_equipamento_pre_insert () // there is nothing to get as a parameter, right?
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('requisicao_item_equipamento_pos_insert'))
{
	function requisicao_item_equipamento_pos_insert ($obj) // active just inserted object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('requisicao_item_equipamento_pre_update'))
{
	function requisicao_item_equipamento_pre_update ($obj) // active object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('requisicao_item_equipamento_pos_update'))
{
	function requisicao_item_equipamento_pos_update ($obj) // active updated object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('requisicao_item_equipamento_pre_delete'))
{
	function requisicao_item_equipamento_pre_delete ($obj) // active object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('requisicao_item_equipamento_pos_delete'))
{
	function requisicao_item_equipamento_pos_delete ($obj) // active deleted object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('requisicao_item_equipamento_pre_list'))
{
	function requisicao_item_equipamento_pre_list () // nothing to be passed here...
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('requisicao_item_equipamento_pos_list'))
{
	function requisicao_item_equipamento_pos_list ($ids) // ids of the listed elements
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('requisicao_item_equipamento_notifications'))
{
	function requisicao_item_equipamento_notifications ($type = '')
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('requisicao_item_equipamento_overview'))
{
	function requisicao_item_equipamento_overview ($foo)
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}


?>