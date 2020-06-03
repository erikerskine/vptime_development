<?php

namespace Drupal\vptime;

/**
 * Utility functions related to the Gregorian calendar.
 */
class GregorianCalendar {

  // Levels of precision. These correspond to the array indices whenever an
  // array of components is used.
  // There is no theoretical upper limit, and so none is defined here,
  // although a practical limit may be imposed elsewhere, typically by database
  // storage. Levels above 5 correspond to fractional seconds, and the number
  // of digits after the decimal point, ie. 6=tenths, 7=hundredths etc.
  const YEAR = 0;
  const MONTH = 1;
  const DAY = 2;
  const HOUR = 3;
  const MINUTE = 4;
  const SECOND = 5;


  /**
   * Increment the smallest component by 1, roll over larger ones if needed.
   *
   * This does not take DST changes into effect, therefore it is not possible
   * for the input to be an array of length 4 (to hour precision).
   * Nor are leap seconds, or any other such non-standard jumps possible.
   *
   * @param int[] $components
   *
   * @return int[]
   *   A new set of components to the same precision.
   */
  static function incrementSmallestComponent($components) {
    if (empty($components)) {
      throw new \InvalidArgumentException("Components array must not be empty");
    }
    if (count($components) === 4) {
      throw new \InvalidArgumentException("Components array must not be to HOUR precision.");
    }

    $precision = count($components) - 1;
    // Increment the least significant component.
    $components[$precision] += 1;

    // Roll over subsecond digits if necessary.
    for ($index = $precision; $index > static::SECOND; $index--) {
      if ($components[$index] > 9) {
        $components[$index] = 0;
        $components[$index - 1] += 1;
      }
    }

    // Roll over second if necessary.
    if ($precision >= 6 && $components[6] > 59) {
      $components[static::SECOND] = 0;
      $components[static::MINUTE] += 1;
    }

    // Roll over minute if necessary.
    if ($precision >= 5 && $components[5] > 59) {
      $components[5] = 0;
      $components[4] += 1;
    }

    // Roll over hour if necessary.
    if ($precision >= 4 && $components[4] > 59) {
      $components[4] = 0;
      $components[3] += 1;
    }

    // Roll over day if necessary.
    if ($precision >= 3 && $components[3] > 23) {
      $components[3] = 0;
      $components[2] += 1;
    }

    // Roll over week if necessary.
    if ($precision >= 2 && $components[2] > static::daysInMonth($components[0], $components[1])) {
      $components[2] = 1;    // 1st
      $components[1] += 1;   // of next month
    }

    // Roll over year if necessary.
    if ($precision >= 1 && $components[1] > 12) {
      $components[1] = 1;    // January
      $components[0] += 1;   // of next year
    }

    return $components;
  }


  /**
   * Extract component values from a partial date in ISO-8601 extended format.
   *
   * @param string $source_iso_8601
   *
   * @return array
   *   An array of year, month, day, hour, minute, second,
   *   plus up to 6 elements corresponding to the decimal digits of fractional
   *   seconds.
   *   The length of the array will be between 1 (for year precision)
   *   and 12 (for microsecond precision). It will not be 4, because hour
   *   precision is not supported.
   */

  // TODO: support the basic YYYYMMDD / hhmmss forms as well as the extended forms?
  // TODO: remove the hardcoded limit on decimal places here

  static function componentsFromString($source_iso_8601) {
    // wrap pattern inside a non capturing group (?:) and make it all optional
    $optional_non_capture_group = function($s) {
      return "(?:$s)?";
    };

    $match_century_and_year = "([0-9]{4})";
    $match_month = $optional_non_capture_group("-([0-9]{2})");
    $match_day = $optional_non_capture_group("-([0-9]{2})");
    $match_date = "{$match_century_and_year}{$match_month}{$match_day}";

    $match_hour_and_minute = "([0-9]{2}):([0-9]{2})";
    $match_second = $optional_non_capture_group(":([0-9]{2})");
    $match_fractional_second = $optional_non_capture_group("\.([0-9])([0-9])?([0-9])?([0-9])?([0-9])?([0-9])?");
    $match_time = $optional_non_capture_group("T{$match_hour_and_minute}{$match_second}{$match_fractional_second}");

    $pattern = "/^{$match_date}{$match_time}$/";
    $matches = [];
    preg_match($pattern, $source_iso_8601, $matches);
    if (empty($matches)) {
      throw new \InvalidArgumentException("$source_iso_8601 is not a recognised ISO-8601 datetime.");
    }

    $strings = array_slice($matches, 1);
    $ints = array_map(function ($s) { return (int) $s; }, $strings);
    if (!static::validateComponents($ints)) {
      throw new \InvalidArgumentException("$source_iso_8601 is not a valid ISO-8601 datetime.");
    }

    return $ints;
  }


