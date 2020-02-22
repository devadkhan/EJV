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


class ProposalStatus extends FormBase{

	public function getFormId() {
		return 'proposal_status_form';
	}

	public function buildForm(array $form, FormStateInterface $form_state) {
		$node = \Drupal::routeMatch()->getParameter('node');
		if ($node instanceof \Drupal\node\NodeInterface) {
			$defaultStatus = (isset($node->get('moderation_state')->getValue()[0])) ? $node->get('moderation_state')->getValue()[0]['value'] : '' ;
			if($defaultStatus == 'draft'){
				$form['#attributes']['class'][] = 'draft-proposal';
			}
			if($defaultStatus == 'unpublished'){
				$form['proposal_status'] = ['#markup' => 'Rejected'];
			}else{
				$options = ['published' => $this->t('Approve'), 'unpublished' => $this->t('Reject')];
				$form['proposal_nid'] = [
				  '#type' => 'hidden',
				  '#default_value' => $node->id(),
				];
				$form['proposal_status'] = [
				  '#type' => 'radios',
				  '#default_value' => $defaultStatus,
				  '#options' => $options,
				  '#ajax' => [
						'callback' => [$this, 'update_proposal_status'],
						'event' => 'change',
						'wrapper' => 'msg-div',
						'method' => 'replace',
						'effect' => 'fade',
					],
				];
				$form['proposal_rejection'] = [
				  '#type' => 'textarea',
				  '#title' => t('Please write reason of rejection'),
				  '#attributes' => ['placeholder' => t('Your comments type here ...')],
				  '#prefix' => '<div class="hidee hide"><div class="rejection-wrap"><h4>Proposal Rejected</h4>',
				  '#suffix' => '',
				];
				$form['submit'] = [
					'#value' => t('Submit'),
					'#type' => 'submit',
					'#attributes' => ['class' => ['submit']],
					'#prefix' => '<div class="hide top-form-button" id="top-form-button">',
				  '#suffix' => '</div>',
				  '#ajax' => [
						'callback' => [$this, 'update_proposal_status'],
						'wrapper' => 'msg-divv',
						'method' => 'replace',
						'effect' => 'fade',
					],
				];
			}
		}
	  return $form;
	}

	public function validateForm(array &$form, FormStateInterface $form_state) {}
	public function submitForm(array &$form, FormStateInterface $form_state) {}
	public function update_proposal_status(array &$form, FormStateInterface $form_state){
		$nid = $form_state->getValue('proposal_nid');
		$status = $form_state->getValue('proposal_status');
		$reason = $form_state->getValue('proposal_rejection');
		$node = \Drupal\node\Entity\Node::load($nid);
		$nodeStatus = $node->get('moderation_state')->getValue()[0]['value'];

		if($status == 'unpublished' && empty($reason)){
		//if($status == 'unpublished' && $nodeStatus == 'unpublished'){
			$form['proposal_rejection']['#prefix'] = '<div><div class="hidee"><div class="rejection-wrap"><h4>Proposal Rejected</h4>';
			//$form['proposal_rejection']['#suffix'] = '';

			$form['submit']['#prefix'] = '<div class="reject-control"><span class="submit-reject">';
			$form['submit']['#suffix'] = '</span><span class="cancel-reject">Cancel</span></div></div></div>';

			$response = new AjaxResponse();
			$response->addCommand(new ReplaceCommand('div .messages__wrapper', ''));
      $response->addCommand(new ReplaceCommand('[id^="proposal-status-top-form"]', $form));

      return $response;
		}
		if($status == 'unpublished'){
			$node->set('field_rejection_remarks', $reason);
			$form['proposal_status'] = ['#markup' => 'Rejected'];
			$form['submit'] = '';
		}
		$node->set('moderation_state', $status);
		$node->save();
		$options = ['published' => 'Approved', 'unpublished' => 'Rejected'];
		if(array_key_exists($status, $options)) drupal_set_message(t('Proposal has been <i>'.$options[$status].'</i>.'), 'status', FALSE);
		else drupal_set_message(t('Proposal has been updated.'), 'status', FALSE);

		$response = new AjaxResponse();
		$status_messages = array('#type' => 'status_messages');
		$messages = \Drupal::service('renderer')->renderRoot($status_messages);
		if (!empty($messages)) {
			if($status == 'unpublished'){
				$reasonComment = '<h4>Admin Comments</h4>'.$reason;
				$titleMarkup = '<span class="rejected-proposal">Rejected</span>';
				$response->addCommand(new AppendCommand('h1.page-header', $titleMarkup));
				$response->addCommand(new ReplaceCommand('.view-proposal-project.view-display-id-block_1 .views-field-nothing-1 .field-content *', ''));
				$response->addCommand(new AppendCommand('.view-proposal-project.view-display-id-block_1 .views-field-nothing-1 .field-content', $reasonComment));
				$response->addCommand(new RemoveCommand('[id^="proposal-status-top-form"]'));
				$response->addCommand(new RemoveCommand('[id^="proposal-status-form"]'));
			}else{
			  $formBottom = \Drupal::formBuilder()->getForm('\Drupal\custom_hooks\Form\ProposalStatusTop');
				$response->addCommand(new ReplaceCommand('[id^="proposal-status-top-form"]', $formBottom));
				$response->addCommand(new ReplaceCommand('[id^="proposal-status-form"]', $form));
			}
		  $response->addCommand(new ReplaceCommand('div .messages__wrapper', ''));
		  $response->addCommand(new PrependCommand('body', $messages));
		}
		return $response;
	}
}
