<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Customer_Visitor_Filter extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->addField('filter_value', 'select',
                array(
                    'name' => 'filter_value',
                    'onchange' => 'this.form.submit()',
                    'values' => array(
                        array(
                            'label' => Mage::helper('security')->__('All'),
                            'value' => '',
                        ),

                        array(
                            'label' => Mage::helper('security')->__('Customers Only'),
                            'value' => 'filterCustomers',
                        ),

                        array(
                            'label' => Mage::helper('security')->__('Visitors Only'),
                            'value' => 'filterGuests',
                        )
                    ),
                    'no_span' => true
                )
        );

        $form->setUseContainer(true);
        $form->setId('filter_form');
        $form->setMethod('post');

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
