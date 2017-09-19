<?php

namespace Drupal\nrw_menu;

use Drupal\Core\Menu\MenuLinkDefault;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\simplify_menu\MenuItems as SimplifiedMenuItems;


/**
 * Class MenuItems.
 *
 * @package \Drupal\nrw_menu
 */
class MenuItems extends SimplifiedMenuItems {

  /**
   * Map menu tree into an array.
   *
   * @param array $links
   *   The array of menu tree links.
   * @param string $submenuKey
   *   The key for the submenu to simplify.
   *
   * @return array
   *   The simplified menu tree array.
   */
  protected function simplifyLinks(array $links, $submenuKey = 'submenu') {
    $result = [];
    foreach ($links as $item) {
      // get menu item definitions to get the entity id of the menu item
      $menuDefinition = $item->link->getPluginDefinition();
      // load the menu link content entity to get menu_extra value
      $extra = '';
      $menuItem = MenuLinkContent::load($menuDefinition['metadata']['entity_id']);
      $classes = $menuItem->getPluginDefinition();
      if ($menuDefinition instanceof MenuLinkContent) {
        // check if the value is set
        if (!$menuItem->get('menu_extra')->isEmpty()) {
          // create the proper markup with all the filters applied
          $extra = check_markup($menuItem->get('menu_extra')->value, $menuItem->get('menu_extra')->format);
        }
      }

      $simplifiedLink = [
        'text' => $item->link->getTitle(),
        'url' => $item->link->getUrlObject()->toString(),
        'description' => empty($item->link->getDescription()) ? $item->link->getTitle() : $item->link->getDescription(),
        'external' => $item->link->getUrlObject()->isExternal(),
        'menu_extra' => $extra, 
        'class' => $classes,
        'active_trail' => FALSE,
        'active' => FALSE
      ];

      $current_path = \Drupal::request()->getRequestUri();
      if ($current_path == $simplifiedLink['url']) {
        $simplifiedLink['active'] = TRUE;
      }

      $plugin_id = $item->link->getPluginId();
      if (isset($this->activeMenuTree[$plugin_id]) && $this->activeMenuTree[$plugin_id] == TRUE) {
        $simplifiedLink['active_trail'] = TRUE;
      }

      if ($item->hasChildren) {
        $simplifiedLink[$submenuKey] = $this->simplifyLinks($item->subtree);
      }
      $result[] = $simplifiedLink;
    }

    return $result;
  }

}

