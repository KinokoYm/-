<?php foreach ($new_comments as $item): ?>
          <li>
            <a href="javascript:;" >
              <div class="avatar">
                <img src="<?php echo $item['avatar'] ?>" alt="">
              </div>
              <div class="txt">
                <p>
                  <span><?php echo $item['author'] ?></span><?php echo substr($item['created'], 0 , 10) ?>&nbsp;说:
                </p>
                <p style="text-overflow:ellipsis;white-space: nowrap; overflow: hidden;"><?php echo $item['content'] ?></p>
              </div>
            </a>
          </li>
        <?php endforeach ?>