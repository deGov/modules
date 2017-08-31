<?php
namespace Drupal\degov_vsm_search\Controller;

/**
 * VSM API controller.
 */
class VSMAPI {

  /**
   * The VSM GSA Search API Endpoint URL.
   */
  const GSA_API = 'http://vsm.d-nrw.de/gsa/searchresult';

  /**
   * The default area value.
   */
  const API_DEFAULT_AREA = 'kommunenundkreise';

  /**
   * Gets the search results from the API.
   *
   * @param string $q The search term.
   * @param int $start The start position of the results.
   * @param int $limit The number of results to show per page.
   * @param string $area The VSM search collection (http://vsm.d-nrw.de/download/2017-05-09_benennung_vsm-collections.pdf)
   * @param array $options The options array to send to the API.
   *
   * @return array Returns the results array.
   */
  public static function getResults(string $q, int $start, int $limit, string $area = self::API_DEFAULT_AREA, array $options = []): array {

    $options['q'] = $q;
    $options['start'] = $start;
    $options['num'] = $limit;
    $options['area'] = $area;

    // Remove all filters that have not been set
    $options = array_filter($options, function ($var) {
      return !empty($var);
    });

    $resp = self::fetchAPI($options);
    $arr = self::getJSONFromXML($resp);

    return $arr;
  }

  /**
   * Formats the XML result string to an PHP array.
   *
   * @param string $str The XML result string.
   *
   * @return array The XML result string as PHP array.
   */
  protected static function getJSONFromXML(string $str): array {

    $str = str_replace(["\n", "\r", "\t"], '', $str);
    $str = trim(str_replace('"', "'", $str));
    $xml = simplexml_load_string($str);
    $json = json_encode((array) $xml);
    $arr = json_decode($json, TRUE);

    return $arr;
  }

  /**
   * Fetches the API with the givne options.
   *
   * @param array $options The API options array.
   *
   * @return string Returns the XML string from the API.
   */
  protected static function fetchAPI(array $options): string {

    $qs = http_build_query($options);
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, self::GSA_API . '?' . $qs);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $resp = curl_exec($ch);

    if(empty($resp) === true) {
      $resp = '';
    }

    curl_close($ch);

    return $resp;
  }
}
