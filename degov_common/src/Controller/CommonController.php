<?php

namespace Drupal\degov_common\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommonController extends ControllerBase {

  /**
   * Returns an array of allowed view modes by given entity type and id.
   *
   * @param string $entity_type
   *   Entity type that needs is checked for allowed view modes.
   * @param string $entity_id
   *   Entity id that needs is checked for allowed view modes.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   An array of allowed view modes.
   */
  public function ajaxEntityLoad($entity_type, $entity_id) {
    $allowed_view_modes = [];
    // Retrieve the entity storage.
    $entity_storage = $this->entityTypeManager()->getStorage($entity_type);
    if (!empty($entity_storage)) {
      // Load the entity to get its bundle, so we can then load the view modes for this bundle.
      $entity = $entity_storage->load($entity_id);
      // Check the entity for enabled view modes.
      $query = $this->entityTypeManager()->getStorage('entity_view_display')->getQuery();
      $view_modes = $query->condition('bundle', $entity->bundle())
        ->execute();
      foreach ($view_modes as $view_mode) {
        $view_mode = explode('.', $view_mode);
        if (!empty($view_mode[2])) {
          $allowed_view_modes[] = $view_mode[2];
        }
      }
    }
    // Return response as JSON.
    return new JsonResponse($allowed_view_modes);
  }

  /**
   * Provides a generic title callback for a single entity.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Entity\EntityInterface $_entity
   *   (optional) An entity, passed in directly from the request attributes.
   *
   * @return string|null
   *   The title for the entity view page, if an entity was found.
   */
  public function mediaTitle(RouteMatchInterface $route_match, EntityInterface $_entity = NULL) {
    if ($entity = $this->doGetEntity($route_match, $_entity)) {
      if ($entity->hasField('field_title') && !$entity->get('field_title')->isEmpty()) {
        return $entity->get('field_title')->value;
      }
      return $entity->label();
    }
  }
}