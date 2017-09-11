<?php

namespace Drupal\degov_vsm_search\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * VSM Search controller.
 */
class VSMSearch extends ControllerBase {

  /**
   * The results to show per page.
   */
  const PER_PAGE = 25;

  /**
   * Fetches the VSM API and renders the result.
   *
   * @return array The drupal renderer array.
   */
  public function showSearch() {

    $total_count = 0;
    $q = \Drupal::request()->query->get('volltext');

    if (empty($q) === FALSE) {
      $results = VSMAPI::getResults($q, $this->getStartPosition(), self::PER_PAGE);
      $results = $this->simpleXMLFix($results);
      $total_count = intval($results['RES']['M']);
    }
    else {
      $results = [];
    }

    pager_default_initialize($total_count, self::PER_PAGE);

    $count_text = t('@count Treffer', [ '@count' => $total_count, ], ['langcode' => 'de']);
    if($total_count === 0) {
      $count_text = '';
    }

    $build = [
      '#theme' => 'vsm_search_results',
      '#results' => $results['RES']['R'],
      '#query' => $q,
      '#pager' => [
        '#type' => 'pager',
      ],
      '#lang' => [
        'result_count' => $count_text,
        'search' => t('Search'),
        'back' => t('Back'),
        'volltextsuche' => t('Volltextsuche', [], ['langcode' => 'de']),
        'empty_search' => t('Keine Treffer gefunden. Bitte überprüfen Sie Ihre Suchbegriffe oder versuchen weniger Begriffe.', [], ['langcode' => 'de']),

      ],
    ];

    return $build;
  }

  /**
   * Gets the start position for the next search position.
   *
   * @return int The start position for the next search position.
   */
  protected function getStartPosition() {
    $start = 0;
    $page = \Drupal::request()->query->get('page');
    if($page > 0) {
      $start = ($page * self::PER_PAGE);
    }
    return $start;
  }

  /**
   * Fix the R layer to always have numeric indexes.
   *
   * @param array $results The result array from the API call.
   *
   * @return array Returns the fixed results array.
   */
  protected function simpleXMLFix(array $results) {
    if (isset($results['RES']['R']) === TRUE && isset($results['RES']['R'][0]) === FALSE) {
      $tmp = $results['RES']['R'];
      $results['RES']['R'] = [];
      $results['RES']['R'][0] = $tmp;
    }
    return $results;
  }
}
