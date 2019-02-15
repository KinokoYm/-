<?php 
    require_once dirname(__FILE__).'/functions.php';


    //获取最新的评论
    $new_comments = muzi_connection("
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
          where comments.status = 'approved'
          order by comments.created desc
          limit 10
      ");

    $categories = $_GET['categories'] ;
//*********************************************************************************
    // //获取分类的文章列表
    // $posts_list = muzi_connection("
    //   select 
    //   posts.`id`,
    //   posts.`slug`,
    //   posts.`title`,
    //   posts.`feature`,
    //   posts.`created`,
    //   posts.`content`,
    //   posts.`views`,
    //   posts.`likes`,
    //   posts.`status`,
    //   users.`nickname`,
    //   categories.`name`,
    //   categories.`slug` as category
    //   from posts
    //   inner join users on posts.user_id = users.id
    //   inner join categories on posts.category_id = categories.id
    //   where categories.slug = '{$categories}'
    //   order by posts.created desc
    //   limit 10
    //  ");
    $search = '';
    if(isset($_GET['categories'])){
      $search .= '&categories=' . $_GET['categories'];
    }
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
      if($_GET['categories'] == 'all'){
        $total_count = (int)muzi_connection_one("
        select count(1) as num from posts
        inner join categories on posts.category_id = categories.id
        inner join users on posts.user_id = users.id
        ")['num'];
      }else{
        $total_count = (int)muzi_connection_one("
        select count(1) as num from posts
        inner join categories on posts.category_id = categories.id
        inner join users on posts.user_id = users.id
        where categories.slug = '{$categories}'
        ")['num'];
      }
      

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
      if($_GET['categories'] == 'all'){
        $posts_list = muzi_connection("
        select 
        posts.`id`,
        posts.`slug`,
        posts.`title`,
        posts.`feature`,
        posts.`created`,
        posts.`content`,
        posts.`views`,
        posts.`likes`,
        posts.`status`,
        users.`nickname`,
        categories.`name`,
        categories.`slug` as category
        from posts
        inner join users on posts.user_id = users.id
        inner join categories on posts.category_id = categories.id
        order by posts.created desc
        limit {$skip},{$size}
       ");
      }else{
        $posts_list = muzi_connection("
        select 
        posts.`id`,
        posts.`slug`,
        posts.`title`,
        posts.`feature`,
        posts.`created`,
        posts.`content`,
        posts.`views`,
        posts.`likes`,
        posts.`status`,
        users.`nickname`,
        categories.`name`,
        categories.`slug` as category
        from posts
        inner join users on posts.user_id = users.id
        inner join categories on posts.category_id = categories.id
        where categories.slug = '{$categories}'
        order by posts.created desc
        limit {$skip},{$size}
       ");
      }
      


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


        //当前页码不为1时显示上一页
        if($page  > 1){
          $pagel = $page - 1 ;
        }
        //当前页码不为最大值时候显示下一页
        if($page < $total_page){
          $pager = $page + 1 ;
        }





    
//**********************************************************************************
    //获取标题
    $category = muzi_connection("select * from categories where slug = '{$categories}'");
    
    //获取评论数量
    function countComents($id) {
      $comments = muzi_connection("
      select count(1) as num 
      from comments 
      inner join posts on posts.id = comments.post_id
      where posts.id = '{$id}' and comments.status = 'approved'
      ");

      return $comments;
    }
    //获取页面内容
    $option = muzi_connection('select * from options');

  ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $option[2]['value'] ?></title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap-btn.css">
  <link rel="stylesheet" href="/static/assets/css/style.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="icon" href="<?php echo $option[1]['value'] ?>" type="image/x-icon" />
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <div class="wrapper">
    <!-- 载入公共的侧边栏模块 -->
    <?php include './admin/inc/header.php' ?>

    <div class="aside">
      <div class="widgets">
        <h4>搜索</h4>
        <!-- 加载搜索模块 -->
        <?php include './admin/inc/search.php' ?>   
      </div>
      <div class="widgets">
        <h4>最新评论</h4>
        <ul class="body discuz">
    
        <!-- 加载评论模块 -->
          <?php include './admin/inc/new_comments.php' ?>   

        </ul>
      </div>
    </div>
    <div class="content">
      <div class="panel new">

        <h3><?php echo empty($category[0]['name']) ? '所有文章' : $category[0]['name'] ?></h3>
        <?php foreach ($posts_list as $item): ?>
          <div class="entry">
            <div class="head">
              <span class="sort"><?php echo $item['name'] ?></span>
              <a id="viewsclick" data-id="<?php echo $item['id'] ?>" href="javascriptl:;"><?php echo $item['title'] ?></a>
            </div>
            <div class="main" style="height: 182.4px;">
              <p class="info"><?php echo $item['nickname'] ?>&nbsp;&nbsp;发表于&nbsp; <?php echo substr($item['created'], 0 , 10) ?></p>
              <p class="brief" style="text-indent:2em;"><?php echo mb_substr($item['content'],0,278,'utf-8'). '......'?></p>

              <p class="extra" style="position: absolute;bottom:0px;overflow: hidden; ">
                <span class="reading" id="viewscontent">阅读(<?php echo $item['views'] ?>)</span>
                <span class="comment">评论(<?php echo countComents($item['id'])[0]['num'] ?>)</span>
                <a href="javascript:;" class="like">
                  <i class="fa fa-thumbs-up"></i>
                  <span id="skr" data-id="<?php echo $item['id'] ?>" data-status="<?php echo $item['likes'] ?>">赞(<?php echo $item['likes'] ?>)</span>
                </a>
                <a href="./list.php?categories=<?php echo $item['category'] ?>" class="tags">
                  分类：<span><?php echo $item['name'] ?></span>
                </a>
              </p>
              <a href="javascript:;" class="thumb">
                <img src="<?php echo empty($item['feature']) ? '/static/uploads/swipe/logo.png' : $item['feature'] ?>" alt="" style="width: 180px ;height: 180px">
              </a>
            </div>
          </div>
        <?php endforeach ?>
        
   
      </div>
    </div>
    <div style="position: relative;height: 100px;overflow: hidden">
      <ul class="pagination pagination-lg pull-right" style="position: absolute;left: 50%;margin-left: -22%">
          <li><a href="?page=<?php echo 1 . $search?>">&laquo;</a></li>

          <?php if ( isset($pagel)): ?>
             <li><a href="?page=<?php echo $pagel . $search?>">上一页</a></li>
          <?php endif ?>
         

          <?php for( $i = $begin; $i <= $end ; $i++) : ?>
            <li<?php echo $i === $page ? ' class=" active"' : '' ?>><a href="?page=<?php echo $i . $search?>"><?php echo $i ?></a></li>
          <?php endfor ?>

          <?php if (isset($pager)): ?>
            <li><a href="?page=<?php echo $pager . $search?>">下一页</a></li>
          <?php endif ?>
          
          <li><a href="?page=<?php echo $total_page . $search?>">&raquo;</a></li>
        </ul>
    </div>
    <div class="footer">
      <p><?php echo $option[5]['value'] ?></p>
    </div>
  </div>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.min.js"></script>
  <script>
    $(function(){
      //添加点赞功能
      $('a').on('click','#skr',function(){
        //获取当前的文本
        var text = $(this).text()
        //获取id
        var id = $(this).data('id')
        //获取当前的状态
        var statusSkr = $(this).data('status')
        //记录当前的this
        var $that = $(this)
        //将点赞数目加1
        var skr = parseInt(text.replace(/[^0-9]/gi,"")) +1;
        //只能点赞一次
        if(skr - statusSkr == 1 ){
          
          $.post('./admin/increase/skr.php?id=' + id,{ skr: skr} ,function(res){
          var json = JSON.parse(res)
          $that.text("赞("+ json.likes +")")  
          })
        }
      })

       //添加阅读功能
      $('.head').on('click','#viewsclick',function(e){
        //阻止a链接的默认事件 
        e.preventDefault();
        //获取文章的id
        var id = $(this).data('id') 

        var text = $(this).parent().siblings('div').children('.extra').children('span:first-child').text()
        console.log(text)
        var viewsNum = parseInt(text.replace(/[^0-9]/gi,"")) +1;
        console.log(viewsNum)
        var $that = $(this).parent().siblings('div').children('.extra').children('span:first-child')

        $.post('./admin/increase/views.php?id=' + id,{ viewsNum: viewsNum} ,function(res){
          var json = JSON.parse(res)
          $that.text("阅读("+ json.views +")")  
          })

        window.location.href = './detail.php?id=' + id
        // window.event.returnValue=false;
      })


    })
  </script>
</body>

</html>
