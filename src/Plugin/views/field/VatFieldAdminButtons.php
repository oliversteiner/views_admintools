<?php
/**
 * @file
 * Definition of
 * Drupal\views_admintools\Plugin\views\field\VatFieldAdminButtons
 */

namespace Drupal\views_admintools\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\Core\Url;
use Drupal\views\ResultRow;

/**
 * Field handler to add Edit and Delete Buttons.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("vat_field_admin_buttons")
 */
class VatFieldAdminButtons extends FieldPluginBase
{

    /**
     * @{inheritdoc}
     */
    public function query()
    {
        // Leave empty to avoid a query on this field.
    }

    /**
     * Define the available options
     *
     * @return array
     */
    protected function defineOptions()
    {
        $options = parent::defineOptions();
        $options['button_edit'] = ['default' => TRUE];
        $options['button_delete'] = ['default' => TRUE];

        $options['button_label'] = ['default' => FALSE];
        $options['button_icon'] = ['default' => TRUE];

        $options['show_as'] = ['default' => 'button'];
        $options['button_class'] = ['default' => FALSE];

        $options['destination_options'] = ['default' => 1]; // active View
        $options['destination_other'] = ['default' => ''];

        //  Modal
        $options['modal'] = ['default' => TRUE];
        $options['modal_width'] = ['default' => 800]; //

        // Icon Set
        $options['icon_set'] = ['default' => 0];    // Automatic
        $options['icon_size'] = ['default' => 1];  // normal

        // Icon Names for Iconfonts
        $options['icon_edit'] = ['default' => 'edit'];  // normal
        $options['icon_delete'] = ['default' => 'trash'];  // normal


        return $options;
    }

