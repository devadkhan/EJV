<?php

/**
 * @file
 * @author Muhammad Usman
 */
namespace Drupal\custom_hooks\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\PrependCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Ajax\AppendCommand;


class SignupStatus extends FormBase{

	public function getFormId() {
		return 'signup_status_form';
	}

	public function buildForm(array $form, FormStateInterface $form_state) {
		$defaultStatus = '';
		$route_name = \Drupal::routeMatch();
    if($route_name->getRouteName() == 'entity.user.canonical'){
      $user = \Drupal::request()->attributes->get('user');
      $newSignup = $user->get('field_new_signup')->getValue()[0]['value'];
    	if ($newSignup == 'New') $defaultStatus = ''; elseif ($newSignup == 'Rejected') $defaultStatus = 'Rejected';
    	elseif ($newSignup == 'Approved') $defaultStatus = 'Approved'; else $defaultStatus = '';
      $roles = $user->getRoles();
      if($user->id != \Drupal::currentUser()->id()){
				$form['signup_uid'] = [
				  '#type' => 'hidden',
				  '#default_value' => $user->id(),
				];
      }
    }
    $options = ['Approved' => $this->t('Approve'), 'Rejected' => $this->t('Reject')];
		$form['signup_status'] = [
      '#type' => 'radios',
      '#options' => $options,
      '#default_value' => (!empty($defaultStatus)) ? $defaultStatus : '',
      '#ajax' => [
				'callback' => [$this, 'update_signup_status'],
				'wrapper' => 'msg-divv',
				'method' => 'replace',
				'effect' => 'fade',
			],
    ];
		$form['#attributes']['class'][] = 'proposal-status-top-form draft-proposal';
	  return $form;
	}

	public function validateForm(array &$form, FormStateInterface $form_state) {}
	public function submitForm(array &$form, FormStateInterface $form_state) {}
	public function update_signup_status(array &$form, FormStateInterface $form_state){
		$uid = $form_state->getValue('signup_uid');
		$user = \Drupal\user\Entity\User::load($uid);
		$clicked = $form_state->getValue('signup_status');
		// if ($clicked == 'Approved') $user->activate(); elseif ($clicked == 'Rejected') $user->block();
  	if ($clicked == 'Approved') $user->addRole('permanent'); elseif ($clicked == 'Rejected') $user->removeRole('permanent');
	  $user->set('field_new_signup', $clicked);
	  $user->save();
	  $options = ['Approved' => $this->t('Approved'), 'Rejected' => $this->t('Rejected')];
		if(array_key_exists($clicked, $options)) drupal_set_message(t('User has been <i>'.$options[$clicked].'</i>.'), 'status', FALSE);
		else drupal_set_message(t('User has been updated.'), 'status', FALSE);

		$response = new AjaxResponse();
		$formBottom = \Drupal::formBuilder()->getForm('\Drupal\custom_hooks\Form\SignupStatusTop');
		$response->addCommand(new ReplaceCommand('[id^="signup-status-top-form"]', $formBottom));
		$response->addCommand(new ReplaceCommand('[id^="signup-status-form"]', $form));
		$status_messages = array('#type' => 'status_messages');
		$messages = \Drupal::service('renderer')->renderRoot($status_messages);
		$response->addCommand(new ReplaceCommand('div .messages__wrapper', ''));
		$response->addCommand(new PrependCommand('body', $messages));

    return $response;
	}
}
