<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'opcao' ============================================ AUTO-CREATED ON 04/12/2013 08:28:16 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('opcao'))
{
	class opcao extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct('opcao');
			if($foo != '')
			{
				if(is_numeric($foo))
				{
					$this->id = $foo;
					$this->load();
				}
				elseif(is_string($foo))
				{
					$this->loadAll($foo);
				}
			}
		}

		//your methods here

		function set($nome = false, $valor = false, $pessoa = false)
		{
			$this->clearData();
			if(!strlen(trim($nome)) || $valor === false)
			{
				echo "ERRO (set): É necessário um nome e um valor para setar uma opção";
				exit();
			}
			elseif($pessoa !== false && $pessoa < 1)
			{
				echo "ERRO (set): Campo pessoa inválido (".$pessoa.")";
				exit();
			}
			else
			{
				$sql = "SELECT * FROM opcao WHERE nome = '".dboescape($nome)."' ".(($pessoa !== false)?(" AND pessoa = '".dboescape($pessoa)."' "):(" AND pessoa IS NULL "))." LIMIT 1";
				$this->query($sql);
				$this->nome = $nome;
				$this->valor = $valor;
				$this->pessoa = (($pessoa)?($pessoa):($this->null()));
				$this->saveOrUpdate();
			}
		}

		function get($nome, $pessoa = false)
		{
			$sql = "SELECT valor FROM opcao WHERE nome = '".dboescape($nome)."' ".(($pessoa)?(" AND pessoa = '".dboescape($pessoa)."' "):(''))." LIMIT 1";
			$res = dboQuery($sql);
			if(dboAffectedRows())
			{
				$lin = dboFetchObject($res);
				return $lin->valor;
			}
			return false;
		}

		function delete($nome, $pessoa = false)
		{
			if(!strlen(trim($nome)))
			{
				echo "ERRO (delete): É necessário um nome e um valor para setar uma opção";
				exit();
			}
			elseif($pessoa !== false && $pessoa < 1)
			{
				echo "ERRO (delete): Campo pessoa inválido (".$pessoa.")";
				exit();
			}
			else
			{
				$sql = "DELETE FROM opcao WHERE nome = '".dboescape($nome)."' ".(($pessoa)?(" AND pessoa = '".dboescape($pessoa)."' "):(' AND pessoa IS NULL '))." LIMIT 1";
				dboQuery($sql);
			}
		}

	} //class declaration
} //if ! class exists

?>