<?php
/**
 * @file
 * @author Rakesh James
 * Contains \Drupal\example\Controller\ExampleController.
 * Please place this file under your example(module_root_folder)/src/Controller/
 */
namespace Drupal\custom_hooks\Controller;

use Drupal\Core\Controller\ControllerBase;
use SoapClient;
/**
 * Provides route responses for the Example module.
 */
class custom_hooksController extends ControllerBase{
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function my_page() {

    //echo "<pre>"; print_r($test);exit;
  }
  
}
