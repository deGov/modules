<?php

namespace Drupal\degov_multilingual\Controller;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class FrontPageController.
 *
 * @package Drupal\degov_multilingual\Controller
 */
class FrontPageController extends ControllerBase {

  /**
   * Renders a node for the front_page route.
   *
   * @return array
   */
  public function render() {
    $front_pages = $this->config('degov_multilingual.settings')->get('front_pages');
    $language = $this->languageManager()->getCurrentLanguage()->getId();
    $default_language = $this->languageManager()->getDefaultLanguage()->getId();

    // Try to find a node to display.
    if (isset($front_pages[$language])) {
      $nid = $front_pages[$language];
    }
    elseif (isset($front_pages[$default_language])) {
      $nid = $front_pages[$default_language];
    }

    if (!empty($nid)) {
      $node = $this->entityTypeManager()->getStorage('node')->load($nid);
    }

    // If we have a node, check access and either throw an access denied
    // exception if access is not allowed or build and return a render array
    // if access was allowed. If we don't have a node, return a not found
    // exception.
    if (!empty($node) && $node instanceof NodeInterface) {
      if ($node->access('view')) {
        $build = $this->entityTypeManager()->getViewBuilder('node')->view($node);
        $build['#cache']['tags'][] = 'degov_multilingual_front_page';
        return $build;
      }
      else {
        throw new AccessDeniedHttpException();
      }
    }
    else {
      throw new NotFoundHttpException();
    }
  }

}