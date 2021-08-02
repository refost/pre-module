<?php

namespace Drupal\tarik\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;

/**
 * Provides route responses for the module.
 */
class CatsController extends ControllerBase {

  /**
   * Create table with cats.
   */
  public function table():array {

    $query = \Drupal::database()->select('tarik', 'cats');
    $query->fields('cats', ['name', 'email', 'image', 'date']);
    $results = $query->execute()->fetchAll();
    $rows = [];
    foreach ($results as $data) {
      $fid = $data->image;
      $file = File::load($fid);
      $path = $file->getFileUri();

      $image = [
        '#theme' => 'image',
        '#uri' => $path,
        '#attributes' => [
          'class' => 'cat-picture',
          'width' => 10,
          'height' => 10
        ]
      ];

      $rows[] = [
        'name' => $data->name,
        'email' => $data->email,
        'image' => ['data' => $image],
        'date' => date('Y-m-d', $data->date),
      ];
    }

    if (!$rows == NULL) {
      krsort($rows);
    }

    return $rows;
  }

  /**
   * Display simple page.
   */
  public function content():array {
    $formCats = \Drupal::formBuilder()->getForm('Drupal\tarik\Form\FormCats');
    $table = $this->table();

    $header = [
      'name' => $this->t('Name'),
      'email' => $this->t('Email'),
      'image' => $this->t('image'),
      'date' => $this->t('Date'),
    ];

    return [
      '#theme' => 'tarik_template',
      '#form' => $formCats,
      '#thead' => $header,
      '#trows' => $table,
    ];
  }

}