  static function componentsToString($components) {
    if (empty($components) || count($components) === 4) {
      throw new \InvalidArgumentException('Components array must not be empty nor be HOUR precision.');
    }

    $precision = count($components) - 1;
    switch ($precision) {
      case static::YEAR:
        return vsprintf('%04d', $components);
      case static::MONTH:
        return vsprintf('%04d-%02d', $components);
        break;
      case static::DAY:
        return vsprintf('%04d-%02d-%02d', $components);
        break;
      case static::MINUTE:
        return vsprintf('%04d-%02d-%02dT%02d:%02d', $components);
        break;
      case static::SECOND:
        return vsprintf('%04d-%02d-%02dT%02d:%02d:%02d', $components);
        break;
      default:
        return vsprintf('%04d-%02d-%02dT%02d:%02d:%02d.', $components) .
          implode('', array_slice($components, 6));
    }
  }

  /**
   * @param \DateTimeInterface $d
   * @param int $precision
   *
   * @return int[]
   */
  static function componentsFromDateTimeObject(\DateTimeInterface $d, $precision) {
    $all_components = static::componentsFromString($d->format('Y-m-d\TH:i:s.u'));
    return array_slice($all_components, 0, $precision + 1);
  }

  /**
   * Return a new list of components in a different timezone.
   *
   * Note: maximum precision is microseconds, because of DateTimeImmutable.
   *
   * @param int[] $components
   * @param string $from_timezone
   * @param string $to_timezone
   *
   * @return int[]
   */
  static function adjustTimezone($components, $from_timezone, $to_timezone) {
    $precision = count($components) - 1;
    if ($precision < static::MINUTE) {
      throw new \InvalidArgumentException('Components array must be at least MINUTE precision to adjust timezone.');
    }

    $str = static::componentsToString($components);
    $d = new \DateTimeImmutable($str, new \DateTimeZone($from_timezone));
    $d = $d->setTimezone(new \DateTimeZone($to_timezone));
    return static::componentsFromDateTimeObject($d, $precision);
  }


  /**
   * Convert component values to a string representation of their timestamp.
   *
   * The output will normally be to the same precision as $components, with
   * the following exceptions:
   *   - If the components contain only a year or year and month, a complete
   *     date string is returned. Missing months or days are assumed to be
   *     January and 1st respectively.
   *   - If the components contain an hour but no minute, :00 is appended
   *     to the time.
   *
   * @param int[] $components
   *
   * @return string
   */
  static function toTimestamp($components) {
    if (empty($components)) {
      throw new \InvalidArgumentException("Components array must not be empty");
    }

    // Ensure we have at least 'day' precision by adding default week and day.
    $components = array_pad($components, 3, 1);

    $result = sprintf('%04d-%02d-%02d',
      $components[static::YEAR],
      $components[static::MONTH],
      $components[static::DAY]);

    if (count($components) >= 4) {
      // If we show time, ensure we have at least 'minute' precision.
      $components = array_pad($components, 5, 0);

      $result .= sprintf('T%02d:%02d', $components[static::HOUR], $components[static::MINUTE]);
      if (count($components) >= 6) {
        $result .= sprintf(':%02d', $components[static::SECOND]);
        if (count($components) >= 7) {
          $result .= '.' . implode('', array_slice($components, 6));
        }
      }
    }

    return $result;
  }


  // Helper functions


  /**
   * Check the number of components is acceptable and they are within range.
   *
   * @param int[] $components
   *
   * @return bool
   *   TRUE if valid, FALSE otherwise.
   */
  static function validateComponents($components) {
    $length = count($components);
    if ($length === 0) {
      return FALSE;
    }
    if ($components[static::YEAR] < 0) {
      return FALSE;
    }
    if ($length >= 2 && ($components[static::MONTH] < 1 || $components[static::MONTH] > 12)) {
      return FALSE;
    }
    if ($length >= 3 && ($components[static::DAY] < 1 || $components[static::DAY] > static::daysInMonth($components[static::YEAR], $components[static::MONTH]))) {
      return FALSE;
    }
    // Hour without minute is not supported.
    if ($length === 4) {
      return FALSE;
    }
    if ($length >= 5 && ($components[static::HOUR] < 0 || $components[static::HOUR] > 23 || $components[static::MINUTE] < 0 || $components[static::MINUTE] > 59)) {
      return FALSE;
    }
    // Leap seconds are not supported here.
    if ($length >= 6 && ($components[static::SECOND] < 0 || $components[static::SECOND] > 59)) {
      return FALSE;
    }
    for ($x = 6; $x < $length; $x++) {
      if ($components[$x] < 0 || $components[$x] > 9) {
        return FALSE;
      }
    }
    return TRUE;
  }

  /**
   * 30, 31 etc...
   *
   * @param int $year
   * @param int $month
   *
   * @return int
   */
  private static function daysInMonth($year, $month) {
    switch ($month) {
      case 1:
      case 3:
      case 5:
      case 7:
      case 8:
      case 10:
      case 12:
        return 31;
      case 4:
      case 6:
      case 9:
      case 11:
        return 30;
      case 2:
        $leap_year = ($year % 400 === 0) || (($year % 4 === 0) && ($year % 100 !== 0));
        return $leap_year ? 29 : 28;
      default:
        throw new \InvalidArgumentException("$month is not a valid month value");
    }
  }

}