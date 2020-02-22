<?php

namespace Drupal\jv_status\Plugin\views\field;

use Drupal\Component\Utility\Xss;
use Drupal\views\Views;
use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\taxonomy\Entity\Term;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("jv_status_type")
 */
class JvStausType extends FieldPluginBase {

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
    $current_path = \Drupal::service('path.current')->getPath();
    if(strpos($current_path, '/sub-department-users/view-jv/') == 0){
      $selectedUser = explode('/', $current_path);
      $selectedUser = end($selectedUser);
    }
    $user = \Drupal\user\Entity\User::load($selectedUser);
    $nidsView = Views::getView('jv_status');
    $nidsView->setDisplay('entity_reference_1');
    $nidsView->preExecute();
    $nidsView->execute();
    $nidsView = $nidsView->result;
    $hasJVs = false;
    $row = [];
    foreach($nidsView as $rows){
      $pAuthor = $rows->_entity->get('field_user')->getValue()[0]['target_id'];
      $JvAuthor = $rows->_entity->get('field_jv_author')->getValue()[0]['target_id'];
      if($pAuthor == $selectedUser || $JvAuthor == $selectedUser){
//        kint($rows->_entity);exit;
        $hasJVs = true;
        $proposal = \Drupal\node\Entity\Node::load($rows->_entity->get('field_proposal')->getValue()[0]['target_id']);
        $proposalAlias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$rows->_entity->get('field_proposal')->getValue()[0]['target_id']);
        $proposalAlias .= '#collapseTwo';

        $jvType = ($JvAuthor === $selectedUser) ? 'Sent' : 'Received' ;
        $company = '';
        if($jvType == 'Sent'){
          //get P Author Company
          $user = \Drupal\user\Entity\User::load($pAuthor);
          
          $company = ($user && isset($user->get('field_sub_department')->getValue()[0])) ? $user->get('field_sub_department')->getValue()[0]['target_id'] : '' ;
          if(!empty($company)) $company = Term::load($company)->getName();
          $status = (isset($rows->_entity->get('field_jv_author_status')->getValue()[0])) ? $rows->_entity->get('field_jv_author_status')->getValue()[0]['value'] : 'None' ;
        }elseif ($jvType == 'Received') {
          //get JV Author Company
          $user = \Drupal\user\Entity\User::load($JvAuthor);
          if(in_array('government', $user->getRoles())){
            $company = (isset($user->get('field_sub_department')->getValue()[0])) ? $user->get('field_sub_department')->getValue()[0]['target_id'] : (isset($user->get('field_department')->getValue()[0])) ? $user->get('field_department')->getValue()[0]['target_id'] : '';
            if(!empty($company)) $company = Term::load($company)->getName();
          }elseif(in_array('permanent', $user->getRoles()) || in_array('authenticated', $user->getRoles())){
            $company = (isset($user->get('field_cpmpany_name')->getValue()[0])) ? $user->get('field_cpmpany_name')->getValue()[0]['value'] : '' ;
          }
          $status = (isset($rows->_entity->get('field_p_author_status')->getValue()[0])) ? $rows->_entity->get('field_p_author_status')->getValue()[0]['value'] : 'N/A' ;
        }
        // kint($rows->_entity->get('field_proposal_status')->getValue());exit;
        $ovarallStatus = (isset($rows->_entity->get('field_proposal_status')->getValue()[0])) ? $rows->_entity->get('field_proposal_status')->getValue()[0]['value'] : 'N/A' ;
        $row[] = [
          'Date' => date('d/m/Y', $rows->_entity->getCreatedTime()),
          'JV Type' => $jvType,
          'Company' => $company,
          'Status' => $status,
          'Ovarall Status' => $ovarallStatus,
          'Operations' => '<a href="'.base_path().substr($proposalAlias, 1).'">'.t('View Details').'</a>',
        ];
      }
    }
    $output = '';
    $user = \Drupal\user\Entity\User::load($selectedUser);
    if(!$hasJVs){
      $output = '<div class="empty-myproposal"><p>'.t($user->getDisplayName().' user don\'t have any eJV.').'</p></div>';
    }else{
      $output .= '<div class="table-responsive"><table class="table table-bordered table-hover table-striped"><thead><tr>';
      foreach ($row[0] as $key => $value) {
        $output .= '<th>'.$key.'</th>';
      }
      $output .= '</thead></tr>';
      foreach ($row as $key => $values) {
        $output .= '<tr>';
        foreach ($values as $key => $value) {
          $output .= '<td>'.$values[$key].'</td>';
        }
        $output .= '</tr>';
      }
      $output .= '</table></div>';
      //$output .= '<div class="empty-myproposal"><p>'.t($user->getDisplayName().' user has some eJV.').'</p></div>';
    }
    return [
      '#type' => 'buy_now',
      '#markup' => $output,
      '#allowed_tags' => [
        'div','ul','li','span','p','a','code','pre','table','thead','tbody','tr','td','th','strong','input','script','select','option','input','button','form','img','canvas','h1','h2','h3','h4','h5','h6','label',
      ],
    ];
  }

}
