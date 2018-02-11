<?php

namespace Drupal\Tests\commerce_examples\Kernel;

use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductAttribute;
use Drupal\Tests\migrate\Kernel\MigrateTestBase;

/**
 * Tests that the demo migration runs.
 *
 * @group commerce_examples
 */
class RunMigrationTest extends MigrateTestBase {

  public static $modules = [
    'system', 'field', 'options', 'user', 'path', 'text', 'views', 'file',
    'image', 'migrate',
    'profile', 'address', 'state_machine', 'inline_entity_form', 'entity',
    'entity_reference_revisions', 'physical',
    'commerce',
    'commerce_price',
    'commerce_store',
    'commerce_order',
    'commerce_product',
    'commerce_payment',
    'commerce_payment_example',
    'commerce_examples',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installSchema('system', 'router');
    $this->installEntitySchema('user');
    $this->installEntitySchema('commerce_store');
    $this->installEntitySchema('commerce_product_attribute');
    $this->installEntitySchema('commerce_product_attribute_value');
    $this->installEntitySchema('commerce_product_variation');
    $this->installEntitySchema('commerce_product_variation_type');
    $this->installEntitySchema('commerce_product');
    $this->installEntitySchema('commerce_product_type');
    $this->installConfig(static::$modules);

    $this->container->get('commerce_examples.migration_runner')->run();
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
    $this->assertEquals(5, count($products));

    /** @var \Drupal\commerce_product\Entity\ProductInterface $product */
    foreach ($products as $product) {
      switch ($product->label()) {
        case 'Commerce Guys Hoodie':
          $this->assertEquals(12, count($product->getVariations()));
          $default_variation = $product->getDefaultVariation();
          $this->assertEquals(10, $default_variation->get('weight')->number);
          $this->assertEquals(' oz', $default_variation->get('weight')->unit);
          break;

        case 'Drupal Commerce Cart Shirt':
          $this->assertEquals(12, count($product->getVariations()));
          $default_variation = $product->getDefaultVariation();
          $this->assertEquals(7, $default_variation->get('weight')->number);
          $this->assertEquals(' oz', $default_variation->get('weight')->unit);
          break;
      }
    }
  }

}
