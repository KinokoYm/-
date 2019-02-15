<?php 
  //====================载入文件======================
  require_once dirname(__FILE__).'/../../functions.php';

  $current_user = muzi_get_current_user();
  //===================================================
  //====================接收筛选传参数================================
  //分类
    $where = '1=1';
    $search = '';
    if(isset($_GET['categories']) && $_GET['categories'] !== 'all'){
      $where .= ' and posts.category_id = '.$_GET['categories'];
      $search .= '&categories=' . $_GET['categories'];
    }


    if(isset($_GET['status']) && $_GET['status'] !== 'all'){
      $where .= " and posts.status = '{$_GET['status']}' " ;
      $search .= '&status=' . $_GET['status'];
    }
    //超级管理员可以看到所有人的文章,其他人不可以
    $where .= $current_user['slug'] !=='superadmin' ? " and posts.user_id = '{$current_user['id']}' " : " and (posts.status = 'published' or (posts.user_id = '{$current_user['id']}' and posts.status = 'drafted')) " ;

  //================================================================ 
   //=======================处理一些数据=========================
    
    //页号
    //每页的条数
    $size = 20;
    $page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
    //输入太小调整第一页
    if($page<1){
      $page = 1;
    }
    // $page = $page < 1 ? 1 : $page;

     //======================处理页码============================
      $total_count = (int)muzi_connection_one("
      select count(1) as num from posts
      inner join categories on posts.category_id = categories.id
      inner join users on posts.user_id = users.id
      where {$where};
      ")['num'];


    //计算总页数
      $total_page = (int)ceil($total_count / $size);

      //==========================================================
      //输入太大调整最后一页
      if($page > $total_page){
        $page = $total_page;
      }
    
      //每页越过几条
      $skip = ($page - 1) * $size; 

      //======================数据库的查询=============================

      $posts = muzi_connection("select
        posts.user_id,
        posts.id,
        posts.title,
        users.nickname as user_name,
        categories.name as category_name,
        posts.created,
        posts.status
        from posts
        inner join categories on posts.category_id = categories.id
        inner join users on posts.user_id = users.id
        where {$where}
        order by posts.created desc
        limit {$skip},{$size}
        ");


        // function muzi_get_category($category_id){
        //   return muzi_connection_one("select name from categories where id={$category_id}")['name'];
        // }

        // function zuzi_get_user($user_id){
        //   return muzi_connection_one("select nickname from users where id={$user_id}")['nickname'];
        // }
        // 
        $categories = muzi_connection('select * from categories');
        //===========获取状态数据=================================
        $status = muzi_connection('select distinct status from posts');
       //=============获取分类数据=========================

    // $posts = muzi_connection("select * from posts;");
    

  
        
 
        //显示5个，所以中间相距4个
        $begin = $page  - 2;
        $end = $begin + 4;


        if($begin<1){
          $begin = 1;
          $end = $begin +4;
        }


        if( $end > $total_page ){
          //end超出范围
          $end = $total_page ;
          $begin = $end - 4;
          
          if($begin<1){
            $begin = 1;

          }
        }


        //当前页码不为1时显示上一页
        if($page  > 1){
          $pagel = $page - 1 ;
        }
        //当前页码不为最大值时候显示下一页
        if($page < $total_page){
          $pager = $page + 1 ;
        }
        //当开始页码不等于1时显示省略号
        //当结束页码不等于最大时显示省略号
    
    
  
  
 

  //================处理数据==========================
    //处理状态格式的转换
    function muzi_convert_status($status){
      $data = array(
        'published' =>'已发布' ,
        'drafted' => '草稿',
        'trashed' => '回收站'
       );

      return isset($data[$status]) ? $data[$status] : '';
    }
    //将时间格式转化
    function muzi_convert_data($created){
      $time = strtotime($created);
      return date('Y年m月d日<b\r>H:i:s',$time);
    }
    //=====================================================
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
        <h1>所有文章</h1>
        <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/posts/posts-delete.php" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?> ">
          <select name="categories" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($categories as  $item): ?>
              <option value="<?php echo $item['id'] ?>" <?php echo isset($_GET['categories']) && $_GET['categories']==$item['id'] ? 'selected':''; ?>><?php echo $item['name'] ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <?php foreach ($status as  $item): ?>
              <option value="<?php echo $item['status'] ?>" <?php echo isset($_GET['status']) && $_GET['status']==$item['status'] ? 'selected':''?>><?php echo muzi_convert_status($item['status']) ?></option>
            <?php endforeach ?>
            
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
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
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($posts)): ?>
          <?php foreach ($posts as  $item): ?>
          <tr>
            <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
            <td><?php echo $item['title']; ?></td>
            <td><?php echo $item['user_name'] ?></td>
            <td><?php echo $item['category_name'] ?></td>
            <td class="text-center"><?php echo muzi_convert_data($item['created']) ?></td>
            <td class="text-center"><?php echo muzi_convert_status($item['status']) ?></td>
            <td class="text-center">
              <a href="/admin/posts/posts-updata.php?id=<?php echo $item['id'] ?>" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/posts/posts-delete.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach ?>
        <?php endif ?>
        </tbody>
      </table>
    </div>
  </div>
  
  <?php $current_page = 'posts' ?>
  <?php include '../inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
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


        // $tbodyCheckboxs.on('change',function(){
            
        //     var flag = false;
        //     $tbodyCheckboxs.each(function(i,item){
        //       if($(item).prop('checked')){
        //         flag = true;
        //       }
              
        //     })

        //     flag ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
        // })
      })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
