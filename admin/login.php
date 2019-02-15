<?php 
  
  require_once dirname(__FILE__).'/../config.php';
  //利用session打开一个门
  session_start();

  function login(){
    //进行邮箱是否为空的验证
    if(empty($_POST['email'])){
      $GLOBALS['message'] = '请正确的输入邮箱!';
      return;
    }
    //进行密码是否为空的验证
    if(empty($_POST['password'])){
      $GLOBALS['message'] = '请输入正确的密码!';
      return;
    }

    //如果进行到这里,那就提取数据,交给变量,方便操做
    $email = $_POST['email'];
    $password = $_POST['password'];

    //进行数据库尔的链接与操作
    $conn = mysqli_connect(muzi_DB_HOST,muzi_DB_USER,muzi_DB_PASS,muzi_DB_NAME);

    if(!$conn){
      exit('链接数据库失败!');
    }

    $query = mysqli_query($conn,"select * from users where email = '{$email}' limit 1 ; ");

    if(!$query){
      $GLOBALS['message'] = '登陆失败,请重试!';
      return;
    }

    $user = mysqli_fetch_assoc($query);

    if(!$user){
       $GLOBALS['message'] = '邮箱与密码不匹配!'; 
       return;
    }

    if($user['password'] !== $password){
      $GLOBALS['message'] = '邮箱与密码不匹配!';
      return;
    }

    //存放一个登陆标识
    $_SESSION['current_logon_user'] = $user;

    if($_GET['location'] == 'detail'){
      header('location: /detail.php?id='.$_GET['id']);
    }elseif ($_GET['location'] == 'postAdd') {
      header('location: /admin/posts/post-add.php');
    }else{
      header('location: /index.php');
    }
    

  }

  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    login();
  }


  if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout'){
    unset($_SESSION['current_logon_user']);
  }
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
</head>
<body>
  <div class="login">
    <form class="login-wrap<?php echo isset($GLOBALS['message']) ? ' shake animated' :'' ;?> " action="<?php $_SERVER['PHP_SELF'] ?>" method="post" novalidate autocomplete="off">
      <img class="avatar" src="/static/assets/img/default.png" height="100px">
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong> 用户名或密码错误！
      </div> -->
      <?php if (isset($GLOBALS['message'])): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong> <?php echo $GLOBALS['message']; ?>
        </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo isset( $_POST['email'])?  $_POST['email'] : '' ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>
</body>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
    
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>

  <script>
     $(function($){

        var emailFomat = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/



        $('#email').on('blur',function(){

          var value = $(this).val();

          if(!value || !emailFomat.test(value) ) return;

          $.get('/admin/avatar/avatar.php',{email : value},function(res){

            if(!res) return;

            $('.avatar').fadeOut(200,function(){
              $(this).on('load',function(){
                $(this).fadeIn()
              }).attr('src',res)
            })    

          })

        })
      })
  
  </script>
</html>