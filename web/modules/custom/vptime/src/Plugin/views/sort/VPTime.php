<?php

namespace Drupal\vptime\Plugin\views\sort;

use Drupal\views\Plugin\views\sort\SortPluginBase;

/**
 * Views sort handler for vptime values.
 *
 * This handler sorts values such that lower precision values always come
 * before higher precision values, for example:
 *
 * In ascending order:
 *   - 2020
 *   - January 2020
 *   - 1st January 2020
 *   - 2nd January 2020
 *   - February 2020
 *
 * In descending order:
 *   - 2020
 *   - December 2020
 *   - 31 December 2020
 *   - 30 December 2020
 *   - November 2020
 *
 * If you don't want this behaviour, sort by 'start_at' or 'end_before'
 * rather than 'value'.
 *
 * @ViewsSort("vptime")
 */
class VPTime extends SortPluginBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();

    $timezone_field = $this->definition['field_name'] . '_timezone';
    $default_timezone = date_default_timezone_get();

    $start_at_field = $this->definition['field_name'] . '_start_at';
    $local_start_at_formula = "$start_at_field AT TIME ZONE COALESCE(NULLIF($timezone_field, ''), '$default_timezone')";
    $local_start_at_alias = "{$start_at_field}_local";

    $end_before_field = $this->definition['field_name'] . '_end_before';
    $local_end_before_formula = "$end_before_field AT TIME ZONE COALESCE(NULLIF($timezone_field, ''), '$default_timezone')";
    $local_end_before_alias = "{$end_before_field}_local";

    if ($this->options['order'] == 'ASC') {
      // First, sort by the start_at column. This is more or less the same
      // as sorting by value, but is a timestamp rather than a string.
//      $this->query->addOrderBy($this->tableAlias, $start_at_field, 'ASC');   // non timezone version
      $this->query->addOrderBy(NULL, $local_start_at_formula, 'ASC', $local_start_at_alias);

      // If two start_at timestamps are identical, the row with the highest
      // end_before timestamp will have the lowest precision. We always put
      // lower precision rows first.
//      $this->query->addOrderBy($this->tableAlias, $end_before_field, 'DESC');
      $this->query->addOrderBy(NULL, $local_end_before_formula, 'DESC', $local_end_before_alias);
    }
    else {
      // For a descending order, we reverse the steps above.
//      $this->query->addOrderBy($this->tableAlias, $end_before_field, 'DESC');
//      $this->query->addOrderBy($this->tableAlias, $start_at_field, 'ASC');
      $this->query->addOrderBy(NULL, $local_end_before_formula, 'DESC', $local_end_before_alias);
      $this->query->addOrderBy(NULL, $local_start_at_formula, 'ASC', $local_start_at_alias);
    }
  }

}
