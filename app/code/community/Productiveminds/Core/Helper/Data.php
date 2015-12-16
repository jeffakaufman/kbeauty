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


class Productiveminds_Core_Helper_Data extends Mage_Core_Helper_Abstract {
	
	private $systemInfo = '<table rules="all" border=1 frame="void">';

	public function isModuleActive($moduleName = null,$enabledLocation=null)
	{

		if ($moduleName === null) {
			$moduleName = $this->_getModuleName();
		}

		if (Mage::getConfig()->getNode('modules/'.$moduleName) &&
				Mage::getStoreConfig('productivemindscore_sectns/productivemindscore_grps/enabled', Mage::app()->getStore())
		) {
			return true;
		} else {
			return false;
		}
		return false;
	}

	// return long from a readable IP address
	public function getIp2long($ip='') {
		// first check IP address is valid
		if(filter_var($ip, FILTER_VALIDATE_IP)) {
			$ip = trim($ip);
			return ip2long($ip);
		}
		return 'invalid IP address';
	}

	// return a human readable IP address from long
	public function getLong2ip($ip=0) {
		return long2ip(trim($ip));
	}

	public function systemInfo()
	{
		$this->mageConfig();
		$this->systemConfigs();
		$this->phpExts();
		$this->otherPromindsExts();

		return $this->getCollatedInfo();
	}

	private function mageConfig() {
		$this->addTableRow("Magento Details");

		$this->addTableRow('Version', self::getInstalledMagentoVersion());

		$compilerConfig = '../includes/config.php';
		if (file_exists($compilerConfig)) {
			include $compilerConfig;
		}

		$this->addTableRow('Compilation', defined('COMPILER_INCLUDE_PATH') ? 'Enabled' : 'Disabled');

		$this->addTableRow('Domain', $_SERVER ["HTTP_HOST"]);
	}

	private function otherPromindsExts() {
		$this->addTableRow("Other ProductiveMinds Extensions");

		$modules = ( array )Mage::getConfig()->getNode('modules')->children();
		foreach ($modules as $key => $value) {
			if (strpos($key, 'Productiveminds_', 0) !== false) {
				$this->addTableRow("{$key} (v {$value->version})", "{$value->active} ({$value->codePool})");
			}
		}
	}

	private function systemConfigs() {
		$this->addTableRow("Server and PhP Configs");
		$this->addTableRow('PHP version', phpversion());
		$ini = array(
				'safe_mode',
				'memory_limit',
				'realpath_cache_ttl',
				'allow_url_fopen'
		);
		foreach ($ini as $i) {
			$val = ini_get($i);
			$val = empty ($val) ? 'off' : $val;
			$this->addTableRow($i, $val);
		}
	}

	private function phpExts() {
		$this->addTableRow("Magento Required Extensions");
		$extensions = array(
				'curl',
				'dom',
				'gd',
				'hash',
				'iconv',
				'mcrypt',
				'pcre',
				'pdo',
				'pdo_mysql',
				'simplexml'
		);
		foreach ($extensions as $extension)
			$this->addTableRow($extension, extension_loaded($extension));
	}

	private function addTableRow($column1, $column2 = "") {
		if ($column2 === "") {
			$this->systemInfo .= "<tr><td colspan='2'> </td></tr>&nbsp;<tr><td colspan='2'><strong>{$column1}</strong></td></tr>";
		} else {
			$this->systemInfo .= "<tr><td>{$column1}</td><td>{$column2}</td></tr>";
		}
	}
	
	private function getCollatedInfo() {
		return ($this->systemInfo .= "<tr><td colspan='2'>&nbsp;</td></tr></table>");
	}
	
	public function getInstalledMagentoVersion()
	{
		$version = Mage::getVersion();
		if (!Mage::getConfig()->getModuleConfig('Enterprise_Enterprise')) {
			return $version;
		}
		$info = explode('.', $version);
		$info[1] -= 5;
		$version = implode('.', $info);
		return $version;
	}

}
?>