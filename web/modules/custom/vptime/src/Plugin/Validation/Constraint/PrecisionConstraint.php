<?php

namespace Drupal\vptime\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Ensure 'vptime' values have an acceptable precision.
 *
 * @Constraint(
 *   id = "vptime_precision",
 *   label = @Translation("Precision valid for variable precision date type.", context = "Validation"),
 *   type = {"vptime"}
 * )
 */
class PrecisionConstraint extends Constraint {

  /** @var int */
  public $low;

  /** @var int */
  public $high;

  /**
   * Message for when the value's precision is too low.
   *
   * @var string
   */
  public $tooLowMessage = "The value must have a precision of at least @precision.";

  /**
   * Message for when the value's precision is too high.
   *
   * @var string
   */
  public $tooHighMessage = "The value must not have a precision higher than @precision.";

}
