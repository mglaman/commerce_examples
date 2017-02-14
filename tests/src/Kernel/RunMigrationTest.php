<?php

namespace Drupal\Tests\commerce_demo\Kernel;

use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductAttribute;
use Drupal\Tests\migrate\Kernel\MigrateTestBase;

/**
 * Tests that the demo migration runs.
 *
 * @group commerce_demo
 */
class RunMigrationTest extends MigrateTestBase {

  public static $modules = [
    'system', 'field', 'options', 'user', 'path', 'text', 'views', 'file',
    'image', 'migrate', 'migrate_plus', 'migrate_tools', 'migrate_source_csv',
    'profile', 'address', 'state_machine', 'inline_entity_form', 'entity',
    'entity_reference_revisions',
    'commerce',
    'commerce_price',
    'commerce_store',
    'commerce_order',
    'commerce_product',
    'commerce_payment',
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
      if ($configured_group_id == 'commerce_demo_tshirt') {
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
    /** @var \Drupal\commerce_product\Entity\ProductAttributeValueInterface[] $color_values */
    $color_values = $color->getValues();
    $this->assertNotEmpty($color_values);
    $this->assertEquals(4, count($color_values));

    $size = ProductAttribute::load('size');
    $size_values = $size->getValues();
    $this->assertNotEmpty($size_values);
    $this->assertEquals(3, count($size_values));
  }

  /**
   * Tests that the products got imported.
   */
  public function testProductsImported() {
    $products = Product::loadMultiple();
    $this->assertNotEmpty($products);
    $this->assertEquals(2, count($products));

    /** @var \Drupal\commerce_product\Entity\ProductInterface $product */
    foreach ($products as $product) {
      switch ($product->label()) {
        case 'Commerce Guys Hoodie':
          $this->assertEquals(12, count($product->getVariations()));
          break;

        case 'Drupal Commerce Cart Shirt':
          $this->assertEquals(12, count($product->getVariations()));
          break;
      }
    }
  }

}
