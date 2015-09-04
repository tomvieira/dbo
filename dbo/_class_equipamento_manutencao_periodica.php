<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'equipamento_manutencao_periodica' ================= AUTO-CREATED ON 31/10/2013 11:08:29 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('equipamento_manutencao_periodica'))
{
	class equipamento_manutencao_periodica extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct('equipamento_manutencao_periodica');
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

		function getNomePeríodo($periodo, $valor = 1)
		{
			$nomes = array(
				'h' => 'hora',
				'd' => 'dia',
				'm' => 'mês',
				'Y' => 'ano',
			);
			$nomes_plural = array(
				'h' => 'horas',
				'd' => 'dias',
				'm' => 'meses',
				'Y' => 'anos',
			);
			return (($valor > 1)?($nomes_plural[$periodo]):($nomes[$periodo]));
		}

		function getSmartNome()
		{
			return $this->nome." (".$this->valor." ".$this->getNomePeríodo($this->periodo, $this->valor).")";
		}

	} //class declaration
} //if ! class exists

?>