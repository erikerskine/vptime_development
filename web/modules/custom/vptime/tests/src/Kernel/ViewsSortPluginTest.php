<?php

namespace Drupal\Tests\vptime\Kernel;

use Drupal\Tests\views\Kernel\ViewsKernelTestBase;
use Drupal\views\Tests\ViewTestData;
use Drupal\views\Views;
use Drupal\vptime_test_data\ExampleContentForSortPluginTest;

/**
 * Tests the functionality of the 'vptime' views sort plugin.
 */
class ViewsSortPluginTest extends ViewsKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $testViews = [
    'sort_plugin_test',
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
    ExampleContentForSortPluginTest::create();
  }


  /**
   * Test the ascending sort order.
   *
   * Make sure the order returned respects the following:
   *   1. Event with times happen in this order: Auckland, Sydney, London.
   *   2. New Year's Day appears before "Celebrations in Sydney" because it
   *        it is a longer period.
   *   3. "Celebrations in Sydney" and "Local celebrations" have the same
   *        local time and so are sorted alphabetically.
   */
  function testAscending() {
    $view = Views::getView('sort_plugin_test');
    $this->assertNotNull($view, 'Ensuring the test view is installed.');
    $this->executeView($view);
    $this->assertCount(5, $view->result, "Ensuring the correct number of events are listed.");

    $titles = array_map(function ($row) {
      return $row->_entity->label();
    }, $view->result);

    $this->assertEquals([
      "Celebrations in Auckland",
      "New Year's Day",
      "Celebrations in Sydney",
      "Local celebrations",
      "Celebrations in London",
    ], $titles, "Ensuring the events are in the correct order.");
  }


  /**
   * Test the descending sort order.
   *
   * The order should be similar to that of ascending order in reverse,
   * but with the following exceptions:
   *   1. New Year's Day appears before all events beginning on 1 Jan
   *        because it is a longer period.
   *   2. "Celebrations in Sydney" and "Local celebrations" have the same
   *        local time and so are sorted alphabetically.
   *
   * TODO: is this behaviour what we want?
   */
  function testDescending() {
    $view = Views::getView('sort_plugin_test');

    // Override the sort order defined in configuration.
    $sorts = $view->getDisplay()->getOption('sorts');
    $sorts['field_vptime_value']['order'] = 'DESC';
    $view->getDisplay()->setOption('sorts', $sorts);

    $this->assertNotNull($view, 'Ensuring the test view is installed.');
    $this->executeView($view);
    $this->assertCount(5, $view->result, "Ensuring the correct number of events are listed.");

    $titles = array_map(function ($row) {
      return $row->_entity->label();
    }, $view->result);

    $this->assertEquals([
      "New Year's Day",
      "Celebrations in London",
      "Celebrations in Sydney",
      "Local celebrations",
      "Celebrations in Auckland",
    ], $titles, "Ensuring the events are in the correct order.");
  }

}
