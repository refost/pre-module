<?php
/** @returns
 * Contains \Drupal\tarik\Controller\CatsController.
 */

namespace Drupal\tarik\Controller;

/**
 * Provides route responses for the module.
 */
class CatsController
{

  /**
   * Display simple page.
   */

    public function content()
    {
      return array(
        '#markup' => 'Hello! You can add here a photo of your cat.',
      );
    }
}
