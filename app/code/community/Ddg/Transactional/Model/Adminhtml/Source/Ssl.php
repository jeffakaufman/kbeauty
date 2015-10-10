<?php
class Ddg_Transactional_Model_Adminhtml_Source_Ssl
{
	public function toOptionArray()
	{
		return array(
			'no' => Mage::helper('ddg_transactional')->__('No SSL'),
			'tls' => Mage::helper('ddg_transactional')->__('TLS')
		);

	}
}