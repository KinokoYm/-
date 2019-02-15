<?php 
	require_once dirname(__FILE__).'/../../functions.php';

	$current_user = muzi_get_current_user();

	$posts_id = $_POST['id'];

	$html = muzi_connection("
		select 
		users.id as user_id,
		users.avatar,
		comments.author,
		comments.content,
		comments.created,
		comments.id as comment_id,
		posts.id
		from comments
		inner join posts on comments.post_id = posts.id
		inner join users on users.nickname= comments.author
		where posts.id = '{$posts_id}' and comments.status = 'approved'
		order by comments.created desc
		limit 10
		");

	echo json_encode($html);
 ?>
