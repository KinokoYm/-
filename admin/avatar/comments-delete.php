<?php 
	require_once dirname(__FILE__).'/../../functions.php';

	if(empty($_GET['id'])){
		exit('缺少参数!');
	}

	$id = $_GET['id'];


	$rows = muzi_execute("delete from comments where id in ({$id})  ;");


	// if(!$rows >0 ){ }

	// header('location: /admin/comments.php');
	// 不加头的话服务器回解析成为String
	header('Content-Type: application/json');

	echo json_encode($rows > 0);
 ?>