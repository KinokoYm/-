<?php 
	require_once dirname(__FILE__).'/../../functions.php';

	if(empty($_GET['id'])){
		exit('缺少参数!');
	}

	$id = $_GET['id'];

	var_dump($id);
	$rows = muzi_execute("delete from users where id in ({$id})  ;");

	var_dump($rows);
	// if(!$rows >0 ){ }

	header('location: '. $_SERVER['HTTP_REFERER']);
 ?>