<?

/* ================================================================================================================== */
/* DBO DEFINITION FILE FOR MODULE 'equipamento' ================================= AUTO-CREATED ON 31/03/2015 12:49:14 */
/* ================================================================================================================== */



/* GENERAL MODULE DEFINITIONS ======================================================================================= */

$module = new Obj();
$module->modulo = 'equipamento';
$module->tabela = 'equipamento';
$module->titulo = 'Equipamento';
$module->titulo_plural = 'Equipamentos';
$module->genero = 'o';
$module->paginacao = '10';
$module->update = true;
$module->delete = true;
$module->insert = 'Novo Equipamento';
$module->preload_insert_form = false;
$module->auto_view = false;
$module->order_by = '10';

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
$field->titulo = 'Foto';
$field->coluna = 'foto';
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
$field->tipo = 'image';
	$image = new Obj();
	$image->width = 1000;
	$image->height = 1000;
	$image->prefix = '';
	$image->quality = 80;
$field->image[] = $image;
	$image = new Obj();
	$image->width = 300;
	$image->height = 300;
	$image->prefix = 't_';
	$image->quality = 80;
$field->image[] = $image;
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Tipo de Equipamento';
$field->coluna = 'tipo_equipamento';
$field->pk = false;
$field->isnull = false;
$field->add = true;
$field->valida = true;
$field->edit = true;
$field->view = true;
$field->lista = true;
$field->filter = true;
$field->order = true;
$field->type = 'INT(11)';
$field->interaction = '';
$field->tipo = 'join';
	$join = new Obj();
	$join->modulo = 'tipo_equipamento';
	$join->chave = 'id';
	$join->valor = 'nome';
	$join->tipo = 'select';
	$join->order_by = '';
$field->join = $join;
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Código de inventário';
$field->coluna = 'codigo';
$field->titulo_listagem = 'Cod.';
$field->dica = 'Código utilizado pela seção responsável pelo equipamento';
$field->default = 'ASC';
$field->pk = false;
$field->isnull = true;
$field->add = true;
$field->valida = false;
$field->edit = true;
$field->view = true;
$field->lista = true;
$field->filter = true;
$field->order = true;
$field->type = 'VARCHAR(255)';
$field->interaction = '';
$field->tipo = 'text';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Marca';
$field->coluna = 'equipamento_marca';
$field->pk = false;
$field->isnull = false;
$field->add = true;
$field->valida = false;
$field->edit = true;
$field->view = true;
$field->lista = true;
$field->filter = true;
$field->order = true;
$field->type = 'INT(11)';
$field->interaction = '';
$field->tipo = 'join';
	$join = new Obj();
	$join->modulo = 'equipamento_marca';
	$join->chave = 'id';
	$join->valor = 'nome';
	$join->tipo = 'select';
	$join->order_by = '';
$field->join = $join;
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Modelo';
$field->coluna = 'modelo';
$field->pk = false;
$field->isnull = false;
$field->add = true;
$field->valida = false;
$field->edit = true;
$field->view = true;
$field->lista = true;
$field->filter = true;
$field->order = true;
$field->type = 'VARCHAR(255)';
$field->interaction = '';
$field->tipo = 'text';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Número de Série';
$field->coluna = 'numero_serie';
$field->pk = false;
$field->isnull = false;
$field->add = true;
$field->valida = false;
$field->edit = true;
$field->view = true;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'VARCHAR(255)';
$field->interaction = '';
$field->tipo = 'text';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Local';
$field->coluna = 'local';
$field->dica = 'Local físico do equipamento';
$field->pk = false;
$field->isnull = false;
$field->add = true;
$field->valida = true;
$field->edit = true;
$field->view = true;
$field->lista = true;
$field->filter = true;
$field->order = true;
$field->type = 'INT(11)';
$field->interaction = '';
$field->tipo = 'plugin';
	$plugin = new Obj();
	$plugin->name = 'fcfar_local';
	$plugin->params = array(
		'root' => 'root',
	);
