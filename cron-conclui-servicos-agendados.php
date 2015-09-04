<?
	require_once('lib/includes.php');

	//pega todos os servicos que tem uma data_agendada_conclusao menores que now()
	$sql = "SELECT * FROM requisicao_item WHERE data_agendada_conclusao < '".dboNow()."'";
	$serv = new requisicao_item();
	$serv->query($sql);
	if($serv->size())
	{
		do {
			$serv->concluir();
		}while($serv->fetch());
	}
?>