<?php
namespace Drupal\gutenberg\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

/**
 * Sets site's default theme on Gutenberg enabled pages.
 */
class ThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   * Creates a new ThemeNegotiator instance.
   */
  public function __construct() {
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    $config = \Drupal::service('config.factory')->getEditable('gutenberg.settings');
    $node = $route_match->getParameter('node');

    if (!$node) {
      return FALSE;
    }

    $node_type = $node->getType();
    $gutenberg_enabled = $config->get($node_type . '_enable_full');

    if (!$gutenberg_enabled) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    $theme_name = \Drupal::service('theme_handler')->getDefault();
    return $theme_name; // $this->configFactory->get('system.theme')->get('bootstrap');
  }

}