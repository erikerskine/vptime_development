<?php

namespace Drupal\vptime\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\vptime\GregorianCalendar;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the level of precision of 'vptime' values.
 */
class PrecisionConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static();
  }

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    /** @var \Drupal\vptime\Plugin\Validation\Constraint\PrecisionConstraint $constraint */
    try {
      $components = GregorianCalendar::componentsFromString($value);
      if (count($components) < $constraint->low + 1) {
        $this->context->addViolation($constraint->tooLowMessage, ['@precision' => $constraint->low]);
      }
      else if (count($components) > $constraint->high + 1) {
        $this->context->addViolation($constraint->tooHighMessage, ['@precision' => $constraint->high]);
      }
    }
    catch (\InvalidArgumentException $e) {
      // We didn't manage to establish the precision, probably because $value
      // is in the wrong format. Let another constraint validator handle that.
    }
  }

}
