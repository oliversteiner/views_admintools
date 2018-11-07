<?php
/**
 * @file
 * Definition of
 * Drupal\views_admintools\Plugin\views\field\VatFieldAnchor
 */

namespace Drupal\views_admintools\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to add an anchor Tag.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("vat_field_anchor")
 */
class VatFieldAnchor extends FieldPluginBase
{

    /**
     * @{inheritdoc}
     */
    public function query()
    {
        // Leave empty to avoid a query on this field.
    }

    /**
     * Define the available options
     *
     * @return array
     */
    protected function defineOptions()
    {
        $options = parent::defineOptions();

        // ------ Group: anchor link ------
        $options['prefix'] = ['default' => TRUE];
        $options['bundle'] = ['default' => TRUE];
        $options['suffix'] = ['default' => TRUE];
        $options['separator'] = ['default' => '-'];
        $options['identification'] = ['default' => FALSE];

        // ------ Group: anchor Tag ------
        $options['css_class'] = ['default' => TRUE];

        return $options;
    }

    /**
     * Provide the options form.
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state)
    {

        // Nodes Types
        $types = NodeType::loadMultiple();
        $bundle_options = [];

        foreach ($types as $key => $type) {
            $bundle_options[$key] = $type->label();
        }

        // ------ Group: anchor link ------

        $form['group_link'] = [
            '#markup' => '<div class="vat-views-option-group">' . $this->t('Anchor Link') . '</div>',
        ];

        // Textfield  Prefix
        // Default: none

        $form['prefix'] = [
            '#title' => $this->t('Prefix'),
            '#type' => 'textfield',
            '#default_value' => $this->options['prefix'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];

        // Dropdown   Bundle
        // Default: bundle name

        $form['bundle'] = [
            '#title' => $this->t('Bundle'),
            '#type' => 'select',
            '#options' => $bundle_options,
            '#default_value' => $this->options['bundle'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];
        // Dropdown Identification
        // default: NID

        /*        $form['identification'] = [
                    '#title' => $this->t('Identification'),
                    '#type' => 'textfield',
                    '#default_value' => $this->options['identification'],
                    '#prefix' => '<div class="vat-views-option-inline">',
                    '#suffix' => '</div>',
                ];*/

        // Textfield  Suffix
        // Default: none

        $form['suffix'] = [
            '#title' => $this->t('Suffix'),
            '#type' => 'textfield',
            '#default_value' => $this->options['suffix'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];

        // Textfield separator
        // Default: "-"

        $form['separator'] = [
            '#title' => $this->t('Separator'),
            '#type' => 'textfield',
            '#default_value' => $this->options['separator'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];


        // ------ Group: anchor Tag ------

        $form['group_tag'] = [
            '#markup' => '<div class="vat-views-option-group">' . $this->t('Anchor Tag') . '</div>',
        ];

        // Textfield CSS Class
        // Default: none

        $form['css_class'] = [
            '#title' => $this->t('CSS Class'),
            '#type' => 'textfield',
            '#default_value' => $this->options['css_class'],
            '#prefix' => '<div class="vat-views-option-inline">',
            '#suffix' => '</div>',
        ];
        //
        // ------------------------------


        parent::buildOptionsForm($form, $form_state);
    }

    /**
     * @{inheritdoc}
     * @param ResultRow $values
     * @return mixed
     */
    public function render(ResultRow $values)
    {

        $nid = $values->_entity->id();

        // options id
        $bundle = $this->options['bundle'];
        $separator = $this->options['separator'];
        $prefix = $this->options['prefix'];
        $suffix = $this->options['suffix'];

        // options tag
        $css = $this->options['css_class'];

        // Build id
        $id = $prefix . $bundle . $separator . $nid . $suffix;

        // Build Elem
        // <a id="[title]">[title]</a>

        $elements['vat_anchor'] = [
            '#type' => 'html_tag',
            '#tag' => 'a',
            '#attributes' => [
                'id' => $id,
                'class' => $css,
            ],
            '#value' => '',
            '#prefix' => '<span class="vat-anchor">',
            '#suffix' => '</span>',
        ];

        return $elements;

    }


}
