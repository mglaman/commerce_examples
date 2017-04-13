<?php

namespace Drupal\commerce_demo\Plugin\Commerce\ShippingMethod;

use Drupal\commerce_price\Price;
use Drupal\commerce_shipping\Entity\ShipmentInterface;
use Drupal\commerce_shipping\PackageTypeManagerInterface;
use Drupal\commerce_shipping\Plugin\Commerce\ShippingMethod\ShippingMethodBase;
use Drupal\commerce_shipping\ShippingRate;
use Drupal\commerce_shipping\ShippingService;

/**
 * Provides the Table shipping method.
 *
 * @CommerceShippingMethod(
 *   id = "demo_table_rate",
 *   label = @Translation("Table rate"),
 * )
 */
class Table extends ShippingMethodBase {

  /**
   * Constructs a new FlatRate object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\commerce_shipping\PackageTypeManagerInterface $package_type_manager
   *   The package type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PackageTypeManagerInterface $package_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $package_type_manager);
    $this->services['default'] = new ShippingService('default', 'Ground shipping');
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'services' => ['default'],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function calculateRates(ShipmentInterface $shipment) {
    // Rate IDs aren't used in a flat rate scenario because there's always a
    // single rate per plugin, and there's no support for purchasing rates.
    $rate_id = 0;

    // Table rate starts at $5.00.
    $rate_amount = '5.00';

    $order_total_number = $shipment->getOrder()->getTotalPrice()->getNumber();

    if ($order_total_number > '100') {
      $rate_amount = '12.00';
    }
    elseif ($order_total_number > '50') {
      $rate_amount = '9.00';
    }
    elseif ($order_total_number > '25') {
      $rate_amount = '7.25';
    }

    $amount = new Price($rate_amount, $shipment->getOrder()->getTotalPrice()->getCurrencyCode());
    $rates = [];
    $rates[] = new ShippingRate($rate_id, $this->services['default'], $amount);

    return $rates;
  }

}
