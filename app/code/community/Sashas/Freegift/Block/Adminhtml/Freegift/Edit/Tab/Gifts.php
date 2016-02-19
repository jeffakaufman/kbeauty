<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Freegift
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */
class Sashas_Freegift_Block_Adminhtml_Freegift_Edit_Tab_Gifts
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('freegift')->__('Gifts');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('freegift')->__('Gifts');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_freegift_rule');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');
               
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
        ->setTemplate('promo/fieldset.phtml') 
         ->setNewChildUrl($this->getUrl('*/promo_catalog/newConditionHtml/form/rule_actions_fieldset'));
        
        $fieldset = $form->addFieldset('actions_fieldset', array(
        		'legend'=>Mage::helper('freegift')->__('Select Gift Products'))
        )->setRenderer($renderer);
       
        $fieldset->addField('actions', 'text', array(
        		'name' => 'actions',
        		'label' => Mage::helper('freegift')->__('Products'),
        		'title' => Mage::helper('freegift')->__('Products'),
        		'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('freegift/adminhtml_actions'));
        
        $form->setValues($model->getData());

        

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
