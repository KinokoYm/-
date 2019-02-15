<?php 
  require_once dirname(__FILE__).'/../functions.php';

  $current_user = muzi_get_current_user();
  $id = $current_user['id'];
  $user = muzi_connection("select * from users where id = '{$id}'")[0];


  function update_password(){
    global $user;
    //验证文本框内容是否为空
    if(empty($_POST['oldPassword']) || empty($_POST['newPassword']) || empty($_POST['confirmNemPassword'])){
      $GLOBALS['message'] = "请输入密码！";
      return;
    }
    //验证2次输入的密码
    if($_POST['newPassword'] !== $_POST['confirmNemPassword']){
      $GLOBALS['message'] = "请输入两次相同的密码！";
      return;
    }
    if($_POST['oldPassword'] !== $user['password']){
      $GLOBALS['message'] = "密码错误！";
      return;
    }

    $confirmNemPassword = $_POST['confirmNemPassword'];
    $id = $user['id'];
    $rows = muzi_execute("update users set password = '{$confirmNemPassword}',status = 'activated' where id='{$id}'");
    
    if($rows > 0 ){
      $_SESSION['current_logon_user'] = null;

      header('location: /admin/login.php');
    }else{
      exit;
    }
   

  }

  if($_SERVER['REQUEST_METHOD'] == 'POST'){
    update_password();
  }
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
      <div class="page-title">
        <h1>修改密码</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $message ?>
        </div>
      <?php endif ?>
      
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        <div class="form-group">
          <label for="old" class="col-sm-3 control-label">旧密码</label>
          <div class="col-sm-7">
            <input id="old" class="form-control" type="password" placeholder="旧密码" name="oldPassword">
          </div>
        </div>
        <div class="form-group">
          <label for="password" class="col-sm-3 control-label">新密码</label>
          <div class="col-sm-7">
            <input id="password" class="form-control" type="password" placeholder="新密码" name="newPassword">
          </div>
        </div>
        <div class="form-group">
          <label for="confirm" class="col-sm-3 control-label">确认新密码</label>
          <div class="col-sm-7">
            <input id="confirm" class="form-control" type="password" placeholder="确认新密码" name="confirmNemPassword">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-7">
            <button type="submit" class="btn btn-primary">修改密码</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <?php $current_page = 'password-reset' ?>
  <?php include 'inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script>
    
  </script>
</body>
</html>
