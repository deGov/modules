<?php

namespace Drupal\degov_common;


use DateInterval;
use Drupal\media_entity\Entity\Media;
use Drupal\video_embed_field\ProviderManager;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Class VideoUtils
 *
 * @package Drupal\degov_video
 */
class VideoUtils {

  /**
   * An http client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * @var \Drupal\video_embed_field\ProviderManager
   */
  protected $videoProviderManager;


  /**
   * Create a service class.
   *
   *   The plugin definition.
   * @param \GuzzleHttp\ClientInterface $http_client
   *    An HTTP client.
   * @param \Drupal\video_embed_field\ProviderManager $video_provider_manager
   *    Video provider manager.
   */
  public function __construct(ClientInterface $http_client, ProviderManager $video_provider_manager) {
    $this->httpClient = $http_client;
    $this->videoProviderManager = $video_provider_manager;
  }

  /**
   * Return the duration of Youtube video in seconds.
   *
   * @return int
   */
  public function getVideoDuration($media) {
    $duration = 0;
    if ($media instanceof Media) {
      if ($media->bundle() == 'video') {
        $embed_field = $media->get('field_media_video_embed_field')->getValue();
        $url = $embed_field[0]['value'];
        /** @var \Drupal\video_embed_field\ProviderPluginBase $videoProvider */
        $videoProvider = $this->videoProviderManager->loadProviderFromInput($url);
        $provider = $videoProvider->getPluginId();
        $videoId = $videoProvider->getIdFromInput($url);
        $method = 'get' . ucfirst($provider) . 'Duration';
        if (method_exists($this, $method)) {
          $duration = $this->$method($videoId, $url);
        }
      }
    }
    return $duration;
  }

  /**
   * Return the duration of Youtube video in seconds.
   *
   * @param $videoId
   *
   * @param $url
   *
   * @return int
   */
  private function getYoutubeDuration($videoId, $url = '') {
    $config = \Drupal::config('degov.default_settings');
    $key = $config->get('youtube_apikey');
    if ($key == '') {
      return 0;
    }
    $params = array(
      'part' => 'contentDetails',
      'id' => $videoId,
      'key' => $key,
      'time' => time(),
    );
    $query_url = 'https://www.googleapis.com/youtube/v3/videos?' . http_build_query($params);
    $request = $this->httpClient->request('GET', $query_url);
    if ($request->getStatusCode() == 200) {
      $result = new JsonDecode(TRUE);
      $details = $result->decode($request->getBody(), 'json');
      if (!empty($details['items'][0]['contentDetails'])) {
        $vinfo = $details['items'][0]['contentDetails'];
        $interval = new DateInterval($vinfo['duration']);
        return $interval->h * 3600 + $interval->i * 60 + $interval->s;
      }
    }
    return 0;
  }

  /**
   * Get duration of Vimeo video.
   *
   * @param $videoId
   * @param $url
   *
   * @return int
   */
  private function getVimeoDuration($videoId, $url) {
    $query_url = 'https://vimeo.com/api/oembed.json?url=' . $url;
    $request = $this->httpClient->request('GET', $query_url);
    if ($request) {
      $result = new JsonDecode(TRUE);
      $details = $result->decode($request->getBody(), 'json');
      if (!empty($details['duration'])) {
        return $details['duration'];
      }
    }
    return 0;
  }
}