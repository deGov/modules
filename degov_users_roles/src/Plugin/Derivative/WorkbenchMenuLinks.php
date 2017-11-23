<?php

namespace Drupal\degov_users_roles\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Derivative class that provides the menu links for workbench.
 */
class WorkbenchMenuLinks extends DeriverBase implements ContainerDeriverInterface {

  /**
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;

  /**
   * Creates a WorkbenchMenuLinks instance.
   *
   * @param $base_plugin_id
   * @param RouteProviderInterface $route_provider
   */
  public function __construct($base_plugin_id, RouteProviderInterface $route_provider) {
    $this->routeProvider = $route_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id,
      $container->get('router.route_provider')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $links = [];

    try {
      $this->routeProvider->getRouteByName('view.media.media_page_list');
      $links[] = [
          'title' => 'Media',
          'route_name' => 'view.media.media_page_list',
          'weight' => 15,
        ] + $base_plugin_definition;
    }
    catch (\Exception $e) {
      // Don't create the menu link if the route does not exist.
    }

    return $links;
  }
}
