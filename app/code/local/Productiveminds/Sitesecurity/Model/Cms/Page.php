<?php

class Productiveminds_Sitesecurity_Model_Cms_Page extends Mage_Core_Model_Abstract
{
	public function createNewPage() {
		$page = Mage::getModel('cms/page');
		$page->setIdentifier(Productiveminds_Sitesecurity_Model_Security::BLANK_PAGE_IDENTIFIER);
		$page->setTitle(Productiveminds_Sitesecurity_Model_Security::BLANK_PAGE_TITLE);
		$page->setContent(Productiveminds_Sitesecurity_Model_Security::BLANK_PAGE_CONTENT);
		$page->setRootTemplate(Productiveminds_Sitesecurity_Model_Security::BLANK_PAGE_TEMPLATE);
		$page->setContentHeading('');
		$page->setMetaKeywords('');
		$page->setMetaDescription('');
		$page->setIsActive(1);
		$page->setStores( array(0) );
		$page->save();
	}
}