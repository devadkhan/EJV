<?php

namespace Drupal\custom_hooks\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class TaxonomyListController.
 */
class TaxonomyListController extends ControllerBase {

    /**
   * GetTaxonomylist.
   *
   * @return string
   *   Return Hello string.
   */
    public function getTaxonomyList() {
        $module_handler = \Drupal::service('module_handler');
        $module_path = $module_handler->getModule('custom_hooks')->getPath();
        $pathToJson = realpath($module_path)."/data/countries.min.json";

        $response = [];
        $parent_tid = \Drupal::request()->query->get('parent');

        $parent_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($parent_tid);
        $childs = [];
        if($parent_term){
            $parent_json_key = $parent_term->field_key_for_json->getValue()[0]['value'];
            $data = json_decode(file_get_contents($pathToJson), true);
            if(isset($data[$parent_json_key])){
                $childs = $data[$parent_json_key];
            }
        }


        $response['childs'] = $childs;
        return new JsonResponse($response);
    }

}
