<?
/*

DBO - Database Object
Author: José Eduardo Biasioli
Date: 10/06/2011
Description: Interface para criação de formularios automatizados e facil comunicação com o banco de dados de uma forma simples.

*/

/*
* ===============================================================================================================================================
* Globais =======================================================================================================================================
* ===============================================================================================================================================
*/

//caminho do arquivo principal dbo.php
define(DBO_CORE_PATH, dirname(__FILE__));

//incluindo o arquivo de funções especificas do dbo
require_once(DBO_CORE_PATH.'/dbo_core_functions.php');

//caminho da pasta mãe, contendo a pasta core e definicoes
define(DBO_PATH, substr(DBO_CORE_PATH, 0, strlen(DBO_CORE_PATH)-5));

/*$dbo_html_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', DBO_PATH);
define (DBO_HTML_PATH, (($_SERVER['SERVER_PROTOCOL'] == "HTTP/1.1")?('http://'):('')).$_SERVER['HTTP_HOST']."/".substr($dbo_html_path, 1, strlen($dbo_html_path)));*/
//define (DBO_HTML_PATH, 'http://empregos.araraquara.com/admin/dbo');

define(DBO_IMAGE_UPLOAD_PATH, DBO_PATH."/upload/images");
define(DBO_FILE_UPLOAD_PATH, DBO_PATH."/upload/files");
define(DBO_IMAGE_HTML_PATH, DBO_URL."/upload/images");
define(DBO_FILE_HTML_PATH, DBO_URL."/upload/files");

/* SALTS */

define(SALT_DBO_AUTO_ADMIN_TOGGLE_ACTIVE, '0A89SD7UF0ASDFA#@');

/*
* ===============================================================================================================================================
* ===============================================================================================================================================
* Objeto Genérico ===============================================================================================================================
* ===============================================================================================================================================
* ===============================================================================================================================================
*/
class Obj {}

/*
* ===============================================================================================================================================
* ===============================================================================================================================================
* DBO ===========================================================================================================================================
* ===============================================================================================================================================
* ===============================================================================================================================================
*/
class Dbo extends Obj
{

	var $__ipp_start = '0';
	var $__pag = '1';
	var $__data = array();
	var $__black_list = array();
	var $__table;
	var $__form_acao;
	var $__chave_array = array();
	var $__valor_array = array();
	var $__ipp;
	var $__size;
	var $__res;
	var $__module_scheme;
	var $__ok;
	var $__class;
	var $__fixos;
	var $__grid;
	var $__rest;
	var $__user_pagination;
	var $__order;
	var $__filter_scheme = array();
	var $__mid;
	var $__inativo = false;
	var $__order_by = array();
	var $__listed_elements = array();
	var $__custom_query = false;
	var $__total = false;
	var $__update_id = false;

	//construtor -------------------------------------------------------------------------------------------------------------------------------

	function __construct ($modulo = '')
	{

		//Instancia virtual do Dbo para qualquer tabela.
		if($modulo) { $this->__table = $modulo; }
		if($this->__table) { $this->__class = $this->__table; }


		$this->__black_list[] = 'id';
		$this->__black_list[] = '__black_list';
		$this->__black_list[] = '__table';
		$this->__black_list[] = '__form_acao';
		$this->__black_list[] = '__chave_array';
		$this->__black_list[] = '__valor_array';
		$this->__black_list[] = '__ipp';
		$this->__black_list[] = '__ipp_start';
		$this->__black_list[] = '__pag';
		$this->__black_list[] = '__size';
		$this->__black_list[] = '__res';
		$this->__black_list[] = '__module_scheme';
		$this->__black_list[] = '__ok';
		$this->__black_list[] = '__class';
		$this->__black_list[] = '__fixos';

		$this->__pag = $_GET['pag'] ? $_GET['pag'] : $this->__pag;
		$this->__size = 0;

		//carregando as definições do modulo (se existir)
		$file = DBO_PATH.'/_dbo_'.strtolower($this->__class).".php";
		if(file_exists($file))
		{
			$file_code = file_get_contents($file);
			ob_start();
			eval("?>".$file_code."<?");
			$resttt = ob_get_clean();
			$this->__module_scheme = $module;
			$this->__table = $this->__module_scheme->tabela;
		}

		//cria a grade de exibição de formularios, se existir
		if(sizeof($this->__module_scheme->grid))
		{
			$this->makeGrid($this->__module_scheme->grid);
		}

		//seta as restricoes globais do modulo, se existir.
		if(strlen($this->__module_scheme->restricao))
		{
			eval($this->__module_scheme->restricao);
			//$this->__rest = trim($rest)." ";
			$this->__rest = trim($rest);
		}

		//verifica se o modulo tem campo inativo, e seta a variavel
		if($this->__module_scheme)
		{
			foreach($this->__module_scheme->campo as $key => $value)
			{
				if($value->default)
				{
					$this->__order_by[$value->coluna] = $value->default;
					break;
				}
			}
		}

		//verifica se o modulo tem campo inativo, e seta a variavel
		if($this->__module_scheme)
		{
			foreach($this->__module_scheme->campo as $key => $value)
			{
				if($value->coluna == 'inativo')
				{
					$this->__inativo = true;
					break;
				}				
			}
		}

		$this->__ok = true;
	} // __construct()

	//pega uma lista de atributos multivalorados -----------------------------------------------------------------------------------------------------------

	function getMulti($field, $type = 'select', $active = '-1')
	{
		$source = $this->__module_scheme->campo[$field]->valores;
		$return = '';
		if(is_array($source))
		{
			foreach($source as $key => $value)
			{
				if($type == 'select')
				{
					$return .= "<option value=\"".$key."\" ".(($active == $key)?('selected'):('')).">".$value."</option>\n";
				}
			}
		}
		return $return;
	}

	//pega um valor multivalorado -----------------------------------------------------------------------------------------------------------

	function getValue($field, $active)
	{
		$source = $this->__module_scheme->campo[$field]->valores;
		if(is_array($source))
		{
			return $source[$active];
		}
	}

	//função magica para tratar atribuição --------------------------------------------------------------------------------------------------------

	public function __get ($name)
	{
		
		if($name == 'id')
		{
			return $this->__data[$this->getPK()];
		}
		elseif($name[0] == '_' && $name[1] == '_' && $name[2] == '_')
		{
			$partes_aux = explode("___", $name);

			foreach($partes_aux as $chave => $valor)
			{
				if(strlen($valor))
				{
					$partes[] = $valor;
				}
			}

			$atual = $this;

			foreach($partes as $chave => $valor)
			{
				if($valor == end($partes))
				{
					return $obj->{$valor};
				}
				else
				{
					$obj = $atual->getJoinModule($valor);
					$obj->{$atual->getJoinKey($valor)} = $atual->{$valor};
					$obj->load();
					$atual = $obj;
				}
			}

		}
		return $this->__data[$name];
	}

	//cria uma nova instancia de si mesmo -----------------------------------------------------------------------------------------------------------

    public function __set($name, $attr)
	{
		if($name == 'id')
		{
			$this->__data[$this->getPK()] = $attr;
		}
		else
		{
			$this->__data[$name] = $attr;
		}
    }
	//cria uma nova instancia de si mesmo -----------------------------------------------------------------------------------------------------------

	function newSelf()
	{
		if(get_class($this) == 'Dbo')
		{
			$obj = new Dbo($this->__class);
		}
		else
		{
			$classe = get_class($this);
			$obj = new $classe;
		}
		return $obj;
	}

	//checa se o campo atual é fixo -----------------------------------------------------------------------------------------------------------------

	private function isFixo($var)
	{
		if($this->__fixos[$var]) return $this->__fixos[$var];
		return false;
	}

	//checa se o campo em questão pertence a este modulo --------------------------------------------------------------------------------------------

	function hasField($field)
	{
		if($this->__module_scheme->campo[$field])
			return true;
		return false;
	}
	
	//função para setar um array de dados, de forma inteligente, conferindo se o modulo possui tal campo --------------------------------------------

	function smartSet($array)
	{
		foreach($array as $key => $value)
		{
			if($this->hasField($key))
			{
				$this->{$key} = $value;
			}
		}
	}

	//checa se o perfil ativo tem acesso ao campo ---------------------------------------------------------------------------------------------------

	private function perfilTemAcessoCampo($perfil)
	{
		if(!is_array($perfil) && strlen($perfil))
		{
			$aux = $perfil;
			$perfil = array();
			$perfil[] = $aux;
		}
		if(!sizeof($perfil)) { return true; }
		else
		{
			foreach($perfil as $perf)
			{
				if(logadoNoPerfil($perf))
				{
					return true;
				}
			}
		}
		return false;
	}

	//cria o grid para exibicao dos campos do formulario --------------------------------------------------------------------------------------------

	function makeGrid($var)
	{
		foreach($var as $chave => $item)
		{
			if(is_numeric($chave))
			{
				$grid[] = "|-";
				foreach($item as $valor)
				{
					$grid[] = $valor;
				}
				$grid[] = "-|";
			}
			else { //contextos view/update/insert
				foreach($item as $valor)
				{
					$grid[$chave][] = "|-";
					foreach($valor as $valor2)
					{
						$grid[$chave][] = $valor2;
					}
					$grid[$chave][] = "-|";
				}
			}
		}
		$this->__grid = $grid;
	}

	//cria o grid para exibicao dos campos do formulario --------------------------------------------------------------------------------------------

	private function hasGrid($contexto = '')
	{
		if(strlen($contexto)) //setou contexto
		{
			if(sizeof($this->__grid[$contexto]))
			{
				return $this->__grid[$contexto];
			}
			elseif(sizeof($this->__grid)) //se  nao tiver tenta a padrão
				return $this->__grid;
			return false;
		}
		else { //pega grid geral se tiver...
			if(sizeof($this->__grid))
				return $this->__grid;
			return false;
		}
	}

	//gera uma matriz com os valores fixos para o modulo atual --------------------------------------------------------------------------------------

	private function makeFixos($var)
	{
		if($var)
		{
			$var = base64_decode($var);
			$variaveis = explode("||", $var);
			foreach($variaveis as $chave => $valor)
			{
				list($campo, $valor) = explode("::", $valor);
				$this->setFixo($campo, $valor);
			}
		}
	}

	//Adiciona um campo fixo --------------------------------------------------------------------------------------

	function setFixo($campo, $valor)
	{
		$this->__fixos[$campo] = $valor;
	}

	//pega o nome de execução do módulo atual -------------------------------------------------------------------------------------------------------

	function getModule ()
	{
		return $this->__module_scheme->modulo;
	}

	//pega uma instancia do modulo do join do campo passado para o objeto atual----------------------------------------------------------------------

	public function getJoinModule ($campo)
	{
		return new Dbo($this->__module_scheme->campo[$campo]->join->modulo);
	}

	//pega a chave estrangeira do do join para o campo atual ----------------------------------------------------------------------------------------

	public function getJoinKey ($campo)
	{
		return $this->__module_scheme->campo[$campo]->join->chave;
	}

	//gera uma matriz com os valores fixos para o modulo atual --------------------------------------------------------------------------------------

	function makeBreadCrumb()
	{
		global $_SESSION;
		$parents = array();

		$mid = $this->getMid();
		$fixo = $_GET['dbo_fixo'];

		while($this->getModuleParent($mid, $fixo))
		{
			$parent_id = $this->getModuleParent($mid, $fixo);
			$mid = $parent_id;
			$fixo = $_SESSION['dbo_mid'][$parent_id][fixo];
			$parents[] = $this->getModuleParentData($mid);
		}

		//preparando para gerar o bread_crumb
		$fixo = $this->decodeFixos($_GET['dbo_fixo']);
		foreach($fixo as $key => $value)
		{
			$fixo_campo = $key;
			$fixo_valor = $value;
		}
		$bread_crumb = array();
		$obj = $this;
		$atual = $this;
		$i = 0;

		foreach($parents as $mid => $module_data)
		{
			$obj = $obj->getJoinModule($fixo_campo);
			$obj->{$atual->getJoinKey($fixo_campo)} = $fixo_valor;
			$obj->load();

			$bread_crumb[$i]['label'] = $atual->__module_scheme->campo[$fixo_campo]->titulo;
			$bread_crumb[$i]['valor'] = $obj->{$atual->__module_scheme->campo[$fixo_campo]->join->valor};
			$bread_crumb[$i]['key'] = $fixo_valor;
			$bread_crumb[$i]['modulo'] = $obj->__module_scheme->modulo;
			$bread_crumb[$i]['fixo'] = $module_data[fixo];
			$bread_crumb[$i]['mid'] = $module_data[mid];

			$fixo = $this->decodeFixos($module_data[fixo]);
			foreach($fixo as $key => $value)
			{
				$fixo_campo = $key;
				$fixo_valor = $value;
			}

			$atual = $obj;

			$i++;
		}

		$bread_crumb = array_reverse($bread_crumb);
		echo "<ul class='panel radius bread-crumb'>";
		foreach($bread_crumb as $chave => $obj)
		{
			?>
			<li>
			<a class="label radius" href='<?= $this->keepUrl(array('dbo_mod='.$obj['modulo']."&mid=".$obj[mid]."&dbo_fixo=".$obj[fixo], "!pag&!dbo_new!&!dbo_update&!dbo_delete&!dbo_view")) ?>'><?= $obj['label'] ?></a><a class='valor' href='<?= $this->keepUrl(array('dbo_view='.$obj['key'].'&dbo_mod='.$obj['modulo']."&mid=".$obj[mid]."&dbo_fixo=".$obj[fixo], "!pag&!dbo_new!&!dbo_update&!dbo_delete")) ?>'><?= $obj['valor'] ?></a>
			</li>
			<?
		}
		echo "</ul><!-- breadcrumbs -->";
	}

	//carrega os dados -------------------------------------------------------------------------------------------------------------------------------

	function load ()
	{
		//$sql = "SELECT * FROM ".$this->__table." WHERE id = '".((!get_magic_quotes_gpc())?(dboEscape($this->id)):($this->id))."'";
		$sql = "SELECT * FROM ".$this->__table." WHERE ".$this->getPK()." = '".dboEscape($this->id)."'";

		if(!$this->__res = dboQuery($sql)) {
			echo "<div class='mysql-error'>MYSQL ERROR: ".mysql_error()."<br>SQL: ".$sql."</div>";
		}

		if(mysql_affected_rows())
		{
			$this->__size = mysql_affected_rows();
			$lin = mysql_fetch_assoc($this->__res);
			foreach($lin as $chave => $valor)
			{
				$this->$chave = $valor;
			}
		}
	}

	// carrega uma query custom ---------------------------------------------------------------------------------------------------------------------------

	function query($sql)
	{
		$this->__custom_query = $sql;
		if($this->__user_pagination)
		{
			$sql .= " ".$this->getSQLipp();
		}
		if(!$this->__res = dboQuery($sql))
		{
			echo "<div class='mysql-error'>MYSQL ERROR: ".mysql_error()."<br>SQL: ".$sql."</div>";
		}
		if(mysql_affected_rows())
		{
			/* salvando o total com LIMIT */
			$this->__size = mysql_affected_rows();

			/* salvando o total de registros sem o LIMIT no objeto */
			$sql = "select FOUND_ROWS()";
			$res = mysql_query($sql);
			$this->__total = mysql_result($res, 0);
			
			$lin = mysql_fetch_assoc($this->__res);
			foreach($lin as $chave => $valor)
			{
				$this->$chave = $valor;
			}
		}
	}

	//carrega todos os dados -------------------------------------------------------------------------------------------------------------------------------

	function loadAll ($restricoes = '')
	{
		$this->makeArrays();

		$sql  = "SELECT * FROM ".$this->__table.( (sizeof($this->__chave_array) || $this->id) ? ' WHERE ' : '' );

		if(sizeof($this->__chave_array) || $this->id)
		{
			foreach($this->__chave_array as $chave => $valor)
			{
				$aux[] = $valor.$this->trataValor($this->__valor_array[$chave]);
			}
			if($this->id)
			{
				$aux[] = "id = '".dboescape($this->id)."'";
			}
			$sql .= implode(" AND ", $aux);
		}

		if($restricoes)
		{
			if(strtolower(substr(trim($restricoes), 0, 5)) == 'order' || strtolower(substr(trim($restricoes), 0, 5)) == 'limit' || strtolower(substr(trim($restricoes), 0, 5)) == 'where') {}
			else
			{
				if(!sizeof($this->__chave_array))
				{
					$sql .= " WHERE ";
				}
				else
				{
					$sql .= " AND ";
				}
			}

			$sql .= " ".$restricoes;
		}

		if($this->__user_pagination)
		{
			$sql .= " ".$this->getSQLipp();
		}

		if(!$this->__res = dboQuery($sql))
		{
			echo "<div class='mysql-error'>MYSQL ERROR: ".mysql_error()."<br>SQL: ".$sql."</div>";
		}
		if(mysql_affected_rows())
		{
			$this->__size = mysql_affected_rows();
			$lin = mysql_fetch_assoc($this->__res);
			foreach($lin as $chave => $valor)
			{
				$this->$chave = $valor;
			}
		}
		$this->clearArrays();
	}

	//retorna a tabela do modulo atual ----------------------------------------------------------------------------------------------------

	function getTable()
	{
		return $this->__module_scheme->tabela;
	}
	
	//retorna a tabela do modulo atual ----------------------------------------------------------------------------------------------------

	function getFieldName($field)
	{
		return $this->__module_scheme->campo[$field]->titulo;
	}
	
	//pega o campo que é a chave primária do modulo ---------------------------------------------------------------------------------------

	function getPK()
	{
		if(is_array($this->__module_scheme->campo))
		{
			foreach($this->__module_scheme->campo as $key => $obj)
			{
				if($obj->pk == 1)
				{
					return $key;
				}
			}
		}
		else
		{
			return 'id';
		}
	}
	
	//pega o tipo de chave primária do módulo ---------------------------------------------------------------------------------------

	function getPKType()
	{
		if($this->__module_scheme->campo[$this->getPK()]->type == 'INT NOT NULL auto_increment')
		{
			return 'AUTO_INCREMENT';
		}
	}
	
	//lista os dados carregados no objeto (do while) --------------------------------------------------------------------------------------

	function fetch ()
	{
		if($lin = mysql_fetch_assoc($this->__res)) {
			foreach($lin as $chave => $valor)
			{
				$this->$chave = $valor;
			}
			return true;
		}
		return false;
	}

	//cria arrays para composição da query ------------------------------------------------------------------------------------------------

	function makeArrays ()
	{
		$this->clearArrays();
		foreach($this->__data as $chave => $valor)
		{

			if(!in_array($chave, $this->__black_list))
			{
				//$this->__chave_array[] = ((!get_magic_quotes_gpc())?(dboEscape($chave)):($chave));
				//$this->__valor_array[] = ((!get_magic_quotes_gpc())?(dboEscape($valor)):($valor));

				/* precisa verificar se é nulo, e setar, caso o campo esteja em branco. */
				if($this->__module_scheme->campo[$chave]->isnull === true && trim($valor) == '')
				{
					$valor = $this->null();
				}

				$this->__chave_array[] = @dboEscape($chave);
				$this->__valor_array[] = @dboEscape($valor);
			}
		}
	}

	//salva os dados no banco de dados ----------------------------------------------------------------------------------------------------

	function save ()
	{
		$this->makeArrays();

		$sql = "INSERT INTO ".$this->__table." (".implode(",", $this->__chave_array).") VALUES ('".implode("','",$this->__valor_array)."')";

		//tratamento de NOW(), NULL, etc.
		$sql = $this->remakeSql($sql);

		if(dboQuery($sql))
		{
			if($this->getPKType() == 'AUTO_INCREMENT')
			{
				$this->id = mysql_insert_id();
			}
			return $this->id;
		}
		else
		{
			echo mysql_error();
			return false;
		}
	}

	//atualiza o registro carregado no objeto no momemtno ---------------------------------------------------------------------------------

	function update ($restricoes = '')
	{
		$this->makeArrays();

		$sql = "UPDATE ".$this->__table." SET ";

		foreach($this->__chave_array as $chave => $valor)
		{
			$aux[] = $valor." = '".$this->__valor_array[$chave]."'";
		}
		$sql .= implode(", ", $aux);
		$sql .= " WHERE ".$this->getPK()." = '".(($this->__update_id)?($this->__update_id):($this->id))."' ".$restricoes;

		//tratamento de NOW(), etc.
		$sql = $this->remakeSql($sql);

		if(dboQuery($sql))
		{
			return $this->id;
		} else {
			echo "<div class='mysql-error'>MYSQL ERROR: ".mysql_error()."<br>SQL: ".$sql."</div>";
		}
		return false;
	}

	//salva no banco caso não eixta um registro carregado no objeto ----------------------------------------------------------------------

	function saveOrUpdate()
	{
		//se a chave for auto increment trata de uma forma
		if($this->getPKType() == 'AUTO_INCREMENT')
		{
			if($this->id)
			{
				return $this->update();
			}
			return $this->save();
		}
		else
		{
			//senão, verifica se a chave primaria já existe no banco
			$sql = "SELECT ".$this->getPK()." FROM ".$this->getTable()." WHERE ".$this->getPK()." = '".$this->id."'";
			dboQuery($sql);
			if(mysql_affected_rows())
			{
				return $this->update();
			}
			return $this->save();
		}
	}

