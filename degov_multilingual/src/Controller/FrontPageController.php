<?php

namespace Drupal\degov_multilingual\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\degov_multilingual\DegovMultilingualFrontPage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class FrontPageController.
 *
 * @package Drupal\degov_multilingual\Controller
 */
class FrontPageController implements ContainerInjectionInterface {

  /**
   * @var \Drupal\degov_multilingual\DegovMultilingualFrontPage
   */
  protected $degovMultilingualFrontPage;

  /**
   * FrontPageController constructor.
   *
   * @param \Drupal\degov_multilingual\DegovMultilingualFrontPage $degovMultilingualFrontPage
   */
  public function __construct(DegovMultilingualFrontPage $degovMultilingualFrontPage) {
    $this->degovMultilingualFrontPage = $degovMultilingualFrontPage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('degov_multilingual.front_page')
    );
  }

  /**
   * Renders a node for the front_page route.
   *
   * @return array
   */
  public function render() {
    $build = $this->degovMultilingualFrontPage->getBuild();

    switch ($build) {
      case DegovMultilingualFrontPage::NOT_FOUND:
        throw new NotFoundHttpException();
        break;

      case DegovMultilingualFrontPage::ACCESS_DENIED:
        throw new AccessDeniedHttpException();
        break;

      default:
        return $build;
    }
  }

}