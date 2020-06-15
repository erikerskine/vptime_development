<?php

namespace Drupal\vptime\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\NumericFilter;
use Drupal\vptime\GregorianCalendar;


/**
 * Filter to handle vptime value.
 *
 * @ViewsFilter("vptime")
 */
class VPTime extends NumericFilter {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['operator']['default'] = 'overlaps';
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function operators() {
    $operators = [];

    // These operators are concerned with both start_at and end_before timestamps:

    $operators['matches'] = [
      'title' => $this->t('Matches'),
      'method' => 'opSimple',
      'short' => $this->t('matches'),
      'values' => 1,
    ];
    $operators['does not match'] = [
      'title' => $this->t('Does not match'),
      'method' => 'opSimple',
      'short' => $this->t('does not match'),
      'values' => 1,
    ];
    $operators['is within'] = [
      'title' => $this->t('Is within'),
      'method' => 'opSimple',
      'short' => $this->t('within'),
      'values' => 1,
    ];
    $operators['is within range'] = [
      'title' => $this->t('Is within range'),
      'method' => 'opRange',
      'short' => $this->t('within range'),
      'values' => 2,
    ];
    $operators['is not within'] = [
      'title' => $this->t('Is not within'),
      'method' => 'opSimple',
      'short' => $this->t('not within'),
      'values' => 1,
    ];
    $operators['is not within range'] = [
      'title' => $this->t('Is not within range'),
      'method' => 'opRange',
      'short' => $this->t('is not within range'),
      'values' => 2,
    ];
    $operators['overlaps'] = [
      'title' => $this->t('Overlaps'),
      'method' => 'opSimple',
      'short' => $this->t('overlaps'),
      'values' => 1,
    ];
    $operators['overlaps range'] = [
      'title' => $this->t('Overlaps range'),
      'method' => 'opRange',
      'short' => $this->t('overlaps range'),
      'values' => 2,
    ];
    $operators['does not overlap'] = [
      'title' => $this->t('Does not overlap'),
      'method' => 'opSimple',
      'short' => $this->t('does not overlap'),
      'values' => 1,
    ];
    $operators['does not overlap range'] = [
      'title' => $this->t('Does not overlap range'),
      'method' => 'opRange',
      'short' => $this->t('does not overlap range'),
      'values' => 2,
    ];

    // These operators are solely concerned with the start_at timestamp:

    $operators['starts before'] = [
      'title' => $this->t('Starts before'),
      'method' => 'opSimple',
      'short' => $this->t('starts before'),
      'values' => 1,
    ];
    $operators['during or after'] = [
      'title' => $this->t('Is during or after'),
      'method' => 'opSimple',
      'short' => $this->t('is during or after'),
      'values' => 1,
    ];
    $operators['is after'] = [
      'title' => $this->t('Is after'),
      'method' => 'opSimple',
      'short' => $this->t('is after'),
      'values' => 1,
    ];
    $operators['starts before or during'] = [
      'title' => $this->t('Starts before or during'),
      'method' => 'opSimple',
      'short' => $this->t('starts before or during'),
      'values' => 1,
    ];
    $operators['starts within'] = [
      'title' => $this->t('Starts within'),
      'method' => 'opSimple',
      'short' => $this->t('starts within'),
      'values' => 1,
    ];
    $operators['starts within range'] = [
      'title' => $this->t('Starts within range'),
      'method' => 'opRange',
      'short' => $this->t('starts within range'),
      'values' => 2,
    ];
    $operators['does not start within'] = [
      'title' => $this->t('Does not start within'),
      'method' => 'opSimple',
      'short' => $this->t('does not start within'),
      'values' => 1,
    ];
    $operators['does not start within range'] = [
      'title' => $this->t('Does not start within range'),
      'method' => 'opRange',
      'short' => $this->t('does not start within range'),
      'values' => 2,
    ];

    // These operators are solely concerned with the end_before timestamp:

    $operators['is before'] = [
      'title' => $this->t('Is before'),
      'method' => 'opSimple',
      'short' => 'is before',
      'values' => 1,
    ];
    $operators['ends during or after'] = [
      'title' => $this->t('Ends during or after'),
      'method' => 'opSimple',
      'short' => 'ends during or after',
      'values' => 1,
    ];
    $operators['ends after'] = [
      'title' => $this->t('Ends after'),
      'method' => 'opSimple',
      'short' => 'ends after',
      'values' => 1,
    ];
    $operators['is before or during'] = [
      'title' => $this->t('Is before or during'),
      'method' => 'opSimple',
      'short' => 'is before or during',
      'values' => 1,
    ];
    $operators['ends within'] = [
      'title' => $this->t('Ends within'),
      'method' => 'opSimple',
      'short' => 'ends within',
      'values' => 1,
    ];
    $operators['ends within range'] = [
      'title' => $this->t('Ends within range'),
      'method' => 'opRange',
      'short' => 'ends within range',
      'values' => 2,
    ];
    $operators['does not end within'] = [
      'title' => $this->t('Does not end within'),
      'method' => 'opSimple',
      'short' => 'does not end within',
      'values' => 1,
    ];
    $operators['does not end within range'] = [
      'title' => $this->t('Does not end within range'),
      'method' => 'opRange',
      'short' => 'does not end within range',
      'values' => 2,
    ];

//
//    // TODO: where is 'allow empty' defined?
////    // if the definition allows for the empty operator, add it.
////    if (!empty($this->definition['allow empty'])) {
////      $operators['empty'] = [
////        'title' => $this->t('Is empty (NULL)'),
////        'method' => 'opEmpty',
////        'short' => $this->t('empty'),
////        'values' => 0,
////      ];
////      $operators['not empty'] = [
////        'title' => $this->t('Is not empty (NOT NULL)'),
////        'method' => 'opEmpty',
////        'short' => $this->t('not empty'),
////        'values' => 0,
////      ];
////    }

    return $operators;
  }



