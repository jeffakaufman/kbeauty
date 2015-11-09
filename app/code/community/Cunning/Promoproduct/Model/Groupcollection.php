<?php
class Cunning_Promoproduct_Model_Groupcollection
{
 	/**
     * Options getter
     *
     * @return array
     */
	public function toOptionArray()
	{
		$group = Mage::getModel('customer/group')->getCollection();
		$groupArray = array();
		foreach ($group as $eachGroup)
		{
			$groupData = array(
							'value' => $eachGroup->getCustomerGroupId(),
							'label' => $eachGroup->getCustomerGroupCode()
							);
			if (!empty($groupData))
			{
				array_push($groupArray, $groupData);
			}
		}
		return $groupArray;
	}
}
?>
