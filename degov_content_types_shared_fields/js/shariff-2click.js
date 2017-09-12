/**
 * @file shariff-2click.js
 *
 * Changes the behavior of the Shariff sharing buttons in the header paragraph.
 */
(function ($, Drupal) {

  'use strict';

  let shariff_2click_applied = false;

  /**
   * Adds overlay for the shariff share buttons.
   */
  function shariff_2click(context) {
    let shariff_object = $('.shariff', context);
    if (shariff_object.length == 0) {
      return;
    }
    let services = JSON.parse(shariff_object.attr('data-services'));
    if (services.length != $('.sharing li', context).length) {
      return;
    }
    // Add an overlay to the sharing container to fake 2-click sharing buttons.
    $('.sharing li', context).once('sharing-overlay').each(function () {
      var sharing = $(this);
      var shariff = $('.shariff', sharing);
      var overlay = $('<div class="sharing-overlay"></div>').appendTo(sharing);


      // Sets shariff theme.
      var setTheme = function (theme) {

        var current_theme = shariff.attr('data-theme');

        if (current_theme !== theme) {
          sharing
            .removeClass('theme-grey')
            .addClass('theme-' + theme);
          shariff.attr('data-theme', theme)
        }
      };

      // Initialize shariff theme.
      setTheme('grey');

      // Switch shariff theme and remove overlay.
      overlay.click(function () {
        setTheme('colored');
        overlay.remove();
      });
      shariff_2click_applied = true;
    });
  }

  /**
   * Adds the behavior for adding 2 click procedure for sharing.
   */
  Drupal.behaviors.shariff_2click = {
    attach: function (context, settings) {
      if ($('.shariff', context).length == 0) {
        return;
      }
      let timerId = setTimeout(shariff_2click, 1000, context);
      if (shariff_2click_applied) {
        clearTimeout(timerId);
      }
    }
  };
})(jQuery, Drupal);
