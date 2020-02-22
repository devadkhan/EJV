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
 * @ViewsField("current_user_department")
 */
class CurrentUserDepartment extends FieldPluginBase {

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

    switch($this->options['user_source']){
      case 'path':
        //make this dynamic
        $uid = $this->view->args[0];    
        $opposite_user = \Drupal\user\Entity\User::load($uid);
        break;
      case 'login':
        $uid = \Drupal::currentUser()->id();

        $sender_id = $values->_entity->field_jv_author->getValue()[0]['target_id'];
        $reciver_id = $values->_entity->field_user->getValue()[0]['target_id'];

        //if login user is sender show reciver name
        if($uid == $sender_id)
        {
          $opposite_user = \Drupal\user\Entity\User::load($reciver_id);
        }else{//if login user is reciver show sender name
          $opposite_user = \Drupal\user\Entity\User::load($sender_id);
        }
    }
    if(!$opposite_user)
    {
      return "NO USER";
    }


    $company_name = "---";
    //if current user is government
    if($opposite_user->hasRole("government"))
    {
      //'field_department'
      //'field_sub_department'
      $main = $opposite_user->field_department->getValue();
      //      kint($main);
      $sub = $opposite_user->field_sub_department->getValue();
      $used = "";
      //      kint($sub);
      if(count($sub))
      {
        $used = "Sub";
        $company_name = $sub;
      }else{

        $used = "Main";
        $company_name = $main;
      }
      $company_term = \Drupal\taxonomy\Entity\Term::load($company_name[0]['target_id']);
      if(!$company_term)
      {
        return "$used Department Not found";
      }
      $company_name = $company_term->getName();


    }else{
      $company_name = $opposite_user->field_cpmpany_name->getValue()[0]['value'];
    }
    return $company_name;

  }



}
