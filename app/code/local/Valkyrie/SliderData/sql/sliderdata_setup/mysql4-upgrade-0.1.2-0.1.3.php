<?php

$installer = $this;

$tableSliderData = $installer->getTable('sliderdata/sliderdata');

//die($tableSliderData);

$installer->startSetup();

$table = $installer->getConnection()
    ->addColumn($tableSliderData, 'active_from', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DATE,
//        'length' => '1',
        'nullable'  => true,
        'default' => NULL,
        'comment' => 'Active-Period-Start',
    ));
$table = $installer->getConnection()
    ->addColumn($tableSliderData, 'active_to', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DATE,
//        'length' => '1',
        'nullable'  => true,
        'default' => NULL,
        'comment' => 'Active-Period-End',
    ))
;
//$installer->getConnection()->createTable($table);

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