	//funcao para identificar deletion engine

	function hasDeletionEngine()
	{
		if($this->hasField('deleted_by') || $this->hasField('deleted_on'))
		{
			return true;
		}
		return false;
	}

	//delete basico -----------------------------------------------------------------------------------------------------------------------

	function delete ()
	{
		//se houver o campo inativo, apenas da o update para 1
		/*if($this->hasInativo())
		{
			$sql = "UPDATE ".$this->__table." SET inativo = '1' WHERE ".$this->getPK()." = '".dboEscape($this->id)."'";
		}*/
		//se tiver o deletion engine, verificar se possui os campos e preencher.
		if($this->hasDeletionEngine())
		{
			$sql_parts = array();

			if($this->hasField('deleted_by')) { $sql_parts[] = " deleted_by = '".loggedUser()."' "; }
			if($this->hasField('deleted_on')) { $sql_parts[] = " deleted_on = NOW() "; }
			if($this->hasField('deleted_because')) { $sql_parts[] = " deleted_because = '".dboescape($_GET['deleted_because'])."' "; }

			$sql = "UPDATE ".$this->__table." SET ".implode(", ", $sql_parts)." WHERE ".$this->getPK()." = '".dboEscape($this->id)."'";
		}
		else
		{
			$sql = "DELETE FROM ".$this->__table." WHERE ".$this->getPK()." = '".dboEscape($this->id)."'";
		}
		if(dboQuery($sql)) { return $this->id; }
		echo "<div class='mysql-error'>MYSQL ERROR: ".mysql_error()."<br>SQL: ".$sql."</div>";
		return false;
	}

	//sobrecarga do header location, para adição de eventuais scripts ---------------------------------------------------------------------

	function myHeader($foo)
	{
		header($foo);
		exit();
	}

	//gera o src de um href para fazer download de arquivos enviados pelo sistema ---------------------------------------------------------

	function getDownloadLink($dados, $novo_nome = '')
	{
		list($nome, $arquivo, $mime, $tamanho) = explode("\n", $dados);
		return "<a href='".DBO_URL."/core/classes/download.php?name=".$nome."&file=".$arquivo."'>".(($novo_nome)?($novo_nome):($nome))."</a>";
	}

	//funcao de paginação para a listagem automatica --------------------------------------------------------------------------------------

	function pagination($ipp)
	{
		$this->__ipp = $ipp;
		$this->__user_pagination = TRUE;
	}

	//funcao de paginação para a listagem automatica --------------------------------------------------------------------------------------

	function splitter ($rest = '')
	{
		if(strlen($rest))
		{
			if(!preg_match('#^\s*WHERE\s*#i', $rest) && !preg_match('#^\s*ORDER\s*#i', $rest) && !preg_match('#^\s*LIMIT\s*#i', $rest))
			{
				$rest = "WHERE ".$rest." ";
			}
		}

		$splitter = '';
		if($this->__ipp)
		{
			$total = $this->total($rest);
			$ipp = $this->__ipp;
			$pag = $this->__pag;
			$total_paginas = ceil($total/$ipp);
			if($total_paginas == 1) { return; }
			$splitter  = "<div class='pagination-centered splitter'>";
			$splitter .= "<ul class='pagination'>";
			$splitter .= $this->getDeltaPag($pag, $total_paginas, 'first', '&laquo;');
			//$splitter .= $this->getDeltaPag($pag, $total_paginas, -1, '<');
			$splitter .= $this->getDeltaPag($pag, $total_paginas, -4);
			$splitter .= $this->getDeltaPag($pag, $total_paginas, -3);
			$splitter .= $this->getDeltaPag($pag, $total_paginas, -2);
			$splitter .= $this->getDeltaPag($pag, $total_paginas, -1);
			$splitter .= $this->getDeltaPag($pag, $total_paginas,  '0');
			$splitter .= $this->getDeltaPag($pag, $total_paginas,  1);
			$splitter .= $this->getDeltaPag($pag, $total_paginas,  2);
			$splitter .= $this->getDeltaPag($pag, $total_paginas,  3);
			$splitter .= $this->getDeltaPag($pag, $total_paginas,  4);
			//$splitter .= $this->getDeltaPag($pag, $total_paginas,  1, '>');
			$splitter .= $this->getDeltaPag($pag, $total_paginas, 'last', '&raquo;');
			$splitter .= "</ul>";
			$splitter .= "</div>";
		}
		return $splitter;
	}

	//mostra o comando para criação da tabela no banco ------------------------------------------------------------------------------------

	function showCreateTable()
	{
		$meta = $this->__module_scheme->campo;

		$create = "<h1>Tabela ".$this->__module_scheme->tabela." não existe</h1>";
		$create .= "<h2>Cole o código abaixo no seu phpMyAdmin para criar a tabela do módulo:</h2>";
		$create .= "<textarea READONLY style='width: 90%; height: 200px;'>CREATE TABLE ".$this->__module_scheme->tabela." (\n";
		foreach($meta as $chave => $campo)
		{
			$create .= "   ".$campo->coluna." ".$campo->type.", \n";
			if($campo->tipo == 'pk')
			{
				$pk = $campo->coluna;
			}
		}
		$create .= "PRIMARY KEY ( ".$pk." )\n";
		$create .= ") ENGINE = MYISAM DEFAULT CHARSET=utf8;</textarea>";
		echo $create;
	}

	//IMPLEMENTAR -- limpar sql injection -------------------------------------------------------------------------------------------------

	private function sanitize($foo)
	{
		return $foo;
	}

	//IMPLEMENTAR -- limpar valor para colocar no "value" do input ------------------------------------------------------------------------

	private function clearValue($foo)
	{
		return htmlspecialchars($foo);
	}

	//calcula a quantidade de paginas baseado na quantidade de itens por pagina do objeto -------------------------------------------------

	private function getDeltaPag ($atual, $total, $qtd, $symbol = '')
	{

		$symbol = $symbol ? $symbol : ($atual+$qtd);

		if($qtd == 'first') {
			$retorno .= "<li class='arrow ".(($atual == 1)?('unavailable'):(''))."'>";
			if  ($atual == 1) { $retorno .= "<a href='#'>".$symbol."</a>"; } //atual já é a first.
			else { $retorno .= "<a href='".$this->keepUrl('pag=1')."'>".$symbol."</a>"; }
			$retorno .= "</li>\n";
		}
		elseif ($qtd == 'last') {
			$retorno .= "<li class='arrow ".(($atual == $total)?('unavailable'):(''))."'>";
			if  ($atual == $total) { $retorno .= "<a href='#'>".$symbol."</a>"; } //atual já é a last.
			else { $retorno .= "<a href='".$this->keepUrl('pag='.$total)."'>".$symbol."</a>"; }
			$retorno .= "</li>\n";
		}
		elseif ($qtd == 0) { // pagina atual, sem delta-paginas
			$retorno .= "<li class='current'>";
			$retorno .= "<a href='#'>".$symbol."</a>";
			$retorno .= "</li>\n";
		}
		else {
			$diferenca = $atual+$qtd;
			if($diferenca > $total || $diferenca <= 0) {}
			else { $retorno .= "<li><a href='".$this->keepUrl('pag='.$diferenca)."'>".$symbol."</a></li>\n"; }
		}
		return $retorno;
	}

	//cria um id unico para guardar informações do modulo atual na sesssao ----------------------------------------------------------------

	function makeMid()
	{
		return base64_encode(time().rand(1,100000));
	}

	//cria um id unico para guardar informações do modulo atual na sesssao ----------------------------------------------------------------

	function setMid($mid)
	{
		global $_SESSION;
		$_SESSION['dbo_mid'][$mid]['modulo'] = $this->__class;
		$_SESSION['dbo_mid'][$mid]['mid'] = $mid;
		return $mid;
	}

	//pega o mid --------------------------------------------------------------------------------------------------------------------------

	function midTypeCheck($mid, $type)
	{
		global $_SESSION;
		global $_GET;

		if($_SESSION['dbo_mid'][$mid][modulo] == $type) return true;
		return false;
	}

	//pega o mid --------------------------------------------------------------------------------------------------------------------------

	function getMid()
	{
		global $_SESSION;
		global $_GET;

		if($_GET['mid'])
		{
			if(sizeof($_SESSION['dbo_mid'][$_GET['mid']]))
			{
				return $_GET['mid'];
			}
			return false;
		}
		return false;
	}

	//checa o pai --------------------------------------------------------------------------------------------------------------------------

	function getModuleParent($mid, $fixo)
	{
		global $_SESSION;
		foreach($_SESSION['dbo_mid'] as $key => $value)
		{
			if($key == $mid)
			{
				if(sizeof($_SESSION['dbo_mid'][$value['parent']]) && $value['fixo'] == $fixo) { return $_SESSION['dbo_mid'][$value['parent']][mid]; }
			}
		}
		return false;
	}

	//checa o pai --------------------------------------------------------------------------------------------------------------------------

	function getModuleParentData($mid)
	{
		global $_SESSION;
		return $_SESSION['dbo_mid'][$mid];
	}

	//checa o pai --------------------------------------------------------------------------------------------------------------------------
	function setModuleParent($mid, $fixo)
	{
		global $_SESSION;
		$new_mid = $this->makeMid();
		$this->setMid($new_mid);
		$_SESSION['dbo_mid'][$new_mid]['parent'] = $mid;
		$_SESSION['dbo_mid'][$new_mid]['fixo'] = $fixo;
		header('Location: '.$this->keepUrl('mid='.$new_mid));
		exit();
	}

	//checa o pai --------------------------------------------------------------------------------------------------------------------------
	function setOrderBy($order_by)
	{
		global $_SESSION;
		unset($_SESSION['dbo_mid'][$this->getMid()][order_by]);
		list($campo, $valor) = explode('::', $order_by);
		$_SESSION['dbo_mid'][$this->getMid()][order_by][$campo] = $valor;
	}

	//cria um complemento de SQL com a paginacao (IPP = itens por pagina )-----------------------------------------------------------------

	function getSQLipp ()
	{
		if($this->__ipp && !$this->isAutoOrdered()) { return "LIMIT ".($this->__pag-1)*$this->__ipp.",".$this->__ipp; }
		return '';
	}

	//cria um complemento de SQL com a restricao de inativo, se existir -------------------------------------------------------------------

	function getSQLInativo ()
	{
		/*if($this->__inativo) 
		{ 
			if($this->getModuleRestriction() || $this->getSQLFixos() || $this->getSQLFilters())
			{
				$ret = " AND ";
			}
			return $ret." inativo = '0' "; 
		}*/
		return false;
	}

	//cria um complemento de SQL com a restricao de deletado, se existir -------------------------------------------------------------------

	function getSQLDeletionEngine ()
	{
		if($this->hasDeletionEngine()) 
		{ 
			if($this->getModuleRestriction() || $this->getSQLFixos() || $this->getSQLFilters() || $this->getSQLInativo())
			{
				$ret = " AND ";
			}
			return $ret." deleted_by = 0 "; 
		}
		return false;
	}

	//cria um complemento de SQL com os fixos --------------------------------------------------------------------------------------------

	function getSQLfixos ()
	{
		if(sizeof($this->__fixos))
		{
			$count = 0;
			foreach($this->__fixos as $chave => $valor)
			{
				$count++;
				if((sizeof($this->__fixos)) == $count)
				{
					$partes[] = " ".$chave." = '".$valor."' ";
				}
			}
			$sql = implode("AND", $partes);
			return $sql." ";
		}
		return false;
	}

	//returns if the current data can be interactive with the user -----------------------------------------------------------------------

	function checkInteraction($module, $field, $data, $interaction_type)
	{
		global $no_user_interaction;

		$module_name = $module->__module_scheme->modulo;
		$field_name = $field->coluna;

		foreach($no_user_interaction as $forbidden)
		{
			if(
				$forbidden->module           == $module_name &&
				$forbidden->field            == $field_name &&
				$forbidden->data             == $data &&
				$forbidden->interaction_type == $interaction_type
			) { return false; }
		}
		return true;
	}

	//retorna a restricao do modulo caso esteja setada, sem o WHERE ----------------------------------------------------------------------

	function getModuleRestriction ()
	{
		if($this->__rest)
		{
			return trim(preg_replace('#^\s*WHERE\s*#i', '', $this->__rest))." ";
		}
		return false;
	}

	//retorna os filtros automaticos do modulo setados pelo usuário ----------------------------------------------------------------------

	function getSQLFilters ()
	{
		global $_SESSION;

		$filters = $_SESSION['dbo_mid'][$this->getMid()][filter];

		if(sizeof($filters))
		{
			foreach($filters as $coluna => $valor)
			{

				$tipo = $this->__filter_scheme[$coluna]->tipo;

				//para campos que tem algo escrito
				if($tipo == 'text' || $tipo == 'textarea' || $tipo == 'textarea-rich') {
					$sql_parts[] = $coluna." LIKE '%".$valor."%'";
				//para campos data (intervalo de valor)
				} elseif ($tipo == 'date') {
					$data_parts = '';
					$data_inicial = '';
					$data_final = '';

					$data_parts = explode('|---|', $valor);
					$data_inicial = ((strlen($data_parts[0]))?(dataSQL($data_parts[0])):(''));
					$data_final = ((strlen($data_parts[1]))?(dataSQL($data_parts[1])):(''));

					if(strlen($data_inicial) && strlen($data_final)) {
						$sql_parts[] = $coluna." BETWEEN '".$data_inicial."' AND '".$data_final."'";
					} elseif(strlen($data_inicial)) {
						$sql_parts[] = $coluna." >= '".$data_inicial."'";
					} elseif(strlen($data_final)) {
						$sql_parts[] = $coluna." <= '".$data_final."'";
					}
				//para todos os outros
				} else {
					$sql_parts[] = $coluna." = '".$valor."'";
				}

			}
			$sql_string = (($this->getModuleRestriction() || $this->getSQLfixos())?(" AND "):('')).implode(" AND ", $sql_parts);

			return $sql_string." ";
			
		}
		return false;
	}

	//retorna a ordenação automatica setada pelo usuário no loadAll ----------------------------------------------------------------------

	function getSQLOrder ()
	{
		if(!$this->isAutoOrdered())
		{
			$data = $this->getOrderBy();
			if($data)
			{
				foreach($data as $key => $value)
				{
					return " ORDER BY ".$key." ".$value." ";
				}
			}
		}
		else
		{
			return " ORDER BY order_by ASC ";
		}
	}

	//retorna true se o modulo tem campo inativo -----------------------------------------------------------------------------------------

	function hasInativo ()
	{
		return (($this->__inativo)?(true):(false));
	}

	//cria um scheme contendo somente os campos filtraveis -------------------------------------------------------------------------------

	function getMaxOrderBy()
	{
		$sql = "SELECT MAX(order_by) as max FROM ".$this->__module_scheme->tabela;
		$res = dboQuery($sql);
		$lin = mysql_fetch_object($res);
		return $lin->max;
	}

	//get max order by from active module ------------------------------------------------------------------------------------------------

	function makeFilterScheme ()
	{
		foreach($this->__module_scheme->campo as $chave => $campo)
		{
			if($campo->filter == TRUE)
			{
				$this->__filter_scheme[$chave] = $campo;
			}
		}
	}

	//mostra o botão de filtors na listagem, se houver filtos na pagina ativa. -----------------------------------------------------------

	function showFilterButton ()
	{
		if(sizeof($this->__filter_scheme))
		{
			ob_start();
			?>
				<a href='' class='filter-button dbo-button-aba button tiny radius secondary <?= ((sizeof($_SESSION['dbo_mid'][$this->getMid()][filter]))?('hidden'):('')) ?>'>Filtrar</a>
			<?
			$ob_result = ob_get_clean();
			return $ob_result;
		}
	}

	//mostra o botão de filtors na listagem, se houver filtos na pagina ativa. -----------------------------------------------------------

	function getFilterValue ($campo)
	{
		global $_SESSION;
		return (string)((strlen($_SESSION['dbo_mid'][$this->getMid()][filter][$campo]))?($_SESSION['dbo_mid'][$this->getMid()][filter][$campo]):(''));
	}

	//mostra o botão de filtors na listagem, se houver filtos na pagina ativa. -----------------------------------------------------------

	function showFilterBox ()
	{

		global $_SESSION;

		if(!sizeof($_SESSION['dbo_mid'][$this->getMid()][filter])) { $class_hidden = 'hidden'; }

		?>
			<div class="wrapper-filter-box <?= $class_hidden ?>">

				<div class="row full">
					<div class="large-12 columns"><hr></div>
				</div>
				
				<div class='row'>
					<div class='large-12 columns'>
						
						<h3>Filtros</h3>
						<form method='POST' action='<?= $this->keepUrl('!pag') ?>' id='form-dbo-filter' class="no-margin">
							<div class='row'>
							<?
								foreach($this->__filter_scheme as $chave => $campo)
								{
									if($campo->tipo == 'text' || $campo->tipo == 'textarea' || $campo->tipo == 'textarea-rich' || $campo->tipo == 'price')
									{
										?>
										<div class='item columns large-3'>
											<label><?= $campo->titulo ?></label>
											<div class='input'><input type='text' name='<?= $campo->coluna ?>' value="<?= htmlspecialchars($this->getFilterValue($campo->coluna)) ?>"></div>
										</div><!-- item -->
										<?
									} elseif ($campo->tipo == 'select' || $campo->tipo == 'radio') {
										?>
										<div class='item columns large-3'>
											<label><?= $campo->titulo ?></label>
											<div class='input'>
												<select name='<?= $campo->coluna ?>'>
												<option value=''></option>
												<?
													foreach($campo->valores as $key => $value)
													{
														$key = (string)$key;
														?><option value='<?= $key ?>' <?= (($this->getFilterValue($campo->coluna) === $key)?('SELECTED'):('')) ?>><?= $value ?></option><?
													}
												?>
												</select>
											</div>
										</div><!-- item -->
										<?
									} elseif ($campo->tipo == 'join') {
										if(!$this->isFixo($campo->coluna))
										{
											?>
											<div class='item columns large-3'>
												<label><?= $campo->titulo ?></label>
												<div class='input'>
													<?
														$jobj = new dbo($campo->join->modulo);
														$rest = '';
														if($campo->restricao) { eval($campo->restricao.";"); }
														$rest .= " ORDER BY ".(($campo->join->order_by)?($campo->join->order_by):($campo->join->valor))." ";
														$jobj->loadAll($rest);
					
													?>
													<select name='<?= $campo->coluna ?>'>
													<option value=''></option>
													<?
														do {
															$key = $jobj->{$campo->join->chave};
															?><option value='<?= $key ?>' <?= (($this->getFilterValue($campo->coluna) === $key)?('SELECTED'):('')) ?>><?= $jobj->{$campo->join->valor} ?></option><?
														}while($jobj->fetch());
													?>
													</select>
												</div>
											</div><!-- item -->
											<?
										}
									} elseif ($campo->tipo == 'date') {
										$data_aux = '';
										$data_inicial = '';
										$data_final = '';
										$data_aux = $this->getFilterValue($campo->coluna);
										$data_aux = explode('|---|', $data_aux);
										$data_inicial = ((strlen($data_aux[0]))?($data_aux[0]):(''));
										$data_final = ((strlen($data_aux[1]))?($data_aux[1]):(''));
										?>
										<div class='columns large-3'>
											<label><?= $campo->titulo ?></label>
											<div class='input filter-type-data'>
												<div><input type='text' placeholder="de" class='data-inicial datepick' name='<?= $campo->coluna ?>_dbo_inicial' value='<?= $data_inicial ?>'></div>
												<div><input type='text' placeholder="até" class='data-final datepick' name='<?= $campo->coluna ?>_dbo_final' value='<?= $data_final ?>'></div>
												<input type='hidden' class='data-montada clear-field' name='<?= $campo->coluna ?>' value='<?= $this->getFilterValue($campo->coluna) ?>'>
											</div>
										</div><!-- item -->
										<?
									}
								}
							?>
							</div><!-- row -->
					
							<div class='row'>
								<div class='item columns large-12 text-right'>
									<span class='input'><input type='submit' class="button no-margin small radius" value='Filtrar' accesskey='s'> <input type='button' class="button no-margin small radius" value='Limpar' id='dbo-button-limpar-filtros'> <a href='' class='dbo-button-aba no-margin filter-button-close button small secondary radius special-button'><i class="fi-x"></i> Fechar</a></span>
								</div><!-- item -->
							</div><!-- row -->
					
							<input type='hidden' name='__dbo_filter_flag' value='1'/>
						</form>
					

					</div>
				</div><!-- col -->

				<div class="row full">
					<div class="large-12 columns"><hr></div>
				</div>
				
			</div><!-- row -->
		<?
	}

	//mostra o botão de filtors na listagem, se houver filtos na pagina ativa. -----------------------------------------------------------

