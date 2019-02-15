<?php 
  require_once dirname(__FILE__).'/../functions.php';

  muzi_get_current_user();

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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right" id="page">
        
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th width="700px">评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody id="tbody">
          <!-- <tr class="danger">
            <td class="text-center"><input type="checkbox"></td>
            <td>大大</td>
            <td>楼主好人，顶一个</td>
            <td>《Hello world》</td>
            <td>2016/10/07</td>
            <td>未批准</td>
            <td class="text-center">
              <a href="post-add.html" class="btn btn-info btn-xs">批准</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr> -->
         
        </tbody>
      </table>
    </div>
  </div>
  
  <?php $current_page = 'comment' ?>
  <?php include 'inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script id="comments_tepl" type="text/x-jsrender">
    {{for comments}}
      <tr {{if status == 'held'}} class="warning"  {{else status == 'rejected'}} class="danger" {{/if}} data-id="{{:id}}">
        <td class="text-center"><input type="checkbox"></td>
          <td>{{:author}}</td>
          <td >{{:content}}</td>
          <td>{{:post_title}}</td>
          <td>{{:created}}</td>
          <td>{{: status === 'held' ? '待审' : status === 'rejected' ? '拒绝' : '准许'}}</td>
          <td class="text-center">
            {{if status == 'held'}}
            <a class="btn btn-info btn-xs btn-edit" href="javascript:;" data-status="approved">批准</a>
            <a class="btn btn-warning btn-xs btn-edit" href="javascript:;" data-status="rejected">拒绝</a>
            {{/if}}
            <a href="javascript:;" class="btn btn-danger btn-xs btn-delete" >删除</a>
          </td>
      </tr>
    {{/for}}
  </script>
  <script>  
    $(document)
    .ajaxStart(function () {
      NProgress.start()
    })
    .ajaxStop(function () {
      NProgress.done()
    })

    //==========================发送AJAX请求================================
    //获取页面数据，渲染按钮
    var current_page = 1;
    var checkedItems = []
    function LoadPageData(page){
      $.getJSON( '/admin/avatar/comments.php', { page: page} ,function(res){
      //将获取到的数据通过模板引擎渲染
        if (page > res.total_page ){
          LoadPageData(res.total_page)
          return 
        }
        $('#page').twbsPagination('destroy');
        $('#page').twbsPagination({
            first: '&laquo;',
            last: '&raquo;',
            prev: '上一页',
            next: '下一页',
            startPage: page,  
            totalPages: res.total_page,
            visiablePages: 5,
            initiateStartPageClick: false,
            onPageClick: function(e,page){
              LoadPageData(page)
            }
          })

        var html = $('#comments_tepl').render({comments:res.comments});
        $('#tbody').html(html)
        current_page = page

      })
    }
    
    LoadPageData(current_page)
    var $btnBatch = $('.btn-batch')


    //删除功能
    $('#tbody').on('click','.btn-delete',function(){
      var $tr = $(this).parent().parent()
      var id = $tr.data('id')

      $.get('/admin/avatar/comments-delete.php',{id : id },function(res){
        if(!res) return;

        // $tr.remove()
        LoadPageData(current_page);

      })
    })

    // 修改评论状态
    $('#tbody').on('click','.btn-edit',function(){
      var id = parseInt($(this).parent().parent().data('id'))

      var status = $(this).data('status')
      $.post('/admin/avatar/comments-status.php?id=' + id, { status: status }, function (res) {
        if(!res) return;

        LoadPageData(current_page);
      })
    })
    //选中项的集合
    
    // 批量操作按钮
    $('#tbody').on('change', 'td > input[type=checkbox]', function () {
      console.log(this)
      var id = parseInt($(this).parent().parent().data('id'))
      if ($(this).prop('checked')) {
        checkedItems.push(id)
        var arr = [];
        //循环去除重复元素
        for(var i =0;i<checkedItems.length;i++){
          if(arr.indexOf(checkedItems[i]) == -1){
            arr.push(checkedItems[i])
          }
        }
        //将不重复的元素传给checkedItems
        checkedItems = arr;

        
      } else {
        checkedItems.splice(checkedItems.indexOf(id), 1)
      }
      checkedItems.length ? $btnBatch.fadeIn() : $btnBatch.fadeOut()
    })

    // 全选 / 全不选
    $('th > input[type=checkbox]').on('change', function () {
      var checked = $(this).prop('checked')
      $('td > input[type=checkbox]').prop('checked', checked).trigger('change')
    })

    // 批量操作
      $btnBatch
        // 批准
        .on('click', '.btn-info', function () {
          $.post('/admin/avatar/comments-status.php?id=' + checkedItems.join(','), { status: 'approved' }, function (res) {
            checkedItems = []
            checkedItems.length ? $btnBatch.fadeIn() : $btnBatch.fadeOut()
            LoadPageData(current_page);
          })
        })
        // 拒绝
        .on('click', '.btn-warning', function () {
          $.post('/admin/avatar/comments-status.php?id=' + checkedItems.join(','), { status: 'rejected' }, function (res) {
            checkedItems = []
            checkedItems.length ? $btnBatch.fadeIn() : $btnBatch.fadeOut()
            LoadPageData(current_page);
          })
        })
        // 删除
        $btnBatch.on('click', '.btn-danger', function () {
          $.get('/admin/avatar/comments-delete.php', { id: checkedItems.join(',') }, function (res) {
            checkedItems = []
            checkedItems.length ? $btnBatch.fadeIn() : $btnBatch.fadeOut()
            LoadPageData(current_page);

          })
        })
      
  </script>
  <script>NProgress.done()</script>
</body>
</html>
