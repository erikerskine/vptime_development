<?php

namespace Drupal\vptime\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'vptime_raw' widget.
 *
 * @FieldWidget(
 *   id = "vptime_raw",
 *   label = @Translation("Raw"),
 *   field_types = {
 *     "vptime"
 *   }
 * )
 */
class VPTimeRawWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $precision_names = ['year', 'month', 'day', 'hour', 'minute', 'second', '.1 second', '.01 second', 'millisecond', '.1 ms', '.01 ms', 'microsecond'];
    $precision_low = $items->getFieldDefinition()->getSetting('precision_low');
    $precision_high = $items->getFieldDefinition()->getSetting('precision_high');

    $element['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Value'),
      '#title_display' => 'hidden',
      '#description' => $this->t("Format: %format<br>Lowest precision: %low<br>Highest precision: %high", [
        '%format' => "yyyy-mm-ddThh:mm:ss.\u{00b5}s",
        '%low' => $precision_names[$precision_low],
        '%high' => $precision_names[$precision_high],
      ]),
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
      '#size' => 30,
    ];
    $element['timezone'] = [
      '#type' => 'select',
      '#title' => t('Time zone'),
      '#default_value' => isset($items[$delta]->timezone) ? $items[$delta]->timezone : NULL,
      '#options' => system_time_zones(TRUE, TRUE),
    ];

    $element['#theme_wrappers'][] = 'fieldset';
    return $element;
  }

}
