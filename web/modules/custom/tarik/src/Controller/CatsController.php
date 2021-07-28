<?php

namespace Drupal\tarik\Controller;

use Drupal\Core\Controller\ControllerBase;

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
    foreach ($results as $data) {
      $fid = $data->image;
      $file = \Drupal\file\Entity\File::load($fid);
      $path = $file->getFileUri();

      $image_render = [
        '#theme' => 'image_style',
        '#style_name' => 'thumbnail',
        '#uri' => $path,
      ];

      $rows[] = [
        'name' => $data->name,
        'email' => $data->email,
        'image' => ['data' => $image_render],
        'date' => date('Y-m-d', $data->date),
      ];
    }

    arsort($rows);

    $header_table = [
      'name' => t('Name'),
      'email' => t('Email'),
      'image' => t('image'),
      'date' => t('Date'),
    ];

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No data found'),
    ];

    return $build;
  }

  /**
   * Display simple page.
   */
  public function content():array {
    $formCats = \Drupal::formBuilder()->getForm('Drupal\tarik\Form\FormCats');
    $table = $this->table();

    return [$formCats, $table];
  }

}
