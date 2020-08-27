<?php

namespace Drupal\views_admintools\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views_admintools\Controller\ViewsAdmintoolsController;
use Symfony\Component\HttpFoundation\Response;

class ViewsAdminToolsSettingsForm extends ConfigFormBase
{
  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'views_admintools_settings_form';
  }

  /**
   * @return string[]
   */
  function getIconSetOption()
  {
    return ViewsAdmintoolsController::getIconSetOption();
  }

  /**
   * @return string[]
   */
  function getIconVariantOption()
  {
    return ViewsAdmintoolsController::getIconVariantOption();
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return ['views_admintools.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    // Default settings.
    $config = $this->config('views_admintools.settings');
    $icon_sets = ViewsAdmintoolsController::getIconSets();

    // Turn Off Form Autocompletion
    $form['#attributes']['autocomplete'] = 'off';

    // Add Library
    $form['#attached'] = array(
      'library' => array('views_admintools/icon_sets'),
      'drupalSettings' => ['iconSets' => $icon_sets]
    );

    // Open Details and Fieldset
    // Set Icon Names Manually
    $form['group_iconfonts_start'] = [
      '#type' => 'html_tag',
      '#tag' => 'label',
      '#value' => $this->t(''),
      '#prefix' => '<details><summary>Icon Set and Names</summary><fieldset>'
    ];

    // Options Icon Set
    $options = $this->getIconSetOption();
    $options_default_icon_set = $config->get('icon_set');
    $form['icon_set'] = array(
      '#type' => 'select',
      '#options' => $options,
      '#title' => $this->t('Default Icon Set'),
      '#default_value' => $options_default_icon_set
    );

    // Options Icon Variant (fas, far, ...)
    $options = $this->getIconVariantOption();
    $options_default_icon_variant = $config->get('icon_variant');
    $form['icon_variant'] = array(
      '#type' => 'select',
      '#options' => $options,
      '#title' => $this->t('Default Icon Variant'),
      '#default_value' => $options_default_icon_variant
    );

    // Set Icon Names without ...
    $form['remark'] = array(
      '#markup' => 'Set Icon Names without prefix (fa-)'
    );

    // Get Icons from Icon-Sets
    // Load "Drupal"-Set and
    // build textform-field for each icon
    foreach ($icon_sets['drupal']['icons'] as $icon) {
      $name = 'icon_' . $icon['name'];
      $form[$name] = [
        '#title' => $this->t($icon['name']),
        '#type' => 'textfield',
        '#attributes' => array('maxlength' => 10, 'size' => 10),
        '#default_value' => $config->get($name),
        '#prefix' => '<span class="vat-options-inline">',
        '#suffix' => '</span>'
      ];
    }

    // Close Fieldset and Details
    $form['group_iconfonts_end'] = [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#suffix' => '</fieldset></details>'
    ];

    // Add Default Sets via icon-sets.html.twig
    $form['default_icon_sets'] = [
      '#theme' => 'icon_sets',
      '#name' => 'icon_sets',
      '#cache' => ['max-age' => 0]
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // Load Icon List
    $icon_list = ViewsAdmintoolsController::getIconList();

    // Load config
    $config = \Drupal::service('config.factory')->getEditable('views_admintools.settings');

    // Icon Set
    $config->set('icon_set', $form_state->getValue('icon_set'));

    // Icon Variant
    $config->set('icon_variant', $form_state->getValue('icon_variant'));

    // Icon Names
    foreach ($icon_list as $icon) {
      $config->set('icon_' .$icon['name'], $form_state->getValue('icon_' . $icon['name']));
    }

    // Save Config
    $config->save();

    parent::submitForm($form, $form_state);
  }
}
