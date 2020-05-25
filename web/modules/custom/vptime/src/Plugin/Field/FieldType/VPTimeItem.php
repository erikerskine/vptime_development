<?php

namespace Drupal\vptime\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\vptime\GregorianCalendar;

/**
 * Plugin implementation of the 'vptime' field type.
 *
 * @FieldType(
 *   id = "vptime",
 *   label = @Translation("Variable precision date/time"),
 *   description = @Translation("A date/time arbitrary granularity."),
 *   default_widget = "vptime_raw",
 *   default_formatter = "vptime_raw"
 * )
 */
class VPTimeItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Value'))
      ->setRequired(TRUE);
    $properties['start_at'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Start at'))
      ->setComputed(TRUE)
      ->setRequired(TRUE)
      ->setClass('\Drupal\vptime\DerivedStartAtTimestamp')
      ->setInternal(TRUE);
    $properties['end_before'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('End before'))
      ->setComputed(TRUE)
      ->setRequired(TRUE)
      ->setClass('\Drupal\vptime\DerivedEndBeforeTimestamp')
      ->setInternal(TRUE);
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'varchar_ascii',
          'pgsql_type' => 'text',
        ],
        'start_at' => [
          'type' => 'varchar_ascii',
          'pgsql_type' => 'timestamp',
        ],
        'end_before' => [
          'type' => 'varchar_ascii',
          'pgsql_type' => 'timestamp',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
      'precision_low' => GregorianCalendar::DAY,
      'precision_high' => GregorianCalendar::SECOND,
    ] + parent::defaultFieldSettings();
  }


  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::fieldSettingsForm($form, $form_state);

    // TODO: we need a way to represent 'unlimited' precision here

    $element['precision_low'] = [
      '#type' => 'select',
      '#title' => t('Lowest precision accepted'),
      '#default_value' => $this->getSetting('precision_low'),
      '#required' => TRUE,
      '#options' => [
        GregorianCalendar::YEAR => $this->t('Years'),
        GregorianCalendar::MONTH => $this->t('Months'),
        GregorianCalendar::DAY => $this->t('Days'),
        GregorianCalendar::MINUTE => $this->t('Minutes'),
        GregorianCalendar::SECOND => $this->t('Seconds'),
      ],
    ];

    $element['precision_high'] = [
      '#type' => 'select',
      '#title' => t('Highest precision accepted'),
      '#default_value' => $this->getSetting('precision_high'),
      '#required' => TRUE,
      '#options' => [
        GregorianCalendar::YEAR => $this->t('Years'),
        GregorianCalendar::MONTH => $this->t('Months'),
        GregorianCalendar::DAY => $this->t('Days'),
        GregorianCalendar::MINUTE => $this->t('Minutes'),
        GregorianCalendar::SECOND => $this->t('Seconds'),
      ],
    ];

    $element['#element_validate'][] = [get_called_class(), 'validateFieldSettingsForm'];
    return $element;
  }

  public static function validateFieldSettingsForm(array &$form, FormStateInterface $form_state) {
    $settings = $form_state->getValue('settings');
    $low = (int) $settings['precision_low'];
    $high = (int) $settings['precision_high'];
    if ($high < $low) {
      $form_state->setError($form['precision_high'], t('Highest precision must not be less than the lowest precision.'));
    }
  }


  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraint_manager = $this->getTypedDataManager()->getValidationConstraintManager();
    $constraints = parent::getConstraints();

    $low = $this->getSetting('precision_low');
    $high = $this->getSetting('precision_high');

    $constraints[] = $constraint_manager->create('ComplexData', [
      'value' => [
        'vptime_syntax' => [],
        'vptime_precision' => ['low' => $low, 'high' => $high],
      ],
    ]);

    return $constraints;
  }



  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return empty($this->value);
  }

  /**
   * {@inheritdoc}
   */
  public function onChange($property_name, $notify = TRUE) {
    parent::onChange($property_name, $notify);
    // TODO: do we need this? TextItemBase has it.
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    parent::generateSampleValue($field_definition);
    // TODO: do we need this? TextItemBase has it.
  }

}
