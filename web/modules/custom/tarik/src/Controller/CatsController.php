<?php

namespace Drupal\tarik\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
//use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\Core\Url;
/**
 * Provides route responses for the module.
 */
class CatsController extends ControllerBase {

  /**
   * Create table with cats.
   */
  public function table():array {

    $query = \Drupal::database()->select('tarik', 'cats');
    $query->fields('cats', ['id','name', 'email', 'image', 'date']);
    $results = $query->execute()->fetchAll();
    $rows = [];
    foreach ($results as $data) {
      $fid = $data->image;
      $file = File::load($fid);
      $path = $file->getFileUri();

      $url_delete = Url::fromRoute('tarik.delete_form', ['id' => $data->id], []);
      $linkDelete = Link::fromTextAndUrl('Delete', $url_delete);
      $image = [
        '#theme' => 'image',
        '#uri' => $path,
        '#attributes' => [
          'class' => 'cat-picture',
          'alt' => 'cat',
          'width' => 250,
          'height' => 250
        ]
      ];

      $rows[] = [
        'name' => $data->name,
        'email' => $data->email,
        'image' => ['data' => $image],
        'date' => date('d-m-Y H:i:s', $data->date),
        'delete' => $linkDelete,
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
