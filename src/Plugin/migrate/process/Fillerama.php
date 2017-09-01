<?php

namespace Drupal\commerce_demo\Plugin\migrate\process;

use Drupal\Component\Serialization\Json;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "fillerama"
 * )
 */
class Fillerama extends ProcessPluginBase {

  protected $fillerQuotes;
  protected $fillerHeadings;

  /**
   * @inheritDoc
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $module_handler = $this->getModuleHandler();
    $module = $module_handler->getModule('commerce_demo');
    $filler = Json::decode(file_get_contents(DRUPAL_ROOT . '/' . $module->getPath() . '/data/fillerama.json'));

    $this->fillerQuotes = $filler['db'];
    $this->fillerHeadings = $filler['headers'];
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $data = [];

    $data[] = $this->getRandomHeader();
    $data[] = $this->getRandomParagraph();
    $data[] = '';
    $data[] = $this->getRandomParagraph(mt_rand(5,10));

    return implode('', $data);
  }

  /**
   * Gets the module handler.
   *
   * @return \Drupal\Core\Extension\ModuleHandlerInterface
   *   The module handler.
   */
  protected function getModuleHandler() {
    if (!isset($this->moduleHandler)) {
      $this->moduleHandler = \Drupal::moduleHandler();
    }
    return $this->moduleHandler;
  }

  protected function getRandomHeader($type = 'h2') {
    return "<$type>" . $this->fillerHeadings[array_rand($this->fillerHeadings)]['header'] . "</$type>";
  }

  protected function getRandomParagraph($max_lines = 4) {
    $sentences = [];

    for ($i = 0; $i < $max_lines; $i++) {
      $sentences[] = $this->fillerQuotes[array_rand($this->fillerQuotes)]['quote'];
    }

    return '<p>' . implode(' ', $sentences) . '</p>';
  }

}
