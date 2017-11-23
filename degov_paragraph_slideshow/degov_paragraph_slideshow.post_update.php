<?php

/**
 * Migrate field_slideshow_type for slideshow paragraph.
 * Type 3 is now deprecated.
 */
function degov_paragraph_slideshow_post_update_migrate_type_3(&$sandbox) {
  // Initialize some variables during the first pass through.
  if (!isset($sandbox['total'])) {
    $sandbox['is_index'] = FALSE;
    $max = \Drupal::entityQuery('paragraph')
      ->condition('type', 'slideshow')
      ->condition('field_slideshow_type.value', 'type_3')
      ->count()
      ->execute();
    $sandbox['total'] = $max;
    $sandbox['current'] = 0;
  }

  $items_per_batch = 50;

  // Handle one pass through.
  $pids = \Drupal::entityQuery('paragraph')
    ->condition('type', 'slideshow')
    ->condition('field_slideshow_type.value', 'type_3')
    ->range($sandbox['current'], $sandbox['current'] + $items_per_batch)
    ->execute();
  $paragraphs = \Drupal\paragraphs\Entity\Paragraph::loadMultiple($pids);
  foreach($paragraphs as $paragraph) {
    $paragraph->set('field_slideshow_type', ['value' => 'type_2']);
    $paragraph->save();
    $sandbox['current']++;
  }

  return t($sandbox['current'] . ' paragraphs processed.');
}
