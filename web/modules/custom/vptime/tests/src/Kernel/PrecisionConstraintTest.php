<?php

namespace Drupal\Tests\vptime\Kernel;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;
use Drupal\Tests\vptime\Traits\GregorianCalendarTestDataTrait;
use Drupal\vptime\GregorianCalendar;


/**
 * Test the precision settings VPTimeItem fields.
 */
class PrecisionConstraintTest extends FieldKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['vptime'];

  /** @var EntityTest */
  protected $entity;

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('entity_test');

    FieldStorageConfig::create([
      'field_name' => 'field_vptime',
      'entity_type' => 'entity_test',
      'type' => 'vptime',
    ])->save();

    FieldConfig::create([
      'entity_type' => 'entity_test',
      'field_name' => 'field_vptime',
      'bundle' => 'entity_test',
      'settings' => [
        'precision_low' => GregorianCalendar::MONTH,
        'precision_high' => GregorianCalendar::MINUTE,
      ]
    ])->save();

    $this->entity = EntityTest::create();
  }

  /**
   * @dataProvider data
   */
  public function testPrecisionValidation($value, $expected_violation_count) {
    $this->entity->field_vptime->value = $value;
    $violations = $this->entity->validate();
    $this->assertEquals($expected_violation_count, count($violations));
  }


  // Provide a value along with the expected number of violations.
  public function data() {
    $data['year'] = ['2020', 1];
    $data['month'] = ['2020-05', 0];
    $data['day'] = ['2020-05-23', 0];
    $data['minute'] = ['2020-05-23T23:00', 0];
    $data['second'] = ['2020-05-23T23:00:12', 1];
    return $data;
  }

}
