<?php

/**
 * Implements hook_views_data_alter().
 */
function views_admintools_views_data_alter(array &$data) {

  // Views

  $data['views']['vat_views_area_admin_tools'] = [
    'title' => t('Admin Tools'),
    'group' => t('Admin'),
    'help' => t('Add Admin Buttons in an area'),
    'area' => [
      'id' => 'vat_views_area_admin_tools',
    ],
  ];

  // Node

  $data['node']['vat_field_admin_buttons'] = [
    'title' => t('Admin Buttons'),
    'group' => t('Admin'),
    'field' => [
      'title' => t('Admin Buttons'),
      'help' => t('Add Buttons for Edit and Delete via Modal Dialog'),
      'id' => 'vat_field_admin_buttons',
    ],
  ];

  $data['node']['vat_field_anchor'] = [
    'title' => t('Field Anchor Link'),
    'group' => t('Admin'),
    'field' => [
      'title' => t('Anchor link'),
      'help' => t('Add anchor link tag'),
      'id' => 'vat_field_anchor',
    ],
  ];

  $data['node']['vat_group_by_field'] = [
    'title' => t('Group title in one table'),
    'group' => t('Admin'),
    'field' => [
      'title' => t('Table Group'),
      'help' => t('Use this to group in one Table'),
      'id' => 'vat_group_by_field',
    ],
  ];


  $data['node']['vat_field_item_count'] = [
    'title' => t('Show the Number of Items from a given Field'),
    'group' => t('Meta'),
    'field' => [
      'title' => t('Item Count'),
      'help' => t('Show the Number of Items from a given Field'),
      'id' => 'vat_field_item_count',
    ],
  ];

  $data['node']['vat_toggle_tag'] = [
    'title' => t('List all Tags, active ones are marked'),
    'group' => t('Admin'),
    'field' => [
      'title' => t('Tags to toggle'),
      'help' => t('List all Tags, active ones are marked'),
      'id' => 'vat_toggle_tag',
    ],
  ];
}


/**
 * Implements hook_preprocess_views_view().
 *
 * @param $variables
 */
function views_admintools_preprocess_views_view(&$variables) {
 // $variables['debug'][] = 'views_admintools_preprocess_views_view';

  if (!isset($variables['view'])) {
    return;
  }
  $view = $variables['view'];
  // Get the current display.
  $display = $view->getDisplay();

  // And get the list of extenders for this display.
  $extenders = $display->getExtenders();
  if (isset($extenders['views_admintools_icon'])) {

    // Retrieve the settings of our plugins using our custom plugin method.
    $iconValues = $extenders['views_admintools_icon']->getIconExtenderValues();
    $variables['icon'] = $iconValues;

  }


}

