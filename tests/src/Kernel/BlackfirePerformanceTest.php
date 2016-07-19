<?php

namespace Drupal\Tests\commerce_demo\Kernel;

use Blackfire\Profile\Configuration;
use Blackfire\Profile\Metric;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_store\StoreCreationTrait;
use Drupal\Tests\migrate\Kernel\MigrateTestBase;
use Blackfire\Bridge\PhpUnit\TestCaseTrait as BlackfireTrait;

/**
 * Tests Commerce w/ Blackfire against imported content.
 *
 * @group commerce_demo
 * @group blackfire
 */
class BlackfirePerformanceTest extends MigrateTestBase {

  use StoreCreationTrait;
  use BlackfireTrait;

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
    $this->installEntitySchema('commerce_store');
    $this->installConfig(static::$modules);

    $this->createStore();

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
   * Tests product load.
   */
  public function testProductViewPerformance() {
    $product = Product::load(1);
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('commerce_product');

    $config = new Configuration();
    $config->defineMetric(new Metric('drupal.entity_field_manager.get_field_definitions', [
      '=Drupal\Core\Entity\EntityFieldManagerInterface::getFieldDefinitions',
    ]));
    $config->defineMetric(new Metric('drupal.cache.invalidate', [
      '=Drupal\Core\Cache\Cache::invalidateTags',
    ]));
    $config->assert('main.wall_time < 0.25s', 'Wall time');
    $config->assert('metrics.drupal.entity_field_manager.get_field_definitions.count < 1');
    $config->assert('drupal.cache.invalidate = 0');

    $this->assertBlackfire($config, function () use ($product, $view_builder) {
      $build = $view_builder->view($product);
      $this->render($build);
    });
  }

}
