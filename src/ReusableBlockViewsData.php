<?php
namespace Drupal\gutenberg;
use Drupal\views\EntityViewsData;
/**
 * Provides the views data for the entity.
 */
class ReusableBlockViewsData extends EntityViewsData {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    //Go to the following url to get details.
    //https://api.drupal.org/api/drupal/core!modules!views!views.api.php/function/hook_views_data/8.2.x
    return $data;
  }
}