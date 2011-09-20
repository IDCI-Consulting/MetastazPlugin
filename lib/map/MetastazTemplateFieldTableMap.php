<?php


/**
 * This class defines the structure of the 'metastaz_template_field' table.
 *
 *
 * This class was autogenerated by Propel 1.4.2 on:
 *
 * lun. 19 sept. 2011 12:03:38 CEST
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    plugins.MetastazPlugin.lib.map
 */
class MetastazTemplateFieldTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.MetastazPlugin.lib.map.MetastazTemplateFieldTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
	  // attributes
		$this->setName('metastaz_template_field');
		$this->setPhpName('MetastazTemplateField');
		$this->setClassname('MetastazTemplateField');
		$this->setPackage('plugins.MetastazPlugin.lib');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('META_NAMESPACE', 'MetaNamespace', 'VARCHAR', false, 128, null);
		$this->addColumn('META_KEY', 'MetaKey', 'VARCHAR', false, 128, null);
		$this->addColumn('IS_INDEXED', 'IsIndexed', 'BOOLEAN', false, null, null);
		$this->addColumn('OPTIONS', 'Options', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

	/**
	 * 
	 * Gets the list of behaviors registered for this table
	 * 
	 * @return array Associative array (name => parameters) of behaviors
	 */
	public function getBehaviors()
	{
		return array(
			'symfony' => array('form' => 'true', 'filter' => 'true', ),
			'symfony_behaviors' => array(),
		);
	} // getBehaviors()

} // MetastazTemplateFieldTableMap
