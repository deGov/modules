<?php

namespace Drupal\degov_scheduled_updates;


use Drupal\Core\Entity\EntityInterface;
use Drupal\scheduled_updates\ScheduledUpdateListBuilder;

class ScheduledUpdatesListBuilder extends ScheduledUpdateListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['content'] = $this->t('Content');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\scheduled_updates\Entity\ScheduledUpdate */
    // @TODO get the list of the content that is scheduled for update.
    // The method below doesn't return anything.
    $referencedEntities = $entity->getUpdateEntityIds();
    foreach ($referencedEntities as $referencedEntity) {

    }
    $row['content'] = '';

    return $row + parent::buildRow($entity);
  }
}
