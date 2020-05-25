<?php

namespace Drupal\vptime\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Ensure 'vptime' values are correctly formed.
 *
 * @Constraint(
 *   id = "vptime_syntax",
 *   label = @Translation("Syntax valid for variable precision date type.", context = "Validation"),
 *   type = {"vptime"}
 * )
 */
class SyntaxConstraint extends Constraint {

  /**
   * Message for when the value could not be parsed.
   *
   * @var string
   */
  public $invalidMessage = "The value could not be interpreted as a date/time.";

}
