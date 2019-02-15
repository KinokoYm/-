<?php 
    require_once dirname(__FILE__).'/functions.php';





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
    //获取轮播图
    $slides = muzi_connection("select * from slides");
    //获取热门推荐
    $hot_posts = muzi_connection("
      select id,feature,title
      from posts
      order by views desc ,likes desc
      limit 4 
      ");
 
    //退出登陆状态，删除session的数据
    if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout'){
    unset($_SESSION['current_logon_user']);
    }

        //将时间格式转化
    function muzi_convert_data($created){
      $time = strtotime($created);
      return date('Y年m月d日',$time);
    }
    //=====================================================
 
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
  ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $option[2]['value'] ?></title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap-btn.css">
  <link rel="stylesheet" href="/static/assets/css/style.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="icon" href="<?php echo $option[1]['value'] ?>" type="image/x-icon" />
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <style>
  	/*.aside .widgets .body {
    	clear: both;
	}*/
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

      <!-- 最新评论 -->
      <div class="widgets">
        <h4>最新评论</h4>
        <ul class="body discuz">
        <!-- 加载评论模块 -->
          <?php include './admin/inc/new_comments.php' ?>      
          
        </ul>
      </div>
    </div>
    <div class="content">
      <!-- 轮播图区域 -->
      <div class="swipe" style="height: 338px;width: 910px">
        <ul class="swipe-wrapper" >
          <?php foreach ($slides as $item): ?>
            <li>
              <a href="#">
                <img src="<?php echo $item['avatar'] ?>">
                <span><?php echo $item['text'] ?></span>
              </a>
            </li>

          <?php endforeach ?>
          
   
      
        </ul>
        <p class="cursor"><span class="active"></span><span></span><span></span><span></span></p>
        <a href="javascript:;" class="arrow prev"><i class="fa fa-chevron-left"></i></a>
        <a href="javascript:;" class="arrow next"><i class="fa fa-chevron-right"></i></a>
      </div>
      	
      <div class="panel hots">
        <h3>热门推荐</h3>
        <ul>
          <?php foreach ($hot_posts as $item): ?>
            <li>
            <a href="./detail.php?id=<?php echo $item['id'] ?>">
              <img src="<?php echo empty($item['feature']) ? 'http://dummyimage.com/208x132/79a2f2&text=KinokoYm' : $item['feature']?>" alt="">
              <span><?php echo $item['title'] ?></span>
            </a>
          </li>
          <?php endforeach ?>
        </ul>
      </div>

      <!-- 最新发布 -->
      <div class="panel new">
        <h3>最新发布</h3>


        <?php include './admin/inc/new_posts.php' ?>
        

    
      </div>






    </div>
    <div class="footer">
      <p><?php echo $option[5]['value'] ?></p>
    </div>
  </div>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/swipe/swipe.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.min.js"></script>
  <script>
    $(function(){
      //
    var swiper = Swipe(document.querySelector('.swipe'), {
      auto: 3000,
      transitionEnd: function (index) {
        // index++;

        $('.cursor span').eq(index).addClass('active').siblings('.active').removeClass('active');
      }
    });

    // 上/下一张
    $('.swipe .arrow').on('click', function () {
      var _this = $(this);

      if(_this.is('.prev')) {
        swiper.prev();
      } else if(_this.is('.next')) {
        swiper.next();
      }
    })


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
        console.log(json)
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



    })
    
  </script>
</body>
</html>
