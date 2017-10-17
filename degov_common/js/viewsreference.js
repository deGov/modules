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

})(jQuery, drupalSettings);
