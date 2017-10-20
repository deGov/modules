<?php

/**
 * Migrate field_audio_caption to field_title.
 *
 * @param $sandbox
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

  foreach($mids as $mid) {
    $media = \Drupal\media_entity\Entity\Media::load($mid);
    $caption = $media->get('field_audio_caption');
    $media->set('field_title', $caption);
    $media->save();
    $sandbox['current']++;
  }

  drupal_set_message($sandbox['current'] . ' media processed.');

  $sandbox['#finished'] = ($sandbox['current'] / $sandbox['total']);
}
