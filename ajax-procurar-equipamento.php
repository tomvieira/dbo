<?

require_once('lib/includes.php');

$term = trim(addslashes($_GET['term']));

if(hasPermission('gerenciar-estoque'))
{
	/* explodindo a pesquisa por espaço */
	$terms = (array)$_GET['term'];

	$eq = new equipamento();
	$mat = new material();
	$tip = new tipo_equipamento();

	$sql_parts = array();
	foreach($terms as $value)
	{
		$sql_parts[] = " (

			equipamento.codigo = '".$value."' OR
			equipamento.numero_serie = '".$value."' OR
			equipamento.patrimonio = '".intval($value)."' OR
			equipamento.ai = '".intval($value)."'

		) ";
	}

	if(sizeof($sql_parts))
	{
		$sql = "
			SELECT
				equipamento.id,
				equipamento.modelo,
				equipamento.numero_serie,
				equipamento.codigo,
				equipamento.patrimonio,
				equipamento.ai,
				equipamento.status,
				equipamento.tipo_equipamento,
				equipamento_marca.nome AS marca,
				tipo_equipamento.nome AS nome,
				material.id AS material_id,
				material.nome AS material_nome,
				material.quantidade_estoque AS quantidade_estoque,
				material.unidade AS unidade_medida
			FROM
				equipamento
			LEFT OUTER JOIN tipo_equipamento ON
				equipamento.tipo_equipamento = tipo_equipamento.id
			LEFT JOIN material ON
				equipamento.vinculo_material = material.id
			LEFT JOIN equipamento_marca ON
				equipamento.equipamento_marca = equipamento_marca.id
			WHERE
				".implode(" AND ", $sql_parts)."
			ORDER BY 
				nome
		";

		$eq->query($sql);

		if($eq->size())
		{
			do {
				$resultados[] = array(
					'id' => $eq->id, 
					'nome' => (($eq->nome)?($eq->nome):('Equipamento indefinido')).((strlen(trim($eq->marca)))?(' '.$eq->marca):('')).((strlen(trim($eq->modelo)))?(' '.$eq->modelo):('')).((strlen(trim($eq->codigo)))?(' - Cod. '.$eq->codigo):('')).((strlen(trim($eq->patrimonio)))?(' - Pat. '.$eq->patrimonio):('')).((strlen(trim($eq->ai)))?(' - AI '.$eq->ai):('')).(' ('.$eq->getValue('status', $eq->status).')'), 
					'material_id' => (($eq->material_id)?($eq->material_id):('')), 
					'material_nome' => (($eq->material_nome)?($eq->material_nome.' ('.$eq->quantidade_estoque.')'):('')), 
					'unidade_medida' => $eq->unidade_medida,
					'status' => $eq->status,
					'indisponivel' => (($eq->status != STATUS_EQUIPAMENTO_NO_ESTOQUE)?(true):(false)),
					'modal_url' => $eq->getModalPermalink()
				);
			}while($eq->fetch());
		}
		else
		{
			$resultados[] = array('id' => '', 'nome' => 'Não há equipamentos correspondentes à busca', 'material_id' => '', 'material_nome' => '', 'unidade_medida' => '', 'status' => '', 'indisponivel' => false, 'modal_url' => '');
		}

		echo json_encode($resultados);
	}
}

?>