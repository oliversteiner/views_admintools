<?php

/**
 * @file
 * Contains
 *   \Drupal\views_admintools_icon\Plugin\views\display_extender\HeadMetadata.
 */

namespace Drupal\views_admintools\Plugin\views\display_extender;

use Drupal\node\Entity\NodeType;
use Drupal\views\Plugin\views\display_extender\DisplayExtenderPluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Dashboard display extender plugin.
 *
 * @ingroup views_display_extender_plugins
 *
 * @ViewsDisplayExtender(
 *   id = "views_admintools_icon",
 *   title = @Translation("Views Icon display extender"),
 *   help = @Translation("Add an Icon field to Views"), no_ui = FALSE
 * )
 */
class IconExtender extends DisplayExtenderPluginBase {


  /**
   * Provide the key options for this plugin.
   */

  public function defineOptionsAlter(&$options) {

    $options['views_admintools_icon'] = [
      'contains' => [
        'icon' => ['default' => ''],
      ],
    ];
  }

  /**
   * Provide the default summary for options and category in the views UI.
   */
  public function optionsSummary(&$categories, &$options) {
    $categories['views_admintools_icon'] = [
      'title' => t('Icon'),
      'column' => 'second',
    ];
    $views_admintools_icon = $this->iconExtenderEnabled() ? $this->getIconExtenderValues() : FALSE;
    $options['views_admintools_icon'] = [
      'category' => 'views_admintools_icon',
      'title' => t('Icon'),
      'value' => $views_admintools_icon ? $views_admintools_icon['icon'] : $this->t('no'),
    ];
  }

  /**
   * Provide a form to edit options for this plugin.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {

    if ($form_state->get('section') == 'views_admintools_icon') {
      $form['#title'] .= t('The metadata for this display');
      $views_admintools_icon = $this->getIconExtenderValues();

      $form['views_admintools_icon']['#type'] = 'container';
      $form['views_admintools_icon']['#tree'] = TRUE;

      $icon = $views_admintools_icon['icon'] ?? '';

      // Title
      $form['views_admintools_icon']['icon'] = [
        '#title' => $this->t('Icon'),
        '#type' => 'textfield',
        '#description' => $this->t('Icon | Example: fal fa-home'),
        '#default_value' => $icon,
      ];


    }
  }

  /**
   * Validate the options form.
   */
  public function validateOptionsForm(&$form, FormStateInterface $form_state) {
  }

  /**
   * Handle any special handling on the validate form.
   */
  public function submitOptionsForm(&$form, FormStateInterface $form_state) {
    if ($form_state->get('section') == 'views_admintools_icon') {
      $views_admintools_icon = $form_state->getValue('views_admintools_icon');
      dpm($views_admintools_icon);
      $this->options['views_admintools_icon'] = $views_admintools_icon;
    }
  }

  /**
   * Set up any variables on the view prior to execution.
   */
  public function preExecute() {
  }

  /**
   * Inject anything into the query that the display_extender handler needs.
   */
  public function query() {
  }

  /**
   * Static member function to list which sections are defaultable
   * and what items each section contains.
   */
  public function defaultableSections(&$sections, $section = NULL) {
  }

  /**
   * Identify whether or not the current display has custom metadata defined.
   */
  public function iconExtenderEnabled(): bool {
    $views_admintools_icon = $this->getIconExtenderValues();
    return !empty($views_admintools_icon['icon']);
  }

  /**
   * Get the head metadata configuration for this display.
   *
   * @return array
   *   The head metadata values.
   */
  public function getIconExtenderValues() {
    return $this->options['views_admintools_icon'] ?? '';
  }


}
