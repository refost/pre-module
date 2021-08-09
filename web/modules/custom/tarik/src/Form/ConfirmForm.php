<?php

namespace Drupal\tarik\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;

/**
 * Class DeleteForm.
 */
class ConfirmForm extends ConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId():string {
    return 'delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion():object {
    return t('Delete data');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl():object {
    return new Url('tarik.cats');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription():object {

    if ($_SESSION['id'] != NULL) {
      $descript = t('Are you sure?');
    }
    else {
      $descript = t('Nothing to delete');
    }

    return $descript;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText():object {
    if ($_SESSION['id'] != NULL) {
      $descript = t('Delete it!');
    }
    else {
      $descript = t('Go back');
    }
    return $descript;
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelText():object {
    return t('Cancel');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state):array {
    return parent::buildForm($form, $form_state);
  }

  /**
   * Function delete record and change file status.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $results = $_SESSION['id'];

    $database = \Drupal::database();

    foreach ($results as $res) {
      $database
        ->delete('tarik')
        ->condition('id', $res)
        ->execute();

      $fid = $database
        ->select('tarik', 'cats')
        ->condition('id', $res)
        ->fields('cats', ['id', 'image'])
        ->execute()
        ->fetch();

      $fid = json_decode(json_encode($fid), TRUE);
      $fid = intval($fid['image']);

      $database
        ->update('file_managed')
        ->fields(['status' => 0])
        ->condition('fid', $fid)
        ->execute();

    }
    if ($_SESSION['id'] != NULL) {
      \Drupal::messenger()->addStatus('You successfully deleted records');
    }
    $form_state->setRedirect('tarik.manage_form');
  }

}
