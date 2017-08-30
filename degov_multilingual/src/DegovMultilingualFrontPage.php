<?php

namespace Drupal\degov_multilingual;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\node\NodeInterface;

/**
 * Class DegovMultilingualFrontPage
 *
 * @package Drupal\degov_multilingual
 */
class DegovMultilingualFrontPage {

  /**
   * @var int
   */
  const NOT_FOUND = 1;

  /**
   * @var int
   */
  const ACCESS_DENIED = 2;

  /**
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * DegovMultilingualFrontPage constructor.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   */
  public function __construct(LanguageManagerInterface $languageManager, EntityTypeManagerInterface $entityTypeManager, ConfigFactoryInterface $configFactory) {
    $this->languageManager = $languageManager;
    $this->entityTypeManager = $entityTypeManager;
    $this->configFactory = $configFactory;
  }

  /**
   * Get the front page build.
   *
   * @return int|array
   */
  public function getBuild() {
    $front_pages = $this->configFactory
      ->get('degov_multilingual.settings')
      ->get('front_pages');
    $language = $this->languageManager
      ->getCurrentLanguage()
      ->getId();
    $default_language = $this->languageManager
      ->getDefaultLanguage()
      ->getId();

    if (isset($front_pages[$language])) {
      $nid = $front_pages[$language];
    }
    elseif (isset($front_pages[$default_language])) {
      $nid = $front_pages[$default_language];
    }

    if (!empty($nid)) {
      $node = $this->entityTypeManager
        ->getStorage('node')
        ->load($nid);

      if (!empty($node) && $node instanceof NodeInterface) {
        if ($node->access('view')) {
          $build = $this->entityTypeManager
            ->getViewBuilder('node')
            ->view($node);
          $build['#cache']['tags'][] = 'degov_multilingual_front_page';
          return $build;
        }
        else {
          return self::ACCESS_DENIED;
        }
      }
    }
    else {
      return self::NOT_FOUND;
    }
  }

}