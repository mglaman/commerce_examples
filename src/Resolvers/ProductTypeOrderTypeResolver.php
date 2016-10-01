<?php

namespace Drupal\commerce_demo\Resolvers;

use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\commerce_order\Resolver\OrderTypeResolverInterface;

/**
 * Resolves order type based on the purchased entity's bundle.
 *
 * This is an example of overriding the default order type destination for a
 * product type. In this case, we ensure that ebooks get put into a digital
 * order, and T Shirts remain in the default shippable order type.
 */
class ProductTypeOrderTypeResolver implements OrderTypeResolverInterface {

  /**
   * {@inheritdoc}
   */
  public function resolve(OrderItemInterface $order_item) {

    $bundle = $order_item->getPurchasedEntity()->bundle();
    switch ($bundle) {
      case 'ebook':
        return 'digital';

      case 't_shirt':
      default:
        return 'default';
    }
  }

}
