<?php

namespace Drupal\tarik\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

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
      '#maxlength' => 32,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add cat'),
      '#ajax' => [
        'callback' => '::setMessage',
        'event' => 'click',
      ],
    ];

    $form['massage'] = [
      '#type' => 'markup',
      '#markup' => '<div id="result_message"></div>',
    ];

    return $form;
  }

  /**
   * Return messenge about form status.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * Form validation.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * Function for message with info what was sended.
   */
  public function setMessage(array &$form, FormStateInterface $form_state):object {

    $response = new AjaxResponse();
    if (2 > strlen($form_state->getValue('name'))) {
      $response->addCommand(
        new HtmlCommand(
          '#result_message',
          '<div class="form-cat-message">' . $this->t('Name of your cat too short')
        )
      );
    }
    else {
      $response->addCommand(
        new HtmlCommand(
          '#result_message',
          '<div class="form-cat-message">' . $this->t('Thanks for sending. The name of cat is @result', ['@result' => ($form_state->getValue('name'))])
        )
      );
    }

    \Drupal::messenger()->deleteAll();

    return $response;
  }

}
