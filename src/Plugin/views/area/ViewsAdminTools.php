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

        // Bundle
        $option_filters = $this->view->display_handler->getOption('filters');
        $option_filters_types = $option_filters['type']['value'];
        $bundle = array_keys($option_filters_types)[0];

        $options['content_type']['default'] = $bundle;


        // Override defaults to from parent.
        $options['tokenize']['default'] = false;
        $options['empty']['default'] = TRUE;

        // Provide our own defaults.
        $options['title_text']['default'] = '';


        for ($i = 1; $i <= 10; $i++) {
            $options['button_b' . $i . '_active']['default'] = false;
            $options['button_b' . $i . '_label']['default'] = '';
            $options['button_b' . $i . '_icon']['default'] = '';
            $options['button_b' . $i . '_link']['default'] = '';
            $options['button_b' . $i . '_destination']['default'] = '';
            $options['button_b' . $i . '_modal']['default'] = '';
        }


        // Button new
        $options['button_b1_active']['default'] = true;
        $options['button_b1_label']['default'] = $this->t('New');
        $options['button_b1_icon']['default'] = 'plus';
        $options['button_b1_link']['default'] = '/node/add/' . $bundle;
        $options['button_b1_destination']['default'] = '/' . $view_path;
        $options['button_b1_modal']['default'] = true;

        // Button sort
        $options['button_b2_active']['default'] = false;
        $options['button_b2_label']['default'] = $this->t('Sort');
        $options['button_b2_icon']['default'] = 'sort';
        $options['button_b2_link']['default'] = '/' . $view_path . '/sort';
        $options['button_b2_destination']['default'] = '/' . $view_path;

        // Button back
        $options['button_b3_active']['default'] = false;
        $options['button_b3_label']['default'] = $this->t('Back');
        $options['button_b3_icon']['default'] = 'chevron-left';
        $options['button_b3_link']['default'] = '/' . $view_path;
        $options['button_b3_destination']['default'] = '';


        // Button Look
        $options['look_show_label']['default'] = TRUE;
        $options['look_show_icon']['default'] = TRUE;
        $options['look_show_as']['default'] = 'Button';
        $options['look_icon_size']['default'] = 1;  // normal
        $options['look_icon_set']['default'] = 0;    // Automatic

        // Vocabulary
        $options['look_separator']['default'] = false;
        // 5 Vocabularies
        for ($i = 1; $i <= 6; $i++) {
            $options['vocabulary_' . $i]['default'] = '';
        }

        //  Modal
        $options['use_modal']['default'] = TRUE;
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

        // dpm($this->options);

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

        $options_icon_size = [
            'Small',
            'Normal',
            'Large',
        ];

        $options_icon_set = [
            'Automatic',
            'Drupal / jQuery Ui',
            'Font Awesome 5',
            'Twitter Bootstrap 3',
        ];


        $form['#attached']['library'][] = 'views_admintools/views_admintools.enable';


        // Title Text / Heading
        $form['title_text'] = [
            '#title' => $this->t('Title'),
            '#type' => 'textfield',
            '#size' => 60,
            '#default_value' => $this->options['title_text'],
        ];

        // Which Node Type ?
        $form['content_type'] = [
            '#title' => $this->t('Content Type'),
            '#type' => 'select',
            '#default_value' => $this->options['content_type'],
            '#options' => $bundle_options,
        ];

        $form['info'] = [
            '#markup' => '<div class="vat-options-info">' . $this->t('Add icon names without prefix (fa-).') . '</div>',
        ];

        // Warning: Default Drupal Fieldset don't work with $options['fieldset']['field']['default']
        // Also create Filesets manuel

        $form['button_fieldset_start'] = [
            '#type' => 'html_tag',
            '#tag' => 'label',
            '#value' => 'Buttons',
            '#prefix' => '<fieldset id="vat-buttons-list">'
        ];

        for ($i = 1; $i <= 10; $i++) {

            // Button Default
            // ------------------------------
            if ($this->options['button_b' . $i . '_label'] == '') {
                $visibility = 'hide';

            } else {
                $visibility = 'show';
            }


            $form['button_b' . $i . '_fieldset_start'] = [
                '#type' => 'html_tag',
                '#tag' => 'span',
                '#value' => '',
                '#prefix' => '<div class="vat-options-button-row ' . $visibility . '" id="vat-options-button-row-' . $i . '">'
            ];


            // Active  ?
            $form['button_b' . $i . '_active'] = [
                '#title' => '',
                '#type' => 'checkbox',
                '#default_value' => $this->options['button_b' . $i . '_active'],
                '#prefix' => '<span class="vat-options-button-inline vat-options-button-active">',
                '#suffix' => '</span>',
            ];

            // Font Awesome

            if ($this->options['button_b' . $i . '_icon']) {
                $form['button_b' . $i . 'fa'] = array(
                    '#theme' => 'fontawesomeicon',
                    '#tag' => 'span',
                    '#name' => 'fas fa-' . $this->options['button_b' . $i . '_icon'],
                    '#settings' => NULL,
                    '#transforms' => '2x',
                    '#mask' => NULL,
                    '#prefix' => '<span class="vat-options-button-inline vat-options-button-fa">',
                    '#suffix' => '</span>',
                );
            } else {
                $form['button_b' . $i . 'no_fa'] = [
                    '#type' => 'html_tag',
                    '#tag' => 'span',
                    '#value' => '',
                    '#prefix' => '<span class="vat-options-button-inline vat-options-button-fa">',
                    '#suffix' => '</span>',];
            }


            //  icon
            $form['button_b' . $i . 'icon'] = [
                '#title' => $this->t('Icon'),
                '#type' => 'textfield',
                '#size' => 10,
                '#default_value' => $this->options['button_b' . $i . '_icon'],
                '#prefix' => '<span class="vat-options-button-inline">',
                '#suffix' => '</span>',
            ];

            // Label
            $form['button_b' . $i . '_label'] = [
                '#title' => $this->t('Label'),
                '#type' => 'textfield',
                '#size' => 20,
                '#default_value' => $this->options['button_b' . $i . '_label'],
                '#prefix' => '<span class="vat-options-button-inline">',
                '#suffix' => '</span>',
            ];

            // Link
            $form['button_b' . $i . '_link'] = [
                '#title' => $this->t('Link'),
                '#type' => 'textfield',
                '#size' => 20,
                '#default_value' => $this->options['button_b' . $i . '_link'],
                '#prefix' => '<span class="vat-options-button-inline">',
                '#suffix' => '</span>',
            ];

            // Destination after save
            $form['button_b' . $i . '_destination'] = [
                '#title' => $this->t('Destination after Save'),
                '#type' => 'textfield',
                '#size' => 20,
                '#default_value' => $this->options['button_b' . $i . '_destination'],
                '#prefix' => '<span class="vat-options-button-inline">',
                '#suffix' => '</span>',
            ];

            // Modal?
            $form['button_b' . $i . '_modal'] = [
                '#title' => '',
                '#type' => 'checkbox',
                '#default_value' => $this->options['button_b' . $i . '_modal'],
                '#prefix' => '<span class="vat-options-button-inline vat-options-button-modal">',
                '#suffix' => '</span>',
            ];

            // Modal?
            if ($i == 1) {

                $form['button_b' . $i . '_modal']['#prefix'] = '<span class="vat-options-button-inline vat-options-button-modal"><label class="modal-label">Modal</label>';
                $form['button_b' . $i . '_modal']['#suffix'] = '</span>';
            }
            $form['button_b' . $i . '_fieldset_end'] = [
                '#type' => 'html_tag',
                '#tag' => 'span',
                '#value' => '',
                '#suffix' => '</div>'
            ];


        }

        // Add more Rows
        // Add a submit button that handles the submission of the form.
        $form['actions_add_more_rows'] = [
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#value' => $this->t('Add more buttons'),
            '#attributes' => ['class' => ['vat-button', 'add-more-buttons']],
        ];


        $form['button_fieldset_end'] = [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#value' => '',
            '#suffix' => '</fieldset>'
        ];


        // Look
        // ------------------------------

        // Fieldset
        $form['look_fieldset_start'] = [
            '#type' => 'html_tag',
            '#tag' => 'label',
            '#value' => $this->t('Button Look'),
            '#prefix' => '<fieldset class="vat-options-group">'
        ];


        // Label
        $form['look_show_label'] = [
            '#title' => $this->t('label'),
            '#type' => 'checkbox',
            '#default_value' => $this->options['look_show_label'],
            '#prefix' => '<span class="vat-options-inline">',
            '#suffix' => '</span>',
        ];


        // Icon
        $form['look_show_icon'] = [
            '#title' => $this->t('icon'),
            '#type' => 'checkbox',
            '#default_value' => $this->options['look_show_icon'],
            '#prefix' => '<span class="vat-options-inline">',
            '#suffix' => '</span>',
        ];


        // Link or Button
        $options = ['Button', 'Link'];
        $form['look_show_as'] = [
            '#title' => $this->t('Show as'),
            '#type' => 'select',
            '#default_value' => $this->options['look_show_as'],
            '#options' => $options,
            '#prefix' => '<span class="vat-options-inline">',
            '#suffix' => '</span>',
        ];

        // Icon Set
        $form['look_icon_set'] = [
            '#title' => $this->t('Icon Set'),
            '#type' => 'select',
            '#default_value' => $this->options['look_icon_set'],
            '#options' => $options_icon_set,
            '#prefix' => '<span class="vat-options-inline">',
            '#suffix' => '</span>',
        ];

        // Icon Size
        $form['look_icon_size'] = [
            '#title' => $this->t('Icon Size'),
            '#type' => 'select',
            '#default_value' => $this->options['look_icon_size'],
            '#options' => $options_icon_size,
            '#prefix' => '<span class="vat-options-inline">',
            '#suffix' => '</span>',
        ];

        // Fieldset End
        $form['look_fieldset_end'] = [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#value' => '',
            '#suffix' => '</fieldset>'
        ];

        // Vocabulary
        // ------------------------------

        // Fieldset
        $form['vocabularies_fieldset_start'] = [
            '#type' => 'html_tag',
            '#tag' => 'label',
            '#value' => $this->t('Vocabularies'),
            '#prefix' => '<fieldset class="vat-options-group">'
        ];

        for ($i = 1; $i <= 6; $i++) {

            // add 4 taxonomy vocabulary dropdowns
            $form['vocabulary_' . $i] = [
                //    '#title' => $this->t('Taxonomy ' . $i),
                '#type' => 'select',
                '#default_value' => $this->options['vocabulary_' . $i],
                '#options' => $vocabulary_options,
                '#prefix' => '<span class="vat-options-inline">',
                '#suffix' => '</span>',
            ];


        }
        // Separator between Buttons and Taxonomy
        $form['look_separator'] = [
            '#title' => $this->t('Separator between Buttons and Taxonomy'),
            '#type' => 'checkbox',
            '#default_value' => $this->options['look_separator'],
            '#prefix' => '<span class="vat-options-inline">',
            '#suffix' => '</span>',
        ];

        // Fieldset End
        $form['vocabularies_fieldset_end'] = [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#value' => '',
            '#suffix' => '</fieldset>'
        ];

        // Modal
        // ------------------------------

        // Fieldset
        $form['modal_fieldset_start'] = [
            '#type' => 'html_tag',
            '#tag' => 'label',
            '#value' => $this->t('Modal Dialog'),
            '#prefix' => '<fieldset class="vat-options-group">'
        ];

        // Modal
        $form['use_modal'] = [
            '#title' => $this->t('Use Modal Dialog?'),
            '#type' => 'checkbox',
            '#default_value' => $this->options['use_modal'],
            '#prefix' => '<span class="vat-options-inline">',
            '#suffix' => '</span>',

        ];

        // Modal Width
        $form['modal_width'] = [
            '#title' => $this->t('Modal Width'),
            '#type' => 'textfield',
            '#size' => 10,
            '#default_value' => $this->options['modal_width'],
            '#prefix' => '<span class="vat-options-inline">',
            '#suffix' => '</span>',
        ];

        // Fieldset End
        $form['modal_fieldset_end'] = [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#value' => '',
            '#suffix' => '</fieldset>'
        ];

        // User Roles
        // ------------------------------

        // Fieldset
        $form['roles_fieldset_start'] = [
            '#type' => 'html_tag',
            '#tag' => 'label',
            '#value' => $this->t('Which roles are allowed to see the Buttons?'),
            '#prefix' => '<fieldset class="vat-options-group">'
        ];

        foreach ($roles as $role) {

            $role_form_name = 'roles-' . $role['id'];
            $form[$role_form_name] = [
                '#title' => $this->t($role['label']),
                '#type' => 'checkbox',
                '#default_value' => $this->options[$role_form_name],
            ];
        }

        // Fieldset End
        $form['roles_fieldset_end'] = [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#value' => '',
            '#suffix' => '</fieldset>'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function render($empty = false)
    {

        if (!$empty || !empty($this->options['empty'])) {


            $view_path = $this->view->getPath();


            // Content
            // -------------------------------
            $content = false;

            if ($this->options['content_type']) {
                $content['type'] = $this->options['content_type'];

            }


            // Look
            // -------------------------------
            $look['separator'] = false;
            $look['icon'] = false;
            $look['label'] = false;


            // Separator
            if ($this->options['look_separator']) {
                $look['separator'] = true;
            }

            // Icon
            if ($this->options['look_show_icon']) {
                $look['icon'] = true;
            }

            // Label
            if ($this->options['look_show_label']) {
                $look['label'] = true;
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
            $access = false;

            // If User is Admin
            if ($user_id == 1) {
                $access = true;

            } else {
                // Check user roles

                foreach ($user_roles as $user_role) {
                    if (in_array($user_role, $access_roles)) {
                        $access = true;
                        break;
                    }
                }
            }


            // Design


            //  Icon Set
            // ----------------------------------------------------

            $icon_set = $this->options['look_icon_set'];

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

            // Look
            // -------------------------------

            // Show
            switch ($this->options['look_show_as']) {

                case  0: // Button:
                    if (\Drupal::moduleHandler()->moduleExists('bootstrap_library')) {
                        $button_classes = 'btn btn-default vat-button';
                    } else {
                        $button_classes = 'vat-button';
                    }

                    break;

                case 1: // Link:
                    $button_classes = 'vat-link';
                    break;

                default:
                    $button_classes = 'vat-default';
                    break;

            }

            // Size
            switch ($this->options['look_icon_size']) {
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

            // Group
            // -------------------------------
            $group['class'] = $size_class;


            // Modal
            // -------------------------------
            $modal = false;

            if ($this->options['use_modal']) {
                $modal = [
                    'width' => $this->options['modal_width'],
                ];
            }


            // Buttons
            // -------------------------------
            $buttons = [];
            $button_attributes = ['active', 'label', 'icon', 'link', 'destination', 'class', 'modal'];

            for ($i = 1; $i <= 10; $i++) {

                $attr = [];
                $button_name = 'button_b' . $i;

                foreach ($button_attributes as $button_attribute) {
                    $option_name = $button_name . '_' . $button_attribute;
                    $attribute = '';
                    switch ($button_attribute) {

                        case 'icon':
                            if ($this->options[$option_name]) {
                                $attribute = $prefix . $this->options[$option_name];
                            }
                            break;

                        case 'class':
                            $attribute = $button_classes;
                            break;

                        case 'link':
                            $attribute = self::buildHref($button_name);
                            $attr['href'] = $attribute;
                            break;

                        default:
                            $attribute = $this->options[$option_name];
                            break;
                    }

                    $attr[$button_attribute] = $attribute;


                }

                $buttons[$i] = $attr;

            }


            // Vocabularies
            // -------------------------------
            $vocabularies = [];

            for ($i = 1; $i <= 6; $i++) {

                $attr = [];
                $vocabulary_name = 'vocabulary_' . $i;

                if ($this->options[$vocabulary_name] != false) {

                    $machine_name = $this->options[$vocabulary_name];
                    $voc = Vocabulary::load($machine_name);
                    $label = $voc->label();

                    $attr['active'] = true;

                    $attr['icon'] = $prefix . $icon_taxonomy;

                    $attr['label'] = $label;

                    $attr['$machine_name'] = $machine_name;

                    // href
                    $href = "/admin/structure/taxonomy/manage/$machine_name/overview?destination=$view_path";
                    $attr['href'] = $href;

                    // class
                    $attr['class'] = $button_classes;

                    // modal
                    $attr['modal'] = false;


                }
                $vocabularies[$i] = $attr;

            }

            //   dpm($vocabularies);

            $test = [
                'ein' => 'Test 1',
                'ein2' => 'Test 2',
                'ein3' => 'Test 3',
                'ein4' => 'Test 5',
            ];

            return [
                '#theme' => 'vat_area',
                '#test' => $test,
                '#access' => $access,
                '#buttons' => $buttons,
                '#modal' => $modal,
                '#content' => $content,
                '#look' => $look,
                '#group' => $group,
                '#voc' => $vocabularies,

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

    public
    function buildHref($button_name)
    {

        $target = $this->options[$button_name . '_link'];
        $destination = $this->options[$button_name . '_destination'];

        if ($destination) {
            $link = $target . '?destination=' . $destination;
        } else {
            $link = $target;
        }

        return $link;
    }


}
