<?php

$installer = $this;
$installer->startSetup();


$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$entityTypeId     = $setup->getEntityTypeId('catalog_product');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$setup->addAttribute('catalog_product', 'top_logo', array(
    'type'    => 'varchar',
    'input'   => 'select',
//    'frontend_input_renderer'=> 'catimage/adminhtml_helper_color',
    'group' => 'General',
    'label'         => 'Top Logo',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'frontend_input' => '',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible_on_front'  => 1,

    'option' =>
        array (
            'values' =>
                array (
                    0 => 'None',
                    1 => 'New Colors',
                    2 => 'New Products',
                ),
        ),
));

$setup->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'top_logo',
    '200'  //sort_order
);

$installer->endSetup();