<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Freegift
 * @copyright   Copyright (c) 2015 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('freegift/rule_apply');
 
// Check if the table already exists
if ($installer->getConnection()->isTableExists($tableName)) {
    $table = $installer->getConnection();
    
    $installer->getConnection()->addIndex(
            $installer->getTable('freegift/rule_apply'),
            $installer->getIdxName('freegift/rule_apply', array('product_id')),
            array('product_id')
    );
    
    $installer->getConnection()->addIndex(
            $installer->getTable('freegift/rule_apply'),
            $installer->getIdxName('freegift/rule_apply', array('customer_group_id')),
            array('customer_group_id')
    );
    
    $installer->getConnection()->addIndex(
            $installer->getTable('freegift/rule_apply'),
            $installer->getIdxName('freegift/rule_apply', array('website_id')),
            array('website_id')
    );        

    $installer->getConnection()->addForeignKey(
            $installer->getFkName('freegift/rule_apply', 'product_id', 'freegift/rule_apply', 'product_id'),
            $installer->getTable('freegift/rule_apply'),
            'product_id',
            $installer->getTable('catalog/product'),
            'entity_id'
    );
   
}
 
$installer->endSetup();