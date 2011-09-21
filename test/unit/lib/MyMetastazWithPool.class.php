<?php
require_once dirname(__FILE__).'/MyMetastaz.class.php';
/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 */
 class MyMetastazWithPool extends MyMetastaz
{
    public function getMetastazContainer()
    {
        if(null === $this->metastaz_container)
        {
            $this->metastaz_container = new MetastazContainer(
                array(
                    'object' => $this,
                    'container' => array(
//                        'use_template' => true,
                        'instance_pooling' => true
                    ),
//                    'store' => array(
//                        'class' => 'DoctrineODMMetastazStore',
//                        'parameters' => array('connection' => 'metastaz')
//                    )
                )
            );
        }
        return $this->metastaz_container;
    }
}
