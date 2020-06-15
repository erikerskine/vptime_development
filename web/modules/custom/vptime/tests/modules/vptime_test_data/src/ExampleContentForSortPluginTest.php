<?php

namespace Drupal\vptime_test_data;

use Drupal\node\Entity\Node;

class ExampleContentForSortPluginTest {

  /**
   * Create some example content suitable for ViewsSortPluginTest.
   *
   * We create a few events around new year, with zoned times, local times
   * and date only values, to demonstrate sorting appropriate to the
   * "current" timezone.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  static function create() {

    Node::create([
      'type' => 'event',
      'title' => 'New Year\'s Day',
      'field_vptime' => [
        'value' => '2021-01-01',
        'timezone' => NULL,
      ],
    ])->save();

    Node::create([
      'type' => 'event',
      'title' => 'Local celebrations',
      'field_vptime' => [
        'value' => '2021-01-01T00:00',
        'timezone' => NULL,
      ],
    ])->save();

    Node::create([
      'type' => 'event',
      'title' => 'Celebrations in Auckland',
      'field_vptime' => [
        'value' => '2021-01-01T00:00',
        'timezone' => 'Pacific/Auckland',
      ],
    ])->save();

    Node::create([
      'type' => 'event',
      'title' => 'Celebrations in Sydney',
      'field_vptime' => [
        'value' => '2021-01-01T00:00',
        'timezone' => 'Australia/Sydney',
      ],
    ])->save();

    Node::create([
      'type' => 'event',
      'title' => 'Celebrations in London',
      'field_vptime' => [
        'value' => '2021-01-01T00:00',
        'timezone' => 'GMT',
      ],
    ])->save();
  }

}
