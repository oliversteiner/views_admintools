(function($) {
  Drupal.behaviors.vatViewsArea = {


    attach(context, settings) {
      $('#vat-buttons-list')
        .once('vatViewsArea')
        .each(() => {

          // Button Add More
          $('.add-more-buttons').click(() => this.addMore());

          // Button Reset to Default
          $('.reset-to-defaults').click(() => this.resetToDefault());

        });

    },


    addMore: function() {
      const elems = $('#vat-buttons-list').find('.vat-options-button-row.hide');
      const first = elems[0];
      $(first)
        .removeClass('hide')
        .addClass('show');
      if (elems.length === 1) {
        $('.add-more-buttons').hide();
      }
    },

    resetToDefault: function() {
      console.log('reset', drupalSettings.viewsDefaults);

      const defaults = drupalSettings.viewsDefaults;

      const buttons = [
        'new',
        'sort',
        'back',
        'forward',
        'search',
      ];

      let i = 1;
      buttons.forEach(button => {
        console.log('button', button);

        // Icon
        $('[name="options[button_b'+i+'_icon]"]').val(defaults[button]);

        // Label
        $('[name="options[button_b'+i+'_label]"]').val(capitalize(button));

        i++;
      });
      // Icon Set


// Variant
      $('.vat-icon-variant').val(defaults.variant);
    },
  };

})(jQuery, Drupal, drupalSettings);

const capitalize = (s) => {
  if (typeof s !== 'string') return ''
  return s.charAt(0).toUpperCase() + s.slice(1)
}
