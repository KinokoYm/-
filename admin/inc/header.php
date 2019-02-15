<?php 
    require_once dirname(__FILE__).'/../../functions.php';

    $current_user = isset($_SESSION['current_logon_user']) ? $_SESSION['current_logon_user'] : '';

    $categories = muzi_connection("select * from nav_menus");
  ?>
  <div class="header">
  <h1 class="logo"><a href="index.php"><img src="/static/uploads/swipe/muzi.png" alt=""></a></h1>
  <ul class="nav">

    <?php foreach ($categories as $item): ?>
      <li><a href="./list.php?categories=<?php echo $item['slug'] ?>"><i class="<?php echo $item['icon'] ?>"></i><?php echo $item['text'] ?></a></li>
    <?php endforeach ?>

  </ul>
  <div class="avatar" style="border-bottom: 1px solid #eee;text-align: center;padding: 10px 0">
    <a href="/admin/index.php" style="text-align: center;display: inline-block;">
      <img src="<?php echo empty($current_user['avatar']) ? '/static/uploads/avatar.png' : $current_user['avatar']; ?>" style="height:80px;width:80px;border-radius: 50%;overflow: hidden;box-shadow: 0 0 1px 2px grey;">
    </a>
  </div>
  <div class="slink" style="margin-top: 10px">
    <?php if (empty($current_user)): ?>
      <div class="btn-group btn-group-justified" role="group" aria-label="...">
        <div class="btn-group " role="group">
          <a  class="btn btn-default " href="/admin/login.php" style="width: 73%;">登陆</a>
        </div>
        <div class="btn-group " role="group">
          <a  class="btn btn-default " href="/admin/login.php?location=postAdd" style="width: 73%;">发表文章</a>
        </div>
      </div>

    <?php else: ?>
      <!-- Split button -->
      <div class="btn-group">
        <button type="button" class="btn btn-default"><?php echo $current_user['nickname'] ?></button>
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
          <span class="sr-only">KinokoYm</span>
        </button>
        <ul class="dropdown-menu">
          <li><a href="/admin/index.php">个人主页</a></li>
          <li><a href="/admin/profile.php">个人中心</a></li>
          <li><a href="/admin/password-reset.php">修改密码</a></li>
          <li><a href="/admin/posts/post-add.php">发表文章</a></li>
          <li role="separator" class="divider"></li>
          <li><a href="/index.php?action=logout">退出登陆</a></li>
        </ul>
      </div>
    <?php endif ?>
    
  </div>
</div>