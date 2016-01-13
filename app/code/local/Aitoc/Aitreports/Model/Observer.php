<?php
class Aitoc_Aitreports_Model_Observer
{
    protected $_order = null;


    protected function _getFreqStr($configParam)
    {
        $strToTimeArray = Mage::getModel('aitreports/system_config_source_cron')->toStrToTimeArray();
        return isset($strToTimeArray[$configParam])?$strToTimeArray[$configParam]:false;
    }

    public function cronExport()
    {
        $processor = Mage::getSingleton('aitreports/processor');
        if($processor->getProcess())
        {   //another process is running
            return false;
        }
        $collection = Mage::getModel('aitreports/profile')
            ->loadCronCollection();
        $currentDate = Mage::getModel('core/date')->timestamp();
        $resultProfile = false;
            
        foreach ($collection as $profile)
        {
            $config = $profile->getConfig();
            $strTime = $this->_getFreqStr($config['auto']['cron_frequency']);
            if(!$strTime) {
                // Cron timeout is not set
                continue;
            }

            if ($profile->getCrondate() && (strtotime('-'.$strTime, $currentDate) < strtotime($profile->getCrondate()))) {
                // Too early to export
                continue;
            }
            if($resultProfile == false || $profile->getCrondate() < $resultProfile->getCrondate() ) {
                // Running profile that were not executed longer
                $resultProfile = $profile;
            }
        }
        if(!is_object($resultProfile)) {
            return false;
        }
        $expModel = Mage::getModel('aitreports/export');
        $expModel
            ->setConfig($resultProfile->getConfig())
            ->setStoreId($resultProfile->getStoreId())
            ->setProfileId($resultProfile->getId())
            ->setIsCron(1)
            ->save();
        $options = array(
            'id' => $expModel->getId()
        );
        $resultProfile->updateDate( 'crondate' )->save();
        $processor->setProcess('export::initExport', $options)->save();
        
    }
    
}
