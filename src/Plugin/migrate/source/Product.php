<?php

namespace Drupal\commerce_demo\Plugin\migrate\source;

use Drupal\commerce_demo\DemoCsv;
use Drupal\migrate_source_csv\Plugin\migrate\source\CSV;
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
  public function initializeIterator() {
    // File handler using header-rows-respecting extension of SPLFileObject.
    $file = new DemoCsv($this->configuration['path']);

    // Set basics of CSV behavior based on configuration.
    $delimiter = !empty($this->configuration['delimiter']) ? $this->configuration['delimiter'] : ',';
    $enclosure = !empty($this->configuration['enclosure']) ? $this->configuration['enclosure'] : '"';
    $escape = !empty($this->configuration['escape']) ? $this->configuration['escape'] : '\\';
    $file->setCsvControl($delimiter, $enclosure, $escape);

    // Figure out what CSV column(s) to use. Use either the header row(s) or
    // explicitly provided column name(s).
    if (!empty($this->configuration['header_row_count'])) {
      $file->setHeaderRowCount($this->configuration['header_row_count']);

      // Find the last header line.
      $file->rewind();
      $file->seek($file->getHeaderRowCount() - 1);

      $row = $file->current();
      foreach ($row as $header) {
        $header = trim($header);
        $column_names[] = [$header => $header];
      }
      $file->setColumnNames($column_names);
    }
    // An explicit list of column name(s) will override any header row(s).
    if (!empty($this->configuration['column_names'])) {
      $file->setColumnNames($this->configuration['column_names']);
    }

    return $file;
  }

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
