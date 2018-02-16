<?php

namespace Drupal\degov_node_press\Plugin\views\area;


use Drupal\calendar\CalendarHelper;
use Drupal\calendar\Plugin\views\area\CalendarHeader;

/**
 * Views area Calendar Header area.
 *
 * @ingroup views_area_handlers
 *
 * @ViewsArea("degov_calendar_press_widget_header")
 */
class CalendarPressWidgetHeader extends CalendarHeader {

  /**
   * Render a text area with \Drupal\Component\Utility\Xss::filterAdmin().
   */
  public function renderTextField($value) {
    if ($value) {
      return $this->sanitizeValue($this->tokenizeValue($value), 'xss_admin');
    }
    /** @var \Drupal\calendar\DateArgumentWrapper $argument */
    $argument = CalendarHelper::getDateArgumentHandler($this->view);
    $dateArgFormat = $argument->getArgFormat();
    $date = $argument->getDateArg()->argument;
    if (!empty($dateArgFormat) && !empty($date)) {
      $timestamp = date_create_from_format($dateArgFormat, $date)->getTimestamp();
      return \Drupal::service('date.formatter')->format($timestamp, 'custom', 'M Y');
    }
    return $argument->format('M Y');
  }
}