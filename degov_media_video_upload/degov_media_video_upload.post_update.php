<?php

/**
 * Migrate field_video_upload_caption to field_title.
 */
function degov_media_video_upload_post_update_migrate_field_title(&$sandbox) {
  // Initialize some variables during the first pass through.
  if (!isset($sandbox['total'])) {
    $max = \Drupal::entityQuery('media')
      ->condition('bundle', 'video_upload')
      ->count()
      ->execute();
    $sandbox['total'] = $max;
    $sandbox['current'] = 0;
  }

  $media_per_batch = 50;

  // Handle one pass through.
  $mids = \Drupal::entityQuery('media')
    ->condition('bundle', 'video_upload')
    ->range($sandbox['current'], $sandbox['current'] + $media_per_batch)
    ->execute();
  $medias = \Drupal\media_entity\Entity\Media::loadMultiple($mids);
  foreach($medias as $media) {
    $caption = $media->get('field_video_upload_caption')->getValue();
    $media->set('field_title', $caption);
    $media->save();
    $sandbox['current']++;
  }

  $sandbox['#finished'] = ($sandbox['current'] / $sandbox['total']);
  return t($sandbox['current'] . ' media processed.');
}
