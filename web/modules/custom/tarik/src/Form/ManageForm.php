<?php

namespace Drupal\tarik\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Class for confirm deleting.
 */
class ManageForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'form_manage';
  }

  /**
   * Function for creating module.
   */
  public function createTable():array {

    $table = \Drupal::database()
      ->select('tarik', 'cats')
      ->fields('cats', ['id', 'name', 'email', 'image', 'date'])
      ->execute()
      ->fetchAll();

    $rows = [];

    foreach ($table as $row) {
      $fid = $row->image;
      $file = File::load($fid);
      $path = $file->getFileUri();

      $image = [
        '#theme' => 'image',
        '#uri' => $path,
        '#attributes' => [
          'class' => 'cat-picture',
          'alt' => 'cat',
          'width' => 100,
          'height' => 100,
        ],
      ];

      $rows[$row->id] = [
        'name' => $row->name,
        'email' => $row->email,
        'image' => ['data' => $image],
        'date' => date('d-m-Y H:i:s', $row->date),
      ];

    }

    if (!$rows == NULL) {
      krsort($rows);
    }

    return $rows;

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $header = [
      'name' => $this->t('name'),
      'email' => $this->t('email'),
      'image' => $this->t('image'),
      'date' => $this->t('Date'),
    ];

    $rows = $this->createTable();

    $form['table'] = [
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $rows,
      '#title' => t('mange table'),
      '#empty' => t('No records found'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('delete'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $_SESSION['id'] = $form_state->getValue(['table']);
    $form_state->setRedirect('tarik.confirm_form');
  }

}
