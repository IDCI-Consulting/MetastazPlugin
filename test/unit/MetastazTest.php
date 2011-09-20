<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once dirname(__FILE__).'/lib/MyMetastaz.class.php';

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
new sfDatabaseManager($configuration);

$t = new lime_test(6);
$metastaz = new MyMetastaz();

// First test about storing and retrieving metadata
$metastaz->__set('1_1', 'it works');
$t->is($metastaz->__get('1_1'), 'it works');

// Second test about storing and retrieving metadata
$metastaz->__set('1_2', 'it always works');
$t->is($metastaz->__get('1_2'), 'it always works');

// Third test about storing and retrieving metadata
$metastaz->__set('1_3', 'to delete');
$t->is($metastaz->__get('1_3'), 'to delete');

// Fourth test retrieving all metadata
$t->is($metastaz->getMetastazContainer()->getAll(), array(
  "1" => array(
    "1"  => "it works",
    "2"  => "it always works",
    "3"  => "to delete"
  )
));

// Fifth test deleting a metadata
$metastaz->getMetastazContainer()->delete('1', '3', 'to delete');
$t->is($metastaz->getMetastazContainer()->getAll(), array(
  "1" => array(
    "1"  => "it works",
    "2"  => "it always works"
  )
));

// Sixth test deleting all metadata
/*$metastaz->getMetastazContainer()->deleteAll();
$t->is($metastaz->getMetastazContainer()->getAll(), array());*/
?>
