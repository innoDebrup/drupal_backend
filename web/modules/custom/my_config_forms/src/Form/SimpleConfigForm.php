<?php

declare(strict_types=1);

namespace Drupal\my_config_forms\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure My Config Forms settings for this site.
 */
final class SimpleConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'my_config_forms_simple_config';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['my_config_forms.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('<strong>Name</strong>'),
    ];
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('<strong>Phone No.</strong>'),
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('<strong>Email</strong>'),
    ];
    $form['gender'] = [
      '#type' => 'radios',
      '#title' => $this->t('<strong>Select Your Gender</strong>'),
      '#options' => [
        'Male' => $this->t('Male'),
        'Female' => $this->t('Female'),
      ],
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary', 
    ];
    return parent::buildForm($form, $form_state);
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
    if (!preg_match('/^[a-zA-Z]*[ ]{0,1}[a-zA-Z]*$/', $form_state->getValue('name'))) {
      $form_state->setErrorByName('name', $this->t('Not a valid name format!'));
    }
    parent::validateForm($form, $form_state);
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
    $this->config('my_config_forms.settings')
      ->set('name', $form_state->getValue('name'))
      ->set('phone_number', $form_state->getValue('phone_number'))
      ->set('email', $form_state->getValue('email'))
      ->set('gender', $form_state->getValue('gender'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
