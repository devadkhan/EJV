<?php

namespace Drupal\custom_hooks\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;

/**
 * Plugin implementation of the 'yearonly_default' widget.
 *
 * @FieldWidget(
 * id = "yearonly_reverse",
 * label = @Translation("Select Year Reverse"),
 * field_types = {
 * "yearonly"
 * }
 * )
 */
class YearOnlyReverseWidget extends WidgetBase implements WidgetInterface {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $field_settings = $this->getFieldSettings();
    if ($field_settings['yearonly_to'] == 'now') {
      $field_settings['yearonly_to'] = date('Y');
    }

    $options = array_combine(range($field_settings['yearonly_to'], $field_settings['yearonly_from']), range($field_settings['yearonly_to'], $field_settings['yearonly_from']));
    $element['value'] = $element + [
      '#type' => 'select',
      '#options' => $options,
      '#empty_value' => '',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : '',
      '#description' => $this->t('Select year'),
    ];
    return $element;
  }

}
