<?php

namespace Drupal\vptime\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\NumericFilter;
use Drupal\vptime\GregorianCalendar;

/**
 * This filter allows for comparisons to be made between values of different
 * precision. The filter value can be thought of as a period with arbitary
 * duration. The field values being filtered have a derived "end",
 * which is an infinitessimaly small "instant".
 *
 * In order to compare these different kinds of value, the '=' operator can
 * be considered to mean 'within'.
 *
 * For example, the following statements are both true:
 *   - "the end of "15 Jun 2020" = "June 2020"
 *         (because the end of that day falls within the month of June 2020)
 *   - "the end of 2019" = "31 December 2019"
 *         (because the end of that year falls within that last day)
 *
 * The other comparison operators behave in the same way:
 * < (before), <= (before or during), > (after) and >= (during or after).
 *
 * @ViewsFilter("vptime_end")
 */

// TODO: VPTimeStart & VPTimeEnd are very similar - can/should they be refactored?

class VPTimeEnd extends NumericFilter {

  /**
   * {@inheritdoc}
   */
  public function operators() {
    $operators = parent::operators();
    unset($operators['regular_expression']);
    $operators['<']['title'] = $this->t('Ends before');
    $operators['<=']['title'] = $this->t('Ends before or during');
    $operators['=']['title'] = $this->t('Ends during');
    $operators['!=']['title'] = $this->t('Does not end during');
    $operators['>']['title'] = $this->t('Ends after');
    $operators['>=']['title'] = $this->t('Ends during or after');
    $operators['between']['title'] = $this->t('Ends between');
    $operators['not between']['title'] = $this->t('Does not end between');

    // Rearrange the numeric operators a bit.
    $ordered_operator_keys = ['<', '<=', '=', '!=', '>=', '>'];
    return array_replace(array_flip($ordered_operator_keys), $operators);
  }


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
    $value_low = $this->getFilterValueStartAtTimestamp();
    $value_high = $this->getFilterValueEndBeforeTimestamp();

    // Treat the two filter value timestamps as though they are in the
    // current timezone.
    $value_low_formula = ":value_low::timestamp AT TIME ZONE :default_timezone";
    $value_high_formula = ":value_high::timestamp AT TIME ZONE :default_timezone";

    // We aren't interested in the column value directly, rather converted
    // to an appropriate timezone. So rather than put '$field' in the query,
    // we always want '$field AT TIME ZONE ...' instead.
    $timezone_field = $this->definition['field_name'] . '_timezone';
    $timezone_formula = "COALESCE(NULLIF($timezone_field, ''), :default_timezone)";
    $formula = "$field AT TIME ZONE $timezone_formula";

    if ($this->operator === '<') {
      // Example: "period end is less than January 2020" implies end_before <= 1 Jan 2020
      $expression = "$formula <= $value_low_formula";
    }
    elseif ($this->operator === '<=') {
      // Example: "period end is less than or equal to January 2020" implies end_before <= 1 Feb 2020
      $expression = "$formula <= $value_high_formula";
    }
    elseif ($this->operator === '>') {
      // Example: "period end is greater than January 2020" implies end_before > 1 Feb 2020
      $expression = "$formula > $value_high_formula";
    }
    elseif ($this->operator === '>=') {
      // Example: "period end is greater than or equal to January 2020" implies end_before > 1 Jan
      $expression = "$formula > $value_low_formula";
    }
    elseif ($this->operator === '=') {
      // We consider "period end = January 2020" to mean period end is sometime in January.
      // Example: "period end is January 2020" implies end_before > 1 Jan AND end_before <= 1 Feb
      $expression = "$formula > $value_low_formula AND $formula <= $value_high_formula";
    }
    elseif ($this->operator === '!=') {
      // We consider "period end != January 2020" to mean period does not end sometime in January.
      // Example: "period end is not January 2020" implies end_before <= 1 Jan OR end_before > 1 Feb
      $expression = "$formula <= $value_low_formula OR $formula > $value_high_formula";
    }
    else {
      throw new \RuntimeException("Operator {$this->operator} not implemented.");
    }

