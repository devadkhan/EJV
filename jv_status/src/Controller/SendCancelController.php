<?php

namespace Drupal\jv_status\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class SendCancelController.
 */
class SendCancelController extends ControllerBase {

  /**
   * Sendrequest.
   *
   * @return string
   *   Return Hello string.
   */
  public function sendRequest(\Drupal\node\NodeInterface $node) {
	  //check if already exist
	   $user = \Drupal::currentUser();

	  $query = \Drupal::entityQuery('node')
	  ->condition('status', 1) //published or not
	  ->condition('type', 'discussion')
	  ->condition('field_proposal', $node->id())
	  ->condition('field_jv_author', $user->id());
		$nids = $query->execute();
		if(count($nids) > 0){
		}

	  $p_nid = $node->id();
	  $p_author = $node->uid->getValue()[0]['target_id'];

	 //create discussion node
		$node = Node::create(['type' => 'discussion']);
		$node->set('title', 'discussion-'.$p_nid);
		$user = \Drupal::currentUser();

		//Body can now be an array with a value and a format.
		//If body field exists.
		$node->set('field_proposal', $p_nid);
		$node->set('field_user', $p_author);
		$node->set('field_jv_author', $user->id());
		$jv_author_email = $node->field_jv_author->referencedEntities()[0]->getEmail();
		$jv_author_name = $node->field_jv_author->referencedEntities()[0]->getUsername();
		$pauthor_email = $node->field_user->referencedEntities()[0]->getEmail();
    //kint($pauthor_email);exit;
		$pauthor_name = $node->field_user->referencedEntities()[0]->getUsername();
		$proposal_title = $node->field_proposal->referencedEntities()[0]->getTitle();
		$node->status = 1;
		$node->enforceIsNew();
		$node->save();
		$user = \Drupal::currentUser();
		$email = $user->getEmail();
		//$arr = array('name' => $pauthor_name, 'pauthor_email' => $email, 'jauthor_name' => $jv_author_name , 'p_title' => $proposal_title);
    $arr = array('name' => $pauthor_name, 'pauthor_email' => $pauthor_email, 'jauthor_name' => $jv_author_name , 'p_title' => $proposal_title);
		//sendEmail($name,$email,$otp);
		sendEmail($arr, 'mail_to_Pauthor_sending_jv');
		drupal_set_message( 'You have send the JV Request successfully.');
		$previousUrl = \Drupal::request()->server->get('HTTP_REFERER');
		return new RedirectResponse($previousUrl);
  }
  /**
   * Cancelrequest.
   *
   * @return string
   *   Return Hello string.
   */
  public function CancelRequest($node) {

	//check if already exist
	   $user = \Drupal::currentUser();

	   $login_id = $user->id();

	   $nid = $node->id();
	   $p_author = $node->uid->getValue()[0]['target_id'];
	   $field_jv_author = $node->field_jv_author->getValue()[0]['target_id'];

		$type = $node->getType();

		if($type == "discussion")
		{
			if($login_id == $p_author || $login_id == $field_jv_author)
			{
				$storage_handler = \Drupal::entityTypeManager()->getStorage("node");
				$storage_handler->delete([$node]);
				drupal_set_message( 'You have cancel the JV Request');
				$previousUrl = \Drupal::request()->server->get('HTTP_REFERER');
				return new RedirectResponse($previousUrl);
				//kint($previousUrl);

				//exit;
				}else{
					throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
					}

		}else{
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();

		}



	/*	if(count($nids) > 0)
		{

			$storage_handler = \Drupal::entityTypeManager()->getStorage("node");
  			$entities = $storage_handler->loadMultiple($nids);
  			$storage_handler->delete($entities);
			echo 'Deleted';
			print_r($nids);

			exit;

		}else{
			echo "NOde not found";
			}*/

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: CancelRequest with parameter(s): $'),
    ];
  }

}
