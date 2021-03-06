<?php

namespace Drupal\degov_common;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\entity_reference_integrity\EntityReferenceDependencyManagerInterface;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The entity_reference_integrity module does not prevent the removal of media
 * entities that are referenced by other entities during batch deletion.
 * This class adds the missing functionality.
 */
class MediaFormAlter extends \Drupal\entity_reference_integrity_enforce\FormAlter {

  /**
   * The array of media entities to delete.
   *
   * @var string[][]
   */
  protected $entityInfo = [];

  /**
   * The tempstore factory.
   *
   * @var \Drupal\user\PrivateTempStoreFactory
   */
  protected $tempStoreFactory;

  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * Current user object.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityReferenceDependencyManagerInterface $calculator, $enabled_entity_type_ids, PrivateTempStoreFactory $temp_store_factory, EntityTypeManagerInterface $manager, AccountInterface $current_user) {
    $this->dependencyManager = $calculator;
    $this->enabledEntityTypeIds = $enabled_entity_type_ids;
    $this->storage = $manager->getStorage('media');
    $this->tempStoreFactory = $temp_store_factory;
    $this->currentUser = $current_user;
    parent::__construct($calculator, $enabled_entity_type_ids);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_reference_integrity.dependency_manager'),
      $container->get('config.factory')->get('entity_reference_integrity_enforce.settings')->get('enabled_entity_type_ids'),
      $container->get('user.private_tempstore'),
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * Implements hook_form_alter().
   */
  public function formAlter(&$form, FormStateInterface $form_state, $form_id) {
    $this->entityInfo = $this->tempStoreFactory->get('media_multiple_delete_confirm')->get($this->currentUser->id());
    /** @var \Drupal\media_entity\MediaInterface[] $entities */
    $entities = $this->storage->loadMultiple(array_keys($this->entityInfo));
    foreach ($entities as $entity) {
      if (in_array($entity->getEntityTypeId(), $this->enabledEntityTypeIds, TRUE) && $this->dependencyManager->hasDependents($entity)) {
        $referencing_entities = $this->dependencyManager->getDependentEntities($entity);
        if (count($entities) > 1) {
          drupal_set_message($this->t('You can not delete the entities as this are being referenced by another entity.'), 'warning');
        }
        else {
          drupal_set_message($this->t('You can not delete this as it is being referenced by another entity.'), 'warning');
        }
        $form['actions']['submit']['#disabled'] = TRUE;
        $form['referencing_entities_list'][] = [
          '#weight' => -10,
          'explanation' => [
            '#prefix' => '<p><i>',
            '#plain_text' => $entity->label(),
            '#suffix' => '</i><p>',
          ],
          'entities' => $this->buildReferencingEntitiesList($referencing_entities),
          '#suffix' => '<br/>',
        ];
        $form['entities']['#access'] = FALSE;
      }
    }
  }
}
