<?php

namespace Drupal\extra_fields_collection\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Datetime\DateFormatterInterface;

/**
 * Extra field Display for date since.
 *
 * @ExtraFieldDisplay(
 *   id = "date_since",
 *   label = @Translation("Date since"),
 *   bundles = {
 *     "node.*",
 *     "media.*"
 *   },
 *   weight = -30,
 *   visible = false
 * )
 */
class DateSince extends ExtraFieldDisplayBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The date formatter service object.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a ExtraFieldDisplayFormattedBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, DateFormatterInterface $date_formatter) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity) {
    $options = [
      'langcode' => $entity->language()->getId(),
    ];
    $date_since = $this->dateFormatter->formatTimeDiffSince($entity->getCreatedTime(), $options);
    $elements = [
      // TODO check if the caching is working correctly.
      '#markup' => $date_since,
    ];

    return $elements;
  }

}
