<?php
  /**
   * @file
   * Definition of Drupal\views_admintools\Plugin\views\field\VatGroupByField
   */

  namespace Drupal\views_admintools\Plugin\views\field;

  use Drupal\Core\Form\FormStateInterface;
  use Drupal\taxonomy\Entity\Term;
  use Drupal\views\Plugin\views\field\FieldPluginBase;
  use Drupal\views\ResultRow;
  use Drupal\taxonomy\Entity\Vocabulary;

  /**
   * Field handler to add Edit and Delete Buttons.
   *
   * @ingroup views_field_handlers
   *
   * @ViewsField("vat_group_by_field")
   */
  class VatGroupByField extends FieldPluginBase {


    var $html_tags = ['div', 'span', 'h1', 'h2', 'h3', 'h4'];


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
      $options['group_by_field_name'] = ['default' => FALSE];
      $options['group_by_vocabulary'] = ['default' => '-'];
      $options['group_by_html_tag'] = ['default' => 0];
      $options['group_by_class'] = ['default' => ''];


      return $options;
    }

    /**
     * Provide the options form.
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state) {


      $form['text'] = [
        '#markup' => $this->t('Fieldname to group'),
      ];

      $form['group_by_field_name'] = [
        '#title' => $this->t('Field Name'),
        '#type' => 'textfield',
        '#default_value' => $this->options['group_by_field_name'],
      ];


      $form['group_by_html_tag'] = [
        '#title' => $this->t('HTML Tag'),
        '#type' => 'select',
        '#default_value' => $this->options['group_by_html_tag'],
        '#options' => $this->html_tags,
      ];

      $form['group_by_class'] = [
        '#title' => $this->t('CSS Class'),
        '#type' => 'textfield',
        '#default_value' => $this->options['group_by_class'],
      ];

      // or voc
      $vocabularies = Vocabulary::loadMultiple();

      $options_voc = [];
      $options_voc[] = '-';

      foreach ($vocabularies as $key => $type) {
        $options_voc[$key] = $type->label();
      }
      $form['group_by_vocabulary'] = [
        '#title' => $this->t('Taxonomy Feld'),
        '#type' => 'select',
        '#default_value' => $this->options['group_by_vocabulary'],
        '#options' => $options_voc,
      ];


      parent::buildOptionsForm($form, $form_state);
    }

    /**
     * @{inheritdoc}
     */
    public function render(ResultRow $values) {

      // Options
      $options['class'] = $this->options['group_by_class'];
      $options['tag'] = $this->options['group_by_html_tag'];
      $options['field'] = $this->options['group_by_field_name'];
      $options['vocabulary'] = $this->options['group_by_vocabulary'];

      // CSS Class Array
      $class = ['vat-group', $options['class']];
      $text = '';

      if (($options['field'] != FALSE) OR ($options['vocabulary'] != '-')) {

        // Load Entitys
        $entity = $values->_entity;

        if ($options['vocabulary'] != '-') {
          $group_by_field_name = $options['vocabulary'];
        }
        else {
          $group_by_field_name = $options['field'];
        }


        // ist ein solches Feld vorhanden?

        $fieldname = 'field_' . $group_by_field_name;


        // Get Taxonomy ID
        if ($entity->hasField($fieldname)) {

          // get Field Content
          $field_content = $entity->get($fieldname)->getValue();



          // Get Taxonomy ID
          $tid_new = $field_content[0]['target_id'];


          // check if last tid is same as new tid
          $tid_old = \Drupal::config('views_admintools.settings')
            ->get('group_by');


          if ($tid_old != $tid_new) {

            // Store New tid globaly:
            $config = \Drupal::service('config.factory')
              ->getEditable('views_admintools.settings');
            $config->set('group_by', $tid_new)->save();

            // Get Taxonomy Name from tid
            $term_name = Term::load($tid_new)
              ->get('name')->value;

            // Output
            $text = $term_name;
            $class[] = 'vat-group-new';
          }

          else {

            // Output
            $text = '';
            $class[] = 'vat-group-old';

          }

        }

      }

      $tag = $this->html_tags[$options['tag']];


      $elements['vat_group_by'] = [
        '#type' => 'html_tag',
        '#tag' => $tag,
        '#attributes' => [
          'class' => $class,
        ],
        '#value' => $text,

      ];


      $elements['#attached']['library'][] = 'views_admintools/views_admintools.group_by';

      return $elements;

    }

  }