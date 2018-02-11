<?php

namespace Drupal\commerce_examples\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of 'commerce_product_attributes_overview' formatter.
 *
 * @FieldFormatter(
 *   id = "commerce_examples_color_name",
 *   label = @Translation("Color name"),
 *   field_types = {
 *     "string",
 *   },
 * )
 */
class ColorNameFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'inline_template',
        '#template' => '<div style="background: {{ value_style }}; width: 30px; height: 30px;"><span class="hidden">{{ value }}</span></div>',
        '#context' => [
          'value' => $item->value,
          'value_style' => strtolower($item->value),
        ],
      ];
    }
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return $field_definition->getTargetEntityTypeId() == 'commerce_product_attribute_value' && $field_definition->getType() == 'string';
  }

}