	function setFilters ($vars)
	{
		global $_SESSION;

		//limpa os filtros que já estão na sessao.
		unset($_SESSION['dbo_mid'][$this->getMid()][filter]);

		foreach($vars as $key => $value)
		{
			if(in_array($key, array_keys($this->__filter_scheme)) && strlen($value))
			{
				$_SESSION['dbo_mid'][$this->getMid()][filter][$key] = $value;
			}
		}
	}

	//cria um complemento de SQL com os fixos --------------------------------------------------------------------------------------------

	function encodeFixos($foo)
	{
		$foo = (array)$foo;
		$partes = array();

		foreach($foo as $key => $value)
		{
			if(strstr($value, '=')) { $explodir = TRUE; }
		}

		if($explodir) {
			foreach($foo as $key => $value)
			{
				list($chave, $valor) = explode('=', $value);
				$partes[$chave] = $valor;
			}
		} else {
			$partes	= $foo;
		}

		foreach($partes as $chave => $valor)
		{
			$query_aux[] = $chave."::".$valor;
		}
		$query .= @implode("||", $query_aux);

		return base64_encode($query);
	}

	//cria um complemento de SQL com os fixos --------------------------------------------------------------------------------------------

	function decodeFixos($foo)
	{
		$foo = base64_decode($foo);
		list($chave, $valor) = explode("::", $foo);
		return array($chave => $valor);
	}

	//retorna os itens fixos em forma de array -------------------------------------------------------------------------------------------

	function getFixos()
	{
		return $this->__fixos;
	}

	//retorna o total de itens de uma tabela segundo a query, usado para comparações que venham a ser necessarias -------------------------

	function total($restricoes = '')
	{
		if($this->__custom_query && strstr($this->__custom_query, "SQL_CALC_FOUND_ROWS"))
		{
			return $this->__total;
		}
		$sql = "SELECT COUNT(*) as total FROM ".$this->__table." ".$restricoes;
		$res = dboQuery($sql);
		$lin = mysql_fetch_object($res);
		return $lin->total;
	}

	//equivalente ao mysql_affected_rows() ------------------------------------------------------------------------------------------------

	function size()
	{
		return $this->__size;
	}

	//habilita o uso de recursos diversos do modulo seguindo seus pre-requisitos de funcionamento -----------------------------------------

	function ok()
	{
		if($this->__ok === true)
		{
			return true;
		}
		return false;
	}

	//trata variaveis de get  -------------------------------------------------------------------------------------------------------------

	function keepUrl ($foo = '')
	{

		if(!is_array($foo))
		{
			$vars[] = $foo;
		}
		else
		{
			$vars = $foo;
		}

		if(sizeof($vars) > 2)
		{
			die('ERRO: keepUrl aceita somente <b>1 variável<b> ou <b>array de 2 chaves</b> como entrada!');
		}
		foreach($vars as $chave => $valor)
		{
			if(strpos($valor, '!') === 0) //remove_list
			{
				$remove_vars = $valor;
				$remove_vars_flag = 1;
			}
			else
			{
				$add_vars = $valor;
			}
		}
		if($remove_vars_flag)
		{
			$remove_vars = str_replace("!", "", $remove_vars);
			$remove_vars = explode("&", $remove_vars);
		}

		//salva o nome da pagina php em questao
		$arquivo = explode("/", $_SERVER['PHP_SELF']);
		$arquivo = $arquivo[sizeof($arquivo)-1];

		//separa as variaveis que jah existian na URL em uma matriz
		if($_SERVER['QUERY_STRING']) { $get_vars_aux = explode("&", $_SERVER['QUERY_STRING']); }
		if(is_array($get_vars_aux))
		{
			foreach($get_vars_aux as $chave => $valor)
			{
				list($key, $value) = explode("=", $valor);
				if(is_array($remove_vars) && in_array($key, $remove_vars)) continue; //checa para ver se nao está na remove list
				$get_vars[$key] = $value;
			}
		}

		//faz a juncao das variaveis da URL com as novas variaveis, e sobrepoe se jah existirem.
		if($add_vars) { $input_vars_aux = explode("&", $add_vars); }
		if(is_array($input_vars_aux))
		{
			foreach($input_vars_aux as $chave => $valor)
			{
				list($key, $value) = explode("=", $valor);
				if(is_array($remove_vars) && in_array($key, $remove_vars)) continue; //checa para ver se nao está na remove list
				$get_vars[$key] = $value;
			}
		}

		//monta a query final
		if(is_array($get_vars))
		{
			$query = "?";
			foreach($get_vars as $chave => $valor)
			{
				$query_aux[] = $chave."=".$valor;
			}
		}
		$query .= @implode("&", $query_aux);

		return $arquivo.$query;
	} //keepUrl()

	//limpas os arrays de query -----------------------------------------------------------------------------------------------------------

	private function clearArrays ()
	{
		$this->__chave_array = array();
		$this->__valor_array = array();
	}

	//limpas os arrays de query -----------------------------------------------------------------------------------------------------------

	function clearData ()
	{
		$this->__data = array();
	}

	//limpas os arrays de query -----------------------------------------------------------------------------------------------------------

	function viewButtons ()
	{
		//checa se exitem botoes customizados no modulo
		if(is_array($this->__module_scheme->button))
		{
			foreach($this->__module_scheme->button as $chave => $botao)
			{
				if($botao->lista === TRUE)
				{
					if(!DBO_PERMISSIONS || hasPermission($botao->value, $_GET['dbo_mod']))
					{
						return true;
					}
				}
			}//foreach
		}//if

		if(!DBO_PERMISSIONS || hasPermission('update', $_GET['dbo_mod'])) { return true; }

		return false;
	}

	//-------------------------------------------------------------------------------------------------------------------------------------
	//funcoes de tratamento de etnrada de dados -------------------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------------------------------------------------------------

	function like ($foo)
	{
		return "|##|LIKE|##|".$foo;
	}

	function notLike ($foo)
	{
		return "|##|NOTLIKE|##|".$foo;
	}

	function now ()
	{
		return "|##|NOW()|##|";
	}

	function null ()
	{
		return "|##|NULL|##|";
	}

	//executa a substituicao pelos valores certos na query --------------------------------------------------------------------------------
	function trataValor ($foo)
	{
		if(strpos($foo, "|##|LIKE|##|") === 0) {

			return " LIKE '".str_replace("|##|LIKE|##|", '', $foo)."'";

		} elseif(strpos($foo, "|##|NOTLIKE|##|") === 0) {

			return " NOT LIKE '".str_replace("|##|NOTLIKE|##|", '', $foo)."'";

		} elseif(strpos($foo, "|##|NOW|##|") === 0) {

			return " = NOW() ";

		} elseif(strpos($foo, "|##|NULL|##|") === 0) {

			return " = NULL ";

		} else {
			return " = '".$foo."'";
		}
	}

	//reconstroi a query segundo funcao acima ---------------------------------------------------------------------------------------------

	function remakeSql ($sql)
	{
		$sql = str_replace("'|##|", "", $sql);
		$sql = str_replace("|##|'", "", $sql);
		return $sql;
	}

	//checks if the module has auto-orderer field -----------------------------------------------------------------------------------------

	function autoOrderAjax($data)
	{
		foreach($data['row'] as $order => $id)
		{
			$sql = "UPDATE ".$this->__module_scheme->tabela." SET order_by = ".$order." WHERE ".$this->getPK()." = ".$id;
			dboQuery($sql);
		}
		exit();
	}

	//checks if the module has auto-orderer field -----------------------------------------------------------------------------------------

	function isAutoOrdered()
	{
		if(is_array($this->__module_scheme->campo))
		{
			foreach($this->__module_scheme->campo as $field)
			{
				if($field->coluna == 'order_by')
				{
					return true;
				}
			}
			return false;
		}
		else
		{
			return false;
		}
	}

	//pega o link com o order para os cabecalhos de campos --------------------------------------------------------------------------------
	function getOrderBy ()
	{
		global $_SESSION;

		if(sizeof($_SESSION['dbo_mid'][$this->getMid()][order_by])) {
			return $_SESSION['dbo_mid'][$this->getMid()][order_by];
		} elseif(sizeof($this->__order_by)) {
			return $this->__order_by;
		} else {
			return null;
		}
	}

	//pega o link com o order para os cabecalhos de campos --------------------------------------------------------------------------------
	function getOrderLink ($campo)
	{
		$var = '';
		$classe = '';
		if($campo->order && !$this->isAutoOrdered())
		{
			$order_by = $this->getOrderBy();
			if($order_by[$campo->coluna] == 'ASC') {
				$var = "dbo_order_by=".$campo->coluna."::DESC";
				$classe = 'dbo-link-order-asc';
			} elseif($order_by[$campo->coluna] == 'DESC') {
				$var = "dbo_order_by=".$campo->coluna."::ASC";
				$classe = 'dbo-link-order-desc';
			} else {
				$var = "dbo_order_by=".$campo->coluna."::ASC";
			}
			return "<a href='".$this->keepUrl($var)."' class='dbo-link-order ".$classe."' title=".(($campo->titulo_listagem)?($campo->titulo):(''))." >".(($campo->titulo_listagem)?($campo->titulo_listagem):($campo->titulo))."</a>";
		}
		else
		{
			return "<span title='".(($campo->titulo_listagem)?($campo->titulo):(''))."'>".(($campo->titulo_listagem)?($campo->titulo_listagem):($campo->titulo))."</span>";
		}
	}

	/*
	* ===============================================================================================================================================
	* Gera a listagem dos registros de acordo com o esquema de modulo ===============================================================================
	* ===============================================================================================================================================
	*/
	function getList ($restricoes = '')
	{

		if($this->ok())
		{
			$this->__module_scheme->paginacao ? $this->__ipp = $this->__module_scheme->paginacao : '';//setando o número para paginação

			$return = "<span class='dbo-element'><div class='fieldset'><div class='content'><table class='responsive list ".(($this->isAutoOrdered())?('auto-order'):(''))."'><thead><tr>";

			// Colocando os THs na tabela.
			foreach($this->__module_scheme->campo as $chave => $valor)
			{
				if($valor->lista === true && !$this->isFixo($valor->coluna))
				{
					$return .= "<th>".$this->getOrderLink($valor)."</th>";
				}
			}

			//coluna para as acoes
			$return .= '<th colspan="10" style="width: 1%;" class="text-right"></th>';
			$return .= "</tr></thead><tbody>";

			/* setando permissões genéricas */

			/* view */
			$dbo_permission_view = hasPermission('view', $_GET['dbo_mod']);

			/* update */
			$dbo_permission_update = hasPermission('update', $_GET['dbo_mod']);

			/* delete */
			$dbo_permission_delete = hasPermission('delete', $_GET['dbo_mod']);

			/* buttons */
			if(is_array($this->__module_scheme->button))
			{
				foreach($this->__module_scheme->button as $chave => $botao)
				{
					$dbo_permission_button[$botao->value] = hasPermission($botao->value, $_GET['dbo_mod']);
				}
			}
			
			// Fazendo um "SELECT *" na tabela
			$classe = ($this->__class);
			$obj = $this->newSelf();
			$sql_list = $this->getModuleRestriction().$this->getSQLfixos().$this->getSQLFilters().$this->getSQLInativo().$this->getSQLDeletionEngine().$this->getSQLOrder().$this->getSQLipp();
			$obj->loadAll($sql_list);
			$modulo = $obj;
			if($obj->size())
			{
				do {

					$id = $obj->id;

					$update_interaction = true;
					$delete_interaction = true;

					// Imprimindo as linhas
					$return .= "<tr id='row-".$id."' rel='".$this->keepUrl('dbo_view='.$id)."' ".(($_GET['dbo_view'] == $id)?("class='active'"):(''))." ".((!$dbo_permission_view && $dbo_permission_update)?("data-update-url='".$this->keepUrl(array("dbo_update=".$id, '!dbo_new&!dbo_delete&!dbo_view'))."'"):(''))." >";

					foreach($this->__module_scheme->campo as $chave => $valor)
					{

						$update_interaction = (($update_interaction === true)?($this->checkInteraction($modulo, $valor, $obj->$chave, 'update')):($update_interaction));
						$delete_interaction = (($delete_interaction === true)?($this->checkInteraction($modulo, $valor, $obj->$chave, 'delete')):($delete_interaction));

						//checa se existe função de exibição de dados
						$list_function = ((strlen($valor->list_function))?($valor->list_function):(false));

						// Tratando todos os possiveis tipos de campo
						if($valor->lista === true && !$this->isFixo($valor->coluna))
						{
							
							$return .= "<td class='".((!DBO_PERMISSIONS || $dbo_permission_view)?("view-handle"):(''))." ".$chave."' data-title='".((strlen(trim($valor->titulo_listagem)))?($valor->titulo_listagem):($valor->titulo))."'>";

							//creates an array with the ids of the listed elements. This array can be used by the pos_list function of the module.
							$this->__listed_elements[$id] = $id;

							$val = $obj->$chave;

							// PK =========================================================================================
							if($valor->tipo == 'pk')
							{
								if($list_function) { $return .= $list_function($obj, $valor->coluna); }
								else {
									$return .= htmlspecialchars($val);
								}
							}
							// TEXT =======================================================================================
							if($valor->tipo == 'text')
							{
								if($list_function) { $return .= $list_function($obj, $valor->coluna); }
								else {
									$return .= htmlspecialchars($val);
								}
							}
							// PASSWORD =======================================================================================
							if($valor->tipo == 'password')
							{
								$return .= "********";
							}
							// TEXT =======================================================================================
							if($valor->tipo == 'price')
							{
								if($list_function) { $return .= $list_function($obj, $valor->coluna); }
								else {
									$valor_price = $val;
									if($valor->formato == 'real')
									{
										$valor_price = 'R$ '.number_format($valor_price, 2, ',', '.');
									}
									elseif($valor->formato == 'generico')
									{
										$valor_price = '$ '.number_format($valor_price, 2, ',', '.');
									}
									else
									{
										$valor_price = 'US$ '.number_format($valor_price, 2, '.', ',');
									}
									$return .= htmlspecialchars($valor_price);
									unset($valor_price);
								}
							}
							// SELECT / RADIO =============================================================================
							elseif ($valor->tipo == 'radio' || $valor->tipo == 'select')
							{
								if($list_function) { $return .= $list_function($obj, $valor->coluna); }
								else {
									$return .= $valor->valores[$val];
								}
							}
							// CAMPO DE DATA ==============================================================================
							elseif ($valor->tipo == 'date')
							{
								if($list_function) { $return .= $list_function($obj, $valor->coluna); }
								else {
									$val = explode("-", $val);
									$val = implode("/", array_reverse($val));
									$return .= htmlspecialchars($val != '00/00/0000' ? $val : '');
								}
							}
							// CAMPO DE DATA E HORA ==============================================================================
							elseif ($valor->tipo == 'datetime')
							{
								if($list_function) { $return .= $list_function($obj, $valor->coluna); }
								else {
									if(strlen(trim($val)))
									{
										$return .= dateTimeNormal($val);
									}
								}
							}
							// SINGLE JOIN ================================================================================
							elseif ($valor->tipo == 'join') {
								$join = $valor->join;
								$jobj = new Dbo($join->modulo);
								$jobj->id = $val;
								$jobj->load();
								if($list_function) { $return .= $list_function($obj, $valor->coluna); }
								else {
									$return .= $jobj->{$join->valor};
								}
							}
							// PLUGINS ==========================================================================
							elseif($valor->tipo == 'plugin')
							{
								$plugin = $valor->plugin;
								$plugin_path = DBO_PATH."/plugins/".$plugin->name."/".$plugin->name.".php";
								$plugin_class = "dbo_".$plugin->name;
								//checa se o plugin existe, antes de mais nada.
								if(file_exists($plugin_path))
								{
									include_once($plugin_path); //inclui a classe
									$plug = new $plugin_class($plugin->params); //instancia com os parametros
									$plug->setData($val);
									$return .= $plug->getList(); //pega os campos de inserção
								}
								else { //senão, avisa que não está instalado.
									$return .= "O Plugin <b>'".$plugin->name."'</b> não está instalado";
								}
							} //plugins
							// IMAGE ================================================================================
							elseif ($valor->tipo == 'image') {
								if($val)
								{
									if($list_function) { $return .= $list_function($obj, $valor->coluna); }
									else {
										$return .= "<img src='".DBO_IMAGE_HTML_PATH."/".$val."' class='thumb-lista' />";
									}
								}
							} //image
							// QUERY ================================================================================
							elseif ($valor->tipo == 'query') {
								$query = '';
								eval($valor->query);
								$res_query = dboQuery($query);
								if(mysql_affected_rows())
								{
									$lin_query = mysql_fetch_object($res_query);
									$val = $lin_query->val;
								}
								if($val !== false)
								{
									if($list_function) { $return .= $list_function($obj, $valor->coluna); }
									else {
										$return .= $val;
									}
								}
							}

							$return .= "</td>";
						} // if lista true
					} //foreach

					//checa se exitem botoes customizados no modulo
					if(is_array($this->__module_scheme->button))
					{
						foreach($this->__module_scheme->button as $chave => $botao)
						{
							if(!DBO_PERMISSIONS || $dbo_permission_button[$botao->value])
							{
								if($botao->custom === TRUE) //botoes customizados. o codigo bem do arquivo de definição do modulo.
								{
									eval(str_replace("[VALUE]", $botao->value, $botao->code));
									$return .= "<td>".$code."</td>";
								} else {
									$return .= "<td><a class='button tiny radius large-no-wrap no-margin' href='".$this->keepUrl(array("dbo_mod=".$botao->modulo."&dbo_fixo=".$this->encodeFixos($botao->modulo_fk."=".$obj->{$botao->key}), "!pag&!dbo_insert&!dbo_update&!dbo_delete&!dbo_view"))."'>".$botao->value."</a></td>";
								}
							}
						}//foreach
					}//if

					$return .= "<td class=\"control-icons\" id='controls-row-".$id."' style='white-space: nowrap'>";

					//checa se o modulo permite edição e exclusão dos dados
					if ($this->__module_scheme->update === true) {
						if(!DBO_PERMISSIONS || $dbo_permission_update)
						{
							//mostrar a chave de inativo/ativo se houver
							if($this->hasInativo())
							{
								$return .= (($obj->inativo == 0)?("<span class='wrapper-lock'><a title='Desativar' class='trigger-dbo-auto-admin-toggle-active-inactive' href='".$this->keepUrl(array("dbo_toggle_inactive=".$id."&token=".md5($id.SALT_DBO_AUTO_ADMIN_TOGGLE_ACTIVE)."&".CSRFVar(), '!dbo_toggle_active'))."'><i class=\"fi-unlock\"></i></a></span>"):("<span class='wrapper-lock'><a title='".$this->__module_scheme->titulo." inativo. Clique para ativar' class='trigger-dbo-auto-admin-toggle-active-inactive alert' href='".$this->keepUrl(array("dbo_toggle_active=".$id."&token=".md5($id.SALT_DBO_AUTO_ADMIN_TOGGLE_ACTIVE)."&".CSRFVar(), '!dbo_toggle_inactive'))."'><i class=\"fi-lock\"></i></a></span>"));
							}
							$return .= (($update_interaction)?(" <a title='Alterar' href='".$this->keepUrl(array("dbo_update=".$id, '!dbo_new&!dbo_delete&!dbo_view'))."'><i class=\"fi-pencil\"></i></a>"):(''));
						}
					}
					if ($this->__module_scheme->delete === true) {
						if(!DBO_PERMISSIONS || $dbo_permission_delete)
						{
							$return .= (($delete_interaction)?(" <a title='Excluir' class=\"trigger-dbo-auto-admin-delete\" data-id=\"".$id."\" href=\"".$this->keepUrl(array("dbo_delete=".$id."&".CSRFVar(), '!dbo_new&!dbo_update&!dbo_view'))."\"><i class=\"fi-x\"></i></a>"):(''));
						}
					}

					$return .= "</td>";

					$return .= "</tr>";
				} while($obj->fetch());
			}
			else
			{
				$return .= "<tr><td colspan='20'>Não há itens cadastrados</td></tr>";
			}

			$return .= "</tbody></table></div></div>";

			echo $return;

			//shows only if the current module is not autoOrdered
			if(!$this->isAutoOrdered())
			{
				echo $this->splitter($this->getModuleRestriction().$this->getSQLfixos().$this->getSQLFilters().$this->getSQLInativo().$this->getSQLOrder());
			}
			else
			{
				?>
				<div class="helper arrow-top hide-for-small">Clique e arraste os itens para reordenar</div>
				<?
			}

			echo "</span>"; //.dbo-element
		} //ok()
	} //getList()

