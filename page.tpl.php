<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id="sixapart-standard">
<head>
  <title><?php print $head_title ?></title>
  <?php print $head ?>
  <?php print $styles ?>
  <?php print $scripts ?>
</head>
<body<?php print theme('body_class', $sidebar_left, $sidebar_right); ?>>
   <div id="container">
      <div id="container-inner" class="pkg">
        <div id="banner">
          <div id="banner-inner" class="pkg">
            <h1 id="banner-header"><a href="<?php print check_url($base_path);?>" accesskey="1"><?php print check_plain($site_name); ?></a></h1>
            <h2 id="banner-description"><?php print check_plain($site_slogan); ?></h2>
          </div>
        </div>
        <div id="pagebody">
           <div id="pagebody-inner" class="pkg">
<?php $div_id = gutenberg_get_next_div_id(); ?>
<?php if ($sidebar_left): ?>
              <div id="<?php print $div_id;?>">
                <div id="<?php print $div_id;?>-inner" class="pkg">
<?php if ($search_box): ?>
                  <div class="module-search module">
                    <h2 class="module-header">Search</h2>
                    <div class="module-content">
                      <?php print $search_box ?>
                    </div>
                  </div>
<?php unset($search_box); ?>
<?php endif; ?>
<?php print $sidebar_left ?>
                </div>
              </div>
<?php endif; ?>

<?php $div_id = gutenberg_get_next_div_id(); ?>
              <div id="<?php print $div_id;?>">
                <div id="<?php print $div_id;?>-inner" class="pkg">
                  <?php if ($title): print '<h2 class="entry-header">'. $title .'</h2>'; endif; ?>
                  <?php if ($help): print $help; endif; ?>
                  <?php if ($messages): print $messages; endif; ?>

                  <?php if ($tabs): print '<div id="tabs-wrapper" class="clear-block">'; endif; ?>
                  <?php if ($tabs): print $tabs .'</div>'; endif; ?>
                  <?php if (isset($tabs2)): print $tabs2; endif; ?>
                  <?php print $content;?>
                </div>
              </div>

<?php $div_id = gutenberg_get_next_div_id(); ?>
<?php if ($sidebar_right): ?>
              <div id="<?php print $div_id;?>">
                <div id="<?php print $div_id;?>-inner" class="pkg">
<?php if ($search_box): ?>
                  <div class="module-search module">
                    <h2 class="module-header">Search</h2>
                    <div class="module-content">
                      <?php print $search_box ?>
                    </div>
                  </div>
<?php endif; ?>
<?php print $sidebar_right ?>
                </div>
              </div>
<?php endif; ?>
           </div>
        </div>
     </div>
  </div>
</body>
</html>