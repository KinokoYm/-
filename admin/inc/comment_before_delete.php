<?php 
	require_once dirname(__FILE__).'/../../functions.php';


	$id = $_POST['id'];

	$rows = muzi_execute(" delete from comments where id = '{$id}'  ;");

	header('Content-Type: application/json');



	if($rows >0 ){ 
		echo json_encode($rows > 0);
	}

	// header('location: /admin/comments.php');
	// 不加头的话服务器回解析成为String
	

	
 ?>