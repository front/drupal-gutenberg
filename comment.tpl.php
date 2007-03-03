<a id="c<?php print $comment->cid ?>"></a>
<div class="comment" id="comment-<?php print $comment->cid ?>">
<div class="comment-content">
<?php if ($picture) {?><div class="comment-userpic"><?php print $picture ?></div><?php }?>
<?php print $content ?>
<p class="comment-footer">
<?php print $submitted ?> | <?php print $links ?></p>
</div>
</div>
