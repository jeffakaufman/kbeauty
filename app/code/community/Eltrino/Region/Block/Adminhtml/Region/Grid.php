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

/**
 * Model for configuration entity
 *
 * @category   Eltrino
 * @package    Eltrino_Region
 */
class Eltrino_Region_Block_Adminhtml_Region_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_countries = array();
    protected $_filterColumn;
    protected $_filterValue;
    protected $_filterCharset;

    public function __construct()
    {
        parent::__construct();
        $this->setId('code');
        $this->setDefaultSort('country');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->_countries = $this->_getCountries();
    }

    /**
     * Prepare grid collection object
     *
     * @return this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_createCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Create collection from array
     *
     * @return Eltrino_Region_Data_Collection
     */
    protected function _createCollection()
    {
        $page = $this->getRequest()->getParam('page', 1);
        $limit = $this->getRequest()->getParam('limit', 20);
        $collection = new Eltrino_Region_Data_Collection();
        $countries = $this->_getCountryAvailableRegions();
        $collection->setDataArray($countries)
            ->setPageSize($limit)
            ->setCurPage($page);

        return $collection;
    }

    /**
     * If country has regions add Yes value else No
     *
     * @return array
     */
    protected function _getCountryAvailableRegions()
    {
        $availableRegionsList = Mage::getResourceModel('eltrino_region/region')->getCountryAvailableRegionsList();
        foreach ($this->_countries as &$country) {
            $country['region_availability'] = $this->__('No');
            if (array_key_exists($country['value'], $availableRegionsList)) {
                $country['region_availability'] = $this->__('Yes');
            }
        }

        return $this->_countries;
    }

    /**
     * Get countries
     *
     * @return array
     */
    protected function _getCountries()
    {
        return Mage::getSingleton('directory/country')->getCollection()
            ->loadByStore()->toOptionArray(false);
    }

    /**
     * Create collection from array
     *
     * @var Mage_Adminhtml_Block_Widget_Grid_Column $column
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _setCollectionOrder($column)
    {
        $order = $column->getDir() == 'asc' ? SORT_ASC : SORT_DESC;
        $columnName = $column->getIndex();

        // variable label is country name, variable value is country code
        $label = $value = array();
        foreach ($this->_countries as $item) {
            $label[] = $item['label'];
            $value[] = $item['value'];
            $region_availability[] = $item['region_availability'];
        }
        array_multisort($$columnName, $order, $this->_countries);
        $collection = $this->_createCollection($this->_countries);
        $this->setCollection($collection);

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'country',
            array(
                'header'                    => $this->__('Country name'),
                'index'                     => 'label',
                'filter_condition_callback' => array($this, 'columnFilter'),
            )
        );

        $this->addColumn(
            'code',
            array(
                'header'                    => $this->__('Code'),
                'width'                     => '150px',
                'index'                     => 'value',
                'filter_condition_callback' => array($this, 'columnFilter'),
            )
        );

        $this->addColumn(
            'region_availability',
            array(
                'header'                    => $this->__('Availability regions/states'),
                'width'                     => '150px',
                'index'                     => 'region_availability',
                'filter_condition_callback' => array($this, 'columnFilter'),
            )
        );

        $this->addColumn(
            'action',
            array(
                'header'   => Mage::helper('eltrino_region')->__('Action'),
                'width'    => '50px',
                'type'     => 'action',
                'getter'   => 'getValue',
                'actions'  => array(
                    array(
                        'caption' => Mage::helper('eltrino_region')->__('Edit'),
                        'url'     => array(
                            'base'   => '*/*/edit',
                            'params' => array('store' => $this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'code'
                    )
                ),
                'filter'   => false,
                'sortable' => false,
                'index'    => 'stores',
            )
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('code' => $row->getValue()));
    }

    /**
     * Callback for column filter
     *
     * @var Eltrino_Region_Data_Collection          $defaultCollection
     * @var Mage_Adminhtml_Block_Widget_Grid_Column $column
     */
    public function columnFilter($defaultCollection, $column)
    {
        $filterData = $column->getFilter()->getData();
        if (!isset($filterData['value'])) {
            return;
        }
        $this->_filterValue = $filterData['value'];
        $this->_filterColumn = $column->getIndex();
        $this->_filterCharset = Mage::getStoreConfig('design/head/default_charset');
        $this->_countries = array_filter($this->_countries, array($this, 'filterItem'));
    }

    public function filterItem($country)
    {
        return mb_stripos($country[$this->_filterColumn], $this->_filterValue, null, $this->_filterCharset) !== false;
    }
}