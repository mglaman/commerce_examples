<?php

namespace Drupal\commerce_demo\Plugin\migrate\source;

use Drupal\migrate\Row;

/**
 * Source plugin for product content.
 *
 * @MigrateSource(
 *   id = "commerce_demo_product_csv"
 * )
 */
class Product extends CSV {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $targets = [];
    /* @var Row $row */
    if (!parent::prepareRow($row)) {
      return FALSE;
    }

    $sku_prefix = substr($row->getSourceProperty('SKU'), 0, 9);

    $query = \Drupal::entityQuery('commerce_product_variation')
      ->condition('sku', $sku_prefix, 'STARTS_WITH');

    $values = $query->execute();

    foreach ($values as $value) {
      $targets[] = ['target_id' => $value];
    }

    $row->setDestinationProperty('variations', $targets);
    $row->rehash();
    return TRUE;
  }

}
