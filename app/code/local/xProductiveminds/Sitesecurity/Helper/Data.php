<?php
/**
 *  A Magento module by ProductiveMinds
 *
 * NOTICE OF LICENSE
 *
 * This code is the work and copyright of Productive Minds Ltd, A UK registered company.
 * The copyright owner prohibit any fom of distribution of this code
 *
 * DISCLAIMER
 *
 * You are strongly advised to backup ALL your server files and database before installing and/or configuring
 * this Magento module. ProductiveMinds will not take any form of responsibility for any adverse effects that
 * may be cause directly or indirectly by using this software. As a usual practice with Software deployment,
 * the copyright owner recommended that you first install this software on a test server verify its appropriateness
 * before finally deploying it to a live server.
 *
 * @category   	Productiveminds
 * @package    	Productiveminds_Sitesecurity
 * @copyright   Copyright (c) 2010 - 2015 Productive Minds Ltd (http://www.productiveminds.com)
 * @license    	http://www.productiveminds.com/license/license.txt
 * @author     	ProductiveMinds <info@productiveminds.com>
 */

class Productiveminds_Sitesecurity_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	public function isModuleActive($moduleName = null)
    {
   	
        if ($moduleName === null) {
            $moduleName = $this->_getModuleName();
        }

        if (Mage::getConfig()->getNode('modules/'.$moduleName) &&
        	Mage::getStoreConfig('sitesecurity_sectns/sitesecurity_grps/enabled', Mage::app()->getStore())
		) {
        	return true;
        } else {
        	return false;
        }
        return false;
    }
}
?>