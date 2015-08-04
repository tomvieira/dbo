<?php 
	require_once('../../lib/includes.php'); 
	dboAuth('json');
	CSRFCheckJson();

	$json_result = array();



	echo json_encode($json_result);

?>