<?php
  /**
   * Twig Filter.
   * User: ost
   * Date: 26.10.17
   * Time: 10:39
   *
   * Usage:
   *
   * {% set url_full = https://www.example.com %}
   * {% set url_readable = url_full | readable_url %}
   * {{ url_readable }} {# example.com #}
   *
   *
   *
   *  Using in Views Templates:
   *
   * set Output to
   *
   */

  namespace Drupal\views_admintools\TwigExtension;


  class readableUrl extends \Twig_Extension {

    /**
     * Generates a list of all Twig filters that this extension defines.
     */
    public function getFilters() {
      return [
        new \Twig_SimpleFilter('readable_url', [$this, 'readableUrl']),
      ];
    }

    /**
     * Gets a unique identifier for this Twig extension.
     */
    public function getName() {
      return 'views_admintools.twig_extension.readable_url';
    }

      /**
       * @param $string
       * @return mixed
       */
    public static function readableUrl($string) {
      $string = str_replace('https://www.', '', $string);
      $string = str_replace('http://www.', '', $string);
      $string = str_replace('https://', '', $string);
      $string = str_replace('http://', '', $string);

      return $string;

    }
  }