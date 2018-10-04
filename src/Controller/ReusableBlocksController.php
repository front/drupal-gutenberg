<?php

namespace Drupal\gutenberg\Controller;

use Drupal\block_content\Entity\BlockContent;
use Drupal\gutenberg\Entity\ReusableBlock;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for our blocks routes.
 */
class ReusableBlocksController extends ControllerBase {
  /**
   * Returns JSON representing the loaded blocks.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   * @param string $block_id
   *   The reusable block id.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function load(Request $request, $block_id = NULL) {
    if ($block_id && $block_id > 0) {
      $block = BlockContent::load($block_id);

      return new JsonResponse([
        'id' => $block->id(),
        'title' => $block->info->value,
        'content' => $block->body->value
      ]);
    }

    $ids = \Drupal::entityQuery('block_content')
    ->condition('type', 'reusable_block')
    ->execute();

    $blocks = BlockContent::loadMultiple($ids);
    $result = array();

    foreach ($blocks as $key => $block) {
      $result[] = array(
        'id' => $block->id(),
        'title' => $block->info->value,
        'content' => $block->body->value
      );
    }
    
    return new JsonResponse($result);
  }

  /**
   * Saves reusable block.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   * @param string $block_id
   *   The reusable block id.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function save(Request $request, $block_id = NULL) {
    if ($block_id && $block_id > 0) {
      $data = json_decode($request->getContent(), true);
      $block = BlockContent::load($block_id);
      $block->body->value = $data['content'];
      $block->info->value = $data['title'];
    }
    else {
      $params = $request->request->all();
      $block = BlockContent::create([
        'info' => $params['title'],
        'type' => 'reusable_block',
        'body' => [
          'value' => $params['content'],
          'format' => 'full_html',
        ],
      ]);
    }

    $block->save();

    return new JsonResponse([
      'id' => $block->id(),
      'title' => $block->info->value,
      'content' => $block->body->value
    ]);
  }

  /**
   * Delete reusable block.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   * @param string $block_id
   *   The reusable block id.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function delete(Request $request, $block_id = NULL) {
    $block = BlockContent::load($block_id);
    $block->delete();

    return new JsonResponse([
      'id' => $block_id,
    ]);
  }

}