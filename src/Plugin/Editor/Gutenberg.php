<?php

namespace Drupal\gutenberg\Plugin\Editor;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\gutenberg\GutenbergPluginManager;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\RendererInterface;
use Drupal\editor\Plugin\EditorBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\editor\Entity\Editor;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a Gutenberg-based text editor for Drupal.
 *
 * @Editor(
 *   id = "gutenberg",
 *   label = @Translation("Gutenberg"),
 *   supports_content_filtering = TRUE,
 *   supports_inline_editing = TRUE,
 *   is_xss_safe = FALSE,
 *   supported_element_types = {
 *     "textarea"
 *   }
 * )
 */
class Gutenberg extends EditorBase implements ContainerFactoryPluginInterface {

  /**
   * The module handler to invoke hooks on.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The Gutenberg plugin manager.
   *
   * @var \Drupal\gutenberg\GutenbergPluginManager
   */
  protected $gutenbergPluginManager;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a Drupal\Component\Plugin\PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\ckeditor\GutenbergPluginManager $gutenberg_plugin_manager
   *   The Gutenberg plugin manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke hooks on.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  // public function __construct(array $configuration, $plugin_id, $plugin_definition, ModuleHandlerInterface $module_handler, LanguageManagerInterface $language_manager, RendererInterface $renderer) {
  public function __construct(array $configuration, $plugin_id, $plugin_definition, GutenbergPluginManager $gutenberg_plugin_manager, ModuleHandlerInterface $module_handler, LanguageManagerInterface $language_manager, RendererInterface $renderer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->gutenbergPluginManager = $gutenberg_plugin_manager;
    $this->moduleHandler = $module_handler;
    $this->languageManager = $language_manager;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.gutenberg.plugin'),
      $container->get('module_handler'),
      $container->get('language_manager'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultSettings() {
    return [
      'plugins' => ['language' => ['language_list' => 'un']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    $settings = $editor->getSettings();

    // Gutenberg plugin settings, if any.
    $form['plugin_settings'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Gutenberg plugin settings'),
      '#attributes' => [
        'id' => 'gutenberg-plugin-settings',
      ],
    ];
    $this->gutenbergPluginManager->injectPluginSettingsForm($form, $form_state, $editor);
    if (count(Element::children($form['plugins'])) === 0) {
      unset($form['plugins']);
      unset($form['plugin_settings']);
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsFormSubmit(array $form, FormStateInterface $form_state) {
    // // Modify the toolbar settings by reference. The values in
    // // $form_state->getValue(array('editor', 'settings')) will be saved directly
    // // by editor_form_filter_admin_format_submit().
    // $toolbar_settings = &$form_state->getValue(['editor', 'settings', 'toolbar']);

    // // The rows key is not built into the form structure, so decode the button
    // // groups data into this new key and remove the button_groups key.
    // $toolbar_settings['rows'] = json_decode($toolbar_settings['button_groups'], TRUE);
    // unset($toolbar_settings['button_groups']);

    // // Remove the plugin settings' vertical tabs state; no need to save that.
    // if ($form_state->hasValue(['editor', 'settings', 'plugins'])) {
    //   $form_state->unsetValue(['editor', 'settings', 'plugin_settings']);
    // }
  }

  /**
   * Returns a list of language codes supported by CKEditor.
   *
   * @return array
   *   An associative array keyed by language codes.
   */
  public function getLangcodes() {
    // // Cache the file system based language list calculation because this would
    // // be expensive to calculate all the time. The cache is cleared on core
    // // upgrades which is the only situation the CKEditor file listing should
    // // change.
    // $langcode_cache = \Drupal::cache()->get('storypage.langcodes');
    // if (!empty($langcode_cache)) {
    //   $langcodes = $langcode_cache->data;
    // }
    // if (empty($langcodes)) {
    //   $langcodes = [];
    //   // Collect languages included with CKEditor based on file listing.
    //   $files = scandir('core/assets/vendor/ckeditor/lang');
    //   foreach ($files as $file) {
    //     if ($file[0] !== '.' && preg_match('/\.js$/', $file)) {
    //       $langcode = basename($file, '.js');
    //       $langcodes[$langcode] = $langcode;
    //     }
    //   }
    //   \Drupal::cache()->set('ckeditor.langcodes', $langcodes);
    // }

    // // Get language mapping if available to map to Drupal language codes.
    // // This is configurable in the user interface and not expensive to get, so
    // // we don't include it in the cached language list.
    // $language_mappings = $this->moduleHandler->moduleExists('language') ? language_get_browser_drupal_langcode_mappings() : [];
    // foreach ($langcodes as $langcode) {
    //   // If this language code is available in a Drupal mapping, use that to
    //   // compute a possibility for matching from the Drupal langcode to the
    //   // CKEditor langcode.
    //   // For instance, CKEditor uses the langcode 'no' for Norwegian, Drupal
    //   // uses 'nb'. This would then remove the 'no' => 'no' mapping and replace
    //   // it with 'nb' => 'no'. Now Drupal knows which CKEditor translation to
    //   // load.
    //   if (isset($language_mappings[$langcode]) && !isset($langcodes[$language_mappings[$langcode]])) {
    //     $langcodes[$language_mappings[$langcode]] = $langcode;
    //     unset($langcodes[$langcode]);
    //   }
    // }

    // return $langcodes;
    return ['en' => 'en'];
  }

  public function getJSSettings(Editor $editor) {
    $settings = [];

    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    $libraries = [
      'gutenberg/frontkom-gutenberg-editor',
    ];
  
    return $libraries;
  }

  /**
   * Builds the "toolbar" configuration part of the CKEditor JS settings.
   *
   * @see getJSSettings()
   *
   * @param \Drupal\editor\Entity\Editor $editor
   *   A configured text editor object.
   * @return array
   *   An array containing the "toolbar" configuration.
   */
  public function buildToolbarJSSetting(Editor $editor) {
    $toolbar = [];

    return $toolbar;
  }

  /**
   * Builds the "contentsCss" configuration part of the CKEditor JS settings.
   *
   * @see getJSSettings()
   *
   * @param \Drupal\editor\Entity\Editor $editor
   *   A configured text editor object.
   * @return array
   *   An array containing the "contentsCss" configuration.
   */
  public function buildContentsCssJSSetting(Editor $editor) {
    return [];
  }

}
