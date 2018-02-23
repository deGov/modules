/**
 * @file press.js
 *
 * Defines the behavior of the calendar widget.
 */
(function ($, Drupal, drupalSettings) {

  'use strict';

  /**
   * deGov node press functionality related to the press calendar widget.
   */
  Drupal.behaviors.degov_press = {
    attach: function (context, settings) {
      // Skip the behavior in case no calendar widget has been found on the page.
      if ($('.calendar-calendar', context).length == 0) {
        return;
      }

      /**
       * Fills the date filters on calendar widget click and submits the form.
       */
      $('.has-events .mini-day-on', context).click(function(){
        var view_wrapper = $(this).closest('.view-display-id-press_page_list');
        var dates = getFromToDates(this);
        $('input[name="from"]', view_wrapper).val(dates.from);
        $('input[name="to"]', view_wrapper).val(dates.to);
        $('form', view_wrapper).submit();
      });

      /**
       *  Redirect to the press listings page when clicking the calendar widget.
       */
      $('.has-events .mini-day-on', context).click(function() {
        var path = settings.degov_node_press.path;
        var dates = getFromToDates(this);
        window.location.replace(path + "?from=" + dates.from + "&to=" + dates.to);
      });

      /**
       * Returns an object with from and to date extracted from the calendar widget.
       *
       * @param element
       *   Clicked date element in the calendar widget.
       *
       * @returns {{from: string, to: string}}
       */
      function getFromToDates(element) {
        var parent = $(element).parent();
        var fromDate = parent.attr('id').replace('press_dates-', '');
        var toDate = new Date(fromDate);
        toDate.setDate(toDate.getDate() + 1);
        toDate = toDate.toISOString().substr(0, 10);
        return {from: fromDate, to: toDate};
      }
    }
  }

})(jQuery, Drupal, drupalSettings);
