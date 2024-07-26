<?php

namespace Drupal\first_form\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements an example form.
 */
class AjaxForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ajax_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('<strong>Name</strong>'),
      '#prefix' => '<div id="messages"></div>',
      '#suffix' => '<div class="error" id="name-err"></div>',
      '#ajax' => [
        'callback' => '::validateNameAjax',
        'event' => 'keyup',
      ],
    ];
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('<strong>Phone No.</strong>'),
      '#suffix' => '<div class="error" id="phone-err"></div>',
      '#ajax' => [
        'callback' => '::validatePhoneAjax',
        'event' => 'keyup',
      ],
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('<strong>Email</strong>'),
      '#suffix' => '<div class="error" id="email-err"></div>',
      '#ajax' => [
        'callback' => '::validateEmailAjax',
        'event' => 'keyup',
      ],
    ];
    $form['gender'] = [
      '#type' => 'radios',
      '#title' => $this->t('<strong>Select Your Gender</strong>'),
      '#options' => [
        'Male' => $this->t('Male'),
        'Female' => $this->t('Female'),
      ],
      '#required' => TRUE,
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    ];
    $form['#attached']['library'][] = 'first_form/first_form_css';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $allowed_domains = ['yahoo.com', 'gmail.com', 'outlook.com', 'innoraft.com'];
    $email = $form_state->getValue('email');
    $email_domain = explode("@",$email);
    if (!preg_match('/^\d{10}$/',strval($form_state->getValue('phone_number')))) {
      $form_state->setErrorByName('phone_number', $this->t('The phone number is not a valid Indian phone number: always 10 digits only !!'));
    }
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('email', $this->t('Not a valid email address format!'));
    }
    elseif (!in_array($email_domain[1], $allowed_domains)){
      $form_state->setErrorByName('email', $this->t('Not an authorized email address!'));
    }
    if (!preg_match('/^[a-zA-Z]+[ ]{0,1}[a-zA-Z]*$/', $form_state->getValue('name'))) {
      $form_state->setErrorByName('name', $this->t('Not a valid name format!'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->messenger()->addStatus($this->t('Your name => @name<br> Phone Number => @number<br> Email => @email<br> Your Gender => @gender<br>', 
    [ '@name' => $form_state->getValue('name'),
      '@number' => $form_state->getValue('phone_number'),
      '@email' => $form_state->getValue('email'),
      '@gender' => $form_state->getValue('gender'),
    ]));
  }

  public function validateNameAjax(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $result = '';
    if (!preg_match('/^[a-zA-Z]+[ ]{0,1}[a-zA-Z]*$/', $form_state->getValue('name'))) {
      $result = 'Not a valid name format!';
    }
    $response->addCommand(new HtmlCommand('#name-err', $result));
    return $response;
  }
  public function validatePhoneAjax(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    if (!preg_match('/^\d{10}$/',strval($form_state->getValue('phone_number')))) {
      $response->addCommand(new HtmlCommand('#phone-err', 'The phone number is not a valid Indian phone number: always 10 digits only !!'));
    }
    else {
      $response->addCommand(new HtmlCommand('#phone-err', ''));
    }
    return $response;
  }
  public function validateEmailAjax(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $allowed_domains = ['yahoo.com', 'gmail.com', 'outlook.com', 'innoraft.com'];
    $email = $form_state->getValue('email');
    $email_domain = explode("@",$email);
    $result = '';
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
      $result = 'Not a valid email address format!';
    }
    elseif (!in_array($email_domain[1], $allowed_domains)){
      $result = 'Not an authorized email address!';
    }
    $response->addCommand(new HtmlCommand('#email-err', $result));
    return $response;
  }
}
