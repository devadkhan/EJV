<?php

namespace Drupal\custom_hooks\Plugin\views\field;

use Drupal\Component\Utility\Xss;
use Drupal\views\Views;
use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("proposal_jv_link")
 */
class ProposalJvLink extends FieldPluginBase {

    /**
   * {@inheritdoc}
   */
    public function query() {
        // do nothing -- to override the parent query.
    }

    /**
  * {@inheritdoc}
  */
    protected function defineOptions() {
        $options = parent::defineOptions();
        $options['user_source'] = ['default' => ''];
        return $options;
    }

    /**
  * {@inheritdoc}
  */
    public function buildOptionsForm(&$form, FormStateInterface $form_state) {
        $form['user_source'] = [
            '#type' => 'select',
            '#required' => TRUE,
            '#options' => [
                '' => 'Select',
                'login'=>'Login User',
                'path' => 'User from Path'
            ],
            '#title' => $this->t('Select User Source'),
            '#description' => $this->t('desc.'),
            '#default_value' => $this->options['user_source'],
        ];

        parent::buildOptionsForm($form, $form_state);
    }

    /**
   * {@inheritdoc}
   */
    public function render(ResultRow $values) {
        return "Halllloooooo";
    }



}
