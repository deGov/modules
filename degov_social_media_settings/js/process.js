/**
 * @file process.js
 *
 * Defines the behaviors of the degov_social_media_settings module
 */
(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.degov_social_media_settings = {
    attach: function (context) {
      // Initialize when cookies are accepted by eu_cookie_compliance module.
      $('.agree-button', context).once('social-media-settings').click(function () {
        initializeSettings();

        $('.js-social-media-wrapper').each(function () {
          applySettings($(this));
        })
      });

      // Open the modal.
      $('.js-social-media-settings-open', context).once('social-media-settings').click(function (e) {
        e.preventDefault();
        openModal();
      });

      // Save the settings.
      $('.js-social-media-settings-save', context).once('social-media-settings').click(function () {
        saveSettings();
      });

      // Apply the settings.
      $('.js-social-media-wrapper', context).once('social-media-settings').each(function () {
        applySettings($(this));
      });

      $('.js-social-media-source-all').once('social-media-settings').click(function () {
        handleAll($(this));
      });
    }
  };

  var modal = $('#social-media-settings');
  var cookie = 'degov_social_media_settings';
  var settings = drupalSettings.degov_social_media_settings;
  var code = settings.code;
  var sources = { };

  // Shows the social media settings link if cookie are allowed and
  // creates a cookie with default values.
  function initializeSettings() {
    if (Drupal.eu_cookie_compliance !== undefined && Drupal.eu_cookie_compliance.hasAgreed()) {
      $('.js-social-media-settings-open').removeClass('hidden');

      if (cookieExists()) {
        sources = cookieGetSettings();
      }
      else {
        sources = settings.sources;
        cookieSaveSettings();
      }
    }
  }

  // Applies the social media settings to a social media wrapper.
  function applySettings(wrapper) {
    var source = wrapper.attr('data-social-media-source');
    var entity = wrapper.attr('data-social-media-entity');
    var target = $('.js-social-media-code', wrapper);

    // Show the code if source is enabled.
    if (sources.hasOwnProperty(source) && sources[source] === true && code.hasOwnProperty(entity)) {
      target.html(code[entity]);
    }
    else {
      if (Drupal.eu_cookie_compliance.hasAgreed()) {
        var link = $('<div class="js-social-media-code__message">' + settings.link + '</div>');

        $('.js-social-media-settings-open', link).click(function (e) {
          e.preventDefault();
          openModal();
        });

        target.html(link);
      }
      else {
        target.html(settings.cookie);
      }
    }
  }

  // Opens the social media settings modal.
  function openModal() {
    // Update checkboxes with settings from cookie.
    $('.js-social-media-source', modal).each(function () {
      var source = $(this).val();

      if (sources.hasOwnProperty(source)) {
        $(this).prop('checked', sources[source]);
      }
    });

    // Open the modal.
    modal.modal();
  }

  // Handle click on 'all' checkbox.
  function handleAll(all) {
    $('.js-social-media-source').prop('checked', all.is(':checked'));
  }

  // Saves the social media settings in the cookie and applies the new
  // settings to all social media wrappers.
  function saveSettings() {
    // Update the sources variable.
    $('.js-social-media-source', modal).each(function () {
      var source = $(this).val();

      if (sources.hasOwnProperty(source)) {
        sources[source] = $(this).is(':checked');
      }
    });

    // Save settings in cookie.
    if (Drupal.eu_cookie_compliance.hasAgreed()) {
      cookieSaveSettings();
    }

    // Apply new settings.
    $('.js-social-media-wrapper').each(function () {
      applySettings($(this));
    });
  }

  // Checks if the cookie exists.
  function cookieExists() {
    return typeof $.cookie(cookie) !== 'undefined';
  }

  // Reads, parses and returns the settings from the cookie.
  function cookieGetSettings() {
    return JSON.parse($.cookie(cookie));
  }

  // Saves the settings in the cookie.
  function cookieSaveSettings() {
    $.cookie(cookie, JSON.stringify(sources), { path: '/' });
  }

  // Initialize.
  initializeSettings();

})(jQuery, Drupal, drupalSettings);


