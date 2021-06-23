<?php

namespace Drupal\extra_fields_collection;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Modifies the language manager service.
 *
 * FYI, see https://www.drupal.org/docs/8/api/services-and-dependency-injection/altering-existing-services-providing-dynamic-services
 * ...if you want this service alteration to be recognized automatically, the
 * name of this class is required to be a CamelCase version of your module's
 * machine name followed by ServiceProvider
 */
class ExtraFieldsCollectionServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $modules = $container->getParameter('container.modules');
    if (isset($modules['statistics'])) {
      // Add a normalizer service for dynamic_entity_reference fields.
      $container->register('extra_fields_collection.statistics_lazy_builder', 'Drupal\extra_fields_collection\LazyBuildersStatistics')
        ->addArgument(new Reference(('statistics.storage.node')));
    }
    if (isset($modules['comment'])) {
      // Add a normalizer service for dynamic_entity_reference fields.
      $container->register('extra_fields_collection.comment_statistics_lazy_builder', 'Drupal\extra_fields_collection\LazyBulderCommentsStatistics')
        ->addArgument(new Reference(('entity_type.manager')))
        ->addArgument(new Reference(('comment.statistics')));
    }
  }
}
