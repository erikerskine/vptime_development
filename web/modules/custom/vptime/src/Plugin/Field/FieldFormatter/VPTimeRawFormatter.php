<?php

namespace Drupal\vptime\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\vptime\GregorianCalendar;

/**
 * Plugin implementation of the 'vptime_raw' formatter.
 *
 * @FieldFormatter(
 *   id = "vptime_raw",
 *   label = @Translation("Raw"),
 *   field_types = {
 *     "vptime"
 *   }
 * )
 */
class VPTimeRawFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }
    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    if ($item->timezone) {
      $components = GregorianCalendar::componentsFromString($item->value);
      $components = GregorianCalendar::adjustTimezone($components, $item->timezone, date_default_timezone_get());
      return Html::escape(GregorianCalendar::componentsToString($components));
    }
    else {
      return Html::escape($item->value);
    }
  }

}
