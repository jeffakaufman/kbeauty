<?php
 
class Productiveminds_Sitesecurity_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() {
    	$this->loadLayout();
    	Mage::getModel('sitesecurity/cleanhouse')->updateVisitor();
    	$this->renderLayout();
    }
    
}