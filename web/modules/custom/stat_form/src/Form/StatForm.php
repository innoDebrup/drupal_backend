<?php

namespace Drupal\stat_form\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class StatForm extends FormBase {
  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new StatDisplayBlock.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'stat_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Initialize the field group array.
    if (empty($form_state->get('num_field_group'))) {
      $form_state->set('num_field_group', 1);
    }

    $num_field_group = $form_state->get('num_field_group');
    
    $form['field_group'] = [
      '#type' => 'container',
      '#prefix' => '<div id="field-group-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
    ];
    // Loop through the field groups.
    for ($i = 0; $i < $num_field_group; $i++) {
      $form['field_group'][$i] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Field Group @number', ['@number' => $i + 1]),
      ];
      $form['field_group'][$i]['name'] = [
        '#type' => 'textfield',
        '#title'=> $this->t('Field-Group Name'),
      ];
      $form['field_group'][$i]['label_1'] = [
        '#type' => 'textfield',
        '#title' => $this->t('1st Label Title'),
      ];
      $form['field_group'][$i]['stat_1'] = [
        '#type' => 'number',
        '#title' => $this->t('1st Label Stats'),
      ];
      $form['field_group'][$i]['label_2'] = [
        '#type' => 'textfield',
        '#title' => $this->t('2nd Label Title'),
      ];
      $form['field_group'][$i]['stat_2'] = [
        '#type' => 'number',
        '#title' => $this->t('2nd Label Stats'),
      ];
    }
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['add_more'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add more'),
      '#submit' => ['::addMore'],
      '#ajax' => [
        'callback' => '::updateCallback',
        'wrapper' => 'field-group-wrapper',
      ],
    ];
    $form['actions']['remove'] = [
      '#type' => 'submit',
      '#value' => $this->t('Remove'),
      '#submit' => ['::removeCallback'],
      '#weight' => 50,
      '#ajax' => [
        'callback' => '::updateCallback',
        'wrapper' => 'field-group-wrapper',
      ],
    ];
    $form['actions']['submit']= [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#weight' => 100,
    ];
    return $form;
  }

  /**
   * Custom submit handler for adding more fields.
   * 
   * @param  array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * 
   * @return void
   */
  public function addMore(array &$form, FormStateInterface $form_state) {
    $num_field_group = $form_state->get('num_field_group');
    $form_state->set('num_field_group', $num_field_group + 1);
    $form_state->setRebuild();
  }

  /**
   * Custom submit handler for removing fields.
   * 
   * @param  array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * 
   * @return void
   */
  public function removeCallback(array &$form, FormStateInterface $form_state) {
    $num_field_group = $form_state->get('num_field_group');
    if ($num_field_group > 1) {
      $form_state->set('num_field_group', $num_field_group - 1);
    }
    $form_state->setRebuild();
  }

  /**
   * AJAX callback to update the form.
   * 
   * @param  array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * 
   * @return void
   */
  public function updateCallback(array &$form, FormStateInterface $form_state) {
    return $form['field_group'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Handle form submission.
    $values = $form_state->getValue('field_group');
    \Drupal::messenger()->addMessage('<pre>' . print_r($form_state->getValue('field_group'), TRUE) . '</pre>');
    foreach ($values as $value) {
      $this->database->insert('stat_form_data')->fields([
        'name' => $value['name'],
        'label_1' => $value['label_1'],
        'stat_1' => $value['stat_1'],
        'label_2' => $value['label_2'],
        'stat_2' => $value['stat_2'],
      ])->execute();
    }
    Cache::invalidateTags(['stat_display_block']);
  }
}
