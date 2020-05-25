<?php

namespace Drupal\vptime;

use Drupal\Core\TypedData\TypedData;

/**
 * A computed property for processing a time point 'start_at' timestamp.
 */
class DerivedStartAtTimestamp extends TypedData {

  /**
   * {@inheritdoc}
   */
  public function getValue() {
    $item = $this->getParent();
    $text = $item->value;

    if (empty($text)) {
      return NULL;
    }

    $components = GregorianCalendar::componentsFromString($text);
    return GregorianCalendar::toTimestamp($components);
  }

}
