/**
 * @file
 */

(function ($) {

  "use strict";

  Drupal.behaviors.degov_views_helper = {
    attach: function (context) {
      $('.viewreference_target_id', context).one('change', function(){
        var parent = $(this).closest('.field--type-viewsreference');
        $('.viewsreference_view_mode', parent).hide();
      });
      $('.viewreference_display_id', context).one('change', function(){
        var parent = $(this).closest('.field--type-viewsreference');
        $('.viewsreference_view_mode', parent).show();
      });
    }
  }

  Drupal.behaviors.degov_views_helper_display_id = {
    attach: function (context) {
      if ($('optgroup option').length == 0) {
        return;
      }
      $('.viewreference_display_id', context).trigger('change');
    }
  }

})(jQuery, drupalSettings);
