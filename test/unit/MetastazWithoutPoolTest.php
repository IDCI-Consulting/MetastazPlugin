<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once dirname(__FILE__).'/lib/MyMetastazWithoutPool.class.php';

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
new sfDatabaseManager($configuration);

$t = new lime_test();

$t->diag('Constructor without parameters.');
$metastaz = new MyMetastazWithoutPool();
$t->is(array(), $metastaz->getAllMetastaz());

// storing metadata 
$t->diag('Storing and retrieving metadata.');
$metastaz->putMetastaz(1, 1, 'it works');
$metastaz->putMetastaz(1, 2, 'it always works');
$metastaz->putMetastaz(1, 3, 'to delete');

// retrieving metadata
$t->is($metastaz->getMetastaz(1, 1), 'it works');
$t->is($metastaz->getMetastaz(1, 2), 'it always works');
$t->is($metastaz->getMetastaz(1, 3), 'to delete');

$t->diag('Replacing an existing metadata by a new.');
$metastaz->putMetastaz(1, 1, 'test');
$t->is($metastaz->getMetastaz(1, 1), 'test');
$metastaz->putMetastaz(1, 1, 'it works');

// retrieving all metadata
$t->diag('Retrieving all metadata.');
$t->is($metastaz->getAllMetastaz(), array(
  "1" => array(
    "1"  => "it works",
    "2"  => "it always works",
    "3"  => "to delete"
  )
));

// deleting a metadata
$t->diag('Deleting a metadata.');
$metastaz->deleteMetastaz('1', '3', 'to delete');
$t->is($metastaz->getAllMetastaz(), array(
  "1" => array(
    "1"  => "it works",
    "2"  => "it always works"
  )
));

// deleting all metadata
$t->diag('Deleting all metadata.');
$metastaz->deleteAllMetastaz();
$t->is($metastaz->getAllMetastaz(), array());
?>
