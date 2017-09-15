<?php

namespace Drupal\degov_video;


use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class VideoUtils
 *
 * @package Drupal\degov_video
 */
class VideoUtils implements ContainerFactoryPluginInterface {

  /**
   * Media entity for operation.
   *
   * @var \Drupal\media_entity\Entity\Media
   */
  protected $media;

  /**
   * An http client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('http_client'));
  }

  /**
   * Create a plugin with the given input.
   *
   * @param string $configuration
   *   The configuration of the plugin.
   * @param string $plugin_id
   *   The plugin id.
   * @param array $plugin_definition
   *   The plugin definition.
   * @param \GuzzleHttp\ClientInterface $http_client
   *    An HTTP client.
   *
   * @throws \Exception
   */
  public function __construct($configuration, $plugin_id, $plugin_definition, ClientInterface $http_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
  }

  /**
   * Video getter.
   *
   * @return \Drupal\media_entity\Entity\Media
   */
  public function getVideo() {
    return $this->media;
  }

  /**
   * @param \Drupal\media_entity\Entity\Media $media
   *
   * @return $this
   */
  public function setVideo($media) {
    $this->media = $media;
    return $this;
  }

  /**
   *
   */
  public function getVideoDuration() {
    //$this->httpClient->request('GET', $this->media->url());
  }
}