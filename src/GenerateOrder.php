<?php

namespace Drupal\commerce_examples;

use Drupal\commerce_price\RounderInterface;
use Drupal\commerce_store\Entity\Store;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\profile\Entity\Profile;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class GenerateOrder {

  protected $entityTypeManager;

  /**
   * @var \Drupal\commerce_product\ProductVariationStorageInterface
   */
  protected $variantStorage;

  /**
   * @var \Drupal\commerce_order\OrderItemStorageInterface
   */
  protected $orderItemStorage;

  /**
   * @var \Drupal\commerce_order\OrderStorage
   */
  protected $orderStorage;

  /**
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * @var \Drupal\commerce_price\RounderInterface
   */
  protected $rounder;

  /**
   * The event generator.
   *
   * @var \Drupal\leancom_reports\EventGenerator
   */
  protected $eventGenerator;

  public function __construct(EntityTypeManagerInterface $entityTypeManager, ClientInterface $client, RounderInterface $rounder) {
    $this->entityTypeManager = $entityTypeManager;
    $this->variantStorage = $entityTypeManager->getStorage('commerce_product_variation');
    $this->orderItemStorage = $entityTypeManager->getStorage('commerce_order_item');
    $this->orderStorage = $entityTypeManager->getStorage('commerce_order');
    $this->client = $client;
    $this->rounder = $rounder;
  }

  public function bulkCreate() {
    $start = new \DateTime();
    $start->modify('-1 year');

    $end = new \DateTime();
    $end->modify('today');

    for ($i = $start; $i <= $end; $i->modify('+1 day')) {
      $how_many = rand(3, 15);
      for ($x = 0; $x < $how_many; $x++) {
        $this->create($i);
        print PHP_EOL . $i->format(\DateTime::ISO8601);
      }
      $event_dispatcher = \Drupal::getContainer()->get('event_dispatcher');
      $event_dispatcher->dispatch(
        KernelEvents::TERMINATE,
        new PostResponseEvent(\Drupal::getContainer()->get('kernel'), new Request(), new Response())
      );
    }
  }

  public function create(\DateTime $when = NULL) {
    if (!$when) {
      $when = new \DateTime();
      $when->modify('-1 month');
    }

    $order = $this->generateOrder();
    $order->setPlacedTime($when->getTimestamp());
    $order->setCreatedTime($when->getTimestamp());
    $order->setBillingProfile($this->generateBillingProfile());
    $order->save();

    $workflow = $order->getState()->getWorkflow();
    $order->getState()->applyTransition($workflow->getTransition('place'));
    $order->save();
  }

  public function generateOrder() {
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $this->orderStorage->create([
      'uid' => 0,
      'type' => 'default',
    ]);
    $order->setStore(Store::load(1));
    $order->setIpAddress($this->getRandomIp());

    $variations = $this->getRandomVariants();
    $order_items = [];
    foreach ($variations as $variation) {
      $order_item = $this->orderItemStorage->createFromPurchasableEntity($variation);
      // Order item total calc happens on save.
      $total_price = $order_item->getUnitPrice()->multiply($order_item->getQuantity());
      $order_item->total_price = $this->rounder->round($total_price);

      $order_items[] = $order_item;
    }

    $order->setItems($order_items);
    $order->recalculateTotalPrice();
    return $order;
  }

  public function generateBillingProfile() {
    $person = $this->getRandomUser();
    $address = $this->getRandomAddress();

    $profile = Profile::create([
      'type' => 'customer',
      'address' => [
        'organization' => '',
        'country_code' => 'US',
        'postal_code' => $address['postal_code'],
        'locality' => $address['locality'],
        'address_line1' => $address['address_line1'],
        'administrative_area' => $address['administrative_area'],
        'given_name' => ucfirst($person['name']['first']),
        'family_name' => ucfirst($person['name']['last']),
      ],
      'uid' => 0,
    ]);

    return $profile;
  }

  /**
   * Gets a set of 1-4 random variations.
   *
   * @return \Drupal\commerce_product\Entity\ProductVariationInterface[]
   *   The variations.
   */
  protected function getRandomVariants() {
    $variations = $this->variantStorage->loadByProperties(['type' => 't_shirt']);

    $keys = array_rand($variations, rand(2, 5));
    $demo_variations = [];
    foreach ($keys as $key) {
      $demo_variations[] = $variations[$key];
    }

    return $demo_variations;
  }

  protected function getRandomIp() {
    $ips = [
      '75.86.161.54',
      '75.26.161.58',
      '15.26.161.58',
    ];

    return $ips[array_rand($ips)];
  }

  protected function getRandomUser() {
    $person = $this->client->get('https://randomuser.me/api/');
    $person = Json::decode($person->getBody()->getContents());
    return $person['results'][0];
  }

  protected function getRandomAddress() {
    $addresses = [
      [
        'address_line1' => '8502 Pilgrim St.',
        'locality' => 'Mokena',
        'administrative_area' => 'IL',
        'postal_code' => '60448',
      ],
      [
        'address_line1' => '7691 East 6th St',
        'locality' => 'Lewiston',
        'administrative_area' => 'ME',
        'postal_code' => '04240',
      ],
      [
        'address_line1' => '315 Addison Court ',
        'locality' => 'New Windsor',
        'administrative_area' => 'NY',
        'postal_code' => '12553',
      ],
      [
        'address_line1' => '45 Bow Ridge Ave',
        'locality' => 'West Chicago',
        'administrative_area' => 'IL',
        'postal_code' => '60185',
      ],
      [
        'address_line1' => '4 Washington Avenue',
        'locality' => 'Commack',
        'administrative_area' => 'NY',
        'postal_code' => '11725',
      ],
      [
        'address_line1' => '31 Fairfield Dr',
        'locality' => 'Bonita Springs',
        'administrative_area' => 'FL',
        'postal_code' => '34135',
      ],
      [
        'address_line1' => '8757 Homestead St',
        'locality' => 'Port Chester',
        'administrative_area' => 'NY',
        'postal_code' => '10573',
      ],
      [
        'address_line1' => '8281 Fawn St.',
        'locality' => 'Strongsville',
        'administrative_area' => 'OH',
        'postal_code' => '44136',
      ],
      [
        'address_line1' => '7364 Wild Horse Street',
        'locality' => 'Wake Forest',
        'administrative_area' => 'NC',
        'postal_code' => '27587',
      ],
      [
        'address_line1' => '7267 Surrey Ave',
        'locality' => 'Butler',
        'administrative_area' => 'PA',
        'postal_code' => '16001',
      ],
    ];

    return $addresses[array_rand($addresses)];
  }

}
