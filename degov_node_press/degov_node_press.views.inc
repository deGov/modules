<?php

/**
 * Implements hook_views_data_alter().
 */
function degov_node_press_views_data_alter(array &$data) {
  $data['views']['degov_calendar_press_widget_header'] = array(
    'title' => t('Calendar Press Widget Header'),
    'help' => t('Calendar heading or pager.'),
    'area' => array(
      'id' => 'degov_calendar_press_widget_header',
    ),
  );
}