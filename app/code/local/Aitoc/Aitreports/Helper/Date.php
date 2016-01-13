<?php

class Aitoc_Aitreports_Helper_Date extends Mage_Core_Helper_Abstract
{
    protected $_currentDate = null;
    protected $_dateRange = null;
    protected $_format = 'FFF';
    
    public function getCurrentDate() {
        if(is_null($this->_currentDate)) {
            $this->_currentDate = Mage::app()->getLocale()->storeDate();
        }
        return $this->_currentDate;        
    }

    /**
     * @param $key
     * @param string $format
     * @return array
     */
    public function getDateRange($key, $format = Zend_Date::DATE_MEDIUM)
    {
        $this->_format = $format;
        $method = 'get'.ucfirst($key);
        if(method_exists($this,$method)) {
            return $this->$method();
        }
        return $this->getToday();        
    }
    
    /**
     * @return array
     */
    public function getToday()
    {
        $today = $this->getCurrentDate()->toString($this->_format);
        return array(
            'name'  => $this->__('Today'),
            'start' => $today,
            'end'   => $today
        );        
    }
    
    /**
     * @return array
     */
    public function getYesterday()
    {
        $yesterday = clone $this->getCurrentDate();
        $yesterday->subDate('0-0-1','YYYY-MM-dd');
        $yesterday = $yesterday->toString($this->_format);
        return array(
            'name'  => $this->__('Yesterday'),
            'start' => $yesterday,
            'end'   => $yesterday
        );        
    }
    
    /**
     * @return array
     */
    public function getLast7days()
    {
        $last_7days = clone $this->getCurrentDate();
        $last_7days ->subDate('0-0-7','YYYY-MM-dd');
        $yesterday = clone $this->getCurrentDate();
        $yesterday->subDate('0-0-1','YYYY-MM-dd');
        $last_7days = $last_7days->toString($this->_format);
        $yesterday = $yesterday->toString($this->_format);
        return array(
            'name'  => $this->__('Last 7 days'),
            'start' => $last_7days,
            'end'   => $yesterday
        );        
    }
    
    /**
     * @return array
     */
    public function getCurrentMonth()
    {
        $month_start = clone $this->getCurrentDate();
        $month_start->subDate('0-0-'.($this->getCurrentDate()->get('dd')-1),'YYYY-MM-dd');
        $today = $this->getCurrentDate()->toString($this->_format);
        $month_start = $month_start->toString($this->_format);
        return array(
            'name'  => $this->__('Current Month'),
            'start' => $month_start,
            'end'   => $today
        );        
    }
    
    /**
     * @return array
     */
    public function getLastMonth()
    {
        $last_month = clone $this->getCurrentDate();
        $last_month_end = clone $this->getCurrentDate();
        $last_month ->subDate('0-1-'.($this->getCurrentDate()->get('dd')-1),'YYYY-MM-dd');
        $last_month_end->subDate('0-0-'.($this->getCurrentDate()->get('dd')),'YYYY-MM-dd');         
        $last_month = $last_month->toString($this->_format);
        $last_month_end = $last_month_end->toString($this->_format);
        return array(
            'name'  => $this->__('Last Month'),
            'start' => $last_month,
            'end'   => $last_month_end,
        );        
    }
    
    /**
     * @return array
     */
    public function getDateRangeList() {
        if(is_null($this->_dateRange)) {
            $this->_dateRange = array(
                'today'=> $this->getToday(),
                'yesterday' => $this->getYesterday(),
                'last7days' => $this->getLast7days(),
                'currentMonth' => $this->getCurrentMonth(),
                'lastMonth' => $this->getLastMonth()
            );
        }
        return $this->_dateRange;
    }

    public function convertDateToCorrectFormat($post_date, $date_format=null){
        if($post_date){
            $current_locale = Mage::app()->getLocale()->getLocaleCode();
            $locale = new Zend_Locale($current_locale);
            $converted_date = new Zend_Date($post_date, false ,$locale);
            return $converted_date->toString("MM/dd/yyyy");
        }else{
            return $post_date;
        }
    }
}
