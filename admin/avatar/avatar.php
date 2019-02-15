<?php 
	require_once dirname(__FILE__).'/../../config.php';

	if(empty($_GET['email'])){
		exit('请输入正确的邮箱!');
	}

	$email = $_GET['email'];



	$conn = mysqli_connect(muzi_DB_HOST,muzi_DB_USER,muzi_DB_PASS,muzi_DB_NAME);

	if(!$conn){
		exit('数据库链接失败!');
	}

	$query = mysqli_query($conn,"select avatar from users where email = '{$email}' limit 1 ;");

	if(!$query){
		exit('数据库查询失败！');
	}

	$row = mysqli_fetch_assoc($query);

	echo $row['avatar'];
 ?>	