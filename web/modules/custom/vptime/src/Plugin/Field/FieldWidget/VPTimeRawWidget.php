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
    $element['value'] = $element + [
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
      '#size' => 30,
    ];
    return $element;
  }

}
