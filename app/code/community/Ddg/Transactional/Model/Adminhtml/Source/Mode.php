<?php
class Ddg_Transactional_Model_Adminhtml_Source_Mode
{
	public function toOptionArray()
	{
		return array(
			'smtp' => Mage::helper('ddg_transactional')->__('SMTP')
			//'api' => Mage::helper('ddg_transactional')->__('API')
		);

	}
}