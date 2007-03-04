<?php
// $Id$

/**
 * Override or insert PHPTemplate variables into the templates.
 * Mostly, we use this to change the default Submited By and title text.
 */
function _phptemplate_variables($hook, $vars) {
  if ($hook == 'node') {
    $node = $vars['node'];
    if (theme_get_setting('toggle_node_info_' . $node->type)) {
      $vars['submitted'] = t('Posted by !a on @b.', array('!a' => theme('username', $node), '@b' => format_date($node->created)));
    }
    return $vars;
  }
  elseif ($hook == 'comment') {
    $comment = $vars['comment'];
    $vars['title'] = check_plain($comment->subject);
    $vars['submitted'] = t('Posted by ') . theme('username', $comment) .' | '. l(format_date($comment->timestamp), $_GET['q'], NULL, NULL, "comment-$comment->cid");
    return $vars;
  }
  return array();
}

/**
 * Movable Type uses a custom body class to determine what the current layout
 * is. This function spits out an appropriate class string based on the presence
 * of right or left sidebars.
 */
function gutenberg_body_class($sidebar_left, $sidebar_right) {
  if ($sidebar_left != '' && $sidebar_right != '') {
    $class = 'layout-three-column';
  }
  else {
    if ($sidebar_left != '') {
      $class = 'layout-two-column-left';
    }
    if ($sidebar_right != '') {
      $class = 'layout-two-column-right';
    }
  }

  if (empty($class)) {
    $class = 'layout-one-column';
  }
  return ' class="'. $class .'"';
}

/**
 * Movable Type outputs its main content areas, and gives them classes corresponding
 * to their order in the HTML rather than the nature of their content. As such,
 * we need to output the div names based on which ones have previously appeared.
 */
function gutenberg_get_next_div_id() {
  static $step;
  $steps = array(1 => 'alpha', 2 => 'beta', 3 => 'gamma');

  if (!isset($step)) {
    $step = 1;
  }

  return $steps[$step++];
}

/**
 * Movable Type does some odd things regarding page titles. Where Drupal sets a
 * global page title and treats it the same across all pages, Movable Type treats
 * different bits of content as pseudo-titles on different pages. To make this
 * work, we let node.tpl.php handle the display of the node's title on node pages,
 * and theme the title as a content-nav element on all others.
 */
function gutenberg_page_title($title) {
  if (arg(0) == 'node' && is_numeric(arg(1))) {
    return '';
  }
  else {
    return '<p class="content-nav">'. $title .'</p>';
  }
}

/**
 * Movable Type's default templates start and end both the right and left
 * sidebars with identical search blocks, feed-links, and powered-by messages.
 * In Drupal-land, we would style the closure of the region. To make these
 * funky styles happy, we'll special-case these particular chunks of content.
 * If you want to change them or alter how they work, feel free to modify the
 * sidebar-open.tpl.php and sidebar-close.tpl.php files.
 */
function gutenberg_sidebar_open($column, $search_box = '') {
  static $output;
  $output = _phptemplate_callback('sidebar-open', array('column' => $column, 'first' => !isset($output), 'search_box' => $search_box), array('column_opening-'. $column));
  return $output;
}

function gutenberg_sidebar_close($column) {
  static $output;
  $output = _phptemplate_callback('sidebar-close', array('column' => $column, 'first' => !isset($output)), array('column_closure-'. $column));
  return $output;
}

/**
 * For some reason, Movable Type outputs both an H3 tag and a form label element
 * for its search form. We hard-code the label and stick it in place before
 * rendering the search form.
 */
function gutenberg_search_theme_form($form) {
  $form['search_theme_form_keys']['#size'] = 15;
  $form['search_theme_form_keys']['#id'] = 'search';
  $output .= '<div id="search" class="container-inline">';
  $output .= '<label for="edit-search-theme-form-keys">Search this blog: </label><br />';
  $output .= drupal_render($form['search_theme_form_keys']);
  $output .= drupal_render($form) .'</div>';
  return $output;
} 

/**
 * Movable Type automatically outputs a formatted divider between nodes posted on
 * different days. This function duplicates that feature: calling
 * theme('date_header', $node->created) in node.tpl.php will output just the right
 * markup. Movable Type also tends to only print its date headers on the front
 * page. So, for now, we also return empty if we're on any page other than <front>.
 */
function gutenberg_date_header($date, $format = 'F d, Y') {
  static $old;
  $new = date($format, $date);

  if ($old != $new && drupal_is_front_page()) {
    $old = $new;
    return '<h2 class="date-header">' . $new . '</h2>';
  }
}

/**
 * Movable Type spits out links as pipe-delimited strings rather than formatted
 * ul's. We've dumbed down Drupal's standard link rendering to make it match.
 */
