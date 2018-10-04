<?php 

namespace Drupal\gutenberg\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @Filter(
 *   id = "filter_reusable_block",
 *   title = @Translation("Gutenberg Reusable Block filter"),
 *   description = @Translation("Renders Gutenberg reusable blocks."),
 *   settings = {
 *   },
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class ReusableBlockFilter extends FilterBase {
  public function process($text, $langcode) {
    $lines = explode("\n", $text);

    $lines = preg_replace_callback('#^<!-- wp:block.*\s(.*)\s\/-->$#', array($this, 'renderBlock'), $lines);

    $text = implode("\n", $lines);

    return new FilterProcessResult($text);
  }

  /**
   * Callback function to process each URL
   */
  private function renderBlock($match) {
    $comment = $match[0];
    $attributes = json_decode($match[1]);
    $plugin_id = $attributes->ref;
    $block_manager = \Drupal::service('plugin.manager.block');
    // You can hard code configuration or you load from settings.
    $config = [];
    $plugin_block = $block_manager->createInstance($plugin_id, $config);
    dsm($plugin_block);
    // Some blocks might implement access check.
    $access_result = $plugin_block->access(\Drupal::currentUser());
    // Return empty render array if user doesn't have access.
    // $access_result can be boolean or an AccessResult class
    if (is_object($access_result) && $access_result->isForbidden() || is_bool($access_result) && !$access_result) {
      // You might need to add some cache tags/contexts.
      return '<h2>Access required.</h2>';
    }
    $render = $plugin_block->build();
    // $render['#printed'] = TRUE;
    $content = \Drupal::service('renderer')->render($render);

    return $comment . $content;
  }
}

