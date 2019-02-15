<?php 
	require_once dirname(__FILE__).'/../../functions.php';

	if(empty($_GET['id'])){
		exit('缺少参数!');
	}

	$id = $_GET['id'];


	$rows = muzi_execute("DELETE from nav_menus WHERE id in ({$id})");


	// if(!$rows >0 ){ }

	header('location: /admin/setting/nav-menus.php');
 ?>