<?php 
    require_once dirname(__FILE__).'/functions.php';

    if(isset($_SESSION['current_logon_user'])){
      $current_user = $_SESSION['current_logon_user'];
    }

    //获取最新的3篇文章
    //换成载入最新文章的模块
    $new_issue = muzi_connection("
        select 
        posts.`id`,
        posts.`slug`,
        posts.`title`,
        posts.`feature`,
        posts.`created`,
        posts.`content`,
        posts.`views`,
        posts.`likes`,
        posts.`status`,
        users.`nickname`,
        categories.`name`,
        categories.`slug` as category
        from posts
        inner join users on posts.user_id = users.id
        inner join categories on posts.category_id = categories.id
        order by posts.id desc 
        limit 3
      ");

    //获取最新的评论
    $new_comments = muzi_connection("
          select 
          users.id as user_id,
          users.avatar,
          comments.author,
          comments.content,
          comments.created,
          comments.id as comment_id,
          posts.id
          from comments
          inner join posts on comments.post_id = posts.id
          inner join users on users.nickname= comments.author
          where comments.status = 'approved'
          order by comments.created desc
          limit 10
      ");

    $id = $_GET['id'];
    $detail_posts = muzi_connection("
      select 
      posts.`id`,
      posts.`slug`,
      posts.`title`,
      posts.`feature`,
      posts.`created`,
      posts.`content`,
      posts.`views`,
      posts.`likes`,
      posts.`status`,
      users.`nickname`,
      categories.`name`,
      categories.`slug` as category
      from posts
      inner join users on posts.user_id = users.id
      inner join categories on posts.category_id = categories.id
      where posts.id = '{$id}'
      ")[0];


        //获取提交的搜索内容


    if(isset($_GET['search'])){
      $search = '';
      $search .= '&search=' . $_GET['search'];
      $search_content = $_GET['search'] ;
    }
    
    

    
    //=======================处理一些数据=========================
    
    //页号
    //每页的条数
    $size = 10;
    $page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
    //输入太小调整第一页
    if($page<1){
      $page = 1;
    }
    // $page = $page < 1 ? 1 : $page;

     //======================处理页码============================
      
        $total_count = (int)muzi_connection_one("
        select count(1) as num 
        from comments 
        inner join posts on posts.id = comments.post_id
        where posts.id = '{$id}' and comments.status = 'approved'
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
        //获取分类的文章列表
        
        // muzi_connection("
        // select 
        // users.id as user_id,
        // users.avatar,
        // comments.author,
        // comments.content,
        // comments.created,
        // comments.id as comment_id,
        // posts.id
        // from comments
        // inner join posts on comments.post_id = posts.id
        // inner join users on users.nickname= comments.author
        // where posts.id = '{$posts_id}' and comments.status = 'approved'
        // order by comments.created desc
        // limit {$skip},{$size}
        // ");
        
      


        //显示5个，所以中间相距4个
        $begin = $page  - 3;
        $end = $begin + 6;


        if($begin<1){
          $begin = 1;
          $end = $begin +6;
        }


        if( $end > $total_page ){
          //end超出范围
          $end = $total_page ;
          $begin = $end - 6;
          
          if($begin<1){
            $begin = 1;

          }
        }






    
//*****************************数据处理**************************************





    //获取评论数量
    function countComents($id) {
      $comments = muzi_connection("
      select count(1) as num 
      from comments 
      inner join posts on posts.id = comments.post_id
      where posts.id = '{$id}' and comments.status = 'approved'
      ");

      return $comments;
    }
    //获取页面内容
    $option = muzi_connection('select * from options');
    // var_dump($option[1]['value'])

  ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $option[2]['value'] ?></title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap-btn.css">
  <link rel="stylesheet" href="/static/assets/css/style.css">
  <link rel="stylesheet" href="/static/assets/css/detail.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="icon" href="<?php echo $option[1]['value'] ?>" type="image/x-icon" />
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <style>
    .posts_content{
      margin: 0px 20px 20px;
    }
    .posts_content p {
      font-size: 16px;
      line-height: 30px;
      text-indent:2em;
      margin: 20px 0;
    }
    textarea.open {
      height: 20px;
      transition: all .8s ease;
    }
    .open:focus{
      height: 60px;

    } 
   

  </style>
</head>
<body>
  <div class="wrapper">
    
    <!-- 载入公共的侧边栏模块 -->
    <?php include './admin/inc/header.php' ?>

    <div class="aside">


      <div class="widgets">
        <h4>搜索</h4>
        <!-- 加载搜索模块 -->
        <?php include './admin/inc/search.php' ?>  
      </div>


      <div class="widgets">
        <h4>最新评论</h4>
        <ul class="body discuz">

          <!-- 加载评论模块 -->
          <?php include './admin/inc/new_comments.php' ?>   

        </ul>
      </div>
    </div>
    <div class="content">
      <div class="article">
        <div class="breadcrumb">
          <dl>
            <dt>当前位置：</dt>
            <dd><a href="./list.php?categories=<?php echo $detail_posts['category'] ?>"><?php echo $detail_posts['name'] ?></a></dd>
            <dd><?php echo $detail_posts['slug'] ?></dd>
          </dl>
        </div>
        <h2 class="title">
          <a href="javascript:;"><?php echo $detail_posts['title'] ?></a>
        </h2>
        <div class="posts_content">
          <p><?php echo $detail_posts['content'] ?></p>
          
        </div>
        <div class="meta">
          <span><?php echo $detail_posts['nickname'] ?> 发布于 <?php echo substr($detail_posts['created'],0,10) ?></span>
          <span>分类: <a href="./list.php?categories=<?php echo $detail_posts['category'] ?>"><?php echo $detail_posts['name'] ?></a></span>
          <span>阅读: (<?php echo $detail_posts['views'] ?>)</span>
          <span>评论: (<?php echo countComents($detail_posts['id'])[0]['num'] ?>)</span>
          <a href="javascript:;" class="like">
            <i class="fa fa-thumbs-up"></i>
            <span id="skr" data-id="<?php echo $detail_posts['id'] ?>" data-status="<?php echo $detail_posts['likes'] ?>">赞(<?php echo $detail_posts['likes'] ?>)</span>
          </a>
        </div>
      </div>
      <!--发布评论 -->
      <?php if ($option[6]['value']): ?>
        <div class="panel new" >
        <h3>发布评论</h3>
        <div >
          <?php if (!empty($current_user['id'])): ?>
          <div class="comments" id="show" data-flag="true">
            <div style="display: flex;">
              <div class="avatar" style="flex:5%;">
                <a href="javascript:void(0);" style="display: inline-block;">
                  <img src="<?php echo empty($current_user['avatar']) ? '/static/uploads/avatar.png' : $current_user['avatar']; ?>" style="height:30px;width:30px;border-radius: 50%;overflow: hidden;box-shadow: 0 0 1px 2px grey;">
                </a>    
              </div>
              <!-- 表单提交 -->
              <form  style="flex: 95%" id="form-post" data-id="<?php echo $detail_posts['id'] ?>" >
                  <textarea class="form-control open" name="comments" style="resize:none;width: 97%;" ></textarea>
                  <div style="text-align: right">
                    <input type="hidden"  name="created" id="created"></input>
                    <input  class="btn area" type="submit" value="发表评论" style="background-color: #ff5e52;margin-top: 10px;color: white;flex:1;"></input>
                  </div>
              </form>

            </div>
            <div class="comments-list">
              <!-- 评论行 -->
              <!-- <ul class="comments-list-ul">
                <li style="display: flex">
                  <a href="javascript:void(0);" style="flex: 1%">
                    <img src="" style="height:30px;width:30px;border-radius: 50%;overflow: hidden;box-shadow: 0 0 1px 2px grey;">
                  </a>
                  <span style="flex: 6%;color: black">木子羊毛说:</span>
                  <div style="flex:80%">
                    <div class="comments-content">
                      <span >究成点信由济价天及论低世八团水。和根也无价统音层</span>
                      <span>(19:20:22&nbsp;&nbsp;&nbsp;&nbsp; #<b>1</b>楼)</span>
                      <span >
                        <a href="">回复</a>
                      </span>
                    </div>
                  </div>
                </li>
              </ul> -->
            </div>
            <?php if ( !empty($total_count)): ?>

            <?php if ($total_count > 10): ?>
              <div style="height: 80px;margin-left: 250px">
                <ul class="pagination pagination-lg  liparent" >
                <li data-page="1"><a href="javascript:;"  id="page" data-id="<?php echo $detail_posts['id'] ?>" data-page="1">第一页</a></li>
             
                <?php for( $i = $begin; $i <= $end ; $i++) : ?>
                  <li <?php echo $i == '1' ? ' class=" active"' : '' ?> data-page="<?php echo $i ?>"><a href="javascript:;" id="page" data-id="<?php echo $detail_posts['id'] ?>" data-page="<?php echo $i ?>"><?php echo $i ?></a></li>
                <?php endfor ?>
                
                <li data-page="<?php echo $total_page ?>"><a href="javascript:;"  id="page" data-id="<?php echo $detail_posts['id'] ?>" data-page="<?php echo $total_page ?>">最后一页</a></li>
              </ul>
            </div>
            <?php endif ?>
            

            <?php endif ?>
          </div>
        <?php else: ?>  
            <div style="text-align: center" id="show" data-flag="flase">
              <p style="display: inline-block;">登陆即可发布评论--<a href="/admin/login.php?location=detail&id=<?php echo $_GET['id'] ?>" style="color: skyblue;text-decoration: underline;">点击登陆</a></p>
            </div>
        <?php endif ?>
        </div>
        
      
      </div>
      <?php endif ?>
    
      
      <div class="panel new">
        <h3>最新发布</h3>
        
        <!-- 载入最新发布模块 -->
        <?php include './admin/inc/new_posts.php' ?>  
        
      </div>
    </div>
      
    <div class="footer">
      <p><?php echo $option[5]['value'] ?></p>
    </div>
  </div>
</body>
<script src="/static/assets/vendors/jquery/jquery.js"></script>
<script src="/static/assets/vendors/bootstrap/js/bootstrap.min.js"></script>
<script src="/static/assets/vendors/moment/moment.js"></script>
<script src="/static/assets/vendors/art-template/art-template.js"></script>
<script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
<!-- 模板引擎 -->
<script id="tpl-user" type="text/html">
  {{each comments}}
  <ul class="comments-list-ul">
    <li style="display: flex">
      <a href="javascript:void(0);" style="flex: 1%">
        <img src="{{ $value.avatar}}" style="height:30px;width:30px;border-radius: 50%;overflow: hidden;box-shadow: 0 0 1px 2px grey;">
      </a>
      <span style="flex: 6%;color: black">{{$value.author}}说:</span>
      <div style="flex:80%">
        <div class="comments-content">
          <span >{{ $value.content }}......</span>
          <span>({{ $value.created }}&nbsp;&nbsp;&nbsp;&nbsp; #<b>{{ $index + 1 }}</b>楼)</span>
          {{if comments[$index].user_id == <?php echo $current_user['id'] ?>}}
          <span  data-id="">
            <a id="reply" href="" data-id="{{$value.comment_id}}">删除</a>
          </span>     
          {{/if}}
        
        </div>
      </div>
    </li>
  </ul>
  {{/each}}
</script>
<!-- 分页模板引擎 -->
<script id="tpl-page" type="text/html">
  {{each comments['detail_posts']}}
  <ul class="comments-list-ul">
    <li style="display: flex">
      <a href="javascript:void(0);" style="flex: 1%">
        <img src="{{ $value.avatar}}" style="height:30px;width:30px;border-radius: 50%;overflow: hidden;box-shadow: 0 0 1px 2px grey;">
      </a>
      <span style="flex: 6%;color: black">{{$value.author}}说:</span>
      <div style="flex:80%">
        <div class="comments-content">
          <span >{{ $value.content }}......</span>

          <span>({{ $value.created }}&nbsp;&nbsp;&nbsp;&nbsp; #<b>{{comments['current_page'] + $index + 1 }}</b>楼)</span>

          {{if comments['detail_posts'][$index].user_id == <?php echo $current_user['id'] ?>}}
          <span  data-id="">
            <a id="reply" href="" data-id="{{$value.comment_id}}">删除</a>
          </span>     
          {{/if}}
        
        </div>
      </div>
    </li>
  </ul>
  {{/each}}
</script>



<script>
    $(function(){
      //添加点赞功能
      $('a').on('click','#skr',function(){
        //获取当前的文本
        var text = $(this).text()
        //获取id
        var id = $(this).data('id')
        //获取当前的状态
        var statusSkr = $(this).data('status')
        //记录当前的this
        var $that = $(this)
        //将点赞数目加1
        var skr = parseInt(text.replace(/[^0-9]/gi,"")) +1;
        //只能点赞一次
        if(skr - statusSkr == 1 ){
          
          $.post('./admin/increase/skr.php?id=' + id,{ skr: skr} ,function(res){
          var json = JSON.parse(res)
          $that.text("赞("+ json.likes +")")  
          })
        }
      })

    //添加阅读功能
      $('.head').on('click','#viewsclick',function(e){
        //阻止a链接的默认事件 
        e.preventDefault();
        //获取文章的id
        var id = $(this).data('id') 

        var text = $(this).parent().siblings('div').children('.extra').children('span:first-child').text()
        console.log(text)
        var viewsNum = parseInt(text.replace(/[^0-9]/gi,"")) +1;
        console.log(viewsNum)
        var $that = $(this).parent().siblings('div').children('.extra').children('span:first-child')

        $.post('./admin/increase/views.php?id=' + id,{ viewsNum: viewsNum} ,function(res){
          var json = JSON.parse(res)
          $that.text("阅读("+ json.views +")")  
          })

        window.location.href = './detail.php?id=' + id
        // window.event.returnValue=false;
      })

    //获取焦点
      // $('.open').on('focus',function(){
      //   $('.area').fadeIn(800).stop(false,true)
      // })
      // $('.open').on('blur',function(){
      //   $('.area').fadeOut(800).stop(false,true)
      // })
      //当前时间
     setInterval(function(){
        $('#created').val(moment().format('YYYY-MM-DD HH:mm:ss'))
      },100);

    $('#form-post').submit(function(e){
      e.preventDefault();
      //获取文章id
      var id = $(this).data('id')
      //获取评论内容
      var comments = $(this).children('textarea').val()

      //获取当前的时间
      var created = $('#created').val()

      //记录当前this
      var $that = $(this)
      console.log(comments)
      console.log(created)
      $.post('./admin/inc/before_comments.php?id=' + id,{comments:comments ,created:created },function(res){

        // alert(`res="${res}"`);
        
        if(res == '0') { return alert("请输入评论内容！")}
        //清空

        $that.children('textarea').val('')

        
        if(res !== '1') { return false }



        //渲染页面数据
        $.post('./admin/inc/html_comment.php',{id:id},function(res){
          var comments = JSON.parse(res)
          for(key in comments){
            comments[key].content = comments[key].content.substr(0,24)
            comments[key].created = comments[key].created.substr(0,10)
          }
          
          var html = template("tpl-user",{comments: comments});
          $('.comments-list').html(html)
        })


      })
    })
    //刚进去页面获取评论数据
    //先进行判断，如果没有登陆，则不进行渲染
    if($('#show').data('flag')==true){

      var id = $('#form-post').data('id')
      $.post('./admin/inc/html_comment.php',{id:id},function(res){
      var comments = JSON.parse(res)
      for(key in comments){
        comments[key].content = comments[key].content.substr(0,24)
        comments[key].created = comments[key].created.substr(0,10)
      }
      var html = template("tpl-user",{comments: comments});
        $('.comments-list').html(html)
      })


    }
    
    //判断是否显示删除按钮
    

    //删除评论
    $('.comments-list').on('click','#reply',function(e){
      e.preventDefault()
      var comment_id = $('#reply').data('id')
      $.post('/admin/inc/comment_before_delete.php',{ id:comment_id },function(res){
        if(!res) {return}

        $.post('./admin/inc/html_comment.php',{id:id},function(res){
          var comments = JSON.parse(res)
          for(key in comments){
            comments[key].content = comments[key].content.substr(0,24)
            comments[key].created = comments[key].created.substr(0,10)
          }
          
          var html = template("tpl-user",{comments: comments});
          $('.comments-list').html(html)
        })

      })
    })
    //删除评论结束
    //ajax实现分页的渲染
    $('ul').on('click','#page',function(e){
      e.preventDefault()

      //获取请求的
      var page = $(this).data('page')
      var id = $(this).data('id')
      var i = $(this).parent().data('page')
      if(page == i ){
        
        $('.liparent').children().each(function(item){
          $('.liparent').children().removeClass('active')
        })
        $(this).parent().addClass(' active')
      }
      

      $.get('/admin/page/page.php',{page:page,id:id},function(res){

        var comments = JSON.parse(res)
     

        for(key in comments['detail_posts']){
            comments['detail_posts'][key].content = comments['detail_posts'][key].content.substr(0,24)
            comments['detail_posts'][key].created = comments['detail_posts'][key].created.substr(0,10)
          }
        comments['current_page'] = (comments['current_page']-1)*10

        console.log(comments)
        var html = template("tpl-page",{comments: comments });
        $('.comments-list').html(html)

      })

    })


    })
  </script>
</html>
