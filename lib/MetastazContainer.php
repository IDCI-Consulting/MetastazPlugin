<?php

/**
 * MetastazContainer manage Metastaz (MetastazStore, MetastazTemplate)
 *
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @author:  Mirsal ENNAIME <mirsal@mirsal.fr>
 * @licence: GPL
 */
class MetastazContainer
{
  /**
   * Parameters
   */
  protected $parameters = array();

  /**
   * Template
   */
  static protected $templates = array();

  /**
   * Store
   */
  static protected $stores = array();

  /**
   * Metastaz Pool
   */
  protected $metastaz_pool = null;

  /**
   * Is persisted
   */
  protected $is_persisted = false;

  /**
   * Constructor
   *
   * @param array $parameters
   */
  public function __construct(array $parameters = array())
  {
    $configParams = sfConfig::get('app_metastaz_parameters');
    $this->setParameters(array_merge($configParams, $parameters));
    if(! $this->getMetastazObject() instanceof MetastazInterface) {
      throw new Exception(sprintf(
          'The given object %s doesn\'t implements MetastazInterface',
          get_class($this->getMetastazObject())
      ));
    }
    $this->metastaz_pool = new MetastazPool($this->getMetastazDimension());
    if ($this->isInstancePoolingEnabled()) {
      $this->load();
    }
  }

  /**
   * Set parameters
   *
   * @param array $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }

  /**
   * Get parameters
   *
   * @return parameters
   */
  public function getParameters()
  {
    return $this->parameters;
  }

  /**
   * Get parameter
   *
   * @param string $name
   * @throw Exception
   * @return mixed
   */
  public function getParameter($name)
  {
    if (!$this->hasParameter($name)) {
      throw new Exception(sprintf('Missing %s parameter', $name));
    }

    return $this->parameters[$name];
  }

  /**
   * Has parameter
   *
   * @param string $name
   * @return boolean
   */
  public function hasParameter($name)
  {
    return isset($this->parameters[$name]);
  }

  /**
   * Is templating enable
   *
   * @return boolean
   */
  public function isTemplatingEnabled()
  {
    if ($this->hasParameter('container'))
    {
      $container = $this->getParameter('container');
      return isset($container['use_template']) && $container['use_template'];
    }
    return true;
  }

  /**
   * Is instance pooling enable
   *
   * @return boolean
   */
  public function isInstancePoolingEnabled()
  {
    if ($this->hasParameter('container'))
    {
      $container = $this->getParameter('container');
      return isset($container['instance_pooling']) && $container['instance_pooling'];
    }
    return false;
  }

  /**
   * Is persisted
   *
   * @return boolean
   */
  public function isPersisted()
  {
    return $this->is_persisted;
  }

  /**
   * Get the associated Metastaz Object
   *
   * @return MetastazInterface
   */
  public function getMetastazObject()
  {
    return $this->getParameter('object');
  }

  /**
   * Get Metastaz Object Dimension
   *
   * @return string
   */
  public function getMetastazDimension()
  {
    $obj = $this->getMetastazObject();
    return get_class($obj).$obj->getMetastazDimensionId();
  }

  /**
   * Get Metastaz Template name
   *
   * @return string
   */
  public function getMetastazTemplateName()
  {
    $obj = $this->getParameter('object');
    return $obj->getMetastazTemplateName();
  }

  /**
   * Get MetastazTemplate
   * If the templating is not enable, this function return a null object
   *
   * @throw NotFoundHttpException
   * @return MetastazTemplate
   */
  public function getMetastazTemplate()
  {
    if (!$this->isTemplatingEnabled())
    {
      return null;
    }

    if (isset(self::$templates[$this->getMetastazTemplateName()]))
    {
      return self::$templates[$this->getMetastazTemplateName()];
    }

    // Retrieve MetastazTemplate by its name
    $em = MetastazTemplateBundle::getContainer()->get('doctrine')->getEntityManager('metastaz_template');
    $re = $em->getRepository('MetastazTemplateBundle:MetastazTemplate');
    $template = $re->findOneByName($this->getMetastazTemplateName());

    if (!$template) {
      throw new Exception(
          sprintf('Unable to find the following MetastazTemplate: %s.', $this->getMetastazTemplateName())
      );
    }

    return self::$templates[$this->getMetastazTemplateName()] = $template;
  }

  /**
   * Get MetastazTemplateFields
   * If the templating is not enable, this function return a empty array
   *
   * @throw NotFoundHttpException
   * @return array
   */
  public function getMetastazTemplateFields()
  {
    if(!$template = $this->getMetastazTemplate())
    {
      return array();
    }

    return $template->getMetastazTemplateFields();
  }

