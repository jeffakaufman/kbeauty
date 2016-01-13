<?php

$installer = $this;
$installer->startSetup();


$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$entityTypeId     = $setup->getEntityTypeId('catalog_category');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$setup->addAttribute('catalog_category', 'text_color', array(
    'type'    => 'varchar',
    'input'   => 'text',
    'input_renderer'=> 'catimage/adminhtml_helper_color',
    'group' => 'General Information',
    'label'         => 'Text Color',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'frontend_input' =>'',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible_on_front'  => 1,
));

$setup->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'text_color',
    '999'  //sort_order
);

$installer->endSetup();