(function ($) {

  /**
   *
   */
  Drupal.behaviors.vatToggleTag = {
    attach: function (context, settings) {

      console.log(' vatToggleTag');

// This is an Hack to remove all ","
      var toggle_tags = document.querySelectorAll('.vat-toggle-tag'), i;
      for (i = 0; i < toggle_tags.length; ++i) {
        toggle_tags[i].nextSibling.nodeValue = "";
      }

      var toggle_tags_single = document.querySelectorAll('div.vat-toggle-tag-single');
      for (i = 0; i < toggle_tags_single.length; ++i) {
        toggle_tags_single[i].firstChild.nodeValue = "";
      }

      var toggle_tags_multi = document.querySelectorAll('div.vat-toggle-tag-multi');
      for (i = 0; i < toggle_tags_multi.length; ++i) {
        toggle_tags_multi[i].firstChild.nodeValue = "";
      }


    }

  } // D


})(jQuery);


