<?php

namespace Drupal\degov_common;


use Drupal\Core\Cache\CacheFactoryInterface;
use Drupal\Core\Cache\CacheTagsChecksumInterface;
use Drupal\Core\Cache\DatabaseBackend;
use Drupal\Core\Database\Connection;
use Drupal\Core\Site\Settings;

/**
 * Class CacheBackendFactory
 *
 * This class helps to decide if memcached service is enabled and ready to be used,
 * if not standard database cache is used instead.
 *
 * @package Drupal\degov_common
 */
class DegovCacheBackendFactory implements CacheFactoryInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Site settings. We need this to check if memcached is supposed to be used as
   * cache backend. In settings.platform.php memcache_storage settings are only
   * set if the environmental variable has memcache relation in it, so it means
   * there is memcache server instance is available for current container.
   *
   * @var \Drupal\Core\Site\Settings
   */
  protected $settings;

  /**
   * The cache tags checksum provider.
   *
   * @var \Drupal\Core\Cache\CacheTagsChecksumInterface
   */
  protected $checksumProvider;

  /**
   * Constructs the DatabaseBackendFactory object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   Database connection
   * @param \Drupal\Core\Site\Settings $settings
   * @param \Drupal\Core\Cache\CacheTagsChecksumInterface $checksum_provider
   *   The cache tags checksum provider.
   */
  public function __construct(Connection $connection, Settings $settings, CacheTagsChecksumInterface $checksum_provider) {
    $this->connection = $connection;
    $this->checksumProvider = $checksum_provider;
    $this->settings = $settings;
  }

  /**
   * Gets a cache backend class for a given cache bin.
   *
   * @param string $bin
   *   The cache bin for which a cache backend object should be returned.
   *
   * @return \Drupal\Core\Cache\CacheBackendInterface
   *   The cache backend object associated with the specified bin.
   */
  public function get($bin) {
    // Check if there is memcache service available.
    $settings = $this->settings->get('memcache_storage');
    if (\Drupal::hasService('cache.backend.memcache_storage') && !empty($settings['memcached_servers'])) {
      /** @var \Drupal\memcache_storage\DrupalMemcachedInterface $memcachedFactory */
      $memcachedFactory = \Drupal::service('memcache_storage.factory');

      // Prepopulate each cache bin name with the specific prefix to have a clear
      // and human readable cache bin names everywhere.
      $bin_name = 'cache_' . $bin;

      // Get DrupalMemcache or DrupalMemcached object for the specified bin.
      $memcached = $memcachedFactory->get($bin_name);

      // Initialize a new object for a class that handles Drupal-specific part
      // of memcached cache backend.
      return new \Drupal\memcache_storage\MemcachedBackend($bin_name, $memcached, $this->settings, $this->checksumProvider);
    }
    // If there is no memcache use the Database Backend.
    // @TODO - maybe try to have this also configurable if any other cache backend should be used.
    return new DatabaseBackend($this->connection, $this->checksumProvider, $bin);
  }
}