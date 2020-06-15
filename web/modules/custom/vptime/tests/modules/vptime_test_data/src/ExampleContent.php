<?php

namespace Drupal\vptime_test_data;

use Drupal\node\Entity\Node;

class ExampleContent {

  /**
   * Create a node suitable for test data.
   *
   * @param array $row
   *   Array with the following elements:
   *     - Date in the form yyyy-mm-dd. Required.
   *     - Time, in the form hh:mm or hh:mm:ss or hh:mm:ss.<frac>. Optional.
   *     - Civil timezone name, eg Europe/London. Optional.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  static function createTestDataNodeFromRow($row) {
    $date = $row[0];
    $time = (count($row) >= 2) ? $row[1] : null;
    $timezone = (count($row) >= 3) ? $row[2] : null;

    $city = $timezone
      ? substr($timezone, strpos($timezone, '/') + 1)
      : '';

    $title = sprintf('%s%s%s', $date,
      ($time ? ' ' . $time : ''),
      ($city ? ' ' . $city : ''));

    $value = sprintf('%s%s', $date, ($time ? 'T' . $time : ''));

    Node::create([
      'type' => 'event',
      'title' => $title,
      'field_vptime' => [
        'value' => $value,
        'timezone' => $timezone,
      ],
    ])->save();
  }

}
