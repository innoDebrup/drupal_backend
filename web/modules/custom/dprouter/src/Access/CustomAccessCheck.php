<?php
namespace Drupal\dprouter\Access;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessCheckInterface;
use Symfony\Component\Routing\Route;

class CustomAccessCheck implements AccessCheckInterface {

  public function access(AccountInterface $account) {
    $roles = $account->getRoles();
    if ($account->hasPermission('access the custom page') && !in_array('editor', $roles)) {
      return AccessResult::allowed();
    }
    return AccessResult::forbidden();
  }

  public function applies(Route $route) {
    return $route->getPath() === '/dprouter';
  }
}
