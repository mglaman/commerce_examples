<?php

namespace Drupal\commerce_examples\Resolvers;

use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\commerce_order\Resolver\OrderTypeResolverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Resolves order type based on roles.
 *
 * This ensures that all B2B users will have the B2B order type.
 */
class RolesOrderTypeResolver implements OrderTypeResolverInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The order type storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $orderTypeStorage;

  /**
   * Constructs a new RolesOrderTypeResolver object.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(AccountProxyInterface $account, EntityTypeManagerInterface $entity_type_manager) {
    $this->currentUser = $account;
    $this->orderTypeStorage = $entity_type_manager->getStorage('commerce_order_type');
  }

  /**
   * {@inheritdoc}
   */
  public function resolve(OrderItemInterface $order_item) {
    $user = \Drupal::currentUser();
    $roles = $user->getRoles();

    return in_array('b2b', $roles) ? $this->orderTypeStorage->load('b2b')->id() : NULL;
  }

}
