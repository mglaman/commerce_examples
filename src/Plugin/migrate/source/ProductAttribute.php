<?php

namespace Drupal\commerce_examples\Plugin\migrate\source;

/**
 * Source plugin for product content.
 *
 * @MigrateSource(
 *   id = "commerce_examples_csv_attribute_values"
 * )
 */
class ProductAttribute extends CSV {

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    $all_items = [];
    $colors = [];
    $fileIterator = parent::initializeIterator();

    $fileIterator->next();
    while ($fileIterator->valid()) {
      $row_data = $fileIterator->current() + $this->configuration;
      $fileIterator->next();

      $colors[] = $row_data[$this->configuration['keys'][0]];
    }
    $items = array_unique($colors);
    $items = array_filter($items);

    foreach ($items as $item) {
      $all_items[] = [
        $this->configuration['keys'][0] => $item,
      ];
    }

    return new \ArrayIterator($all_items);
  }

}
