<?php
/**
 * Remove or Change Displayed States and Regions
 *
 * LICENSE
 *
 * This source file is subject to the Eltrino LLC EULA
 * that is bundled with this package in the file LICENSE_EULA.txt.
 * It is also available through the world-wide-web at this URL:
 * http://eltrino.com/license-eula.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@eltrino.com so we can send you a copy immediately.
 *
 * @category    Eltrino
 * @package     Eltrino_Region
 * @copyright   Copyright (c) 2014 Eltrino LLC. (http://eltrino.com)
 * @license     http://eltrino.com/license-eula.txt  Eltrino LLC EULA
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('eltrino_region/step'))
    ->addColumn(
        'step_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ), 'Region Step Id'
    )
    ->addColumn(
        'country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 2, array(
            'default'  => null,
            'nullable' => true,
        ), 'Country Id'
    )
    ->addColumn(
        'type_id', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
            'default'  => 'both',
            'nullable' => false,
        ), 'Type Id'
    )
    ->addColumn(
        'scope', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
            'default'  => 'stores',
            'nullable' => false,
        ), 'Scope'
    )
    ->addColumn(
        'scope_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'default'  => '0',
            'nullable' => false,
        ), 'Scope Id'
    )
    ->addColumn(
        'fieldset_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'Fieldset Id'
    )
    ->setComment('Eltrino Region Step');
$installer->getConnection()->createTable($table);

$installer->getConnection()->addColumn(
    $this->getTable('eltrino_region/entity'),
    'fieldset_id',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => false,
        'unsigned' => true,
        'comment'  => 'Fieldset Id'
    )
);

$entityCollection = Mage::getResourceModel('eltrino_region/entity_collection');
$entityCollection->getSelect()
    ->distinct(true)
    ->group(array('country_id', 'scope', 'scope_id'));


$fieldsetId = 1;
foreach ($entityCollection as $item) {
    $countryId = $item->getCountryId();
    $connection = Mage::getSingleton('core/resource')
        ->getConnection('core_write');
    $connection->update(
        $this->getTable('eltrino_region/entity'),
        array('fieldset_id' => $fieldsetId),
        array('country_id = ?' => $countryId)
    );
    $stepData = array('country_id'  => $countryId,
                      'type_id'     => 'both',
                      'scope'       => $item->getScope(),
                      'scope_id'    => $item->getScopeId(),
                      'fieldset_id' => $fieldsetId);
    $connection->insert($this->getTable('eltrino_region/step'), $stepData);
    $fieldsetId++;
}

$installer->endSetup();
