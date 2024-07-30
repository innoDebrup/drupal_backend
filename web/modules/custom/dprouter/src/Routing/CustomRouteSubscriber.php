<?php

namespace Drupal\dprouter\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

class CustomRouteSubscriber extends RouteSubscriberBase {

  public function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('dprouter.displayUsername')) {
      $route->setRequirement('_permission', 'access the custom page');
      $route->setRequirement('_role', 'administrator');
      $route->setRequirement('_custom_access', 'dprouter.custom_access_check::access');
    }
  }
}
