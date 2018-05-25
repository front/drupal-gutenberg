<?php

namespace Drupal\gutenberg;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\editor\Entity\Editor;

/**
 * Defines an interface for (loading of) Gutenberg plugins.
 */
interface GutenbergPluginInterface extends PluginInspectionInterface {

  /**
   * Returns a list of libraries this plugin requires.
   *
   * These libraries will be attached to the text_format element on which the
   * editor is being loaded.
   *
   * @param \Drupal\editor\Entity\Editor $editor
   *   A configured text editor object.
   * @return array
   *   An array of libraries suitable for usage in a render API #attached
   *   property.
   */
  public function getLibraries(Editor $editor);

  /**
   * Returns the Drupal root-relative file path to the plugin JavaScript file.
   *
   * Note: this does not use a Drupal library because this uses CKEditor's API,
   * see http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.resourceManager.html#addExternal.
   *
   * @return string|false
   *   The Drupal root-relative path to the file, FALSE if an internal plugin.
   */
  public function getFile();

  /**
   * Returns the additions to CKEDITOR.config for a specific CKEditor instance.
   *
   * The editor's settings can be retrieved via $editor->getSettings(), but be
   * aware that it may not yet contain plugin-specific settings, because the
   * user may not yet have configured the form.
   * If there are plugin-specific settings (verify with isset()), they can be
   * found at
   * @code
   * $settings = $editor->getSettings();
   * $plugin_specific_settings = $settings['plugins'][$plugin_id];
   * @endcode
   *
   * @param \Drupal\editor\Entity\Editor $editor
   *   A configured text editor object.
   * @return array
   *   A keyed array, whose keys will end up as keys under CKEDITOR.config.
   */
  public function getConfig(Editor $editor);

}
