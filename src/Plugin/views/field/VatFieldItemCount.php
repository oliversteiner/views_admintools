<?php
  /**
   * @file
   * Definition of Drupal\views_admintools\Plugin\views\field\VatFieldItemCount
   */

  namespace Drupal\views_admintools\Plugin\views\field;

  use Drupal\Core\Form\FormStateInterface;
  use Drupal\node\Entity\Node;
  use Drupal\views\Plugin\views\field\FieldPluginBase;
  use Drupal\views\ResultRow;

  /**
   * Field handler to flag the node type.
   *
   * @ingroup views_field_handlers
   *
   * @ViewsField("vat_field_item_count")
   */
  class VatFieldItemCount extends FieldPluginBase {


    public $output_element = [
      'row number',
      'div',
      'span',
    ];


    public $options_fields = [
      'image',
      'event_image',
      'video',
      'audio',
      'datei2',
    ];

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
      $options['field_with_items'] = ['default' => 0];
      $options['output_element'] = ['default' => 0];
      $options['css_class'] = ['default' => ''];

      return $options;
    }

    /**
     * Provide the options form.
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state) {


      $form['field_with_items'] = [
        '#title' => $this->t('Which field to Count the Items'),
        '#type' => 'select',
        '#default_value' => $this->options['field_with_items'],
        '#options' => $this->options_fields,
      ];


      $form['output_element'] = [
        '#title' => $this->t('Output'),
        '#type' => 'select',
        '#default_value' => $this->options['output_element'],
        '#options' => $this->output_element,
      ];

      $form['css_class'] = [
        '#title' => $this->t('CSS Class'),
        '#type' => 'textfield',
        '#default_value' => $this->options['css_class'],
      ];


      parent::buildOptionsForm($form, $form_state);
    }

    /**
     * @{inheritdoc}
     */
    public function render(ResultRow $values) {

      $elements = [];

      $entity = $this->getEntity($values);
      $nid = $entity->id();
      $node = Node::load($nid);


      // Get Field from Options
      $index = $this->options['field_with_items'];
      $name = $this->options_fields[$index];
      $fieldname = 'field_' . $name;


      if ($node->hasField($fieldname)) {

        // Load Items from Filed
        $arr_items = $node->get($fieldname)->getValue();

        // Count items
        $count = count($arr_items);

        // CSS Class Array
        $class = ['', $this->options['css_class']];

        $tag = $this->output_element[$this->options['output_element']];

        if ($tag == 'row number') {
          $elements= (int)$count;
        }
        else {
          // Output
          $elements[] = [
            '#type' => 'html_tag',
            '#tag' => $tag,
            '#attributes' => [
              'class' => $class,
            ],
            '#value' => $count,
          ];

        }
      }
      return $elements;
    }
  }