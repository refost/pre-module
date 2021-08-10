<?php

namespace Drupal\tarik\Form;

use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\file\Entity\File;

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

    $form['name-valid'] = [
      '#type' => 'markup',
      '#markup' => '<div id="name_message"></div>',
    ];
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your cat’s name:'),
      '#placeholder' => $this->t('min length - 2 symbols, min - 32'),
      '#required' => TRUE,
      '#maxlength' => 32,
      '#pattern' => '^[\w+]{2,32}$',
    ];

    $form['email-valid'] = [
      '#type' => 'markup',
      '#markup' => '<div id="valid_message"></div>',
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your email:'),
      '#placeholder' => $this->t('your@mail.com'),
      '#required' => TRUE,
      '#pattern' => '^[-_aA-zZ]{2,30}@([a-z]{2,10})\.[a-z]{2,10}$',
      '#ajax' => [
        'callback' => '::validSymb',
        'event' => 'keyup',
      ],
    ];

    $form['upload-img'] = [
      '#type' => 'managed_file',
      '#title' => t('Profile Picture'),
      '#required' => TRUE,
      '#upload_location' => 'public://images/',
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
        'file_validate_size' => [2097152],
      ],
    ];

    $form['massage'] = [
      '#type' => 'markup',
      '#markup' => '<div id="result_message"></div>',
    ];
      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Add cat'),
        '#ajax' => [
          'callback' => '::setMessage',
          'event' => 'click',
        ],
    ];

    return $form;
  }

  /**
   * Return messenge about form status.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $picture = $form_state->getValue('upload-img');
    $file = File::load($picture[0]);
    $file->setPermanent();
    $file->save();
    \Drupal::database()
      ->insert('tarik')
      ->fields([
        'name' => $form_state->getValue('name'),
        'email' => $form_state->getValue('email'),
        'date' => time(),
        'image' => $picture[0],
      ])
      ->execute();
  }

  /**
   * Form validation.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * Symbols input validation.
   */
  public function validSymb(array &$form, FormStateInterface $form_state):object {
    $regular = '/[-_@aA-zZ.]/';
    $line = $form_state->getValue('email');

    $response = new AjaxResponse();
    for ($i = 0; $i < strlen($line); $i++) {
      if (!preg_match($regular, $line[$i])) {
          $response->addCommand(
          new HtmlCommand(
            '#valid_message',
            '<div class="invalid-message">' . $this->t('You can use only letters and "-" or "_" or "@"')
          )
        );
        $response->addCommand(
          new CssCommand('.form-email', ['border-color' => 'red'])
        );
        break;
      }
      else {
        $response->addCommand(
          new HtmlCommand(
            '#valid_message',
            ''
          )
        );
        $response->addCommand(
          new CssCommand('.form-email', ['border-color' => '#ced4da'])
        );
      }
    }
    return $response;
  }

  /**
   * Function for message with info what was sended.
   */
  public function setMessage(array &$form, FormStateInterface $form_state):object {
    \Drupal::messenger()->deleteAll();

    $response = new AjaxResponse();

    if (!preg_match('/^[\w+]{2,32}$/', $form_state->getValue('name'))) {
      $response->addCommand(
        new HtmlCommand(
          '#name_message',
          '<div class="invalid-message">' . $this->t("The cat's name must be longer than 2 and shorter than 32 symbol")
        )
      );
      $response->addCommand(
        new CssCommand(
          '#edit-name',
          ['border-color' => 'red']
        )
      );
    }
    if ($form_state->hasAnyErrors()) {
      $response->addCommand(
        new HtmlCommand(
          '#result_message',
          '<div class="form-cat-message error">' .
          $this->t('you entered incorrect information')
        )
      );
    }
    else {
      \Drupal::messenger()->addStatus(t('Thanks for sending. You can see your cat in table in down'));
      $response->addCommand(new RedirectCommand('\tarik\cats'));
    }

    return $response;
  }

}
