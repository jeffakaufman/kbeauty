<?php
/**
 * Remove or Change Displayed States and Regions
 *
 * LICENSE
 *
 * This source file is subject to the Eltrino LLC EULA
 * that is bundled with this package in the file LICENSE_EULA.txt.
 * It is also available through the world-wide-web at this URL:
 * http://eltrino.com/license-eula.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@eltrino.com so we can send you a copy immediately.
 *
 * @category    Eltrino
 * @package     Eltrino_Region
 * @copyright   Copyright (c) 2014 Eltrino LLC. (http://eltrino.com)
 * @license     http://eltrino.com/license-eula.txt  Eltrino LLC EULA
 */

/**
 * Controller with AJAX actions
 *
 * @category   Eltrino
 * @package    Eltrino_Region
 */
class Eltrino_Region_Adminhtml_JsonController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Return JSON-encoded array of country regions
     *
     * @return string
     */
    public function countryRegionAction()
    {
        $arrRes = array();
        $countryId = $this->getRequest()->getParam('parent');
        $collection = Mage::getResourceModel('directory/region_collection')
            ->addCountryFilter($countryId);

        $this->_addRegionFilter($collection);

        $arrRegions = $collection->load()
            ->toOptionArray();

        if (!empty($arrRegions)) {
            foreach ($arrRegions as $region) {
                $arrRes[] = $region;
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($arrRes));
    }

    /**
     * Checks if it's called from section Origin Shipping Settings
     *
     * @return bool
     */
    protected function _isRewriteNeeded()
    {
        $url = Mage::helper('core/http')->getHttpReferer();
        return strpos($url, '/section/shipping') === false;
    }

    /**
     * Adds filter to collection for not loading disabled regions
     *
     * @param $collection
     */
    protected function _addRegionFilter($collection)
    {
        if (!$this->_isRewriteNeeded()) {
            return;
        }

        /* @var $helper Eltrino_Region_Helper_Data */
        $helper = Mage::helper('eltrino_region');
        $scopeData = $helper->getActualScopeFromRequest();
        /* @var $availableRegionsCollection Eltrino_Region_Model_Resource_Entity_Collection */
        $availableRegionsCollection = Mage::getResourceModel('eltrino_region/entity_collection');
        $availableRegionsCollection->addFieldToFilter('scope_id', $scopeData['scope_id']);
        $availableRegionsCollection->addFieldToFilter('scope', $scopeData['scope']);

        $disabledRegions = array();
        foreach ($availableRegionsCollection as $item) {
            $disabledRegions[] = $item->getRegionId();
        }

        /* @var $directoryHelper Eltrino_Region_Helper_Directory */
        $directoryHelper = Mage::helper('eltrino_region/directory');
        if (!empty($disabledRegions)) {
            $collection->addFieldToFilter(
                $directoryHelper->getRegionTableAlias() . ".region_id", array('nin' => $disabledRegions)
            );
        }
    }
}
