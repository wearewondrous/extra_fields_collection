<?php

namespace Drupal\extra_fields_collection\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\statistics\StatisticsViewsResult;
use Drupal\statistics\StatisticsStorageInterface;

/**
 * Extra field Display for statistics.
 *
 * @ExtraFieldDisplay(
 *   id = "statistics",
 *   label = @Translation("Statistics"),
 *   bundles = {
 *     "node.*"
 *   },
 *   weight = -30,
 *   visible = false
 * )
 */
class Statistics extends ExtraFieldDisplayBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The statistic storage service object.
   *
   * @var \Drupal\statistics\StatisticsStorageInterface
   */
  protected $statistics;

  /**
   * Constructs a ExtraFieldDisplayFormattedBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\statistics\StatisticsStorageInterface
   *   The statistic storage service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, StatisticsStorageInterface $statistics) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->statistics = $statistics;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $container->get('statistics.storage.node')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity) {
    $elements = [];

    /** @var \Drupal\statistics\StatisticsViewsResult $statistics */
    $statistics = $this->statistics->fetchView($entity->id());
    if ($statistics instanceof StatisticsViewsResult) {
      $elements = [
        'totalcount' => [
          '#lazy_builder' => ['extra_fields_collection.statistics_lazy_builder:getEntityViewCount', [$entity->id()]],
          '#create_placeholder' => TRUE,
          '#cache' => [
            'keys' => ['extra_fields_collection', 'statistics_lazy_builder', $entity->getEntityType()->id(), $entity->id()],
            // Cache the results of the lazy_builder for 10 sec.
            'max-age' => 10,
            'contexts' => [
              'languages'
            ],
          ],
        ],
      ];
    }

    return $elements;
  }

}
