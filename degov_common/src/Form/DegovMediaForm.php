<?php

namespace Drupal\degov_common\Form;


use Drupal\lightning_media\Form\MediaForm;

/**
 * Class DegovMediaForm
 *
 * Add thumbnail regeneration flag on edit form, so always the current thumbnail
 * is shown.
 *
 * @package Drupal\degov_common\Form
 */
class DegovMediaForm extends MediaForm {

  /**
   * {@inheritdoc}
   */
  protected function prepareEntity() {
    parent::prepareEntity();
    $media = $this->entity;

    // If this is a new media, fill in the default values.
    if (!$media->isNew()) {
      $media->setQueuedThumbnailDownload();
    }
  }
}
