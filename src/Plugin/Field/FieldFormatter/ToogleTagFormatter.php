<?php
  /**
   * Created by PhpStorm.
   * User: ost
   * Date: 07.11.17
   * Time: 01:49
   */

  namespace Drupal\views_admintools\Plugin\Field\FieldFormatter;


  use Drupal\Core\Entity\EntityInterface;
  use Drupal\Core\Entity\Exception\UndefinedLinkTemplateException;
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
      return [
          'add_tag' => FALSE,
          'remove_tag' => FALSE,
        ] + parent::defaultSettings();
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

      $entity = $items->getFieldDefinition();
      $field_name = $items->getFieldDefinition()->getName();

      $setting = $items->getFieldDefinition()->getSetting('handler_settings');
      $target_bundles = $setting['target_bundles'];
      $target_bundle = reset($target_bundles); // First Element's Value*/

      $node = $items->getEntity();

      $target_nid = $node->id();
      $bundle = $node->bundle();
      $entity_type = 'node';

      /*
            // Load Node
            $node = \Drupal::entityTypeManager()
              ->getStorage('node')
              ->load($node_id);

            $bundle = $node->bundle();

            // Get Field Definition
            $entityManager = \Drupal::service('entity_field.manager');
            $fields = $entityManager->getFieldDefinitions($entity_type, $bundle);
            $field_definition = $fields[$field_name];

            // target_bundles
            $setting = $field_definition->getSetting('handler_settings');
            $target_bundles = $setting['target_bundles'];

            // get first item from array
            $target_bundle = reset($target_bundles); // First Element's Value*/


      // load Options
      //  $vocabulary = $referencedEntity->label();

      $vocabulary = $target_bundle;
      // init renderoutput

      // Default CSS Classes
      $default_classes = ['use-ajax', 'vat-toggle-tag'];

     // kint($vocabulary);


      // init renderoutput
      $elements = [];

      // Default CSS Classes
      $default_classes = ['use-ajax', 'vat-toggle-tag'];


      $default_tags = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadTree($vocabulary);


      // ToDo: Check if multiple
      $active_tags = $node->get($field_name)
        ->getValue();


     // kint($active_tags);

      // save only tid
      $active_tids = [];
      foreach ($active_tags as $active_tag) {
        $active_tids[] = $active_tag['target_id'];
      }

      kint($active_tids);


      foreach ($default_tags as $default_tag) {

        $term_id = $default_tag->tid;
        $term_name = $default_tag->name;


     //   kint($term_name);

        // url for SubscriberController::toggleSubsciberTag'
        $url = Url::fromRoute('views_admintools.vat_toggle_tag',
          [
            'target_nid' => $target_nid,
            'term_tid' => $term_id,
            'field_name' => $field_name,
          ]);

      //  kint($url);


        // class
        if (in_array($term_id, $active_tids)) {
          $class = $default_classes;
          $label = '<span>*</span>' . $term_name;
          array_push($class, 'active');
        }
        else {
          $label = $term_name;
          $class = $default_classes;

        }


        // build
        $elements[] = [
          '#title' => ['#markup' => $label],
          '#type' => 'link',
          '#url' => $url,
          '#attributes' => [
            'class' => $class,
            'id' => $field_name . '-' . $target_nid . '-' . $term_id,
          ],
        ];


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


     //  $elements['#attached']['library'][] = 'views_admintools/views_admintools.vat_toggle_tag';

/*
      $elements['add_tag'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => 'test',
      ];*/

 /*     foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
        $label = $entity->label();
        // If the link is to be displayed and the entity has a uri, display a
        // link.

        kint($delta);

          $elements[$delta] = ['#plain_text' => $label];
        $elements[$delta]['#cache']['tags'] = $entity->getCacheTags();
      }*/

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