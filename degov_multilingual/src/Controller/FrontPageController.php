<?php

namespace Drupal\degov_multilingual\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultForbidden;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Routing\RouteMatch;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\degov_multilingual\DegovMultilingualFrontPage;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @param \Drupal\Core\Routing\RouteMatch $route_match
   *
   * @return \Drupal\Core\Access\AccessResult
   */
  public function access(AccountInterface $account, RouteMatch $route_match) {
    $operation = 'view';
    $route = $route_match->getRouteName();
    switch ($route) {
      case 'degov_multilingual.front_page.version_history':
        $operation = 'revisions';
        break;
      case 'degov_multilingual.front_page.edit_form';
        $operation = 'update';
        break;
      case 'degov_multilingual.front_page.delete_form';
        $operation = 'delete';
        break;
    }
    $node = $this->degovMultilingualFrontPage->getObject();
    if (!empty($node) && $node instanceof NodeInterface) {
      if ($operation == 'revisions') {
        return AccessResult::allowedIf($account->hasPermission('view ' . $node->bundle() . ' revisions'));
      }
      if ($node->access($operation, $account)) {
        return AccessResult::allowed();
      }
    } else {
      return AccessResultForbidden::forbidden();
    }
    // Check permissions and combine that with any custom access checking needed. Pass forward
    // parameters from the route and/or request as needed.
    return AccessResult::neutral();
  }

  /**
   * Redirect to corresponding revisionOverview page of node.
   */
  public function revisionOverview() {
    $node = $this->degovMultilingualFrontPage->getObject();
    if (!empty($node) && $node instanceof NodeInterface) {
      $url = Url::fromRoute('entity.node.version_history', ['node' => $node->id()]);
      $redirect = new RedirectResponse($url->toString());
      return $redirect;
    } else {
      throw new NotFoundHttpException();
    }
  }

  /**
   * Redirect to corresponding edit form.
   *
   * @return \Drupal\Core\Cache\CacheableRedirectResponse
   */
  public function edit() {
    $node = $this->degovMultilingualFrontPage->getObject();
    if (!empty($node) && $node instanceof NodeInterface) {
      $url = Url::fromRoute('entity.node.edit_form', ['node' => $node->id()]);
      $redirect = new RedirectResponse($url->toString());
      return $redirect;
    } else {
      throw new NotFoundHttpException();
    }
  }

  /**
   * Redirect to corresponding delete form.
   * @return \Drupal\Core\Cache\CacheableRedirectResponse
   */
  public function delete() {
    $node = $this->degovMultilingualFrontPage->getObject();
    if (!empty($node) && $node instanceof NodeInterface) {
      $url = Url::fromRoute('entity.node.delete_form', ['node' => $node->id()]);
      $redirect = new RedirectResponse($url->toString());
      return $redirect;
    } else {
      throw new NotFoundHttpException();
    }
  }
}