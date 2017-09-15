/**
 * @file slideshow.js
 *
 * Defines the behavior of the Slideshow paragraph.
 */
(function ($, Drupal, drupalSettings) {

  'use strict';

  /**
   * Initializes the slideshow paragraph with Slick.
   */
  Drupal.behaviors.slideshow = {
    attach: function (context, settings) {
      var $slideshow = $('.slideshow.default', context);
      if ($slideshow.length < 1) {
        return;
      }
      var $slider = $('.slideshow__slides', $slideshow);
      $slider.once().slick({
        dots: true,
        autoplay: false,
        speed: 500
      });

      $('.slick__pause', $slideshow).on('click', function () {
        $slider.slick('slickPause');
        $(this).hide().siblings('.slick__play').show().focus();
      }).hide();
      $('.slick__play', $slideshow).on('click', function () {
        $slider.slick('slickPlay');
        $(this).hide().siblings('.slick__pause').show().focus();
      }).show();

      // Slick slider for prev/next thumbnails images
      var $slideshow_prev_next = $('.slideshow-with-prev-next');
      var $slides_prev_next = $('.slides_prev_next', $slideshow_prev_next);
      $slides_prev_next.once().slick({
        dots: true,
        slidesToShow: 1,
        autoplay: false
      });
      setTimeout(function () {
        $slides_prev_next.prepend('<div class="prev-slick-img slick-thumb-nav"><img src="/prev.jpg" class="img-responsive"></div>').append('<div class="next-slick-img slick-thumb-nav"><img src="/next.jpg" class="img-responsive"></div>');
        get_prev_slick_img();
        get_next_slick_img();
      }, 500);

      $slides_prev_next.on('click', '.slick-prev', function () {
        get_prev_slick_img();
      });
      $slides_prev_next.on('click', '.slick-next', function () {
        get_next_slick_img();
      });
      $slides_prev_next.on('swipe', function (event, slick, direction) {
        if (direction == 'left') {
          get_prev_slick_img();
        }
        else {
          get_next_slick_img();
        }
      });

      function get_prev_slick_img() {
        var $slick_current = $('.slick-current');
        // For prev img
        var prev_slick_img = $slick_current.prev('.slide').find('.slide__media img').attr('src');
        $('.prev-slick-img img').attr('src', prev_slick_img);
        $('.prev-slick-img').css('background-image', 'url(' + prev_slick_img + ')');
        // For next img
        var prev_next_slick_img = $slick_current.next('.slide').find('.slide__media img').attr('src');
        $('.next-slick-img img').attr('src', prev_next_slick_img);
        $('.next-slick-img').css('background-image', 'url(' + prev_next_slick_img + ')');
      }

      function get_next_slick_img() {
        var $slick_current = $('.slick-current');
        // For next img
        var next_slick_img = $slick_current.next('.slide').find('.slide__media img').attr('src');
        $('.next-slick-img img').attr('src', next_slick_img);
        $('.next-slick-img').css('background-image', 'url(' + next_slick_img + ')');
        // For prev img
        var next_prev_slick_img = $slick_current.prev('.slide').find('.slide__media img').attr('src');
        $('.prev-slick-img img').attr('src', next_prev_slick_img);
        $('.prev-slick-img').css('background-image', 'url(' + next_prev_slick_img + ')');
      }

      // End
    }
  }

})(jQuery, Drupal, drupalSettings);
