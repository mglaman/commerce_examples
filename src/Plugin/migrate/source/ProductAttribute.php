<?php

namespace Drupal\commerce_demo\Plugin\migrate\source;

use Drupal\migrate_source_csv\Plugin\migrate\source\CSV;

/**
 * Source plugin for product content.
 *
 * @MigrateSource(
 *   id = "commerce_demo_csv_attribute_values"
 * )
 */
class ProductAttribute extends CSV {
    public function initializeIterator() {
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
