<?php

$installer = $this;

$theTable = $installer->getTable('promotionmodules/promotionmodules');

//die($tableSliderData);

$installer->startSetup();

$installer->getConnection()->dropTable($theTable);
$table = $installer->getConnection()
    ->newTable($theTable)
    ->addColumn('module_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ))
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
        'nullable'  => false,
    ))
    ->addColumn('sub_title', Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
        'nullable'  => false,
    ))
    ->addColumn('link_caption', Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
        'nullable'  => false,
    ))
    ->addColumn('link_href', Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
        'nullable'  => false,
    ))
    ->addColumn('desktop_image', Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
        'nullable'  => false,
    ))
    ->addColumn('mobile_image', Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
        'nullable'  => false,
    ))
    ->addColumn('details', Varien_Db_Ddl_Table::TYPE_TEXT, '2048', array(
        'nullable'  => false,
    ))
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, '5', array(
        'nullable'  => false,
    ))
    ->addColumn('active', Varien_Db_Ddl_Table::TYPE_SMALLINT, '1', array(
        'nullable'  => false,
        'default' => '1',
        'comment' => 'Active-InActive',
    ))
    ->addColumn('active_from', Varien_Db_Ddl_Table::TYPE_DATE, array(
        'nullable'  => true,
        'default' => NULL,
        'comment' => 'Active-Period-Start',
    ))
    ->addColumn('active_to', Varien_Db_Ddl_Table::TYPE_DATE, array(
        'nullable'  => true,
        'default' => NULL,
        'comment' => 'Active-Period-End',
    ))
    ->addColumn('text_color', Varien_Db_Ddl_Table::TYPE_VARCHAR, '20', array(
        'nullable'  => true,
    ))
    ->addColumn('bg_color', Varien_Db_Ddl_Table::TYPE_VARCHAR, '20', array(
        'nullable'  => true,
    ));

    ;
$installer->getConnection()->createTable($table);

$installer->endSetup();

/*

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS `slider_data_list`;
CREATE TABLE IF NOT EXISTS `slider_data_list` (
  `slide_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `sub_title` varchar(255) NOT NULL DEFAULT '',
  `link_caption` varchar(255) NOT NULL,
  `link_href` varchar(255) NOT NULL,
  `bg_image` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`slide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ");

$installer->endSetup();
*/
/*
{
bg: "<?php echo $this->helper('cms')->getBlockTemplateProcessor()->filter('{{media url="wysiwyg/slider/slide_bg1.jpg"}}') ?>",
image: "<?php echo $this->helper('cms')->getBlockTemplateProcessor()->filter('{{media url="wysiwyg/slider/slide1_image.jpg"}}') ?>",
title: "CABANA BRONZE",
subTitle: "SUMMERY SATIN FINISH<br>ALL&ndash;YEAR ROUND",
linkCaption: "SHOP NOW",
linkHref: "#"
},

 */