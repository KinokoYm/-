  <?php 
    require_once dirname(__FILE__).'/../../functions.php';

    $current_page = isset($current_page) ? $current_page : '';  

    $current_user = muzi_get_current_user();


  ?>
 
  <div class="aside">
    <div class="profile">
      <img class="avatar" src="<?php echo $current_user['avatar'] ?>">
      <h3 class="name"><?php echo $current_user['nickname']; ?></h3>
    </div>
    <ul class="nav">
      <li <?php echo $current_page === 'index' ? ' class="active" ' : '' ;?> >
        <a href="/admin/index.php"><i class="fa fa-dashboard"></i>主页面</a>
      </li>

      <!-- 文章区域 -->
      <?php $menu_post = array('posts','post-add','categories'); ?>
      <li <?php  echo in_array($current_page, $menu_post)? ' class="active" ' : '' ; ?>>
        <a href="#menu-posts"  <?php  echo in_array($current_page, $menu_post)? '' : 'class="collapsed"' ; ?> data-toggle="collapse">
          <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-posts" class="collapse<?php echo in_array($current_page, $menu_post) ? ' in ' : '' ;?>">
          <li <?php echo $current_page === 'posts' ? ' class="active" ' : '' ;?> ><a href="/admin/posts/posts.php">所有文章</a></li>
          <li <?php echo $current_page === 'post-add' ? ' class="active" ' : '' ;?> ><a href="/admin/posts/post-add.php">写文章</a></li>
          <?php if ($current_user['slug'] == 'superadmin' ) : ?>
            <li <?php echo $current_page === 'categories' ? ' class="active" ' : '' ;?> ><a href="/admin/posts/categories.php">分类目录</a></li>
          <?php endif ?>
        </ul>
      </li>
      <!-- 文章区域结束 -->
      <!-- 评论区域 -->
        <li<?php echo $current_page === 'comment' ? ' class="active" ' : '' ;?>>
        <a href="/admin/comments.php"><i class="fa fa-comments"></i>评论</a>
        </li>
      
    
     <!-- 评论区域结束 -->
     <!-- 用户区域 -->
      <?php if ( $current_user['slug'] == 'admin'   || $current_user['slug'] == 'superadmin' ) : ?>
        <li <?php echo $current_page === 'user' ? ' class="active" ' : '' ;?>>
        <a href="/admin/user/users.php?page=1"><i class="fa fa-users"></i>用户</a>
      </li>
      <?php endif ?>
      <!-- 用户区域结束 -->
      
      <!-- 网站设置区域 -->
      <?php if ($current_user['slug'] == 'superadmin' ) : ?>
        <?php $menu_settings =array('nav-menus','slides','settings') ?>
        <li <?php echo in_array($current_page,$menu_settings)?' class="active" ' : '' ; ?>>
          <a href="#menu-settings" <?php  echo in_array($current_page, $menu_settings)? '' : 'class="collapsed"' ; ?> data-toggle="collapse">
            <i class="fa fa-cogs"></i>设置<i class="fa fa-angle-right"></i>
          </a>
          <ul id="menu-settings" class="collapse<?php echo in_array($current_page, $menu_settings)? ' in" ' : '' ; ?>">
            <li <?php echo $current_page === 'nav-menus' ? ' class="active" ' : '' ;?>><a href="/admin/setting/nav-menus.php">导航菜单</a></li>
            <li <?php echo $current_page === 'slides' ? ' class="active" ' : '' ;?>><a href="/admin/setting/slides.php">图片轮播</a></li>
            <li <?php echo $current_page === 'settings' ? ' class="active" ' : '' ;?>><a href="/admin/setting/settings.php">网站设置</a></li>
          </ul>
          
        </li>
      <?php endif ?>
      <!-- 网站设置区域结束 -->
    </ul>
  </div>