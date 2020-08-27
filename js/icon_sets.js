(function($) {
  Drupal.behaviors.iconSets = {


    attach(context, settings) {
      $('#views-admintools-icon-sets', context)
        .once('icon-sets')
        .each(() => {
          $('.icon-set-trigger').click(event => {
            let iconSetName = event.currentTarget.dataset.iconSet;


            setIconSet(iconSetName);
          });


        });

    },
  };

  function setIconSet(iconSetName) {
    console.log('iconSetName', iconSetName);
    let iconSets = drupalSettings.iconSets;
    let iconSet = iconSets[iconSetName];

    // Fill Icon Set Options
    $('#edit-icon-set').val(iconSet.set);

    // Fill Default Prefix Options
    $('#edit-icon-variant').val(iconSet.variant);

    // Fill Icon Name Textfield
    let icons = new Map(Object.entries(iconSet.icons));
    icons.forEach(icon => {
      $('#edit-icon-' + icon.name).val(icon.icon);
    });

  }

})(jQuery, Drupal, drupalSettings);
