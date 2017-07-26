/**
 * @file gallery.js
 *
 * Defines the behavior of the media bundle gallery.
 */
(function ($, Drupal, drupalSettings) {

  'use strict';

  /**
   * Initializes the slideshow with Slick and PhotoSwipe.
   */
  Drupal.behaviors.gallery = {
    attach: function (context, settings) {
      var $slider = $('.media-gallery__images .slideshow__slides');
      var $images = $slider.find('img');
      $slider.slick({
        dots: false,
        autoplay: false,
        arrows: true,
        swipeToSlide: true
      });
      $('.media-gallery__preview .image').click(function() {
        $slider.slick('slickGoTo', $(this).parent().data('index'));
      });

      var $pswpElement = document.querySelectorAll('.pswp__media-gallery')[0];

      var $pswpItems = [];
      $.each($images, function(k, img) {
        var $pswpItem = {
          src: img.src,
          w: img.width,
          h: img.height
        };
        $pswpItems.push($pswpItem);
      });
      $('.slick-controls__gallery').append('<span class="slick__download"><a href="' + drupalSettings.degov_media_gallery.imagesDownloadLinks[$slider.slick('slickCurrentSlide')] + '">' + Drupal.t('Download') + '</a></span>');

      $slider.find('article').click(function() {
        var $index = parseInt($slider.slick('slickCurrentSlide'));
        var $options = {
          index: $index
        };
        // Initializes and opens PhotoSwipe.
        var $pswp = new PhotoSwipe($pswpElement, PhotoSwipeUI_Default, $pswpItems, $options);
        $pswp.init();
      });
      $('.media-gallery-js-open-lightroom').click(function() {
        $images.get($slider.slick('slickCurrentSlide')).click();
      });
      $('.media-gallery__images button').click(function() {
        $('.slick__counter__current').text(parseInt($slider.slick('slickCurrentSlide')) + 1);
        $('.slick-controls__gallery').find('a').attr('href', drupalSettings.degov_media_gallery.imagesDownloadLinks[$slider.slick('slickCurrentSlide')]);
      });

    }
  }

})(jQuery, Drupal, drupalSettings);
