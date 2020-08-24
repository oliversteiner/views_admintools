<?php
  /**
   * @file
   * Definition of Drupal\views_admintools\Plugin\views\field\VatFieldToggleTag
   */

  namespace Drupal\views_admintools\Plugin\views\field;

  use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
  use Drupal\Core\Form\FormStateInterface;
  use Drupal\field\Entity\FieldStorageConfig;
  use Drupal\taxonomy\Entity\Vocabulary;
  use Drupal\views\Plugin\views\field\FieldPluginBase;
  use Drupal\Core\Url;
  use Drupal\views\ResultRow;

  /**
   * Field handler to List all Tags, active onces are marked.
   *
   * @ingroup views_field_handlers
   *
   * @ViewsField("vat_toggle_tag")
   */
  class VatFieldToggleTag extends FieldPluginBase {

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

      // Taxonomy Voc
      $options['vocabulary'] = ['default' => 0];

      // "Field_name"
      $options['entity_reference_field'] = ['default' => 0];

      // "Add New"
      $options['add_tag'] = ['default' => FALSE];

      //  "Add remove New"
      $options['remove_tag'] = ['default' => FALSE];

      return $options;
    }

    /**
     * Provide the options form.
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state) {


      // load Taxonomy Vocabulary
      $vocabularies = Vocabulary::loadMultiple();
      $options_voc = [];

      foreach ($vocabularies as $key => $type) {
        $options_voc[$key] = $type->label();
      }

      $form['vocabulary'] = [
        '#title' => $this->t('Taxonomy Field'),
        '#type' => 'select',
        '#default_value' => $this->options['vocabulary'],
        '#options' => $options_voc,
      ];


      // Get a List of all entity_reference-Fields
      $all_fields = \Drupal::service('entity_field.manager')->getFieldMap();
      $node_field = $all_fields['node'];
      $options_entity_reference_field = [];


      // remove unused fields
      $ignore = ['uid', 'revision_uid', 'type'];

      foreach ($node_field as $machine_name => $meta) {

        // only Field of Type entity_reference
        if (!in_array($machine_name, $ignore)) {
          if ($meta['type'] === 'entity_reference') {
            $options_entity_reference_field[$machine_name] = $machine_name;
          }
        }
      }

      // "Field Name"
      $form['entity_reference_field'] = [
        '#title' => $this->t('Field Name'),
        '#type' => 'select',
        '#default_value' => $this->options['entity_reference_field'],
        '#options' => $options_entity_reference_field,
      ];


      // "Add New Tag"
      $form['add_tag'] = [
        '#title' => $this->t('Add "New Tag"'),
        '#type' => 'checkbox',
        '#default_value' => $this->options['add_tag'],
      ];


      //  "Add Remove Tag"
      $form['remove_tag'] = [
        '#title' => $this->t('Add "remove Tag"'),
        '#type' => 'checkbox',
        '#default_value' => $this->options['remove_tag'],
      ];

      // Custom CSS Class
      $form['css_class'] = [
        '#title' => $this->t('Custom CSS Class'),
        '#type' => 'textfield',
        '#default_value' => $this->options['css_class'],
      ];

      parent::buildOptionsForm($form, $form_state);
    }

    /**
     * @{inheritdoc}
     * @param ResultRow $values
     * @return array
     * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
     */
    public function render(ResultRow $values) {


      // load Options
      $field_name = $this->options['entity_reference_field'];
      $vocabulary = $this->options['vocabulary'];

      // init renderoutput
      $elements = [];

      // Default CSS Classes
      $default_classes = ['use-ajax', 'vat-toggle-tag'];
      $all_terms_from_vocabulary = null;

      // load all Tags
      try {
        $all_terms_from_vocabulary = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadTree($vocabulary);
      } catch (InvalidPluginDefinitionException $e) {
        drupal_set_message('vocabulary not found');
      }

      // Load items from Row
      $node = $values->_entity;
      $target_nid = $values->_entity->id();

      // Load Active Tags

      if ($node->hasField($field_name)) {


        $storageConfig = FieldStorageConfig::loadByName('node', $field_name);

        $number_of_values = $storageConfig->getCardinality();

        // is field width Multible Values:
        if ($number_of_values === 1) {
          $default_classes[] = 'vat-toggle-tag-single';
        }
        else {
          $default_classes[] = 'vat-toggle-tag-multi';
        }


        $active_tags = $values->_entity->get($field_name)
          ->getValue();
        dpm($active_tags);


        // save only tid
        $active_tids = [];
        foreach ($active_tags as $active_tag) {
          $active_tids[] = $active_tag['target_id'];
        }


        foreach ($all_terms_from_vocabulary as $item) {

          $term_id = $item->tid;
          $term_name = $item->name;

          // url for SubscriberController::toggleSubsciberTag'
          $url = Url::fromRoute('views_admintools/views_admintools.vat_toggle_tag',
            [
              'target_nid' => $target_nid,
              'term_tid' => $term_id,
              'field_name' => $field_name,
              'multi' => $field_name,
            ]);



          // class
          if (in_array($term_id, $active_tids, true)) {
            $classes = $default_classes;
            $label = '<span>*</span>' . $term_name;
            $classes[] = 'active';
          }
          else {
            $label = $term_name;
            $classes = $default_classes;
          }

          // build
          $elements[$term_name] = [
            '#title' => ['#markup' => $label],
            '#type' => 'link',
            '#url' => $url,
            '#attributes' => [
              'class' => $classes,
              'id' => $field_name . '-' . $target_nid . '-' . $term_id,
            ],
          ];


        }

        // Add new
        if ($this->options['add_tag']) {
          $elements['add_tagadd_tag'] = [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#attributes' => [
              'class' => 'vat-toggle-tag vat-toggle-tag-add',
              'id' => $field_name . '-' . $target_nid . '-add-' . $this->options['vocabulary'],
              'data-vocabulary_id' => $this->options['vocabulary'],
            ],
            '#value' => '<i class="fa fa-plus-circle" aria-hidden="true"></i>',
          ];
        }

        // remove
        if ($this->options['remove_tag']) {
          $elements['remove_tag'] = [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#attributes' => [
              'class' => 'vat-toggle-tag vat-toggle-tag-remove',
              'id' => $field_name . '-' . $target_nid . '-add-' . $this->options['vocabulary'],
              'data-vocabulary_id' => $this->options['vocabulary'],
            ],
            '#value' => '<i class="fa fa-minus-circle" aria-hidden="true"></i>',
          ];
        }
      } // End has Field

      $elements['#attached']['library'][] = 'views_admintools/views_admintools.vat_toggle_tag';

      return $elements;

    }


  }
