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
 * @ViewsField("proposal_meta_data")
 */
class ProposalMetaData extends FieldPluginBase {

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
    if($values->_entity->getType() != "proposal")
    {
      return '';
    }
    $proposal = $values->_entity;
//    kint($proposal);

  }
}
