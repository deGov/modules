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
  Drupal.behaviors.audio = {
    attach: function (context, settings) {
      $('.audio__transcription').once('audio-js').each(function(){
        $('.audio__transcription__header').click(function(){
          $('.audio__transcription__body').slideToggle();
          $('i', this).toggleClass('fa-caret-right fa-caret-down');
        });
      });
    }
  }

})(jQuery, Drupal, drupalSettings);