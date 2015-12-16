<?php
/**
 *  A Magento module by ProductiveMinds
 *
 * NOTICE OF LICENSE
 *
 * This code is the effort and copyright of Productive Minds Ltd, A UK registered company.
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

class Productiveminds_Sitesecurity_Model_Email extends Mage_Core_Model_Abstract
{
	const XML_PATH_EMAIL_RECIPIENT  = 'sitesecurity_sectns/sitesecurity_grps/email_recipient';
    const XML_PATH_EMAIL_SENDER     = 'sitesecurity_sectns/sitesecurity_grps/email_sender';
    const XML_PATH_EMAIL_TEMPLATE   = 'sitesecurity_sectns/sitesecurity_grps/email_template';
	
	public function sendEmail()
	{
		self::_sendEmail();
	}
	
	protected function _sendEmail() {
		
		$timeSinceLastEmail = new DateTime('Yesterday 07:14:59');
		$timeSinceLastEmail = $timeSinceLastEmail->format('Y-m-d H:i:s');
		
		// Get order model
		$deniedAttempts = Mage::getModel('sitesecurity/sitesecure')
		->getCollection()
		->addFieldToFilter('created_at ', array('gt'=> $timeSinceLastEmail)); // get all since 7:15am yesterday
		
		if (count($deniedAttempts) > 0 ) {
			
			$translate = Mage::getSingleton('core/translate');
			$translate->setTranslateInline(false);
			$data = array(
					Productiveminds_Sitesecurity_Model_Security::EMAIL_VAR_XSS => 0,
					Productiveminds_Sitesecurity_Model_Security::EMAIL_VAR_FORM_INJECTION => 0,
					Productiveminds_Sitesecurity_Model_Security::EMAIL_VAR_BLACKLIST_COUNTRY => 0,
					Productiveminds_Sitesecurity_Model_Security::EMAIL_VAR_BLACKLIST_IP => 0
				);
			
			foreach ($deniedAttempts as $deniedAttempt) {
				if($deniedAttempt->getTypeCode() == Productiveminds_Sitesecurity_Model_Security::ACL_CODE_XSS) {
					$data[Productiveminds_Sitesecurity_Model_Security::EMAIL_VAR_XSS] += 1;
				} elseif($deniedAttempt->getTypeCode() == Productiveminds_Sitesecurity_Model_Security::ACL_CODE_FORM_INJECTION) {
					$data[Productiveminds_Sitesecurity_Model_Security::EMAIL_VAR_FORM_INJECTION] += 1;
				} elseif($deniedAttempt->getTypeCode() == Productiveminds_Sitesecurity_Model_Security::ACL_CODE_BLACKLIST_COUNTRY) {
					$data[Productiveminds_Sitesecurity_Model_Security::EMAIL_VAR_BLACKLIST_COUNTRY] += 1;
				} elseif($deniedAttempt->getTypeCode() == Productiveminds_Sitesecurity_Model_Security::ACL_CODE_BLACKLIST_IP) {
					$data[Productiveminds_Sitesecurity_Model_Security::EMAIL_VAR_BLACKLIST_IP] += 1;
				}
			}
			
			$data['total'] = count($deniedAttempts);
			$data['admin_url'] = Mage::getUrl('sitesecurity_admin/adminhtml_sitesecure');
			
			try {
				$dataObject = new Varien_Object();
				$dataObject->setData($data);
				
				$mailTemplate = Mage::getModel('core/email_template');
				
				$store = Mage::app()->getStore();
				$senderEmailId = Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, $store);
				$recipientEmailId = Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT, $store);
				$emailTemplate = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $store);
				
				$recipientEmailId = str_replace(' ', '', $recipientEmailId);
				$recipientEmailId = explode(',', $recipientEmailId);
				
				$mailTemplate->setDesignConfig(array('area' => 'frontend'))
					->setReplyTo($senderEmailId)
					->sendTransactional($emailTemplate, $senderEmailId, $recipientEmailId, null, array('data' => $dataObject)
				);
				
				if (!$mailTemplate->getSentSuccess()) {
					throw new Exception();
				}
				$translate->setTranslateInline(true);
				return;
			} catch (Exception $e) {
				$translate->setTranslateInline(true);
				Mage::log('Productiveminds_Sitesecurity_Model_Email::sendEmail - error sending email - see exception message below', null, 'Productiveminds_Sitesecurity.log');
				Mage::log('Productiveminds_Sitesecurity_Model_Email::sendEmail' . $e->getMessage(), null, 'Productiveminds_Sitesecurity.log');
				return;
			}
		}
		
	}
}