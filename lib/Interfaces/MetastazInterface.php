<?php

/**
 * Metastaz interface define operations which must be override 
 * by each Metastaz object.
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 */
interface MetastazInterface
{
  function getMetastazDimensionId();
  function getMetastazTemplateName();
  function getMetastazContainer();
  function getMetastazTemplateFields();
  function getMetastazIndexes();
  function getMetastaz($namespace, $key, $culture = null);
  function putMetastaz($namespace, $key, $value, $culture = null);
  function deleteMetastaz($namespace, $key);
  function getAllMetastaz();
  function deleteAllMetastaz();
  function loadMetastaz();
  function persistMetastaz();
  function flushMetastaz();
}
