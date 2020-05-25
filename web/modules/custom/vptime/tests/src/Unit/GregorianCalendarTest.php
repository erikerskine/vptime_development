<?php

namespace Drupal\Tests\vptime\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\Tests\vptime\Traits\GregorianCalendarTestDataTrait;
use Drupal\vptime\GregorianCalendar;

/**
 * Unit tests for utility functions in GregorianCalendar.
 */
class GregorianCalendarTest extends UnitTestCase {

  use GregorianCalendarTestDataTrait;

  /**
   * @dataProvider validData
   */
  public function testComponents($source, $expected) {
    $actual = GregorianCalendar::componentsFromString($source);
    $this->assertArrayEquals($expected['components'], $actual);
  }

  /**
   * @dataProvider validData
   */
  public function testIncremented($source, $expected) {
    $components = GregorianCalendar::componentsFromString($source);
    $actual = GregorianCalendar::incrementSmallestComponent($components);
    $this->assertArrayEquals($expected['incremented'], $actual);
  }

  /**
   * Make sure invalid input throws an exception.
   *
   * @dataProvider invalidData
   */
  public function testInvalidComponents($source) {
    $this->expectException(\InvalidArgumentException::class);
    GregorianCalendar::componentsFromString($source);
  }

}
