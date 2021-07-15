<?php

namespace Drupal\tarik\Controller;

/**
 * Provides route responses for the module.
 */
class CatsController {

  /**
   * Display simple page.
   */
  public function content():array {
    return [
      '#markup' => '',
    ];
  }

}
