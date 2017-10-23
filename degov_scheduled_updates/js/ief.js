/**
 * @file
 */

(function ($) {

  "use strict";

  Drupal.behaviors.degov_scheduled_updates = {
    attach: function (context) {
      if ($('#scheduled-updates', context).length == 0) {
        return;
      }
      $('#scheduled-updates', context).trigger('click');
    }
  }

})(jQuery, drupalSettings);