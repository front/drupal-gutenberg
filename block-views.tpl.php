<div id="block-<?php print $block->module .'-'. $block->delta; ?>" class="module-archives module">
  <?php if ($block->subject): ?><h2 class="module-header"><?php print $block->subject ?></h2><?php endif;?>
  <div class="module-content">
    <?php print $block->content ?>
  </div>
</div>