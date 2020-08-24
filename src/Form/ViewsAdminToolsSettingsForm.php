<?php

namespace Drupal\views_admintools\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views_admintools\Controller\ViewsAdmintoolsController;

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
  function getIconPrefixOption()
  {
    return ViewsAdmintoolsController::getIconPrefixOption();
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

    // Turn Off Form Autocompletion
    // -------------------------------
    $form['#attributes']['autocomplete'] = 'off';

    // Options Icon Set
    // -------------------------------
    $options = $this->getIconSetOption();
    $options_default_icon_set = $config->get('icon_set');
    $form['icon_set'] = array(
      '#type' => 'select',
      '#options' => $options,
      '#title' => $this->t('Default Icon Set'),
      '#default_value' => $options_default_icon_set
    );

    // Options Icon Prefix
    // -------------------------------
    $options = $this->getIconPrefixOption();
    $options_default_icon_prefix = $config->get('icon_prefix');
    $form['icon_prefix'] = array(
      '#type' => 'select',
      '#options' => $options,
      '#title' => $this->t('Default Icon Prefix'),
      '#default_value' => $options_default_icon_prefix
    );

    // Default Icon Names
    // -------------------------------

    // Icon Names for Iconfonts
    // ------------------------------

    // Title
    $form['group_iconfonts_start'] = [
      '#type' => 'html_tag',
      '#tag' => 'label',
      '#value' => $this->t('Icon Name (without prefix)'),
      '#prefix' => '<fieldset class="vat-options-group">'
    ];



    // edit
    $form['icon_edit'] = [
      '#title' => $this->t('edit'),
      '#type' => 'textfield',
      '#attributes' => array('maxlength' => 10, 'size' => 10),
      '#default_value' => $config->get('icon_edit'),
      '#prefix' => '<span class="vat-options-inline">',
      '#suffix' => '</span>'
    ];

    // publish
    $form['icon_publish'] = [
      '#title' => $this->t('publish'),
      '#type' => 'textfield',
      '#attributes' => array('maxlength' => 10, 'size' => 10),
      '#default_value' => $config->get('icon_publish'),
      '#prefix' => '<span class="vat-options-inline">',
      '#suffix' => '</span>'
    ];

    // unpublish
    $form['icon_unpublish'] = [
      '#title' => $this->t('unpublish'),
      '#type' => 'textfield',
      '#attributes' => array('maxlength' => 10, 'size' => 10),
      '#default_value' => $config->get('icon_unpublish'),
      '#prefix' => '<span class="vat-options-inline">',
      '#suffix' => '</span>'
    ];

    // delete
    $form['icon_delete'] = [
      '#title' => $this->t('delete'),
      '#type' => 'textfield',
      '#attributes' => array('maxlength' => 10, 'size' => 10),
      '#default_value' => $config->get('icon_delete'),
      '#prefix' => '<span class="vat-options-inline">',
      '#suffix' => '</span>'
    ];

    // edit vocabulary
    $form['icon_vocabulary'] = [
      '#title' => $this->t('edit vocabulary'),
      '#type' => 'textfield',
      '#attributes' => array('maxlength' => 10, 'size' => 10),
      '#default_value' => $config->get('icon_vocabulary'),
      '#prefix' => '<span class="vat-options-inline">',
      '#suffix' => '</span>'
    ];

    $form['group_iconfonts_end'] = [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#suffix' => '</fieldset>'
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // Load config
    $this->configFactory
      ->getEditable('views_admintools.settings')

      // Set Config Value
      // Icon Set
      ->set('icon_set', $form_state->getValue('icon_set'))

      // Icon Prefix
      ->set('icon_prefix', $form_state->getValue('icon_prefix'))

      // Icon Names
      ->set('icon_edit', $form_state->getValue('icon_edit'))
      ->set('icon_publish', $form_state->getValue('icon_publish'))
      ->set('icon_unpublish', $form_state->getValue('icon_unpublish'))
      ->set('icon_delete', $form_state->getValue('icon_delete'))
      ->set('icon_vocabulary', $form_state->getValue('icon_vocabulary'))

      // Save
      ->save();

    parent::submitForm($form, $form_state);
  }
}
