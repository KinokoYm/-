<?php 
	//预定义函数
	require_once dirname(__FILE__).'/../../functions.php';

	$current_user = muzi_get_current_user();
	
	//超级管理员可以看到所有人的评论,其他人不可以

    //=====================================================
	//取得？传递过来的第几页
	$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];

	//越过20条数据
	$size = 20;
	//当前页越过的数据
	$nowpage = ($page-1) * $size;


	if ($current_user['slug'] =='superadmin' || $current_user['slug'] =='admin') {
		$sql = sprintf("select comments.*,
		posts.title as post_title 
		from comments 
		inner join posts on comments.post_id = posts.id
		order by comments.created desc
		limit %s,%d;
		", $nowpage , $size);
	}else{
		$sql = sprintf("select comments.*,
		posts.title as post_title 
		from comments 
		inner join posts on comments.post_id = posts.id
		where posts.user_id ='{$current_user['id']}' and comments.status = 'approved'
		order by comments.created desc
		limit %s,%d;
		", $nowpage , $size);
	}
	

	//总条数
	if ($current_user['slug'] =='superadmin' || $current_user['slug'] =='admin' ) {
		$total_count = muzi_connection_one("select count(1) as count
		from comments
		inner join posts on comments.post_id = posts.id;
		")['count'];

	}else{
		$total_count = muzi_connection_one("select count(1) as count
		from comments
		inner join posts on comments.post_id = posts.id
		where posts.user_id ='{$current_user['id']}' and comments.status = 'approved'	
		")['count'];
	}
	//总页数
	//ceil 返回类型为float
	$total_page = ceil( $total_count/ $size );
	//评论数据
	$comments = muzi_connection($sql);





	$json = json_encode(array( 
		'total_page' => $total_page,
		'comments' => $comments
			));



	 

	//设置响应头
	header('Content-Type: application/json');

	echo $json;
 ?>
