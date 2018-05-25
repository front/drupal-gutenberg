<?php

namespace Drupal\gutenberg;

use Drupal\Core\Plugin\PluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines a base Gutenberg plugin implementation.
 */
abstract class GutenbergPluginBase extends PluginBase implements GutenbergPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [];
  }

}
