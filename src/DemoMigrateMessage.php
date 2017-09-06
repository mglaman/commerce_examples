<?php

namespace Drupal\commerce_demo;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\migrate\MigrateMessage;

class DemoMigrateMessage extends MigrateMessage {

  protected $messages = [];

  /**
   * {@inheritdoc}
   */
  public function display($message, $type = 'status') {
    $type = isset($this->map[$type]) ? $this->map[$type] : RfcLogLevel::NOTICE;
    $this->messages[$type] = (string) $message;
  }

  public function getMessages() {
    return $this->messages;
  }

}
