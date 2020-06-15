<?php

namespace Drupal\vptime_test_data;

use Drupal\node\Entity\Node;

class ExampleContentForArgumentPluginTest {

  /**
   * Create some example content suitable for ViewsArgumentPluginTest.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  static function create() {

    // Note that when tests are run the "current" timezone is Australia/Sydney,
    // therefore any local times (stored without a timezone) are interpreted
    // as being in that zone.

    $timestamps = [
      '2020-06-01T23:00',
      '2020-06-02T00:00',
      '2020-06-02T09:30',
      '2020-06-02T23:59',
      '2020-06-03T00:00',
    ];

    $timezones = [
      'Pacific/Auckland',
      'Australia/Sydney',
      'Europe/Berlin',
    ];

    // One local event that matches the argument exactly.
    Node::create([
      'type' => 'event',
      'title' => 'LOCAL 2020-06-02',
      'field_vptime' => [
        'value' => '2020-06-02',
        'timezone' => NULL,
      ],
    ])->save();

    // One local event per timestamp
    foreach ($timestamps as $timestamp) {
      Node::create([
        'type' => 'event',
        'title' => "LOCAL $timestamp",
        'field_vptime' => [
          'value' => $timestamp,
          'timezone' => NULL,
        ],
      ])->save();
    }

    // One non-local event per timestamp per timezone
    foreach ($timestamps as $timestamp) {
      foreach ($timezones as $timezone) {
        Node::create([
          'type' => 'event',
          'title' => "$timezone $timestamp",
          'field_vptime' => [
            'value' => $timestamp,
            'timezone' => $timezone,
          ],
        ])->save();
      }
    }

  }

}
