<?php

namespace Drupal\Tests\commerce_demo\Kernel;

use Drupal\commerce_product\Entity\ProductType;
use Drupal\commerce_product\Entity\ProductAttribute;
use Drupal\commerce_product\Entity\ProductVariationType;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests that config installed.
 *
 * @group commerce_demo
 */
class DemoInstalledTest extends KernelTestBase {
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
    'commerce_shipping',
    'commerce_checkout',
    'commerce_demo',
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
    $this->installEntitySchema('commerce_shipment');
    $this->installEntitySchema('commerce_shipment_type');
    $this->installEntitySchema('commerce_shipping_method');
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

  /**
   * Tests that the demo store is installed.
   */
  public function testStoreInstalled() {
    module_load_install('commerce_demo');
    commerce_demo_install();

    /** @var \Drupal\commerce_store\StoreStorageInterface $store_storage */
    $store_storage = $this->container->get('entity_type.manager')->getStorage('commerce_store');

    $demo_store = $store_storage->loadDefault();
    $this->assertEquals('Demo store', $demo_store->label());
    $this->assertEquals('admin@example.com', $demo_store->getEmail());
    $this->assertEquals('USD', $demo_store->getDefaultCurrencyCode());

    $demo_address = $demo_store->getAddress();
    $this->assertEquals('US', $demo_address->getCountryCode());
  }

}
