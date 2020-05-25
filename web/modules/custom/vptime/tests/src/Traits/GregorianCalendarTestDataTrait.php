<?php

namespace Drupal\Tests\vptime\Traits;


trait GregorianCalendarTestDataTrait {

  function validData() {
    $data['year'][] = '2020';
    $data['year'][] = [
      'components' => [2020],
      'incremented' => [2021],
      'start_at' => '2020-01-01',
      'end_before' => '2021-01-01',
    ];
    $data['month'][] = '2020-05';
    $data['month'][] = [
      'components' => [2020, 5],
      'incremented' => [2020, 6],
      'start_at' => '2020-05-01',
      'end_before' => '2020-06-01',
    ];
    $data['day'][] = '2020-05-16';
    $data['day'][] = [
      'components' => [2020, 5, 16],
      'incremented' => [2020, 5, 17],
      'start_at' => '2020-05-16',
      'end_before' => '2020-05-17',
    ];
    $data['minute'][] = '2020-05-16T13:47';
    $data['minute'][] = [
      'components' => [2020, 5, 16, 13, 47],
      'incremented' => [2020, 5, 16, 13, 48],
      'start_at' => '2020-05-16T13:47',
      'end_before' => '2020-05-16T13:48',
    ];
    $data['second'][] = '2020-05-16T13:47:58';
    $data['second'][] = [
      'components' => [2020, 5, 16, 13, 47, 58],
      'incremented' => [2020, 5, 16, 13, 47, 59],
      'start_at' => '2020-05-16T13:47:58',
      'end_before' => '2020-05-16T13:47:59',
    ];
    $data['hundredths'][] = '2020-05-16T17:27:22.01';
    $data['hundredths'][] = [
      'components' => [2020, 5, 16, 17, 27, 22, 0, 1],
      'incremented' => [2020, 5, 16, 17, 27, 22, 0, 2],
      'start_at' => '2020-05-16T17:27:22.01',
      'end_before' => '2020-05-16T17:27:22.02',
    ];
    $data['millisecond'][] = '2020-05-16T17:27:22.123';
    $data['millisecond'][] = [
      'components' => [2020, 5, 16, 17, 27, 22, 1, 2, 3],
      'incremented' => [2020, 5, 16, 17, 27, 22, 1, 2, 4],
      'start_at' => '2020-05-16T17:27:22.123',
      'end_before' => '2020-05-16T17:27:22.124',
    ];
    $data['microsecond'][] = '2020-05-16T17:27:22.123456';
    $data['microsecond'][] = [
      'components' => [2020, 5, 16, 17, 27, 22, 1, 2, 3, 4, 5, 6],
      'incremented' => [2020, 5, 16, 17, 27, 22, 1, 2, 3, 4, 5, 7],
      'start_at' => '2020-05-16T17:27:22.123456',
      'end_before' => '2020-05-16T17:27:22.123457',
    ];

    // Periods where the end_before timestamp rolls over:
    $data['december'][] = '2020-12';
    $data['december'][] = [
      'components' => [2020, 12],
      'incremented' => [2021, 1],
      'start_at' => '2020-12-01',
      'end_before' => '2021-01-01',
    ];
    $data['last_day_of_jan_2020'][] = '2020-01-31';
    $data['last_day_of_jan_2020'][] = [
      'components' => [2020, 1, 31],
      'incremented' => [2020, 2, 1],
      'start_at' => '2020-01-31',
      'end_before' => '2020-02-01',
    ];
    $data['last_day_of_year'][] = '2020-12-31';
    $data['last_day_of_year'][] = [
      'components' => [2020, 12, 31],
      'incremented' => [2021, 1, 1],
      'start_at' => '2020-12-31',
      'end_before' => '2021-01-01',
    ];
    $data['last_minute_of_day'][] = '2020-05-16T23:59';
    $data['last_minute_of_day'][] = [
      'components' => [2020, 5, 16, 23, 59],
      'incremented' => [2020, 5, 17, 0, 0],
      'start_at' => '2020-05-16T23:59',
      'end_before' => '2020-05-17T00:00',
    ];
    $data['last_minute_of_year'][] = '2020-12-31T23:59';
    $data['last_minute_of_year'][] = [
      'components' => [2020, 12, 31, 23, 59],
      'incremented' => [2021, 1, 1, 0, 0],
      'start_at' => '2020-12-31T23:59',
      'end_before' => '2021-01-01T00:00',
    ];
    $data['last_minute_of_28th_feb_2019'][] = '2019-02-28T23:59';
    $data['last_minute_of_28th_feb_2019'][] = [
      'components' => [2019, 2, 28, 23, 59],
      'incremented' => [2019, 3, 1, 0, 0],
      'start_at' => '2019-02-28T23:59',
      'end_before' => '2019-03-01T00:00',
    ];
    $data['last_minute_of_28th_feb_2020'][] = '2020-02-28T23:59';
    $data['last_minute_of_28th_feb_2020'][] = [
      'components' => [2020, 2, 28, 23, 59],
      'incremented' => [2020, 2, 29, 0, 0],
      'start_at' => '2020-02-28T23:59',
      'end_before' => '2020-02-29T00:00',
    ];
    $data['last_second_of_month'][] = '2020-04-30T23:59:59';
    $data['last_second_of_month'][] = [
      'components' => [2020, 4, 30, 23, 59, 59],
      'incremented' => [2020, 5, 1, 0, 0, 0],
      'start_at' => '2020-04-30T23:59:59',
      'end_before' => '2020-05-01T00:00:00',
      'duration' => 'PT1S',
    ];
    $data['last_millisecond'][] = '2020-05-16T23:37:00.999';
    $data['last_millisecond'][] = [
      'components' => [2020, 5, 16, 23, 37, 0, 9, 9, 9],
      'incremented' => [2020, 5, 16, 23, 37, 1, 0, 0, 0],
      'start_at' => '2020-05-16T23:37:00.999',
      'end_before' => '2020-05-16T23:37:01.000',
    ];
    // Because we don't know about leap seconds, we assume that 23:59:59 is
    // always be one second before midnight. That's ok for practical purposes,
    // as the OS/database isn't likely to know anything else.
    $data['second_before_leap_second'][] = '2015-06-30T23:59:59';
    $data['second_before_leap_second'][] = [
      'components' => [2015, 6, 30, 23, 59, 59],
      'incremented' => [2015, 7, 1, 0, 0, 0],
      'start_at' => '2015-06-30T23:59:59',
      'end_before' => '2015-07-01T00:00:00',
    ];

    return $data;
  }

  function invalidData() {
    return [
      // Century/decade granularity are not supported because they can't be
      // represented as a string in ISO-8601.
      'century' => ['20'],
      'decade' => ['198'],
      // Hour granularity is *not* supported. Unlike other time periods, an
      // hour written on its own is normally understood to mean a single time,
      // eg: "9am" means "9:00am", not "9-10am". This also handily avoids
      // needing any knowledge of DST, where can be missed or repeated.
      'hour' => ['2020-05-16T09'],
      // Gaps are not supported.
      'missing_day' => ['2020-05T09:30'],
      'missing_hour_minute' => ['2020-05-25T00.123'],
      // Leap seconds are not supported (although 30 June 2015 really had one).
      // We don't have enough information about the calendar to handle them,
      // and can't guarantee the OS/database know about them either.
      'leap_second' => ['2015-06-30T23:59:60'],
      // We don't allow values that are out of range.
      'bad_month' => ['2020-13-01'],
      'bad_day' => ['2020-04-31'],
      'bad_leap_day' => ['2019-02-29'],
      'bad_hour' => ['2020-01-01T24:15'],
      'bad_minute' => ['2020-01-01T00:75'],
    ];
  }

}