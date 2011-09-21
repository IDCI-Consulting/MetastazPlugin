<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once dirname(__FILE__).'/lib/MyMetastazWithPool.class.php';

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
new sfDatabaseManager($configuration);

$t = new lime_test();

// constructor
$t->diag('Constructor without parameters.');
$metastaz = new MyMetastazWithPool();
$t->is(array(), $metastaz->getAllMetastaz());

// Define a dimension for the test
$metastaz->setMetastazDimensionId('Test');
$metastaz->loadMetastaz();

// storing metadata
$t->diag('Storing and retrieving metadata.');
$metastaz->putMetastaz('NS_1', 'K1', 'it works');
$metastaz->putMetastaz('NS_1', 'K2', 'it always works');
$metastaz->putMetastaz('NS_1', 'K3', 'to delete');

// retrieving metadata
$t->is($metastaz->getMetastaz('NS_1', 'K1'), 'it works');
$t->is($metastaz->getMetastaz('NS_1', 'K2'), 'it always works');
$t->is($metastaz->getMetastaz('NS_1', 'K3'), 'to delete');

$t->diag('Replacing an existing metadata by a new.');
$metastaz->putMetastaz('NS_1', 'K1', 'test');
$t->is($metastaz->getMetastaz('NS_1', 'K1'), 'test');
$metastaz->putMetastaz('NS_1', 'K1', 'it works');

// retrieving all metadata
$t->diag('Retrieving all metadata.');
$t->is($metastaz->getAllMetastaz(), array(
  "NS_1" => array(
    "K1"  => "it works",
    "K2"  => "it always works",
    "K3"  => "to delete"
  )
));


// deleting a metadata
$t->diag('Deleting a metadata.');
$metastaz->deleteMetastaz('NS_1', 'K3');
$t->is($metastaz->getAllMetastaz(), array(
  "NS_1" => array(
    "K1"  => "it works",
    "K2"  => "it always works"
  )
));

$metastaz->persistMetastaz();

// deleting all metadata
$t->diag('Deleting all metadata.');
$metastaz->deleteAllMetastaz();
$t->is($metastaz->getAllMetastaz(), array());
?>
