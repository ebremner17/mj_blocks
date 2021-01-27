<?php

namespace Drupal\mj_blocks\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\file\Entity\File;

/**
 * MJ Copy Text.
 *
 * @Block(
 *   id = "mj_block_copy_text",
 *   admin_label = @Translation("Copy Text"),
 * )
 */
class MjCopyText extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a BlockComponentRenderArray object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('entity_type.manager'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $build = [];

    if (isset($this->configuration['image'])) {
      $file = File::load($this->configuration['image']);
      $path = file_create_url($file->getFileUri());
    }

    $ct = [
      'text_color' => isset($this->configuration['text_color']) ? $this->configuration['text_color'] : 'black',
      'text_width' => isset($this->configuration['text_width']) ? $this->configuration['text_width'] : 'full',
      'text' => [
        '#type' => 'processed_text',
        '#text' => $this->configuration['copy_text']['value'],
        '#format' => $this->configuration['copy_text']['format'],
      ],
      'use_background' => $this->configuration['use_background'],
      'image' => isset($path) ? $path : '',
      'image_opacity' => isset($this->configuration['image_opacity']) ? $this->configuration['image_opacity'] : 1,
      'id' => Html::getUniqueId('copy-text'),
    ];

    $build = [
      '#theme' => 'mj_block_copy_text',
      '#ct' => $ct,
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    // The color of the text for element.
    $form['text_color'] = [
      '#type' => 'select',
      '#title' => $this->t('Copy Text Color'),
      '#options' => [
        'black' => 'Black',
        'white' => 'White',
        'red' => 'Red',
        'yellow' => 'Yellow'
      ],
      '#default_value' => $this->configuration['text_color'] ?: 'black',
      '#required' => TRUE,
    ];

    // The form element for the width of the text.
    $form['text_width'] = [
      '#type' => 'select',
      '#title' => $this->t('The width of the text'),
      '#options' => [
        'full' => 'Full width',
        'contained' => 'Contained width',
      ],
      '#default_value' => $this->configuration['text_width'] ?: 'full',
    ];

    // The actual text.
    $form['copy_text'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Copy Text1'),
      '#default_value' => $this->configuration['copy_text']['value'],
      '#format' => 'mj_tf_standard',
      '#required' => TRUE,
    ];

    // The form element for whether or not to use a background image.
    $form['use_background'] = [
      '#type' => 'checkbox',
      '#title' => 'Use a background image?',
      '#default_value' => $this->configuration['use_background'],
    ];

    // The form element for the image.
    $form['image'] = [
      '#type' => 'media_library',
      '#allowed_bundles' => ['mj_mt_image'],
      '#title' => t('Upload your image'),
      '#default_value' => $this->configuration['image'],
      '#description' => t('Upload or select your profile image.'),
      '#states' => array(
        'visible' => array(
          ':input[name="settings[use_background]"]' => array('checked' => TRUE),
        ),
      ),
    ];

    // The form element for the opacity of the image.
    $form['image_opacity'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Image Opacity'),
      '#default_value' => $this->configuration['image_opacity'] ?: 1,
      '#description' => $this->t('Enter the opacity of the image, a value between 0 and 1.'),
      '#states' => array(
        'visible' => array(
          ':input[name="settings[use_background]"]' => array('checked' => TRUE),
        ),
      ),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {

    // Load in the values from the form_sate.
    $values = $form_state->getValues();

    // Set the config for the text color.
    $this->configuration['text_color'] = $values['text_color'];

    // Set the config for the text width.
    $this->configuration['text_width'] = $values['text_width'];

    // Set the config for the actual text.
    $this->configuration['copy_text'] = $values['copy_text'];

    // Set the config for use background image.
    $this->configuration['use_background'] = $values['use_background'];

    // Set the config for the image.
    $this->configuration['image'] = $values['image'];

    // Set the config for the image opacity.
    $this->configuration['image_opacity'] = $values['image_opacity'];
  }

}
