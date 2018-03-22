(function ($) {

  /**
   *
   */
  Drupal.behaviors.rowAdmintools = {
    attach: function (context, settings) {

        $('#drupal-modal').on('show.bs.modal', function (e) {
            if (!data) return e.preventDefault() // stops modal from being shown
            alert('test');
        })


    }
  };


})(jQuery);


