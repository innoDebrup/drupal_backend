<?php

namespace Drupal\welcome_block\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a 'Welcome' block.
 */
#[Block(
  id: 'custom_welcome_block',
  admin_label:new TranslatableMarkup('Custom Welcome Block')
)]
class WelcomeBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $role_names = implode(', ',$roles);
    return [
      '#markup' => $this->t('Welcome @roles', ['@roles' => $role_names]),
    ];
  }
}
