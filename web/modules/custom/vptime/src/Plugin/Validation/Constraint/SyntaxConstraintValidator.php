<?php

namespace Drupal\vptime\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\vptime\GregorianCalendar;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the syntax of 'vptime' values.
 */
class SyntaxConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

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
    /** @var \Drupal\vptime\Plugin\Validation\Constraint\SyntaxConstraint $constraint */
    try {
      GregorianCalendar::componentsFromString($value);
    }
    catch (\InvalidArgumentException $e) {
      $this->context->addViolation($constraint->invalidMessage);
    }
  }

}
