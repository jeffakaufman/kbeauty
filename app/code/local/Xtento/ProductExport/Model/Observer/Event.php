<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2014-07-02T20:37:47+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/Observer/Event.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Observer_Event extends Xtento_ProductExport_Model_Observer_Abstract
{
    private $_events = array();
    static $_exportedIds = array();

    /*
     * Event configuration
     */
    // Magento default events
    const EVENT_CATALOG_PRODUCT_SAVE_AFTER = 1;
    const EVENT_CATALOG_CATEGORY_SAVE_AFTER = 2;

    // Third party events
    // None at this time.

    public function getEvents($entity = false, $allEvents = false)
    {
        $events = array();
        // Events where product information can be exported
        if ($allEvents || $entity == Xtento_ProductExport_Model_Export::ENTITY_PRODUCT) {
            $events[Xtento_ProductExport_Model_Export::ENTITY_PRODUCT][self::EVENT_CATALOG_PRODUCT_SAVE_AFTER] = array(
                'event' => 'catalog_product_save_after',
                'label' => Mage::helper('xtento_productexport')->__('After product gets saved (Event: catalog_product_save_after)'),
                'method' => 'getProduct()'
            );
        }
        // Events where category information can be exported
        if ($allEvents || $entity == Xtento_ProductExport_Model_Export::ENTITY_CATEGORY) {
            $events[Xtento_ProductExport_Model_Export::ENTITY_CATEGORY][self::EVENT_CATALOG_CATEGORY_SAVE_AFTER] = array(
                'event' => 'catalog_category_save_after',
                'label' => Mage::helper('xtento_productexport')->__('After category gets saved (Event: catalog_category_save_after)'),
                'method' => 'getCategory()'
            );
        }
        // Third party events
        /* Sample:
        if (Mage::helper('xtcore/utils')->isExtensionInstalled('MDN_ProductReturn') && ($allEvents || $entity == Xtento_ProductExport_Model_Export::ENTITY_PRODUCT)) {
            $events[Xtento_ProductExport_Model_Export::ENTITY_PRODUCT][self::EVENT_PRODUCTRETURN_PRODUCT_CREATED_FOR_RMA] = array(
                'event' => 'productreturn_product_created_for_rma',
                'label' => Mage::helper('xtento_productexport')->__('productreturn_order_created_for_rma (Called by "MDN_ProductReturn" extension after RMA order placement)'),
                'method' => 'getProduct()'
            );
        }*/
        return $events;
    }

    /*
     * Add events below this line
     */
    public function catalog_product_save_after(Varien_Event_Observer $observer)
    {
        $this->_handleEvent($observer, self::EVENT_CATALOG_PRODUCT_SAVE_AFTER, Xtento_ProductExport_Model_Export::ENTITY_PRODUCT);
    }

    public function catalog_category_save_after(Varien_Event_Observer $observer)
    {
        $this->_handleEvent($observer, self::EVENT_CATALOG_CATEGORY_SAVE_AFTER, Xtento_ProductExport_Model_Export::ENTITY_CATEGORY);
    }


    /*
     *  Third party events
     */
    /*
     * Sample
     */
    /*public function productreturn_order_created_for_rma(Varien_Event_Observer $observer)
    {
        $this->_handleEvent($observer, self::EVENT_PRODUCTRETURN_ORDER_CREATED_FOR_RMA, Xtento_ProductExport_Model_Export::ENTITY_ORDER);
    }*/

    /* For third party events calling the handleEvent function from outside this class */
    public function handleEvent(Varien_Event_Observer $observer, $eventId = 0, $entity)
    {
        $this->_handleEvent($observer, $eventId, $entity);
    }

    /*
     * Code handling events
     */
    private function _handleEvent(Varien_Event_Observer $observer, $eventId = 0, $entity)
    {
        try {
            if (!Mage::helper('xtento_productexport')->getModuleEnabled() || !Mage::helper('xtento_productexport')->isModuleProperlyInstalled()) {
                return;
            }
            $event = $observer->getEvent();

            // Load profiles which are listening for this event
            $profileCollection = Mage::getModel('xtento_productexport/profile')->getCollection()
                ->addFieldToFilter('enabled', 1) // Profile enabled
                ->addFieldToFilter('entity', $entity)
                ->addFieldToFilter('event_observers', array('like' => '%' . $eventId . '%')); // Event enabled "pre-check"
            foreach ($profileCollection as $profile) {
                $profileId = $profile->getId();
                $eventObservers = explode(",", $profile->getEventObservers());
                if (!in_array($eventId, $eventObservers)) {
                    continue; // Not enabled for this event
                }
                if (!isset(self::$_exportedIds[$profileId])) {
                    self::$_exportedIds[$profileId] = array();
                }
                $exportObject = $this->_getExportObject($entity, $event, $eventId);
                if ($exportObject && $exportObject->getId() && !in_array($exportObject->getId(), self::$_exportedIds[$profileId])) {
                    $exportModel = Mage::getModel('xtento_productexport/export', array('profile' => $profile));
                    $filters = array(array('entity_id' => $exportObject->getId()));
                    $filters = array_merge($filters, $this->_addProfileFilters($profile));
                    if ($exportModel->eventExport($filters)) {
                        // Has been exported in this execution.. do not export again in the same execution.
                        array_push(self::$_exportedIds[$profileId], $exportObject->getId());
                        Mage::registry('product_export_log')->setExportEvent($this->_events[$entity][$eventId]['event'])->save();
                    }
                } else {
                    Mage::log('Event handler for event ' . $eventId . ': Could not find export object.', 'xtento_productexport_event.log', true);
                }
            }
        } catch (Exception $e) {
            Mage::log('Event handler exception for event ' . $eventId . ': ' . $e->getMessage(), null, 'xtento_productexport_event.log', true);
            return;
        }
    }

    private function _getExportObject($entity, $event, $eventId)
    {
        if (empty($this->_events)) {
            $this->_events = $this->getEvents(false, true);
        }
        if (isset($this->_events[$entity][$eventId]) && isset($this->_events[$entity][$eventId]['method'])) {
            $eventMethods = explode("->", str_replace('()', '', $this->_events[$entity][$eventId]['method']));
            if (count($eventMethods) == 1) {
                return $event->{$eventMethods[0]}();
            } else if (count($eventMethods) == 2) {
                return $event->{$eventMethods[0]}()->{$eventMethods[1]}();
            }
        }
        return false;
    }
}