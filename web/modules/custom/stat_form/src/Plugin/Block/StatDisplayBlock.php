<?php

namespace Drupal\stat_form\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Stat Display' Block.
 */
#[Block(
  id: 'stat_display',
  admin_label: new TranslatableMarkup('Stat Display Block')
)]
class StatDisplayBlock extends BlockBase implements ContainerFactoryPluginInterface {
  
  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new StatDisplayBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $database) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Retrieve data from the database.
    $query = $this->database->select('stat_form_data', 's')
      ->fields('s', ['name', 'label_1', 'stat_1', 'label_2', 'stat_2']);
    $results = $query->execute()->fetchAll();
    // Prepare the render array.
    $rows = [];
    foreach ($results as $record) {
      $rows[] = [
        'name'=> $record->name,
        'label_1'=> $record->label_1,
        'stat_1'=> $record->stat_1,
        'label_2'=> $record->label_2,
        'stat_2'=> $record->stat_2,
      ];
    }
    $render_array = [
      '#theme' => 'stat_form_table',
      '#rows' => $rows,
      '#attached' => [
        'library' => [
          'stat_form/stat-form-css',
        ],
      ],
    ];
    // Add cache tag for cache management.
    $render_array['#cache'] = [
      'tags' => ['stat_display_block'],
    ];
    return $render_array;
  }
}
