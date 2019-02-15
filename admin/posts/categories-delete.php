<?php 
	require_once dirname(__FILE__).'/../../functions.php';

	if(empty($_GET['id'])){
		exit('缺少参数!');
	}

	$id = $_GET['id'];


	$rows = muzi_execute("delete from categories where id in ({$id})  ;");


	// if(!$rows >0 ){ }

	header('location: /admin/posts/categories.php');
 ?>