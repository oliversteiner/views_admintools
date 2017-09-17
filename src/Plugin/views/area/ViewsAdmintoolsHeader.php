<?php

  namespace Drupal\views_admintools\Plugin\views\area;

  use Drupal\Core\Form\FormStateInterface;
  use Drupal\node\Entity\NodeType;
  use Drupal\views\Plugin\views\area\TokenizeAreaPluginBase;
  use Drupal\views\Views;


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
      $options['header_button_taxonomy'] = ['default' => FALSE];
      $options['header_button_text'] = ['default' => ''];


      return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state) {
      parent::buildOptionsForm($form, $form_state);


      // load all bundles
      $types = NodeType::loadMultiple();
      $bundle_options = [];
      foreach ($types as $key => $type) {
        $bundle_options[$key] = $type->label();
      }

      // Which Node Type ?
      $form['header_node_type'] = [
        '#title' => $this->t('Node Type'),
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

      // welche buttons?
      $form['text'] = [
        '#markup' => $this->t('Choose Buttons:'),
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
      $form['header_button_taxonomy'] = [
        '#title' => $this->t('Taxonomy, comma-seperated'),
        '#type' => 'textfield',
        '#default_value' => $this->options['header_button_taxonomy'],
      ];

      // text
      $form['text'] = [
        '#title' => $this->t('Text'),
        '#type' => 'textfield',
        '#default_value' => $this->options['header_button_text'],
      ];

    }

    /**
     * {@inheritdoc}
     */
    public function render($empty = FALSE) {

      if (!$empty || !empty($this->options['empty'])) {

        // Get Current View
        $view_id = \Drupal::routeMatch()->getParameter('view_id');
        $destination = str_replace('_', '-', $view_id);

        // Taxonomy
        if($this->options['header_button_taxonomy'] != false){
          $taxonomy = explode(',', $this->options['header_button_taxonomy']);

        }
        else{
          $taxonomy = false;
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


  }
