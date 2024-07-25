<?php

namespace Drupal\dpmodule\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;

class SimpleController extends ControllerBase {
  protected $currentUser;

  public function __construct(AccountInterface $current_user) {
    $this->currentUser = $current_user;
  }

  public function displayUsername() {
    $username = $this->currentUser->getAccountName();
    return [
      '#markup' => $this->t('Hello, @username', ['@username' => $username]),
    ];
  }
}
