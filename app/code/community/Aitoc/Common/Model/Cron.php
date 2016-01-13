<?php
/**
 * This class should NOT be modified under any circumstances except major bug fixes
 *
 * @copyright  Copyright (c) 2009 AITOC, Inc.
 */
class Aitoc_Common_Model_Cron extends Mage_Core_Model_Abstract
{
    CONST NO_LOGS           = 'There are no cron logs in magento database.';
    CONST BIG_DELAY         = 'Cron hasn\'t been executed for more than %s.';

    /**
     * @var string
     */
    protected $_lastDate = false;

    /**
     * @var type
     */
    protected $_errorMessage = null;

    /**
     * @var string
     */
    protected $_updateString = '';

    /**
     * Select last executed_at date from crom_scheldure table, validate if it were executed lonag ago and show error about this.
     *
     * @param int $max_delta
     * @param boolean $show_error
     *
     * @return Aitoc_Common_Model_Cron
     */
    public function validateCronDate($max_delta = 3600)
    {
        $last_executed = $this->_getLastCronDate();
        if($last_executed == '') {
            $this->_setError(self::NO_LOGS);
            return $this;
        }
        $last_executed = strtotime($last_executed);
        $now = time();
        if($last_executed < $now - $max_delta) {
            $this->_setError(self::BIG_DELAY);
            $delta = $this->_convertTimeToString($now - $last_executed);
            $this->_setUpdateText( $delta );
        }
        return $this;
    }

    /**
     * Setup error text to show to user
     *
     * @param string $message
     *
     * @return Aitoc_Common_Model_Cron
     */
    protected function _setError($message)
    {
        $this->_errorMessage = $message;
        return $this;
    }

    /**
     * Setup text that will be replaced in error text, if required
     *
     * @param mixed $text
     *
     * @return Aitoc_Common_Model_Cron
     */
    protected function _setUpdateText($text)
    {
        $this->_updateString = $text;
        return $this;
    }

    /**
     * Return if error with cron
     *
     * @return boolean
     */
    public function isWorking()
    {
        return is_null($this->_errorMessage);
    }

    /**
     * Return a generated error string, if cron were validated
     *
     * @param string
     */
    public function getError()
    {
        if($this->isWorking()) {
            //cron were mot validated or there is no error
            return '';
        }
        $message = Mage::helper('aitoc_common')->__($this->_errorMessage, $this->_updateString);
        $message .= '<br />'.Mage::helper('aitoc_common')->__('Some module function may not work properly if cron hasn\'t been setup and is not working properly.');
        $message .= '<br />'.Mage::helper('aitoc_common')->__('For proper cron setup please reffer to the <a href="%s" target="_blank">magento official guide</a>.', 'http://www.magentocommerce.com/wiki/1_-_installation_and_configuration/how_to_setup_a_cron_job');
        return $message;

    }

    /**
     * Output error text to adminhtml session
     *
     * @return Aitoc_Common_Model_Cron
     */
    public function showError()
    {
        if(!$this->isWorking()) {
            $session = Mage::getSingleton('adminhtml/session');
            $session->addError($this->getError());
        }
        return $this;
    }

    /**
     * Convert int time to to srting interval, showing how long ago it had happaned
     *
     * @param int $time
     *
     * @return string
     */
    protected function _convertTimeToString($time)
    {
        $time_intervals = array(
            86400   => 'day(s)',
            3600    =>'hour(s)',
            60      =>'minute(s)'
        );
        $result = array();
        foreach($time_intervals as $amount => $name) {
            if($time < $amount) {
                continue;
            }
            $num = floor($time / $amount);
            $result[] = $num.' '.$name;
            $time = $time - $num * $amount;
        }
        return implode(' ', $result);
    }

    /**
     * Select last date when magento cron has been executed
     *
     * @return string
     */
    protected function _getLastCronDate()
    {
        if($this->_lastDate === false) {
            $collection = Mage::getModel('cron/schedule')->getCollection();
            $collection->getSelect()
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns(array(
                    'executed_last' => 'max(executed_at)'
                ));
            $this->_lastDate = $collection->getFirstItem()->getExecutedLast();
        }
        return $this->_lastDate;
    }

}
