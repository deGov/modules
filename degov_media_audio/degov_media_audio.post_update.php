<?php

/**
 * Migrate field_audio_caption to field_title.
 */
function degov_media_audio_post_update_migrate_field_title(&$sandbox) {
  // Initialize some variables during the first pass through.
  if (!isset($sandbox['total'])) {
    $max = \Drupal::entityQuery('media')
      ->condition('bundle', 'audio')
      ->count()
      ->execute();
    $sandbox['total'] = $max;
    $sandbox['current'] = 0;
  }

  $media_per_batch = 50;

  // Handle one pass through.
  $mids = \Drupal::entityQuery('media')
    ->condition('bundle', 'audio')
    ->range($sandbox['current'], $sandbox['current'] + $media_per_batch)
    ->execute();
  $medias = \Drupal\media_entity\Entity\Media::loadMultiple($mids);
  foreach($medias as $media) {
    $caption = $media->get('field_audio_caption')->getValue();
    $media->set('field_title', $caption);
    $media->save();
    $sandbox['current']++;
  }

  $sandbox['#finished'] = ($sandbox['current'] / $sandbox['total']);
  return t($sandbox['current'] . ' media processed.');
}
