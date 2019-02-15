<?php 
 //导入判断的函数
  require_once dirname(__FILE__).'/../functions.php';
  
  //判断用户是否登陆
  $current_user = muzi_get_current_user();
  /**
   * 全部的数量
   */
  //获取文章数量
  $posts_count = muzi_connection_one("select count(1) as num from posts where status='published';");
  //获取草稿数量
  $posts_count_dtafted = muzi_connection_one("select count(1) as num from posts where status ='drafted' ");
  //获取分类数量
  $categories_count = muzi_connection_one("select count(1) as num from categories;");
  //获取评论数量
  $comments_count = muzi_connection_one("select count(1) as num from comments;");
  //获取待审核数量
  $comments_count_held = muzi_connection_one("select count(1) as num from comments where status ='held';");
  /**
   * 个人的数量
   */
  //获取个人的文章数量
  $user_posts_count = muzi_connection_one("select count(1) as num from posts  where user_id = '{$current_user['id']}'");
  //获取个人草稿数量
  $user_posts_count_dtafted = muzi_connection_one("select count(1) as num from posts  where user_id = '{$current_user['id']}' and status = 'drafted'");
  //获取个人评论数量
  $user_comments_count = muzi_connection_one("select count(1) as num from comments inner join posts on posts.id = comments.post_id where posts.user_id ='{$current_user['id']}'");
  //获取个人待审核数量
  $user_comments_count_held = muzi_connection_one("select count(1) as num from comments inner join posts on posts.id = comments.post_id where posts.user_id ='{$current_user['id']}' and comments.`status` = 'held'");
  
  //获取页面内容
  $option = muzi_connection('select * from options');
 ?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title><?php echo $option[2]['value'] ?></title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="icon" href="<?php echo $option[1]['value'] ?>" type="image/x-icon" />
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
      
    <?php include 'inc/navbar.php' ?>

    
    <div class="container-fluid">
      <div class="jumbotron text-center">
        <h1>毕业设计</h1>
        <h3>校园文章发布管理系统</h3>
        <p><a class="btn btn-primary btn-lg" href="./posts/post-add.php" role="button">写文章</a></p>
      </div>
      <div class="row">
        <?php if ($current_user['slug'] == 'superadmin'): ?>
          <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item">
                <strong>
                <?php echo isset($posts_count['num'])?$posts_count['num'] : '0' ; ?>
                </strong>篇文章
              </li>

              <li class="list-group-item">
                <strong>
                <?php echo isset($categories_count['num'])?$categories_count['num'] : '0' ; ?>
                </strong>个分类
              </li>
              <li class="list-group-item">
                <strong>
                <?php echo isset($comments_count['num'])?$comments_count['num'] : '0' ; ?>
                </strong>条评论（<strong>
                <?php echo isset($comments_count_held['num'])?$comments_count_held['num'] : '0' ; ?>
                </strong>条待审核）
              </li>
            </ul>
          </div>
        </div>
        <?php endif ?>
        

        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">个人内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item">
                <strong>
                <?php echo isset($user_posts_count['num'])?$user_posts_count['num'] : '0' ; ?>
                </strong>篇文章（<strong>
                <?php echo isset($user_posts_count_dtafted['num'])?$user_posts_count_dtafted['num'] : '0' ; ?>
                </strong>篇草稿）
              </li>

              <li class="list-group-item">
                <strong>
                <?php echo isset($categories_count['num'])?$categories_count['num'] : '0' ; ?>
                </strong>个分类
              </li>
              <li class="list-group-item">
                <strong>
                <?php echo isset($user_comments_count['num'])?$user_comments_count['num'] : '0' ; ?>
                </strong>条评论（<strong>
                <?php echo isset($user_comments_count_held['num'])?$user_comments_count_held['num'] : '0' ; ?>
                </strong>条待审核）
              </li>
            </ul>
          </div>
        </div>

        <div class="col-md-4"></div>
      </div>
    </div>
  </div>
  
  <?php $current_page = 'index' ?>
  <?php include 'inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/chart/Chart.js"></script>
  <!-- <script>
  var ctx = document.getElementById('chart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            // labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
            datasets: [{
                data: [ <?php echo $posts_count['num']; ?>, <?php echo $categories_count['num']; ?>, <?php echo $comments_count['num']; ?>]
            }],

            labels: [
                '文章',
                '分类',
                '评论'
            ]              
        }
    });
  </script> -->
  <script>NProgress.done()</script>
</body>
</html>
