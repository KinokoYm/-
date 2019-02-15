<?php 
  require_once dirname(__FILE__).'/../../functions.php';

  $curent_user = muzi_get_current_user();

  

  function add_categories(){
    if(empty($_POST['name']) || empty($_POST['slug'])){
      $GLOBALS['message'] = '请填写完整数据！';
      $GLOBALS['success'] = false;
      return;
    }

    $name = $_POST['name'];
    $slug = $_POST['slug'];

    $rows = muzi_execute("insert into categories values(null, '{$slug}' ,'{$name}' );");

    $GLOBALS['success'] = $rows > 0;
    $GLOBALS['message'] = $rows <=0 ? '添加失败!' : '添加成功!';


  }

  function edit_categories(){
    global $current_edit_category;
 

    $name = isset($_POST['name']) ? $_POST['name'] : $current_edit_category['name'];
    $current_edit_category['name'] = $name;
    $slug = isset($_POST['slug']) ? $_POST['slug'] : $current_edit_category['slug'];
    $current_edit_category['slug'] = $slug;
    $id = $_GET['id'];

   $rows = muzi_execute("update categories set slug='{$slug}', name='{$name}' where id='{$id}'; ");

    $GLOBALS['success'] = $rows > 0;
    $GLOBALS['message'] = $rows <=0 ? '修改失败!' : '修改成功!';
  }
  //新增

  
  if (empty($_GET['id'])) {

  // 添加
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      add_categories();
    }

  } else {
    // 编辑
    // 客户端通过 URL 传递了一个 ID
    // => 客户端是要来拿一个修改数据的表单
    // => 需要拿到用户想要修改的数据
    $current_edit_category = muzi_connection_one('select * from categories where id = ' . $_GET['id']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      edit_categories();
    }
  }

  //查询显示逻辑
  $categories = muzi_connection("select * from categories");
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
        <h1>分类目录</h1>
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
      <div class="row">

        <?php if (isset($current_edit_category)): ?>
          <div class="col-md-4">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_edit_category['id']; ?>" method="post">
              <h2>编辑《<?php echo $current_edit_category['name']; ?>》</h2>
              <div class="form-group">
                <label for="name">名称</label>
                <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" autocomplete="off" value="<?php echo $current_edit_category['name']; ?>">
              </div>
              <div class="form-group">
                <label for="slug">别名</label>
                <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" autocomplete="off" value="<?php echo $current_edit_category['slug']; ?>">
                <p class="help-block"></p>
              </div>
              <div class="form-group">
                <button class="btn btn-primary" type="submit" >保存</button>
                <a class="btn btn-default btn-cancel" href="categories.php">取消</a>
              </div>
            </form> 
          </div>

        <?php else: ?>
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" autocomplete="off">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" autocomplete="off">
              <p class="help-block"></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit" >添加</button>
            </div>
          </form>
        </div>
        <?php endif ?>

        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm"  href="/admin/categories-delete.php" style="display: none">批量删除</a>
            <input class="btn btn-danger btn-sm " type="text" style="visibility:hidden;width: 0">
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox" id="allselect"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
            <?php if (isset($categories)): ?>
            
              
            
              <?php foreach ($categories as $item): ?>
                <tr>
                  <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
                  <td><?php echo $item['name'] ?></td>
                  <td><?php echo $item['slug'] ?></td>
                  <td class="text-center">
                    <a href="/admin/posts/categories.php?id=<?php echo $item['id'] ?>;" class="btn btn-info btn-xs">编辑</a>
                    <a href="/admin/posts/categories-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                  </td>
                </tr>
              <?php endforeach ?>
    
            <?php endif ?>
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
  <?php $current_page ='categories' ?>
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


      })
  </script>
</body>
</html>
