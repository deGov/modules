<?php
/**
 * Created by PhpStorm.
 * User: artem
 * Date: 15/09/2016
 * Time: 15:07
 */

namespace Drupal\degov_common\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;


/**
 * Plugin implementation of the 'hero_slider' formatter.
 *
 * @FieldFormatter(
 *   id = "duration",
 *   label = @Translation("Duration formatter"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class DurationFormatter extends FormatterBase {

  /**
   * Convert the seconds into human readable time.
   *
   * @param $item
   *
   * @return bool|string
   */
  public function _convertSecondsToReadableTime($item) {
    $seconds = $item->get('value')->getValue();
    if (!is_numeric($seconds) || !$seconds) {
      return FALSE;
    }
    $zero = new \DateTime("@0");
    $offset = new \DateTime("@$seconds");
    $diff = $zero->diff($offset);
    if ($diff && $diff->h) {
      return sprintf("%02d:%02d:%02d", $diff->days * 24 + $diff->h, $diff->i, $diff->s) . ' ' . $this->t('Hours');
    }
    else {
      return sprintf("%02d:%02d", $diff->i, $diff->s) . ' ' . $this->t('Minutes');
    }
  }

  /**
   * @param FieldItemListInterface $items
   * @param string $langcode
   *
   * @return array
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $duration = $this->_convertSecondsToReadableTime($item);
      // Add duration to render array only if it is not empty.
      if ($duration) {
        $elements[$delta] = [
          '#markup' => $duration,
        ];
      }
    }

    return $elements;
  }
}