<?php

namespace Drupal\commerce_examples\Demo;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;

/**
 * Runs the migration on demand.
 */
class MigrationRunner {

  /**
   * The migration plugin manager.
   *
   * @var \Drupal\migrate\Plugin\MigrationPluginManagerInterface|\Drupal\migrate\Plugin\MigrationPluginManager
   */
  protected $manager;

  /**
   * MigrationRunner constructor.
   *
   * @param \Drupal\migrate\Plugin\MigrationPluginManagerInterface $manager
   *   The migration plugin manager.
   */
  public function __construct(MigrationPluginManagerInterface $manager) {
    $this->manager = $manager;
  }

  /**
   * Import content from migrations.
   */
  public function run() {
    $this->execute('import');
  }

  /**
   * Remove content from migrations.
   */
  public function remove() {
    $this->execute('rollback');
  }

  /**
   * Import or remove content from migrations.
   *
   * @param string $method_name
   *   The method to execute: import or rollback.
   */
  protected function execute($method_name) {
    $migration_ids = [
      'commerce_examples_product_attribute_color',
      'commerce_examples_product_attribute_size',
      'commerce_examples_product_variation_import_tshirt',
      'commerce_examples_product_import_tshirt',

      'commerce_examples_product_variation_import_ebook',
      'commerce_examples_product_import_ebook',
    ];

    array_walk($migration_ids, function ($migration_id) use ($method_name) {
      /** @var \Drupal\migrate\Plugin\Migration $migration */
      if (!$migration = $this->manager->createInstance($migration_id)) {
        throw new PluginNotFoundException($migration_id);
      }
      $migrate_executable = (new MigrateExecutable($migration, new MigrateMessage()));
      $result = call_user_func_array([$migrate_executable, $method_name], []);
      if ($result != MigrationInterface::RESULT_COMPLETED) {
        throw new \Exception($migration->id() . ' failed');
      }
    });
  }

}
