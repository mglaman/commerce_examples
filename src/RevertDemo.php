<?php

namespace Drupal\commerce_demo;

use Drupal\Core\Config\Entity\ConfigDependencyManager;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\Config\InstallStorage;
use Drupal\Core\Config\StorageInterface;

class RevertDemo {

  function revertConfig() {
    /** @var \Drupal\commerce\Config\ConfigUpdaterInterface $updater */
    $updater = \Drupal::service('commerce.config_updater');
    $default_install_path = drupal_get_path('module', 'commerce_demo') . '/' . InstallStorage::CONFIG_INSTALL_DIRECTORY;
    $storage = new FileStorage($default_install_path, StorageInterface::DEFAULT_COLLECTION);
    $data = $storage->readMultiple($storage->listAll(''));
    $dependency_manager = new ConfigDependencyManager();
    $config_names = $dependency_manager->setData($data)->sortAll();

    $import = [];
    $revert = [];
    foreach ($config_names as $name) {
      // For some reason this causes errors, skip this config.
      if (strpos($name, 'migrate_plus.migration_group') !== FALSE) {
        continue;
      }
      if ($updater->loadFromActive($name)) {
        $revert[] = $name;
      }
      else {
        $import[] = $name;
      }
    }
    $updater->import($import);
    $updater->revert($revert, FALSE);
  }

}
