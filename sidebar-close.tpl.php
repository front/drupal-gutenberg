<?php if ($first): ?><div class="module-syndicate module">
  <div class="module-content">
     <?php print theme('feed_icon', url('rss.xml')) ?><br/>
     [<a href="http://en.wikipedia.org/wiki/RSS_(file_format)">What is this?</a>]
  </div>
</div>
<div class="module-powered module">
  <div class="module-content">
    Powered by<br /><a href="http://www.drupal.org">Drupal <?php print VERSION ?></a>
  </div>
</div><?php endif; ?>