<?php
namespace Drupal\first_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\user\Entity\User;

/**
* Implements an Reset form.
*/
class ResetForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'reset_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div id="messages"></div>',
    ];
    $form['user_id'] = [
      '#type' => 'number',
      '#title' => $this->t('Enter User Id'),
      '#required' => TRUE
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
      '#ajax' => [
        'callback' => '::submitFormAjax',
      ],
    ];
    $form['#attached']['library'][] = 'first_form/first_form_css';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->messenger()->addStatus($this->t('Hello'));
  }

  /**
   * Function to generate a one-time login link for a user.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param FormStateInterface $form_state
   *   Current state of the form.
   * 
   * @return void
   */
  public function generateOtl(array &$form, FormStateInterface $form_state) {
    $userId = $form_state->getValue('user_id');
    $user = User::load($userId);
    $message = '';
    if ($userId && $user) {
      $otll = user_pass_reset_url($user);
      $message = $this->t('One-time login link: <a href="@link">Reset Password Link Generated! Click Here!</a>', ['@link' => $otll]);
    }
    else {
      $message = $this->t('<div class="error">Invalid user ID.</div>');
    }
    return $message;
  }

  /**
   * Ajax Fuction to handle the submission of the reset form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param FormStateInterface $form_state
   *   Current state of the form..
   * 
   * @return void
   */
  public function submitFormAjax(array &$form, FormStateInterface $form_state) {
    $ajaxResponse = new AjaxResponse();
    $message = $this->generateOtl($form, $form_state);
    $ajaxResponse->addCommand(new HtmlCommand('#messages', $message));
    return $ajaxResponse;
  }
}
