<?

require_once('lib/includes.php');

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

$sql_parts = array();
foreach($terms as $value)
{
	$sql_parts[] = " (

		l.nome LIKE '%".$value."%' OR 
		l.sigla LIKE '%".$value."%' OR
		l.nome_alternativo LIKE '%".$value."%' OR
		l.numero LIKE '%".$value."%' OR

		lp.nome LIKE '%".$value."%' OR 
		lp.sigla LIKE '%".$value."%' OR
		lp.nome_alternativo LIKE '%".$value."%' OR
		lp.numero LIKE '%".$value."%' OR

		la.nome LIKE '%".$value."%' OR 
		la.sigla LIKE '%".$value."%' OR
		la.nome_alternativo LIKE '%".$value."%' OR
		la.numero LIKE '%".$value."%'

	) ";
}

if(sizeof($sql_parts))
{
	$sql = "

		SELECT 

			CONCAT(

				IFNULL(
					CASE 
						WHEN lp.pai <= 0 THEN ''
						ELSE CONCAT(
							(SELECT 
								CONCAT(
									CASE lavo.sigla
										WHEN '' THEN lavo.nome
										ELSE lavo.sigla
									END,
									CASE lavo.numero
										WHEN '' THEN ''
										ELSE CONCAT(' ', lavo.numero)
									END
								)
							FROM ".$_local->__module_scheme->tabela." lavo
							WHERE
								lavo.id = lp.pai
							),
							' - '
						)
					END,
					''
				),

				CASE 
					WHEN l.pai <= 0 THEN ''
					ELSE CONCAT(
						(SELECT 
							CONCAT(
								CASE lpai.sigla
									WHEN '' THEN lpai.nome
									ELSE lpai.sigla
								END,
								CASE lpai.numero
									WHEN '' THEN ''
									ELSE CONCAT(' ', lpai.numero)
								END
							)
						FROM ".$_local->__module_scheme->tabela." lpai
						WHERE
							lpai.id = l.pai
						),
						' - '
					)
				END,

				l.nome,
				CASE l.numero
					WHEN '' THEN ''
					ELSE CONCAT(' ', l.numero)
				END

			) AS nome,

			l.id AS id

		FROM 
			".$_local->__module_scheme->tabela." l

		LEFT JOIN ".$_local->__module_scheme->tabela." lp 
			ON l.pai = lp.id
		LEFT JOIN ".$_local->__module_scheme->tabela." la 
			ON lp.pai = la.id 

		WHERE

		".implode(" AND ", $sql_parts)."

		ORDER BY nome

	";

	$res = dboQuery($sql);

	if(dboAffectedRows())
	{
		while($lin = dboFetchObject($res))
		{
			$resultados[] = array('id' => $lin->id, 'local' => $lin->nome);
		}
	}
	else
	{
		$resultados[] = array('id' => '-1', 'local' => ERROR_LOCAL_404, 'depto' => '');
	}

	echo json_encode($resultados);
}

?>