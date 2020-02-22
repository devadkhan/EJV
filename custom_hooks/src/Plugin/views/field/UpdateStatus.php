<?php

namespace Drupal\custom_hooks\Plugin\views\field;

use Drupal\Component\Utility\Xss;
use Drupal\views\Views;
use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\field\FieldPluginBase;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("update_status")
 */
class UpdateStatus extends FieldPluginBase {

   /**
   * {@inheritdoc}
   */
  public function query() {
    // do nothing -- to override the parent query.
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $nid = $values->nid;
    $defaultStatus = (isset($values->_entity->get('moderation_state')->getValue()[0])) ? $values->_entity->get('moderation_state')->getValue()[0]['value'] : '' ;
    if($defaultStatus == 'unpublished'){
      return '';
      //return ['#markup' => '<span class="rejected-proposal">Rejected</span>', '#allowed_tags' => ['span']];
    }
    $form = \Drupal::formBuilder()->getForm('\Drupal\custom_hooks\Form\ProposalStatus');
    return $form;
  }

}
