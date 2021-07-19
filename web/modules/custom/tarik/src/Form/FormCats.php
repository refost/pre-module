<?php

namespace Drupal\tarik\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides route responses for the module.
 */
class FormCats extends FormBase {

  /**
   * Display simple page.
   */
  public function getFormId(): string {
    return 'form_cats';
  }

  /**
   * Build form for cat info.
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your cat’s name:'),
      '#placeholder' => $this->t('min length - 2 symbols, min - 32'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add cat'),
    ];
    return $form;
  }

  /**
   * Return messenge about form status.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addStatus(t('Sending successful. The name is correct '));
  }

  /**
   * Form validation.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (2 > strlen($form_state->getValue('name')) ||
      32 < strlen($form_state->getValue('name'))) {
      $form_state->setErrorByName('name', $this->t('Name must be longer than 2 symbols and shorter than 32'));
    }
  }

}
