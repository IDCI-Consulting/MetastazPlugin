<?php

/**
 * MetastazStore interface define operations which must be overwrite
 * by each concrete Metastaz Store.
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @author:  Mirsal ENNAIME <mirsal@mirsal.fr>
 * @licence: LGPL
 */
interface MetastazStoreInterface
{
    /**
     * Retrieve a Metastaz
     *
     * @param string $dimension
     * @param string $namespace
     * @param string $key
     * @param string $culture
     * @return mixed $value
     */
    public function get($dimension, $namespace, $key, $culture = null);

    /**
     * Add or update a Metastaz
     *
     * @param string $dimension
     * @param string $namespace
     * @param string $key
     * @param string $value
     * @param string $culture
     */
    public function put($dimension, $namespace, $key, $value, $culture = null);

    /**
     * Remove a Metastaz
     *
     * @param string $dimension
     * @param string $namespace
     * @param string $key
     */
    public function delete($dimension, $namespace, $key);

    /**
     * Retrieve all Metastaz for a given object dimension
     *
     * $metastazs must respect this structure:
     *
     * array(
     *   'namespace0' => array(
     *      'key0A'       => 'value0A'
     *      'key0B'       => 'value0B'
     *   ),
     *   'namespace1' => array(
     *      'key1A'       => 'value1A'
     *      'key2A'       => 'value1B'
     *   ),
     * )
     *
     * @param string $dimension
     * @return array $metastazs
     */
    public function getAll($dimension);

    /**
     * Remove all Metastaz related to an object (match with the object dimension)
     *
     * @param string $dimension
     */
    public function deleteAll($dimension);

    /**
     * Add metastazs
     *
     * $metastazs must respect this structure:
     *
     * array(
     *   'namespace0' => array(
     *      'key0A'       => 'value0A'
     *      'key0B'       => 'value0B'
     *   ),
     *   'namespace1' => array(
     *      'key1A'       => 'value1A'
     *      'key2A'       => 'value1B'
     *   ),
     * )
     *
     * @param string $dimension
     * @param array $metastazs
     */
    public function addMany($dimension, array $metastazs);

    /**
     * Update metastazs
     *
     * $metastazs must respect this structure:
     *
     * array(
     *   'namespace0' => array(
     *      'key0A'       => 'value0A'
     *      'key0B'       => 'value0B'
     *   ),
     *   'namespace1' => array(
     *      'key1A'       => 'value1A'
     *      'key2A'       => 'value1B'
     *   ),
     * )
     *
     * @param string $dimension
     * @param array $metastazs
     */
    public function updateMany($dimension, array $metastazs);

    /**
     * Delete metastazs
     *
     * $metastazs must respect this structure:
     *
     * array(
     *   'namespace0' => array(
     *      'key0A'       => 'value0A'
     *      'key0B'       => 'value0B'
     *   ),
     *   'namespace1' => array(
     *      'key1A'       => 'value1A'
     *      'key2A'       => 'value1B'
     *   ),
     * )
     *
     * @param string $dimension
     * @param array $metastazs
     */
    public function deleteMany($dimension, array $metastazs);

    /**
     * Flush persisted metastaz
     */
    public static function flush();
}
