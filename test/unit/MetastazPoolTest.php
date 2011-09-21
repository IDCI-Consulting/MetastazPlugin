<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once dirname(__FILE__).'/lib/MyMetastaz.class.php';

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
new sfDatabaseManager($configuration);

$t = new lime_test();
$fixtures = array(
    'ns1' => array(
        'key1A' => 'valeur1A',
        'key1B' => 'valeur1B',
    ),
    'ns2' => array(
        'key2A' => 'valeur2A',
        'key2B' => 'valeur2B',
    )
);

// Test constructor without params
$t->diag('Constructor without parameters.');
$pool = new MetastazPool('dimension_1');
$t->is(array(), $pool->getAll());
$t->is(array(), $pool->getInserts());
$t->is(array(), $pool->getUpdates());
$t->is(array(), $pool->getDeletes());
$t->ok(!$pool->isLoaded());
$t->ok(!$pool->isUpdated());

// Test constructor with params
$t->diag('Constructor with parameters.');
$pool = new MetastazPool('dimension_1', $fixtures);
$t->is($fixtures, $pool->getAll());
$t->is(array(), $pool->getInserts());
$t->is(array(), $pool->getUpdates());
$t->is(array(), $pool->getDeletes());
$t->ok($pool->isLoaded());
$t->ok(!$pool->isUpdated());

// Test load
$t->diag('Loading pool.');
$pool = new MetastazPool('dimension_1');
$t->ok(!$pool->isLoaded());
$pool->load($fixtures);
$t->ok($pool->isLoaded());
$t->ok(!$pool->isUpdated());

// Test get
$pool = new MetastazPool('dimension_1', $fixtures);
$t->diag('Getting pool.');
$t->is('valeur1A', $pool->get('ns1', 'key1A'));
$t->is('valeur1B', $pool->get('ns1', 'key1B'));
$t->is('valeur2A', $pool->get('ns2', 'key2A'));
$t->is('valeur2B', $pool->get('ns2', 'key2B'));

// Test add if pool is empty
$t->diag('Adding value if pool is empty.');
$pool = new MetastazPool('dimension_1');
$value = 'new_value_with_empty_pool';
$pool->add('ns1', 'key1C', $value);
$t->is($value, $pool->get('ns1', 'key1C'));
$t->ok($pool->isUpdated());

// Test add if pool has data
$t->diag('Adding value if pool has data.');
$pool = new MetastazPool('dimension_2', $fixtures);
$value = 'new_value_with_pool_data';
$pool->add('ns1', 'key1C', $value);
$t->is($value, $pool->get('ns1', 'key1C'));
$t->ok($pool->isUpdated());

// Test add if pool has updated data
$t->diag('Adding value if pool has updated data.');
$value = 'update_value_with_pool_data';
$pool->add('ns1', 'key1C', $value);
$t->is($value, $pool->get('ns1', 'key1C'));
$t->is(array('ns1' => array('key1C' => $value)), $pool->getUpdates());
$t->ok($pool->isUpdated());

// Test delete an absent data
$t->diag('Deleting an absent data.');
$pool = new MetastazPool('dimension_1', $fixtures);
$pool->delete('ns', 'key');
$t->is($fixtures, $pool->getAll());
$t->is(array(), $pool->getDeletes());

// Test delete an existing data
$t->diag('Deleting an existing data.');
$pool = new MetastazPool('dimension_1', $fixtures);
$pool->delete('ns1', 'key1A');
$t->is($pool->get('ns1', 'key1A'), null);
$t->is(array('ns1' => array('key1A' => 'valeur1A')), $pool->getDeletes());

// Test getAll
$t->diag('Getting all metadata.');
$pool = new MetastazPool('dimension_1', $fixtures);
$t->is($fixtures, $pool->getAll());

// Test deleteAll
$t->diag('Deleting all metadata.');
$pool = new MetastazPool('dimension_1', $fixtures);
$pool->deleteAll();
$t->is(array(), $pool->getAll());
$t->is(array(), $pool->getInserts());
$t->is(array(), $pool->getUpdates());
$t->is($fixtures, $pool->getDeletes());

// Test reload data
$t->diag('Reload data in the pool.');
$pool->load($fixtures);
$t->ok($pool->isLoaded());
$t->ok(!$pool->isUpdated());

