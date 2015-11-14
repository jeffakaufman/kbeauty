<?php

/**
 * Product:       Xtento_TrackingImport (2.1.2)
 * ID:            9Hiec+sXo9Z8XvyLsrsgPILEHN0W5+Sn/0xZtemTYL0=
 * Packaged:      2015-11-13T21:34:24+00:00
 * Last Modified: 2013-11-03T16:32:56+01:00
 * File:          app/code/local/Xtento/TrackingImport/Model/Processor/Abstract.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

abstract class Xtento_TrackingImport_Model_Processor_Abstract extends Varien_Object
{
    protected $mappingModel;
    protected $mapping;

    protected function getConfiguration()
    {
        return $this->getProfile()->getConfiguration();
    }

    protected function getConfigValue($key)
    {
        $configuration = $this->getConfiguration();
        if (isset($configuration[$key])) {
            return $configuration[$key];
        } else {
            return false;
        }
    }
}