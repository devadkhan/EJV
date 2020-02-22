<?php

namespace Drupal\custom_hooks\Controller;
use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Example module.
 */
class Otp extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function otp() {
	  
	  exit;
	  //$otp = $num = str_pad(mt_rand(1,9999),4,'0',STR_PAD_LEFT);
	  
		$session = \Drupal::request()->getSession();

	  //echo "<pre>"; print_r($session->get('otp'));exit;
	  $form['#prefix'] = '<div class="col-md-5 col-sm-12"> <div class="inc-db-login">';
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
		'#type' => 'textfield',
		'#title' => t('Enter Code'),
	);
	$form['resend_otp'] = array(
		'#type' => 'button',
		'#value' => t('Resend OTP'),
		'#ajax' => array(
			'callback' => '::zain_resend_otp',
			'event' => 'click',
			'wrapper' => 'resend-otp',
			'method' => 'replace',
			'effect' => 'fade',
		),
		'#prefix' => '<div class="resend-otp">',
		'#suffix' => '</div>',
	);
	$form['submit'] = array(
		'#value' => t('Verify'),
		'#type' => 'submit',
		'#attributes' => array('class' => array('submit')),
		
	);
	$form['#suffix'] = '</div></div>';
    return $form;
  }
  public function otp_submit(&$form, FormStateInterface $form_state){
	  //echo "<pre>"; print_r("Hi"); exit;
	 }
  	public function zain_resend_otp(&$form, FormStateInterface $form_state){
		//echo "<pre>"; print_r("Hi"); exit;
	  
	}
   public function otpOne() {
	   
	   kint($_POST);
	   kint($_GET);
	   kint(\Drupal::request()->request->all());
	   
	   exit;
    $element = array(
      '#markup' => 'Hello, world one',
    );
    return $element;
  }

}