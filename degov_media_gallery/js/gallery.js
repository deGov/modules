/**
 * @file gallery.js
 *
 * Defines the behavior of the media bundle gallery.
 */
(function ($, Drupal) {

  'use strict';

  /**
   * Initializes the slideshow with Slick and PhotoSwipe.
   */
  Drupal.behaviors.gallery = {
    pswpItems: [],
    attach: function (context, settings) {
      var $gallery = $('.media-gallery__images', context);
      if ($gallery.length < 1) {
        return;
      }
      var $slider = $('.slideshow__slides', $gallery);
      var $images = $slider.find('img');
      $slider.once().slick({
        dots: false,
        autoplay: false,
        arrows: true,
        swipeToSlide: true
      });
      $('.media-gallery__preview .image').click(function () {
        $slider.slick('slickGoTo', $(this).parent().data('index'));
      });
      var $pswpElement = document.querySelectorAll('.pswp__media-gallery')[0];
      if (Drupal.behaviors.gallery.pswpItems.length > 0) {
        return;
      }
      $.each($images, function (k, img) {
        var $pswpItem = {
          src: settings.degov_media_gallery.imagesDownloadLinks[k].uri,
          w: settings.degov_media_gallery.imagesDownloadLinks[k].width,
          h: settings.degov_media_gallery.imagesDownloadLinks[k].height
        };
        Drupal.behaviors.gallery.pswpItems.push($pswpItem);
      });

      $('.slick-controls__gallery', $gallery).once().append('<span class="slick__download"><a href="' + settings.degov_media_gallery.imagesDownloadLinks[0].download + '"><i aria-hidden="true" class="fa fa-download"></i>' + Drupal.t('Download') + '</a></span>');

      $slider.find('.media-image').click(function () {
        var $index = parseInt($slider.slick('slickCurrentSlide'));
        var $options = {
          index: $index
        };
        // Initializes and opens PhotoSwipe.
        var $pswp = new PhotoSwipe($pswpElement, PhotoSwipeUI_Default, Drupal.behaviors.gallery.pswpItems, $options);
        $pswp.init();
      });
      $('.media-gallery-js-open-lightroom', $gallery).click(function () {
        $images.get($slider.slick('slickCurrentSlide')).click();
      });
      $slider.on('init reInit afterChange', function (event, slick, currentSlide, nextSlide) {
        var i = (currentSlide ? currentSlide : 0) + 1;
        $('.slick__counter__current', $gallery).text(i);
        $('.slick__counter__total', $gallery).text(slick.slideCount);
        $('.slick-controls__gallery .slick__download a', $gallery).prop('href', settings.degov_media_gallery.imagesDownloadLinks[$slider.slick('slickCurrentSlide')].download);
      });
      $('.slick__pause', $gallery).on('click', function () {
        $slider.slick('slickPause');
        $(this).hide().siblings('.slick__play').show().focus();
      }).hide();
      $('.slick__play', $gallery).on('click', function () {
        $slider.slick('slickPlay');
        $(this).hide().siblings('.slick__pause').show().focus();
      }).show();
    }
  }

})(jQuery, Drupal);
