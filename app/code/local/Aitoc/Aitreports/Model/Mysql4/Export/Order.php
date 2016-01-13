<?php

class Aitoc_Aitreports_Model_Mysql4_Export_Order extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('aitreports/export_order', 'order_id');
    }
    
    /**
    * Delete old exported orders ids from log table to be able to export same orders again
    * 
    * @param int $profile_id
    * @return Aitoc_Aitreports_Model_Mysql4_Export_Order
    */
    public function truncateByProfileId( $profile_id )
    {
        $deleteSql = ' UPDATE `'.$this->getMainTable().'` SET profile_id = 0'.
            ' WHERE profile_id = '.(int)$profile_id.' ';
        $this->_getWriteAdapter()->query($deleteSql);

        return $this;
    }

    /**
     * Works with loaded object 
     * Checks $this->getOrdersCount(). If 0 retreives orders and adds them to the airreports_export_order table. Updates orders_count
     * Loads orders collection
     * 
     * @return Aitoc_Aitreports_Model_Mysql4_Export_Order_Collection
     */
    public function assignOrders(Aitoc_Aitreports_Model_Export $export)
    {
        $deleteSql = ' DELETE FROM `'.$this->getMainTable().'` '.
            ' WHERE export_id = '.$this->_getWriteAdapter()->quote($export->getId()).' ';
        $this->_getWriteAdapter()->query($deleteSql);

        $filter         = $export->getConfig()->getFilter();
        $auto           = $export->getConfig()->getAuto();
        $mainTableAlias = Mage::helper('aitreports/version')->collectionMainTableAlias();

        $exportOrderCollection = Mage::getModel('sales/order')->getCollection();
        /* @var $exportOrderCollection Mage_Sales_Model_Mysql4_Order_Collection */
        $selectSql = $exportOrderCollection->getSelect();
        /* @var $selectSql Varien_Db_Select */

        if (!empty($filter['product_id_from']) || !empty($filter['product_id_to']))
        {
            $selectSql->join(array('order_item_table' => $this->getTable('sales/order_item')), 
                                    $mainTableAlias.'.entity_id=order_item_table.order_id', 
                                    array());
        }

        if (!empty($filter['product_id_from']))
        {
            $selectSql->where('order_item_table.product_id >= ?', $filter['product_id_from']);
        }

        if (!empty($filter['product_id_to']))
        {
            $selectSql->where('order_item_table.product_id <= ?', $filter['product_id_to']);
        }

        if ($export->getStoreId())
        {
            $exportOrderCollection->addAttributeToFilter('store_id', $export->getStoreId());
        }

        if (!empty($filter['orderstatus']))
        {
            $exportOrderCollection->addAttributeToFilter('status', $filter['orderstatus']);
        }
        
        if(isset($filter['range']) && !in_array($filter['range'], array('custom','alltime'))) {
            $override = Mage::helper('aitreports/date')->getDateRange($filter['range'], 'MM/dd/yyy');
            $filter['date_from'] = $override['start'];
            $filter['date_to'] = $override['end']; 
        }
        
        if (!empty($filter['date_from']))
        {
            $exportOrderCollection->addAttributeToFilter($mainTableAlias.'.created_at',
                array('gteq' => date('Y-m-d H:i:s', strtotime($filter['date_from']))));
        }

        if (!empty($filter['date_to']))
        {
            $exportOrderCollection->addAttributeToFilter($mainTableAlias.'.created_at',
                array('lt' => date('Y-m-d H:i:s', strtotime($filter['date_to'].' + 1 day'))));
        }

        if (!empty($filter['order_id']))
        {
            $exportOrderCollection->addAttributeToFilter('entity_id', $filter['order_id']);
        }

        if (!empty($filter['order_id_from']))
        {
            $orderFrom = Mage::getModel('sales/order')->loadByIncrementId($filter['order_id_from']);
            if ($orderFrom->getId())
            {
                $exportOrderCollection->addAttributeToFilter('entity_id', array('gteq' => $orderFrom->getId()));
            }
        }

        if (!empty($filter['order_id_to']))
        {
            $orderTo = Mage::getModel('sales/order')->loadByIncrementId($filter['order_id_to']);
            if ($orderTo->getId())
            {
                $exportOrderCollection->addAttributeToFilter('entity_id', array('lteq' => $orderTo->getId()));
            }
        }

        if (!empty($auto['bind_order_id']) && $export->getProfileId()!=0)
        {
            $selectSql->joinLeft(array('aeo' => $this->getTable('aitreports/export_order')), 
                                    'aeo.profile_id = '.(int)$export->getProfileId().' AND '.$mainTableAlias.'.entity_id=aeo.order_id', 
                                    array());
            $selectSql->where('aeo.order_id is NULL');
        }

        if (!empty($filter['customer_id_from']))
        {
            $exportOrderCollection->addAttributeToFilter('customer_id', array('gteq' => $filter['customer_id_from']));
        }

        if (!empty($filter['customer_id_to']))
        {
            $exportOrderCollection->addAttributeToFilter('customer_id', array('lteq' => $filter['customer_id_to']));
        }

        $selectSql->setPart(Zend_Db_Select::COLUMNS, array())->columns(array('order_id' => ' DISTINCT('.$mainTableAlias.'.entity_id)', 'export_id' => '('.$export->getId().')', 'profile_id' => '('.(int)$export->getProfileId().')'));

        $insertSql = ' INSERT INTO `'.$this->getTable('aitreports/export_order').'` (order_id, export_id, profile_id) '.$selectSql->assemble().' ';
        
        $this->_getWriteAdapter()->query($insertSql);

        return $this;
    }
}
