<?

/* ================================================================================================================== */
/* DBO DEFINITION FILE FOR MODULE 'config' ====================================== AUTO-CREATED ON 31/03/2015 12:49:14 */
/* ================================================================================================================== */



/* GENERAL MODULE DEFINITIONS ======================================================================================= */

$module = new Obj();
$module->modulo = 'config';
$module->tabela = 'config';
$module->titulo = 'Configuração';
$module->titulo_plural = 'Configurações';
$module->genero = 'a';
$module->paginacao = '20';
$module->update = true;
$module->delete = true;
$module->insert = 'Nova Configuração';
$module->preload_insert_form = true;
$module->auto_view = true;
$module->order_by = '2';

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
$field->titulo = 'Nome da Seção';
$field->coluna = 'nome_secao';
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
$field->tipo = 'text';
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Nome curto da seção';
$field->coluna = 'nome_curto_secao';
$field->pk = false;
$field->isnull = false;
$field->add = true;
$field->valida = true;
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
$field->titulo = 'Gênero da seção';
$field->coluna = 'genero';
$field->pk = false;
$field->isnull = false;
$field->add = true;
$field->valida = true;
$field->edit = true;
$field->view = true;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'VARCHAR(255)';
$field->interaction = '';
$field->tipo = 'radio';
$field->valores = array(
	'f' => 'Feminino',
	'm' => 'Masculino',
);
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Nome do remetente';
$field->coluna = 'from_name';
$field->pk = false;
$field->isnull = false;
$field->add = true;
$field->valida = true;
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
$field->titulo = 'E-mail do remetente';
$field->coluna = 'from_mail';
$field->pk = false;
$field->isnull = false;
$field->add = true;
$field->valida = true;
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
$field->titulo = 'Rodapé do e-mail';
$field->coluna = 'mail_footer';
$field->dica = 'Suporta tags Html';
$field->pk = false;
$field->isnull = false;
$field->add = true;
$field->valida = true;
$field->edit = true;
$field->view = true;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'TEXT';
$field->interaction = '';
$field->tipo = 'textarea-rich';
$field->rows = 10;
$module->campo[$field->coluna] = $field;

/*==========================================*/

$field = new Obj();
$field->titulo = 'Local padrão de entrada de equipamentos';
$field->coluna = 'local_entrada_equipamentos';
$field->dica = 'Local padrão onde os equipamentos são enviados quando chegam para manutenção. Ex: Sala do Suporte.';
$field->pk = false;
$field->isnull = true;
$field->add = true;
$field->valida = false;
$field->edit = true;
$field->view = true;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'INT';
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
$field->titulo = 'Local de saída de equipamentos';
$field->coluna = 'local_saida';
$field->dica = 'Local externo aos locais da Faculdade. Ex: Externo à instituição.';
$field->pk = false;
$field->isnull = true;
$field->add = true;
$field->valida = false;
$field->edit = true;
$field->view = true;
$field->lista = false;
$field->filter = false;
$field->order = false;
$field->type = 'INT';
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

/* GRID FOR THE FORM LAYOUT ========================================================================================= */

$grid = array();

$grid[] = array('Dados da seção utilizadora do sistema');
$grid[] = array('12');
$grid[] = array('6','6');
$grid[] = array('Dados do e-mail que o sistema envia');
$grid[] = array('6','6');
$grid[] = array('12');
$grid[] = array('Dados para controle de manutenção de equipamentos');
$grid[] = array('12');
$grid[] = array('12');

$module->grid = $grid;

/* MODULE LIST BUTTONS ============================================================================================== */

/* FUNÇÕES AUXILIARES =============================================================================================== */

if(!function_exists('config_pre_insert'))
{
	function config_pre_insert () // there is nothing to get as a parameter, right?
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('config_pos_insert'))
{
	function config_pos_insert ($obj) // active just inserted object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('config_pre_update'))
{
	function config_pre_update ($obj) // active object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('config_pos_update'))
{
	function config_pos_update ($obj) // active updated object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------

		setMessage("<div class='success'>Configurações atualizadas com sucesso.</div>");
		header("Location: dbo_admin.php?dbo_mod=config&dbo_update=1");
		exit();

	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('config_pre_delete'))
{
	function config_pre_delete ($obj) // active object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('config_pos_delete'))
{
	function config_pos_delete ($obj) // active deleted object
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('config_pre_list'))
{
	function config_pre_list () // nothing to be passed here...
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------

		header("Location: dbo_admin.php?dbo_mod=config&dbo_update=1");
		exit();

	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('config_pos_list'))
{
	function config_pos_list ($ids) // ids of the listed elements
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('config_notifications'))
{
	function config_notifications ($type = '')
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}

if(!function_exists('config_overview'))
{
	function config_overview ($foo)
	{ global $dbo;
	// ----------------------------------------------------------------------------------------------------------



	// ----------------------------------------------------------------------------------------------------------
	}
}


?>