<?php

namespace Drupal\gutenberg\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

define('OEMBED_DEFAULT_PROVIDER', 'http://open.iframe.ly/api/oembed');

/**
 * Returns responses for embed routes.
 */
class EmbedController extends ControllerBase {
  /**
   * Returns JSON representing the loaded file.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function get() {
    $url = \Drupal::request()->query->get('url') ?: 'default';
    $client = \Drupal::httpClient();
    $response = '';

    try {
      $request = $client->get(OEMBED_DEFAULT_PROVIDER . '?origin=drupal&url=' . $url);
      $response = $request->getBody();
    }

    catch (RequestException $e) {
      watchdog_exception('oembed', $e->getMessage());
    }

    var_dump($request);
    die();
    
    // if (!empty($response)) {
    //   $embed = json_decode($response);
    //   if (!empty($embed->html)) {
    //    $output = $embed->html;
    //   }
    //   elseif ($embed->type == 'photo') {
    //     $output = '<img src="' . $embed->url . '" title="' . $embed->title . '" style="width: 100%" />';
    //     $output = '<a href="' . $url . '">' . $output .'</a>';
    //   }
    // }

    return new JsonResponse($response);
  }
}