<?php

namespace Drupal\mj_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
    $ct = [];
    $build = [
      '#theme' => 'mj_block_copy_text',
      '#blogs' => $ct,
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $form['copy_text'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Copy Text'),
      '#default_value' => $this->configuration['copy_text']['value'],
      '#format' => 'mj_tf_standard',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {

    // Load in the values from the form_sate.
    $values = $form_state->getValues();

    // Set the config for content to include.
    $this->configuration['copy_text'] = $values['copy_text'];
  }

}
