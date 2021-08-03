<?php

namespace Drupal\tarik\Form;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;

/**
 * Class DeleteForm
 * @package Drupal\tarik\Form
 */
class DeleteForm extends ConfirmFormBase {

  public $id;
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'delete_form';
  }

  public function getQuestion() {
    return t('Delete data');
  }

  public function getCancelUrl() {
    return new Url('tarik.cats');
  }

  public function getDescription() {
    return t('Are you sure?');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete it!');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelText()
  {
    return t('Cancel');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL)
  {

    $this->id = $id;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    parent::validateForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $query = \Drupal::database();
    $query->delete('tarik')
      ->condition('id',$this->id)
      ->execute();
    \Drupal::messenger()->addStatus('Succes');
    $form_state->setRedirect('tarik.cats');
  }

}
