<?php

namespace Drupal\Tests\vptime\Kernel;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;
use Drupal\Tests\vptime\Traits\GregorianCalendarTestDataTrait;
use Drupal\vptime\GregorianCalendar;


/**
 * Test the computed properties on VPTimeItem fields.
 */
class ComputedPropertiesTest extends FieldKernelTestBase {

  use GregorianCalendarTestDataTrait;

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

    // Create a 'vptime' field and storage for validation.
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
        'precision_low' => GregorianCalendar::YEAR,
        'precision_high' => GregorianCalendar::SECOND,
      ]
    ])->save();

    $this->entity = EntityTest::create();
  }


  /**
   * Make sure that 'start_at' and 'end_before' are calculated correctly.
   *
   * @dataProvider validData
   */
  public function testDerivedTimestamps($source, $expected) {
    $this->entity->field_vptime->value = $source;
    $this->assertEquals($expected['start_at'], $this->entity->field_vptime->start_at);
    $this->assertEquals($expected['end_before'], $this->entity->field_vptime->end_before);
  }

}
