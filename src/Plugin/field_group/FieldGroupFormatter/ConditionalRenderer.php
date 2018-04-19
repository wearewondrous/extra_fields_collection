<?php

namespace Drupal\extra_fields_collection\Plugin\field_group\FieldGroupFormatter;

use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Conditional renderer field group element.
 *
 * @FieldGroupFormatter(
 *   id = "conditional_renderer",
 *   label = @Translation("Conditional Renderer"),
 *   description = @Translation("Renders the first not empty field added to the group"),
 *   supported_contexts = {
 *     "form",
 *     "view"
 *   }
 * )
 */
class ConditionalRenderer extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function preRender(&$element, $rendering_object) {
    parent::preRender($element, $rendering_object);
    $this->filterElements($element, $rendering_object);
  }

  /**
   * Let's to print the first field with value.
   *
   * @param $element
   *   The render element.
   * @param $rendering_object
   *   The rendering object information.
   */
  public function filterElements(&$element, $rendering_object) {
    if (isset($this->group->children)) {
      $keep_field = [];
      $entity = $rendering_object['#entity'];
      foreach ($this->group->children as $field_name) {
        if (isset($element[$field_name]) && ($entity->hasField($field_name) && !empty($entity->get($field_name)->getValue()))) {
          // Save the field name and stop the loop.
          $keep_field[] = $field_name;
          break 1;
        }
      }
      $hide_fields = array_diff($this->group->children, $keep_field);
      foreach ($hide_fields as $field_name) {
        // Keep the field but disable the access.
        $element[$field_name]['#access'] = FALSE;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm() {
    $form = parent::settingsForm();
    unset($form['id']);
    unset($form['classes']);
    return $form;
  }

}
