<?php

$installer = $this;
$installer->startSetup();


$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$entityTypeId     = $setup->getEntityTypeId('catalog_product');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$setup->addAttribute('catalog_product', 'k_sister_fave', array(
    'type'    => 'int',
    'input'   => 'boolean',
//    'frontend_input_renderer'=> 'catimage/adminhtml_helper_color',
    'group' => 'General',
    'label'         => 'K Sister Fave',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'frontend_input' => '',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible_on_front'  => 1,

    'source' => 'eav/entity_attribute_source_boolean',
));

$setup->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'k_sister_fave',
    '201'  //sort_order
);

$setup->addAttribute('catalog_product', 'fierce_collection', array(
    'type'    => 'int',
    'input'   => 'boolean',
//    'frontend_input_renderer'=> 'catimage/adminhtml_helper_color',
    'group' => 'General',
    'label'         => 'Fierce Collection',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'frontend_input' => '',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible_on_front'  => 1,

    'source' => 'eav/entity_attribute_source_boolean',
));

$setup->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'fierce_collection',
    '202'  //sort_order
);

$installer->endSetup();