<?php

namespace Drupal\Tests\vptime\Kernel;

use Drupal\Tests\views\Kernel\ViewsKernelTestBase;
use Drupal\views\Tests\ViewTestData;
use Drupal\views\Views;
use Drupal\vptime_test_data\ExampleContent;
use Drupal\vptime_test_data\ExampleContentForArgumentPluginTest;
use Drupal\vptime_test_data\ExampleContentForFilterPluginTests;

/**
 * Tests the functionality of the vptime start/end filter plugins.
 */
class ViewsStartEndFilterPluginTest extends ViewsKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $testViews = [
    'test_view',
  ];

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'field',
    'text',
    'user',
    'node',
    'vptime',
    'vptime_test_data',
  ];

  /**
   * @throws \Exception
   */
  protected function setUp($import_test_views = TRUE) {
    parent::setUp(FALSE);

    ViewTestData::createTestViews(get_class($this), ['vptime_test_data']);

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');

    $this->installConfig('system');
    $this->installConfig('field');
    $this->installConfig('text');
    $this->installConfig('user');
    $this->installConfig('node');
    $this->installConfig('vptime_test_data');

    // Create some test data.
    // Note that when tests are run the "current" timezone is Australia/Sydney,
    // therefore any local times (stored without a timezone) are interpreted
    // as being in that zone.
    foreach (ExampleContentForFilterPluginTests::ROWS as $row) {
      ExampleContent::createTestDataNodeFromRow($row);
    }
  }


  /**
   * Test the filter plugin.
   *
   * @param array $filter_definition
   *   A views filter definition.
   * @param string[] $expected_titles
   *   A list of expected node titles, in alphabetical order.
   *
   * @dataProvider data
   */
  function testFilter($filter_definition, $expected_titles) {

    // Get the basic view and override it, adding the appropriate filter.
    $view = Views::getView('test_view');
    $view->getDisplay()->setOption('filters', [$filter_definition]);
    $this->executeView($view);

    $actual_titles = array_map(function ($row) {
      return $row->_entity->label();
    }, $view->result);

    sort($actual_titles);
    sort($expected_titles);
    $this->assertEquals($expected_titles, $actual_titles);
  }


  function data() {

    // Test filtering by start, using a low precision filter value (month).

    $tests['start = January'] = [
      'field' => 'field_vptime_start',
      'operator' => '=',
      'value' => '2000-01',
      'expected' => [
        '2000-01-01',
        '2000-01-02',
        '2000-01-30',
        '2000-01-31',
        '2000-01-01 00:00',
        '2000-01-01 00:01',
        '2000-01-31 23:58',
        '2000-01-31 23:59',
        '1999-12-31 21:00 Perth',
        '1999-12-31 21:01 Perth',
        '2000-01-31 20:58 Perth',
        '2000-01-31 20:59 Perth',
        '2000-01-01 00:00 Sydney',
        '2000-01-01 00:01 Sydney',
        '2000-01-31 23:58 Sydney',
        '2000-01-31 23:59 Sydney',
        '2000-02-01 01:58 Auckland',
        '2000-02-01 01:59 Auckland',
        '2000-01-01 02:00 Auckland',
        '2000-01-01 02:01 Auckland',
      ],
    ];

    $tests['start < January'] = [
      'field' => 'field_vptime_start',
      'operator' => '<',
      'value' => '2000-01',
      'expected' => [
        '1999-12-30',
        '1999-12-31',
        '1999-12-31 23:58',
        '1999-12-31 23:59',
        '1999-12-31 20:58 Perth',
        '1999-12-31 20:59 Perth',
        '1999-12-31 23:58 Sydney',
        '1999-12-31 23:59 Sydney',
        '2000-01-01 01:58 Auckland',
        '2000-01-01 01:59 Auckland',
      ],
    ];

    $tests['start > January'] = [
      'field' => 'field_vptime_start',
      'operator' => '>',
      'value' => '2000-01',
      'expected' => [
        '2000-02-01',
        '2000-02-02',
        '2000-02-01 00:00',
        '2000-02-01 00:01',
        '2000-01-31 21:00 Perth',
        '2000-01-31 21:01 Perth',
        '2000-02-01 00:00 Sydney',
        '2000-02-01 00:01 Sydney',
        '2000-02-01 02:00 Auckland',
        '2000-02-01 02:01 Auckland',
      ],
    ];

    $tests['start <= January'] = [
      'field' => 'field_vptime_start',
      'operator' => '<=',
      'value' => '2000-01',
      'expected' => array_merge(
        $tests['start = January']['expected'],
        $tests['start < January']['expected']
      ),
    ];

    $tests['start >= January'] = [
      'field' => 'field_vptime_start',
      'operator' => '>=',
      'value' => '2000-01',
      'expected' => array_merge(
        $tests['start = January']['expected'],
        $tests['start > January']['expected']
      ),
    ];

    $tests['start != January'] = [
      'field' => 'field_vptime_start',
      'operator' => '!=',
      'value' => '2000-01',
      'expected' => array_merge(
        $tests['start < January']['expected'],
        $tests['start > January']['expected']
      ),
    ];

    // Test filtering by end, using a low precision filter value (month).
    // Because all the rows in the test data are shorter than one month, the
    // expected results should be the same as filtering by start date.
    // Testing the end date filtering is here for completeness:

    $tests['end = January'] = [
      'field' => 'field_vptime_end',
      'operator' => '=',
      'value' => '2000-01',
      'expected' => $tests['start = January']['expected'],
    ];

    $tests['end < January'] = [
      'field' => 'field_vptime_end',
      'operator' => '<',
      'value' => '2000-01',
      'expected' => $tests['start < January']['expected'],
    ];

    $tests['end > January'] = [
      'field' => 'field_vptime_end',
      'operator' => '>',
      'value' => '2000-01',
      'expected' => $tests['start > January']['expected'],
    ];

    $tests['end <= January'] = [
      'field' => 'field_vptime_end',
      'operator' => '<=',
      'value' => '2000-01',
      'expected' => array_merge(
        $tests['end = January']['expected'],
        $tests['end < January']['expected']
      ),
    ];

    $tests['end >= January'] = [
      'field' => 'field_vptime_end',
      'operator' => '>=',
      'value' => '2000-01',
      'expected' => array_merge(
        $tests['end = January']['expected'],
        $tests['end > January']['expected']
      ),
    ];

    $tests['end != January'] = [
      'field' => 'field_vptime_end',
      'operator' => '!=',
      'value' => '2000-01',
      'expected' => array_merge(
        $tests['start < January']['expected'],
        $tests['start > January']['expected']
      ),
    ];

    // Test filtering by start, using a high precision filter value (second).

    $tests['start = second'] = [
      'field' => 'field_vptime_start',
      'operator' => '=',
      'value' => '2000-01-01T00:00:00',
      'expected' => [
        '2000-01-01',
        '2000-01-01 00:00',
        '1999-12-31 21:00 Perth',
        '2000-01-01 00:00 Sydney',
        '2000-01-01 02:00 Auckland',
      ],
    ];

    $tests['start < second'] = [
      'field' => 'field_vptime_start',
      'operator' => '<',
      'value' => '2000-01-01T00:00:00',
      'expected' => [
        '1999-12-30',
        '1999-12-31',
        '1999-12-31 23:58',
        '1999-12-31 23:59',
        '1999-12-31 20:58 Perth',
        '1999-12-31 20:59 Perth',
        '1999-12-31 23:58 Sydney',
        '1999-12-31 23:59 Sydney',
        '2000-01-01 01:58 Auckland',
        '2000-01-01 01:59 Auckland',
      ],
    ];

    $tests['start > second'] = [
      'field' => 'field_vptime_start',
      'operator' => '>',
      'value' => '2000-01-01T00:00:00',
      'expected' => [
        '2000-01-02',
        '2000-01-30',
        '2000-01-31',
        '2000-02-01',
        '2000-02-02',
        '2000-01-01 00:01',
        '2000-01-31 23:58',
        '2000-01-31 23:59',
        '2000-02-01 00:00',
        '2000-02-01 00:01',
        '1999-12-31 21:01 Perth',
        '2000-01-31 20:58 Perth',
        '2000-01-31 20:59 Perth',
        '2000-01-31 21:00 Perth',
        '2000-01-31 21:01 Perth',
        '2000-01-01 00:01 Sydney',
        '2000-01-31 23:58 Sydney',
        '2000-01-31 23:59 Sydney',
        '2000-02-01 00:00 Sydney',
        '2000-02-01 00:01 Sydney',
        '2000-01-01 02:01 Auckland',
        '2000-02-01 01:58 Auckland',
        '2000-02-01 01:59 Auckland',
        '2000-02-01 02:00 Auckland',
        '2000-02-01 02:01 Auckland',
      ],
    ];

    $tests['start <= second'] = [
      'field' => 'field_vptime_start',
      'operator' => '<=',
      'value' => '2000-01-01T00:00:00',
      'expected' => array_merge(
        $tests['start = second']['expected'],
        $tests['start < second']['expected']
      ),
    ];

    $tests['start >= second'] = [
      'field' => 'field_vptime_start',
      'operator' => '>=',
      'value' => '2000-01-01T00:00:00',
      'expected' => array_merge(
        $tests['start = second']['expected'],
        $tests['start > second']['expected']
      ),
    ];

    $tests['start != second'] = [
      'field' => 'field_vptime_start',
      'operator' => '!=',
      'value' => '2000-01-01T00:00:00',
      'expected' => array_merge(
        $tests['start < second']['expected'],
        $tests['start > second']['expected']
      ),
    ];

    // Test filtering by end, using a high precision filter value (second).

    $tests['end = second'] = [
      'field' => 'field_vptime_end',
      'operator' => '=',
      'value' => '1999-12-31T23:59:59',
      'expected' => [
        '1999-12-31',
        '1999-12-31 23:59',
        '1999-12-31 20:59 Perth',
        '1999-12-31 23:59 Sydney',
        '2000-01-01 01:59 Auckland',
      ],
    ];

    $tests['end < second'] = [
      'field' => 'field_vptime_end',
      'operator' => '<',
      'value' => '1999-12-31T23:59:59',
      'expected' => [
        '1999-12-30',
        '1999-12-31 23:58',
        '1999-12-31 20:58 Perth',
        '1999-12-31 23:58 Sydney',
        '2000-01-01 01:58 Auckland',
      ],
    ];

    $tests['end > second'] = [
      'field' => 'field_vptime_end',
      'operator' => '>',
      'value' => '1999-12-31T23:59:59',
      'expected' => [
        '2000-01-01',
        '2000-01-02',
        '2000-01-30',
        '2000-01-31',
        '2000-02-01',
        '2000-02-02',
        '2000-01-01 00:00',
        '2000-01-01 00:01',
        '2000-01-31 23:58',
        '2000-01-31 23:59',
        '2000-02-01 00:00',
        '2000-02-01 00:01',
        '1999-12-31 21:00 Perth',
        '1999-12-31 21:01 Perth',
        '2000-01-31 20:58 Perth',
        '2000-01-31 20:59 Perth',
        '2000-01-31 21:00 Perth',
        '2000-01-31 21:01 Perth',
        '2000-01-01 00:00 Sydney',
        '2000-01-01 00:01 Sydney',
        '2000-01-31 23:58 Sydney',
        '2000-01-31 23:59 Sydney',
        '2000-02-01 00:00 Sydney',
        '2000-02-01 00:01 Sydney',
        '2000-01-01 02:00 Auckland',
        '2000-01-01 02:01 Auckland',
        '2000-02-01 01:58 Auckland',
        '2000-02-01 01:59 Auckland',
        '2000-02-01 02:00 Auckland',
        '2000-02-01 02:01 Auckland',
      ],
    ];

    $tests['end <= second'] = [
      'field' => 'field_vptime_end',
      'operator' => '<=',
      'value' => '1999-12-31T23:59:59',
      'expected' => array_merge(
        $tests['end = second']['expected'],
        $tests['end < second']['expected']
      ),
    ];

    $tests['end >= second'] = [
      'field' => 'field_vptime_end',
      'operator' => '>=',
      'value' => '1999-12-31T23:59:59',
      'expected' => array_merge(
        $tests['end = second']['expected'],
        $tests['end > second']['expected']
      ),
    ];

    $tests['end != second'] = [
      'field' => 'field_vptime_end',
      'operator' => '!=',
      'value' => '1999-12-31T23:59:59',
      'expected' => array_merge(
        $tests['end < second']['expected'],
        $tests['end > second']['expected']
      ),
    ];



    // @dataProvider expects array of arrays, each with two elements
    $return = [];
    foreach ($tests as $name => $data) {
      // first argument to test function: views filter definition
      $return[$name][] = [
        'id' => $data['field'],
        'table' => 'node__field_vptime',
        'field' => $data['field'],
        'operator' => $data['operator'],
        'value' => [
          'min' => $data['min'] ?? '',
          'max' => $data['max'] ?? '',
          'value' => $data['value'] ?? '',
        ],
      ];
      // second argument to test function: expected titles
      $return[$name][] = $data['expected'];
    }
    return $return;
  }

}
