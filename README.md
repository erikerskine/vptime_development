# Variable precision date field

This module provides a field type that can store a date and/or time with
arbitrary precision. For example, values can be a year, a year and month,
a full date with or without time, and a full date time with or without
seconds/milliseconds. All of these can be mixed within the same field.

## Background

This module was intended to overcome some limitations in core's date handling.
Specifically, it is difficult to do the following:

1. A calendar listing where some entries have dates with times, and other
   entries only have dates.

2. A calendar listing with timezone-less entries that are always shown in the
   user's timezone. For example, "Christmas Day" is always "25 December".
   If you move one hour to the westlocation, it should not change to
   "24 December @ 11pm to 25 December @ 11pm". (A problem which is
   exacerbated when using a date-only format pattern!)

There are some technical reasons why the above is difficult:

1. a _datetime_ field can be configured to store either a date on its own,
   or a date and time, but not both.
2. a _datetime_range_ field can be configured as _all day_, this pre-fills the
   start and end values with a time of `00:00` and `23:59` respectively, but
   still considers this to be in the user's timezone. If you view it using a
   different timezone a different 24 hour period is displayed.

## Variable precision

We would like to be able to store a temporal value as it is intended,
without requiring a user to interpolate or omit accuracy to fit round a
technical limitation.

So, if a value is "June 2020", that - and only that - is what should be stored.
If the value is "16 June 2020 @ 18:05 in London", _that_ is what should
be stored. Both are valid and accurate.

By storing these values in the same field, this module can determine how best
to handle them together. That includes ordering them, filtering by them
and performing appropriate timezone adjustments where necessary.

For more on this concept, see
[Time Point: Represents a point in time to some granularity](https://www.martinfowler.com/eaaDev/TimePoint.html) (martinfowler.com).

## Caveats

1. This contains PostgreSQL specific code and __does not yet work with MySQL__.
   It should be possible to avoid the PostgreSQL specifics, but I'm working
   on getting the remaining views filter code in place -
   _with test coverage_ - first.

2. The field formatters and widgets are very rudimentary and display/expect
   raw ISO-8601 strings. As above, the priority is establishing views
   integration and comprehensive test coverage.

## TODO

- Ensure adequate test coverage for the the various views plugins.
- Add views filter that works like the SQL OVERLAPS keyword (aka "all events
  during ..."). This is possible now with two filters:
  "start of value is before or during" AND "end of value is during or after".
- Add MySQL/SQLite support (remove the PostgreSQL specific `AT TIME ZONE`
  construct).
- Views filter/argument plugins aren't making use of database indexes.
  Calculating local times on the fly makes that difficult, but there are some
  workarounds that may help.
- Add a sensible default field formatter (to to replace the `raw` formatter
  which just outputs an ISO-8601 formatted string).
- A better default field widget to replace the `raw` widget which just
  accepts an ISO-8601 formatted string.
- Add timezone handling behaviour (automatic/manual) to the field settings.
- Views filter plugin - can we make use of core's existing date filters to
  support relative dates, eg "+1 week"?
