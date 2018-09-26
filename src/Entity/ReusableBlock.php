<?php

namespace Drupal\gutenberg\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Defines the reusable block entity.
 *
 * @ContentEntityType(
 *   id = "reusable_block",
 *   label = @Translation("Reusable block"),
 *   base_table = "reusable_block",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 *   handlers = {
 *     "views_data" = "Drupal\gutenberg\ReusableBlockViewsData",
 *   }
 * )
 */
class ReusableBlock extends ContentEntityBase implements ContentEntityInterface {
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Reusable block entity.'))
      ->setReadOnly(TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Reusable block entity.'))
      ->setReadOnly(TRUE);

    $fields['content'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Content'))
      ->setDescription(t('The content (html) of the Reusable block entity.'));

    return $fields;
  }

}