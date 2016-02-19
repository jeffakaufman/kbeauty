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
/*freegift_rule*/
$table = $installer->getConnection()->newTable($installer->getTable('freegift/rule'))
	->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned' => true,
			'nullable' => false,
			'primary' => true,
			'identity' => true,
	), 'Rule ID')
	->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
			'nullable' => false,
	), 'Rule Name')
	->addColumn('customer_groups', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
			'nullable' => false,
	), 'Customer Groups')
	->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
			'nullable' => false,
	), 'Conditions')
	->addColumn('actions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
			'nullable' => false,
	), 'Actions')
	->addColumn('website_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
			'nullable' => false,
	), 'Website Ids')
	->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'nullable' => false,
	), 'Is active')
	->setComment('Freegift Rules Table');

$installer->getConnection()->createTable($table);

/*gfreegift_rule_apply*/
$table = $installer->getConnection()->newTable($installer->getTable('freegift/rule_apply'))
	->addColumn('apply_rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned' => true,
			'nullable' => false,
			'primary' => true,
			'identity' => true,
	), 'Applied Rule ID')
	->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'nullable' => false,
	), 'Rule Id')
	->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'nullable' => false,
	), 'Product Id')
	->addColumn('gift_product_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
			'nullable' => false,
	), 'Ids of gift Products')
	->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'nullable' => false,
	), 'Customer Group Id')
	->addColumn('cart_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
			'nullable' => false,
	), 'Cart Rules')
	->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'nullable' => false,				
	),
	 'Website Id')
	->setComment('Freegift Applied Rules Table');

$installer->getConnection()->createTable($table);


$installer->endSetup();