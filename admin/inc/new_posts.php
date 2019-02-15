<?php foreach ($new_issue as $item): ?>
  <div class="entry">
    <div class="head">
      <span class="sort"><?php echo $item['name'] ?></span>
      <a id="viewsclick" data-id="<?php echo $item['id'] ?>" href="javascriptl:;"><?php echo $item['title'] ?></a>
    </div>
    <div class="main" style="height: 182.4px;position: relative;">
      <p class="info"><?php echo $item['nickname'] ?>&nbsp;&nbsp;发表于&nbsp; <?php echo substr($item['created'], 0 , 10) ?></p>
      <p class="brief" style="text-indent:2em;"><?php echo mb_substr($item['content'],0,278,'utf-8'). '......'?></p>

      <p class="extra" style="position: absolute;bottom:0px;height:13.2px;">
        <span class="reading" id="viewscontent">阅读(<?php echo $item['views'] ?>)</span>
        <span class="comment">评论(<?php echo countComents($item['id'])[0]['num'] ?>)</span>
        <a href="javascript:;" class="like">
          <i class="fa fa-thumbs-up"></i>
          <span id="skr" data-id="<?php echo $item['id'] ?>" data-status="<?php echo $item['likes'] ?>">赞(<?php echo $item['likes'] ?>)</span>
        </a>
        <a href="./list.php?categories=<?php echo $item['category']?>" class="tags">
          分类：<span><?php echo $item['name'] ?></span>
        </a>
      </p>
      <a href="javascript:;" class="thumb">
        <img src="<?php echo empty($item['feature']) ? '/static/uploads/swipe/logo.png' : $item['feature'] ?>" alt="" style="width: 180px ;height: 180px">
      </a>
    </div>
  </div>
<?php endforeach ?>
        