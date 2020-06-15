<?php

namespace Drupal\vptime_test_data;

class ExampleContentForFilterPluginTests {

  const ROWS = [

    // Day long periods (timezone not applicable)
    ['1999-12-30'],
    ['1999-12-31'],
    ['2000-01-01'],
    ['2000-01-02'],
    ['2000-01-30'],
    ['2000-01-31'],
    ['2000-02-01'],
    ['2000-02-02'],

    // Local times

    // Before/after New Year
    ['1999-12-31', '23:58'],
    ['1999-12-31', '23:59'],
    ['2000-01-01', '00:00'],
    ['2000-01-01', '00:01'],
    // Before/after the end of Jan
    ['2000-01-31', '23:58'],
    ['2000-01-31', '23:59'],
    ['2000-02-01', '00:00'],
    ['2000-02-01', '00:01'],

    // Perth times (3 hours behind)

    // Before/after New Year in Sydney
    ['1999-12-31', '20:58', 'Australia/Perth'],
    ['1999-12-31', '20:59', 'Australia/Perth'],
    ['1999-12-31', '21:00', 'Australia/Perth'],
    ['1999-12-31', '21:01', 'Australia/Perth'],
    // Before/after the end of Jan in Sydney
    ['2000-01-31', '20:58', 'Australia/Perth'],
    ['2000-01-31', '20:59', 'Australia/Perth'],
    ['2000-01-31', '21:00', 'Australia/Perth'],
    ['2000-01-31', '21:01', 'Australia/Perth'],

    // Sydney times (correspond to current timezone when tests are run)

    // Before/after New Year
    ['1999-12-31', '23:58', 'Australia/Sydney'],
    ['1999-12-31', '23:59', 'Australia/Sydney'],
    ['2000-01-01', '00:00', 'Australia/Sydney'],
    ['2000-01-01', '00:01', 'Australia/Sydney'],
    // Before/after the end of Jan
    ['2000-01-31', '23:58', 'Australia/Sydney'],
    ['2000-01-31', '23:59', 'Australia/Sydney'],
    ['2000-02-01', '00:00', 'Australia/Sydney'],
    ['2000-02-01', '00:01', 'Australia/Sydney'],

    // Auckland times (2 hours ahead of Sydney)

    // Before/after New Year in Sydney
    ['2000-01-01', '01:58', 'Pacific/Auckland'],
    ['2000-01-01', '01:59', 'Pacific/Auckland'],
    ['2000-01-01', '02:00', 'Pacific/Auckland'],
    ['2000-01-01', '02:01', 'Pacific/Auckland'],
    // Before/after the end of Jan in Sydney
    ['2000-02-01', '01:58', 'Pacific/Auckland'],
    ['2000-02-01', '01:59', 'Pacific/Auckland'],
    ['2000-02-01', '02:00', 'Pacific/Auckland'],
    ['2000-02-01', '02:01', 'Pacific/Auckland'],

  ];

}
