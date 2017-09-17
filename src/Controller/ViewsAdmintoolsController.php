<?php

/**
* @file
* Contains \Drupal\views_admintools\Controller\ViewsAdmintoolsController.
*/
namespace Drupal\views_admintools\Controller;
class ViewsAdmintoolsController{
public function content() {
return array(
'#type' => 'markup',
'#markup' => t('Hello, World!'),
);
}
}


// TODO
/*
* hier die gleichen einstellungen wie in der View. Diese einstellungen gelten dann als default wert
*
* hier die Möglichkeit zusätzliche Buttons zu definieren einfügen.
*
* Dazu braucht es folgende Felder:
*
* "Name"
* "Icon"
* "Link"
* "Beschreibung"
*
*/