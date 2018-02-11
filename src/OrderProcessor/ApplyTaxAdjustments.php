<?php

namespace Drupal\commerce_examples\OrderProcessor;

use Drupal\commerce_order\Adjustment;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\OrderProcessorInterface;

/**
 * Applies VAT, if needed.
 *
 * Order processors run on the order refresh process. This process allows for
 * the recalculation of adjustments.
 *
 * This examples provides a shiv for applying VAT until the commerce_tax module
 * is replaced.
 */
class ApplyTaxAdjustments implements OrderProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function process(OrderInterface $order) {
    // Make sure the billing profile is set.
    if (!$order->get('billing_profile')->isEmpty()) {
      $billing_profile = $order->getBillingProfile();
      /** @var \Drupal\address\Plugin\Field\FieldType\AddressItem $address */
      $address = $billing_profile->get('address')->first();

      // Apply VAT for Germany.
      if ($address->getCountryCode() == 'DE') {
        $vat_adjustment = new Adjustment([
          'type' => 'tax',
          'label' => t('VAT'),
          'amount' => $order->getTotalPrice()->multiply('0.19'),
        ]);
        $order->addAdjustment($vat_adjustment);
      }
      // Let's say we have some nexus' in the United States.
      elseif ($address->getCountryCode() == 'US') {

        // Charge state sales tax for Wisconsin.
        if ($address->getAdministrativeArea() == 'WI') {
          $vat_adjustment = new Adjustment([
            'type' => 'tax',
            'label' => t('Sales tax'),
            'amount' => $order->getTotalPrice()->multiply('0.05'),
          ]);
          $order->addAdjustment($vat_adjustment);
        }
      }
    }
  }

}
