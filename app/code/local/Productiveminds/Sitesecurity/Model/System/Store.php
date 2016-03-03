<?php

class Productiveminds_Sitesecurity_Model_System_Store extends Mage_Adminhtml_Model_System_Store 
{
    public function getStoreValuesForForm($empty = false, $all = false)
    {
        $options = array();
        if ($empty) {
            $options[] = array(
                'label' => Mage::helper('adminhtml')->__('-- Please Select --'),
					'value' => ''
            );
        }
        if ($all && $this->_isAdminScopeAllowed) {
            $options[] = array(
                'label' => Mage::helper('adminhtml')->__('All Store Views'),
                'value' => 0
            );
        }

        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');

        foreach ($this->_websiteCollection as $website) {
            $websiteShow = false;
            foreach ($this->_groupCollection as $group) {
                if ($website->getId() != $group->getWebsiteId()) {
                    continue;
                }
                $groupShow = false;
                foreach ($this->_storeCollection as $store) {
                    if ($group->getId() != $store->getGroupId()) {
                        continue;
                    }
                    if (!$websiteShow) {
                        $options[] = array(
                            'label' => $website->getName(),
                            'value' => array()
                        );
                        $websiteShow = true;
                    }
                    if (!$groupShow) {
                        $groupShow = true;
                        $values    = array();
                    }
                    $values[] = array(
                        'label' => str_repeat($nonEscapableNbspChar, 4) . $store->getName(),
                        'value' => $store->getId()
                    );
                }
                if ($groupShow) {
                    $options[] = array(
                        'label' => str_repeat($nonEscapableNbspChar, 4) . $group->getName(),
                        'value' => $values
                    );
                }
            }
        }
        return $options;
    }
}
