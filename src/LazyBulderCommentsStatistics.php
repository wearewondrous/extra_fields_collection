<?php

namespace Drupal\extra_fields_collection;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\comment\CommentStatisticsInterface;

/**
 * Class LazyBulderCommentsStatistics.
 */
class LazyBulderCommentsStatistics {

  /**
   * Drupal\statistics\NodeStatisticsDatabaseStorage definition.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Comments statistics service
   *
   * @var \Drupal\comment\CommentStatisticsInterface
   */
  protected $commentStatistics;

  /**
   * Constructs a new LazyBuilders object.
   */
  public function __construct(EntityManagerInterface $entity_manager, CommentStatisticsInterface $comment_statistics) {
    $this->commentStatistics = $comment_statistics;
    $this->entityManager = $entity_manager;
  }

  /**
   * #lazy_builder callback; builds the total comment count render array.
   *
   * @param string $entity_type
   *   The entity type ID.
   *
   * @param string $entity_id
   *   The entity ID.
   *
   * @return array
   *   A renderable array containing the total count.
   */
  public function getEntityCommentCount($entity_type, $entity_id) {
    $comments_count = 0;
    $entity = $this->entityManager->getStorage($entity_type)->load($entity_id);
    if ($comments_statistics = $this->commentStatistics->read([$entity->id() => $entity], $entity->getEntityTypeId())) {
      foreach ($comments_statistics as $record) {
        if (isset($record->entity_id) && $record->entity_id == $entity->id()) {
          $comments_count = $record->comment_count;
        }
      }
    }

    return  [
      '#markup' => $comments_count,
    ];
  }

}
