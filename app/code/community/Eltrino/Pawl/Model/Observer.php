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


class Eltrino_Pawl_Model_Observer
{
    /**
     * Pre-dispatch admin action controller
     *
     * @param Varien_Event_Observer $observer
     */
    public function preDispatch(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $feedModel  = Mage::getModel('eltrino_pawl/feed');
            /* @var $feedModel Eltrino_Pawl_Model_Feed */
            $feedModel->checkUpdate();
        }
    }
}