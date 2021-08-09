<?php

namespace Drupal\tarik\Controller;

use Drupal\Core\Controller\ControllerBase;

class CatsAdminController extends ControllerBase {

  public function manage() {

   return \Drupal::formBuilder()->getForm('Drupal\tarik\Form\ManageForm');


  }

}
