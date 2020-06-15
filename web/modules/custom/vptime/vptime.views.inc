<?php

use Drupal\field\FieldStorageConfigInterface;

/**
 * Implements hook_field_views_data().
 */
function vptime_field_views_data(FieldStorageConfigInterface $field_storage) {
  $data = views_field_default_views_data($field_storage);

  if ($field_storage->getType() === 'vptime') {
    foreach ($data as $table_name => $table_data) {

      $value_column = $field_storage->getName() . '_value';

      // Use a dedicated sort plugin for the main 'value' column.
      $data[$table_name][$value_column]['sort']['id'] = 'vptime';

      // Use a dedicated argument plugin for the main 'value' column.
      $data[$table_name][$value_column]['argument']['id'] = 'vptime';

      // Use dedicated filter plugins that operate on the start and end
      // of the value rather than the of the textual 'value' column.
      unset($data[$table_name][$value_column]['filter']);

      // Define an additional psuedo-column 'start' for filtering rows based
      // on the start of the vptime period.
      $data[$table_name][$field_storage->getName() . '_start'] = [
        'title' => t('@label (@name) - start of period', [
          '@label' => $table_data[$value_column]['title short'],
          '@name' => $field_storage->getName(),
        ]),
        'title short' => t('@label - start of period', [
          '@label' => $table_data[$value_column]['title short'],
        ]),
        'group' => $table_data[$value_column]['group'],
        'help' => t('The start of the period defined by the field value. Appears: in @bundles.', [
          '@bundles' => implode(', ', $field_storage->getBundles()),
        ]),
      ];

      // Make it possible to filter by the 'start' psuedo-column.
      $data[$table_name][$field_storage->getName() . '_start']['filter'] = [
        'id' => 'vptime_start',
        'field' => 'field_vptime_start_at',  // a real column name for the handler to use
        'table' => $table_name,
        'field_name' => $field_storage->getName(),
        'entity_type' => $field_storage->getEntityTypeId(),
        'allow empty' => FALSE,
      ];

      // Define an additional pseudo-column 'end' for filtering rows based
      // on the end of the vptime period. This has subtly different logic
      // than 'end_before', which is a timestamp outside the period.
      $data[$table_name][$field_storage->getName() . '_end'] = [
        'title' => t('@label (@name) - end of period', [
          '@label' => $table_data[$value_column]['title short'],
          '@name' => $field_storage->getName(),
        ]),
        'title short' => t('@label - end of period', [
          '@label' => $table_data[$value_column]['title short'],
        ]),
        'group' => $table_data[$value_column]['group'],
        'help' => t('The end of the period defined by the field value. Appears in: @bundles.', [
          '@bundles' => implode(', ', $field_storage->getBundles()),
        ]),
      ];

      // Make it possible to filter by the 'end' psuedo-column.
      $data[$table_name][$field_storage->getName() . '_end']['filter'] = [
        'id' => 'vptime_end',
        'field' => 'field_vptime_end_before',  // a real column name for the handler to use
        'table' => $table_name,
        'field_name' => $field_storage->getName(),
        'entity_type' => $field_storage->getEntityTypeId(),
        'allow empty' => FALSE,
      ];

      // The raw start_at/end_before columns contain local timestamps,
      // so they are unsuitable for sorting/filtering.
      unset($data[$table_name][$field_storage->getName() . '_start_at']['sort']);
      unset($data[$table_name][$field_storage->getName() . '_start_at']['filter']);
      unset($data[$table_name][$field_storage->getName() . '_start_at']['argument']);
      unset($data[$table_name][$field_storage->getName() . '_end_before']['sort']);
      unset($data[$table_name][$field_storage->getName() . '_end_before']['filter']);
      unset($data[$table_name][$field_storage->getName() . '_end_before']['argument']);

    }
  }

  return $data;
}
