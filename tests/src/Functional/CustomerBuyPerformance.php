<?php

namespace Drupal\Tests\commerce_demo\Functional;

use Blackfire\Bridge\PhpUnit\TestCaseTrait as BlackfireTrait;
use Blackfire\Profile\Configuration;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests customer buying experience performance.
 *
 * @group commerce_demo
 */
class CustomerBuyPerformance extends BrowserTestBase {
  use BlackfireTrait;

  /**
   * The source database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $sourceDatabase;

  public static $modules = ['migrate', 'migrate_plus', 'migrate_tools', 'migrate_source_csv',
    'commerce', 'commerce_price', 'commerce_order', 'commerce_product', 'commerce_cart',
    'commerce_checkout', 'commerce_demo',
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
   * Tests add to cart performance.
   *
   * @group blackfire
   */
  public function testAddToCartPerformance() {
    $config = new Configuration();
    $config->assert('main.wall_time < 1s', 'Wall time');
    $this->drupalGet('product/1');
    $this->assertBlackfire($config, function () {
      $this->submitForm([], 'Add to cart');
    });
  }

  /**
   * Executes a set of migrations in dependency order.
   *
   * @param string[] $ids
   *   Array of migration IDs, in any order.
   */
  protected function executeMigrations(array $ids) {
    $manager = $this->container->get('plugin.manager.migration');
    array_walk($ids, function ($id) use ($manager) {
      // This is possibly a base plugin ID and we want to run all derivatives.
      $instances = $manager->createInstances($id);
      array_walk($instances, [$this, 'executeMigration']);
    });
  }

}
