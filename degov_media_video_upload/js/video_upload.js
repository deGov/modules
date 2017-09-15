/**
 * @file video_upload.js
 *
 * Defines the behavior of the media bundle video.
 */
(function ($, Drupal, drupalSettings) {

  'use strict';

  /**
   * Behavior for Video Transcript acordion
   */
  Drupal.behaviors.videoUpload = {
    attach: function (context, settings) {
      $('.video-upload__transcription').once('video-upload-js').each(function(){
        $('.video-upload__transcription__header').click(function(){
          $('.video-upload__transcription__body').slideToggle();
          $('i', this).toggleClass('fa-caret-right fa-caret-down');
        });
      });
    }
  }

})(jQuery, Drupal, drupalSettings);