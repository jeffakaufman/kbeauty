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


class Eltrino_Pawl_Model_Source_Channels
{
    const TYPE_PROMO  = 'PROMO';
    const TYPE_NEW_RELEASE = 'NEW_RELEASE';
    const TYPE_UPDATES = 'UPDATES';
    const TYPE_OTHER_INFO = 'OTHER_INFO';

    public function toOptionArray()
    {
        $helper = Mage::helper('eltrino_pawl');
        return array(
            array('value' => self::TYPE_UPDATES, 'label' => $helper->__('Updates For Installed Extensions')),
            array('value' => self::TYPE_NEW_RELEASE, 'label' => $helper->__('New Releases')),
            array('value' => self::TYPE_PROMO, 'label' => $helper->__('Promotions and Discounts from Eltrino Team')),
            array('value' => self::TYPE_OTHER_INFO, 'label' => $helper->__('Other information'))
        );
    }
}
