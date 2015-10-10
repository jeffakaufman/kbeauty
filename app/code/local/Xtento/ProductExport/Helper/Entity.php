<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-07-29T15:37:16+02:00
 * File:          app/code/local/Xtento/ProductExport/Helper/Entity.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Helper_Entity extends Mage_Core_Helper_Abstract
{
    public function getPluralEntityName($entity) {
        if ($entity == Xtento_ProductExport_Model_Export::ENTITY_CATEGORY) {
            return "categories";
        }
        if ($entity == Xtento_ProductExport_Model_Export::ENTITY_PRODUCT) {
            return "products";
        }
        return $entity;
    }
}