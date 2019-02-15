<?php 
	require_once dirname(__FILE__).'/../../functions.php';


	
	$id = $_GET['id'];

	$views = $_POST['viewsNum'];

	$rows = muzi_execute(" UPDATE posts set views = '{$views}' where id = {$id}  ");

	if($rows > 0) {
		$res = muzi_connection_one("select views from posts where id = {$id} ");
	}
	
	echo json_encode($res);
 ?>