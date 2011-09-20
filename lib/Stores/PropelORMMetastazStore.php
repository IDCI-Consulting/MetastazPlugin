<?php

/**
 * PropelORMMetastazStore is a concrete provider to store Metastazs throw 
 * Propel ORM.
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: LGPL
 */
class PropelORMMetastazStore implements MetastazStoreInterface
{
  protected static $parameters = array();
  protected static $em         = null;

  public function __construct($parameters)
  {
    if(empty(self::$parameters))
    {
      self::$parameters = $parameters;
    }
  }

  /**
   * @see Metastaz\Interfaces\MetastazStoreInterface
   */
  public function get($dimension, $namespace, $key, $culture = null)
  {
    $entity = MetastazPeer::retrieveByPK($dimension, $namespace, $key);

    if (!$entity) {
        return null;
    }
    return self::_deserialize($entity->getMetaValue());
  }

  /**
   * @see Metastaz\Interfaces\MetastazStoreInterface
   */
  public function put($dimension, $namespace, $key, $value, $culture = null)
  {
    $entity = MetastazPeer::retrieveByPK($dimension, $namespace, $key);

    if (!$entity)
    {
      $entity = new Metastaz();
      $entity->setMetaDimension($dimension);
      $entity->setMetaNamespace($namespace);
      $entity->setMetaKey($key);
    }

    $entity->setMetaValue(self::_serialize($value));
    $entity->save();
  }

  /**
   * @see Metastaz\Interfaces\MetastazStoreInterface
   */
  public function getAll($dimension)
  {
    $criteria = new Criteria();
    $criteria->add(MetastazPeer::META_DIMENSION, $dimension);
    $entities = MetastazPeer::doSelect($criteria);

    $ret = array();
    foreach($entities as $entity) {
      $ret[$entity->getMetaNamespace()][$entity->getMetaKey()] = self::_deserialize($entity->getMetaValue());
    }

    return $ret;
  }

  /**
   * @see Metastaz\Interfaces\MetastazStoreInterface
   * @throw Exception
   */
  public function delete($dimension, $namespace, $key)
  {
    $entity = MetastazPeer::retrieveByPK($dimension, $namespace, $key);
    if (!$entity) {
      throw new Exception(
        sprintf('Unable to find Metastaz entity with the following parameter: %s %s %s.',
          $dimension,
          $namespace,
          $key
        )
      );
    }

    $entity->delete();
  }

  /**
   * @see Metastaz\Interfaces\MetastazStoreInterface
   * @throw Exception
   */
  public function deleteAll($dimension)
  {
    $criteria = new Criteria();
    $criteria->add(MetastazPeer::META_DIMENSION, $dimension);
    $entities = MetastazPeer::doSelect($criteria);

    foreach($entities as $entity)
    {
      $entity->delete();
    }
  }

  /**
   * @see Metastaz\Interfaces\MetastazStoreInterface
   */
  public function addMany($dimension, array $metastazs)
  {
    foreach($metastazs as $namespace => $keys)
    {
      foreach($keys as $key => $value)
      {
        $entity = new Metastaz();
        $entity->setMetaDimension($dimension);
        $entity->setMetaNamespace($namespace);
        $entity->setMetaKey($key);
        $entity->setMetaValue(self::_serialize($value));
        $entity->save();
      }
    }
  }

  /**
   * @see Metastaz\Interfaces\MetastazStoreInterface
   */
  public function updateMany($dimension, array $metastazs)
  {
    foreach($metastazs as $namespace => $keys)
    {
      foreach($keys as $key => $value)
      {
        $entity = MetastazPeer::retrieveByPK(array(
          'meta_dimension' => $dimension,
          'meta_namespace' => $namespace,
          'meta_key' => $key
        ));

        if (!$entity) {
          $entity = new Metastaz();
          $entity->setMetaDimension($dimension);
          $entity->setMetaNamespace($namespace);
          $entity->setMetaKey($key);
        }
        $entity->setMetaValue(self::_serialize($value));
        $entity->save();
      }
    }
  }

  /**
   * @see Metastaz\Interfaces\MetastazStoreInterface
   */
  public function deleteMany($dimension, array $metastazs)
  {
    foreach($metastazs as $namespace => $keys)
    {
      foreach($keys as $key => $value)
      {
        $entity = MetastazPeer::retrieveByPK(array(
          'meta_dimension' => $dimension,
          'meta_namespace' => $namespace,
          'meta_key' => $key
        ));
        if (!$entity) 
        {
          throw new Exception(
            sprintf('Unable to find Metastaz entity with the following parameter: %s %s %s.',
                $dimension,
                $namespace,
                $key
            )
          );
        }
        $entity->delete();
      }
    }
  }

  public static function flush() {}

  /**
   * Serialize
   */
  protected static function _serialize($data)
  {
    $confParams = sfConfig::get('app_metastaz_parameters');
    $callback = isset($confParams['method_encode']) ? $confParams['method_encode'] : 'serialize';
    return call_user_func($callback, $data);
  }

  /**
   * Deserialize
   */
  protected static function _deserialize($data)
  {
    $confParams = sfConfig::get('app_metastaz_parameters');
    $callback = isset($confParams['method_decode']) ?  $confParams['method_decode'] : 'unserialize';
    return call_user_func($callback, $data);
  }
}
