<?php

/**
 * MetastazObject is a concrete Object which implement a MetastazInterface.
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 */
 class MyMetastaz extends MetastazObject
{
  protected $dimension_id;
  protected $template_name;

  public function __construct($dimension_id = null, $template_name = null)
  {
    $this->dimension_id = $dimension_id;
    $this->template_name = $template_name;
  }

  public function getMetastazDimensionId() { return $this->dimension_id; }
  public function getMetastazTemplateName() { return $this->template_name; }
}
