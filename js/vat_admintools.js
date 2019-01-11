(function($) {
  Drupal.behaviors.viewAdminTools = {


    attach(context, settings) {

      const scope = this;

      // Click Handler for adding Rows
      $('.add-more-buttons')
        .once('.add-more-buttons')
        .click(() => {
          const elems = $('#vat-buttons-list').find('.vat-options-button-row.hide');
          const first = elems[0];
          $(first)
            .removeClass('hide')
            .addClass('show');

          if (elems.length === 1) {
            $('.add-more-buttons').hide();
          }
        });
    },
  };
})(jQuery, Drupal, drupalSettings);