$field->plugin = $plugin;
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Detalhe do Local';
$field->coluna = 'local_detalhe';
$field->pk = false;
$field->isnull = false;
$field->add = true;
$field->valida = false;
$field->edit = true;
$field->view = true;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'VARCHAR(255)';
$field->interaction = '';
$field->tipo = 'text';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Patrimônio';
$field->coluna = 'patrimonio';
$field->pk = false;
$field->isnull = true;
$field->add = true;
$field->valida = false;
$field->edit = true;
$field->view = true;
$field->lista = true;
$field->filter = true;
$field->order = true;
$field->type = 'VARCHAR(255)';
$field->interaction = '';
$field->tipo = 'text';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'A.I.';
$field->coluna = 'ai';
$field->pk = false;
$field->isnull = true;
$field->add = true;
$field->valida = false;
$field->edit = true;
$field->view = true;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'VARCHAR(255)';
$field->interaction = '';
$field->tipo = 'text';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Custo';
$field->coluna = 'custo';
$field->pk = false;
$field->isnull = true;
$field->add = true;
$field->valida = false;
$field->edit = true;
$field->view = true;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'DOUBLE';
$field->interaction = '';
$field->tipo = 'price';
$field->formato = 'real';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Status';
$field->coluna = 'status';
$field->pk = false;
$field->isnull = false;
$field->add = true;
$field->valida = true;
$field->edit = true;
$field->view = true;
$field->lista = true;
$field->filter = true;
$field->order = true;
$field->type = 'INT';
$field->interaction = '';
$field->tipo = 'select';
$field->valores = array(
	'1' => 'No estoque',
	'2' => 'Em uso',
	'3' => 'Emprestado',
	'4' => 'Consumido',
	'5' => 'Em manutenção',
	'6' => 'Na assistência técnica',
	'7' => 'Não localizado',
	'8' => 'Baixa',
);
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Vinculado ao estoque como...';
$field->coluna = 'vinculo_material';
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
$field->interaction = 'insertonly';
$field->tipo = 'join';
	$join = new Obj();
	$join->modulo = 'material';
	$join->chave = 'id';
	$join->valor = 'nome';
	$join->select2 = true;
	$join->tipo = 'select';
	$join->order_by = 'nome';
$field->join = $join;
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'É equipamento de outra seção / terceiros?';
$field->coluna = 'externo';
$field->dica = 'Equipamentos de responsabilidade externa, sem relação com o inventário local da seção. Ex: Computadores ou monitores patrimoniados de outras seções.';
$field->pk = false;
$field->isnull = false;
$field->add = true;
$field->valida = true;
$field->edit = true;
$field->view = true;
$field->lista = false;
$field->filter = true;
$field->order = false;
$field->type = 'INT';
$field->interaction = '';
$field->tipo = 'radio';
$field->valores = array(
	'0' => 'não',
	'1' => 'sim',
);
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Criado Em';
$field->coluna = 'created_on';
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
$field->mask = '99/99/9999';
$field->tipo = 'date';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Observações';
$field->coluna = 'observacao';
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
$field->rows = 4;
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
	$join->order_by = '';
$field->join = $join;
$module->campo[$field->coluna] = $field;

/*==========================================*/

/* GRID FOR THE FORM LAYOUT ========================================================================================= */

$grid = array();

$grid[] = array('8.end');
$grid[] = array('9');
$grid[] = array('3','3','3','3');
$grid[] = array('12');
$grid[] = array('6','3','3');
$grid[] = array('3','3','6');
$grid[] = array('8');
$grid[] = array('12');

$module->grid = $grid;

/* MODULE LIST BUTTONS ============================================================================================== */

$button = new Obj();
$button->value = 'Manutenções Agendadas';
$button->modulo = 'equipamento_manutencao';
$button->modulo_fk = 'equipamento';
$button->key = 'id';
$button->view = false;
$button->show = false;
$button->subsection = true;
$button->autoload = false;
$module->button[] = $button;

$button = new Obj();
$button->value = 'Duplicar';
$button->custom = true;
$button->code = '
	$code = "<a class=\'button tiny radius no-margin trigger-duplicar-equipamento\' href=\'".$id."\'>Duplicar</a>";
';
$module->button[] = $button;

/* FUNÇÕES AUXILIARES =============================================================================================== */

if(!function_exists('equipamento_pre_insert'))
{
	function equipamento_pre_insert () // there is nothing to get as a parameter, right?
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('equipamento_pos_insert'))
{
	function equipamento_pos_insert ($obj) // active just inserted object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------

		criaAgendamentosIniciais($obj);

	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('equipamento_pre_update'))
{
	function equipamento_pre_update ($obj) // active object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('equipamento_pos_update'))
{
	function equipamento_pos_update ($obj) // active updated object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------

		criaMovimentacao($obj);

	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('equipamento_pre_delete'))
{
	function equipamento_pre_delete ($obj) // active object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('equipamento_pos_delete'))
{
	function equipamento_pos_delete ($obj) // active deleted object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('equipamento_pre_list'))
{
	function equipamento_pre_list () // nothing to be passed here...
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('equipamento_pos_list'))
{
	function equipamento_pos_list ($ids) // ids of the listed elements
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('equipamento_notifications'))
{
	function equipamento_notifications ($type = '')
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('equipamento_overview'))
{
	function equipamento_overview ($foo)
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}


?>