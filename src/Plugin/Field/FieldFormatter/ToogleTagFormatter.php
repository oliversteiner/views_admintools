<?php
  /**
   * Created by PhpStorm.
   * User: ost
   * Date: 07.11.17
   * Time: 01:49
   */

  namespace Drupal\views_admintools\Plugin\Field\FieldFormatter;


  use Drupal\Core\Entity\EntityInterface;
  use Drupal\Core\Field\FieldItemListInterface;
  use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
  use Drupal\Core\Form\FormStateInterface;
  use Drupal\Core\Url;


  /**
   * Plugin implementation of the 'toogle_tag' formatter.
   *
   * @FieldFormatter(
   *   id = "toogle_tag_formatter",
   *   label = @Translation("Tag Style with toggle"),
   *   field_types = {
   *     "entity_reference"
   *   }
   * )
   */
  class ToogleTagFormatter extends EntityReferenceFormatterBase {


    /**
     * {@inheritdoc}
     */
    public static function defaultSettings() {
      $default_settings = [
          'add_tag' => FALSE,
          'remove_tag' => FALSE,
        ] + parent::defaultSettings();


      return $default_settings;
    }

    /**
     * {@inheritdoc}
     */
    public function settingsForm(array $form, FormStateInterface $form_state) {

      // "Add New Tag"
      $elements['add_tag'] = [
        '#title' => $this->t('Add "New Tag"'),
        '#type' => 'checkbox',
        '#default_value' => $this->getSetting('add_tag'),
      ];


      //  "Add Remove Tag"
      $elements['remove_tag'] = [
        '#title' => $this->t('Add "remove Tag"'),
        '#type' => 'checkbox',
        '#default_value' => $this->getSetting('remove_tag'),
      ];

      return $elements;
    }

    /**
     * {@inheritdoc}
     */
    public function settingsSummary() {
      $summary = [];
      //  $summary[] = $this->getSetting('link') ? t('Link to the referenced entity') : t('No link');
      return $summary;
    }

    /**
     * {@inheritdoc}
     */
    public function viewElements(FieldItemListInterface $items, $langcode) {
      $elements = [];

      $field_name = $items->getFieldDefinition()->getName();
      $setting = $items->getFieldDefinition()->getSetting('handler_settings');

      // -1 for Multi-Field  / 1 for Single Field
      $number_of_values = $items->getFieldDefinition()
        ->getFieldStorageDefinition()
        ->getCardinality();


      $target_bundles = $setting['target_bundles'];
      $target_bundle = reset($target_bundles); // First Element's Value*/
      $node = $items->getEntity();
      $target_nid = $node->id();
      $vocabulary = $target_bundle;


      // Default CSS Classes
      $default_classes = [
        'use-ajax',
        'vat-toggle-tag',
        $field_name . '-' . $target_nid,
      ];

      if ($number_of_values === 1) {
        $default_classes[] = 'vat-toggle-tag-single';
        $prefix_class = 'vat-toggle-tag-single';
      }
      else {
        $default_classes[] = 'vat-toggle-tag-multi';
        $prefix_class = 'vat-toggle-tag-multi';

      }

      $default_tags = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadTree($vocabulary);

      $active_tags = $node->get($field_name)
        ->getValue();

      // save only tid
      $active_tids = [];
      foreach ($active_tags as $active_tag) {
        $active_tids[] = $active_tag['target_id'];
      }

      $elements[] = [
        '#prefix' => '<div class = "' . $prefix_class . '">',
      ];

      $number_of_items = count($default_tags);

      $count = 1;
      foreach ($default_tags as $default_tag) {

        $term_id = $default_tag->tid;
        $term_name = $default_tag->name;

        // url for \Drupal\views_admintools\Controller\VatToggleTagController::toggleTag
        $url = Url::fromRoute('views_admintools.vat_toggle_tag',
          [
            'target_nid' => $target_nid,
            'term_tid' => $term_id,
            'field_name' => $field_name,
            'values' => $number_of_values,
          ]);

        // class
        if (in_array($term_id, $active_tids)) {
          $class = $default_classes;
          $label = '<span></span>' . $term_name;
          array_push($class, 'active');
        }
        else {
          $label = $term_name;
          $class = $default_classes;
        }

        // First
        if ($count === 1) {
          $class[] = 'first';
        };

        // Last
        if ($count === $number_of_items) {
          $class[] = 'last';
        };


        // build
        $elements[] = [
          '#title' => ['#markup' => $label],
          '#type' => 'link',
          '#url' => $url,
          '#attributes' => [
            'class' => $class,
            'id' => $field_name . '-' . $target_nid . '-' . $term_id,
            'data-number-of-values' => $number_of_values,
          ],
          '#ajax' => ['progress' => ['type' => 'none']],
        ];

        //
        $count++;
      }


      // Add new
      if ($this->getSetting('add_tag')) {
        $elements[] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => [
            'class' => 'vat-toggle-tag vat-toggle-tag-add',
            'id' => $field_name . '-' . $target_nid . '-add-' . $vocabulary,
            'data-vocabulary_id' => $vocabulary,
          ],
          '#value' => '<i class="fa fa-plus-circle" aria-hidden="true"></i>',
        ];
      }


      // remove
      if ($this->getSetting('remove_tag')) {
        $elements[] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => [
            'class' => 'vat-toggle-tag vat-toggle-tag-remove',
            'id' => $field_name . '-' . $target_nid . '-add-' . $vocabulary,
            'data-vocabulary_id' => $vocabulary,
          ],
          '#value' => '<i class="fa fa-minus-circle" aria-hidden="true"></i>',
        ];
      }

      $elements[] = [
        '#suffix' => '</div>',
      ];

      $elements['#attached']['library'][] = 'views_admintools/views_admintools.vat_toggle_tag';


      return $elements;
    }

    /**
     * {@inheritdoc}
     */
    protected
    function checkAccess(EntityInterface $entity) {
      return $entity->access('view label', NULL, TRUE);
    }
  }