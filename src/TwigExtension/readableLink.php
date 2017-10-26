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


  class readableLink extends \Twig_Extension {

    /**
     * Generates a list of all Twig filters that this extension defines.
     */
    public function getFilters() {
      return [
        new \Twig_SimpleFilter('readable_link', [$this, 'readableLink']),
      ];
    }

    /**
     * Gets a unique identifier for this Twig extension.
     */
    public function getName() {
      return 'views_admintools.twig_extension.readable_link';
    }

    /**
     * @param $string
     *
     * @return string
     */
    public static function readableLink($string) {

      $url = $string;
      $readable_url = $string;

      // make readable Text
      $readable_url = str_replace('https://www.', '', $readable_url);
      $readable_url = str_replace('http://www.', '', $readable_url);
      $readable_url = str_replace('https://', '', $readable_url);
      $readable_url = str_replace('http://', '', $readable_url);


      // set options
      $option_target = 'target="_blank"';
      $option_no_follow = 'rel="nofollow"';


      // generate link
      $link = '<a href="' . $url . '" ' . $option_target . ' ' . $option_no_follow . '>' . $readable_url . '</a>';

      return $link;

    }
  }