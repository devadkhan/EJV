<?php
/**
 * @file
 * @author Rakesh James
 * Contains \Drupal\example\Controller\ExampleController.
 * Please place this file under your example(module_root_folder)/src/Controller/
 */
namespace Drupal\custom_hooks\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
/**
 * Provides route responses for the Example module.
 */
class Otp extends FormBase{
 
	public function getFormId() {
		return 'otp_form';
	}
   	public function buildForm(array $form, FormStateInterface $form_state) {
		global $base_url;
	//$otp = $num = str_pad(mt_rand(1,9999),4,'0',STR_PAD_LEFT);
	  
		$session = \Drupal::request()->getSession();

	  //echo "<pre>"; print_r($session->get('otp'));exit;
	  $form['#prefix'] = '<div class="col-md-5 col-sm-12"> <div class="inc-db-login otp-page">';
	    $form['markup'] = array(
		'#markup' => t('PIN Verification '),
		'#title' => t(""),
		'#description' => '',
		'#prefix' => '<div class="col-sm-12"><h1>',
		'#suffix' => '</h1></div>',
	);
	$form['digit'] = array(
		'#markup' => '<span class="numinengar">'.t('Please enter 4 digit code we sent on registered Email').'</span>',
		'#title' => t("Confirmation"),
		'#description' => '',
		'#prefix' => '<div class="row"><div class="col-sm-12 col-xs-12">',
		'#suffix' => '</div></div>',
	);
	$form['field_otp'] = array(
		'#type' => 'password',
		'#title' => t('Enter Code'),
		'#maxlength' => 4,
		'#attributes' =>array('placeholder' => t('****')),
	);
	
	$form['submit'] = array(
		'#value' => t('Verify'),
		'#type' => 'submit',
		'#attributes' => array('class' => array('submit')),
		
	);
	
	$form['msg-wrapper'] =[
		'#markup'=>'<div id="msg-div"></div>'
	];
 	
/*	$form['acount'] = array(
		'#markup' => '<p class="have_account">'.t('Dont have a account?').''.'<a href="'.$base_url.'/user/register" class="">'.t('Click Here').'</a></p>',
		'#weight' =>'100',	
	);*/
	$form['resend_otp'] = array(
		'#type' => 'button',
		'#value' => t('Click Here'),
		'#ajax' => array(
			'callback' => '::zain_resend_otp',
			'event' => 'click',
			'wrapper' => 'msg-div',
			'method' => 'replace',
			'effect' => 'fade',
		),
		'#prefix' => '<div class="resend-otp">Resend Code', 
		'#suffix' => '</div>',
	);
	$form['#suffix'] = '</div></div>';
    return $form;
	}
	public function validateForm(array &$form, FormStateInterface $form_state) {
	
	}
	public function submitForm(array &$form, FormStateInterface $form_state) {
		$session = \Drupal::request()->getSession();
		$otpsession_val = $session->get('otp');
		$login_id = $session->get('login-id');
		$otp_value = $form['field_otp']['#value'];
		if($otp_value == $otpsession_val){
			if(isset($login_id)) {
				$user = User::load($login_id);
				user_login_finalize($user);
				$user_destination = \Drupal::destination()->get();
				$response = new RedirectResponse($user_destination);
				$response->send();
			}
		}else{
			drupal_set_message(t('Please enter Correct PIN Code.'),"error");
		}
		//echo "<pre>"; print_r($otp_value);print_r($otpsession_val);exit;

	
	}
	public function zain_resend_otp(array &$form, FormStateInterface $form_state){
	global $user;
	$otp = str_pad(mt_rand(1,9999),4,'0',STR_PAD_LEFT);
	$session = \Drupal::request()->getSession();
		/**** SET SESSION ****/
	$session->set('otp', $otp);
	$session_name = $session->get('session-name');
	$session_email = $session->get('login-email');

	$arr = array('name' => $session_name, 'email' => $session_email, 'otp' => $otp);
	//sendEmail($name,$email,$otp);
	sendEmail($arr, 'user_login_opt');

$output = [];
$output['a'] = ['#markup'=>"<span class='otp-resend'>OTP has been sent Successfully.</span>"];
return $output;	
			drupal_set_message(t('Its done'), 'status', false);	
		$ajax_response = new AjaxResponse();

 		//$ajax_response->addCommand(new AlertCommand("Please Enter Your Coupon Code If You have..."));
      return $ajax_response;
	}
}
