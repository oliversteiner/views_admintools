(function ($) {

  /**
   *
   */
  Drupal.behaviors.rowAdmintools = {
    attach: function (context, settings) {


      var buttons = $('.vat-button');


      $.each(buttons, function (key, value) {
        //  console.log(value);

        var icon_name = $(value).data('vat-icon');
        $(value).empty();
        var html = '<span class="glyphicon glyphicon-' + icon_name + '" aria-hidden="true"></span>';

        $(value).prepend(html)

      });


    }
  };


})(jQuery);


