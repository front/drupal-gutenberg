<?php 

namespace Drupal\gutenberg\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @Filter(
 *   id = "filter_block",
 *   title = @Translation("Gutenberg Block filter"),
 *   description = @Translation("Embeds Drupal blocks."),
 *   settings = {
 *   },
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class BlockFilter extends FilterBase {
  public function process($text, $langcode) {

    $lines = explode("\n", $text);

    $lines = preg_replace_callback('#^<!-- wp:drupalblock\/.*\s(.*)\s\/-->$#', array($this, 'renderBlock'), $lines);

    $text = implode("\n", $lines);

    return new FilterProcessResult($text);
  }


  /**
   * Callback function to process each URL
   */
  private function renderBlock($match) {
    $comment = $match[0];
    $attributes = json_decode($match[1]);
    $plugin_id = $attributes->blockId;
    $block_manager = \Drupal::service('plugin.manager.block');
    // You can hard code configuration or you load from settings.
    $config = [];
    $plugin_block = $block_manager->createInstance($plugin_id, $config);
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

  /**
   * Define settings for text filter.
   */
  // public function settingsForm(array $form, FormStateInterface $form_state) {
  //   $form['oembed_providers'] = array(
  //     '#type' => 'textarea',
  //     '#title' => $this->t('Providers'),
  //     '#default_value' => $this->settings['oembed_providers'],
  //     '#description' => $this->t('A list of oEmbed providers. Add your own by adding a new line and using this pattern: [Url to match] | [oEmbed endpoint] | [Use regex (true or false)]'),
  //   );
  //   $form['oembed_maxwidth'] = array(
  //     '#type' => 'textfield',
  //     '#title' => $this->t('Maximum width of media embed'),
  //     '#default_value' => $this->settings['oembed_maxwidth'],
  //     '#description' => $this->t('Set the maximum width of an embedded media. The unit is in pixels, but only put a number in the textbox.'),
  //   );
  //   return $form;
  // }

}

