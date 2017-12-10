<?php

  namespace Drupal\views_admintools\Controller;

  use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
  use Drupal\Core\Ajax\AjaxResponse;
  use Drupal\Core\Ajax\InvokeCommand;
  use Drupal\Core\Ajax\ReplaceCommand;
  use Drupal\Core\Controller\ControllerBase;

  /**
   * Controller routines for page example routes.
   */
  class VatToggleTagController extends ControllerBase {


    /**
     * {@inheritdoc}
     */
    protected function getModuleName() {
      return 'views_admintools';
    }


    /**
     * @param $target_nid
     * @param $term_tid
     * @param $field_name
     *
     * @return \Drupal\Core\Ajax\AjaxResponse
     *
     */
    public static function toggleTag($target_nid, $term_tid, $field_name, $values) {

      $result = self::_toggleTag($target_nid, $term_tid, $field_name, $values);

      $response = new AjaxResponse();
      $selector = '#' . $field_name . '-' . $target_nid . '-' . $term_tid;


      if ($values === 1) {

        // remove all
        $selector_all = '.' . $field_name.'-'.$target_nid . '.vat-toggle-tag-single';


        $response->addCommand(new InvokeCommand($selector_all, 'removeClass', ['active']));

        // activate new
        $response->addCommand(new InvokeCommand($selector, 'addClass', ['active']));


      }
      else {

        if ($result['mode'] == 'add') {
          $response->addCommand(new InvokeCommand($selector, 'addClass', ['active']));
        }

        elseif ($result['mode'] == 'remove') {
          $response->addCommand(new InvokeCommand($selector, 'removeClass', ['active']));
        }


        else {
          $message = 'Es ist ein Fehler aufgetreten beim ändern der Empfängergruppe';
          $response->addCommand(new ReplaceCommand('.ajax-container',
            '<div class="ajax-container">' . $message . '</div>'));
        }
      }
      return $response;

    }

    /**
     * @param $target_nid
     * @param $term_tid
     * @param $field_name
     *
     * @param $value
     *
     * @return array
     * @throws \Drupal\Core\Entity\EntityStorageException
     */
    public static function _toggleTag($target_nid, $term_tid, $field_name, $value) {


      $output = [
        'status' => FALSE,
        'mode' => FALSE,
        'nid' => $target_nid,
        'tid' => $term_tid,
        'field_name' => $field_name,
      ];


      // Load Node
      try {
        $entity = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->load($target_nid);
      } catch (InvalidPluginDefinitionException $e) {
      }

      // Field OK?
      if (!empty($entity->{$field_name})) {

        // Load all items
        $all_terms = $entity->get($field_name)
          ->getValue();


        // take only tid
        $arr_item_id = [];
        foreach ($all_terms as $item) {
          $arr_item_id[] = $item['target_id'];
        }

        $item_id_unique = array_unique($arr_item_id);  // performace?
        $position = array_search($term_tid, $item_id_unique);

        if ($position !== FALSE) {

          // Remove Term
          unset($item_id_unique[$position]);

          $output['mode'] = 'remove';
        }

        else {
          // Add Term
          $item_id_unique[] = $term_tid;

          $output['mode'] = 'add';
        }

        // Apply Term changes
        $entity->$field_name->setValue($item_id_unique);
        $entity->save();
        $output['status'] = TRUE;
      }

      return $output;
    }

  }