function gutenberg_links($links, $attributes = array('class' => 'links')) {
  $links_list = array();
  if (count($links) > 0) {
    foreach ($links as $key => $link) {
      $class = '';

      // Automatically add a class to each link and also to each LI
      if (isset($link['attributes']) && isset($link['attributes']['class'])) {
        $link['attributes']['class'] .= ' ' . $key;
      }
      else {
        $link['attributes']['class'] = $key;
      }

      // Is the title HTML?
      $html = isset($link['html']) && $link['html'];

      // Initialize fragment and query variables.
      $link['query'] = isset($link['query']) ? $link['query'] : NULL;
      $link['fragment'] = isset($link['fragment']) ? $link['fragment'] : NULL;

      if (isset($link['href'])) {
        $link_list[] = l($link['title'], $link['href'], $link['attributes'], $link['query'], $link['fragment'], FALSE, $html);
      }
      else if ($link['title']) {
        //Some links are actually not links, but we wrap these in <span> for adding title and class attributes
        if (!$html) {
          $link['title'] = check_plain($link['title']);
        }
        $link_list[] = '<span'. drupal_attributes($link['attributes']) .'>'. $link['title'] .'</span>';
      }
    }
    return implode(' | ', $link_list);
  }
}


/**
 * Drupal normally includes all kinds of slick classes to determine whether a
 * list item is a leaf node or a branch, but no. We'll strip those out and
 * make them look like MT's defaults. We'll also do the same stuff to themed
 * item lists; many sidebar blocks use that function to output lists of links.
 */
function gutenberg_menu_tree($pid = 1) {
  if ($tree = menu_tree($pid)) {
    return "\n<ul class=\"module-list\">\n". $tree ."\n</ul>\n";
  }
}

function gutenberg_menu_item($mid, $children = '', $leaf = TRUE) {
  return '<li class="module-list-item">'. menu_item_link($mid) . $children ."</li>\n";
}

function gutenberg_item_list($items = array(), $title = NULL, $type = 'ul', $attributes = NULL) {
  if (isset($title)) {
    $output .= '<h3>'. $title .'</h3>';
  }

  if (isset($attributes['class'])) {
    $attributes['class'] .= ' module-list';
  }
  else {
    $attributes['class'] = 'module-list';
  }

  if (!empty($items)) {
    $output .= "<$type" . drupal_attributes($attributes) . '>';
    foreach ($items as $item) {
      $attributes = array();
      $children = array();
      if (is_array($item)) {
        foreach ($item as $key => $value) {
          if ($key == 'data') {
            $data = $value;
          }
          elseif ($key == 'children') {
            $children = $value;
          }
          else {
            $attributes[$key] = $value;
          }
        }
      }
      else {
        $data = $item;
      }
      if (count($children) > 0) {
        $data .= theme('item_list', $children, NULL, $type, $attributes); // Render nested list
      }
      $output .= '<li class="module-list-item">'. $data .'</li>';
    }
    $output .= "</$type>";
  }
  return $output;
}

/**
 * Wrap the display of comments in Movable Type's standard divs. We also attempt
 * to wrap the comment form properly when it's shown on a separate page -- a
 * scenerio MT doesn't provide for. To prevent double-wrapping, we use the helper
 * function _gutenberg_comments_wrapped and bail if we're already insie the wrapper.
 */
function gutenberg_comment_wrapper($content, $type = null) {
  if ($content) {
    if (_gutenberg_comments_wrapped()) {
      $output = $content;
    }
    else {
      $output .= '<div id="comments" class="comments">';
      $output .= '<div class="comments-content">';
      $output .= '<h3 class="comments-header">Comments</h3>';
      $output .= $content;
      $output .= '</div>';
      $output .= '</div>';
    }
  }
  return $output;
}

function gutenberg_comment_form($form) {
  $content = drupal_render($form);
  return theme('comment_wrapper', $content);
}

function _gutenberg_comments_wrapped() {
  static $flag;
  $wrapped = isset($flag);
  $flag = TRUE;
  return $wrapped;
}
/**
 * Movable Type uses a smallish text blurb instead of a feed icon. We'll mirror that,
 * so that CSS styles can do their usual thing with it.
 */
function gutenberg_feed_icon($url) {
  return '<a href="'. check_url($url) .'" class="feed-icon">'. t("Subscribe to this blog's feed") .'</a>';
}

/**
 * Override Views module's standard summary display, so categories aren't polluted
 * by summary counts that Movable Type doesn't normally generate. They mess up the
 * occasional funkily-coded MT style.
 */
function gutenberg_views_summary($view, $type, $level, $nodes, $args) {
  foreach ($nodes as $node) {
    $items[] = views_get_summary_link($view->argument[$level]['type'], $node, $view->real_url);
  }
  if ($items) {
    $output .= theme('item_list', $items);
  }

  return $output;
}

/**
 * If custom_pagers module is installed and active, this function will automatically
 * theme the pager to match Movable Type's default entry-archive browse links.
 */
function gutenberg_custom_pager($nav_array, $node, $pager) {
  if (is_numeric($nav_array['prev'])) {
    $prev = node_load($nav_array['prev']);
    $links[] = l(t('‹ ') . $prev->title, 'node/'. $prev->nid);
  }

  $links[] = l(t('Main'), '');

  if (is_numeric($nav_array['next'])) {
    $next = node_load($nav_array['next']);
    $links[] = l($next->title . t(' ›'), 'node/'. $next->nid);
  }

  $output .= '<p class="content-nav">'. implode(' | ', $links) .'</p>';

  return $output;
}