  /**
   * Filters by operators that take a single value.
   *
   * @param object $field
   *   The views field.
   */
  protected function opSimple($field) {

    // If the filter value is a single 'value', we can still treat it as
    // if it were a range, the size of which depends on the precision,
    // eg 'January 2020'.
    // The corresponding WHERE expressions need to be constructed using the
    // two timestamps at either end of that range, referred to here as
    // $value_low and $value_high.
    // They follow the same convention as start_at/end_before, whereby the
    // timestamp at the end ($value_high) is not within, but immediately
    // *after* the end of the range.
    $value_low = $this->getStartAtTimestampForValue('value');
    $value_high = $this->getEndBeforeTimestampForValue('value');

    $expression = $this->buildExpression($this->operator, $value_low, $value_high);
    if ($expression) {
      $this->query->addWhereExpression($this->options['group'], $expression, [
        ':default_timezone' => date_default_timezone_get(),
        ':value_low' => $value_low,
        ':value_high' => $value_high,
      ]);
    }
  }


  /**
   * Filters by operators that take min and max values.
   *
   * @param object $field
   *   The views field.
   */
  protected function opRange($field) {

    // As opSimple(), but where value_low/value_high are derived from
    // min and max filter values.
    $value_low = $this->getStartAtTimestampForValue('min');
    $value_high = $this->getEndBeforeTimestampForValue('max');

    $expression = $this->buildExpression($this->operator, $value_low, $value_high);
    if ($expression) {
      $this->query->addWhereExpression($this->options['group'], $expression, [
        ':default_timezone' => date_default_timezone_get(),
        ':value_low' => $value_low,
        ':value_high' => $value_high,
      ]);
    }
  }


  /**
   * @param string $operator
   * @param string $value_low
   * @param string $value_high
   *
   * @return string
   *   A string to be passed to addWhereExpression().
   */


