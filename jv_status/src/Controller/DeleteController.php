<?php

namespace Drupal\jv_status\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DefaultController.
 */
class DeleteController{

    /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
    public function delete() {

        $p = \Drupal::request()->query->get('name');
        $d_nids = array_keys(json_decode($p, true));
		if(count($d_nids)==0){
			
			return;
			}
        //filter nids by author
        $uid = \Drupal::currentUser()->id();
        if($uid!=0)
        {

            //sent ids
            $query = \Drupal::entityQuery('node')
                ->condition('type', 'discussion')
                ->condition('uid', $uid);

            //check author nodes and filter 
            $nids = array_values($query->execute());


            //received ids
            $query = \Drupal::entityQuery('node')
                ->condition('type', 'discussion')
                ->condition('field_user', $uid);
            $rnids = array_values($query->execute());


            $to_delete_nids = [];
            foreach($nids as $nid)
            {
                if(in_array($nid, $d_nids))
                {
                    $to_delete_nids[] = $nid;
                }
            }

            foreach($rnids as $nid)
            {
                if(in_array($nid, $d_nids))
                {
                    $to_delete_nids[] = $nid;
                }
            }

            $storage_handler = \Drupal::entityTypeManager()->getStorage("node");
            $entities = $storage_handler->loadMultiple($to_delete_nids);
            $storage_handler->delete($entities);

            $msg = "Successfully deleted ".count($to_delete_nids)." JV";
            drupal_set_message($msg);
            $status = true;
        }else{
            $msg = "access denied";
            $status = false;
        }
        $msgs = ["#type"=>"status_messages"];
        return new JsonResponse([
            'response' => json_encode([
                "status" => $status,
                //                "msg" => $msg,
                "deleted"=>$to_delete_nids,
                //                "ids"=>["request"=>$d_nids, "sent"=>$nids,"received"=>$rnids, "after_filter"=>$to_delete_nids],
                "msgs"=>\Drupal::service('renderer')->renderRoot($msgs)
            ])
        ]);
    }

}
