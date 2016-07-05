<?php

namespace Drupal\Tests\commerce_demo\Kernel;

use Drupal\commerce_product\Entity\ProductType;
use Drupal\commerce_product\Entity\ProductAttribute;
use Drupal\commerce_product\Entity\ProductVariationType;
use Drupal\Tests\token\Kernel\KernelTestBase;

/**
 * Tests that config installed.
 */
class DemoConfigInstalledTest extends KernelTestBase {
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

    $size = ProductAttribute::load('size');
    $this->assertNotNull($size);
  }

}
