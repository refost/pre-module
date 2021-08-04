<?php

namespace Drupal\tarik\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;

/**
 * Class DeleteForm.
 */
class DeleteForm extends ConfirmFormBase {

  public $id;

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
    return t('Are you sure?');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText():object {
    return t('Delete it!');
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
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL):array {
    $this->id = $id;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * Function delete record and change file status.
   */

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $database = \Drupal::database();

    $num = $this->id;

    $query = $database->select('tarik', 'cats');
    $result = $query->condition('id', $num)
      ->fields('cats', ['id', 'image'])
      ->execute()->fetch();

    $result = json_decode(json_encode($result), TRUE);
    $fid = intval($result['image']);

    $database->update('file_managed')
      ->fields(['status' => 0])
      ->condition('fid', $fid)->execute();

    $database->delete('tarik')
      ->condition('id', $this->id)
      ->execute();

    \Drupal::messenger()->addStatus('You successfully deleted record');
    $form_state->setRedirect('tarik.cats');
  }

}
