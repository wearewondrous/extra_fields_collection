<?php

namespace Drupal\extra_fields_collection;
use Drupal\statistics\NodeStatisticsDatabaseStorage;

/**
 * Class LazyBuildersStatistics.
 */
class LazyBuildersStatistics {

  /**
   * Drupal\statistics\NodeStatisticsDatabaseStorage definition.
   *
   * @var \Drupal\statistics\NodeStatisticsDatabaseStorage
   */
  protected $statisticsStorageNode;
  /**
   * Constructs a new LazyBuilders object.
   */
  public function __construct(NodeStatisticsDatabaseStorage $statistics_storage_node) {
    $this->statisticsStorageNode = $statistics_storage_node;
  }

  /**
   * #lazy_builder callback; builds the total count render array.
   *
   * @param string $entity
   *   The entity type ID.
   *
   * @return array
   *   A renderable array containing the total count.
   */
  public function getEntityViewCount($entity_id) {
    /** @var \Drupal\statistics\StatisticsViewsResult $statistics */
    $statistics = $this->statisticsStorageNode->fetchView($entity_id);
    return  [
      '#markup' => $statistics->getTotalCount(),
    ];
  }

}
