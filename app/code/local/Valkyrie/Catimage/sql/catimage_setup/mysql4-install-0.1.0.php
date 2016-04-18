<?php

$installer = $this;
$installer->startSetup();


$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$entityTypeId     = $setup->getEntityTypeId('catalog_category');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$setup->addAttribute('catalog_category', 'cat_image', array(
    'type'    => 'varchar',
    'input'   => 'image',
    'backend' => 'catalog/category_attribute_backend_image',
    'group' => 'General Information',
    'label'         => 'Category Image',
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
    'cat_image',
    '999'  //sort_order
);

$installer->endSetup();