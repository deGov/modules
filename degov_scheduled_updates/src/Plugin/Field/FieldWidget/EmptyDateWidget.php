<?php
/**
 * Created by PhpStorm.
 * User: onexinternet
 * Date: 18.10.17
 * Time: 17:15
 */

namespace Drupal\degov_scheduled_updates\Plugin\Field\FieldWidget;


use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Datetime\Plugin\Field\FieldWidget\TimestampDatetimeWidget;
use Drupal\Core\Form\FormStateInterface;


/**
 * Plugin implementation of the 'datetime timestamp' widget.
 *
 * @FieldWidget(
 *   id = "degov_datetime_timestamp",
 *   label = @Translation("Degov Datetime Timestamp"),
 *   field_types = {
 *     "timestamp",
 *   }
 * )
 */
class EmptyDateWidget extends TimestampDatetimeWidget {

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as &$item) {
      // @todo The structure is different whether access is denied or not, to
      //   be fixed in https://www.drupal.org/node/2326533.
      if (isset($item['value']) && $item['value'] instanceof DrupalDateTime) {
        $date = $item['value'];
      }
      elseif (isset($item['value']['object']) && $item['value']['object'] instanceof DrupalDateTime) {
        $date = $item['value']['object'];
      }
      else {
        $date = FALSE;
      }
      if ($date) {
        $item['value'] = $date->getTimestamp();
      } else {
        $item['value'] = NULL;
      }
    }
    return $values;
  }

}