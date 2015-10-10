<?php
class Ddg_Transactional_Model_Adminhtml_Source_Port
{

	public function toOptionArray()
	{
		return array(
				'25' => Mage::helper('ddg_transactional')->__("25"),
				'2525' => Mage::helper('ddg_transactional')->__("2525"),
				'587' => Mage::helper('ddg_transactional')->__("587")
		);


	}
}