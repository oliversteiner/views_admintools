<?php

  namespace Drupal\views_admintools\Plugin\views\area;

  use Drupal\Core\Form\FormStateInterface;
  use Drupal\node\Entity\NodeType;
  use Drupal\taxonomy\Entity\Vocabulary;
  use Drupal\views\Plugin\views\area\TokenizeAreaPluginBase;
  use Drupal\views\Views;
  use function GuzzleHttp\Psr7\str;


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
     function defineOptions() {
      $options = parent::defineOptions();

      // Override defaults to from parent.
      $options['tokenize']['default'] = FALSE;
      $options['empty']['default'] = TRUE;

      // Provide our own defaults.
      $options['content'] = ['default' => ''];
      $options['pager_embed'] = ['default' => FALSE];

      // Bundle
      $options['header_node_type'] = ['default' => 'article'];
      $options['header_vocabulary'] = ['default' => ''];


      // Buttons
      $options['header_button_new'] = ['default' => TRUE];
      $options['header_button_sort'] = ['default' => FALSE];
      $options['header_button_text'] = ['default' => ''];
      $options['header_destination'] = ['default' => ''];


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
      // taxonomy
      $form['header_vocabulary'] = [
        '#title' => $this->t('Taxonomy'),
        '#type' => 'select',
        '#default_value' => $this->options['header_vocabulary'],
        '#options' => $vocabulary_options,
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
        else{
          $destination = $this->options['header_destination'];
        }


        // Taxonomy
        if($this->options['header_vocabulary'] != false){
          $taxonomy_term_name = explode(',', $this->options['header_vocabulary']);

        }
        else{
          $taxonomy_term_name = false;
        }

        $taxonomy = [];

        $i = 0;
        foreach ($taxonomy_term_name as $item){
          $taxonomy[$i]['machine_name'] = $item;
          $taxonomy[$i]['title'] = self::_properTitle($item);
        }



        $vat = [
          'test_var' => 'test',
          'button_new' => $this->options['header_button_new'],
          'button_sort' => $this->options['header_button_sort'],
          'button_text' => $this->options['header_button_text'],
          'list_taxonomy' => $taxonomy,
          'node_type' => $this->options['header_node_type'],
          'view_id' => $view_id,
          'destination' => $destination,
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


    private function _properTitle($string){

      $string = str_replace('_', ' ', $string);
      $string = ucwords($string);

      return $string;
    }

  }