	/*
	* ===============================================================================================================================================
	* Gera o formulário de INSERT no banco de dados  de acordo com o esquema de modulo ==============================================================
	* ===============================================================================================================================================
	*/
	function getInsertForm ()
	{
		if($this->ok())
		{
			//campos a serem usados no eval();
			$meta = $this->__module_scheme;
			$fixos = $this->__fixos;

			//inicializando mapeamento de validação
			$validation_meta = array();

			if(!is_object($this->__module_scheme))
			{
				echo "<h1 style='font-size: 21px; color: #C00;'>ERRO: A classe '".get_class($this)."' não possui esquema de módulo definido.</h1>";
			}
			else
			{
				//setando um id para o formulario, usado na validacao e mascaras.
				$id_formulario = "form-".time().rand(1,100);

				$return = "<span class='dbo-element'><div class='fieldset' style='clear: both;'><div class='content'>";

				$return .= "<form method='POST' enctype='multipart/form-data' action='".$this->keepUrl('!dbo_delete&!dbo_update&!dbo_new')."' id='".$id_formulario."'>";

				//checando se há grid de exibição de dados customizado... e setando variaveis para seu uso.
				if($this->hasGrid('insert')) { $gc = 0; $hasgrid = true; $grid = $this->hasGrid('insert'); }

				//checando se há algo a se colocar antes do formulario (campos por exemplo)
				$function_name = 'form_'.$this->__module_scheme->modulo."_prepend";
				if(function_exists($function_name))
				{
					$return .= $function_name('insert', $this);
				}

				// Criando o form de inserçao.
				foreach($this->__module_scheme->campo as $chave => $valor)
				{

					/* checando para ver se existe uma função custom para determinado campo */
					$custom_field = false;
					$function_name = 'field_'.$meta->modulo."_".$valor->coluna;
					if(function_exists($function_name))
					{
						$custom_field = $function_name('insert', $this);
					}


					if($hasgrid)
					{
						if($grid[$gc] == '|-')
						{
							$return .= "<div class='row clearfix'>\n"; $gc++;
							//inserts the section separator, if exists.
							if(intval($grid[$gc]) == 0 && $grid[$gc] != '|-' && $grid[$gc] != '-|' )
							{
								$return .= "<div class='large-12 columns'><h5 class='section subheader'>".$grid[$gc]."</h5><hr></div>\n"; $gc++;
								$return .= "</div> <!-- row -->\n\n"; $gc++;
								$return .= "<div class='row'>\n"; $gc++;
							}
						}
						if($grid[$gc] == '-|') { $return .= "</div> <!-- row -->\n\n"; /*row*/ $gc++; }
					}
					if($valor->add === true)
					{
						if($this->perfilTemAcessoCampo($valor->perfil))
						{

								if (!$hasgrid) { $return .= "<div class='row clearfix'>"; }
								$return .= "<div class='item columns ".(($hasgrid)?('large-'.$grid[$gc++]):(''))."' id='item-".$valor->coluna."'>\n";
								$return .= "<label>".$valor->titulo.(($valor->valida)?(" <span class='required'></span>"):('')).(($valor->dica)?(" <span data-tooltip class='has-tip tip-top' title='".$valor->dica."'>(?)</span>"):(''))."</label>";
								$return .= "<span class='input input-".$valor->tipo."'>";

								if($custom_field)
								{
									$return .= $custom_field;
								}
								else
								{
									// TEXT ======================================================================================
									if($valor->tipo == 'text')
									{
										$return .= "<input type='text' name='".$valor->coluna."' data-name='".$valor->titulo."' class='".(($valor->valida)?('required'):(''))."'/>";
									}
									// PASSWORD ==================================================================================
									if($valor->tipo == 'password')
									{
										$return .= "<input type='password' name='".$valor->coluna."' data-name='".$valor->titulo."' class='".(($valor->valida)?('required'):(''))."'/>";
									}
									// TEXTAREA ==================================================================================
									if($valor->tipo == 'textarea')
									{
										$return .= "<textarea rows='".(($valor->rows)?($valor->rows):('5'))."' name='".$valor->coluna."' data-name='".$valor->titulo."' class='".(($valor->valida)?('required'):(''))."'></textarea>";
									}
									// TEXTAREA-RICH ==================================================================================
									if($valor->tipo == 'textarea-rich')
									{
										$return .= "<textarea rows='".(($valor->rows)?($valor->rows):('5'))."' name='".$valor->coluna."' class='tinymce ".(($valor->valida)?('required'):(''))."' id='insert-".$valor->coluna."' data-name='".$valor->titulo."'></textarea>";

										//getting the settings for tinymce
										/*$file_code = '';
										$file_code = file_get_contents(getTinyMCESettingsFile());
										ob_start();
										eval("?>".$file_code."<?");
										$return .= ob_get_clean();*/
									}
									// RADIO =====================================================================================
									elseif ($valor->tipo == 'radio')
									{
										$return .= "<span class='form-height-fix list-radio-checkbox' style='display: block'>";
										foreach($valor->valores as $chave2 => $valor2)
										{
											$return .= "<span style='white-space: nowrap'><input type='radio' id='radio-".$valor->coluna."-".makeSlug($chave2)."' name='".$valor->coluna."' value='".$chave2."' data-name='".$valor->titulo."' class='".(($valor->valida)?('required'):(''))."'><label for='radio-".$valor->coluna."-".makeSlug($chave2)."'>".$valor2."</label></span>\n";
										}
										$return .= "</span>";
									}
									// CHECKBOX ==================================================================================
									elseif ($valor->tipo == 'checkbox')
									{
										$return .= "<span class='form-height-fix list-radio-checkbox' style='display: block'>";
										foreach($valor->valores as $chave2 => $valor2)
										{
											$return .= "<span style='display: block; white-space: nowrap' data-name='".$valor->titulo."' class='".(($valor->valida)?('required'):(''))."'><input type='checkbox' id='radio-".$valor->coluna."-".makeSlug($chave2)."' name='".$valor->coluna."[]' value='".$chave2."'><label for='radio-".$valor->coluna."-".makeSlug($chave2)."'>".$valor2."</label></span>\n";
										}
										$return .= "</span>";
									}
									// PRICE ====================================================================================
									elseif ($valor->tipo == 'price')
									{
										$return .= "<div class=\"row collapse\">";
										$return .= "	<div class=\"small-10 columns\"><input type='text' name='".$valor->coluna."' data-name='".$valor->titulo."' class='".(($valor->valida)?('required'):(''))." price price-".$valor->formato." text-right'/></div>";
										$return .= "	<div class=\"small-2 columns\">";
										$return .= "		<span class=\"postfix radius pointer trigger-clear-price\" title=\"Limpar o valor do preço\"><i class=\"fi-x\"></i></span>";
										$return .= "	</div>";
										$return .= "</div>";
									}
									// SELECT ====================================================================================
									elseif ($valor->tipo == 'select')
									{
										$return .= "<select name='".$valor->coluna."' data-name='".$valor->titulo."' class='".(($valor->valida)?('required'):(''))."'>";
										$return .= "<option value='-1'>...</option>";
										foreach($valor->valores as $chave2 => $valor2)
										{
											$return .= "<option value='".$chave2."'>".$valor2."</option>";
										}
										$return .= "</select>";
									}
									// DATA ======================================================================================
									elseif($valor->tipo == 'date')
									{
										$return .= (($custom_field)?($custom_field):("<input type='text' class='datepick ".(($valor->valida)?('required'):(''))."' name='".$valor->coluna."' data-name='".$valor->titulo."'/>"));
									}
									// DATA E HORA ================================================================================
									elseif($valor->tipo == 'datetime')
									{
										ob_start();
										?>
										<div class="row collapse">
											<div class="small-10 columns"><input type='text' <?= (($valor->valida)?('required'):('')) ?> name="<?= $valor->coluna ?>" class='datetimepick <?= (($valor->valida)?('required'):('')) ?>' data-name="<?= $valor->titulo ?>"/></div>
											<div class="small-2 columns"><a href="#" class="button secondary postfix radius trigger-clear-closest-input" title="Limpar data e hora" style="background-image: url('<?= DBO_URL ?>/../images/cross.png'); background-repeat: no-repeat; background-position: center center;">&nbsp;</a></div>
										</div>
										<?
										$return .= ob_get_clean();
									}
									// PLUGINS ==========================================================================
									elseif($valor->tipo == 'plugin')
									{
										$plugin = $valor->plugin;
										$plugin_path = DBO_PATH."/plugins/".$plugin->name."/".$plugin->name.".php";
										$plugin_class = "dbo_".$plugin->name;
										//checa se o plugin existe, antes de mais nada.
										if(file_exists($plugin_path))
										{
											include_once($plugin_path); //inclui a classe
											$plug = new $plugin_class($plugin->params); //instancia com os parametros
											$return .= "<div class='wrapper-plugin'>".$plug->getInsertForm($valor->coluna)."</div>"; //pega os campos de inserção
										}
										else { //senão, avisa que não está instalado.
											$return .= "O Plugin <b>'".$plugin->name."'</b> não está instalado";
										}
									} //plugins
									// SINGLE JOIN ============================================================================
									elseif($valor->tipo == 'join')
									{

										$join = $valor->join;
										$mod_aux = $join->modulo;
										$obj = new $mod_aux();

										//se for ajax, muda tudo!
										if($join->ajax)
										{
											$mod_selected = $join->modulo;
											$mod_selected = new $mod_selected();
										
											//handler para o ID do campo
											$id_handler = uniqid();

											//variaveis necessárias para o javascript
											$url_dbo_ui_joins_ajax = DBO_URL."/core/dbo-ui-joins-ajax.php";
											$tamanho_minimo = (($join->tamanho_minimo)?($join->tamanho_minimo):(3));
											
											ob_start();
											?>
												<input type="text" name="<?= $valor->coluna ?>_select2_aux" data-name="<?= $valor->titulo ?>" data-target="#<?= $id_handler ?>" class="<?= (($valor->valida)?('required'):('')) ?>"/>
												<input type="hidden" name="<?= $valor->coluna ?>" id="<?= $id_handler ?>" value="" class="<?= (($valor->valida)?('required'):('')) ?>"/>
												<script>
													$(document).ready(function(){

														//pegando responsavels por ajax
														$('input[name=<?= $valor->coluna ?>_select2_aux]').select2({
															placeholder: "...",
															minimumInputLength: <?= $tamanho_minimo ?>,
															allowClear: true,
															ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
																url: "<?= $url_dbo_ui_joins_ajax ?>",
																dataType: 'json',
																data: function (term, page) {
																	return {
																		module: '<?= $this->getModule(); ?>',
																		field: '<?= $valor->coluna; ?>',
																		term: term
																	};
																},
																results: function (data, page) {
																	return { results: data };
																}
															},
															formatResult: function (data) {
																return data.valor;
															},
															formatSelection: function (data, container) {
																return data.valor;
															},
															initSelection: function (element, callback) {
																callback({ valor: element.val() });
															}
														});
														$('input[name=<?= $valor->coluna ?>_select2_aux]').on('change', function(e){
															target = $($(this).data('target'));
															if(e.val > 0){
																target.val(e.val).closest('.item').addClass('ok');
															}
															else {
																target.val('').closest('.item').removeClass('ok');
															}
														}).on('select2-opening', function(){
															//$(window).scrollTo($(this).closest('.item'), 500);
														})
													}) //doc.ready
												</script>
											<?
											$return .= ob_get_clean();
										}
										else
										{
											//setando restricoes...
											$rest = '';
											if($valor->restricao) { eval($valor->restricao.";"); }

											//seta deleted_by = 0, se for o caso
											$rest .= (($obj->hasDeletionEngine())?(((strlen($rest))?(" AND "):(" WHERE "))." deleted_by = 0 "):(''));

											//seta inativo = 0 caso o modulo externo se enquadre, e depois o order by
											$rest .= (($obj->hasInativo())?(((strlen($rest))?(" AND "):(" WHERE "))." inativo = 0 "):(''))." ORDER BY ".(($valor->join->order_by)?($valor->join->order_by):($valor->join->valor))." ";

											$obj->loadAll($rest);
											if($this->isFixo($valor->coluna))
											{
												do {
													if($obj->{$join->chave} == $this->isFixo($valor->coluna))
													{
														$return .= "<input type='hidden' name='".$valor->coluna."' value='".$obj->{$join->chave}."'><span class='dbo_fixo'>".$obj->{$join->valor}."</span>";
													}
												}while($obj->fetch());
												$return .= "
													<script type='text/javascript' charset='utf-8'>
														$('input[name=".$valor->coluna."]').closest('.item').hide();
													</script>
												";
											}
											else
											{
												if($custom_field)
												{
													$return .= $custom_field;
												}
												else
												{
													$metodo_retorno = (($join->metodo_retorno)?($join->metodo_retorno):(false));
													if($join->tipo == 'select') //se o join for do tipo select
													{
														$return .= "<select name='".$valor->coluna."' data-name='".$valor->titulo."' class='".(($valor->valida)?(' required '):('')).(($join->select2)?('select2'):(''))."' ".(($join->tamanho_minimo)?(' data-tamanho_minimo="'.$join->tamanho_minimo.'" '):('')).">";
														$return .= "<option value='-1'>...</option>";
														do {
															$join_retorno = '';
															$join_retorno = (($metodo_retorno)?($obj->$metodo_retorno()):($obj->{$join->valor}));
															$return .= "<option value='".$obj->{$join->chave}."'>".$join_retorno."</option>";
														}while($obj->fetch());
														$return .= "</select>";
													}
													elseif($join->tipo == 'radio') //se o join for do tipo select
													{
														$return .= "<span class='form-height-fix list-radio-checkbox' style='display: block;'>";
														do {
															$join_retorno = '';
															$join_retorno = (($metodo_retorno)?($obj->$metodo_retorno()):($obj->{$join->valor}));
															$return .= "<span style='white-space: nowrap'><input type='radio' id='radio-".$valor->coluna."-".makeSlug($join_retorno)."' name='".$valor->coluna."' value='".$obj->{$join->chave}."' data-name='".$valor->titulo."' class='".(($valor->valida)?('required'):(''))."'><label for='radio-".$valor->coluna."-".makeSlug($join_retorno)."'>".$join_retorno."</label></span>\n";
														}while($obj->fetch());
														$return .= "</span>";
													}
												}
											}
										}
									}
									// MULTI JOIN ============================================================================
									elseif($valor->tipo == 'joinNN')
									{

										$join = $valor->join;
										$mod_aux = $join->modulo;
										$obj = new $mod_aux();

										//setando restricoes...
										$rest = '';
										if($valor->restricao) { eval($valor->restricao.";"); }

										//seta deleted_by = 0, se for o caso
										$rest .= (($obj->hasDeletionEngine())?(((strlen($rest))?(" AND "):(" WHERE "))." deleted_by = 0 "):(''));

										//seta inativo = 0 caso o modulo externo se enquadre, e depois o order by
										$rest .= (($obj->hasInativo())?(((strlen($rest))?(" AND "):(" WHERE "))." inativo = 0 "):(''))." ORDER BY ".(($valor->join->order_by)?($valor->join->order_by):($valor->join->valor))." ";

										$obj->loadAll($rest);

										$metodo_retorno = (($join->metodo_retorno)?($join->metodo_retorno):(false));
										if($join->tipo == 'select') //se o join for do tipo select
										{
											$return .= "<select name='".$valor->coluna."[]' multiple ".(($join->tamanho_minimo)?(' data-tamanho_minimo="'.$join->tamanho_minimo.'" '):(''))." class='".(($join->select2)?('select2'):('multiselect'))." ".(($valor->valida)?('required'):(''))."' size='5' data-name='".$valor->titulo."'";
											do {
												$join_retorno = '';
												$join_retorno = (($metodo_retorno)?($obj->$metodo_retorno()):($obj->{$join->valor}));
												$join_key_2 = '';
												$join_key_2 = (($join->chave2_pk)?($obj->{$join->chave2_pk}):($obj->{$join->chave}));
												$return .= "<option value='".$join_key_2."'>".$join_retorno."</option>";
											}while($obj->fetch());
											$return .= "</select>";
										}
										elseif($join->tipo == 'checkbox') //se o join for do tipo select
										{
											$return .= "<span class='form-height-fix list-radio-checkbox' style='display: block'>";
											do {
												$join_retorno = '';
												$join_retorno = (($metodo_retorno)?($obj->$metodo_retorno()):($obj->{$join->valor}));
												$join_key_2 = '';
												$join_key_2 = (($join->chave2_pk)?($obj->{$join->chave2_pk}):($obj->{$join->chave}));
												$return .= "<span style='display: block; white-space: nowrap' data-name='".$valor->titulo."' class='".(($valor->valida)?('required'):(''))."'><input type='checkbox' id='radio-".$valor->coluna."-".makeSlug($join_retorno)."' name='".$valor->coluna."[]' value='".$join_key_2."' data-name='".$valor->titulo."' class ".(($valor->valida)?('required'):(''))."><label for='radio-".$valor->coluna."-".makeSlug($join_retorno)."'>".$join_retorno."</label></spam>";
											}while($obj->fetch());
											$return .= "</span>";
										}
									}
									// IMAGE ============================================================================
									elseif($valor->tipo == 'image')
									{
										$return .= "<input type='file' name='".$valor->coluna."' data-name='".$valor->titulo."' class='".(($valor->valida)?('required'):(''))."'>";
									}
									// FILES ============================================================================
									elseif($valor->tipo == 'file')
									{
										$return .= "<input type='file' name='".$valor->coluna."' data-name='".$valor->titulo."' class='".(($valor->valida)?('required'):(''))."'>";
									}
								} //if custom_field
								$return .= "</span>"; //input
								$return .= "\n</div>\n"; //item
								if (!$hasgrid) { $return .= "</div> <!-- row -->\n\n"; /*row*/ }
						} //if se o perfil tem acesso a esse campo!
						else
						{
							if($hasgrid) { $gc++; }
						}
					} //if add === true
					if($hasgrid)
					{
						if($grid[$gc] == '|-')
						{
							$return .= "<div class='row'>\n"; $gc++;
							//inserts the section separator, if exists.
							if(intval($grid[$gc]) == 0 && $grid[$gc] != '|-' && $grid[$gc] != '-|' )
							{
								$return .= "<h3 class='section'>".$grid[$gc]."</h3>\n"; $gc++;
								$return .= "</div> <!-- row -->\n\n"; $gc++;
								$return .= "<div class='row clearfix'>\n"; $gc++;
							}
						}
						if($grid[$gc] == '-|') { $return .= "</div> <!-- row -->\n\n"; /*row*/ $gc++; }
					}

					//tratando as mascaras de campo
					if($valor->mask)
					{
						$return .= "\t<script type='text/javascript' charset='utf-8'> \$('#".$id_formulario." #item-".$valor->coluna." input').mask('".$valor->mask."') </script>\n\n";
					}
				} //foreach

				//checando se há algo a se colocar depois do formulario (campos por exemplo)
				$function_name = 'form_'.$this->__module_scheme->modulo."_append";
				if(function_exists($function_name))
				{
					$return .= $function_name('insert', $this);
				}

			}
			$return .= "<div class='row'><div class='item large-12 columns text-right'><div class='input'><input class='button radius' type='Submit' accesskey='s' value='Inserir ".$this->__module_scheme->titulo."'></div></div></div>";
			$return .= "<input type='hidden' name='__dbo_insert_flag' value='1'>";
			$return .= CSRFInput();
			$return .= "</form></div></div></span>"; //.dbo-element

			echo $return;

			//validacao do formulario, se houver.
			$this->getValidationEngine($id_formulario);
		} //ok()
	} //getInsertForm

	/*
	* ===============================================================================================================================================
	* Gera o formulário de UPDATE no banco de dados  de acordo com o esquema de modulo ==============================================================
	* ===============================================================================================================================================
	*/
	function getUpdateForm ()
	{
		if($this->ok())
		{
			//campos a serem usados no eval
			$meta = $this->__module_scheme;
			$fixos = $this->__fixos;

			global $_GET;
			$update = $this->sanitize($_GET['dbo_update']);

			$modulo = $this->newSelf();
			$modulo->id = $update;
			$modulo->load();

			//inicializando mapeamento de validação
			$validation_meta = array();

			if(!is_object($this->__module_scheme))
			{
				echo "<h1 style='font-size: 21px; color: #C00;'>ERRO: A classe '".get_class($this)."' não possui esquema de módulo definido.</h1>";
			}
			else
			{

				//setando um id para o formulario, usado na validacao e mascaras.
				$id_formulario = "form-".time().rand(1,100);

				$return = "<span class='dbo-element'><div class='fieldset' style='clear: both;'><div class='content'>\n";

				$return .= "<form method='POST' enctype='multipart/form-data' id='".$id_formulario."'>\n\n";

				//checando se há grid de exibição de dados customizado... e setando variaveis para seu uso.
				if($this->hasGrid('update')) { $gc = 0; $hasgrid = true; $grid = $this->hasGrid('update'); }

				//checando se há algo a se colocar antes do formulario (campos por exemplo)
				$function_name = 'form_'.$this->__module_scheme->modulo."_prepend";
				if(function_exists($function_name))
				{
					$return .= $function_name('update', $modulo);
				}

				// Criando o formulário de update
				foreach($this->__module_scheme->campo as $chave => $valor)
				{

					/* checando para ver se existe uma função custom para determinado campo */
					$custom_field = false;
					$function_name = 'field_'.$meta->modulo."_".$valor->coluna;
					if(function_exists($function_name))
					{
						$custom_field = $function_name('update', $modulo);
					}
					
					if($hasgrid)
					{
						if($grid[$gc] == '|-')
						{
							$return .= "<div class='row clearfix'>\n"; $gc++;
							//inserts the section separator, if exists.
							if(intval($grid[$gc]) == 0 && $grid[$gc] != '|-' && $grid[$gc] != '-|' )
							{
								$return .= "<div class='large-12 columns'><h5 class='section subheader'>".$grid[$gc]."</h5><hr></div>\n"; $gc++;
								$return .= "</div> <!-- row -->\n\n"; $gc++;
								$return .= "<div class='row'>\n"; $gc++;
							}
						}
						if($grid[$gc] == '-|') { $return .= "</div> <!-- row -->\n\n"; /*row*/ $gc++; }
					}
					if($valor->edit === true)
					{
						if($this->perfilTemAcessoCampo($valor->perfil))
						{

							//checa se existe função de exibição de dados
							$edit_function = ((strlen($valor->edit_function))?($valor->edit_function):(false));

							if (!$hasgrid) { $return .= "<div class='row clearfix'>"; }
							$return .= "\t<div class='item columns ".(($hasgrid)?('large-'.$grid[$gc++]):(''))."' id='item-".$valor->coluna."'>\n";
							$return .= "\t\t<label>".$valor->titulo.(($valor->valida)?(" <span class='required'></span>"):('')).(($valor->dica)?(" <span data-tooltip class='has-tip tip-top' title='".$valor->dica."'>(?)</span>"):(''))."</label>\n";
							$return .= "\t\t<span class='input input-".$valor->tipo."'>\n";

							
							if($custom_field)
							{
								$return .= $custom_field;
							}
							else
							{
								// TEXT ======================================================================================
								if($valor->tipo == 'text')
								{
									$return .= "\t\t\t<input type='text' name='".$valor->coluna."' value=\"".(($edit_function)?($edit_function($this->clearValue($modulo->{$valor->coluna}))):($this->clearValue($modulo->{$valor->coluna})))."\" class='".(($valor->valida)?('required'):(''))."' data-name='".$valor->titulo."'/>\n";
								}
								// PASSWORD ==================================================================================
								if($valor->tipo == 'password')
								{
									$return .= "\t\t\t<input type='password' name='".$valor->coluna."' value=\"".(($edit_function)?($edit_function($this->clearValue($modulo->{$valor->coluna}))):($this->clearValue($modulo->{$valor->coluna})))."\" class='".(($valor->valida)?('required'):(''))."' data-name='".$valor->titulo."'/>\n";
								}
								// TEXTAREA ==================================================================================
								if($valor->tipo == 'textarea')
								{
									$return .= "\t\t\t<textarea rows='".(($valor->rows)?($valor->rows):('5'))."' name='".$valor->coluna."' class='".(($valor->valida)?('required'):(''))."' data-name='".$valor->titulo."'>".(($edit_function)?($edit_function($this->clearValue($modulo->{$valor->coluna}))):($this->clearValue($modulo->{$valor->coluna})))."</textarea>\n";
								}
								// TEXTAREA-RICH ==================================================================================
								if($valor->tipo == 'textarea-rich')
								{
									$return .= "\t\t\t<textarea rows='".(($valor->rows)?($valor->rows):('5'))."' name='".$valor->coluna."' class='tinymce ".(($valor->valida)?('required'):(''))."' id='update-".$valor->coluna."' data-name='".$valor->titulo."'>".(($edit_function)?($edit_function($modulo->{$valor->coluna})):($modulo->{$valor->coluna}))."</textarea>\n";

									//getting the settings for tinymce
									/*$file_code = '';
									$file_code = file_get_contents(getTinyMCESettingsFile());
									ob_start();
									eval("?>".$file_code."<?");
									$return .= ob_get_clean();*/
								}
								// RADIO =====================================================================================
								elseif ($valor->tipo == 'radio')
								{
									$return .= "<span class='form-height-fix list-radio-checkbox' style='display: block'>";
									foreach($valor->valores as $chave2 => $valor2)
									{
										$return .= "\t\t\t<span style='white-space: nowrap'><input type='radio' id='radio-".$valor->coluna."-".makeSlug($chave2)."' name='".$valor->coluna."' value='".$chave2."' ".(($modulo->{$valor->coluna} == $chave2)?('CHECKED'):(''))." class='".(($valor->valida)?('required'):(''))."' data-name='".$valor->titulo."'><label for='radio-".$valor->coluna."-".makeSlug($chave2)."'>".$valor2."</label></span>\n";
									}
									$return .= "</span>";
								}
								// CHECKBOX ==================================================================================
								elseif ($valor->tipo == 'checkbox')
								{
									$return .= "<span class='form-height-fix list-radio-checkbox' style='display: block'>";
									$database_checkbox_values = explode("\n", $modulo->{$valor->coluna});
									foreach($valor->valores as $chave2 => $valor2)
									{
										$return .= "<span style='display: block; white-space: nowrap'><input type='checkbox' id='radio-".$valor->coluna."-".makeSlug($chave2)."' name='".$valor->coluna."[]' value='".$chave2."' ".((in_array($chave2, $database_checkbox_values))?('CHECKED'):(''))." class='".(($valor->valida)?('required'):(''))."' data-name='".$valor->titulo."'><label for='radio-".$valor->coluna."-".makeSlug($chave2)."'>".$valor2."</label></span>\n";
									}
									$return .= "</span>";
								}
								// PRICE ====================================================================================
								elseif ($valor->tipo == 'price')
								{
									$valor_price = $modulo->{$valor->coluna};
									$valor_price = number_format($valor_price, 2, '', '.');

									$return .= "<div class=\"row collapse\">";
									$return .= "	<div class=\"small-10 columns\"><input type='text' name='".$valor->coluna."' data-name='".$valor->titulo."' class='".(($valor->valida)?('required'):(''))." price price-".$valor->formato." text-right' value=\"".(($edit_function)?($edit_function($this->clearValue($valor_price))):($this->clearValue($valor_price)))."\"/></div>";
									$return .= "	<div class=\"small-2 columns\">";
									$return .= "		<span class=\"postfix radius pointer trigger-clear-price\" title=\"Limpar o valor do preço\"><i class=\"fi-x\"></i></span>";
									$return .= "	</div>";
									$return .= "</div>";

									unset($valor_price);
								}
								// SELECT ====================================================================================
								elseif ($valor->tipo == 'select')
								{
									$return .= "\t\t\t<select name='".$valor->coluna."' class='".(($valor->valida)?('required'):(''))."' data-name='".$valor->titulo."'>\n";
									$return .= "\t\t\t\t<option value='-1'>...</option>\n";
									foreach($valor->valores as $chave2 => $valor2)
									{
										$return .= "\t\t\t\t<option value='".$chave2."' ".(($modulo->{$valor->coluna} == $chave2)?('SELECTED'):('')).">".(($edit_function)?($edit_function($valor2)):($valor2))."</option>\n";
									}
									$return .= "\t\t\t</select>\n";
								}
								// DATA ======================================================================================
								elseif($valor->tipo == 'date')
								{
									list($ano,$mes,$dia) = explode("-", $this->clearValue($modulo->{$valor->coluna}));
									if($dia == '00') { $val = ''; }
									else { $val = $dia."/".$mes."/".$ano; }
									$return .= "\t\t\t<input type='text' class='datepick ".(($valor->valida)?('required'):(''))."' name='".$valor->coluna."' value='".$val."'  data-name='".$valor->titulo."'/>\n";
								}
								// DATA E HORA ===============================================================================
								elseif($valor->tipo == 'datetime')
								{
									$datetime_valor = '';
									if(strlen(trim($this->clearValue($modulo->{$valor->coluna}))))
									{
										$datetime_valor = dateTimeNormal($this->clearValue($modulo->{$valor->coluna}));
									}
									ob_start();
									?>
									<div class="row collapse">
										<div class="small-10 columns"><input type='text' <?= (($valor->valida)?('required'):('')) ?> name="<?= $valor->coluna ?>" class='datetimepick <?= (($valor->valida)?('required'):('')) ?>' data-name="<?= $valor->titulo ?>" value="<?= $datetime_valor ?>"/></div>
										<div class="small-2 columns"><a href="#" class="button secondary postfix radius trigger-clear-closest-input" title="Limpar data e hora" style="background-image: url('<?= DBO_URL ?>/../images/cross.png'); background-repeat: no-repeat; background-position: center center;">&nbsp;</a></div>
									</div>
									<?
									$return .= ob_get_clean();
								}
								// PLUGINS ==========================================================================
								elseif($valor->tipo == 'plugin')
								{
									$plugin = $valor->plugin;
									$plugin_path = DBO_PATH."/plugins/".$plugin->name."/".$plugin->name.".php";
									$plugin_class = "dbo_".$plugin->name;
									//checa se o plugin existe, antes de mais nada.
									if(file_exists($plugin_path))
									{
										include_once($plugin_path); //inclui a classe
										$plug = new $plugin_class($plugin->params); //instancia com os parametros
										$plug->setData($this->clearValue($modulo->{$valor->coluna}));
										$return .= "\t\t\t<div class='wrapper-plugin'>".$plug->getUpdateForm($valor->coluna)."</div>\n"; //pega os campos de inserção
									}
									else { //senão, avisa que não está instalado.
										$return .= "O Plugin <b>'".$plugin->name."'</b> não está instalado";
									}
								} //plugins
								// SINGLE JOIN ===============================================================================
								elseif($valor->tipo == 'join')
								{
									$join = $valor->join;
									$mod_aux = $join->modulo;
									$obj = new $mod_aux();

									if($join->ajax)
									{

										$metodo_retorno = (($join->metodo_retorno)?($join->metodo_retorno):(false));

										//pegando o item "selected"
										$join_key_2 = '';
										$join_key_2 = (($join->chave2_pk)?($join->chave2_pk):('id'));
										$obj->$join_key_2 = $modulo->{$valor->coluna};
										$obj->loadAll();

										$join_retorno = '';
										$join_retorno = (($metodo_retorno)?($obj->$metodo_retorno()):($obj->{$join->valor}));

										$mod_selected = $join->modulo;
										$mod_selected = new $mod_selected();
									
										//handler para o ID do campo
										$id_handler = uniqid();

										//variaveis necessárias para o javascript
										$url_dbo_ui_joins_ajax = DBO_URL."/core/dbo-ui-joins-ajax.php";
										$tamanho_minimo = (($join->tamanho_minimo)?($join->tamanho_minimo):(3));
										
										ob_start();
										?>
											<input type="text" name="<?= $valor->coluna ?>_select2_aux" value="<?= htmlSpecialChars($join_retorno) ?>" data-name="<?= $valor->titulo ?>" data-target="#<?= $id_handler ?>" class="<?= (($valor->valida)?('required'):('')) ?>"/>
											<input type="hidden" name="<?= $valor->coluna ?>" id="<?= $id_handler ?>" value="<?= $modulo->{$valor->coluna} ?>" class="<?= (($valor->valida)?('required'):('')) ?>"/>
											<script>
												$(document).ready(function(){

													//pegando responsavels por ajax
													$('input[name=<?= $valor->coluna ?>_select2_aux]').select2({
														placeholder: "...",
														minimumInputLength: <?= $tamanho_minimo ?>,
														allowClear: true,
														ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
															url: "<?= $url_dbo_ui_joins_ajax ?>",
															dataType: 'json',
															data: function (term, page) {
																return {
																	module: '<?= $this->getModule(); ?>',
																	field: '<?= $valor->coluna; ?>',
																	term: term
																};
															},
															results: function (data, page) {
																return { results: data };
															}
														},
														formatResult: function (data) {
															return data.valor;
														},
														formatSelection: function (data, container) {
															return data.valor;
														},
														initSelection: function (element, callback) {
															callback({ valor: element.val() });
														}
													});
													$('input[name=<?= $valor->coluna ?>_select2_aux]').on('change', function(e){
														target = $($(this).data('target'));
														if(e.val > 0){
															target.val(e.val).closest('.item').addClass('ok');
														}
														else {
															target.val('').closest('.item').removeClass('ok');
														}
													}).on('select2-opening', function(){
														//$(window).scrollTo($(this).closest('.item'), 500);
													})

													<?
														if($obj->size()) {
															?>
															$('input[name=<?= $valor->coluna ?>_select2_aux]').closest('.item').addClass('ok');
															<?
														}
													?>

												}) //doc.ready
											</script>
										<?
										$return .= ob_get_clean();
									}
									else
									{
										//setando restricoes...
										$rest = '';
										if($valor->restricao) { eval($valor->restricao.";"); }

										//seta deleted_by = 0, se for o caso
										$rest .= (($obj->hasDeletionEngine())?(((strlen($rest))?(" AND "):(" WHERE "))." deleted_by = 0 "):(''));

										//seta inativo = 0 caso o modulo externo se enquadre, e depois o order by
										$rest .= (($obj->hasInativo())?(((strlen($rest))?(" AND "):(" WHERE "))." inativo = 0 "):(''))." ORDER BY ".(($valor->join->order_by)?($valor->join->order_by):($valor->join->valor))." ";

										//caso o modulo externo tenha inativos, precisamos certificar que o valor previamente existente não é um inativo.
										//deverá ser adicionado à listagem em caso positivo.
										$inativo_atual = false;
										if($obj->hasInativo())
										{
											$obj_inativo = new Dbo($join->modulo);
											$obj_inativo->{$join->chave} = $modulo->{$valor->coluna};
											$obj_inativo->load();
											if($obj_inativo->inativo > 0)
											{
												$inativo_atual[chave] = $modulo->{$valor->coluna};
												$inativo_atual[valor] = $obj_inativo->{$join->valor};
											}
										}

										//depois de descobrirmos se era inativo, fazemos um loadAll.
										$obj->loadAll($rest);

										if($this->isFixo($valor->coluna))
										{
											do {
												if($obj->{$join->chave} == $this->isFixo($valor->coluna))
												{
													$return .= "\t\t\t<input type='hidden' name='".$valor->coluna."' value='".$obj->{$join->chave}."'><span class='dbo_fixo'>".$obj->{$join->valor}."</span>\n";
												}
											}while($obj->fetch());
											$return .= "
												<script type='text/javascript' charset='utf-8'>
													$('input[name=".$valor->coluna."]').closest('.row').hide();
												</script>
											";
										}
										else
										{
											//checando para ver se há metodo de retorno
											$metodo_retorno = (($join->metodo_retorno)?($join->metodo_retorno):(false));
											if($join->tipo == 'select') //se o join for do tipo select
											{
												$return .= "\t\t\t<select name='".$valor->coluna."' class='".(($valor->valida)?(' required '):('')).(($join->select2)?('select2'):(''))."' data-name='".$valor->titulo."' ".(($join->tamanho_minimo)?(' data-tamanho_minimo="'.$join->tamanho_minimo.'" '):('')).">\n";
												$return .= "\t\t\t\t<option value='-1'>...</option>\n";
												//se o atual for inativo, colocar no começo, e em destaque...
												if($inativo_atual)
												{
													$return .= "\t\t\t\t<option value='".$inativo_atual[chave]."' SELECTED class='flag-inativo'>".$inativo_atual[valor]." (Inativo)</option>\n";
												}
												do {
													$join_retorno = '';
													$join_retorno = (($metodo_retorno)?($obj->$metodo_retorno()):($obj->{$join->valor}));
													$return .= "\t\t\t\t<option value='".$obj->{$join->chave}."' ".(($obj->{$join->chave} == $modulo->{$valor->coluna})?(" SELECTED "):('')).">".(($edit_function)?($edit_function($join_retorno)):($join_retorno))."</option>\n";
												}while($obj->fetch());
												$return .= "\t\t\t</select>\n";
											}
											elseif($join->tipo == 'radio') //se o join for do tipo select
											{
												$return .= "<span class='form-height-fix list-radio-checkbox' style='display: block;'>";
												do {
													$join_retorno = '';
													$join_retorno = (($metodo_retorno)?($obj->$metodo_retorno()):($obj->{$join->valor}));
													if($inativo_atual)
													{
														$return .= "\t\t\t<span style='white-space: nowrap'><input id='radio-".$valor->coluna."-".makeSlug((($edit_function)?($edit_function($inativo_atual[valor])):($inativo_atual[valor])))."' type='radio' name='".$valor->coluna."' CHECKED value='".$inativo_atual[chave]."'><label for='radio-".$valor->coluna."-".makeSlug((($edit_function)?($edit_function($inativo_atual[valor])):($inativo_atual[valor])))."'  class='flag-inativo'>".(($edit_function)?($edit_function($inativo_atual[valor])):($inativo_atual[valor]))." (Inativo)</label></span>\n";
													}
													$return .= "\t\t\t<span style='white-space: nowrap'><input type='radio' id='radio-".$valor->coluna."-".makeSlug((($edit_function)?($edit_function($join_retorno)):($join_retorno)))."' name='".$valor->coluna."' ".(($obj->{$join->chave} == $modulo->{$valor->coluna})?(" CHECKED "):(''))." value='".$obj->{$join->chave}."' class='".(($valor->valida)?('required'):(''))."' data-name='".$valor->titulo."'><label for='radio-".$valor->coluna."-".makeSlug((($edit_function)?($edit_function($join_retorno)):($join_retorno)))."'>".(($edit_function)?($edit_function($join_retorno)):($join_retorno))."</label></span>\n";
												}while($obj->fetch());
												$return .= "</span>";
											}
										}
									}
								} //single join
								// MULTI JOIN ============================================================================
								elseif($valor->tipo == 'joinNN')
								{
									$join = $valor->join;
									$mod_aux = $join->modulo;
									$obj = new $mod_aux();
									
									$cadastrados_array = array();

									//setando restricoes...
									$rest = '';
									if($valor->restricao) { eval($valor->restricao.";"); }

									//seta deleted_by = 0, se for o caso
									$rest .= (($obj->hasDeletionEngine())?(((strlen($rest))?(" AND "):(" WHERE "))." deleted_by = 0 "):(''));

									//seta inativo = 0 caso o modulo externo se enquadre, e depois o order by
									$rest .= (($obj->hasInativo())?(((strlen($rest))?(" AND "):(" WHERE "))." inativo = 0 "):(''))." ORDER BY ".(($valor->join->order_by)?($valor->join->order_by):($valor->join->valor))." ";

									$obj->loadAll($rest);

									$cadastrados = new Dbo($join->tabela_ligacao);
									$cadastrados->{$join->chave1} = (($join->chave1_pk)?($modulo->{$join->chave1_pk}):($modulo->id));
									$cadastrados->loadAll();
									do {
										$cadastrados_array[] = $cadastrados->{$join->chave2};
									}while($cadastrados->fetch());

									//caso o modulo externo tenha inativos, precisamos certificar que os valores previamente existentes não são inativos.
									//deverá ser adicionado à listagem em caso positivo.
									$inativo_atual = false;
									if($obj->hasInativo())
									{
										foreach($cadastrados_array as $key => $value)
										{
											$obj_inativo = new Dbo($join->modulo);
											$obj_inativo->{$join->chave} = $value;
											$obj_inativo->load();
											if($obj_inativo->inativo > 0)
											{
												$inativo_atual[$modulo->{$valor->coluna}][chave] = $value;
												$inativo_atual[$modulo->{$valor->coluna}][valor] = $obj_inativo->{$join->valor};
											}
										}
									}

									$metodo_retorno = (($join->metodo_retorno)?($join->metodo_retorno):(false));
									if($join->tipo == 'select') //se o join for do tipo select
									{
										$return .= "\t\t\t<select name='".$valor->coluna."[]' multiple ".(($join->tamanho_minimo)?(' data-tamanho_minimo="'.$join->tamanho_minimo.'" '):(''))." class='".(($join->select2)?('select2'):('multiselect'))." ".(($valor->valida)?('required'):(''))."' size='5' data-name='".$valor->titulo."'>\n";
										if($inativo_atual)
										{
											foreach($inativo_atual as $inativo_value)
											{
												$return .= "\t\t\t\t<option value='".$inativo_value[chave]."' SELECTED class='flag-inativo'>".$inativo_value[valor]." (Inativo)</option>\n";
											}
										}
										do {
											$join_retorno = '';
											$join_retorno = (($metodo_retorno)?($obj->$metodo_retorno()):($obj->{$join->valor}));
											$join_key_2 = '';
											$join_key_2 = (($join->chave2_pk)?($obj->{$join->chave2_pk}):($obj->{$join->chave}));
											$return .= "\t\t\t\t<option ".((in_array($join_key_2, $cadastrados_array))?('SELECTED'):(''))." value='".$join_key_2."'>".$join_retorno."</option>\n";
										}while($obj->fetch());
										$return .= "\t\t\t</select>\n";
									}
									elseif($join->tipo == 'checkbox') //se o join for do tipo checkbox
									{
										$return .= "<span class='form-height-fix list-radio-checkbox' style='display: block'>";
										if($inativo_atual)
										{
											foreach($inativo_atual as $key => $inativo_value)
											{
												$return .= "\t\t\t<span style='display: block; white-space: nowrap' data-name='".$valor->titulo."' class='".(($valor->valida)?('required'):(''))."'><input type='checkbox' id='radio-".$valor->coluna."-".makeSlug($obj->{$join->valor})."' name='".$valor->coluna."[]' CHECKED value='".$inativo_value[chave]."' class='".(($valor->valida)?('required'):(''))."' data-name='".$valor->titulo."'><label for='radio-".$valor->coluna."-".makeSlug($obj->{$join->valor})."' class='flag-inativo'>".(($edit_function)?($edit_function($inativo_value[valor])):($inativo_value[valor]))." (Inativo)</label></span>\n";
											}
										}
										do {
											$join_retorno = '';
											$join_retorno = (($metodo_retorno)?($obj->$metodo_retorno()):($obj->{$join->valor}));
											$join_key_2 = '';
											$join_key_2 = (($join->chave2_pk)?($obj->{$join->chave2_pk}):($obj->{$join->chave}));
											$return .= "\t\t\t<span style='display: block; white-space: nowrap' data-name='".$valor->titulo."' class='".(($valor->valida)?('required'):(''))."'><input id='radio-".$valor->coluna."-".makeSlug($join_retorno)."' ".((in_array($join_key_2, $cadastrados_array))?('CHECKED'):(''))." type='checkbox' name='".$valor->coluna."[]' value='".$join_key_2."'><label for='radio-".$valor->coluna."-".makeSlug($join_retorno)."'>".$join_retorno."</label></span>\n ";
										}while($obj->fetch());
										$return .= "</span>";
									}
								}
								// IMAGE ============================================================================
								elseif($valor->tipo == 'image')
								{
									$file_exists = file_exists(DBO_IMAGE_UPLOAD_PATH."/".$this->clearValue($modulo->{$valor->coluna})) && strlen($modulo->{$valor->coluna});
									if($file_exists)
									{
										$return .= "\t\t\t<a rel='lightbox[album]' href='".DBO_IMAGE_HTML_PATH."/".$this->clearValue($modulo->{$valor->coluna})."'><img src='".DBO_IMAGE_HTML_PATH."/".$this->clearValue($modulo->{$valor->coluna})."' class='thumb-lista'></a><input type='checkbox' name='manter_atual_".$valor->coluna."' id='check-imagem-".$valor->coluna."' CHECKED> Manter foto atual<br>\n";
										ob_start();
										?>
										<script>
											$(document).ready(function(){
												$(document).on('change', '#check-imagem-<?= $valor->coluna ?>', function(){
													clicado = $(this);
													target = $('#input-imagem-<?= $valor->coluna ?>');
													if(clicado.is(':checked')){
														target.fadeOut('fast');
													}
													else {
														target.fadeIn('fast');
													}
												});
											}) //doc.ready
										</script>
										<?
										$ob_result = ob_get_clean();
										$return .= $ob_result;
									}
									$return .= "\t\t\t<input type='file' id='input-imagem-".$valor->coluna."' name='".$valor->coluna."' class='".(($file_exists)?('hidden'):(''))." ".(($valor->valida)?('required'):(''))."' data-name='".$valor->titulo."'>\n";
								}
								// FILE ============================================================================
								elseif($valor->tipo == 'file')
								{
									if(strlen($modulo->{$valor->coluna}))
									{
										$return .= "\t\t\t".$this->getDownloadLink($modulo->{$valor->coluna})." <input type='checkbox' name='manter_atual_".$valor->coluna."' CHECKED> Manter arquivo atual<br>\n";
									}
									$return .= "\t\t\t<input type='file' name='".$valor->coluna."' class='".(($valor->valida)?('required'):(''))."' data-name='".$valor->titulo."'>\n";
								}
							} //if custom field
							$return .= "\t\t</span>\n"; //input
							$return .= "\t</div> <!-- item -->\n"; //item
							if (!$hasgrid) { $return .= "</div> <!-- row -->\n\n"; /*row*/ }
						} //if se o perfil tem acesso a esse campo!
						else
						{
							if($hasgrid) { $gc++; }
						}
					} //if edit === true
					if($hasgrid)
					{
						if($grid[$gc] == '|-')
						{
							$return .= "<div class='row clearfix'>\n"; $gc++;
							//inserts the section separator, if exists.
							if(intval($grid[$gc]) == 0 && $grid[$gc] != '|-' && $grid[$gc] != '-|' )
							{
								$return .= "<h3 class='section'>".$grid[$gc]."</h3>\n"; $gc++;
								$return .= "</div> <!-- row -->\n\n"; $gc++;
								$return .= "<div class='row clearfix'>\n"; $gc++;
							}
						}
						if($grid[$gc] == '-|') { $return .= "</div> <!-- row -->\n\n"; /*row*/ $gc++; }
					}

					//tratando as mascaras de campo
					if($valor->mask)
					{
						$return .= "\t<script type='text/javascript' charset='utf-8'> \$('#".$id_formulario." #item-".$valor->coluna." input').mask('".$valor->mask."') </script>\n\n";
					}
				} //foreach

				//checando se há algo a se colocar depois do formulario (campos por exemplo)
				$function_name = 'form_'.$this->__module_scheme->modulo."_append";
				if(function_exists($function_name))
				{
					$return .= $function_name('update', $modulo);
				}
			}

			$return .= "<div class='row'><div class='item large-12 columns text-right'><div class='input'><input class='button radius' type='Submit' accesskey='s' value='Alterar ".$this->__module_scheme->titulo."'></div></div></div>";
			$return .= "<input type='hidden' name='__dbo_update_flag' value='".$update."'>\n\n";
			$return .= CSRFInput();
			$return .= "</form></div></div></span>"; //.dbo-element

			echo $return;

			//validacao do formulario, se houver.
			$this->getValidationEngine($id_formulario, $modulo);
		} //ok()
	} //getUpdateForm

	/*
	* ===============================================================================================================================================
	* Gera a interface de administração automaticamente =============================================================================================
	* ===============================================================================================================================================
	*/
	function autoAdmin($opt = '')
	{

		//se não tiver permissão de listar, fora.
		if(DBO_PERMISSIONS)
		{
			if(!hasPermission('access', $_GET['dbo_mod']))
			{
				setMessage("<div class='error'>Seu usuário não tem permissão de acesso a essa página.</div>");
				$this->myHeader("Location: index.php");
			}
		}

		if($this->ok())
		{

			if(!is_object($this->__module_scheme))
			{
				echo "<h1 style='font-size: 21px; color: #C00;'>ERRO: A classe '".get_class($this)."' não possui esquema de módulo definido.</h1>";
			}
			else
			{

				global $_FILES;

				//controle criando MID para o modulo atual
				if(!$this->getMid())
				{
					$mid = $this->setMid($this->makeMid());
					header('Location: '.$this->keepUrl(array('mid='.$mid)));
					exit();
				}

				//checking if there is auto_order for ajax.
				if($_GET['dbo_auto_order_ajax'])
				{
					$this->autoOrderAjax($_GET);
				}

				//se houver um fixo, verifica se o MID atual é o do pai ou o meu proprio. se nao houver pai para mim, quer dizer que esse mid é do meu pai, entao preciso criar um pra mim  e setar o atual como meu pai.
				if($_GET['dbo_fixo'])
				{
					if(!$this->getModuleParent($this->getMid(), $_GET['dbo_fixo']))
					{
						$this->setModuleParent($this->getMid(), $_GET['dbo_fixo']);
					}
				}

				//checa se o tipo de MID é valido para o modulo atual
				if(!$this->midTypeCheck($this->getMid(), $this->__class))
				{
					header('Location: '.$this->keepUrl(array('!mid')));
					exit();
				}

				//checa para refazer a ordenação
				if($_GET['dbo_order_by'])
				{
					$this->setOrderBy($_GET['dbo_order_by']);
					header('Location: '.$this->keepUrl('!dbo_order_by'));
					exit();
				}

				//salva a definição do modulo em um lugar mais facil
				$meta = $this->__module_scheme;

				//tratando os campos fixos de dados
				$this->makeFixos($_GET['dbo_fixo']);

				//criando o schema de filtros
				$this->makeFilterScheme();

				if($_POST['__dbo_filter_flag'])
				{
					$this->setFilters($_POST);
					header('Location: '.$this->keepUrl());
					exit();
				}

				//delete automatico
				if($_GET['dbo_delete'])
				{
					
					CSRFCheckRequest();

					//checa se o usuário logado pode deletar deste modulo, senão, tchau!
					if(DBO_PERMISSIONS)
					{
						if(!hasPermission('delete', $_GET['dbo_mod']))
						{
							setMessage("<div class='error'>Seu usuário não tem permissão para realizar essa ação.</div>");
							$this->myHeader("Location: index.php");
						}
					}

					$obj = $this->newSelf();
					$obj->id = $this->sanitize($_GET['dbo_delete']);

					//executando pre_delete
					$func = $this->getModule()."_pre_delete";
					if(function_exists($func)) { $func($obj); }

					$obj->delete();

					//executando pos_delete
					$func = $this->getModule()."_pos_delete";
					if(function_exists($func)) { $func($obj); }

					if(function_exists("setMessage"))
					{
						setMessage("<div class='success'>".$this->__module_scheme->titulo." de ".$this->getFieldName($this->getPK())." ".$obj->id." removido com sucesso.</div>");
					}
					$this->myHeader("Location: ".$this->keepUrl("!dbo_delete&!deleted_because&!DBO_CSRF_token&!token"));
					exit();
				}

				//toggle de ativo/inativo
				if($_GET['dbo_toggle_active'] || $_GET['dbo_toggle_inactive'])
				{
					CSRFCheckRequest();
					if(DBO_PERMISSIONS && hasPermission('update', $_GET['dbo_mod']))
					{
						$mod_name = $this->__module_scheme->modulo;
						if($_GET['dbo_toggle_active'])
						{
							if(md5($_GET['dbo_toggle_active'].SALT_DBO_AUTO_ADMIN_TOGGLE_ACTIVE) == $_GET['token'])
							{
								$mod_toggle_obj = new $mod_name(dboescape($_GET['dbo_toggle_active']));
								$mod_toggle_obj->inativo = 0;
								$mod_toggle_obj->update();
							}
						}
						elseif($_GET['dbo_toggle_inactive'])
						{
							if(md5($_GET['dbo_toggle_inactive'].SALT_DBO_AUTO_ADMIN_TOGGLE_ACTIVE) == $_GET['token'])
							{
								$mod_toggle_obj = new $mod_name(dboescape($_GET['dbo_toggle_inactive']));
								$mod_toggle_obj->inativo = 1;
								$mod_toggle_obj->update();
							}
						}
						unset($mod_name);
						unset($mod_toggle_obj);
					}
				}

				//insert ou update automatico
				if($_POST['__dbo_insert_flag'] || $_POST['__dbo_update_flag']) 
				{

					//checa se o usuário logado pode inserir ou alterar deste modulo, senão, tchau!
					if(DBO_PERMISSIONS)
					{
						if($_POST['__dbo_insert_flag'])
						{
							if(!hasPermission('insert', $_GET['dbo_mod'])) //se pode inserir...
							{
								setMessage("<div class='error'>Seu usuário não tem permissão para realizar essa ação.</div>");
								$this->myHeader("Location: index.php");
							}
						} else {
							if(!hasPermission('update', $_GET['dbo_mod'])) //se pode alterar...
							{
								setMessage("<div class='error'>Seu usuário não tem permissão para realizar essa ação.</div>");
								$this->myHeader("Location: index.php");
							}
						}
					}

					$this->__update_id = dboescape($_POST['__dbo_update_flag']);
					$this->autoAdminInsertUpdate();
				}

				/* css para o autoadmin */
				if(DBO_INLINE_LOCAL_STYLES)
				{
					$this->localStyles();
				}
				?>

				<div class='wrapper-dbo-auto-admin' id='module-<?= $meta->modulo ?>'>

					<?
					//cria cabeçalho em caso de ser um form com valor fixo na URL (bread-crumb header)
					if(is_array($this->__fixos))
					{
					?>
						<div class='dbo-header row wrapper-module-fixos'>
							<div class='large-12 columns'>
							<? $this->makeBreadCrumb() ?>
							</div><!-- col -->
						</div>
					<?
					}
					?>


					<div class='row'>
						<div class='large-9 columns wrapper-module-id'>
							<h2><a href='<?= $this->keepUrl('!dbo_view&!dbo_update&!dbo_delete&!dbo_new&!pag') ?>'><?= $meta->titulo_plural ?></a></h2>
							<?
								$notification_function = $meta->modulo."_notifications";
								if(function_exists($notification_function))
								{
									$notf_return = $notification_function('message');
									$notf_tag_return = $notification_function();

									if($notf_return && $notf_tag_return)
									{
										?>
										<input type='button' name='' value="<?= $notf_tag_return ?> <?= $notf_return ?>" class="button round small <?= $meta->modulo ?>-notification-action"/>
										<?
									}
								}
							?>
						</div><!-- large-9 -->
						<div class='large-3 columns text-right wrapper-module-button-new'>
						<?
							//checa se mostra ou não o botão de inserir
							if(!DBO_PERMISSIONS || hasPermission('insert', $_GET['dbo_mod']))
							{
							?>
								<span class='button-new' rel='<?= $meta->modulo ?>'>
									<a class="button radius small top-14 trigger-dbo-auto-admin-inserir" href='<?= $this->keepUrl(array('dbo_new=1', '!dbo_update&!dbo_delete&!dbo_view')) ?>'  style="<?= (($_GET['dbo_update'] || $_GET['dbo_new'])?('display: none;'):('')) ?>"><i class="fi-plus"></i> Cadastrar Nov<?= $meta->genero ?></a>
									<a style="<?= (($_GET['dbo_update'] || $_GET['dbo_new'])?(''):('display: none;')) ?>" class="button radius secondary small top-14 trigger-dbo-auto-admin-cancelar-insercao-edicao" href='<?= $this->keepUrl(array('!dbo_update&!dbo_delete&!dbo_view&!dbo_new')) ?>'><i class="fi-x"></i> Cancelar <?= (($_GET['dbo_update'])?('Alteração'):('Inserção')) ?></a>
								</span>
							<?
							}
						?>
						</div><!-- col -->
					</div><!-- row -->
					

					<?
						if(!isset($meta->preload_insert_form) || $meta->preload_insert_form == TRUE || $_GET['dbo_new'])
						{
						?>
						<div class='<?= !$_GET['dbo_new'] ? 'hidden' : '' ?>' id='novo-<?= $meta->modulo ?>'>
							<div class='row'>
								<div class='large-12 columns'>
									<h3>Nov<?= $meta->genero ?> <?= $meta->titulo ?></h3>
								</div>
							</div><!-- row -->
							<?= ((!$_GET['dbo_update'])?($this->getInsertForm()):('')); ?>
						</div>
						<?
						}
					?>

					<div class='wrapper-auto-admin-view general-box'>
					<?
						//checa se o usuário pode visualizar o registro
						if(!DBO_PERMISSIONS || hasPermission('view', $_GET['dbo_mod']))
						{
							if($_GET['dbo_view'])
							{
								echo "<span id='view-anchor'>";
								$view_obj = $this->newSelf();
								$view_obj->id = $_GET['dbo_view'];
								$view_obj->load();
								echo $view_obj->autoAdminView();
								echo "</span>";
							}
						}
					?>
					</div>

					<?
						//checa se o usuário pode visualizar o registro
						if(!DBO_PERMISSIONS || hasPermission('update', $_GET['dbo_mod']))
						{
							if($_GET['dbo_update'])
							{
								?>
								<div class='row'>
									<div class='large-12 columns'>
										<h3>Alterar <?= $meta->titulo ?></h3>
									</div><!-- col -->
								</div><!-- row -->
								<?
								$this->getUpdateForm();
							}
						}
					?>

					<?
						//shows the list if you're not in the update or insert form.
						if(!$_GET['dbo_new'] && !$_GET['dbo_update'])
						{
							?>
							<div id='dbo-list'>

								<? $this->showFilterBox(); ?>

								<?
								/* executa a funcao append */
								$function_name = 'list_'.$this->__module_scheme->modulo."_prepend";
								if(function_exists($function_name))
								{
									echo $function_name(clone $this);
								}

								/* verifica se existe uma função pos-list, se sim, clona o obj para poder passar a lista de elementos que serao listados */

								$function_name = 'list_'.$this->__module_scheme->modulo."_append";
								if(function_exists($function_name))
								{
									$append = $function_name(clone $this);
								}

								?>
								<div class='row' id='list-<?= $meta->modulo ?>'>
									<div class='large-12 columns'>
										<div class='row'>
											<div class='large-12 columns text-right'>
												<?= $this->showFilterButton(); ?>	
											</div><!-- col -->
										</div><!-- row -->

										<div class='anchor-get-list'>
										<?
											//executes the pre_list function, if exists.
											$func = $this->getModule()."_pre_list";
											if(function_exists($func)) { $func(); }

											$this->getList();

											//executes the pos_list function, if exists... recieves the ids of the listed elements as parameter
											$func = $this->getModule()."_pos_list";
											if(function_exists($func)) { $func($this->__listed_elements); }
										?>
										</div><!-- anchor-get-list -->
									</div>
								</div>

								<?= $append ?>

							</div>
							<?
						}
					?>
				</div><!-- wrapper-dbo-auto-admin -->

				<?
				/* Scripts para o autoadmin */
				if(!$_GET['noscript']) { $this->localScripts(); }
			}//modulo
		} //ok()
	} // autoAdmin()

	/*
	* ===============================================================================================================================================
	* Função para impressão dos JS necessários para o funcionamento do auto-admin ===================================================================
	* ===============================================================================================================================================
	*/
	function localScripts ()
	{
		$meta = $this->__module_scheme;
		?>
		<script type='text/javascript' charset='utf-8'>

			//para carregar coisas com ajax
			function ajaxLoad(alvo, url, conteudo)
			{
				if(conteudo === undefined, dboInit()) {
					$(alvo).load(url);
				} else {
					$(alvo).load(url+" "+conteudo, alert('callback'));
				}
			}

			//esconde fixos com ajax
			function hideFixos ()
			{
				$('.hide-fixo').closest('.row').hide();
			}

			//limpar os precos
			$(document).on('click', '.trigger-clear-price', function(){
				clicado = $(this);
				clicado.closest('.item').find('.price').val('');
			});

			//toggle active e inactive
			$(document).on('click', '.trigger-dbo-auto-admin-toggle-active-inactive', function(e){
				e.preventDefault();
				clicado = $(this);
				peixeGet(clicado.attr('href'), function(d) {
					var html = $.parseHTML(d);
					/* item 1 */
					handler = '#'+clicado.closest('td').attr('id')+" .wrapper-lock";
					content = $(html).find(handler).html();
					if(typeof content != 'undefined'){
						$(handler).fadeHtml(content);
					}
				})
				return false;
			});

			//scripts para filtros
			$(document).on('click', ".filter-button", function(e){
				e.preventDefault();
				var botao = $(this);
				botao.hide();
				$('.wrapper-filter-box').slideDown();
			})

			$(document).on('click', '.filter-button-close', function(e){
				e.preventDefault();
				$('.wrapper-filter-box').slideUp(function(){
					$('.filter-button').show();
				})
			})

			$('.filter-type-data input.data-inicial, .filter-type-data input.data-final').mask('99/99/9999');

			$(document).on('change', '.filter-type-data input.data-inicial, .filter-type-data input.data-final', function(){
				var dinicial = $(this).closest('.input').find('input.data-inicial');
				var dfinal = $(this).closest('.input').find('input.data-final');
				var montada = $(this).closest('.input').find('input.data-montada');
				montada.val(dinicial.val()+'|---|'+dfinal.val());
			})

			$(document).on('click', '.trigger-dbo-auto-admin-delete', function(e){
				e.preventDefault();
				item = $(this).data('id');
				url = $(this).attr('href');
				<?
				if($this->hasDeletionEngine() && $this->hasField('deleted_because')){
					?>
					var ans = prompt("Digite a razão da exclusão do item "+item);
					if (ans!=null)
					{
						if($.trim(ans) != ''){
							document.location = url+"&deleted_because="+encodeURIComponent(ans);
						}
						else {
							setPeixeMessage("<div class='error'>Erro: As exclusões devem ser justificadas.</div>");
							showPeixeMessage();
						}
					}
					else {
						setPeixeMessage("<div class='success'>Exclusão cancelada.</div>");	
						showPeixeMessage();
					}
					<?
				}
				else 
				{
					?>
					var ans = confirm("Tem certeza que deseja excluir o item "+item+"?");
					if (ans==true) {
						document.location = url;
					} else {
						setPeixeMessage("<div class='success'>Exclusão cancelada.</div>");	
						showPeixeMessage();
					}
					<?	
				}
				?>
			});

			//filtros com ajax
			$('#form-dbo-filter').submit(function(){
				var target = $('.anchor-get-list');
				var final_data = '';
				var este_form = $(this);
				$.post(
					$(this).attr('action'),
					$(this).serialize(),
					function(data){
						data = $.parseHTML(data);
						final_data = $(data).find('.anchor-get-list').html();
						target.fadeTo('fast', 0, function(){
							target.html(final_data);
							target.fadeTo('fast', 1);
							dboInit();
						})
					}
				);
				return false;
			})

			//update quando clicar na linha
			$(document).on('click', '[data-update-url]', function(){
				document.location = $(this).data('update-url');
			});

			//e quebrando a propagação dos links
			$(document).on('click', '[data-update-url] a', function(e){
				e.stopPropagation();
			});

			//paginação com ajax
			$(document).on('click', '.pagination a', function(e){
				e.preventDefault();
				if($(this).closest('li').hasClass('unavailable') || $(this).closest('li').hasClass('current')){
					return false;
				}
				target = $('.anchor-get-list');
				url = $(this).attr('href');
				target.load(url+" .anchor-get-list", function(){ dboInit(); });
			})

			//ordenação com ajax
			$(document).on('click', 'a.dbo-link-order', function(e){
				e.preventDefault();
				target = $('.anchor-get-list');
				url = $(this).attr('href');
				target.load(url+" .anchor-get-list", function(){ dboInit(); });
			})


			$(document).on('click', '#dbo-button-limpar-filtros', function(){
				$(':input','#form-dbo-filter')
				.not(':button, :submit, :reset, :hidden')
				.val('')
				.removeAttr('checked')
				.removeAttr('selected');
				$(':input.clear-field', '#form-dbo-filter').val('');
				$('#form-dbo-filter').submit();
			})

			//handlers para o view
			$(document).on('click', 'div.fieldset .view-handle', function(){
				var tr = $(this).closest('tr');
				var target = $(this);

				$('div.fieldset tr').removeClass('active');
				$(tr).addClass('active');
				var fade = "fast";
				$('.wrapper-auto-admin-view').fadeTo(fade, 0, function(){
					$('.wrapper-auto-admin-view').load(target.closest('tr').attr('rel')+" #view-anchor", null, function(){
					//$('.wrapper-auto-admin-view').load('bla.php .bg-header', null, function(){
						$('.wrapper-auto-admin-view').fadeTo(fade, 1);
						hideFixos();
					});
				});
			})

			//close do view
			$(document).on('click', '.view-button-close', function(e){
				e.preventDefault();
				$('.wrapper-auto-admin-view').fadeOut('fast', function(){
					$('div.fieldset tr').removeClass('active');
				});
			})

			//enabling ordering for auto ordered modules
			<? if($this->isAutoOrdered()) { ?>

			var fixHelper = function(e, ui) {
				ui.children().each(function() {
					$(this).width($(this).width());
				});
				return ui;
			};

			$('table.auto-order tbody').sortable({
				helper: fixHelper,
				/*axis: 'y',*/
				cursor: 'resize-w',
				distance: 5,
				placeholder: "auto-order-place-holder",
				start: function(e, ui){
					ui.placeholder.html('<td colspan="30">&nbsp;</td>');
					ui.placeholder.height(ui.helper.height());
				},
				stop: function(){
					$.ajax({
						type: 'GET',
						url: '<?= $this->keepUrl("dbo_auto_order_ajax=1") ?>',
						data: $(this).sortable('serialize')
					})
				}
			}).disableSelection();

			<? } ?>

			<? if(!$_GET['dbo_update'] && (!isset($meta->preload_insert_form) || $meta->preload_insert_form == TRUE)) { ?>

				$(document).on('click', '.button-new', function(e){
					e.preventDefault();
					clicado = $(this);
					var $wrapper_novo = $('.wrapper-dbo-auto-admin #novo-'+$(this).attr('rel'));
					if($($wrapper_novo).hasClass('hidden'))
					{
						$wrapper_novo.fadeIn().removeClass('hidden');
						$('.trigger-dbo-auto-admin-inserir').fadeOut('fast', function(){
							$('.trigger-dbo-auto-admin-cancelar-insercao-edicao').fadeIn('fast');
						})
						$('#dbo-list').hide().addClass('hidden');
						$('.wrapper-auto-admin-view').hide();

						//tinymce rendering bug correction - basically, disables and re-enables tinyMCE to force match the text-area size.
						$('textarea.tinymce').each(function(){
							/*tinymce.editors[$(this).attr('id')].hide();
							tinymce.editors[$(this).attr('id')].show();*/
							/*tinymce.execCommand('mceRemoveControl', false, $(this).attr('id'));
							tinymce.execCommand('mceAddControl', false, $(this).attr('id'));*/
						})

					} else {
						$('.trigger-dbo-auto-admin-cancelar-insercao-edicao').fadeOut('fast', function(){
							$('.trigger-dbo-auto-admin-inserir').fadeIn('fast');
						})
						$wrapper_novo.fadeOut('fast', function(){
							$wrapper_novo.addClass('hidden');
							$('#dbo-list').fadeIn().removeClass('hidden');
							$('.wrapper-auto-admin-view').fadeIn();
						});
					}
				})

			<? } /* dbo_update */ ?>

			//inicializa tudo, pode ser chamada como callback de outra função (ajax por exemp.)
			function dboInit ()
			{

				/* datepickers */
				$('.datepick').each(function(){
					$(this).datepicker();
				});

				$('.datetimepick').each(function(){
					$(this).datetimepicker({
						dateFormat: 'dd/mm/yy',
						timeFormat: 'HH:mm',
						stepMinute: 5
					});
				});

				$('.datetimepick').each(function(){
					$(this).mask('99/99/9999 99:99');
				});

				//select2
				$('select.select2').each(function(){
					$(this).select2({
						minimumInputLength: (($(this).data('tamanho_minimo'))?($(this).data('tamanho_minimo')):(0))
					})
				})

				//multiselects
				$(".multiselect").each(function(){
					$(this).multiselect({sortable: false, searchable: true});
				})

				tinymce.init({
					selector: 'textarea.tinymce',
					height: '300',
					theme: 'dbo',
					plugins: [
						"advlist autolink lists link image charmap preview hr anchor pagebreak",
						"searchreplace wordcount visualblocks visualchars code fullscreen",
						"media nonbreaking save table contextmenu directionality",
						"emoticons template paste textcolor"
					],
					toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fullscreen | code",
					image_advtab: true
				});

				//precos
				$('.price.price-real').priceFormat({
					prefix: 'R$ ',
					centsSeparator: ',',
					thousandsSeparator: '.'
				});

				$('.price.price-generico').priceFormat({
					prefix: '$ ',
					centsSeparator: ',',
					thousandsSeparator: '.'
				});

				$('.price.price-dolar').priceFormat({
					prefix: 'US$ ',
					centsSeparator: '.',
					thousandsSeparator: ','
				});

				//tinymce
/*				$("textarea.tinymce").each(function(){
					$(this).tinymce();
				})*/

			} //dboInit();

			/* botoes ajax nas listagens */

			$(document).on('click', '.ajax-button', function(e){
				e.preventDefault();
				var partes = $(this).attr('href').split('?');
				$.get(partes[0], partes[1], function(data){
					var result = $.parseJSON(data);
					if(result.message){
						setMessage(result.message);
						showDboMessage();
					}
					if(result.html){
						$(result.html).each(function(){
							$(this.selector).html(this.content);
						})
					}
					if(result.append){
						//implementar
					}
					if(result.addClass){
						//implementar
					}
					if(result.removeClass){
						//implementar
					}
				})
			})

			$(document).ready(function(){

				dboInit();
					
				$(document).on('click', '.trigger-clear-closest-input', function(e){
					e.preventDefault();
					$(this).closest('.item').find('input').val('');
				});

			})
		</script>
		<?
	}
	/*
	* ===============================================================================================================================================
	* Função que gera o script de validação do formulario ==== ======================================================================================
	* ===============================================================================================================================================
	*/
	function getValidationEngine ($id_form, $modulo = false)
	{
		//checando se existe uma função custom para validação deste modulo
		$function_name = 'validation_'.$this->__module_scheme->modulo;

		if(function_exists($function_name)){
			echo $function_name(($modulo)?('update'):('insert'), (($modulo)?($modulo):($this)));
		}	

		?>
		<script type='text/javascript' charset='utf-8'>

			$(document).on('focus', '.validation-error input, .validation-error select, .validation-error textarea', function(){
				$(this).closest('.validation-error').removeClass('validation-error');
			});

			function validationEngine(form) {

				var error = false;
				var message = "";
				
				form.find('.item').removeClass('validation-error');
		
				//processamento

				/* inputs */
				form.find('input[type="text"].required:visible,input[type="password"].required:visible,input[type="date"].required:visible,input[type="datetime"].required:visible,input[type="datetime-local"].required:visible,input[type="month"].required:visible,input[type="week"].required:visible,input[type="email"].required:visible,input[type="number"].required:visible,input[type="search"].required:visible,input[type="tel"].required:visible,input[type="time"].required:visible,input[type="file"].required:visible,input[type="url"].required:visible,textarea.required:visible').each(function(){
					if($.trim($(this).val()) == ''){
						error = true;
						message += "\n- "+$(this).attr('data-name');
						$(this).closest('.item').addClass('validation-error');
					}					
				})

				/* selects */
				form.find('select.required:visible').each(function(){
					if($.trim($(this).val()) == '-1'){
						error = true;
						message += "\n- "+$(this).attr('data-name');
						$(this).closest('.item').addClass('validation-error');
					}					
				})

				/* radios e checkboxes */
				var old_name = '';
				form.find('input[type="radio"].required:visible, input[type="checkbox"].required:visible').each(function(){
					var name = $(this).attr('name');

					if(name != old_name){
						old_name = name;
						if(!$('input[name="'+name+'"]:checked').val()){
							error = true;
							message += "\n- "+$(this).attr('data-name');
							$(this).closest('.item').addClass('validation-error');
						}					
					}
				})

				if(error){
					alert("Os seguintes campos são obrigatórios:\n"+message);
					return false;
				}

				return true;
			}

			$(document).on('submit', '#<?= $id_form ?>', function(){
				if(!validationEngine($(this))){
					return false;
				}
				if(typeof <?= $function_name ?> == 'function') {
					if(!<?= $function_name ?>($(this))){
						return false;
					}
				}
				return true;
			});

		</script>
		<?
	}
	function getValidationEngineOld ($id_form, $meta = '')
	{
		?>
		<script type='text/javascript' charset='utf-8'>
			$('#<?= $id_form ?>').submit(function(){
				$('#<?= $id_form ?> .item').removeClass('validation-error');
				var i = 0;
				var campos = new Array();
				var message = new Array();

				//processamento
				<?
					foreach($meta as $campo)
					{
						$token = "#".$id_form." #item-".$campo->coluna;
						// TEXT ======================================================================================
						if($campo->tipo == 'text')
						{ ?>
							if(!$('<?= $token ?> input').val()) {
								$('<?= $token ?> input').addClass('candidate');
								message[i] = '- <?= $campo->titulo ?>';
								campos[i] = '<?= $campo->coluna ?>';
								i++;
							}
						<? }
						// PASSWORD ===============================================================================
						elseif($campo->tipo == 'password')
						{ ?>
							if(!$('<?= $token ?> input').val()) {
								$('<?= $token ?> input').addClass('candidate');
								message[i] = '- <?= $campo->titulo ?>';
								campos[i] = '<?= $campo->coluna ?>';
								i++;
							}
						<? }
						// TEXTAREA ==================================================================================
						elseif($campo->tipo == 'textarea')
						{ ?>
							if(!$('<?= $token ?> textarea').val()) {
								$('<?= $token ?> textarea').addClass('candidate');
								message[i] = '- <?= $campo->titulo ?>';
								campos[i] = '<?= $campo->coluna ?>';
								i++;
							}
						<? }
						// TEXTAREA-RICH ==================================================================================
						elseif($campo->tipo == 'textarea-rich')
						{
							?>
							if($('<?= $token ?> textarea').tinymce().getContent() == '') {
								$('<?= $token ?> textarea').addClass('candidate');
								message[i] = '- <?= $campo->titulo ?>';
								campos[i] = '<?= $campo->coluna ?>';
								i++;
							}
						<? }
						// RADIO =====================================================================================
						elseif ($campo->tipo == 'radio')
						{ ?>
							if(!$('<?= $token ?> input:checked').val()) {
								$('<?= $token ?> input').addClass('candidate');
								message[i] = '- <?= $campo->titulo ?>';
								campos[i] = '<?= $campo->coluna ?>';
								i++;
							}
						<? }
						// CHECKBOX ==================================================================================
						elseif ($campo->tipo == 'checkbox')
						{ ?>
							if(!$('<?= $token ?> input:checked').val()) {
								$('<?= $token ?> input').addClass('candidate');
								message[i] = '- <?= $campo->titulo ?>';
								campos[i] = '<?= $campo->coluna ?>';
								i++;
							}
						<? }
						// SELECT ====================================================================================
						elseif ($campo->tipo == 'select')
						{ ?>
							if($('<?= $token ?> select option:selected').val() == -1) {
								$('<?= $token ?> select').addClass('candidate');
								message[i] = '- <?= $campo->titulo ?>';
								campos[i] = '<?= $campo->coluna ?>';
								i++;
							}
						<? }
						// DATA ======================================================================================
						elseif ($campo->tipo == 'date')
						{ ?>
							if(!$('<?= $token ?> input').val()) {
								$('<?= $token ?> input').addClass('candidate');
								message[i] = '- <?= $campo->titulo ?>';
								campos[i] = '<?= $campo->coluna ?>';
								i++;
							}
						<? }
						// PLUGINS ==========================================================================
						elseif ($campo->tipo == 'plugin')
						{ ?>

						<? }
						// SINGLE JOIN ===============================================================================
						elseif ($campo->tipo == 'join')
						{
							if($campo->join->tipo == 'select')
							{
							?>if($('<?= $token ?> select option:selected').val() == -1) {
								$('<?= $token ?> select').addClass('candidate');
							<?
							} elseif($campo->join->tipo == 'radio') {
							?>if(!$('<?= $token ?> input:checked').val()) {
								$('<?= $token ?> input').addClass('candidate');
							<?
							}
							?>
								message[i] = '- <?= $campo->titulo ?>';
								campos[i] = '<?= $campo->coluna ?>';
								i++;
							}
						<? }
						// MULTI JOIN ============================================================================
						elseif ($campo->tipo == 'joinNN')
						{
							if($campo->join->tipo == 'select')
							{
							?>if(!$('<?= $token ?> select option:selected').val()) {
								$('<?= $token ?> select').addClass('candidate');
							<?
							} elseif($campo->join->tipo == 'checkbox') {
							?>if(!$('<?= $token ?> input:checked').val()) {
								$('<?= $token ?> input').addClass('candidate');
							<?
							}
							?>
								message[i] = '- <?= $campo->titulo ?>';
								campos[i] = '<?= $campo->coluna ?>';
								i++;
							}
						<? }
						// FILE / IMAGE ====================================================================
						elseif ($campo->tipo == 'file' || $campo->tipo == 'image')
						{ ?>
							if($('<?= $token ?> input[type=checkbox]').val()) //edição
							{
								if(!$('<?= $token ?> input[type=checkbox]:checked').val())
								{
									if(!$('<?= $token ?> input[type=file]').val())
									{
										$('<?= $token ?> input[type=file]').addClass('candidate');
										message[i] = '- <?= $campo->titulo ?>';
										campos[i] = '<?= $campo->coluna ?>';
										i++;
									}
								}
							} else { //inserção
								if(!$('<?= $token ?> input[type=file]').val())
								{
									$('<?= $token ?> input[type=file]').addClass('candidate');
									message[i] = '- <?= $campo->titulo ?>';
									campos[i] = '<?= $campo->coluna ?>';
									i++;
								}
							}
						<? }
					}
				?>

				if($(campos).length)
				{
					e.preventDefault();
					var first = campos[0];
					alert('Os seguintes campos são obrigatórios:\n\n'+message.join('\n'));
					$(campos).each(function(){
						if(this == first) { $('#<?= $id_form ?> #item-'+this+' .candidate').focus(); }
						$('#<?= $id_form ?> #item-'+this).addClass('validation-error');
						$('#<?= $id_form ?> #item-'+this+' .candidate').change(function(){
							$(this).closest('.validation-error').removeClass('validation-error');
						});
					})
				}
			})
		</script>
		<?
	}
	/*
	* ===============================================================================================================================================
	* Função para visualização de um item especifico do modulo ======================================================================================
	* ===============================================================================================================================================
	*/
	function autoAdminView ()
	{
		if($this->ok())
		{
			$meta = $this->__module_scheme;

			//tratando os campos fixos de dados
			$this->makeFixos($_GET['dbo_fixo']);

			$fixos = $this->__fixos;

			$view = $this->id;

			$modulo = $this->newSelf();
			$modulo->id = $view;
			$modulo->load();

			//global refferences to be used by functions, external queries, etc.
			$id = $modulo->id;

			if(!is_object($this->__module_scheme))
			{
				echo "<h1 style='font-size: 21px; color: #C00;'>ERRO: A classe '".get_class($this)."' não possui esquema de módulo definido.</h1>";
			}
			else
			{

				$return .= "<div class='row'><div class='columns large-12'><span class='dbo-element'><div class='viewset' style='clear: both;'><div class='content'><fieldset>";

				//checando se há grid de exibição de dados customizado... e setando variaveis para seu uso.
				if($this->hasGrid('view')) { $gc = 0; $hasgrid = true; $grid = $this->hasGrid('view'); }

				// Colocando os THs na tabela.

				$first = FALSE;

				$update_interaction = true;

				foreach($this->__module_scheme->campo as $chave => $valor)
				{

					$update_interaction = (($update_interaction === true)?($this->checkInteraction($modulo, $valor, $modulo->{$valor->coluna}, 'update')):($update_interaction));

					if($hasgrid)
					{
						if($grid[$gc] == '|-')
						{
							$return .= "<div class='row clearfix'>\n"; $gc++;
							//inserts the section separator, if exists.
							if(intval($grid[$gc]) == 0 && $grid[$gc] != '|-' && $grid[$gc] != '-|' )
							{
								$return .= "<div class='columns large-12'><h3 class='section subheader'>".$grid[$gc]."</h3></div>\n"; $gc++;
								$return .= "</div> <!-- row -->\n\n"; $gc++;
								$return .= "<div class='row clearfix'>\n"; $gc++;
							}
						}
						if($grid[$gc] == '-|') { $return .= "</div> <!-- row -->\n\n"; /*row*/ $gc++; }
					}
					if($valor->view === true)
					{
						if (!$hasgrid) { $return .= "<div class='row'>"; }
						$return .= "<div class='item columns ".(($hasgrid)?('large-'.$grid[$gc++]):(''))."'>\n";
						//$return .= (($valor->dica)?("<span class='dica'>".$valor->dica."</span>"):(''));
						$return .= "<label>".$valor->titulo."</label>\n";

						//dando um destaque para o primeiro elemento, potencialmente o "titulo" do view atual.
						if($valor->tipo == 'text' && !$first)
						{
							$first_class = 'field-first';
							$first = TRUE;
						}
						$return .= "<div class='field input-".$valor->tipo." ".$first_class."'>\n";
						$first_class = '';

						// TEXT ======================================================================================
						if($valor->tipo == 'text')
						{
							$return .= $this->clearValue($modulo->{$valor->coluna});
						}
						// PASSWORD ===============================================================================
						if($valor->tipo == 'password')
						{
							$return .= "********";
						}
						// TEXTAREA ==================================================================================
						if($valor->tipo == 'textarea')
						{
							$return .= nl2br($modulo->{$valor->coluna});
						}
						// TEXTAREA-RICH ==================================================================================
						if($valor->tipo == 'textarea-rich')
						{
							$return .= $modulo->{$valor->coluna};
						}
						// RADIO =====================================================================================
						elseif ($valor->tipo == 'radio')
						{
							foreach($valor->valores as $chave2 => $valor2)
							{
								$return .= (($modulo->{$valor->coluna} == $chave2)?($valor2):(''));
							}
						}
						// CHECKBOX ==================================================================================
						elseif ($valor->tipo == 'checkbox')
						{
							$database_checkbox_values = explode("\n", $modulo->{$valor->coluna});
							foreach($valor->valores as $chave2 => $valor2)
							{
								$return .= ((in_array($chave2, $database_checkbox_values))?($valor2."<br />"):(''));
							}
						}
						// PRICE ====================================================================================
						elseif ($valor->tipo == 'price')
						{
							if($valor->formato == 'real')
							{
								$return .= "R$ ".number_format($modulo->{$valor->coluna}, 2, ',', '.');
							}
							elseif($valor->formato == 'generico')
							{
								$return .= number_format($modulo->{$valor->coluna}, 2, ',', '.');
							}
							else
							{
								$return .= "US$ ".number_format($modulo->{$valor->coluna}, 2, '.', ',');
							}
						}
						// SELECT ====================================================================================
						elseif ($valor->tipo == 'select')
						{
							foreach($valor->valores as $chave2 => $valor2)
							{
								if($modulo->{$valor->coluna} == $chave2)
								{
									$return .= $valor2;
								}
							}
						}
						// DATA ======================================================================================
						elseif($valor->tipo == 'date')
						{
							if($this->clearValue($modulo->{$valor->coluna}) != null)
							{
								list($ano,$mes,$dia) = explode("-", $this->clearValue($modulo->{$valor->coluna}));
								if($dia == '00') { $val = ''; }
								else { $val = $dia."/".$mes."/".$ano; }
								$return .= $this->clearValue($val);
							}
							$return .= '';
						}
						// PLUGINS ==========================================================================
						elseif($valor->tipo == 'plugin')
						{
							$plugin = $valor->plugin;
							$plugin_path = DBO_PATH."/plugins/".$plugin->name."/".$plugin->name.".php";
							$plugin_class = "dbo_".$plugin->name;
							//checa se o plugin existe, antes de mais nada.
							if(file_exists($plugin_path))
							{
								include_once($plugin_path); //inclui a classe
								$plug = new $plugin_class($plugin->params); //instancia com os parametros
								$plug->setData($this->clearValue($modulo->{$valor->coluna}));
								$return .= $plug->getView($valor->coluna); //pega os campos na visualização do registro
							}
							else { //senão, avisa que não está instalado.
								$return .= "O Plugin <b>'".$plugin->name."'</b> não está instalado";
							}
						} //plugins
						// SINGLE JOIN ===============================================================================
						elseif($valor->tipo == 'join')
						{
							$join = $valor->join;

							$obj = new Dbo($join->modulo);
							$obj->{$join->{chave}} = $this->clearValue($modulo->{$valor->coluna});

							//setando restricoes...
							$rest = '';
							if($valor->restricao) { eval($valor->restricao); }
							$rest .= " ORDER BY ".$valor->join->valor." ";

							$obj->load($rest);
/*							if($this->isFixo($valor->coluna))
							{
								if($obj->{$join->chave} == $this->isFixo($valor->coluna))
								{
									$return .= "<span class='hide-fixo' name='".$valor->coluna."' ".$obj->{$join->valor}."</span>";
								}
								$return .= "
									<script type='text/javascript' charset='utf-8'>
										$('span[name=".$valor->coluna."]').closest('.row').hide();
									</script>
								";
							}
							else
							{
							} */
							$return .= $obj->{$join->valor};
						} //single join
						// MULTI JOIN ============================================================================
						elseif($valor->tipo == 'joinNN')
						{
							$todoNN = array();
							$join = $valor->join;
							$obj = new Dbo($join->modulo);
							$cadastrados_array = array();

							//setando restricoes...
							$rest = '';
							if($valor->restricao) { eval($valor->restricao); }
							$rest .= " ORDER BY ".$valor->join->valor." ";

							$obj->loadAll($rest);

							$cadastrados = new Dbo($join->tabela_ligacao);
							$cadastrados->{$join->chave1} = $modulo->id;
							$cadastrados->loadAll();
							do {
								$cadastrados_array[] = $cadastrados->{$join->chave2};
							}while($cadastrados->fetch());
							do {
								if(in_array($obj->{$join->chave}, $cadastrados_array))
								{
									$todoNN[] = $obj->{$join->valor};
								}
							}while($obj->fetch());
							$return .= implode("<br>", $todoNN);
						}
						// IMAGE ============================================================================
						elseif($valor->tipo == 'image')
						{
							if(file_exists(DBO_IMAGE_UPLOAD_PATH."/".$this->clearValue($modulo->{$valor->coluna})) && strlen($modulo->{$valor->coluna}))
							{
								$return .= "<a rel='lightbox[album]' href=".DBO_IMAGE_HTML_PATH."/".$this->clearValue($modulo->{$valor->coluna})."><img src='".DBO_IMAGE_HTML_PATH."/".$this->clearValue($modulo->{$valor->coluna})."' class='thumb-lista'></a>";
							}
						}
						// FILE ============================================================================
						elseif($valor->tipo == 'file')
						{
							if(strlen($modulo->{$valor->coluna}))
							{
								$return .= $this->getDownloadLink($modulo->{$valor->coluna});
							}
						}
						// QUERY ================================================================================
						elseif ($valor->tipo == 'query') {
							$query = '';
							eval($valor->query);
							$res_query = dboQuery($query);
							if(mysql_affected_rows())
							{
								$lin_query = mysql_fetch_object($res_query);
								$val = $lin_query->val;
							}
							if($val !== false)
							{
								$return .= $val;
							}
						}
						$return .= "</div>\n"; //input
						$return .= "\n</div>\n"; //item
						if (!$hasgrid) { $return .= "</div> <!-- row -->\n\n"; /*row*/ }
					}
					if($hasgrid)
					{
						if($grid[$gc] == '|-')
						{
							$return .= "<div class='row clearfix'>\n"; $gc++;
							//inserts the section separator, if exists.
							if(intval($grid[$gc]) == 0 && $grid[$gc] != '|-' && $grid[$gc] != '-|' )
							{
								$return .= "<h3 class='section'>".$grid[$gc]."</h3>\n"; $gc++;
								$return .= "</div> <!-- row -->\n\n"; $gc++;
								$return .= "<div class='row clearfix'>\n"; $gc++;
							}
						}
						if($grid[$gc] == '-|') { $return .= "</div> <!-- row -->\n\n"; /*row*/ $gc++; }
					}
				} //foreach
			}


			$return .= "<div class='row'><div class='item large-12 columns text-right'> ";

			//checa se exitem botoes customizados no modulo
			if(is_array($this->__module_scheme->button))
			{
				foreach($this->__module_scheme->button as $chave => $botao)
				{
					if(!DBO_PERMISSIONS || hasPermission($botao->value, $_GET['dbo_mod']))
					{
						if($botao->custom === TRUE) //botoes customizados. o codigo bem do arquivo de definição do modulo.
						{
							$id = $modulo->id;
							eval(str_replace("[VALUE]", $botao->value, $botao->code));
							$return .= str_replace("tiny", "small", $code)." ";
						} else {
							$return .= "<a class='button small radius no-margin' href='".$this->keepUrl(array("dbo_mod=".$botao->modulo."&dbo_fixo=".$this->encodeFixos($botao->modulo_fk."=".$modulo->{$botao->key}), "!pag&!dbo_insert&!dbo_update&!dbo_delete&!dbo_view"))."'>".$botao->value."</a> ";
						}
					}
				}//foreach
			}//if

			if(!DBO_PERMISSIONS || hasPermission('update', $_GET['dbo_mod']))
			{
				$return .= (($update_interaction)?("<a class='button small radius no-margin' href='".$this->keepUrl(array('dbo_update='.$modulo->id, '!dbo_view'))."'>Alterar</a>"):(''));
			}

			$return .= " <a href='' class='view-button-close button secondary small radius no-margin'>Fechar</a></div></div>"; //input //item //row (dos botoes customizados)

			//verifica se existem botoes de visualização recursive
			if(is_array($this->__module_scheme->button))
			{
				foreach($this->__module_scheme->button as $chave => $botao)
				{
					if($botao->view === TRUE && !$botao->custom)
					{
						$obj_botao = new dbo($botao->modulo);
						$obj_botao->{$botao->modulo_fk} = $modulo->id;
						$obj_botao->loadAll();
						$obj_botao->setFixo($botao->modulo_fk, $modulo->id);
						if($obj_botao->size())
						{
							$return .= "<div class='row'><div class='item'>";
							$return .= "<div class='recursive'>";
							$return .= "<h1>".$botao->value."</h1>";
							do {
								$return .= $obj_botao->autoAdminView();
							}while($obj_botao->fetch());
							$return .= "</div></div></div>";
						}
					} //view == TRUE
				}//foreach
			}//if

			$return .= "</div></div></span></fieldset></div></div>"; //content //viewset //dbo-element //fieldset //12-columns //row

			return $return;

		} //ok()
	}
	/*
	* ===============================================================================================================================================
	* Função para inserção / editar no banco a partir do admin automatico ===========================================================================
	* ===============================================================================================================================================
	*/
	function autoAdminInsertUpdate ()
	{
		global $_GET;
		global $_POST;
		global $_FILES;
		global $__dbo_auto_fields;

		//checando CSRF
		CSRFCheckRequest();

		if($_POST['__dbo_update_flag']) //se for pra dar update...
		{
			$this->id = $this->sanitize($_POST['__dbo_update_flag']);
		}

		//executando pre_update e pre_insert
		if($_POST['__dbo_update_flag']) { //update
			$func = $this->getModule()."_pre_update";
			if(function_exists($func))
			{
				$func($this);
			}
		} else { //insert
			$func = $this->getModule()."_pre_insert";
			if(function_exists($func))
			{
				$func();
			}
		}

		foreach($this->__module_scheme->campo as $chave => $campo)
		{
			//treating the automatic fields
			if(in_array($campo->coluna, $__dbo_auto_fields))
			{
				if(!$_POST['__dbo_update_flag']) //insert
				{
					//created_by -------------------------------------------------------
					if($campo->coluna == 'created_by')
					{
						$this->created_by = loggedUser();
					}
					//created_on -------------------------------------------------------
					elseif($campo->coluna == 'created_on')
					{
						$this->created_on = $this->now();
					}
					//order_by -------------------------------------------------------
					elseif($campo->coluna == 'order_by')
					{
						$this->order_by = $this->getMaxOrderBy()+1;
					}
				}
				else //update
				{
					//updated_by -------------------------------------------------------
					if($campo->coluna == 'updated_by')
					{
						$this->updated_by = loggedUser();
					}
					//updated_on -------------------------------------------------------
					elseif($campo->coluna == 'updated_on')
					{
						$this->updated_on = $this->now();
					}
				}
			}
			//or the other ordinary fields.
			elseif($campo->{(($_POST['__dbo_update_flag'])?('edit'):('add'))} === TRUE && $this->perfilTemAcessoCampo($campo->perfil)) //checa se deve colocar na listagem de insert/update
			{
				// TEXT ======================================================================================
				if($campo->tipo == 'text')
				{
					$this->{$campo->coluna} = $_POST[$campo->coluna];
				}
				// PASSWORD ======================================================================================
				elseif($campo->tipo == 'password')
				{
					$this->{$campo->coluna} = $_POST[$campo->coluna];
				}
				// TEXTAREA ==================================================================================
				elseif($campo->tipo == 'textarea')
				{
					$this->{$campo->coluna} = $_POST[$campo->coluna];
				}
				// TEXTAREA-RICH ==================================================================================
				elseif($campo->tipo == 'textarea-rich')
				{
					$this->{$campo->coluna} = $_POST[$campo->coluna];
				}
				// RADIO =====================================================================================
				elseif ($campo->tipo == 'radio')
				{
					$this->{$campo->coluna} = (($campo->isnull && $_POST[$campo->coluna] == '-1')?($this->null()):($_POST[$campo->coluna]));
				}
				// CHECKBOX ==================================================================================
				elseif ($campo->tipo == 'checkbox')
				{
					if(is_array($_POST[$campo->coluna]))
					{
						$this->{$campo->coluna} = implode("\n", $_POST[$campo->coluna]);
					}
					else
					{
						$this->{$campo->coluna} = '';
					}
				}
				// PRICE ====================================================================================
				elseif ($campo->tipo == 'price')
				{
					$valor_price = $_POST[$campo->coluna];
					if($campo->formato == 'real' || $campo->formato == 'generico')
					{
						$replace_from = array('R$ ', '$ ', '.', ',');
						$replace_to = array('', '', '', '.');
						$valor_price = str_replace($replace_from, $replace_to, $valor_price);
					}
					else
					{
						$replace_from = array('US$ ', ',');
						$replace_to = array('', '');
						$valor_price = str_replace($replace_from, $replace_to, $valor_price);
					}
					$this->{$campo->coluna} = (($campo->isnull && $_POST[$campo->coluna] == '')?($this->null()):($valor_price));
					unset($valor_price);
				}
				// SELECT ====================================================================================
				elseif ($campo->tipo == 'select')
				{
					$this->{$campo->coluna} = (($campo->isnull && $_POST[$campo->coluna] == '-1')?($this->null()):($_POST[$campo->coluna]));
				}
				// DATA ======================================================================================
				elseif($campo->tipo == 'date')
				{
					/* se for nulo */
					if($campo->isnull && trim($_POST[$campo->coluna]) == '')
					{
						$this->{$campo->coluna} = $this->null();
					}
					else
					{
						list($dia, $mes, $ano) = explode("/", $_POST[$campo->coluna]);
						$val = $ano."-".$mes."-".$dia;
						$this->{$campo->coluna} = $val;
					}
				}
				// DATA ======================================================================================
				elseif($campo->tipo == 'datetime')
				{
					/* se for nulo */
					if($campo->isnull && trim($_POST[$campo->coluna]) == '')
					{
						$this->{$campo->coluna} = $this->null();
					}
					else
					{
						$this->{$campo->coluna} = dateTimeSQL($_POST[$campo->coluna]);
					}
				}
				// PLUGINS ==========================================================================
				elseif($campo->tipo == 'plugin')
				{
					$plugin = $campo->plugin;
					$plugin_path = DBO_PATH."/plugins/".$plugin->name."/".$plugin->name.".php";
					$plugin_class = "dbo_".$plugin->name;
					//checa se o plugin existe, antes de mais nada.
					if(file_exists($plugin_path))
					{
						include_once($plugin_path); //inclui a classe
						$plug = new $plugin_class($plugin->params); //instancia com os parametros
						$plug->setFormData($campo->coluna); //seta os dados que vem do formulário para o plugin processar.
						$this->{$campo->coluna} = $plug->getData(); //pega os dados processados para o banco de dados.
					}
					else { //senão, avisa que não está instalado.
						$return .= "O Plugin <b>'".$plugin->name."'</b> não está instalado";
					}
				} //plugins
				// JOIN ======================================================================================
				elseif($campo->tipo == 'join')
				{
					$this->{$campo->coluna} = (($campo->isnull && $_POST[$campo->coluna] == '-1')?($this->null()):($_POST[$campo->coluna]));
				}
				// MULTI join ======================================================================================
				elseif($campo->tipo == 'joinNN')
				{
					if(!$this->id) //insert
					{
						if(!$temp_id) { $temp_id = rand(100000000, 999999999); } //numero randomico para o id temporario, já que o id do objeto atual ainda nao existe.

						$acertar[$campo->join->tabela_ligacao]['chave'] = $campo->join->chave1; //vai marcando as tabelas que precisam ser acertadas quando acabar o insert, para o caso em que há mais de um campo NxN.

						foreach($_POST[$campo->coluna] as $chave2 => $valor2)
						{
							$obj = new Dbo($campo->join->tabela_ligacao);
							$obj->{$campo->join->chave1} = $temp_id;
							$obj->{$campo->join->chave2} = $valor2;
							$obj->save();
						}
					} else {

						//remove os que já estavam cadastrados
						$cadastrados = new Dbo($campo->join->tabela_ligacao);
						$cadastrados->{$campo->join->chave1} = $this->id;
						$cadastrados->loadAll();
						do {
							$cadastrados->delete();
						}while($cadastrados->fetch());

						//insere os novos
						foreach($_POST[$campo->coluna] as $atualizado)
						{
							$obj = new Dbo($campo->join->tabela_ligacao);
							$obj->{$campo->join->chave1} = $this->id;
							$obj->{$campo->join->chave2} = $atualizado;
							$obj->save();
						}
					}
				} //multi join
				// IMAGE ======================================================================================
				elseif($campo->tipo == 'image')
				{
					if(!$_POST['manter_atual_'.$campo->coluna])
					{
						$nome_arquivo = '';
						if($_FILES[$campo->coluna]['size'] > 0)
						{
							include_once(DBO_PATH."/core/classes/simpleimage.php"); //classe para fazer resize das imagens

							$nome_arquivo = time().rand(1,100).".jpg"; //criando um nome randomico para o arquivo

							foreach($campo->image as $chave2 => $valor2) //processando o resize para todos os tamanhos das imagens
							{
								$w = $valor2->width;
								$h = $valor2->height;
								$q = $valor2->quality;
								$prefix = $valor2->prefix;
								$image = new SimpleImage();
								$image->load($_FILES[$campo->coluna]['tmp_name']);
								if($w && !$h)
								{
									$image->resizeToWidth($w);
								}
								elseif ($h && !$w)
								{
									$image->resizeToHeight($h);
								}
								else
								{
									if($w >= $h) {
										$image->resizeToWidth($w);
									} else {
										$image->resizeToHeight($h);
									}
								}

								$caminho_arquivo = DBO_PATH."/upload/images/".$prefix.$nome_arquivo;
								$image->save($caminho_arquivo); //salvando o arquivo no server
							}
						}
						$this->{$campo->coluna} = $nome_arquivo; //salvando o nome do arquivo no banco.
					}
				} //image
				// FILE ======================================================================================
				elseif($campo->tipo == 'file')
				{
					if(!$_POST['manter_atual_'.$campo->coluna])
					{
						if($_FILES[$campo->coluna]['size'] > 0 )
						{
							$arquivo = $_FILES[$campo->coluna];

							$partes_nome = explode(".", $arquivo['name']);
							$extensao = $partes_nome[sizeof($partes_nome)-1]; //pega extensao do arquivo
							$novo_nome = time().rand(1,100).".".$extensao; //cria um novo nome randomico
							$string_arquivo = $arquivo['name']."\n".$novo_nome."\n".$arquivo['type']."\n".$arquivo['size']; //monta string com os dados do arquivo para o banco.

							echo $campo->coluna;

							if(move_uploaded_file($arquivo['tmp_name'], DBO_FILE_UPLOAD_PATH."/".$novo_nome)) { //se salvar no server,
								$this->{$campo->coluna} = $string_arquivo; //salvando os dados do arquivo no banco...
							}
							else {
								echo "erro ao enviar o arquivo";
								exit();
							}
						}
					}
				} //file
			} //if add/edit === true

			//put on the default values on insert
			if(
				!$_POST['__dbo_update_flag'] && 
				(
					$this->{$campo->coluna} == '' || 
					$this->{$campo->coluna} == null
				) && 
				isset($campo->default_value)
			)
			{
				$this->{$campo->coluna} = $campo->default_value;
			}

		} //foreach

		if($_POST['__dbo_update_flag'])
		{
			$new = $this->update();
		}
		else
		{
			$new = $this->save();
		}

		if($temp_id) //se foi insert de NxN...
		{
			foreach($acertar as $tabela_acerto => $dados_acerto)
			{
				$obj = new Dbo($tabela_acerto);
				$obj->{$dados_acerto['chave']} = $temp_id;
				$obj->loadAll();
				do {
					$obj->{$dados_acerto['chave']} = $new;
					$obj->update();
				} while($obj->fetch());
			}
		}//acerto de temp_id...

		//executando pos_update e pos_insert
		if($_POST['__dbo_update_flag']) { //update
			$func = $this->getModule()."_pos_update";
			if(function_exists($func))
			{
				$func($this);
			}
		} else { //insert
			$func = $this->getModule()."_pos_insert";
			if(function_exists($func))
			{
				$func($this);
			}
		}

		/* verificando se deve rodar uma funcao ou fazer um redirect por get */
		if($_GET['dbo_return_function'])
		{
			$function_name = $_GET['dbo_return_function'];
			if(function_exists($function_name))
			{
				$function_name((($_POST['__dbo_update_flag'])?('update'):('insert')), $this);
			}
		}

		/* verificando se deve fazer um redirect  */
		if($_GET['dbo_return_redirect'])
		{
			header("Location: ".$_GET['dbo_return_redirect']);
			exit();
		}

		//setando mensagens de sucesso
		if($new)
		{
			if(function_exists(setMessage))
			{
				if($_POST['__dbo_update_flag'])
				{
					setMessage("<div class='success'>".$this->__module_scheme->titulo." de ".$this->getFieldName($this->getPK())." ".$new." alterado com sucesso.</div>");
					$url = (($this->__module_scheme->auto_view)?($this->keepUrl(array('dbo_view='.$new, '!dbo_update'))):($this->keepUrl(array('!dbo_update'))));
				}
				else
				{
					setMessage("<div class='success'>".$this->__module_scheme->titulo." inserido com sucesso. ".$this->getFieldName($this->getPK()).": ".$new."</div>");
					$url = (($this->__module_scheme->auto_view)?($this->keepUrl(array('dbo_view='.$new, '!dbo_new'))):($this->keepUrl(array('!dbo_new'))));
				}
			}
			else
			{
				if($_POST['__dbo_update_flag'])
				{
					$url = $this->keepUrl(array('sucesso='.$new, '!dbo_update'));
				}
				else
				{
					$url = $this->keepUrl(array('sucesso='.$new, '!dbo_new'));
				}
			}
			$this->myHeader("Location: ".$url);
		}
	} // autorAdminInsert()

	/* funcão com os estilos aplicados no autoAdmin do dbo ================================================================= */
	function localStyles()
	{
		?>
			<!-- <link href="<?= DBO_URL ?>/core/structure.css" rel="stylesheet" type="text/css"> -->
			<!-- <link href="<?= DBO_URL ?>/core/styles.css" rel="stylesheet" type="text/css"> -->
		<?
	} //localStyles

} // Class DBO

/*
* ===============================================================================================================================================
* Cria una instancia de dbo para servir de uso de metodos =======================================================================================
* ===============================================================================================================================================
*/
$dbo = new Dbo;

?>