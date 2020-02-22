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
 * @ViewsField("sub_department_email")
 */
class SubDepartmentEmail extends FieldPluginBase {

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
    $output = $values->_entity->getEmail();
    return $output;
  }

}
