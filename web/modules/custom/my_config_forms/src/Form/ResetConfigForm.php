<?php

declare(strict_types=1);

namespace Drupal\my_config_forms\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Configure My Config Forms settings for this site.
 */
final class ResetConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'my_config_forms_reset_config';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['my_config_forms_reset.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div id="messages"></div>',
    ];
    $form['user_id'] = [
      '#type' => 'number',
      '#title' => $this->t('Enter User Id'),
      '#required' => TRUE
    ];

    $form['#attached']['library'][] = 'my_config_forms/my_config_forms_css';
    return parent::buildForm($form, $form_state);
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
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $message = $this->generateOtl($form, $form_state);
    $this->messenger()->addStatus($this->t('Your One-Time Login Link is : @message', ['@message' => $message]));
    parent::submitForm($form, $form_state);
  }

}
