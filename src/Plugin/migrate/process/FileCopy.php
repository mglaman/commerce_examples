<?php

namespace Drupal\commerce_demo\Plugin\migrate\process;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\migrate\process\FileCopy as CoreFileCopy;
use Drupal\migrate\Plugin\MigrateProcessInterface;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "commerce_demo_file_copy"
 * )
 */
class FileCopy extends CoreFileCopy {

  protected $filePath;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, StreamWrapperManagerInterface $stream_wrappers, FileSystemInterface $file_system, MigrateProcessInterface $download_plugin) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $stream_wrappers, $file_system, $download_plugin);
    $module = \Drupal::getContainer()->get('module_handler')->getModule('commerce_demo');
    $this->filePath = DRUPAL_ROOT . '/' . $module->getPath() . '/data/images';
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // If we're stubbing a file entity, return a URI of NULL so it will get
    // stubbed by the general process.
    if ($row->isStub()) {
      return NULL;
    }

    if (empty($value)) {
      return NULL;
    }

    $value = [
      $this->filePath . '/' . $value,
      'public://' . $value,
    ];

    return parent::transform($value, $migrate_executable, $row, $destination_property);
  }

  protected function getOverwriteMode() {
    return FILE_EXISTS_REPLACE;
  }
}
