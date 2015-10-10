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
class Eltrino_Region_Block_Adminhtml_Region_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('eltrino_region_form');
    }

    /**
     * Setup form fields for inserts/updates
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $code = $this->getRequest()->getParam('code');
        $country = Mage::app()->getLocale()->getCountryTranslation($code);

        $form = new Varien_Data_Form(array(
            'id'     => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('code' => $code)),
            'method' => 'post',
        ));

        $fieldset = $form->addFieldset(
            'base_fieldset', array(
                'legend' => Mage::helper('eltrino_region')->__('General Information'),
                'class'  => 'fieldset-wide',
            )
        );

        $fieldset->addField(
            'country', 'text', array(
                'name'     => 'country',
                'label'    => Mage::helper('eltrino_region')->__('Country Name'),
                'title'    => Mage::helper('eltrino_region')->__('Country Name'),
                'disabled' => true,
                'style'    => 'width:459px !important'
            )
        );

        $fieldset->addField(
            'country_id', 'hidden', array(
                'name' => 'country_id',
            )
        );

        $fieldset->addField(
            'code', 'text', array(
                'name'     => 'code',
                'label'    => Mage::helper('eltrino_region')->__('Code'),
                'title'    => Mage::helper('eltrino_region')->__('Code'),
                'disabled' => true,
                'style'    => 'width:459px !important'
            )
        );

        $fieldset->addField(
            'regions_states', 'text', array(
                'name'  => 'regions_states',
                'label' => Mage::helper('eltrino_region')->__('Regions/States'),
            )
        );

        $form->getElement('regions_states')->setRenderer(
            $this->getLayout()->createBlock('eltrino_region/adminhtml_region_edit_form_regions')
        );

        $form->setValues(array('code' => $code, 'country' => $country, 'country_id' => $code));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
