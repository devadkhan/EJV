<?php

namespace Drupal\custom_hooks\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EmailDomainRestrcitionForm.
 */
class EmailDomainRestrcitionForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'custom_hooks.emaildomainrestrcition',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'email_domain_restrcition_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('custom_hooks.emaildomainrestrcition');
    $form['domain_list'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Domain list'),
      '#description' => $this->t('Enter domains with .com e.g. gmail.com yahoo.com on each line'),
      '#default_value' => $config->get('domain_list'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('custom_hooks.emaildomainrestrcition')
      ->set('domain_list', $form_state->getValue('domain_list'))
      ->save();
  }

}
