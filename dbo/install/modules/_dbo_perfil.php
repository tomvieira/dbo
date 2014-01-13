<?

/* ================================================================================================================== */
/* DBO DEFINITION FILE FOR MODULE 'perfil' ====================================== AUTO-CREATED ON 16/04/2013 09:53:21 */
/* ================================================================================================================== */



/* GENERAL MODULE DEFINITIONS ======================================================================================= */

$module = new Obj();
$module->modulo = 'perfil';
$module->tabela = 'perfil';
$module->titulo = 'Perfil';
$module->titulo_plural = 'Perfis';
$module->genero = 'o';
$module->paginacao = '10';
$module->update = true;
$module->delete = true;
$module->insert = 'Novo Perfil';
$module->preload_insert_form = false;
$module->auto_view = false;
$module->order_by = '0';

/* FIELDS =========================================================================================================== */

$field = new Obj();
$field->titulo = 'Id';
$field->coluna = 'id';
$field->pk = true;
$field->add = false;
$field->valida = false;
$field->edit = false;
$field->view = false;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'INT NOT NULL auto_increment';
$field->tipo = 'pk';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Nome';
$field->coluna = 'nome';
$field->pk = false;
$field->add = true;
$field->valida = false;
$field->edit = true;
$field->view = true;
$field->lista = true;
$field->filter = false;
$field->order = false;
$field->type = 'VARCHAR(255)';
$field->tipo = 'text';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Permissão';
$field->coluna = 'permissao';
$field->pk = false;
$field->add = false;
$field->valida = false;
$field->edit = false;
$field->view = false;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'TEXT';
$field->tipo = 'text';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Pessoa';
$field->coluna = 'pessoa';
$field->pk = false;
$field->add = true;
$field->valida = false;
$field->edit = true;
$field->view = true;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'INT(11)';
$field->tipo = 'joinNN';
	$join = new Obj();
	$join->modulo = 'pessoa';
	$join->chave = 'id';
	$join->valor = 'nome';
	$join->tabela_ligacao = 'pessoa_perfil';
	$join->chave1 = 'perfil';
	$join->chave2 = 'pessoa';
	$join->tipo = 'select';
$field->join = $join;
$module->campo[$field->coluna] = $field;

/*==========================================*/

/* GRID FOR THE FORM LAYOUT ========================================================================================= */

/* MODULE LIST BUTTONS ============================================================================================== */

$button = new Obj();
$button->value = 'Permissões';
$button->custom = true;
$button->code = '
	$code = "<a class=\'button-dbo-fixo\' href=\'dbo_permissions.php?perfil=$id\'>[VALUE]</a>";
';
$module->button[] = $button;

/* FUNÇÕES AUXILIARES =============================================================================================== */

if(!function_exists('perfil_pre_insert'))
{
	function perfil_pre_insert () // there is nothing to get as a parameter, right?
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('perfil_pos_insert'))
{
	function perfil_pos_insert ($id) // id of the just inserted element
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('perfil_pre_update'))
{
	function perfil_pre_update ($id) // id of the active element
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('perfil_pos_update'))
{
	function perfil_pos_update ($id) // id of the active element
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('perfil_pre_delete'))
{
	function perfil_pre_delete ($id) // id of the active element
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('perfil_pos_delete'))
{
	function perfil_pos_delete ($id) // id of the active element
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('perfil_pre_list'))
{
	function perfil_pre_list () // nothing to be passed here...
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('perfil_pos_list'))
{
	function perfil_pos_list ($ids) // ids of the listed elements
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('perfil_notifications'))
{
	function perfil_notifications ($type = '')
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('perfil_overview'))
{
	function perfil_overview ($foo)
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}


?>