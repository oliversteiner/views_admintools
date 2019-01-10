<?php

namespace Drupal\views_admintools\Plugin\views\area;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Drupal\views\Plugin\views\area\TokenizeAreaPluginBase;


/**
 * Views area Admin Tools.
 *
 * @ingroup views_area_handlers
 *
 * @ViewsArea("vat_views_area_admin_tools")
 */
class ViewsAdminTools extends TokenizeAreaPluginBase
{


    /**
     * {@inheritdoc}
     */
    protected function defineOptions()
    {
        $options = parent::defineOptions();

        $view_path = $this->view->getPath();

        // Override defaults to from parent.
        $options['tokenize']['default'] = FALSE;
        $options['empty']['default'] = TRUE;

        // Provide our own defaults.
        $options['title']['default'] = '';

        // Bundle
        $options['node_type']['default'] = '';

        // Buttons
        $options['buttons_blank']['default'] = FALSE;

        // Button new
        $options['button_new']['default'] = TRUE;
        $options['target_path_after_save']['default'] = $view_path;
        $options['icon_new']['default'] = 'plus';

        // Button sort
        $options['button_sort']['default'] = FALSE;
        $options['target_path_sort']['default'] = $view_path . '-sort';
        $options['icon_sort']['default'] = 'sort';


        // Button back
        $options['button_back']['default'] = FALSE;
        $options['button_back_label']['default'] = $this->t('Back');
        $options['target_path_back']['default'] = $view_path;
        $options['icon_back']['default'] = 'chevron-left';

        // Buttons


        $options['button_text']['default'] = '';
        $options['destination']['default'] = '';
        $options['separator']['default'] = FALSE;


        // Design
        $options['button_label']['default'] = TRUE;
        $options['button_icon']['default'] = TRUE;
        $options['show_as']['default'] = 'Button';
        $options['button_class']['default'] = FALSE;
        $options['icon_size']['default'] = 1;  // normal

        // Icon Set
        $options['icon_set']['default'] = 0;    // Automatic

        // Vocabulary
        for ($i = 1; $i <= 5; $i++) {
            $options['vocabulary_' . $i]['default'] = '';
        }

        //  Modal
        $options['modal']['default'] = TRUE;
        $options['modal_width']['default'] = 800; //

        // Role
        $role_objects = Role::loadMultiple();

        foreach ($role_objects as $role) {
            $option_name = 'roles-' . $role->id();
            $options[$option_name]['default'] = false;  // normal
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state)
    {
        parent::buildOptionsForm($form, $form_state);


        // Nodes Types
        $types = NodeType::loadMultiple();
        $bundle_options = [];

        foreach ($types as $key => $type) {
            $bundle_options[$key] = $type->label();
        }

        // Roles
        $roles = [];
        $i = 0;
        $role_objects = Role::loadMultiple();

        foreach ($role_objects as $role) {
            $roles[$i]['id'] = $role->id();
            $roles[$i]['label'] = $role->label();
            $i++;
        }


        // Taxonomy
        $types = Vocabulary::loadMultiple();
        $vocabulary_options = [];
        $vocabulary_options[''] = '';

        foreach ($types as $key => $type) {
            $vocabulary_options[$key] = $type->label();
        }


        $form['#attached']['library'][] = 'views_admintools/views_admintools.enable';

        // Title Text / Heading
        $form['title'] = [
            '#title' => $this->t('Title'),
            '#type' => 'textfield',
            '#default_value' => $this->options['title'],
        ];

        // Which Node Type ?
        $form['node_type'] = [
            '#title' => $this->t('Content Type'),
            '#type' => 'select',
            '#default_value' => $this->options['node_type'],
            '#options' => $bundle_options,
        ];


        // Button New
        // ------------------------------

        $form['group_button_new'] = [
            '#markup' => '<div class="vat-views-option-group">' . $this->t('Add "New" Button') . '</div>',
        ];

        // Use Button New ?
        $form['button_new'] = [
            '#title' => $this->t('Button New'),
            '#type' => 'checkbox',
            '#default_value' => $this->options['button_new'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];

        // Destination after save
        $form['destination'] = [
            '#title' => $this->t('Destination after Save'),
            '#type' => 'textfield',
            '#default_value' => $this->options['target_path_after_save'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];

        // new icon
        $form['icon_new'] = [
            '#title' => $this->t('Icon Name (without prefix)'),
            '#type' => 'textfield',
            '#attributes' => array('maxlength' => 10, 'size' => 10),
            '#default_value' => $this->options['icon_new'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];


        // Button Sort
        // ------------------------------

        $form['group_button_sort'] = [
            '#markup' => '<div class="vat-views-option-group">' . $this->t('Add "Sorting" Button') . '</div>',
        ];

        $form['button_sort'] = [
            '#title' => $this->t('Button Sorting'),
            '#type' => 'checkbox',
            '#default_value' => $this->options['button_sort'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];

        // Target View Sort
        $form['view_name_sorting'] = [
            '#title' => $this->t('View name'),
            '#type' => 'textfield',
            '#default_value' => $this->options['target_path_sort'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];


        // sort icon
        $form['icon_sort'] = [
            '#title' => $this->t('Icon Name (without prefix)'),
            '#type' => 'textfield',
            '#attributes' => array('maxlength' => 10, 'size' => 10),
            '#default_value' => $this->options['icon_sort'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];

        // Button Back
        // ------------------------------

        $form['group_button_back'] = [
            '#markup' => '<div class="vat-views-option-group">' . $this->t('Add "Back" Button') . '</div>',
        ];

        // activation
        $form['button_back'] = [
            '#title' => $this->t('Back'),
            '#type' => 'checkbox',
            '#default_value' => $this->options['button_back'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];
        // Label
        $form['button_back_label'] = [
            '#title' => $this->t('Label'),
            '#type' => 'textfield',
            '#default_value' => $this->options['button_back_label'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];

        // Destination
        $form['view_name_back'] = [
            '#title' => $this->t('View name'),
            '#type' => 'textfield',
            '#default_value' => $this->options['target_path_back'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];

        // Icon
        $form['icon_back'] = [
            '#title' => $this->t('Icon Name (without prefix)'),
            '#type' => 'textfield',
            '#attributes' => array('maxlength' => 10, 'size' => 10),
            '#default_value' => $this->options['icon_back'],
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

        $options = ['Button', 'Link'];
        $form['show_as'] = [
            '#title' => $this->t('Display as'),
            '#type' => 'select',
            '#default_value' => $this->options['show_as'],
            '#options' => $options,
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];

        // Icon Set
        // ------------------------------

        $options_icon_set = [
            'Automatic',
            'Drupal / jQuery Ui',
            'Font Awesome 5',
            'Twitter Bootstrap 3',
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

        // Separator between Buttons and Taxonomy
        // ------------------------------
        $form['separator'] = [
            '#title' => $this->t('Separator between Buttons and Taxonomy'),
            '#type' => 'checkbox',
            '#default_value' => $this->options['separator'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];


        $form['group_end'] = [
            '#markup' => '<div class="vat-views-option-group"></div>',
        ];


        // Taxonomy
        // ------------------------------
        $form['group_taxonomy_title'] = [
            '#markup' => '<div class="vat-views-option-group">' . $this->t('Add Taxonomy Links') . '</div>',
        ];


        for ($i = 1; $i <= 5; $i++) {

            // add 4 taxonomy vocabulary dropdowns
            $form['vocabulary_' . $i] = [
                '#title' => $this->t('Taxonomy ' . $i),
                '#type' => 'select',
                '#default_value' => $this->options['vocabulary_' . $i],
                '#options' => $vocabulary_options,
                '#prefix' => '<div class="vat-views-option-inline">',
                '#suffix' => '</div>',
            ];


        }


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

        // User Roles
        // ------------------------------

        // Title
        $form['group_roles'] = [
            '#markup' => '<div class="vat-views-option-group">' . $this->t('Which roles are allowed to see the Buttons? ') . '</div>',
        ];

        foreach ($roles as $role) {

            $role_form_name = 'roles-' . $role['id'];
            $form[$role_form_name] = [
                '#title' => $this->t($role['label']),
                '#type' => 'checkbox',
                '#default_value' => $this->options[$role_form_name],
            ];
        }

    }

    /**
     * {@inheritdoc}
     */
    public function render($empty = FALSE)
    {

        if (!$empty || !empty($this->options['empty'])) {

            // Taxonomy
            $taxonomy = [];
            for ($i = 1; $i <= 5; $i++) {

                if ($this->options['vocabulary_' . $i] != FALSE) {
                    $taxonomy_term_name = $this->options['vocabulary_' . $i];
                    $taxonomy[$i]['machine_name'] = $taxonomy_term_name;
                    $taxonomy[$i]['title'] = self::_properTitle($taxonomy_term_name);
                } else {
                    $taxonomy[$i] = FALSE;
                }

            }

            // Roles with access
            $system_roles = [];
            $i = 0;
            $role_objects = Role::loadMultiple();
            foreach ($role_objects as $role) {
                $system_roles[$i]['id'] = $role->id();
                $system_roles[$i]['label'] = $role->label();
                $i++;
            }

            $access_roles = [];

            foreach ($role_objects as $role) {
                $role_id = $role->id();
                $option_name = 'roles-' . $role_id;
                if ($this->options[$option_name]) {
                    $access_roles[] = $role_id;
                }

            }


            // Get User Roles
            $current_user = \Drupal::currentUser();
            $user_roles = $current_user->getRoles();
            $user_id = $current_user->id();


            // is user roles in access roles ?
            $has_access = false;

            // If User is Admin
            if ($user_id == 1) {
                $has_access = true;

            } else {
                // Check user roles

                foreach ($user_roles as $user_role) {
                    if (in_array($user_role, $access_roles)) {
                        $has_access = true;
                        break;
                    }
                }
            }


            // Design


            // Size Class
            switch ($this->options['icon_size']) {
                case 0: // small
                    $size_class = 'btn-sm vat-button-sm';
                    break;

                case 1:  // normal
                    $size_class = 'btn-md vat-button-md';
                    break;

                case 2: // large
                    $size_class = 'btn-lg vat-button-lg';
                    break;

                default:
                    $size_class = '';
                    break;
            }


            //  Icon Set
            // ----------------------------------------------------

            $icon_set = $this->options['icon_set'];

            //  Icon Theme
            if ($icon_set == 0) {

                // Search for Module Fontawesome
                if (\Drupal::moduleHandler()->moduleExists('fontawesome')) {
                    $icon_set = 2; // Font Awesome 5
                } else {
                    $icon_set = 1; // Drupal / jQuery Ui
                }
            }

            switch ($icon_set) {

                case 1:  // Drupal / jQuery Ui
                    $prefix = 'ui-icon ui-icon-';
                    $icon_taxonomy = "pencil";
                    break;

                case 2: // 'Font Awesome 5'
                    $prefix = 'fas fa-';
                    $icon_taxonomy = "list-ul";
                    break;

                case 3: // 'Bootstrap 3'
                    $prefix = 'glyphicon glyphicon-';
                    $icon_taxonomy = "list";
                    break;

                default:
                    $prefix = 'ui-icon ui-icon-';
                    $icon_taxonomy = "pencil";
                    break;
            }


            switch ($this->options['show_as']) {

                case  0: // Button:
                    if (\Drupal::moduleHandler()->moduleExists('bootstrap_library')) {
                        $button_class = 'btn btn-default vat-button';
                    } else {
                        $button_class = 'vat-button';
                    }
                    break;

                case 1: // Link:
                    $button_class = 'vat-link';
                    break;

                default:
                    $button_class = 'vat-default';
                    break;

            }

            // Modal Dialog?

            $button_class_dialog = $this->options['modal'] ? 'use-ajax' : '';


            $vat = [
                'title' => $this->options['title'],
                'button_new' => $this->options['button_new'],
                'button_sort' => $this->options['button_sort'],
                'button_back' => $this->options['button_back'],
                'button_back_label' => $this->options['button_back_label'],
                'separator' => $this->options['separator'],
                'list_taxonomy' => $taxonomy,
                'node_type' => $this->options['node_type'],
                'target_path_after_save' => '/' . $this->options['target_path_after_save'],
                'target_path_sort' => '/' . $this->options['target_path_sort'],
                'target_path_back' => '/' . $this->options['target_path_back'],
                'button_label' => $this->options['button_label'],
                'button_icon' => $this->options['button_icon'],
                'show_as' => $this->options['show_as'],
                'icon_size' => $this->options['icon_size'],
                'button_class' => $button_class,
                'size_class' => $size_class,

                // Icon
                'icon_set' => $icon_set,
                'icon_prefix' => $prefix,


                // Icon names
                'icon_new' => $this->options['icon_new'],
                'icon_sort' => $this->options['icon_sort'],
                'icon_back' => $this->options['icon_back'],
                'icon_taxonomy' => $icon_taxonomy,

                // Modal
                'modal' => $this->options['modal'],
                'modal_width' => $this->options['modal_width'],
                'modal_button' => $button_class_dialog,

                // Roles
                'system_roles' => $system_roles,
                'access_roles' => $access_roles,
                'user_roles' => $user_roles,
                'has_access' => $has_access,
                'user_id' => $user_id,


            ];


            return [
                '#theme' => 'vat_area',
                '#vat' => $vat,
            ];

        }

        return [];
    }


    /**
     * Render a text area with \Drupal\Component\Utility\Xss::filterAdmin().
     */
    public
    function renderTextField($value)
    {
        if ($value) {
            return $this->sanitizeValue($this->tokenizeValue($value), 'xss_admin');
        }
        return '';
    }


    private
    function _properTitle($string)
    {

        $string = str_replace('_', ' ', $string);
        $string = ucwords($string);

        return $string;
    }

}
