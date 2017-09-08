/**
 * @file nrw_menu.js
 *
 * Defines the behavior of the NRW Menu module.
 */
(function ($, Drupal) {

  'use strict';

  /**
   * Collapsible/Expandable menu behaviour.
   */
  Drupal.behaviors.nrw_menu = {
    attach: function (context, settings) {
      // Do nothing if there is no menu__header on the page.
      if ($('.nrw-menu__header', context).length == 0) {
        return;
      }

      // Add class to first icon menu item as initially that is active
      $('.nrw-menu-header__icon:first-child').addClass('is-active');

      // Hover in second level to open menu.
      var $menu_col = $('.nrw-menu-header__col');
      $menu_col.find('a').on('focusin', function(e) {
        openNrwMenu(e, $(this).closest('.nrw-menu-header__col'));
      });
      $menu_col.hoverIntent(function (e) {
        openNrwMenu(e, $(this));
      });

      // Click on first level to open menu.
      $('.nrw-menu-header__icon').click(function () {
        var classes = $(this).prop("classList");
        var lastEl = classes[0];

        $('.nrw-menu-header__icon').removeClass('is-active');
        $(this).addClass('is-active');

        $('.nrw-menu-header__col').removeClass('is-active');
        if ($('.nrw-menu-header__col').hasClass(lastEl)) {
          $('.nrw-menu-header__col.' + lastEl).addClass('is-active');
        }

      });
      // Close the menu on click.
      $('.nrw-menu__content-close a', context).click(function (e) {
        $(this).closest('.nrw-menu-header__col').removeClass('is-open');
        $(this).closest('.nrw-menu-header__content').removeClass('is-expanded');
      });
      // Close the menu when clicking outside of the menu.
      $('body').click(function(e) {
        if ($(e.target).closest('.header__menu').length === 0) {
          closeNrwMenu();
        }
      });
    }
  };

  /**
   * Opens the nrw menu
   *
   * @param e Event handler
   * @param $elm Sub menu element
   */
  function openNrwMenu(e, $elm) {
    if ($(e.target).hasClass('link--nolink')) {
      return;
    }
    // First reset the menu, by closing it.
    closeNrwMenu();
    // Open the new focused menu tab.
    $elm.addClass('is-open');
    $elm.find('.nrw-menu-header__content').addClass('is-expanded');
  }

  /**
   * Closes the nrw menu.
   *
   * The menu closes by resetting all classes related to an active menu tab.
   */
  function closeNrwMenu() {
    $('.nrw-menu-header__col').removeClass('is-open').find('.nrw-menu-header__content').removeClass('is-expanded');
  }

  /**
   * Adds double tab menu functionality.
   */
  Drupal.behaviors.doubleTap = {
    attach: function (context, settings) {
      $('.nrw-menu-header__icon').each(
        function (i) {
          var classes = this.className.split(/\s+/);
          for (var i = 0, len = classes.length; i < len; i++) {
            if ($('.nrw-menu-header__col').hasClass(classes[i])) {
              $(this).addClass('has-children');
            }
          }
        });
      $('.has-children').doubleTapToGo();
      $('.nrw-menu__header-link-area').doubleTapToGo();
    }
  };

  /**
   * Adds responsive menu behaviour.
   */
  Drupal.behaviors.responsiveMenu = {
    attach: function (context, settings) {
      $('.header__menu-icon').click(function () {
        $(this).toggleClass('is-open');
        $('.nrw-menu-header-responsive').toggleClass('is-open');
      });
      $('.nrw-menu-header-responsive .nrw-menu-header-responsive__block-title').click(function () {
        $(this).toggleClass('is-close is-open');
        $(this).siblings('.nrw-menu-header-responsive__content').toggleClass('is-close is-open');
      });
      $('.nrw-menu-header-responsive .action').click(function () {
        $(this).parent().siblings('.nrw-menu-header-responsive__list').toggleClass('is-open');
        $(this).toggleClass('is-open');
      });
    }
  };

})(jQuery, Drupal);
