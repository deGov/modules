/**
 * @file load_code.js
 *
 */
(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.degov_social_media_settings = {
    attach: function (context) {
      $('.social-media-source-disabled', context).once('social-media-source').each(function () {
        var wrapper = $(this);

        Drupal.degov_social_media_settings.applySettings(wrapper);

        wrapper.on('degov_social_media_settings', function() {
          Drupal.degov_social_media_settings.applySettings(wrapper);
        });
      });
    }
  };

  Drupal.degov_social_media_settings = {
    applySettings: function (wrapper) {
      var source = wrapper.attr('data-social-media-source');

      // Show the code if source is enabled.
      if (typeof settings !== 'undefined' && settings[source] === true) {
        var entity_id = wrapper.attr('data-entity-id');
        var target = $('.social-media-source-target', wrapper);
        target.html(drupalSettings.degov_social_media_settings.code[entity_id]);
        console.log('Revealed');
      }
      else  {
        console.log('Nahhh');
      }
    },

    getSettings: function() {
      // Create the settings cookie if it does not exist yet.
      if (typeof $.cookie('cookie-agreed') !== 'undefined' && typeof $.cookie('degov_social_media_settings') === 'undefined') {
        var value = JSON.stringify(drupalSettings.degov_social_media_settings.sources);
        $.cookie('degov_social_media_settings', value);
      }

      // Parse the settings and get the source type.
      var settings = JSON.parse($.cookie('degov_social_media_settings'));
    }
  }

})(jQuery, Drupal, drupalSettings);


