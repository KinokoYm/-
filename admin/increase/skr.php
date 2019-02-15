<?php 
	require_once dirname(__FILE__).'/../../functions.php';


	
	$id = $_GET['id'];

	$skr = $_POST['skr'];

	$rows = muzi_execute(" UPDATE posts set likes = '{$skr}' where id = {$id}  ");

	if($rows > 0) {
		$res = muzi_connection_one("select likes from posts where id = {$id} ");
	}
	
	echo json_encode($res);
 ?>