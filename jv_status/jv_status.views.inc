<?php

/**
 * @file
 * Contains jv_status\jv_status.views.inc..
 * Provide a custom views field data that isn't tied to any other module. */

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Render\Markup;
use Drupal\field\FieldConfigInterface;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\system\ActionConfigEntityInterface;

/**
* Implements hook_views_data().
*/
function jv_status_views_data() {

    $data['views']['table']['group'] = t('Custom Global');
    $data['views']['table']['join'] = [
      // #global is a special flag which allows a table to appear all the time.
      '#global' => [],
    ];


    $data['views']['send_cancel_jv_field'] = [
      'title' => t('Send cancel jv field'),
      'help' => t('link for send and cencel request'),
      'field' => [
          'id' => 'send_cancel_jv_field',
      ],
    ];
    $data['views']['jv_status_type'] = [
      'title' => t('JV Status Type'),
      'help' => t('JV Status Type (Send/receive)'),
      'field' => [
          'id' => 'jv_status_type',
      ],
    ];

    return $data;
}
