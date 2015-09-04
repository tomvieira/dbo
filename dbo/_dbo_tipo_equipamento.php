<?

/* ================================================================================================================== */
/* DBO DEFINITION FILE FOR MODULE 'tipo_equipamento' ============================ AUTO-CREATED ON 31/03/2015 12:49:14 */
/* ================================================================================================================== */



/* GENERAL MODULE DEFINITIONS ======================================================================================= */

$module = new Obj();
$module->modulo = 'tipo_equipamento';
$module->tabela = 'tipo_equipamento';
$module->titulo = 'Tipo de equipamento';
$module->titulo_plural = 'Tipos de equipamentos';
$module->titulo_big_button = 'Inventário';
$module->genero = 'o';
$module->paginacao = '20';
$module->update = true;
$module->delete = true;
$module->insert = 'Novo Tipo de equipamento';
$module->preload_insert_form = true;
$module->auto_view = false;
$module->order_by = '9';

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
$field->titulo = 'Nome';
$field->coluna = 'nome';
$field->default = 'ASC';
$field->pk = false;
$field->isnull = false;
$field->add = true;
$field->valida = true;
$field->edit = true;
$field->view = true;
$field->lista = true;
$field->filter = true;
$field->order = true;
$field->type = 'VARCHAR(255)';
$field->interaction = '';
$field->list_function = 'tipoEquipamentoTotal';
$field->tipo = 'text';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Descrição';
$field->coluna = 'descricao';
$field->pk = false;
$field->isnull = false;
$field->add = true;
$field->valida = false;
$field->edit = true;
$field->view = true;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'TEXT';
$field->interaction = '';
$field->tipo = 'textarea';
$field->rows = 5;
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Relação com Tipo de Serviço';
$field->coluna = 'tipo_equipamento_tipo_servico';
$field->dica = 'Equipamentos nestes tipos de serviço serão sugeridos ao servidor para se criar um vínculo da Requisição com o Equipamento.';
$field->pk = false;
$field->isnull = true;
$field->add = true;
$field->valida = false;
$field->edit = true;
$field->view = true;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'INT(11)';
$field->interaction = '';
$field->tipo = 'joinNN';
	$join = new Obj();
	$join->modulo = 'tipo_servico';
	$join->chave = 'id';
	$join->valor = 'nome';
	$join->select2 = true;
	$join->tabela_ligacao = 'tipo_equipamento_tipo_servico';
	$join->chave1 = 'tipo_equipamento';
	$join->chave2 = 'tipo_servico';
	$join->tipo = 'select';
	$join->order_by = 'id';
$field->join = $join;
$module->campo[$field->coluna] = $field;

/*==========================================*/

/* GRID FOR THE FORM LAYOUT ========================================================================================= */

$grid = array();

$grid[] = array('6');
$grid[] = array('12');
$grid[] = array('12');

$module->grid = $grid;

/* MODULE LIST BUTTONS ============================================================================================== */

$button = new Obj();
$button->value = 'Equipamentos';
$button->modulo = 'equipamento';
$button->modulo_fk = 'tipo_equipamento';
$button->key = 'id';
$button->view = false;
$button->show = true;
$button->subsection = false;
$button->autoload = false;
$module->button[] = $button;

$button = new Obj();
$button->value = 'Manutenções Periódicas';
$button->modulo = 'equipamento_manutencao_periodica';
$button->modulo_fk = 'tipo_equipamento';
$button->key = 'id';
$button->view = false;
$button->show = true;
$button->subsection = false;
$button->autoload = false;
$module->button[] = $button;

/* FUNÇÕES AUXILIARES =============================================================================================== */

if(!function_exists('tipo_equipamento_pre_insert'))
{
	function tipo_equipamento_pre_insert () // there is nothing to get as a parameter, right?
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('tipo_equipamento_pos_insert'))
{
	function tipo_equipamento_pos_insert ($obj) // active just inserted object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('tipo_equipamento_pre_update'))
{
	function tipo_equipamento_pre_update ($obj) // active object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('tipo_equipamento_pos_update'))
{
	function tipo_equipamento_pos_update ($obj) // active updated object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('tipo_equipamento_pre_delete'))
{
	function tipo_equipamento_pre_delete ($obj) // active object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('tipo_equipamento_pos_delete'))
{
	function tipo_equipamento_pos_delete ($obj) // active deleted object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('tipo_equipamento_pre_list'))
{
	function tipo_equipamento_pre_list () // nothing to be passed here...
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('tipo_equipamento_pos_list'))
{
	function tipo_equipamento_pos_list ($ids) // ids of the listed elements
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('tipo_equipamento_notifications'))
{
	function tipo_equipamento_notifications ($type = '')
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('tipo_equipamento_overview'))
{
	function tipo_equipamento_overview ($foo)
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}


?>