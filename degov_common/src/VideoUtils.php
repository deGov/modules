<?php

namespace Drupal\degov_common;


use DateInterval;
use Drupal\Core\File\FileSystem;
use Drupal\media_entity\Entity\Media;
use Drupal\video_embed_field\ProviderManager;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use GetId3\GetId3Core as GetId3;

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
   * @var FileSystem
   */
  protected $fileSystem;
  /**
   * Create a service class.
   *
   *   The plugin definition.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *    An HTTP client.
   * @param \Drupal\video_embed_field\ProviderManager $video_provider_manager
   *    Video provider manager.
   * @param \Drupal\Core\File\FileSystem $file_system
   */
  public function __construct(ClientInterface $http_client, ProviderManager $video_provider_manager, FileSystem $file_system) {
    $this->httpClient = $http_client;
    $this->videoProviderManager = $video_provider_manager;
    $this->fileSystem = $file_system;
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
      if ($media->bundle() == 'video_upload') {
        $file_uri = '';
        if (!$media->get('field_video_upload_mp4')->isEmpty()) {
          $file_uri = $media->get('field_video_upload_mp4')->entity->getFileUri();
        } elseif (!$media->get('field_video_upload_webm')->isEmpty()) {
          $file_uri = $media->get('field_video_upload_webm')->entity->getFileUri();
        } elseif (!$media->get('field_video_upload_ogg')->isEmpty()) {
          $file_uri = $media->get('field_video_upload_ogg')->entity->getFileUri();
        }
        if ($file_uri != '') {
          $file_uri = $this->fileSystem->realpath($file_uri);
        }
        $getId3 = new GetId3();
        $id3Info = $getId3
          ->setOptionMD5Data(true)
          ->setOptionMD5DataSource(true)
          ->setEncoding('UTF-8')
          ->analyze($file_uri);
        if (isset($id3Info['error'])) {
          drupal_set_message(t('Error at reading audio properties from @uri with GetId3: @error.', ['uri' => $file_uri, 'error' => $id3Info['error']]));
        }
        if (!empty($id3Info['playtime_seconds'])) {
          $duration = (int) ceil($id3Info['playtime_seconds']);
        }
      }
      if ($media->bundle() == 'audio') {
        $file_uri = '';
        if (!$media->get('field_audio_mp3')->isEmpty()) {
          $file_uri = $media->get('field_audio_mp3')->entity->getFileUri();
        } elseif (!$media->get('field_audio_ogg')->isEmpty()) {
          $file_uri = $media->get('field_audio_ogg')->entity->getFileUri();
        }
        if ($file_uri != '') {
          $file_uri = $this->fileSystem->realpath($file_uri);
        }
        $getId3 = new GetId3();
        $id3Info = $getId3
          ->setOptionMD5Data(true)
          ->setOptionMD5DataSource(true)
          ->setEncoding('UTF-8')
          ->analyze($file_uri);
        if (isset($id3Info['error'])) {
          drupal_set_message(t('Error at reading audio properties from @uri with GetId3: @error.', ['uri' => $file_uri, 'error' => $id3Info['error']]));
        }
        if (!empty($id3Info['playtime_seconds'])) {
          $duration = (int) ceil($id3Info['playtime_seconds']);
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