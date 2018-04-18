<?php

namespace Drupal\extra_fields_collection\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\comment\CommentStatisticsInterface;

/**
 * Extra field Display for statistics.
 *
 * @ExtraFieldDisplay(
 *   id = "comments_count",
 *   label = @Translation("Comment count"),
 *   bundles = {
 *     "node.*"
 *   },
 *   weight = -30,
 *   visible = false
 * )
 */
class CommentsCount extends ExtraFieldDisplayBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The comments statistic service object.
   *
   * @var \Drupal\comment\CommentStatisticsInterface
   */
  protected $comments_statistics;

  /**
   * Constructs a ExtraFieldDisplayFormattedBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\comment\CommentStatisticsInterface
   *   The comments statistic service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CommentStatisticsInterface $comments_statistics) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->comments_statistics = $comments_statistics;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $container->get('comment.statistics')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity) {
    // FYI comment_list as tag will not work here.
    // See Drupal\comment\Entity\Comment::postSave() when a comment is added the
    // comments module invalidates the caches based on the commented entity
    // cache tags.
    $tags = $entity->getCacheTagsToInvalidate();
    $elements = [
      'totalcount' => [
        '#lazy_builder' => [
          'extra_fields_collection.comment_statistics_lazy_builder:getEntityCommentCount',
          [$entity->getEntityTypeId(), $entity->id()]
        ],
        '#create_placeholder' => TRUE,
        // Cache the results until a new comment is added.
        '#cache' => [
          'keys' => [
            'extra_fields_collection',
            'comments_statistics_lazy_builder',
            $entity->getEntityType()->id(),
            $entity->id()
          ],
          'tags' => $tags,
          'contexts' => [
            'languages',
            'url.path'
          ],
        ],
      ],
    ];

    return $elements;
  }
}
