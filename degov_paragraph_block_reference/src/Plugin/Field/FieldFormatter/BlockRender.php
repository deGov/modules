<?php

namespace Drupal\degov_paragraph_block_reference\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;


/**
 * Field label formatter for Block Field.
 *
 * @FieldFormatter(
 *   id = "degov_block_render",
 *   label = @Translation("deGov Block label Display"),
 *   field_types = {"block_field"}
 * )
 */
class BlockRender extends FormatterBase {

  /**
   * Builds a renderable array for a field value.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The field values to be rendered.
   * @param string $langcode
   *   The language that should be used to render the field.
   *
   * @return array
   *   A renderable array for $items, as an array of child elements keyed by
   *   consecutive numeric indexes starting from 0.
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    /** @var \Drupal\block_field\BlockFieldManagerInterface $block_field_manager */
    $block_field_manager = \Drupal::service('block_field.manager');
    $definitions = $block_field_manager->getBlockDefinitions();
    foreach ($items as $delta => $item) {
      /** @var \Drupal\block_field\BlockFieldItemInterface $item */
      $block_instance = $item->getBlock();
      // Make sure the block exists and is accessible.
      if (!$block_instance || !$block_instance->access(\Drupal::currentUser())) {
        continue;
      }
      $title = $block_instance->getPluginId();
      if (!empty($definitions[$title])) {
        $category = (string) $definitions[$title]['category'];
        $label = $definitions[$title]['admin_label'];
        $title = $category . ': ' . $label;
      }
      $elements[$delta] = [
        '#markup' => $title,
      ];
    }
    return $elements;
  }
}
