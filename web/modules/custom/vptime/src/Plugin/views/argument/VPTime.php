<?php

namespace Drupal\vptime\Plugin\views\argument;

use Drupal\views\Plugin\views\argument\ArgumentPluginBase;
use Drupal\vptime\GregorianCalendar;

/**
 * Views argument handler for vptime values.
 *
 * Accepts an ISO-8601 string and filters values contained entirely within
 * that period.
 *
 * @ViewsArgument("vptime")
 */
class VPTime extends ArgumentPluginBase {

  /**
   * Set up the query for this argument.
   *
   * The argument sent may be found at $this->argument.
   */
  public function query($group_by = FALSE) {
    $this->ensureMyTable();

    $components = GregorianCalendar::componentsFromString($this->argument);

    $default_timezone = date_default_timezone_get();
    $timezone_field = $this->definition['field_name'] . '_timezone';
    $timezone_formula = "COALESCE(NULLIF($timezone_field, ''), :timezone)";

    $start_at_value = GregorianCalendar::toTimestamp($components);
    $start_at_field = "{$this->tableAlias}.{$this->definition['field_name']}_start_at";
    $local_start_at_formula = "$start_at_field AT TIME ZONE $timezone_formula";
    $local_start_at_expression = "$local_start_at_formula >= :start_at::timestamp AT TIME ZONE :timezone";

    $end_before_value = GregorianCalendar::toTimestamp(GregorianCalendar::incrementSmallestComponent($components));
    $end_before_field = "{$this->tableAlias}.{$this->definition['field_name']}_end_before";
    $local_end_before_formula = "$end_before_field AT TIME ZONE $timezone_formula";
    $local_end_before_expression = "$local_end_before_formula <= :end_before::timestamp AT TIME ZONE :timezone";

    $this->query->addWhereExpression(0, $local_start_at_expression, [':start_at' => $start_at_value, ':timezone' => $default_timezone]);
    $this->query->addWhereExpression(0, $local_end_before_expression, [':end_before' => $end_before_value, ':timezone' => $default_timezone]);

    // Add a slightly wider set of WHERE clauses here that don't involve
    // adding "AT TIME ZONE" to calculate localised equivalents of
    // start_at/end_before.
    // By referring to start_at/end_before directly, we can make use of
    // indexes on those columns to work with a subset of rows and avoid a
    // potentially slower query.
    // The easternmost timezone is UTC+14, and westernmost timezone is UTC-12.
    // Therefore any localised time will never be more than 14 hours ahead of,
    // or 12 hours behind, the original timezone-less value.

    $start_at_buffer_expression = "$start_at_field >= :start_at::timestamp - 'PT14H'::interval";
    $this->query->addWhereExpression(0, $start_at_buffer_expression, [':start_at' => $start_at_value]);

    $end_before_buffer_expression = "$end_before_field <= :end_before::timestamp + 'PT12H'::interval";
    $this->query->addWhereExpression(0, $end_before_buffer_expression, [':end_before' => $end_before_value]);
  }

}
