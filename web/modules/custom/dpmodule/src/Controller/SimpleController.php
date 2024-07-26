<?php

namespace Drupal\dpmodule\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;

/**
 *  Class SimpleController for managing dpmodule functionality.
 */
class SimpleController extends ControllerBase {
  
  /**
   * Current User of the site.
   *
   * @var AccountInterface
   */
  protected $currentUser;

  /**
   * Constructor of the Controller class.
   *
   * @param AccountInterface $current_user
   */
  public function __construct(AccountInterface $current_user) {
    $this->currentUser = $current_user;
  }

  /**
   * Function to greet currently logged-in user.
   */
  public function displayUsername() {
    $username = $this->currentUser->getAccountName();
    return [
      '#markup' => $this->t('Hello, @username', ['@username' => $username]),
    ];
  }
}
