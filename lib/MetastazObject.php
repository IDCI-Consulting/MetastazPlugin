<?php

/**
 * MetastazObject is a concrete Object which implement a MetastazInterface.
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 */
abstract class MetastazObject implements MetastazInterface
{
    /**
     * Holds MetastazContainer Objects
     */
    protected $metastaz_container = null;

    /**
     * Get the metastaz id which is a unique identifier in the metastaz dimension
     *
     * @see Metastaz\Interfaces\MetastazInterface
     *
     * @return string
     */
    public function getMetastazDimensionId() {}

    /**
     * Get the object metastaz template name
     *
     * @see Metastaz\Interfaces\MetastazInterface
     *
     * @return string
     */
    public function getMetastazTemplateName() {}

    /**
     * Retrieve the metastaz object container
     *
     * @see Metastaz\Interfaces\MetastazInterface
     *
     * @return MetastazContainer $metastaz_container
     */
    public function getMetastazContainer()
    {
        if(null === $this->metastaz_container)
        {
            $this->metastaz_container = new MetastazContainer(
                array(
                    'object' => $this,
//                    'container' => array(
//                        'use_template' => true,
//                        'instance_pooling' => true
//                    ),
//                    'store' => array(
//                        'class' => 'DoctrineODMMetastazStore',
//                        'parameters' => array('connection' => 'metastaz')
//                    )
                )
            );
        }
        return $this->metastaz_container;
    }

    /**************************************************************************/
    /*                          Magic functions                               */
    /**************************************************************************/

    /**
     * Try to get metastaz value
     *
     * @see Metastaz\Interfaces\MetastazInterface
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (false === strpos($name, '_')) {
            return null;
        }

        list($namespace, $key) = explode('_', $name);
        return $this->getMetastaz($namespace, $key);
    }

    /**
     * Try to set metastaz value
     *
     * @see Metastaz\Interfaces\MetastazInterface
     */
    public function __set($name, $value)
    {
        if (false === strpos($name, '_')) {
            return;
        }

        list($namespace, $key) = explode('_', $name);

        if (null == $value or $value === '' ) {
            // TODO: Have to remove the metadata ?
            return;
        }
        $this->putMetastaz($namespace, $key, $value);
    }

    /**************************************************************************/
    /*                          Proxy functions                               */
    /**************************************************************************/

    /**
     * Get metastaz template fields
     *
     * @see Metastaz\Interfaces\MetastazInterface
     *
     * @return array
     */
    public function getMetastazTemplateFields()
    {
        return $this->getMetastazContainer()->getMetastazTemplateFields();
    }

    /**
     * Get metastaz indexed fields
     *
     * @see Metastaz\Interfaces\MetastazInterface
     *
     * @return array
     */
    public function getMetastazIndexes()
    {
        return $this->getMetastazContainer()->getIndexedFields();
    }

    /**
     * Get the metastaz value identify by its namespace and key (optionaly culture)
     *
     * @see Metastaz\Interfaces\MetastazInterface
     *
     * @return mixed
     */
    public function getMetastaz($namespace, $key, $culture = null)
    {
        return $this->getMetastazContainer()->get($namespace, $key, $culture);
    }

    /**
     * Put the metastaz value identify by its namespace and key (optionaly culture)
     *
     * @see Metastaz\Interfaces\MetastazInterface
     */
    public function putMetastaz($namespace, $key, $value, $culture = null)
    {
        return $this->getMetastazContainer()->put($namespace, $key, $value, $culture);
    }

    /**
     * Delete the metastaz identify by its namespace and key
     *
     * @see Metastaz\Interfaces\MetastazInterface
     */
    public function deleteMetastaz($namespace, $key)
    {
        return $this->getMetastazContainer()->delete($namespace, $key);
    }

    /**
     * Get all the metastaz for a given namespace (optionaly culture)
     *
     * @see Metastaz\Interfaces\MetastazInterface
     *
     * @return array
     */
    public function getAllMetastaz($culture = null)
    {
        return $this->getMetastazContainer()->getAll($culture = null);
    }

    /**
     * Delete all the metastaz for a given namespace
     *
     * @see Metastaz\Interfaces\MetastazInterface
     */
    public function deleteAllMetastaz()
    {
        return $this->getMetastazContainer()->deleteAll();
    }

    /**
     * Load all the metastaz related to this object
     *
     * @see Metastaz\Interfaces\MetastazInterface
     */
    public function loadMetastaz()
    {
        return $this->getMetastazContainer()->load();
    }

    /**
     * Persist all the metastaz related to this object
     *
     * @see Metastaz\Interfaces\MetastazInterface
     */
    public function persistMetastaz()
    {
        return $this->getMetastazContainer()->persist();
    }

    /**
     * Flush setted or persisted metastaz
     *
     * @see Metastaz\Interfaces\MetastazInterface
     */
    public function flushMetastaz()
    {
        return $this->getMetastazContainer()->flush();
    }
}
