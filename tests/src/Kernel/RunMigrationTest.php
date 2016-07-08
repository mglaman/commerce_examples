<?php

namespace Drupal\Tests\commerce_demo\Kernel;

use Drupal\commerce_product\Entity\ProductAttribute;
use Drupal\Tests\migrate\Kernel\MigrateTestBase;

/**
 * Tests that the demo migration runs.
 *
 * @group commerce_demo
 */
class RunMigrationTest extends MigrateTestBase {

  public static $modules = [
    'system', 'field', 'options', 'user', 'path', 'text', 'user', 'views',
    'file', 'image',
    'migrate', 'migrate_plus', 'migrate_tools', 'migrate_source_csv',
    'profile', 'address', 'state_machine', 'inline_entity_form', 'entity',
    'commerce',
    'commerce_price',
    'commerce_store',
    'commerce_order',
    'commerce_product',
    'commerce_migrate',
    'commerce_demo',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installSchema('system', 'router');
    $this->installEntitySchema('user');
    $this->installEntitySchema('commerce_product_attribute');
    $this->installEntitySchema('commerce_product_attribute_value');
    $this->installEntitySchema('commerce_product_variation');
    $this->installEntitySchema('commerce_product_variation_type');
    $this->installEntitySchema('commerce_product');
    $this->installEntitySchema('commerce_product_type');
    $this->installConfig(static::$modules);

    /** @var \Drupal\migrate_plus\Plugin\MigrationConfigEntityPluginManager $manager */
    $manager = \Drupal::service('plugin.manager.config_entity_migration');
    $plugins = $manager->createInstances([]);
    $migrations = [];

    /** @var \Drupal\migrate_plus\Entity\Migration $migration */
    foreach ($plugins as $id => $migration) {
      $configured_group_id = $migration->get('migration_group');
      if (empty($configured_group_id)) {
        continue;
      }
      if ($configured_group_id == 'commerce_demo') {
        $migrations[] = $migration->id();
      }
    }

    $this->executeMigrations($migrations);
  }

  /**
   * Tests around attributes.
   */
  public function testAttributesImported() {
    $color = ProductAttribute::load('color');
    $color_values = $color->getValues();
    $this->assertNotEmpty($color_values);

    $size = ProductAttribute::load('size');
    $size_values = $size->getValues();
    $this->assertNotEmpty($size_values);
  }

}
