<?php

namespace Drupal\tarik\Form;

use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\file\Entity\File;
use Drupal\Core\Url;

/**
 * Provides route responses for the module.
 */
class EditForm extends FormBase {

  public $fid;

  /**
   * Display simple page.
   */
  public function getFormId(): string {
    return 'edit_form';
  }

  /**
   * Build form for cat info.
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $id = \Drupal::routeMatch()->getParameter('id');

    $database = Database::getConnection();
    $data = [];
      $query = $database->select('tarik', 'cats')
        ->condition('id', $id)
        ->fields('cats');
      $data = $query->execute()->fetchAssoc();

    $this->fid = $data['image'];

    $form['edit-name-valid'] = [
      '#type' => 'markup',
      '#markup' => '<div id="edit-name_message"></div>',
    ];
    $form['edit-name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your cat’s name:'),
      '#placeholder' => $this->t('min length - 2 symbols, min - 32'),
      '#default_value' => (isset($data['name'])) ? $data['name'] : '',
      '#required' => TRUE,
      '#maxlength' => 32,
      '#pattern' => '[aA-zZ]{2,32}',
    ];

    $form['edit-email-valid'] = [
      '#type' => 'markup',
      '#markup' => '<div id="edit-valid_message"></div>',
    ];
    $form['edit-email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your email:'),
      '#placeholder' => $this->t('your@mail.com'),
      '#default_value' => (isset($data['email'])) ? $data['email'] : '',
      '#required' => TRUE,
      '#pattern' => '[-_aA-zZ]{2,30}@([a-z]{2,10})\.[a-z]{2,10}',
      '#ajax' => [
        'callback' => '::validSymbEdit',
        'event' => 'change',
      ],
    ];

    $form['edit-upload-img'] = [
      '#type' => 'managed_file',
      '#title' => t('Profile Picture'),
      '#default_value' => (isset($data['image'])) ? [$data['image']] : [],
      '#required' => TRUE,
      '#upload_location' => 'public://images/',
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
        'file_validate_size' => [2097152],
      ],
    ];

    $form['edit-massage'] = [
      '#type' => 'markup',
      '#markup' => '<div id="edit-result_message"></div>',
    ];
    $form['edit-submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save changes'),
      '#ajax' => [
        'callback' => '::setMessageEdit',
        'event' => 'click',
      ],
    ];

    return $form;
  }

  /**
   * Return messenge about form status.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $picture = $form_state->getValue('edit-upload-img');
    $data = [
      'name' => $form_state->getValue('edit-name'),
      'email' => $form_state->getValue('edit-email'),
      'image' => $picture[0],
    ];
    $file = File::load($picture[0]);
    $file->setPermanent();
    $file->save();

    $id = \Drupal::routeMatch()->getParameter('id');

    $this->changStatus($file);

    \Drupal::database()
        ->update('tarik')
        ->fields($data)
        ->condition('id', $id)->execute();
    }

  /**
   * Function change status image in file_managed table if image was changed.
   */
    public function changStatus($currId) {
        $fid =$this->fid;
        if ($fid != $currId){
          \Drupal::database()
            ->update('file_managed')
            ->fields(['status' => 0])
            ->condition('fid', $fid)->execute();
        }
    }

  /**
   * Form validation.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * Symbols input validation.
   */
  public function validSymbEdit(array &$form, FormStateInterface $form_state):object {
    $regular = '/[-_@aA-zZ.]/';
    $line = $form_state->getValue('edit-email');

    $response = new AjaxResponse();
    for ($i = 0; $i < strlen($line); $i++) {
      if (!preg_match($regular, $line[$i])) {
        $response->addCommand(
          new HtmlCommand(
            '#edit-valid_message',
            '<div class="edit-invalid-message">' . $this->t('You can use only letters and "-" or "_" or "@"')
          )
        );
        $response->addCommand(
          new CssCommand('.edit-form-email', ['border-color' => 'red'])
        );
        break;
      }
      else {
        $response->addCommand(
          new HtmlCommand(
            '#edit-valid_message',
            ''
          )
        );
        $response->addCommand(
          new CssCommand('.edit-form-email', ['border-color' => '#ced4da'])
        );
      }
    }
    return $response;
  }

  /**
   * Function for message with info what was sended.
   */
  public function setMessageEdit(array &$form, FormStateInterface $form_state):object {
    \Drupal::messenger()->deleteAll();

    $response = new AjaxResponse();

    if (!preg_match('/^[aA-zZ]{2,32}$/', $form_state->getValue('edit-name'))) {
      $response->addCommand(
        new HtmlCommand(
          '#edit-name_message',
          '<div class="edit-invalid-message">' . $this->t("The cat's name should contain only Latin characters")
        )
      );
      $response->addCommand(
        new CssCommand(
          '#edit-edit-name',
          ['border-color' => 'red']
        )
      );
    }
    if ($form_state->hasAnyErrors()) {
      $response->addCommand(
        new HtmlCommand(
          '#edit-result_message',
          '<div class="edit-form-cat-message error">' .
          $this->t('you entered incorrect information')
        )
      );
    }
    else {
      \Drupal::messenger()->addStatus(t('All changes was saved'));
      $response->addCommand(new RedirectCommand('\tarik\cats'));
    }

    return $response;
  }

}
