<?php

namespace Drupal\degov_social_media_settings\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'SocialMediaSettingsBlock' block.
 *
 * @Block(
 *  id = "social_media_settings_block",
 *  admin_label = @Translation("Social media settings block"),
 * )
 */
class SocialMediaSettingsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['#theme'] = 'degov_social_media_settings_block';
    $build['#social_media_sources'] = degov_social_media_settings_sources();
    $build['#attached']['library'][] = 'degov_social_media_settings/process';
    $build['#attached']['drupalSettings']['degov_social_media_settings']['link'] = t('This social media source is disabled. You can enable it in the <a role="button" href="#" class="js-social-media-settings-open">social media settings</a>.');
    $build['#attached']['drupalSettings']['degov_social_media_settings']['cookie'] = t('This social media source is disabled. After accepting our cookie policy, you can enable it.');
    foreach ($build['#social_media_sources'] as $key => $value) {
      $build['#attached']['drupalSettings']['degov_social_media_settings']['sources'][$key] = FALSE;
    }
    return $build;
  }

}