    /**
     * Provide the options form.
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state)
    {

        //
        // ------------------------------
        $form['group_buttons'] = [
            '#markup' => '<div class="vat-views-option-group">' . $this->t('Show Buttons') . '</div>',
        ];


        $form['button_edit'] = [
            '#title' => $this->t('Edit'),
            '#type' => 'checkbox',
            '#default_value' => $this->options['button_edit'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];

        $form['button_delete'] = [
            '#title' => $this->t('delete'),
            '#type' => 'checkbox',
            '#default_value' => $this->options['button_delete'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',

        ];


        // Destination
        // ------------------------------
        $form['group_destination'] = [
            '#markup' => '<div class="vat-views-option-group">' . $this->t('Chose Destination') . '</div>',
        ];

        $options_destination = [
            'Show Content',
            'this view',
            '<content_type>_admin',
            'other view',
        ];

        $form['destination_options'] = [
            '#title' => $this->t('Chose destination after save'),
            '#type' => 'select',
            '#default_value' => $this->options['destination_options'],
            '#options' => $options_destination,
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',

        ];

        $form['destination_other'] = [
            '#title' => $this->t('Destination View id'),
            '#type' => 'textfield',
            '#default_value' => '',
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];

        // Button Look
        // ------------------------------

        $form['group_elements'] = [
            '#markup' => '<div class="vat-views-option-group">' . $this->t('Show') . '</div>',
        ];

        $form['button_label'] = [
            '#title' => $this->t('label'),
            '#type' => 'checkbox',
            '#default_value' => $this->options['button_label'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];

        $form['button_icon'] = [
            '#title' => $this->t('icon'),
            '#type' => 'checkbox',
            '#default_value' => $this->options['button_icon'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];


        // Design
        // ------------------------------

        $form['group_design'] = [
            '#markup' => '<div class="vat-views-option-group">' . $this->t('Design:') . '</div>',
        ];


        // Link or Button

        $options = ['button', 'link'];
        $form['show_as'] = [
            '#title' => $this->t('Display as'),
            '#type' => 'select',
            '#default_value' => $this->options['show_as'],
            '#options' => $options,
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];

        // Icon Set

        $options_icon_set = [
            'automatic',
            'Font Awesome',
            'Twitter Bootstrap',
            'Drupal / jQuery Ui',
        ];

        $form['icon_set'] = [
            '#title' => $this->t('Icon Set'),
            '#type' => 'select',
            '#default_value' => $this->options['icon_set'],
            '#options' => $options_icon_set,
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];


        // Icon Size
        // ------------------------------

        $options_icon_size = [
            'Small',
            'Normal',
            'Large',
        ];

        $form['icon_size'] = [
            '#title' => $this->t('Icon Size'),
            '#type' => 'select',
            '#default_value' => $this->options['icon_size'],
            '#options' => $options_icon_size,
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];


        // Icon Names for Iconfonts
        // ------------------------------

        // Title
        $form['group_design'] = [
            '#markup' => '<div class="vat-views-option-group">' . $this->t('Icon Name (without prefix)') . '</div>',
        ];


        // edit
        $form['icon_edit'] = [
            '#title' => $this->t('edit'),
            '#type' => 'textfield',
            '#attributes' => array('maxlength' => 10, 'size' => 10),
            '#default_value' => $this->options['icon_edit'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];

        // delete
        $form['icon_delete'] = [
            '#title' => $this->t('delete'),
            '#type' => 'textfield',
            '#attributes' => array('maxlength' => 10, 'size' => 10),
            '#default_value' => $this->options['icon_delete'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];

        $form['group_end'] = [
            '#markup' => '<div class="vat-views-option-group"></div>',
        ];


        // Modal
        // ------------------------------

        // Title
        $form['group_modal'] = [
            '#markup' => '<div class="vat-views-option-group">' . $this->t('Modal Dialog') . '</div>',
        ];

        // Modal
        $form['modal'] = [
            '#title' => $this->t('Use Modal Dialog?'),
            '#type' => 'checkbox',
            '#default_value' => $this->options['modal'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',

        ];

        // Modal Width
        $form['modal_width'] = [
            '#title' => $this->t('Modal Width'),
            '#type' => 'textfield',
            '#attributes' => array('maxlength' => 10, 'size' => 10),
            '#default_value' => $this->options['modal_width'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];


        // Parent Options
        // ------------------------------

        parent::buildOptionsForm($form, $form_state);
    }

    /**
     * @{inheritdoc}
     */
    public function render(ResultRow $values)
    {

        $node = $values->_entity;
        $bundle = $node->bundle();
        $nid = $values->_entity->id();
        $display_path = $this->displayHandler->getPath();
        $buttons = ['edit', 'delete'];
        $elements = [];
        $icon_prefix = '';
        $icon = '<span></span>';
        $label = '';


        switch ($this->options['destination_options']) {
            case 1:
                // this view
                $destination = '?destination=' . $display_path;
                break;
            case 2:
                // <content_type>_admin
                $destination = '?destination=' . $bundle . '_admin';
                break;
            case 3:
                // other view
                $path_other = $this->options['destination_other'];
                $destination = '?destination=' . $path_other;
                break;
            default:
                //Show Content
                $destination = '';
                break;
        }


        // Size Class
        switch ($this->options['icon_size']) {
            case 0: // small
                $class_size = 'btn-sm vat-button-sm';
                break;

            case 1:  // normal
                $class_size = 'btn-md vat-button-md';
                break;

            case 2: // large
                $class_size = 'btn-lg vat-button-lg';
                break;

            default:
                $class_size = '';
                break;
        }


        //  Icon Theme prefix
        if ($this->options['button_icon']) {

            switch ($this->options['icon_set']) {

                case 1: // 'Font Awesome 5'
                    $icon_prefix = 'fas fa-';
                    break;

                case 2: // 'Bootstrap'
                    $icon_prefix = 'glyphicon glyphicon-';
                    break;

                case 3:  // 'Drupal / jQuery Ui'
                    $icon_prefix = 'ui-icon ui-icon-';
                    break;

                default: //'automatic'

                    // Font Awesome
                    //
                    if (\Drupal::moduleHandler()->moduleExists('fontawesome')) {
                        $icon_prefix = 'fas fa-';
                    } // Twitter Bootstap 3
                    elseif (\Drupal::moduleHandler()
                        ->moduleExists('bootstrap_library')) {
                        $icon_prefix = 'glyphicon glyphicon-';
                    } // Drupal Default / jQuery UI Icons
                    else {
                        $icon_prefix = 'ui-icon ui-icon-';
                    }
                    break;

            }
        }

        // show as
        switch ($this->options['show_as']) {

            case  0: // Button:
                if (\Drupal::moduleHandler()->moduleExists('bootstrap_library')) {
                    $class_show_as = ' btn btn-default vat-button';
                } else {
                    $class_show_as = 'vat-button';
                }
                break;

            case 1: // Link:
                $class_show_as = 'vat-link';
                break;

            default:
                $class_show_as = 'vat-default';
                break;

        }


        foreach ($buttons as $button_name) {

            // if Button selected
            $options_button_active = 'button_' . $button_name;

            if ($this->options[$options_button_active]) {


                // Link
                switch ($button_name) {

                    case 'edit':
                        $link = 'node/' . $nid . '/edit' . $destination;
                        $icon_name = $this->options['icon_edit'];


                        break;

                    case 'delete':
                        $link = 'node/' . $nid . '/delete' . $destination;
                        $icon_name = $this->options['icon_delete'];


                        break;

                    default:
                        $link = 'node/' . $nid;
                        $icon_name = FALSE;
                        break;

                }


                // Options Icon
                if ($this->options['button_icon']) {
                    $icon = '<span class="' . $icon_prefix . $icon_name . '" aria-hidden="true"></span>';
                }

                // Options Label
                if ($this->options['button_label']) {
                    $label = '<span class="vat-row-label">' . $this->t($button_name) . '</span>';
                }


                $title = ['#markup' => $icon . $label];


                $elements[$button_name] = [
                    '#title' => $title,
                    '#type' => 'link',
                    '#url' => Url::fromUri('internal:/' . $link),
                    '#attributes' => [
                        'class' => $class_show_as . ' ' . $class_size,
                    ],
                    '#prefix' => '<span><div class="vat-no-break">',
                    '#suffix' => '</div></span>',
                ];


                // Modal Dialog

                if ($this->options['modal']) {
                    $elements[$button_name]['#attributes'] = [
                        'class' => 'use-ajax ' . $class_show_as . ' ' . $class_size,
                        'data-dialog-type' => 'modal',
                        'data-dialog-options' => json_encode(['width' => $this->options['modal_width']]),


                    ];
                }
            }
        }

        $elements['#attached']['library'][] = 'views_admintools/views_admintools.enable';

        return $elements;

    }
}
