<?php

namespace Drupal\degov_views_helper\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\node\NodeInterface;
use Drupal\views\Entity\View;
use Drupal\viewsreference\Plugin\Field\FieldFormatter\ViewsReferenceFieldFormatter;

/**
 * Field formatter for Viewsreference Field.
 *
 * @FieldFormatter(
 *   id = "degov_viewsreference_formatter",
 *   label = @Translation("Views Reference with extra options"),
 *   field_types = {"viewsreference"}
 * )
 */
class ViewsReferenceOverridenFieldFormatter extends ViewsReferenceFieldFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $view_name = $item->getValue()['target_id'];
      $display_id = $item->getValue()['display_id'];
      $argument = $item->getValue()['argument'];
      $title = $item->getValue()['title'];

      $extra_data = unserialize($item->getValue()['data']);
      /** @var \Drupal\views\Entity\View $view_object */
      $view_object = View::load($view_name);
      /** @var \Drupal\views\ViewExecutable $view */
      $view = $view_object->getExecutable();
      // Someone may have deleted the View.
      if (!is_object($view)) {
        continue;
      }

      // Get the view display.
      $display = $view_object->getDisplay($display_id);
      // Contexual arguments if they are not overriden are set only in default.
      if (empty($display['display_options']['arguments'])) {
        // If no arguments found try to get the ones from default display.
        $display = $view_object->getDisplay('default');
      }
      // Try to load the view arguments configuration.
      if (!empty($display['display_options']['arguments'])) {
        $view_arguments_config = [];
        foreach ($display['display_options']['arguments'] as $index => $argument_config) {
          $view_arguments_config[] = $argument_config;
        }
      }
      // load the arguments from the field storage.
      $arguments = [$argument];
      if (preg_match('/\//', $argument)) {
        $arguments = explode('/', $argument);
      }
      /** @var \Drupal\node\NodeInterface $node */
      $node = \Drupal::routeMatch()->getParameter('node');
      $token_service = \Drupal::token();
      if (is_array($arguments)) {
        foreach ($arguments as $index => $argument) {
          // Check if there are any tokens that need to be replaced.
          if (!empty($token_service->scan($argument))) {
            $arguments[$index] = $token_service->replace($argument, ['node' => $node]);
          }
          // If the argument is not set in the field set the exception value.
          if ($argument == '' && !empty($view_arguments_config[$index])) {
            if (!empty($view_arguments_config[$index]['exception']['value'])) {
              $arguments[$index] = $view_arguments_config[$index]['exception']['value'];
            } else {
              $arguments[$index] = 0;
            }
            // If there is a default value for the node, set it - we have the node object.
            if ($view_arguments_config[$index]['default_argument_type'] == 'node' && $node instanceof NodeInterface) {
              $arguments[$index] = $node->id();
            }
          }
        }
      }
      // Set the view display and arguments.
      $view->setDisplay($display_id);
      $view->setArguments($arguments);
      // Set number of available elements.
      if (!empty($extra_data['page_limit']) && is_numeric($extra_data['page_limit'])) {
        $limit = (int) $extra_data['page_limit'];
        $view->setItemsPerPage($limit);
      }
      // Build and execute the view.
      $view->build($display_id);
      $view->preExecute();
      $view->execute($display_id);
      // If the view mode is set in field settings set it for the view display.
      if (!empty($extra_data['view_mode'])) {
        if (!$view->rowPlugin->usesFields() && !empty($view->rowPlugin->options['view_mode'])) {
          $view->rowPlugin->options['view_mode'] = $extra_data['view_mode'];
          // Add view mode to the cache keys, so the renderable array could be safely cached.
          $view->element['#cache']['keys'][] = $extra_data['view_mode'];
        }
      }
      if (!empty($view->result) || !empty($view->empty)) {
        if ($title) {
          $title = $view->getTitle();
          $title_render_array = [
            '#theme' => 'viewsreference__view_title',
            '#title' => $this->t('@title', ['@title' => $title]),
          ];
        }

        if ($this->getSetting('plugin_types')) {
          if ($title) {
            $elements[$delta]['title'] = $title_render_array;
          }
        }
        // Create renderable array.
        $elements[$delta]['contents'] = $view->buildRenderable($display_id);
      }
    }

    return $elements;
  }

}
