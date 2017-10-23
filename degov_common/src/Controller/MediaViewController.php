<?php
/**
 * Created by PhpStorm.
 * User: onexinternet
 * Date: 20.10.17
 * Time: 12:39
 */

namespace Drupal\degov_common\Controller;


use Drupal\Core\Entity\Controller\EntityViewController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\FieldableEntityInterface;

/**
 * Class MediaViewController
 *
 * @package Drupal\degov_common\Controller
 */
class MediaViewController extends EntityViewController {

  /**
   * Pre-render callback to build the page title.
   *
   * @param array $page
   *   A page render array.
   *
   * @return array
   *   The changed page render array.
   */
  public function buildTitle(array $page) {
    $entity_type = $page['#entity_type'];
    $entity = $page['#' . $entity_type];
    // If the entity's label is rendered using a field formatter, set the
    // rendered title field formatter as the page title instead of the default
    // plain text title. This allows attributes set on the field to propagate
    // correctly (e.g. RDFa, in-place editing).
    if ($entity instanceof FieldableEntityInterface) {
      $label_field = $entity->getEntityType()->getKey('label');
      if (isset($page[$label_field])) {
        $page['#title'] = $this->renderer->render($page[$label_field]);
      }
      if ($entity->hasField('field_title') && !$entity->get('field_title')->isEmpty() && isset($page['field_title'])) {
        $page['#title'] = $this->renderer->render($page['field_title']);
      }
    }
    return $page;
  }

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $media, $view_mode = 'full', $langcode = NULL) {
    $build = parent::view($media, $view_mode, $langcode);
    return $build;
  }

}