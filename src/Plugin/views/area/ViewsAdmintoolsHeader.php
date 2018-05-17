<?php

  namespace Drupal\views_admintools\Plugin\views\area;

  use Drupal\Core\Form\FormStateInterface;
  use Drupal\node\Entity\NodeType;
  use Drupal\taxonomy\Entity\Vocabulary;
  use Drupal\views\Plugin\views\area\TokenizeAreaPluginBase;


  /**
   * Views area Admin Tools.
   *
   * @ingroup views_area_handlers
   *
   * @ViewsArea("vat_header")
   */
  class ViewsAdmintoolsHeader extends TokenizeAreaPluginBase {


    /**
     * {@inheritdoc}
     */
    protected function defineOptions() {
      $options = parent::defineOptions();

      // Override defaults to from parent.
      $options['tokenize']['default'] = FALSE;
      $options['empty']['default'] = TRUE;

      // Provide our own defaults.
      $options['content'] = ['default' => ''];
      $options['pager_embed'] = ['default' => FALSE];

      // Bundle
      $options['header_node_type'] = ['default' => 'article'];

      // Buttons
      $options['header_button_new'] = ['default' => TRUE];
      $options['header_button_sort'] = ['default' => FALSE];
      $options['header_button_text'] = ['default' => ''];
      $options['header_destination'] = ['default' => ''];
      $options['seperator'] = ['default' => FALSE];

      // Design
      $options['button_label'] = ['default' => FALSE];
      $options['button_icon'] = ['default' => TRUE];
      $options['show_as'] = ['default' => 'Button'];
      $options['button_class'] = ['default' => FALSE];
      $options['icon_set'] = ['default' => 0];    // Automatic
      $options['icon_size'] = ['default' => 1];  // normal

      // Icon Names for Iconfonts
      $options['icon_new'] = ['default' => 'plus'];  // normal
      $options['icon_sort'] = ['default' => 'sort'];  // normal
      $options['icon_edit'] = ['default' => 'edit'];  // normal

      // Vocabularis
      for ($i = 1; $i <= 5; $i++) {
        $options['header_vocabulary_' . $i] = ['default' => ''];
      }


      return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state) {
      parent::buildOptionsForm($form, $form_state);


      // Nodes Types
      $types = NodeType::loadMultiple();
      $bundle_options = [];

      foreach ($types as $key => $type) {
        $bundle_options[$key] = $type->label();
      }

      // Taxonomy
      $types = Vocabulary::loadMultiple();
      $vocabulary_options = [];
      $vocabulary_options[''] = '';

      foreach ($types as $key => $type) {
        $vocabulary_options[$key] = $type->label();
      }

      // Which Node Type ?
      $form['header_node_type'] = [
        '#title' => $this->t('Content Type'),
        '#type' => 'select',
        '#default_value' => $this->options['header_node_type'],
        '#options' => $bundle_options,
      ];

      // Title Text / Heading
      $form['content'] = [
        '#title' => $this->t('Heading'),
        '#type' => 'textfield',
        '#default_value' => $this->options['content'],
      ];

      // Use Pager
      $form['pager_embed'] = [
        '#title' => $this->t('Use Pager'),
        '#type' => 'checkbox',
        '#default_value' => $this->options['pager_embed'],
      ];

      // Destination
      $form['header_destination'] = [
        '#title' => $this->t('Destination'),
        '#type' => 'textfield',
        '#default_value' => $this->options['header_destination'],
      ];

      // welche buttons?
      $form['text'] = [
        '#markup' => $this->t('Buttons:'),
      ];
      // neu
      $form['header_button_new'] = [
        '#title' => $this->t('New'),
        '#type' => 'checkbox',
        '#default_value' => $this->options['header_button_new'],
      ];
      // sort
      $form['header_button_sort'] = [
        '#title' => $this->t('Sorting'),
        '#type' => 'checkbox',
        '#default_value' => $this->options['header_button_sort'],
      ];

      $form['group_taxonomy_title'] = [
        '#markup' => '<div class="vat-views-option-group">' . $this->t('Show') . '</div>',
      ];


      for ($i = 1; $i <= 5; $i++) {

        // taxonomy 4
        $form['header_vocabulary_' . $i] = [
          '#title' => $this->t('Taxonomy ' . $i),
          '#type' => 'select',
          '#default_value' => $this->options['header_vocabulary_' . $i],
          '#options' => $vocabulary_options,
          '#prefix' => '<div class="vat-views-option-inline">',
          '#suffix' => '</div>',
        ];


      }


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


      $form['seperator'] = [
        '#title' => $this->t('Seperator'),
        '#type' => 'checkbox',
        '#default_value' => $this->options['seperator'],
        '#prefix' => '<div class="vat-views-option-inline">',
        '#suffix' => '</div>',
      ];


      $form['group_end'] = [
        '#markup' => '<div class="vat-views-option-group"></div>',
      ];



      // Icon Names for Iconfonts
      // ------------------------------

      // Title
      $form['group_design'] = [
        '#markup' => '<div class="vat-views-option-group">' . $this->t('Icon Name (without prefix)') . '</div>',
      ];

      // new
      $form['icon_new'] = [
        '#title' => $this->t('new'),
        '#type' => 'textfield',
        '#attributes' => array('maxlength' => 10, 'size' => 10),
        '#default_value' => $this->options['icon_new'],
        '#prefix' => '<div class="vat-views-option-inline">',
        '#suffix' => '</div>',
      ];

      // sort
      $form['icon_sort'] = [
        '#title' => $this->t('sort'),
        '#type' => 'textfield',
        '#attributes' => array('maxlength' => 10, 'size' => 10),
        '#default_value' => $this->options['icon_sort'],
        '#prefix' => '<div class="vat-views-option-inline">',
        '#suffix' => '</div>',
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
    }

    /**
     * {@inheritdoc}
     */
    public function render($empty = FALSE) {

      if (!$empty || !empty($this->options['empty'])) {


        // Destination
        if (empty($this->options['header_destination'])) {
          $view_id = \Drupal::routeMatch()->getParameter('view_id');
          $destination = str_replace('_', '-', $view_id);
        }
        else {
          $destination = $this->options['header_destination'];
        }


        // Taxonomy
        $taxonomy = [];
        for ($i = 1; $i <= 5; $i++) {

          if ($this->options['header_vocabulary_' . $i] != FALSE) {
            $taxonomy_term_name = $this->options['header_vocabulary_' . $i];
            $taxonomy[$i]['machine_name'] = $taxonomy_term_name;
            $taxonomy[$i]['title'] = self::_properTitle($taxonomy_term_name);
          }
          else {
            $taxonomy[$i] = FALSE;
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

        //  Icon Theme prefix
        if ($this->options['button_icon']) {

          switch ($this->options['icon_set']) {

            case 1: // 'Font Awesome 5'
              $prefix = 'fas fa-';
              break;

            case 2: // 'Bootstrap'
              $prefix = 'glyphicon glyphicon-';
              break;

            case 3:  // 'Drupal / jQuery Ui'
              $prefix = 'ui-icon ui-icon-';
              break;

            default: //'automatic'

              // Font Awesome
              //
              if (\Drupal::moduleHandler()->moduleExists('fontawesome')) {
                $prefix = 'fas fa-';
              }
              // Twitter Bootstap 3
              elseif (\Drupal::moduleHandler()
                ->moduleExists('bootstrap_library')) {
                $prefix = 'glyphicon glyphicon-';
              } // Drupal Default / jQuery UI Icons
              else {
                $prefix = 'ui-icon ui-icon-';
              }
              break;

          }
        }


        switch ($this->options['show_as']) {

          case  0: // Button:
            if (\Drupal::moduleHandler()->moduleExists('bootstrap_library')) {
              $button_class = 'btn btn-default vat-button';
            }
            else {
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

        $vat = [
          'test_var' => 'test',
          'button_new' => $this->options['header_button_new'],
          'button_sort' => $this->options['header_button_sort'],
          'button_text' => $this->options['header_button_text'],
          'seperator' => $this->options['seperator'],
          'list_taxonomy' => $taxonomy,
          'node_type' => $this->options['header_node_type'],
          'view_id' => $view_id,
          'destination' => $destination,
          'button_label' => $this->options['button_label'],
          'button_icon' => $this->options['button_icon'],
          'show_as' => $this->options['show_as'],
          'icon_set' => $this->options['icon_set'],
          'icon_prefix' => $prefix,
          'icon_size' => $this->options['icon_size'],
          'button_class' => $button_class,
          'size_class' => $size_class,

          // Icon names
          'icon_new' => $this->options['icon_new'],
          'icon_sort' => $this->options['icon_sort'],
          'icon_edit' => $this->options['icon_edit'],



        ];


        return [
          '#theme' => 'vat_header',
          '#vat' => $vat,
        ];

      }

      return [];
    }


    /**
     * Render a text area with \Drupal\Component\Utility\Xss::filterAdmin().
     */
    public function renderTextField($value) {
      if ($value) {
        return $this->sanitizeValue($this->tokenizeValue($value), 'xss_admin');
      }
      return '';
    }


    private function _properTitle($string) {

      $string = str_replace('_', ' ', $string);
      $string = ucwords($string);

      return $string;
    }

  }
