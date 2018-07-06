<?php

namespace Drupal\gutenberg\Render\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Render\Element\RenderElement;
use Drupal\Core\Render\Element\FormElementInterface;
use Drupal\Core\Url;

abstract class FormElement extends RenderElement implements FormElementInterface {
  /**
   * #process callback for #pattern form element property.
   *
   * @param array $element
   *   An associative array containing the properties and children of the
   *   generic input element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   *
   * @return array
   *   The processed element.
   */
  public static function processFormElement(&$element, FormStateInterface $form_state, &$complete_form) {
    // if (isset($element['#pattern']) && !isset($element['#attributes']['pattern'])) {
    //   $element['#attributes']['pattern'] = $element['#pattern'];
    //   $element['#element_validate'][] = [get_called_class(), 'validatePattern'];
    // }
    // $element['#theme_wrappers'][] = 'form_element__gutenberg';
    // unset($element['#pre_render'][1]);
    // var_dump($element);
    $element['#theme'] .= '__gutenberg';
    return $element;
  }
}