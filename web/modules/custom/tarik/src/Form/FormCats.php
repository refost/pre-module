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
  public function getFormId() {
    return 'form_cats';
  }

  /**
   * Build form for cat info.
   */
  public function buildForm(array $form, FormStateInterface $form_state):array {

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your cat’s name:'),
      '#placeholder' => $this->t('min length - 2 symbols, min - 32'),
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
    \Drupal::messenger()->addStatus(t('Succes'));
  }

}
