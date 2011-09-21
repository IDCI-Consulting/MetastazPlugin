<?php

/**
* MetastazPool
* 
* @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
* @licence: LGPL
*/
class MetastazPool
{
  /**
   * Pool
   * Insert pool
   * Update pool
   * Delete pool
   */
  protected $pool, $insert_pool, $update_pool, $delete_pool = array();

  /**
   * Is loaded
   */
  protected $is_loaded = false;

  /**
   * Is updated
   */
  protected $is_updated = false;

  /**
   * Dimension
   */
  protected $dimension = null;

  /**
   * Constructor
   *
   * @param string $dimension
   * @param array $metastazs
   */
  public function __construct($dimension, $metastazs = array())
  {
      $this->setDimension($dimension);
      $this->initPools();

      if(!empty($metastazs))
          $this->load($metastazs);
  }

  /**
   * Init pools with empty array
   */
  public function initPools()
  {
    $this->pool = array();
    $this->insert_pool = array();
    $this->update_pool = array();
    $this->delete_pool = array();
    $this->is_updated = false;
    $this->is_loaded = false;
  }

  /**
   * Set Dimension
   *
   * @param string $dimension
   */
  public function setDimension($dimension)
  {
      $this->dimension = $dimension;
  }

  /**
   * Get Dimension
   *
   * @return string $dimension
   */
  public function getDimension()
  {
      return $this->dimension;
  }

  /**
   * Load metastazs data in pool
   *
   * @param array $metastazs
   */
  public function load($metastazs)
  {
      $this->initPools();
      $this->pool = $metastazs;
      $this->is_loaded = true;
  }

  /**
   * isLoaded
   *
   * @return boolean
   */
  public function isLoaded()
  {
      return $this->is_loaded;
  }

  /**
   * isUpdated
   *
   * @return boolean
   */
  public function isUpdated()
  {
      return $this->is_updated;
  }

  /**
   * Get
   *
   * @param string $namespace
   * @param string $key
   * @param string $culture
   */
  public function get($namespace, $key, $culture = null)
  {
      if(isset($this->update_pool[$namespace]) && isset($this->update_pool[$namespace][$key]))
        return $this->update_pool[$namespace][$key];

      if(isset($this->insert_pool[$namespace]) && isset($this->insert_pool[$namespace][$key]))
        return $this->insert_pool[$namespace][$key];

      if(isset($this->pool[$namespace]) && isset($this->pool[$namespace][$key]))
        return $this->pool[$namespace][$key];

      return null;
  }

  /**
   * Add
   *
   * @param string $namespace
   * @param string $key
   * @param mixed $value
   * @param string $culture
   */
  public function add($namespace, $key, $value, $culture = null)
  {
      $previousValue = $this->get($namespace, $key, $culture);
      if($value === $previousValue)
      {
          // Already exist and not changed: nothing to do
          return;
      }

      $this->is_updated = true;
      $metastaz = array($key => $value);

      if(null === $previousValue)
      {
          // To insert
          if(!isset($this->insert_pool[$namespace]))
              $this->insert_pool[$namespace] = array();

          $this->insert_pool[$namespace] = array_merge(
              $this->insert_pool[$namespace],
              $metastaz
          );
      }
      else
      {
          // To update
          if(!isset($this->update_pool[$namespace]))
              $this->update_pool[$namespace] = array();

          $this->update_pool[$namespace] = array_merge(
              $this->update_pool[$namespace],
              $metastaz
          );
      }
  }


  /**
   * Delete
   *
   * @param string $namespace
   * @param string $key
   */
  public function delete($namespace, $key)
  {
      $previousValue = $this->get($namespace, $key);
      if(null === $previousValue)
      {
          // Remove a non existing metastaz: nothing to do
          return;
      }

      if(!isset($this->delete_pool[$namespace]))
          $this->delete_pool[$namespace] = array();

      if(null !== $this->get($namespace, $key))
        $this->delete_pool[$namespace][$key] = $previousValue;

      unset(
          $this->pool[$namespace][$key],
          $this->insert_pool[$namespace][$key],
          $this->update_pool[$namespace][$key]
      );
  }

  /**
   * Delete all
   */
  public function deleteAll()
  {
      $this->delete_pool = array_merge(
          $this->delete_pool,
          $this->pool
      );

      $this->pool = $this->insert_pool = $this->update_pool = array();
  }

  /**
   * Get all
   *
   * @return array $pool
   */
  public function getAll()
  {
      return $this->pool;
  }

  /**
   * Get inserts
   *
   * @return array $insert_pool
   */
  public function getInserts()
  {
      return $this->insert_pool;
  }

  /**
   * Get updates
   *
   * @return array update_pool
   */
  public function getUpdates()
  {
      return $this->update_pool;
  }

  /**
   * Get deletes
   *
   * @return delete_pool
   */
  public function getDeletes()
  {
      return $this->delete_pool;
  }
}
