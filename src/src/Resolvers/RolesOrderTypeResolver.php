<?php

namespace Drupal\commerce_demo\Resolvers;

use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\commerce_order\Resolver\OrderTypeResolverInterface;
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
   * Constructs a new RolesOrderTypeResolver object.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   The current user.
   */
  public function __construct(AccountProxyInterface $account) {
    $this->currentUser = $account;
  }

  /**
   * {@inheritdoc}
   */
  public function resolve(OrderItemInterface $order_item) {
    $user = \Drupal::currentUser();
    $roles = $user->getRoles();

    return in_array('b2b', $roles) ? 'b2b' : NULL;
  }

}
