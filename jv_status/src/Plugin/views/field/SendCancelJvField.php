<?php

namespace Drupal\jv_status\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Random;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("send_cancel_jv_field")
 */
class SendCancelJvField extends FieldPluginBase {

    /**
   * {@inheritdoc}
   */
    public function usesGroupBy() {
        return FALSE;
    }

    /**
   * {@inheritdoc}
   */
    public function query() {
        // Do nothing -- to override the parent query.
    }

    /**
   * {@inheritdoc}
   */
    protected function defineOptions() {
        $options = parent::defineOptions();

        $options['hide_alter_empty'] = ['default' => FALSE];
        return $options;
    }

    /**
   * {@inheritdoc}
   */
    public function buildOptionsForm(&$form, FormStateInterface $form_state) {
        parent::buildOptionsForm($form, $form_state);
    }

    /**
   * {@inheritdoc}
   */
    public function render(ResultRow $values) {
        // Return a random text, here you can include your custom logic.
        // Include any namespace required to call the method required to generate
        // the desired output.
        //2 LINK SEND cancel
        //if() /sendRequest/1
        //else /cancelRequest/15

        //$url = Url::fromRoute('jv_status.send_cancel_controller_sendRequest', 10);
        //$link = Link::fromTextAndUrl(t('Send Request'), $url);


        //        $valuess = $this->getEntity($valuess);
        //        kint($values);
        //        $govt_ant = $values->get("title")->getValue();
        $link  = "";
        $node = $values->_entity;
        $owner = $node->getOwner();
        $ownerRole = $owner->getRoles();
       //kint($ownerRole[1]);
        //  kint($ownerRole);exit;
        //    $ntitle =$node->getTitle();
        //    $nbody =$node->getbody();


        //drupal_set_message('This is only for Usman -- '.$node->getOwner()->getEmail(), 'status', FALSE);

        $p_author = $node->uid->getValue()[0]['target_id'];
        $user = \Drupal::currentUser();


        $query = \Drupal::entityQuery('node')
            ->condition('status', 1) //published or not
            ->condition('type', 'discussion')
            ->condition('field_proposal', $node->id())
            ->condition('field_jv_author', $user->id());
        $nids = $query->execute();


        if (\Drupal::currentUser()->isAnonymous() || $user->id()==$p_author) {

            return '';
        }

        $current_user = \Drupal::currentUser();
        $roles = $current_user->getRoles();

        if(array_search('government', $roles) === false && array_search('permanent', $roles) === false ){
            return '';
        }



        $userCompnay = $current_user->getUsername();

        if($roles[1] == "government"){
            
            $userobj = \Drupal\user\Entity\User::load($current_user->id());
            $sub_dep = $userobj->get('field_sub_department')->getValue();


           
            //if sub_dep is empty then this user is main dep
            if(empty($sub_dep)){
                //node owner government and there is no sub_dep
                if($ownerRole[1] == "government"){
                    if($owner->field_department->getValue()[0]['target_id'] == $userobj->field_department->getValue()[0]['target_id']){
                        $link  = "";
                    }else{

                        if(count($nids) > 0){
                            $nid = array_pop($nids);
                            $link = \Drupal\Core\Link::fromTextAndUrl('Cancel JV Request', \Drupal\Core\Url::fromUserInput('/cancelRequest/'.$nid));
                        }else{
                            $link = \Drupal\Core\Link::fromTextAndUrl('Send JV Request', \Drupal\Core\Url::fromUserInput('/sendRequest/'.$node->id()));
                        }
                        $link = $link->toRenderable();
                    }
                }else if($ownerRole[1] == "permanent"){

                    if(count($nids) > 0){
                        $nid = array_pop($nids);
                        $link = \Drupal\Core\Link::fromTextAndUrl('Cancel JV Request', \Drupal\Core\Url::fromUserInput('/cancelRequest/'.$nid));
                    }else{
                        $link = \Drupal\Core\Link::fromTextAndUrl('Send JV Request', \Drupal\Core\Url::fromUserInput('/sendRequest/'.$node->id()));
                    }
                    $link = $link->toRenderable();   
                }

            }else{

                if(@$owner->field_department->getValue()[0]['target_id'] == $userobj->field_department->getValue()[0]['target_id']){
                    $link  = "";
                }else{
                    //echo "<pre>";print_r("dfadsf");exit;
                    // if(count($nids) > 0){
                    //     $nid = array_pop($nids);
                    //     $link = \Drupal\Core\Link::fromTextAndUrl('Cancel JV Request', \Drupal\Core\Url::fromUserInput('/cancelRequest/'.$nid));
                    // }else{
                    //     $link = \Drupal\Core\Link::fromTextAndUrl('Send JV Request', \Drupal\Core\Url::fromUserInput('/sendRequest/'.$node->id()));
                    // }
                    // $link = $link->toRenderable(); 

                    if($ownerRole[1] == "permanent"){
                        if(count($nids) > 0){
                            $nid = array_pop($nids);
                            $link = \Drupal\Core\Link::fromTextAndUrl('Cancel JV Request', \Drupal\Core\Url::fromUserInput('/cancelRequest/'.$nid));
                        }else{
                            $link = \Drupal\Core\Link::fromTextAndUrl('Send JV Request', \Drupal\Core\Url::fromUserInput('/sendRequest/'.$node->id()));
                        }
                        $link = $link->toRenderable();   
                    }
                }
            }

        }else{
            if(count($nids) > 0){
                $nid = array_pop($nids);
                $link = \Drupal\Core\Link::fromTextAndUrl('Cancel JV Request', \Drupal\Core\Url::fromUserInput('/cancelRequest/'.$nid));
            }else{
                $link = \Drupal\Core\Link::fromTextAndUrl('Send JV Request', \Drupal\Core\Url::fromUserInput('/sendRequest/'.$node->id()));
            }
            $link = $link->toRenderable();

        }
        return $link;
    }

}
