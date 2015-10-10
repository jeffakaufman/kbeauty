<?php
/**
 * Eltrino News Notification
 *
 * LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE_OSL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@eltrino.com so we can send you a copy immediately.
 *
 * @category    Eltrino
 * @package     Eltrino_Pawl
 * @copyright   Copyright (c) 2013 Eltrino LLC. (http://eltrino.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Eltrino_Pawl_Model_Feed extends Mage_AdminNotification_Model_Feed
{
    const XML_USE_HTTPS_PATH = 'eltrino_pawl/news/use_https';
    const XML_FEED_URL_PATH = 'eltrino_pawl/news/url';
    const XML_FREQUENCY_PATH = 'eltrino_pawl/news/frequency';

    const XML_CHANNELS = 'eltrino_pawl/news/channels';

    const CACHE_ID = 'eltrino_pawl_notifications_lastcheck';

    protected $_selectedChannels = array();
    protected $_showNewReleases = false;

    /**
     * Initialization
     *
     * @return Eltrino_Pawl_Model_Feed
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_feedUrl = Mage::getStoreConfig(self::XML_FEED_URL_PATH);
        $this->_selectedChannels = explode(',', (string)Mage::getStoreConfig(self::XML_CHANNELS));
        $this->_showNewReleases = array_search('NEW_RELEASE', $this->_selectedChannels) !== FALSE;

        return $this;
    }

    /**
     * Check feed for modification
     *
     * @return Eltrino_Pawl_Model_Feed
     */
    public function checkUpdate()
    {
        if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
            return $this;
        }

        $this->_receiveUpdate();

        $this->setLastUpdate();

        return $this;
    }

    /**
     * Receive update and process data via notification system.
     *
     * @return null
     */
    protected function _receiveUpdate()
    {
        $feedData = array();
        $feedXml = $this->getFeedData();

        if (!$feedXml || !$feedXml->channel || !$feedXml->channel->item) {
            return;
        }

        foreach ($feedXml->channel->item as $item) {

            if (!$this->isNotificationInteresting($item)) {
                continue;
            }

            $feedData[] = array(
                'severity'      => (int)$item->severity,
                'date_added'    => $this->getDate((string)$item->pubDate),
                'title'         => (string)$item->title,
                'description'   => (string)$item->description,
                'url'           => (string)$item->link
            );
        }

        if ($feedData) {
            Mage::getModel('adminnotification/inbox')->parse(array_reverse($feedData));
        }
    }

    /**
     * Retrieve Last update time
     *
     * @return int
     */
    public function getLastUpdate()
    {
        return Mage::app()->loadCache(self::CACHE_ID);
    }

    /**
     * Set last update time (now)
     *
     * @return Mage_AdminNotification_Model_Feed
     */
    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), self::CACHE_ID);
        return $this;
    }

    /**
     * Determine that notification item is interesting to customer
     *  - related extension installed
     *  - appropriate channels have been selected
     *
     * @param $item
     * @return bool
     */
    protected function isNotificationInteresting($item)
	{
		$channels = explode(',', (string)$item->channel);
        $channels = array_intersect($channels, $this->_selectedChannels);

        if (empty($channels)) {
            return false;
        }

        $extensions  = explode(',', (string)$item->extension);
        $extensions = array_filter($extensions);

        if (empty($extensions)) {
            return true;
        }

        foreach ($extensions as $extension) {
            if ($this->isExtensionInstalled($extension)) {
                return true;
            } else if ($this->_showNewReleases) {
                return true;
            }
        }

        return false;
	}

    /**
     * Determine that extension with $code installed on current instance
     *
     * @param $code
     * @return bool
     */
    protected function isExtensionInstalled($code)
	{
		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        foreach ($modules as $moduleName) {
        	if ($moduleName == $code){
        		return true;
        	}
        }
        
		return false;
	}
}