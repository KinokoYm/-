<?php 
	require_once dirname(__FILE__).'/../../functions.php';

	$current_user = muzi_get_current_user();

	

	function add_comments(){
	global $current_user;
		if(empty($_POST['comments'])){
			echo '0';
		    exit; // 不再往下执行	
		}
	$option = muzi_connection("select * from options");
	
	$author = $current_user['nickname'];
	$email = $current_user['email'];
	$created = $_POST['created'];
	$content = $_POST['comments'];

	// echo "Created: " . $created;
	// echo "Content: " . $content;
	// exit; 


	$status = $option[7]['value'] == '1' ? 'held' : 'approved';

	$posts_id = $_GET['id'];

	$rows = muzi_execute("
		insert into comments values(
		null,
		'{$author}',
		'{$email}',
		'{$created}',
		'{$content}',
		'{$status}',
		'{$posts_id}'
	)");

	// echo "Rows: " . var_dump($rows);

	// exit;

	if($rows > 0 ){
		echo '1';
		exit;
	}
	
	
	}
	



	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		add_comments();
	}

	
 ?>