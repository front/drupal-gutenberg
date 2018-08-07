<?php

namespace Drupal\gutenberg\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\block\Entity\Block;

/**
 * Returns responses for our blocks routes.
 */
class BlocksController extends ControllerBase {
  /**
   * Returns JSON representing the loaded blocks.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function load(Request $request) {
    $blockManager = \Drupal::service('plugin.manager.block');
    $contextRepository = \Drupal::service('context.repository');
    // Get blocks definition
    $definitions = $blockManager->getDefinitionsForContexts($contextRepository->getAvailableContexts());
    return new JsonResponse($definitions);
  }

  /**
   * Returns JSON representing the loaded blocks.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   * @param string $plugin_id
   *   Plugin ID.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function loadById(Request $request, $plugin_id) {
    $block_manager = \Drupal::service('plugin.manager.block');
    $config = [];
    $plugin_block = $block_manager->createInstance($plugin_id, $config);

    // Some blocks might implement access check.
    $access_result = $plugin_block->access(\Drupal::currentUser());

    // Return empty render array if user doesn't have access.
    // $access_result can be boolean or an AccessResult class
    if (is_object($access_result) && $access_result->isForbidden() || is_bool($access_result) && !$access_result) {
      // You might need to add some cache tags/contexts.
      return new JsonResponse(['html' => 'No access.']);
    }

    $render = $plugin_block->build();
    $content = \Drupal::service('renderer')->render($render);

    // If the block is a view with contexts defined, it may
    // not render on the editor because of, for example, the
    // node path. Let's just write some warning if no content.
    if ($content === '') {
      $content = t('Not able to render the content on editor possibiliy due to path restrictions.');
    }

    return new JsonResponse(['html' => $content]);
  }

}