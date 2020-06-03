<?php

namespace Drupal\vptime\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Ensure 'vptime' values have an acceptable precision.
 *
 * @Constraint(
 *   id = "vptime_timezone",
 *   label = @Translation("Timezone valid for variable precision date type.", context = "Validation"),
 *   type = {"vptime"}
 * )
 */
class TimeZoneConstraint extends Constraint {

  /**
   * Message for when the value has a timezone but shouldn't.
   *
   * @var string
   */
  public $forbidden = "A date-only value must not specify a timezone.";

}
