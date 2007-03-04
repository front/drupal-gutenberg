<?php if (!$page) print theme('date_header', $node->created); ?>
<a id="a<?php print $node->nid;?>"></a>
<div class="entry" id="entry-<?php print $node->nid;?>">

<h3 class="entry-header"><?php print $title ?></h3>
<div class="entry-content">
<?php if ($picture): ?><div class="entry-userpic"><?php print $picture ?></div><?php endif; ?>
      <div class="entry-body"><?php print $content ?>
         <p class="entry-footer">
            <span class="post-footers"><?php if ($submitted): ?><?php print $submitted ?></span> <span class="separator"> | </span><?php endif; ?><a class="permalink" href="<?php print $node_url ?>">Permalink</a>
            <?php if ($links) print ' | '. $links;?>
         </p>
      </div>
   </div>
</div>