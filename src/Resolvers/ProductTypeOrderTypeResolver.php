<?php

namespace Drupal\commerce_demo\Resolvers;

use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\commerce_order\Resolver\OrderTypeResolverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Resolves order type based on the purchased entity's bundle.
 *
 * This is an example of overriding the default order type destination for a
 * product type. In this case, we ensure that ebooks get put into a digital
 * order, and T Shirts remain in the default shippable order type.
 */
class ProductTypeOrderTypeResolver implements OrderTypeResolverInterface {

  /**
   * The order type storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $orderTypeStorage;

  /**
   * Constructs a new ProductTypeOrderTypeResolver object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->orderTypeStorage = $entity_type_manager->getStorage('commerce_order_type');
  }

  /**
   * {@inheritdoc}
   */
  public function resolve(OrderItemInterface $order_item) {

    $bundle = $order_item->getPurchasedEntity()->bundle();
    switch ($bundle) {
      case 'ebook':
        return $this->orderTypeStorage->load('digital')->id();

      case 't_shirt':
      default:
        return $this->orderTypeStorage->load('default')->id();
    }
  }

}