    if ($expression) {
      $this->query->addWhereExpression($this->options['group'], $expression, [
        ':default_timezone' => date_default_timezone_get(),
        ':value_low' => $value_low,
        ':value_high' => $value_high,
      ]);
    }
  }


  /**
   * Filters by operators that take two values (between/not between).
   *
   * @param object $field
   *   The views field.
   */
  protected function opBetween($field) {

    // Both min and max filter values are ranges, dependent on precision,
    // eg 'January 2020' to 'May 2020'.
    // The corresponding WHERE clauses need to be constructed using the two
    // timestamps at either end of those ranges.
    $value_low = $this->getFilterValueStartAtTimestamp();
    $value_high = $this->getFilterValueEndBeforeTimestamp();

    // Treat the two filter value timestamps as though they are in the
    // current timezone.
    $value_low_formula = ":value_low::timestamp AT TIME ZONE :default_timezone";
    $value_high_formula = ":value_high::timestamp AT TIME ZONE :default_timezone";

    // We aren't interested in the column value directly, rather converted
    // to an appropriate timezone. So rather than put '$field' in the query,
    // we always want '$field AT TIME ZONE ...' instead.
    $default_timezone = date_default_timezone_get();
    $timezone_field = $this->definition['field_name'] . '_timezone';
    $timezone_formula = "COALESCE(NULLIF($timezone_field, ''), :default_timezone)";
    $formula = "$field AT TIME ZONE $timezone_formula";

    $expression = '';
    if ($this->operator === 'between') {
      // Example: ENDS BETWEEN January 2020 & May 2020 implies end_before > 1 Jan 2020 AND end_before <= 1 Jun 2020
      $expression = "$formula > $value_low_formula AND $formula <= $value_high_formula";
    }
    else if ($this->operator == 'not between') {
      // Example: DOES NOT END BETWEEN January 2020 & May 2020 implies end_before <= 1 Jan 2020 OR end_before > 1 Jun 2020
      $expression = "$formula <= $value_low_formula OR $formula > $value_high_formula";
    }
    else {
      throw new \RuntimeException("Operator {$this->operator} not implemented.");
    }

    if ($expression) {
      $this->query->addWhereExpression($this->options['group'], $expression, [
        ':default_timezone' => date_default_timezone_get(),
        ':value_low' => $value_low,
        ':value_high' => $value_high,
      ]);
    }
  }


  /**
   * The timestamp at the start of the filter value or range of values.
   *
   * Examples:
   *   if value is January 2020, returns 2020-01-01T00:00
   *   if min is March 2020 and max is April 2020, returns 2020-03-01T00:00
   *
   * @return string
   *   ISO-8601 format, without timezone.
   *   See GregorianCalendar::toTimestamp() for details.
   */
  function getFilterValueStartAtTimestamp() {
    $number_of_values = $this->operators()[$this->operator]['values'];
    $property_name = ($number_of_values == 2) ? 'min' : 'value';
    $value = $this->value[$property_name];
    if (empty($value)) {
      // TODO: what should we do here?
      throw new \RuntimeException("No $property_name value was set.");
    }

    $components = GregorianCalendar::componentsFromString($value);
    return GregorianCalendar::toTimestamp($components);
  }

  /**
   * The timestamp immediately after the filter value or range of values.
   *
   * Examples:
   *   if value is January 2020, returns 2020-02-01T00:00
   *   if min is March 2020 and max is April 2020, returns 2020-05-01T00:00
   *
   * @return string
   *   ISO-8601 format, without timezone.
   *   See GregorianCalendar::toTimestamp() for details.
   */
  function getFilterValueEndBeforeTimestamp() {
    $number_of_values = $this->operators()[$this->operator]['values'];
    $property_name = ($number_of_values == 2) ? 'max' : 'value';
    $value = $this->value[$property_name];
    if (empty($value)) {
      // TODO: what should we do here?
      throw new \RuntimeException("No $property_name value was set.");
    }

    $components = GregorianCalendar::componentsFromString($value);
    $components = GregorianCalendar::incrementSmallestComponent($components);
    return GregorianCalendar::toTimestamp($components);
  }

}
