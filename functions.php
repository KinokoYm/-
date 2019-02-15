<?php 
	require_once dirname(__FILE__).'/config.php';
	
	session_start();
	/**
	 * 获取当前登陆用户信息
	 * @return [type] [description]
	 */
	function muzi_get_current_user(){
		

		if(empty($_SESSION['current_logon_user'])){
    		header('location: /admin/login.php');
    		exit();//退出执行
  		}
  		return $_SESSION['current_logon_user'];


	}
	
	/**
	 * 用于数据库链接的封装
	 * @param  [type] $sql [description]
	 * @return [type]      [description]
	 */
	function muzi_connection($sql){
		$conn = mysqli_connect(muzi_DB_HOST,muzi_DB_USER,muzi_DB_PASS,muzi_DB_NAME);

	    if(!$conn){
	      exit('链接数据库失败!');
	    }

	    $query = mysqli_query($conn,$sql);

	    if(!$query){
		  //查询失败
	      return false;
	    }

	    $result = array();
	    while($row = mysqli_fetch_assoc($query)){
	      $result[] = $row;
	    }

	    return $result;
	    
	    mysqli_free_result($query);
	    mysqli_close($conn);

	    
	}
	
	/**
	 * 获取单条数据
	 * @param  [type] $sql [description]
	 * @return [type]      [description]
	 */
	function muzi_connection_one($sql){
		$res = muzi_connection($sql);	
		return isset( $res[0] ) ? $res[0] : null;
	}
	/**
	 * 执行增删改的操作
	 * @return [type] [description]
	 */
	function muzi_execute($sql){
		$conn = mysqli_connect(muzi_DB_HOST,muzi_DB_USER,muzi_DB_PASS,muzi_DB_NAME);

	    if(!$conn){
	      exit('链接数据库失败!');
	    }

	    $query = mysqli_query($conn,$sql);

	    if(!$query){
		  //查询失败
	      return false;
	    }

	    $affected = mysqli_affected_rows($conn);

	    mysqli_close($conn);

	    return isset($affected) ? $affected : 0;

	}


