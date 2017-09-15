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
class DurationFormatter extends FormatterBase
{

    public function _convertSecondsToReadableTime($item) {
        $seconds = $item->get('value')->getValue();
        if (!is_numeric($seconds)) {
          return '';
        }
        $zero    = new \DateTime("@0");
        $offset  = new \DateTime("@$seconds");
        $diff    = $zero->diff($offset);
        if ($diff && $diff->h) {
            return sprintf("%02d:%02d:%02d", $diff->days * 24 + $diff->h, $diff->i, $diff->s) . ' ' . $this->t('Hours');
        } else {
            return sprintf("%02d:%02d", $diff->i, $diff->s) . ' ' . $this->t('Minutes');
        }
    }

    /**
     * @param FieldItemListInterface $items
     * @param string $langcode
     * @return array
     */
    public function viewElements(FieldItemListInterface $items, $langcode) {
        $elements = array();
        // we will loop only through one element because it is supposed to
        // extract the values from all the children in the first run
        foreach ($items as $delta => $item) {
            $elements[$delta] = array(
                '#markup' => $this->_convertSecondsToReadableTime($item),
            );
            break;
        }

        return $elements;
    }
}