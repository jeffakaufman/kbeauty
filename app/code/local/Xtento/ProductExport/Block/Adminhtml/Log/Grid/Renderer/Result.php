<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-02-10T18:06:03+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Log/Grid/Renderer/Result.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Log_Grid_Renderer_Result extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if ($row->getResult() === NULL || $row->getResult() == 0) {
            return '<span class="grid-severity-major"><span>' . Mage::helper('xtento_productexport')->__('No Result') . '</span></span>';
        } else if ($row->getResult() == 1) {
            return '<span class="grid-severity-notice"><span>' . Mage::helper('xtento_productexport')->__('Success') . '</span></span>';
        } else if ($row->getResult() == 2) {
            return '<span class="grid-severity-minor"><span>' . Mage::helper('xtento_productexport')->__('Warning') . '</span></span>';
        } else if ($row->getResult() == 3) {
            return '<span class="grid-severity-critical"><span>' . Mage::helper('xtento_productexport')->__('Failed') . '</span></span>';
        }
        return '';
    }
}