<?php 
  require_once dirname(__FILE__).'/../functions.php';

   $current_user = muzi_get_current_user();
   $id = $current_user['id'];

   $user = muzi_connection("select * from users where id = '{$id}'")[0];
   

   function verify_profile(){
    global $current_user;
    global $user;
    global $id;

    $current_avatar = empty($_POST['avatar']) ?  $user['avatar'] :  $_POST['avatar'];
    $user['avatar'] = $current_avatar;

    $current_nickname = isset($_POST['nickname']) ? $_POST['nickname'] : $user['nickname'];
    $user['nickname'] = $current_nickname;

    $current_bio = isset($_POST['bio']) ? $_POST['bio'] : $user['bio'];
    $user['bio'] = $current_bio;
    
    //接收传送过来的值
    $avatar = $user['avatar'];
    $nickname = $user['nickname'];
    $bio = $user['bio'];

    $rows = muzi_execute("update users set avatar = '{$avatar}' ,nickname = '{$nickname}',bio = '{$bio}' ,status = 'activated'
                          where id='{$id}'");

    $GLOBALS['success'] = $rows > 0;
    $GLOBALS['message'] = $rows <=0 ? '修改失败!' : '修改成功!';

   }

   if($_SERVER['REQUEST_METHOD'] === 'POST'){
    verify_profile();
   }

   function muzi_convert_status($status){
      $data = array(
        'superadmin' =>'超级管理员' ,
        'admin' => '管理员',
        'user' => '普通用户'
       );

      return isset($data[$status]) ? $data[$status] : '';
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
        <h1>我的个人资料</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        
        <?php if ($success): ?>
          <div class="alert alert-success">
              <strong>成功！</strong><?php echo $message ?>
            </div>
          
        <?php else: ?>
          <div class="alert alert-danger">
              <strong>错误！</strong><?php echo $message ?>
          </div>

        <?php endif ?>

      <?php endif ?>
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label class="col-sm-3 control-label">头像</label>
          <div class="col-sm-6">
            <label class="form-image">
              <input id="avatar" type="file">
              <img src="<?php echo isset($current_user['avatar']) ? $current_user['avatar'] : '/static/assets/img/default.png' ?>">
              <input type="hidden" name="avatar" value="">
              <i class="mask fa fa-upload"></i>
            </label>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-3 control-label">邮箱</label>
          <div class="col-sm-6">
            <input id="email" class="form-control" name="email" type="type" value="<?php echo $current_user['email'] ?>" placeholder="邮箱" readonly>
            <p class="help-block">登录邮箱不允许修改</p>
          </div>
        </div>
        <div class="form-group">
          <label for="slug" class="col-sm-3 control-label">权限</label>
          <div class="col-sm-6">
            <input id="slug" class="form-control" name="slug" type="type" <?php echo  $current_user['slug'] == 'admin' ? 'readonly' : 'readonly' ?> value="<?php echo muzi_convert_status($current_user['slug']) ?>" placeholder="<?php echo $current_user['slug'] ?>">
            <p class="help-block"> </p>
          </div>
        </div>
        <div class="form-group">
          <label for="nickname" class="col-sm-3 control-label">昵称</label>
          <div class="col-sm-6">
            <input id="nickname" class="form-control" name="nickname" type="type" value="<?php echo $user['nickname'] ?>" placeholder="昵称" maxlength="16" minlength="2">
            <p class="help-block">限制在 2-16 个字符</p>
          </div>
        </div>
        <div class="form-group">
          <label for="bio" class="col-sm-3 control-label">简介</label>
          <div class="col-sm-6">
            <textarea id="bio" class="form-control" placeholder="Bio" cols="30" rows="6" name="bio"><?php echo isset($user['bio'] ) ? $user['bio'] : '毕业论文真麻烦！！！！！'?></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6">
            <button type="submit" class="btn btn-primary">更新</button>
            <a class="btn btn-link" href="./password-reset.php">修改密码</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <?php $current_page = 'profile' ?>
  <?php include 'inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>

  <script>
    
    $('#avatar').on('change',function(){
       var files = $(this).prop('files')
       var $this = $(this);
       if(!files.length) return

       var file = files[0]

       var data = new FormData() 

       data.append('avatar',file)

       var ajax = new XMLHttpRequest()

       ajax.open('post','/admin/avatar/upload.php')

       // ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded')

       ajax.send(data)

       ajax.onload = function(){
        // console.log(this.responseText)
        $this.siblings('img').attr('src',this.responseText)
        $this.siblings('input').val(this.responseText)
       }
    })
  </script>
</body>
</html>
