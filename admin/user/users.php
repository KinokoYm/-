<?php 
  require_once dirname(__FILE__).'/../../functions.php';

  $current_user = muzi_get_current_user();

  $user = muzi_connection("select * from users"); 
  
  
  function inert_user(){
    global $user;

    //判断邮箱是否输入
    if(empty($_POST['email'])){
      $GLOBALS['message'] = "请输入邮箱!";
      return;
    }
    //循环判断邮箱是否重复
    foreach ($user as $value) {
      if($_POST['email'] == $value['email']){
        $GLOBALS['message'] = "邮箱重复!";
        return;
      }
    }
    //判断是否输入权限
    if(empty($_POST['slug'])){
      $GLOBALS['message'] = "请输入权限!";
      return;
    }

    //判断是否输入昵称
    if(empty($_POST['nickname'])){
      $GLOBALS['message'] = "请输入昵称!";
      return;
    }

    //判断是否输入密码
    if(empty($_POST['password'])){
      $GLOBALS['message'] = "请输入密码!";
      return;
    }

    $email = $_POST['email'];
    $slug = $_POST['slug'];
    $nickname = $_POST['nickname'];
    $password = $_POST['password'];
    $avatar = "/static/uploads/default.png";
    $status = "unactivated";

    $rows = muzi_execute("insert into users values(
                    null,
                    '{$slug}',
                    '{$email}',
                    '{$password}',
                    '{$nickname}',
                    '{$avatar}',
                    null,
                    '{$status}') ");

    $GLOBALS['success'] = $rows > 0;
    $GLOBALS['message'] = $rows <=0 ? '添加失败!' : '添加成功!';
  }


      //===================================================
  //====================接收筛选传参数================================
  //分类
    $search = '';
    if(isset($_GET['categories']) && $_GET['categories'] !== 'all'){
      $search .= '&categories=' . $_GET['categories'];
    }

    $current_update_page = isset($_GET['page']) ? $_GET['page'] : '';
    //超级管理员可以看到所有人的文章,其他人不可以
  //================================================================ 
   //=======================处理一些数据=========================
    $where = '1=1';
    $search = '';
    if(isset($_GET['categories']) && $_GET['categories'] !== 'all'){
      $where .= ' and users.slug = '. ' \'' .$_GET['categories'].'\' ' ;
      $search .= '&categories=' . $_GET['categories'];
    }

    //页号
    //每页的条数
    $size = 10;
    $GLOBALS['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page'];
    //输入太小调整第一页
    if($GLOBALS['page']<1){
      $GLOBALS['page'] = 1;
    }
    // $page = $page < 1 ? 1 : $page;

     //======================处理页码============================
      $total_count = (int)muzi_connection_one("
      select count(1) as num from users
      where {$where}
      ")['num'];

    //计算总页数
      $total_page = (int)ceil($total_count / $size);

      //==========================================================
      //输入太大调整最后一页
      if($GLOBALS['page'] > $total_page){
        $GLOBALS['page'] = $total_page;
      }
      //每页越过几条
      $skip = ($GLOBALS['page'] - 1) * $size; 


      //======================数据库的查询=============================

      $users = muzi_connection("
        select * 
        from users
        where {$where}
        limit {$skip},{$size}
        ");


        
 
        //显示5个，所以中间相距4个
        $GLOBALS['begin'] = $GLOBALS['page']  - 2;
        $GLOBALS['end'] = $GLOBALS['begin'] + 4;


        if($GLOBALS['begin']<1){
          $GLOBALS['begin'] = 1;
          $GLOBALS['end'] = $GLOBALS['begin'] +4;
        }


        if( $GLOBALS['end'] > $total_page ){
          //end超出范围
          $GLOBALS['end'] = $total_page ;
          $GLOBALS['begin'] = $GLOBALS['end'] - 4;
          
          if($GLOBALS['begin']<1){
            $GLOBALS['begin'] = 1;

          }
        }


        //当前页码不为1时显示上一页
        if($GLOBALS['page']  > 1){
          $GLOBALS['pagel'] = $GLOBALS['page'] - 1 ;
        }
        //当前页码不为最大值时候显示下一页
        if($GLOBALS['page'] < $total_page){
          $GLOBALS['pager'] = $GLOBALS['page'] + 1 ;
        }
        //当开始页码不等于1时显示省略号
        //当结束页码不等于最大时显示省略号
  


  if($_SERVER['REQUEST_METHOD'] === 'POST'){

    inert_user();

  }

  $users = muzi_connection("select * from users where {$where} limit {$skip},{$size}");

  
  /**
 * 将英文状态描述转换为中文
 * @param  string $status 英文状态
 * @return string         中文状态
 */
function convert_status ($status) {
  switch ($status) {
    case 'unactivated':
      return '未激活';
    case 'activated':
      return '已激活';
    case 'forbidden':
      return '禁止';  
    default:
      return '未知';
  }
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
      
    <?php include '../inc/navbar.php' ?>

    
    <div class="container-fluid">
      <div class="page-title">
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (!empty($message)): ?>
        
        <?php if (!empty($success)): ?>
          <div class="alert alert-success">
              <strong>成功！</strong><?php echo $message ?>
            </div>
          
        <?php else: ?>
          <div class="alert alert-danger">
              <strong>错误！</strong><?php echo $message ?>
          </div>

        <?php endif ?>

      <?php endif ?>
      
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
              <div id="verify" style="display: none">邮箱格式有错！</div>
            </div>
            <div class="form-group">
              <label for="slug">权限</label>
              <?php if ($current_user['slug'] == 'superadmin'): ?>
                <select name="slug" id="slug" class="form-control">
                  <option value="">请选择权限</option>
                  <option value="admin" <?php echo isset($_POST['slug']) && $_POST['slug'] == 'superadmin' ? ' selected' : ''; ?>>超级管理员</option>
                  <option value="admin" <?php echo isset($_POST['slug']) && $_POST['slug'] == 'admin' ? ' selected' : ''; ?>>管理员</option>
                  <option value="user" <?php echo isset($_POST['slug']) && $_POST['slug'] == 'user' ? ' selected' : ''; ?>>用户</option>
                </select>                
              <?php else: ?>
                <select name="slug" id="slug" class="form-control">
                  <option value="">请选择权限</option>
                  <option value="user" <?php echo isset($_POST['slug']) && $_POST['slug'] == 'user' ? ' selected' : ''; ?>>用户</option>
                </select>
              <?php endif ?>

              


            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称" value="<?php echo isset($_POST['nickname']) ? $_POST['nickname'] : ''; ?>">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <?php if ($current_user['slug'] == 'superadmin'): ?>
              <a class="btn btn-danger btn-sm" href="/admin/user/user-delete.php" style="display: none" id="btn_delete">批量删除</a>
            <?php endif ?>

            <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?> ">
              <select name="categories" class="form-control input-sm">
                <option value="all">所有分类</option>
                <option value="superadmin" <?php echo isset($_GET['categories']) && $_GET['categories']== 'superadmin' ? 'selected':''; ?>>超级管理员</option>
                <option value="admin" <?php echo isset($_GET['categories']) && $_GET['categories']== 'admin' ? 'selected':''; ?>>管理员</option>
                <option value="user" <?php echo isset($_GET['categories']) && $_GET['categories']== 'user' ? 'selected':''; ?>>用户</option>
              </select>

              <button class="btn btn-default btn-sm" type="submit">筛选</button>
            </form>
            <ul class="pagination pagination-sm pull-right">
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
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox" id="allselect"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>权限</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $item): ?>
                <tr>
                  <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
                  <td class="text-center"><img class="avatar" src="<?php echo $item['avatar'] ?>"></td>
                  <td><?php echo $item['email'] ?></td>
                  <td><?php echo $item['slug'] ?></td>
                  <td><?php echo $item['nickname'] ?></td>
                  <td><?php echo convert_status($item['status']) ?></td>
                  <?php if ($current_user['slug'] == 'superadmin'): ?>

                    <td class="text-center">
                      <a href="/admin/user/user-updata.php?id=<?php echo $item['id'] ?>" class="btn btn-default btn-xs">编辑</a>
                      <a href="/admin/user/user-delete.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                    </td>

                  <?php else: ?>

                    <?php if ( ($item['slug'] == 'superadmin') || ($item['slug'] == 'admin') ): ?>
                    <td class="text-center">权限不足</td>
                    <?php else: ?>
                    <td class="text-center">
                      <a href="/admin/user/user-updata.php?id=<?php echo $item['id'] ?>&page=<?php echo $current_update_page ?>" class="btn btn-default btn-xs">编辑</a>

                      <?php if ($item['slug'] == 'superadmin'): ?>
                        <a href="/admin/user/user-delete.php?id=<?php echo $item['id'] ?>&page=<?php echo $current_update_page ?>" class="btn btn-danger btn-xs">删除</a>
                      <?php endif ?>
                      
                    </td>
                    <?php endif ?>

                  <?php endif ?>
                  
                  
                </tr>
              <?php endforeach ?>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
  <?php $current_page = 'user' ?>
  <?php include '../inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script>
    $(function($){
        var $tbodyCheckboxs = $('tbody input');
        var $btnDelete = $('#btn_delete');
        var $allselect = $('#allselect');

        var checkedId = [];

        //全选按钮
        $allselect.on('change',function(){
          var id = $(this).data('id');
          var flag = false;
          //选中状态为true
          if($allselect.prop('checked')){
             $tbodyCheckboxs.each(function(i,item){
              $(item).prop('checked',true);
              // checkedId.splice($(item),checkedId.length);
              checkedId.push($(this).data('id'));
              //定义一个空数组
              var arr = [];
              //循环去除重复元素
              for(var i =0;i<checkedId.length;i++){
                if(arr.indexOf(checkedId[i]) == -1){
                  arr.push(checkedId[i])
                }
              }
              //将不重复的元素传给checkedId
              checkedId = arr;
              //将flag设置为true，在下面三元表达式显示批量删除按钮
              flag = true;
            })
          }else{
            //选中状态为false
            $tbodyCheckboxs.each(function(i,item){
              $(item).prop('checked',false);
              checkedId.splice($(item),checkedId.length);

            })
          }
          //根据flag判断是否出现批量删除按钮
          flag ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
          //根据console.dir（）来设置search
          $btnDelete.prop('search','?id='+ checkedId)

          // console.log(checkedId)
             
        })


        //单选控制全选按钮
        $tbodyCheckboxs.on('change',function(){
          var id = $(this).data('id');

          if($(this).prop('checked')){
            checkedId.push(id);
          }else{
            checkedId.splice(checkedId.indexOf(id),1);
          }

          if($tbodyCheckboxs.length === checkedId.length){
            $allselect.prop('checked',true)
          }else{
            $allselect.prop('checked',false)  
          }
          //调试bug代码
          // console.log('下main',$tbodyCheckboxs.length)
          // console.log($tbodyCheckboxs)
          // console.log('上',checkedId.length)
          // console.log(checkedId)
          checkedId.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
          $btnDelete.prop('search','?id='+ checkedId)
        })


        

        var emailFomat = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/

        $('#email').on('blur',function(){

          var value = $(this).val();

          if(!value || !emailFomat.test(value) ) return;

          $('#verify').style.display = block;
          

        })
      

    })
  </script>
</body>
</html>
