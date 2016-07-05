<?php

namespace Drupal\Tests\commerce_demo\Kernel;

use Drupal\commerce_product\Entity\ProductAttribute;
use Drupal\commerce_product\Entity\ProductType;
use Drupal\commerce_product\Entity\ProductVariationType;
use Drupal\Tests\migrate\Kernel\MigrateTestBase;

/**
 * Tests that the demo migration runs.
 *
 * @group commerce_demo
 */
class RunMigrationTest extends MigrateTestBase {

  public static $modules = ['migrate', 'commerce', 'commerce_price', 'commerce_order',
    'commerce_product', 'migrate_plus', 'migrate_tools', 'migrate_source_csv', 'commerce_demo',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

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
   * Test product and variation type imported.
   */
  public function testProductType() {
    $product_type = ProductType::load('t_shirt');
    $this->assertNotNull($product_type);
    $product_variation_type = ProductVariationType::load('t_shirt');
    $this->assertNotNull($product_variation_type);

  }

  /**
   * Tests around attributes.
   */
  public function testAttributesImported() {
    $color = ProductAttribute::load('color');
    $this->assertNotNull($color);
    $color_values = $color->getValues();
    $this->assertNotEmpty($color_values);

    $size = ProductAttribute::load('size');
    $this->assertNotNull($size);
    $size_values = $size->getValues();
    $this->assertNotEmpty($size_values);
  }

}
