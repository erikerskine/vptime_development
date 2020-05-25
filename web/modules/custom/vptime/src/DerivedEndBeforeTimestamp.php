<?php

namespace Drupal\vptime;

use Drupal\Core\TypedData\TypedData;

/**
 * A computed property for processing a period 'end_before' timestamp.
 */
class DerivedEndBeforeTimestamp extends TypedData {

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
    $components = GregorianCalendar::incrementSmallestComponent($components);
    return GregorianCalendar::toTimestamp($components);
  }

}
