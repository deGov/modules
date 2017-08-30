<?php

/**
 * @file Contains the drush commands.
 */

/**
 * Implements hook_drush_command().
 */
function degov_scheduled_updates_drush_command() {
  $items = [];
  $items['scheduled-updates'] = [
    'description' => t('Task to run scheduled updates with drush'),
    'aliases' => ['sch-updates']
  ];
  return $items;
}

/**
 * Implements hook_scheduled_updates().
 */
function drush_degov_scheduled_updates_scheduled_updates() {
  \Drupal::service('scheduled_updates.update_runner')->runAllUpdates();
}