  /**
   * Get stores
   *
   * @return array
   */
  public static function getStores()
  {
    return self::$stores;
  }

  /**
   * Get Store
   *
   * @throw NotFoundHttpException
   * @return MetastazStore
   */
  public function getMetastazStoreService()
  {
    $store = $this->getParameter('store');
    $class = $store['class'];

    if(!class_exists($class)) {
      throw new Exception(
          sprintf('Unable to find the following MetastazStore: %s.', $class)
      );
    }

    if (!isset(self::$stores[$class]))
    {
      $store = new $class($store['parameters']);
      self::$stores[$class] = $store;
    }

    return self::$stores[$class];
  }

  /**
   * Get Indexed Fields related to the object template
   *
   * @return array
   */
  public function getIndexedFields()
  {
    return MetastazTemplate::getIndexedFields($this->getMetastazTemplateName());
  }

  /**
   * To get a Metastaz value for a specified Metastaz namespace and key
   *
   * @param string $namespace
   * @param string $key
   * @param string $culture
   * @return mixed
   */
  public function get($namespace, $key, $culture = null)
  {
    if ($this->isInstancePoolingEnabled()) {
      return $this->metastaz_pool->get($namespace, $key, $culture);
    }
    return $this->getMetastazStoreService()->get(
      $this->getMetastazDimension(),
      $namespace,
      $key,
      $culture
    );
  }

  /**
   * To put a Metastaz value for a specified Metastaz namespace and key
   *
   * @throw NotFoundHttpException
   * @param string $namespace
   * @param string $key
   * @param string $value
   * @param string $culture
   */
  public function put($namespace, $key, $value, $culture = null)
  {
    $template = $this->getMetastazTemplate();

    // TODO: Use Custom Repository to optimize the request to fields and prevent the lazy loading on field type
    if($template && !$template->hasField($namespace, $key))
    {
      throw new Exception(
        sprintf('The MetastazTemplate "%s" doesn\'t contain the following field {namespace: "%s", key: "%s"}.',
          $template->getName(),
          $namespace,
          $key
        )
      );
    }
    if ($this->isInstancePoolingEnabled()) {
      $this->metastaz_pool->add($namespace, $key, $value, $culture);
    } else {
      $this->getMetastazStoreService()->put(
        $this->getMetastazDimension(),
        $namespace,
        $key,
        $value,
        $culture
      );
    }
  }

  /**
   * Delete a Metastaz for a specified metastaz namespace and key
   *
   * @param string $namespace
   * @param string $key
   */
  public function delete($namespace, $key)
  {
    if ($this->isInstancePoolingEnabled()) {
        $this->metastaz_pool->delete($namespace, $key);
    } else {
        $this->getMetastazStoreService()->delete(
            $this->getMetastazDimension(),
            $namespace,
            $key
        );
    }
  }

  /**
   * To get all Metastaz value group by namespaces for a metastazed object
   *
   * @return array
   */
  public function getAll()
  {
    if ($this->isInstancePoolingEnabled()) {
      return $this->metastaz_pool->getAll();
    }

    return $this->getMetastazStoreService()->getAll(
      $this->getMetastazDimension()
    );
  }

  /**
   * Delete all Metastaz for a specified Metastaz dimension
   */
  public function deleteAll()
  {
    if ($this->isInstancePoolingEnabled()) {
       $this->metastaz_pool->deleteAll();
    } else {
      $this->getMetastazStoreService()->deleteAll(
          $this->getMetastazDimension()
      );
    }
  }

  /**
   * Load Metastaz in the pool
   */
  public function load()
  {
    $this->metastaz_pool->load(
      $this->getMetastazStoreService()->getAll($this->getMetastazDimension())
    );
  }

  /**
   * Persist Metastaz in the pool
   */
  public function persist()
  {
    $this->getMetastazStoreService()->addMany(
      $this->getMetastazDimension(),
      $this->metastaz_pool->getInserts()
    );

    $this->getMetastazStoreService()->updateMany(
      $this->getMetastazDimension(),
      $this->metastaz_pool->getUpdates()
    );

    $this->getMetastazStoreService()->deleteMany(
      $this->getMetastazDimension(),
      $this->metastaz_pool->getDeletes()
    );

    $this->is_persisted = true;
  }

  /**
   * Flush Metastaz from the pool
   */
  public function flush()
  {
    if (!$this->isPersisted()) {
      $this->persist();
    }

    $store = $this->getMetastazStoreService();
    $store::flush();

    $em  = MetastazTemplateBundle::getContainer()->get('doctrine')->getEntityManager();
    $evm = $em->getEventManager();
    $evm->dispatchEvent('metastazFlush', new LifecycleEventArgs($this->getParameter('object'), $em));
  }
}
