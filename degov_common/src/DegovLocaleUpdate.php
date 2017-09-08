<?php

namespace Drupal\degov_common;

use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Class DegovLocaleUpdate
 *
 * @package Drupal\degov_common
 */
class DegovLocaleUpdate {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * DegovLocaleUpdate constructor.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   */
  public function __construct(ModuleHandlerInterface $module_handler) {
    $this->module_handler = $module_handler;
  }

  /**
   * Updates all translations of modules providing translation files.
   *
   * A folder 'translations' needs to be created under the module folder.
   * And needs to implement hook_locale_translation_projects_alter().
   */
  public function localeUpdate() {
    $this->module_handler->loadInclude('locale', 'fetch.inc');
    $this->module_handler->loadInclude('locale', 'bulk.inc');

    $langcodes = [];
    foreach (locale_translation_get_status() as $project_id => $project) {
      foreach ($project as $langcode => $project_info) {
        if (!empty($project_info->type)) {
          $langcodes[] = $langcode;
        }
      }
    }

    // Deduplicate the list of langcodes since each project may have added the
    // same language several times.
    $langcodes = array_unique($langcodes);

    // Set the translation import options. This determines if existing
    // translations will be overwritten by imported strings.
    $options = _locale_translation_default_update_options();
    locale_translation_clear_status();
    $batch = locale_translation_batch_update_build(array(), $langcodes, $options);
    batch_set($batch);
  }
}