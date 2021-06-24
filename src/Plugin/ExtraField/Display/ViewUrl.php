<?php

namespace Drupal\extra_fields_collection\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;
use Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Extra field Display for view_url.
 *
 * @ExtraFieldDisplay(
 *   id = "view_url",
 *   label = @Translation("URL"),
 *   bundles = {
 *     "node.*",
 *     "media.*"
 *   },
 *   weight = -30,
 *   visible = false
 * )
 */
class ViewUrl extends ExtraFieldDisplayBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The link relationship type, for example: canonical or edit-form.
   *
   * @var string
   */
  public $rel = 'canonical';

  /**
   * The link options.
   *
   * See \Drupal\Core\Routing\UrlGeneratorInterface::generateFromRoute() for
   *   the available options.
   *
   * @var array
   */
  public $options = [
    'absolute' => TRUE,
  ];

  /**
   * Constructs a ExtraFieldDisplayFormattedBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity) {
    $this->options['absolute'] = (bool) $this->getSetting('absolute');

    $elements = [
      '#markup' => $entity->toUrl($this->rel, $this->options)->toString(),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   *
   * Applies only if the current class extends
   * Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayBase
   * defined by extra_field_plus contrib module.
   */
  protected function defaultFormValues() {
    return [
      'absolute' => $this->options['absolute']
    ];
  }

  /**
   * {@inheritdoc}
   *
   * Applies only if the current class extends
   * Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayBase
   * defined by extra_field_plus contrib module.
   */
  public function getSetting($name) {
    $value = NULL;

    if (is_callable('parent::getSetting')) {
      $value = $settings = parent::getSetting($name);
    } 
    else {
      $default_settings = $this->defaultFormValues();
      $value = isset($default_settings[$name]) ? $default_settings[$name] : NULL;
    }

    return $value;
  }

  /**
   * {@inheritdoc}
   *
   * Applies only if the current class extends
   * Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayBase
   * defined by extra_field_plus contrib module.
   */
  protected function settingsForm() {
    $elements = [];

    $elements['absolute'] = [
      '#title' => $this->t('Absolute URL'),
      '#type' => 'checkbox',
    ];

    return $elements;
  }
}
