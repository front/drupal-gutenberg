<?php

namespace Drupal\gutenberg;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\editor\Entity\Editor;

/**
 * Provides a Gutenberg Plugin plugin manager.
 */
class GutenbergPluginManager extends DefaultPluginManager {
  /**
   * Constructs a GutenbergPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/GutenbergPlugin', $namespaces, $module_handler, 'Drupal\gutenberg\GutenbergPluginInterface', 'Drupal\gutenberg\Annotation\GutenbergPlugin');
    $this->alterInfo('gutenberg_plugin_info');
    $this->setCacheBackend($cache_backend, 'gutenberg_plugins');
  }

  /**
   * Injects the Gutenberg plugins settings forms as a vertical tabs subform.
   *
   * @param array &$form
   *   A reference to an associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param \Drupal\editor\Entity\Editor $editor
   *   A configured text editor object.
   */
  public function injectPluginSettingsForm(array &$form, FormStateInterface $form_state, Editor $editor) {
    $definitions = $this->getDefinitions();

    foreach (array_keys($definitions) as $plugin_id) {
      $plugin = $this->createInstance($plugin_id);
      if ($plugin instanceof GutenbergPluginConfigurableInterface) {
        $plugin_settings_form = [];
        $form['plugins'][$plugin_id] = [
          '#type' => 'details',
          '#title' => $definitions[$plugin_id]['label'],
          '#open' => TRUE,
          '#group' => 'editor][settings][plugin_settings',
          '#attributes' => [
            'data-gutenberg-plugin-id' => $plugin_id,
          ],
        ];
        // Provide enough metadata for the drupal.ckeditor.admin library to
        // allow it to automatically show/hide the vertical tab containing the
        // settings for this plugin. Only do this if it's a CKEditor plugin that
        // just provides buttons, don't do this if it's a contextually enabled
        // CKEditor plugin. After all, in the latter case, we can't know when
        // its settings should be shown!
        // if ($plugin instanceof CKEditorPluginButtonsInterface && !$plugin instanceof CKEditorPluginContextualInterface) {
        //   $form['plugins'][$plugin_id]['#attributes']['data-ckeditor-buttons'] = implode(' ', array_keys($plugin->getButtons()));
        // }
        $form['plugins'][$plugin_id] += $plugin->settingsForm($plugin_settings_form, $form_state, $editor);
      }
    }
  }


}
