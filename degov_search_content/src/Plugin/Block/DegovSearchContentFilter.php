<?php

namespace Drupal\degov_search_content\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;


/**
 * Provides a block to filter the search.
 *
 * @Block(
 *   id = "degov_search_content_filter",
 *   admin_label = @Translation("DeGov search content filters")
 * )
 */
class DegovSearchContentFilter extends BlockBase {
  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form['filter'] = array(
      '#type' => 'details',
      '#title' => t('Filter search results'),
      '#title_display' => 'invisible',
      '#open' => TRUE,
      '#attributes' => ['class' => ['block-degov-search-content-filter']],
    );

    $ids = [
      'search_content_node_bundle',
      'search_content_tags',
      'search_content_section',
      'search_content_node_changed'
    ];
    foreach ($ids as $id) {
      $block = \Drupal\block\Entity\Block::load($id);
      $block->disable();
      if ($block) {
        $form['filter'][$id] = \Drupal::entityTypeManager()
          ->getViewBuilder('block')
          ->view($block);
      }
    }


    return $form;
  }
}
