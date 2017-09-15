/**
 * @file video.js
 *
 * Defines the behavior of the media bundle video.
 */
(function ($, Drupal, drupalSettings) {

  'use strict';

  /**
   * Behavior for Video Transcript acordion
   */
  Drupal.behaviors.video = {
    attach: function (context, settings) {
      $('.video__transcription').once('video-js').each(function(){
        $('.video__transcription__header').click(function(){
          $('.video__transcription__body').slideToggle();
          $('i', this).toggleClass('fa-caret-right fa-caret-down');
        });
      });
    }
  }

})(jQuery, Drupal, drupalSettings);