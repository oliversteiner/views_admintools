<?php
  /**
   * @file
   * Definition of Drupal\views_admintools\Plugin\views\field\VatFieldAdminButtons
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
  class VatFieldAdminButtons extends FieldPluginBase {

    /**
     * @{inheritdoc}
     */
    public function query() {
      // Leave empty to avoid a query on this field.
    }

    /**
     * Define the available options
     *
     * @return array
     */
    protected function defineOptions() {
      $options = parent::defineOptions();
      $options['row_button_edit'] = ['default' => TRUE];
      $options['row_button_delete'] = ['default' => TRUE];
      $options['row_button_label'] = ['default' => FALSE];
      $options['row_button_icon'] = ['default' => TRUE];
      $options['row_button_tag'] = ['default' => 'button'];
      $options['row_button_class'] = ['default' => FALSE];
      $options['row_button_destination'] = ['default' => FALSE];

      return $options;
    }

    /**
     * Provide the options form.
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state) {

      $form['text'] = [
        '#markup' => $this->t('Choose Buttons:'),
      ];

      $form['row_button_edit'] = [
        '#title' => $this->t('Edit'),
        '#type' => 'checkbox',
        '#default_value' => $this->options['row_button_edit'],
      ];

      $form['row_button_delete'] = [
        '#title' => $this->t('delete'),
        '#type' => 'checkbox',
        '#default_value' => $this->options['row_button_delete'],
      ];


      $form['text_look'] = [
        '#markup' => $this->t('Button look '),
      ];

      $form['row_button_label'] = [
        '#title' => $this->t('show icon'),
        '#type' => 'checkbox',
        '#default_value' => $this->options['row_button_icon'],
      ];

      $form['row_button_icon'] = [
        '#title' => $this->t('show label'),
        '#type' => 'checkbox',
        '#default_value' => $this->options['row_button_label'],
      ];

      $options = ['button', 'link'];

      $form['row_button_tag'] = [
        '#title' => $this->t('Display as'),
        '#type' => 'select',
        '#default_value' => $this->options['row_button_tag'],
        '#options' => $options,
      ];

      parent::buildOptionsForm($form, $form_state);
    }

    /**
     * @{inheritdoc}
     */
    public function render(ResultRow $values) {

      $node = $values->_entity;
      $bundle = $node->bundle();
      $nid = $values->_entity->id();
      $view_display_destination = $bundle . '-admin';

      $buttons = ['edit', 'delete'];
      $elements = [];


      // Destination Display
      if ($this->options['row_button_destination'] != FALSE) {
        $view_display_destination = $this->options['row_button_destination'];
      }


      foreach ($buttons as $button_name) {

        // if Button selected
        $options_button_active = 'row_button_' . $button_name;

        if ($this->options[$options_button_active]) {


          $link = 'href="node/' . $nid;

          // Options Class
          $option_class = $this->options['row_button_class'];
          $class = ['use-ajax', 'vat-button', $option_class];

          // Link
          switch ($button_name) {

            case 'edit':
              $link = 'node/' . $nid . '/edit?destination=' . $view_display_destination;
              $icon_name = 'pencil';
              $class[] = 'vat-button-edit';
              break;

            case 'delete':
              $link = 'node/' . $nid . '/delete?destination=' . $view_display_destination;
              $icon_name = 'trash';
              $class[] = 'vat-button-delete';
              break;

            default:
              $link = 'node/' . $nid;
              $icon_name = FALSE;
              break;

          }


          // Options: Icon
          if ($this->options['row_button_icon']) {
            $icon = '<span class="glyphicon glyphicon-' . $icon_name . '" aria-hidden="true"></span>';
          }

          // Options: Label
          if ($this->options['row_button_label']) {
            $label = $this->t($button_name);
          }


          // Options: display
          switch ($this->options['row_button_tag']) {

            case 'link':
              $class[] = 'link';
              break;

            default:
              $class[] = 'btn';
              $class[] = 'btn-sm';
              $class[] = 'btn-default';
              break;
          }


          $elements[$button_name] = [
            '#title' => $this->t($button_name),
            '#type' => 'link',
            '#url' => Url::fromUri('internal:/' . $link),
            '#attributes' => [
              'class' => $class,
              'data-dialog-type' => 'modal',
              'data-vat-icon' => $icon_name,
              'type' => 'button',
            ],
          ];
        }
      }

      $elements['#attached']['library'][] ='views_admintools/views_admintools.enable';

      return $elements;

    }
  }