  protected function buildExpression($operator, $value_low, $value_high) {

    // Treat the two filter value timestamps as though they are in the
    // current timezone.
    $value_low_formula = ":value_low::timestamp AT TIME ZONE :default_timezone";
    $value_high_formula = ":value_high::timestamp AT TIME ZONE :default_timezone";

    // Views thinks this filter operates on the 'value' column.
    // That's a string holding something like '2020-01'. Of more use to us
    // is the corresponding _start_at and end_before timestamp columns.
    $start_at_field = $this->definition['field_name'] . '_start_at';
    $end_before_field = $this->definition['field_name'] . '_end_before';

    // We will be querying the start_at/end_before columns, but because those
    // columns hold local timestamps we aren't in the column values directly.
    // Instead, we need to read them converted into an appropriate time zone.
    // So rather than refer to start_at/end_before in the query, we always
    // use an expression like 'start_at/end_before AT TIME ZONE ...' instead.
    $timezone_field = $this->definition['field_name'] . '_timezone';
    $timezone_formula = "COALESCE(NULLIF($timezone_field, ''), :default_timezone)";
    $start_at_formula = ":start_at::timestamp AT TIME ZONE $timezone_formula";
    $end_before_formula = ":end_before::timestamp AT TIME ZONE $timezone_formula";

    \Drupal::messenger()->addStatus("\$start_at_formula: $start_at_formula");
    \Drupal::messenger()->addStatus("\$end_before_formula: $end_before_formula");
    \Drupal::messenger()->addStatus("\$value_low_formula: $value_low_formula");
    \Drupal::messenger()->addStatus("\$value_high_formula: $value_high_formula");

    if ($this->operator === 'is') {
      // Example: "is January" implies start_at = 1 Jan AND end_before = 1 Feb
      return "$start_at_formula = $value_low_formula AND $end_before_formula = $value_high_formula";
    }
    elseif ($this->operator === 'is not') {
      // Example: "is not January" implies NOT (start_at = 1 Jan AND end_before = 1 Feb)
      return "NOT ($start_at_formula = $value_low_formula AND $end_before_formula = $value_high_formula)";
    }
    elseif ($this->operator === 'is within' || $this->operator === 'is within range') {
      // Example: "is within January" implies start_at >= 1 Jan AND end_before <= 1 Feb
      return "$start_at_formula >= $value_low_formula AND $end_before_formula <= $value_high_formula";
    }
    elseif ($this->operator === 'is not within' || $this->operator === 'is not within range') {
      // Example: "is not within January" implies start_at >= 1 Feb OR end_before <= 1 Jan
      return "$start_at_formula >= $value_high_formula OR $end_before_formula <= $value_low_formula";
    }
    elseif ($this->operator === 'overlaps' || $this->operator === 'overlaps range') {
      // Example: "overlaps January" implies start_at < 1 Feb AND end_before > 1 Jan
      return "$start_at_formula < $value_high_formula AND $end_before_formula > $value_low_formula";
    }
    elseif ($this->operator === 'does not overlap' || $this->operator === 'does not overlap range') {
      // Example: "does not overlap January" implies start_at >= 1 Feb OR end_before <= 1 Jan
      return "$start_at_formula >= $value_high_formula OR $end_before_formula <= $value_low_formula";
    }
    else {
      \Drupal::messenger()->addWarning("\"{$this->operator}\" opperator is not yet handled.");
      return '';
    }
  }


  /**
   * A string representation of the 'start at' timestamp for the value.
   *
   * @param string $property_name
   *   One of 'value', 'min' or 'max'.
   *
   * @return string
   *   ISO-8601 format. See GregorianCalendar::toTimestamp() for details.
   */
  function getStartAtTimestampForValue($property_name = 'value') {
    $value = $this->value[$property_name] ?: '';
    if (empty($value)) {
      // TODO: what should we do here?
      throw new \RuntimeException("No $property_name value was set.");
    }

    $components = GregorianCalendar::componentsFromString($value);
    return GregorianCalendar::toTimestamp($components);
  }

  /**
   * A string representation of the 'end before' timestamp for the value.
   *
   * @param string $property_name
   *   One of 'value', 'min' or 'max'.
   *
   * @return string
   *   ISO-8601 format. See GregorianCalendar::toTimestamp() for details.
   */
  function getEndBeforeTimestampForValue($property_name = 'value') {
    $value = $this->value[$property_name] ?: '';
    if (empty($value)) {
      // TODO: what should we do here?
      throw new \RuntimeException("No $property_name value was set.");
    }

    $components = GregorianCalendar::componentsFromString($value);
    $components = GregorianCalendar::incrementSmallestComponent($components);
    return GregorianCalendar::toTimestamp($components);
  }

}
