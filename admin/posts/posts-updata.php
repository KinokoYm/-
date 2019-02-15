<?php 
  require_once dirname(__FILE__).'/../../functions.php';
  //获取用户登陆信息
  $current_user = muzi_get_current_user();
 
  $id = $_GET['id'];

  $posts = muzi_connection("select * from posts where id = '{$id}' ")[0];
  

  function edit_posts(){
    global $id;
    global $posts;

    //判断别名是否输入
    $slug = isset($_POST['slug']) ?  $_POST['slug'] : $posts['slug'];
    $posts['slug'] = $slug;

    //判断标题是否输入
    $title = isset($_POST['title']) ? $_POST['title'] : $posts['title'];
    $posts['title'] = $title;
    
    //判断内容是否存在
    $content = isset($_POST['content']) ? $_POST['content'] : $posts['content'];
    $posts['content'] = $content;

    //判断是否选择分类
    $category = isset($_POST['category']) ? $_POST['category'] : $posts['category'];
    $posts['category'] = $category;

    //判断是否选择状态
    $status = isset($_POST['status']) ? $_POST['status'] : $posts['status'];
    $posts['status'] = $status;

    //处理上传的文件
    if (empty($_FILES['feature']['error'])) {

      $temp_file = $_FILES['feature']['tmp_name'];

      $target_file = '../../static/uploads/' . $_FILES['feature']['name'];
      if (move_uploaded_file($temp_file, $target_file)) {
        $image_file = '/static/uploads/' . $_FILES['feature']['name'];
      }
    }
    //接收传入的值
    $slug = $_POST['slug'];
    $title = $_POST['title'];
    $feature = isset($image_file) ? $image_file : '';
    $created = $_POST['created'];
    $content = $_POST['content'];
    $status = $_POST['status'];
    $category_id = $_POST['category'];

    //执行sql语句 更新用户提交的新数据
    $rows = muzi_execute("update posts set slug='{$slug}',feature='{$feature}',created='{$created}',
                                            content='{$content}',status='{$status}',category_id='{$category_id}'
                                    where id='{$id}';");


    $GLOBALS['success'] = $rows > 0;
    $GLOBALS['message'] = $rows <=0 ? '修改失败!' : '修改成功!';
  }

  
  //判断类型
  if($_SERVER['REQUEST_METHOD'] == 'POST'){
    edit_posts();
  }


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
  <link rel="stylesheet" href="/static/assets/vendors/simplemde/simplemde.min.css">
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
        <h1>编辑文章</h1>
      </div>
     <!-- 产生错误时候的操作 -->
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
      
      <form class="row" action="<?php echo $_SERVER['PHP_SELF'];?>?id=<?php echo $posts['id']; ?>" method="post" enctype="multipart/form-data">
        <div class="col-md-9"> 
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" placeholder="文章标题" value="<?php echo  $posts['title']; ?>">
          </div>
          <div class="form-group">
            <label for="content">内容</label>
            <!-- <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" placeholder="内容"></textarea> -->
            
            <textarea id="editor_id" name="content" class="form-control input-lg" name="content" placeholder="内容"><?php echo $posts['content'];?></textarea>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $posts['slug'];  ?>">
            <p class="help-block"><strong></strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" style="display:none">
            <input id="feature" class="form-control" name="feature" type="file" accept="image/*">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <?php foreach ($categories as $item): ?>
                <option value="<?php echo $item['id'] ?>" <?php echo  $posts['category_id'] == $item['id'] ? ' selected' : ''; ?>><?php echo $item['name'] ?></option>
              <?php endforeach ?>
              
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" readonly value="">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted"<?php echo $posts['status'] == 'drafted' ? ' selected' : ''; ?>>草稿</option>
              <option value="published"<?php echo $posts['status'] == 'published' ? ' selected' : ''; ?>>已发布</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">更新</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  
 
  <?php include '../inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/simplemde/simplemde.min.js"></script>
  <script charset="utf-8" src="/static/assets/vendors/kindeditor/kindeditor-all.js"></script>
  <script charset="utf-8" src="/static/assets/vendors/kindeditor/lang/zh-CN.js"></script>
  <script src="/static/assets/vendors/moment/moment.js"></script>
  <script>
     $(function () {
      // 当文件域文件选择发生改变过后，本地预览选择的图片
      $('#feature').on('change', function () {
        var file = $(this).prop('files')[0]
        // 为这个文件对象创建一个 Object URL
        var url = URL.createObjectURL(file)

        // 将图片元素显示到界面上（预览）
        $(this).siblings('.thumbnail').attr('src', url).fadeIn()
      })


      // Markdown 编辑器
      // new SimpleMDE({
      //   element: $("#content")[0],
      //   autoDownloadFontAwesome: false
      // })

      KindEditor.ready(function(K) {
          K.create('#editor_id',{
                  width: '100%',
                  height: '420px'
                });
        });
        
      // 发布时间初始值
      setInterval(function(){
        $('#created').val(moment().format('YYYY-MM-DD HH:mm:ss'))
      },100);
    })
   
  </script>
  <script>NProgress.done()</script>
</body>
</html>