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

      $field_name = $items->getFieldDefinition()->getName();
      $setting = $items->getFieldDefinition()->getSetting('handler_settings');
      $target_bundles = $setting['target_bundles'];
      $target_bundle = reset($target_bundles); // First Element's Value*/
      $node = $items->getEntity();
      $target_nid = $node->id();
      $vocabulary = $target_bundle;

      // Default CSS Classes
      $default_classes = ['use-ajax', 'vat-toggle-tag'];

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

      foreach ($default_tags as $default_tag) {

        $term_id = $default_tag->tid;
        $term_name = $default_tag->name;

        // url for \Drupal\views_admintools\Controller\VatToggleTagController::toggleTag
        $url = Url::fromRoute('views_admintools.vat_toggle_tag',
          [
            'target_nid' => $target_nid,
            'term_tid' => $term_id,
            'field_name' => $field_name,
          ]);

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