<?php

/**
 * Implements hook_views_data_alter().
 */
function views_admintools_views_data_alter(array &$data) {

  // Views

  $data['views']['vat_header'] = array(
    'title' => t('Admin Tools Header'),
    'group' => t('Admin'),
    'help' => t('Admin Tools heading or pager.'),
    'area' => array(
      'id' => 'vat_header',
    ),
  );

  // Node

  $data['node']['vat_field_admin_buttons'] = [
    'title' => t('Field Admin Buttons'),
    'group' => t('Admin'),
    'field' => [
      'title' => t('Field Admin Buttons'),
      'help' => t('Add Buttons for Edit and Delete'),
      'id' => 'vat_field_admin_buttons'
    ],
  ];

    $data['node']['vat_field_anchor'] = [
        'title' => t('Field Anchor Link'),
        'group' => t('Admin'),
        'field' => [
            'title' => t('Anchor link'),
            'help' => t('Add anchor link tag'),
            'id' => 'vat_field_anchor'
        ],
    ];

  $data['node']['vat_group_by_field'] = [
    'title' => t('Group title in one table'),
    'group' => t('Admin'),
    'field' => [
      'title' => t('Table Group'),
      'help' => t('Use this to group in one Table'),
      'id' => 'vat_group_by_field'
    ],
  ];


  $data['node']['vat_field_item_count'] = [
    'title' => t('Show the Number of Items from a given Field'),
    'group' => t('Meta'),
    'field' => [
      'title' => t('Item Count'),
      'help' => t('Show the Number of Items from a given Field'),
      'id' => 'vat_field_item_count'
    ],
  ];

  $data['node']['vat_toggle_tag'] = [
    'title' => t('List all Tags, active onces are marked'),
    'group' => t('Admin'),
    'field' => [
      'title' => t('Tags to toggle'),
      'help' => t('List all Tags, active onces are marked'),
      'id' => 'vat_toggle_tag'
    ],
  ];
}



