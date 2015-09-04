<?

require_once('lib/includes.php');

$term = trim(addslashes($_GET['term']));

if(hasPermission('gerenciar-estoque'))
{
	/* explodindo a pesquisa por espaço */
	$partes = explode(" ", $term);
	$terms = array();

	foreach($partes as $key => $value)
	{
		if(trim(addslashes($value)))
		{
			$terms[] = trim(addslashes($value));
		}
	}

	$sql_parts = array();
	foreach($terms as $value)
	{
		$sql_parts[] = " (

			nome LIKE '%".$value."%'

		) ";
	}

	if(sizeof($sql_parts))
	{
		$mat = new material();

		$sql = "
			SELECT
				id, nome, quantidade_estoque, unidade
			FROM
				".$mat->getTable()."
			WHERE
				".implode(" AND ", $sql_parts)."
			ORDER BY 
				nome
		";

		$res = dboQuery($sql);

		if(dboAffectedRows())
		{
			while($lin = dboFetchObject($res))
			{
				$resultados[] = array('id' => $lin->id, 'nome' => $lin->nome.' ('.$lin->quantidade_estoque.' '.$lin->unidade.')', 'unidade_medida' => $lin->unidade);
			}
		}
		else
		{
			$resultados[] = array('id' => '', 'nome' => 'Não há materiais correspondentes à busca', 'unidade' => '');
		}

		echo json_encode($resultados);
	}
}

?>