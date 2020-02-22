<?php

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Render\Markup;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\FieldConfigInterface;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\views\ViewExecutable;


function custom_hooks_views_data() {
    $data = array();
    $data['custom_hooks']['table']['group'] = t('Global');
    $data['custom_hooks']['table']['join'] = [
        // #global is a special flag which allows a table to appear all the time.
        '#global' => [],
    ];

    $data['custom_hooks']['update_status'] = [
        'title' => t('Update Status Form'),
        'help' => t('Update Status Form'),
        'field' => [
            'id' => 'update_status',
        ],
    ];
    $data['custom_hooks']['update_status_top'] = [
        'title' => t('Update Status Top Form'),
        'help' => t('Update Status Top Form'),
        'field' => [
            'id' => 'update_status_top',
        ],
    ];
    $data['custom_hooks']['update_signup_status'] = [
        'title' => t('Update Signup Status Form'),
        'help' => t('Update Signup Status Form'),
        'field' => [
            'id' => 'update_signup_status',
        ],
    ];
    $data['custom_hooks']['update_signup_status_top'] = [
        'title' => t('Update Signup Status Top Form'),
        'help' => t('Update Signup Status Top Form'),
        'field' => [
            'id' => 'update_signup_status_top',
        ],
    ];
    $data['custom_hooks']['sub_department_email'] = [
        'title' => t('Sub Department Email'),
        'help' => t('Sub Department Email'),
        'field' => [
            'id' => 'sub_department_email',
        ],
    ];

    $data['custom_hooks']['current_user_department'] = [
        'title' => t('User Department'),
        'help' => t('Sub Department Email'),
        'field' => [
            'id' => 'current_user_department',
        ],
    ];

    $data['custom_hooks']['proposal_jv_link'] = [
        'title' => t('Proposal or JV link'),
        'help' => t('show proposal link if sender is viewing else show jv request'),
        'field' => [
            'id' => 'current_user_department',
        ],
    ];

    $data['custom_hooks']['proposal_meta_data'] = [
        'title' => t('Proposal Meta Data'),
        'help' => t('Add info about the proposal like '),
        'field' => [
            'id' => 'proposal_meta_data',
        ],
    ];

    return $data;
}


/**
 * Implements hook_views_pre_render().
 */
//function custom_hooks_views_pre_render(Drupal\views\ViewExecutable $view) {
//  $unique_uuids = $new_view_result = [];
//  $new_row_index = 0;
//
//  // Loop through results and filter out duplicate results.
//  foreach ($view->result as $row) {
//    // Use UUID so this works for all entities
//    $uuid = $row->_entity->uuid();
//
//    if (!in_array($uuid, $unique_uuids)) {
//      $row->index = $new_row_index;
//      $new_view_result[] = $row;
//      $unique_uuids[] = $uuid;
//      $new_row_index++;
//    }
//  }
//
//  // Replace $view->result with new array. Apparently views requires sequentially keyed
//  // array of results instead of skipping keys (e.g. 0, 2, 4, etc), so we can't just
//  // unset the duplicates.
//  $view->result = $new_view_result;
//
//  // Update the view index to match the new number of rows.
//  $view->total_rows = $new_row_index;
//}