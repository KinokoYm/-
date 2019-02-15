<?php 
  require_once dirname(__FILE__).'/../../functions.php';

  muzi_get_current_user();


  function add_slides(){

    //判断图片是否上传
    if(empty($_FILES['avatar']['name'])){
      $GLOBALS['message'] = "请添加轮播图图片!";
      return;
    }
    //判断描述是否填写        
    if(empty($_POST['text'])){
      $GLOBALS['message'] = "请输入轮播图描述!";
      return;
    }
    //判断链接是否填写
    if(empty($_POST['slug'])){
      $GLOBALS['message'] = "请输入轮播图链接!";
      return;
    }
    //上传没有错误，将图片持久化保存
    if (empty($_FILES['avatar']['error'])) {

      $temp_file = $_FILES['avatar']['tmp_name'];

      $target_file = '../../static/uploads/swipe/slide' . $_FILES['avatar']['name'];
      if (move_uploaded_file($temp_file, $target_file)) {
        $image_file = '/static/uploads/swipe/slide' . $_FILES['avatar']['name'];
      }
    }
    //接收上传的数据
    $avatar = $image_file;
    $text = $_POST['text'];
    $slug = $_POST['slug'];

    $rows = muzi_execute(" INSERT INTO slides VALUES(null,'{$avatar}','{$text}','{$slug}')");
    
    $GLOBALS['success'] = $rows > 0;
    $GLOBALS['message'] = $rows <=0 ? '添加失败!' : '添加成功!';
  }

  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    add_slides();
  }

  $slides = muzi_connection("select * from slides");
  
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
        <h1>图片轮播</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (!empty($message)): ?>
        
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
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
            <h2>添加新轮播内容</h2>
            <div class="form-group">
              <label for="avatar">图片</label>
              <!-- show when avatar chose -->
              <img class="help-block thumbnail" style="display: none" id="img">
              <input id="avatar" class="form-control" name="avatar" type="file">
            </div>
            <div class="form-group">
              <label for="text">文本</label>
              <input id="text" class="form-control" name="text" type="text" placeholder="文本">
            </div>
            <div class="form-group">
              <label for="slug">链接</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="链接">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
            <input class="btn btn-danger btn-sm " type="text" style="visibility:hidden;width: 0">
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox" id="allselect"></th>
                <th class="text-center">图片</th>
                <th>文本</th>
                <th>链接</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($slides as $item): ?>
              <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
                <td class="text-center"><img class="slide" src="<?php echo $item['avatar'] ?>"></td>
                <td><?php echo $item['text'] ?></td>
                <td><?php echo $item['slug'] ?></td>
                <td class="text-center">
                  <a href="delete-slides.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
            <?php endforeach ?>
              

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
  <?php $current_page = 'slides' ?>
  <?php include '../inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script>
    $(function () {
      // 当文件域文件选择发生改变过后，本地预览选择的图片
      $('#avatar').on('change', function () {
        var file = $(this).prop('files')[0]
        // 为这个文件对象创建一个 Object URL
        var url = URL.createObjectURL(file)

        // 将图片元素显示到界面上（预览）
        $(this).siblings('.thumbnail').attr('src', url).fadeIn()
      })





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
