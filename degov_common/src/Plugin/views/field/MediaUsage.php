<?php

namespace Drupal\degov_common\Plugin\views\field;


use Drupal\entity_reference_integrity\EntityReferenceDependencyManagerInterface;
use Drupal\media_entity\MediaInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Field handler to present a link to the subscriber.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("degov_media_usage")
 */
class MediaUsage extends FieldPluginBase {

  /**
   * @var EntityReferenceDependencyManagerInterface
   */
  protected $entityReferenceDependencyManager;

  /**
   * MediaUsage constructor.
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\entity_reference_integrity\EntityReferenceDependencyManagerInterface $entityReferenceDependencyManager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityReferenceDependencyManagerInterface $entityReferenceDependencyManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityReferenceDependencyManager = $entityReferenceDependencyManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_reference_integrity.dependency_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->addAdditionalFields();
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    if ($media = $this->getEntity($values)) {
      if ($media instanceof MediaInterface) {
        if ($this->entityReferenceDependencyManager->hasDependents($media)) {
          $referencing_entities = $this->entityReferenceDependencyManager->getDependentEntities($media);

          foreach ($referencing_entities as $entity_type_id => $entities) {
            $build[$entity_type_id]['list'] = [
              '#title' => reset($entities)->getEntityType()->getLabel(),
              '#theme' => 'item_list',
              '#items' => [],
            ];

            /* @var \Drupal\Core\Entity\EntityInterface $entity */
            foreach ($entities as $entity) {
              $build[$entity_type_id]['list']['#items'][] = $entity->hasLinkTemplate('canonical') ? $entity->toLink() : $entity->label();
            }
          }

          if (!empty($build)) {
            return $this->renderer->render($build);
          }
        }
      }
    }

    return '';
  }

}