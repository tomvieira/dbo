<?

require_once('lib/includes.php');

$tabela_pessoa = $_pessoa->__module_scheme->tabela;

$term = trim(addslashes($_GET['term']));

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

$sql_parts = array('size' => 1);

foreach($terms as $value)
{
	$sql_parts[] = " (

		pes.nome LIKE '%".$value."%' OR 
		pes.apelido LIKE '%".$value."%' OR 
		pes.email LIKE '%".$value."%'

	) ";
}

if(sizeof($sql_parts))
{
	$sql = "

		SELECT 
			pes.id AS id,
			pes.nome AS nome,
			pes.email AS email,
			pes.telefone AS telefone
		FROM
			".$tabela_pessoa." pes
		WHERE
			".implode(" AND ", $sql_parts)."
		ORDER BY
			pes.nome

	";

	$res = dboQuery($sql);

	if(dboAffectedRows())
	{
		while($lin = dboFetchObject($res))
		{
			$resultados[] = array('id' => $lin->id, 'nome' => $lin->nome, 'email' => $lin->email, 'telefone' => $lin->telefone);
		}
	}
	else
	{
		$resultados[] = array('id' => '-1', 'nome' => "Usuário não encontrado. Utilize somente o nome, sem títulos.");
	}

	echo json_encode($resultados);
}

?>