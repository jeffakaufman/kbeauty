<?php
/**
 * Singleton
 */
class Aitoc_Aitreports_Model_Container extends Mage_Core_Model_Abstract
{
    protected $_products = array();

    /**
     * @param int $id
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct($id)
    {
        if(!isset($this->_products[$id])) {
            $product = Mage::getModel('catalog/product');
            $this->_products[$id] = $product->load($id);
        }
        return $this->_products[$id];
    }
}
