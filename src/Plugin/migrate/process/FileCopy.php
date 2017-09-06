<?php

namespace Drupal\commerce_demo\Plugin\migrate\process;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Drupal\migrate\MigrateException;
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

    $source = $this->filePath . '/' . $value;
    $destination = 'public://' . $value;

    // If the source path or URI represents a remote resource, delegate to the
    // download plugin.
    if (!$this->isLocalUri($source)) {
      return $this->downloadPlugin->transform($value, $migrate_executable, $row, $destination_property);
    }

    // Ensure the source file exists, if it's a local URI or path.
    if (!file_exists($source)) {
      throw new MigrateException("File '$source' does not exist");
    }

    // If the start and end file is exactly the same, there is nothing to do.
    if ($this->isLocationUnchanged($source, $destination)) {
      return $destination;
    }

    // Check if a writable directory exists, and if not try to create it.
    $dir = $this->getDirectory($destination);
    // If the directory exists and is writable, avoid file_prepare_directory()
    // call and write the file to destination.
    if (!is_dir($dir) || !is_writable($dir)) {
      if (!file_prepare_directory($dir, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS)) {
        throw new MigrateException("Could not create or write to directory '$dir'");
      }
    }

    $final_destination = $this->writeFile($source, $destination, $this->getOverwriteMode());
    if ($final_destination) {
      return $final_destination;
    }
    throw new MigrateException("File $source could not be copied to $destination");
  }

  /**
   * {@inheritdoc}
   */
  protected function getOverwriteMode() {
    return FILE_EXISTS_REPLACE;
  }

}
