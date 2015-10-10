<?php

$installer = $this;

$mainModuleTable = $installer->getTable('press/press');

//die($tableSliderData);

$installer->startSetup();


//TODO: Create Correct Schema

$installer->getConnection()->dropTable($mainModuleTable);
$table = $installer->getConnection()
    ->newTable($mainModuleTable)
    ->addColumn('press_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))
    ->addColumn('category', Varien_Db_Ddl_Table::TYPE_VARCHAR, '100', array(
        'nullable'  => false,
    ))
    ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
        'nullable'  => false,
    ))
    ->addColumn('product_image_src', Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
        'nullable'  => false,
    ))
    ->addColumn('product_description', Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
        'nullable'  => false,
    ))
    ->addColumn('product_link', Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
        'nullable'  => false,
    ))
    ->addColumn('image_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
        'nullable'  => false,
    ))
    ->addColumn('image_src', Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
        'nullable'  => false,
    ))
    ->addColumn('article_link', Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
        'nullable'  => false,
    ))
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, '5', array(
        'nullable'  => false,
    ));
$installer->getConnection()->createTable($table);

$installer->endSetup();

/*

	{
			* "category": "editorial",
			* "product_name": "Contrast Black Top",
			* "product_image_src": "/media/catalog/product/cache/3/image/157x157/9df78eab33525d08d6e5fb8d27136e95/3/f/3f74001_print_flat.jpg",
			* "product_description": "A crew neck shell top gets a modern update with an artsy paint-printed front and a solid contrast back that dips into a high-low hem",
			* "product_link": "/shirts-and-blouses/contrast-back-top-2860",
			* "image_name": "People En Espanol - September 2015",
			* "image_src": "/media/cms1520/press/images/press/editorial/Fifteen_Twenty_People_En_Espanol_September issue_Cover.jpg",
			"article_link": ""
		},


$installer->startSetup();


 */