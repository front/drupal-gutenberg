<?php

namespace Drupal\gutenberg\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a GutenbergPlugin annotation object.
 *
 * Plugin Namespace: Plugin\GutenbergPlugin
 *
 * For a working example, see \Drupal\gutenberg\Plugin\GutenbergPlugin\DrupalImage
 *
 * @see \Drupal\gutenberg\GutenbergPluginManager
 * @see hook_gutenberg_plugin_info_alter()
 * @see plugin_api
 *
 * @Annotation
 */
class GutenbergPlugin extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the Gutenberg plugin.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

}
