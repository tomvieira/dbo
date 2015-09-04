<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'requisicao' ======================================= AUTO-CREATED ON 26/07/2012 09:44:04 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('requisicao'))
{
	class requisicao extends dbo
	{

		var $status_names = array(
			'0' => 'Nova',
			'1' => 'Ag. aprov. da diretoria',
			'2' => 'Aprovado',
			'3' => 'Atribuído',
			'4' => 'Ag. requisitante',
			'5' => 'Em andamento',
			'6' => 'Concluído',
			'7' => 'Não aprov. diretoria',
			'8' => 'Cancelado'
		);

		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct(get_class($this));
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

		function getStatus()
		{
			return $this->getStatusName($this->status);
		}

		function getStatusName($status)
		{
			return $this->status_names[$status];
		}

		function getServicos($type = 'unique')
		{
			$servicos = array();

			$sql = "SELECT tipo_servico.nome, tipo_servico.id FROM tipo_servico WHERE id IN (SELECT tipo_servico FROM requisicao_item WHERE requisicao = '".addslashes($this->id)."' ) ORDER BY tipo_servico.nome";
			$res = dboQuery($sql);
			while($lin = dboFetchObject($res))
			{
				$servicos[$lin->id] = $lin->nome;
			}
			return $servicos;
		}

		function getDepartamentos()
		{
			$deptos = array();
			$deptos[] = 'Depto...';
			return $deptos;
		}

		function getBreadcrumbIdentifier()
		{
			return $this->id;
		}

	} //class declaration
} //if ! class exists

?>