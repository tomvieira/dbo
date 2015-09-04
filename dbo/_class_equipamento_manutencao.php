<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'equipamento_manutencao' =========================== AUTO-CREATED ON 31/10/2013 11:37:39 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('equipamento_manutencao'))
{
	class equipamento_manutencao extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct('equipamento_manutencao');
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
		function getTotalAtrasado()
		{
			$sql = "
				SELECT 
					COUNT(*) AS total
				FROM 
					equipamento_manutencao 
				WHERE 
					data_agendada IS NOT NULL AND
					data_realizada IS NULL AND
					data_agendada <= '".date('Y-m-d')."'
			";
			$res = dboQuery($sql);
			$lin = dboFetchObject($res);
			return $lin->total;
		}

		function getTotalAvisado()
		{
			$sql = "
				SELECT 
					COUNT(*) AS total
				FROM 
					equipamento_manutencao 
				WHERE 
					data_agendada IS NOT NULL AND
					data_realizada IS NULL AND
					data_agendada <= '".somaDataAMD(date('Y-m-d'), 0, 0, DIAS_DE_AVISO)."' AND
					data_agendada > '".somaDataAMD(date('Y-m-d'))."'
			";
			$res = dboQuery($sql);
			$lin = dboFetchObject($res);
			return $lin->total;
		}	
	
		function getManutencoesAtrasadas()
		{
			$sql = "
				SELECT 
					equipamento_manutencao.*,
					equipamento_marca.nome AS marca_nome,
					tipo_equipamento.nome AS tipo_equipamento_nome,
					equipamento.codigo AS codigo
				FROM 
					equipamento_manutencao,
					equipamento_marca,
					tipo_equipamento,
					equipamento
				WHERE 
					equipamento_manutencao.equipamento = equipamento.id AND
					equipamento_marca.id = equipamento.equipamento_marca AND
					tipo_equipamento.id = equipamento.tipo_equipamento AND
					data_agendada IS NOT NULL AND
					data_realizada IS NULL AND
					data_agendada <= '".date('Y-m-d')."'
			";
			$this->query($sql);
		}

		function getManutencoesAvisadas()
		{
			$sql = "
				SELECT 
					equipamento_manutencao.*,
					equipamento_marca.nome AS marca_nome,
					tipo_equipamento.nome AS tipo_equipamento_nome,
					equipamento.codigo AS codigo
				FROM 
					equipamento_manutencao,
					equipamento_marca,
					tipo_equipamento,
					equipamento
				WHERE 
					equipamento_manutencao.equipamento = equipamento.id AND
					equipamento_marca.id = equipamento.equipamento_marca AND
					tipo_equipamento.id = equipamento.tipo_equipamento AND
					data_agendada IS NOT NULL AND
					data_realizada IS NULL AND
					data_agendada <= '".somaDataAMD(date('Y-m-d'), 0, 0, DIAS_DE_AVISO)."' AND
					data_agendada > '".somaDataAMD(date('Y-m-d'))."'
			";
			$this->query($sql);
		}	
	
		function criaNovaManutencao($obj)
		{
			if(strlen(trim($_POST['nova_data_agendada'])))
			{
				$manut = new equipamento_manutencao();
				$manut->data_agendada = dataSQL($_POST['nova_data_agendada']);
				$manut->observacao = $_POST['nova_observacao'];
				if($_POST['nova_eh_periodica'])
				{
					$manut->eh_periodica = $_POST['nova_eh_periodica'];
					$manut->equipamento_manutencao_periodica = $_POST['nova_equipamento_manutencao_periodica'];
				}
				$manut->equipamento = $obj->equipamento;
				$manut->save();
			}
		}

	} //class declaration
} //if ! class exists

?>