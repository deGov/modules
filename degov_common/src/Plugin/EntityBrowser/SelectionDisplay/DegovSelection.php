<?php

namespace Drupal\degov_common\Plugin\EntityBrowser\SelectionDisplay;


use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_browser\Plugin\EntityBrowser\SelectionDisplay\MultiStepDisplay;

/**
 * Show current selection and delivers selected entities.
 *
 * @EntityBrowserSelectionDisplay(
 *   id = "degov_multi_step_display",
 *   label = @Translation("Multi step selection display for multi valued fields"),
 *   description = @Translation("Shows the current selection display, allowing to mix elements selected through different widgets in several steps."),
 *   acceptPreselection = TRUE,
 *   js_commands = TRUE
 * )
 */
class DegovSelection extends MultiStepDisplay {

  /**
   * {@inheritdoc}
   */
  public function getForm(array &$original_form, FormStateInterface $form_state) {
    $storage = &$form_state->getStorage();
    if (!empty($storage['entity_browser']['validators']['cardinality']['cardinality'])) {
      if ($storage['entity_browser']['validators']['cardinality']['cardinality'] == 1) {
        return [];
      }
    }
    return parent::getForm($original_form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array &$form, FormStateInterface $form_state) {
    $storage = &$form_state->getStorage();
    if (!empty($storage['entity_browser']['validators']['cardinality']['cardinality'])) {
      if ($storage['entity_browser']['validators']['cardinality']['cardinality'] == 1) {
        // Only finish selection if the form was submitted using main submit
        // element. This allows widgets to build multi-step workflows.
        if (!empty($form_state->getTriggeringElement()['#eb_widget_main_submit'])) {
          $this->selectionDone($form_state);
        }
      }
    }
    parent::submit($form, $form_state);
  }

}
