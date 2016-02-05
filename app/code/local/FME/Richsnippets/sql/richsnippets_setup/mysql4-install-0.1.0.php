<?php

$installer = $this;

$installer->startSetup();

$installer->setConfigData('richsnippets_options/settings/set_name','Yes');
$installer->setConfigData('richsnippets_options/settings/set_breadcrumbs','Yes');
$installer->setConfigData('richsnippets_options/settings/product_sku','Yes');
$installer->setConfigData('richsnippets_options/settings/set_url','Yes');
$installer->setConfigData('richsnippets_options/settings/set_image','Yes');
$installer->setConfigData('richsnippets_options/settings/set_desc','Yes');
$installer->setConfigData('richsnippets_options/settings/set_price','Yes');
$installer->setConfigData('richsnippets_options/settings/set_pricecurrencey','Yes');
$installer->setConfigData('richsnippets_options/settings/product_status','Yes');
$installer->setConfigData('richsnippets_options/settings/p_brand','No');
$installer->setConfigData('richsnippets_options/settings/p_color','No');
$installer->setConfigData('richsnippets_options/settings/p_weight','No');
$installer->setConfigData('richsnippets_options/settings/p_brand_att','Brand');
$installer->setConfigData('richsnippets_options/settings/p_coloratt','Color');
$installer->setConfigData('richsnippets_options/settings/p_weightatt','Weight');
$installer->setConfigData('richsnippets_options/settings/p_review','Yes');
$installer->setConfigData('richsnippets_options/general_info/schema_url','http://schema.org/Product');
$installer->setConfigData('richsnippets_options/general_info/schema_offerurl','http://schema.org/Offer');
$installer->setConfigData('richsnippets_options/general_info/breadcrumb_url','http://data-vocabulary.org/Breadcrumb');

$installer->endSetup(); 