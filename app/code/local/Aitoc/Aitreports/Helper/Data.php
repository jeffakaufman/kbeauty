<?php

class Aitoc_Aitreports_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_isPreorderEnabled = null;
    
    protected $_isEmptyValuesAllowed = false;

    /**
     * @param string $entityType
     * @return Aitoc_Aitreports_Model_Export_Type_Iterface
     */
    public function getExportModelByEntityType($entityType)
    {
        return Mage::getSingleton('aitreports/export_type_'.$entityType);
    }

    /**
     * @param string $entityType
     * @return Aitoc_Aitreports_Model_Export_Type_Iterface
     */
    public function getImportModelByEntityType($entityType)
    {
        return Mage::getSingleton('aitreports/import_type_'.$entityType);
    }

    public function getTmpPath()
    {
        $path = Mage::getBaseDir('var').DS.'aitreports'.DS;

        if (!is_dir($path))
        {
            $file = new Varien_Io_File();
            $file->mkdir($path);
        }

        return $path;
    }
    
    public function isEmptyValuesAllowed() 
    {
        return $this->_isEmptyValuesAllowed;        
    }

    public function isPreorderEnabled()
    {
        if($this->_isPreorderEnabled === null)
        {
            $this->_isPreorderEnabled = false;

            if (Mage::getConfig()->getNode('modules/Aitoc_Aitpreorder'))
            {
                $isActive = Mage::getConfig()->getNode('modules/Aitoc_Aitpreorder/active');
                if ($isActive && in_array((string)$isActive, array('true', '1')))
                {
                    $this->_isPreorderEnabled = true;
                }
            }
        }

        return $this->_isPreorderEnabled;
    }


    public function getEntityId($entityType)
    {
        $entityType = Mage::getModel('eav/config')->getEntityType($entityType);
        return $entityType->getEntityTypeId();
    }

    public function getEntityFields($entityType)
    {
        $attributeCodes = Mage::getSingleton('eav/config')
            ->getEntityAttributeCodes($entityType, null);
        return $attributeCodes;
    }
}
