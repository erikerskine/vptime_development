<?php

namespace Drupal\Tests\vptime\Kernel;

use Drupal\Tests\views\Kernel\ViewsKernelTestBase;
use Drupal\views\Tests\ViewTestData;
use Drupal\views\Views;
use Drupal\vptime_test_data\ExampleContentForArgumentPluginTest;

/**
 * Tests the functionality of the 'vptime' views argument plugin.
 */
class ViewsArgumentPluginTest extends ViewsKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $testViews = [
    'argument_plugin_test',
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
    ExampleContentForArgumentPluginTest::create();
  }


  /**
   * Test the argument plugin with a day-long value.
   *
   */
  function testDayArgument() {
    $view = Views::getView('argument_plugin_test');
    $this->assertNotNull($view, 'Ensuring the test view is installed.');
    $this->executeView($view, ['2020-06-02']);

    $titles = array_map(function ($row) {
      return $row->_entity->label();
    }, $view->result);

    $this->assertEquals([
      "Australia/Sydney 2020-06-02T00:00",
      "Australia/Sydney 2020-06-02T09:30",
      "Australia/Sydney 2020-06-02T23:59",
      "Europe/Berlin 2020-06-01T23:00",
      "Europe/Berlin 2020-06-02T00:00",
      "Europe/Berlin 2020-06-02T09:30",
      "LOCAL 2020-06-02",
      "LOCAL 2020-06-02T00:00",
      "LOCAL 2020-06-02T09:30",
      "LOCAL 2020-06-02T23:59",
      "Pacific/Auckland 2020-06-02T09:30",
      "Pacific/Auckland 2020-06-02T23:59",
      "Pacific/Auckland 2020-06-03T00:00",
    ], $titles, "Ensuring the events are correct.");
  }

}
