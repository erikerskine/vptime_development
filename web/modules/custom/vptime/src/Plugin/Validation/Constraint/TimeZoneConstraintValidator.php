<?php

namespace Drupal\vptime\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\vptime\GregorianCalendar;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the time zone of 'vptime' values.
 */
class TimeZoneConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static();
  }

  /**
   * {@inheritdoc}
   */
  public function validate($item, Constraint $constraint) {
    /** @var \Drupal\vptime\Plugin\Validation\Constraint\TimeZoneConstraint $constraint */
    try {
      $components = GregorianCalendar::componentsFromString($item->value);
      $precision = count($components) - 1;
      if ($precision < GregorianCalendar::HOUR && $item->timezone) {
        $this->context->addViolation($constraint->forbidden);
      }
    }
    catch (\InvalidArgumentException $e) {
      // We didn't manage to establish the precision, probably because $value
      // is in the wrong format. Let another constraint validator handle that.
    }
  }

}
