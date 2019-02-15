<?php 
	require_once dirname(__FILE__).'/../../functions.php';

	    //获取提交的搜索内容
    if($_SERVER['REQUEST_METHOD'] =='GET'){
    
    $id = $_GET['id'];
    //=======================处理一些数据=========================
    
    //页号
    //每页的条数
    $size = 10;
    $page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
    //输入太小调整第一页
    if($page<1){
      $page = 1;
    }
    // $page = $page < 1 ? 1 : $page;

     //======================处理页码============================
      
        $total_count = (int)muzi_connection_one("
        select count(1) as num 
        from comments 
        inner join posts on posts.id = comments.post_id
        where posts.id = '{$id}' and comments.status = 'approved'
        ")['num'];

      

    //计算总页数
      $total_page = (int)ceil($total_count / $size);

      //==========================================================
      //输入太大调整最后一页
      if($page > $total_page){
        $page = $total_page;
      }
    
      //每页越过几条
      $skip = ($page - 1) * $size; 

      //======================数据库的查询=============================
        //获取分类的文章列表
         $detail_posts = muzi_connection("
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
		        where posts.id = '{$id}' and comments.status = 'approved'
		        order by comments.created desc
		        limit {$skip},{$size}
	      ");



        //显示5个，所以中间相距4个
        $begin = $page  - 3;
        $end = $begin + 6;


        if($begin<1){
          $begin = 1;
          $end = $begin +6;
        }


        if( $end > $total_page ){
          //end超出范围
          $end = $total_page ;
          $begin = $end - 6;
          
          if($begin<1){
            $begin = 1;

          }
        }



    }
    //设置响应头

    echo json_encode(array(
      'detail_posts' => $detail_posts,
      'current_page' => $_GET['page'])
  );
    
   
 ?>