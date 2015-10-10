<?php

class FME_Richsnippets_Helper_Data extends Mage_Core_Helper_Abstract
{
const IS_EXT_ENABLE = 'richsnippets_options/general_info/module_enable';

const PRODUCT_NAME = 'richsnippets_options/settings/set_name';

const BREADCRUMBS = 'richsnippets_options/settings/set_breadcrumbs';

const PRODUCT_SKU = 'richsnippets_options/settings/product_sku';

const PRODUCT_URL = 'richsnippets_options/settings/set_url';

const SET_IMAGE   = 'richsnippets_options/settings/set_image';

const DESCRIPTION = 'richsnippets_options/settings/set_desc';

const PRICE = 'richsnippets_options/settings/set_price';

const PRICE_CURRENCEY = 'richsnippets_options/settings/set_pricecurrencey';

// ////////////////////////////////////////////////////////////////////
const PRODUCT_STATUS = 'richsnippets_options/settings/product_status';

const BRAND = 'richsnippets_options/settings/p_brand';

const COLOR = 'richsnippets_options/settings/p_color';

const WEIGHT = 'richsnippets_options/settings/p_weight';


const BRAND_ATT = 'richsnippets_options/settings/p_brand_att';

const COLOR_ATT = 'richsnippets_options/settings/p_coloratt';

const WEIGHT_ATT = 'richsnippets_options/settings/p_weightatt';
//////////////////////////////////////////////////////////////////////

const REVIEW = 'richsnippets_options/settings/p_review';

const SCHEMA_URL = 'richsnippets_options/general_info/schema_url';

const SCHEMA_OFFERURL = 'richsnippets_options/general_info/schema_offerurl';

const BREADCRUMB_URL = 'richsnippets_options/general_info/breadcrumb_url';



	public function isEnableExtension()
	    {
	        return Mage::getStoreConfig(self::IS_EXT_ENABLE, Mage::app()->getStore()->getId());
	    }
	public function BreadcrumbEnable()
	    {
	        return Mage::getStoreConfig(self::BREADCRUMBS, Mage::app()->getStore()->getId());
	    }
	public function nameEnable()
	    {
	        return Mage::getStoreConfig(self::PRODUCT_NAME, Mage::app()->getStore()->getId());
	    }
	public function SkuEnable()
	    {
	        return Mage::getStoreConfig(self::PRODUCT_SKU, Mage::app()->getStore()->getId());
	    }
	public function UrlEnable()
	    {
	        return Mage::getStoreConfig(self::PRODUCT_URL, Mage::app()->getStore()->getId());
	    }
	public function ImageEnable()
	    {
	        return Mage::getStoreConfig(self::SET_IMAGE, Mage::app()->getStore()->getId());
	    }
	public function DescEnable()
	    {
	        return Mage::getStoreConfig(self::DESCRIPTION, Mage::app()->getStore()->getId());
	    }
	public function PriceEnable()
	    {
	        return Mage::getStoreConfig(self::PRICE, Mage::app()->getStore()->getId());
	    }

	public function PriceCurrencyEnable()
	    {
	        return Mage::getStoreConfig(self::PRICE_CURRENCEY, Mage::app()->getStore()->getId());
	    }

	public function StatusEnable()
	    {
	        return Mage::getStoreConfig(self::PRODUCT_STATUS, Mage::app()->getStore()->getId());
	    }
/////////////////////////////////////////////////
	public function BrandEnable()
	    {
	        return Mage::getStoreConfig(self::BRAND, Mage::app()->getStore()->getId());
	    }
	public function ColorEnable()
	    {
	        return Mage::getStoreConfig(self::COLOR, Mage::app()->getStore()->getId());
	    }
	public function WeightEnable()
	    {
	        return Mage::getStoreConfig(self::WEIGHT, Mage::app()->getStore()->getId());
	    }
	    public function SetBrandAtt()
	    {
	        return Mage::getStoreConfig(self::BRAND_ATT, Mage::app()->getStore()->getId());
	    }
	public function SetColorAtt()
	    {
	        return Mage::getStoreConfig(self::COLOR_ATT, Mage::app()->getStore()->getId());
	    }
	public function SetWeightAtt()
	    {
	        return Mage::getStoreConfig(self::WEIGHT_ATT, Mage::app()->getStore()->getId());
	    }

////////////////////////////////////////////////
	public function ReviewEnable()
	    {
	        return Mage::getStoreConfig(self::REVIEW, Mage::app()->getStore()->getId());
	    }

    // public function SchemaUrl()
	   //  {
	   //      return Mage::getStoreConfig(self::SCHEMA_URL, Mage::app()->getStore()->getId());
	   //  }
	// public function SchemaOfferUrl()
	//     {
	//         return Mage::getStoreConfig(self::SCHEMA_OFFERURL, Mage::app()->getStore()->getId());
	//     }
	// public function BreadcrumbUrl()
	//     {
	//         return Mage::getStoreConfig(self::BREADCRUMB_URL, Mage::app()->getStore()->getId());
	//     }
  
    public function getbySchemaUrl()
       {

       	if ($this->isEnableExtension()==1){
       	   
       	    	//$url = 'itemscope '.' itemtype='.$this->SchemaUrl();
       		$url = 'itemscope  itemtype="http://schema.org/Product"';
       		
       	    	return $url; 
       	    	
       	    }
       	}
       
    public function getbyName()
       {

       	if ($this->isEnableExtension()==1){
       	    if ($this->nameEnable()==1) {

       	    	$name =  'itemprop="name"';
       	    	return $name; 
       	    	
       	    }
       	}
       
       }
    public function getbyDescriptions()
       {

       	if ($this->isEnableExtension()==1){
       	    if ($this->DescEnable()==1) {

       	    	$desc =  'itemprop="description"';
       	    	return $desc; 
       	    	
       	    }
       	}
       
       }
     public function getbyImage()
       {

       	if ($this->isEnableExtension()==1){
       	    if ($this->ImageEnable()==1) {

       	    	return 'itemprop="image"'; 
       	    	
       	    }
       	}
       
       }


    public function getByoffer(){
		  	if ($this->isEnableExtension()==1){
		       	   
		       	 return 'itemprop="offers" itemscope itemtype="http://schema.org/Offer"'; 
		       	    	 
		       	}

		  }
	public function getByCurrencey(){
		  	if ($this->isEnableExtension()==1){
		      if ($this->PriceCurrencyEnable()==1) {
		       	
		       	return '<meta itemprop=priceCurrency content="'.Mage::app()->getStore()->getCurrentCurrencyCode().'" />'; 
		       	    	 
		       	}
		       }

		  }
    public function getByStatusInstock(){
		  	if ($this->isEnableExtension()==1){
		      if ($this->StatusEnable()==1) {
		       	return '<link itemprop="availability" href="http://schema.org/InStock">'; 
		       	    	 
		       	}
		       }

		  } 
    public function getByStatusOutstock(){
		  	if ($this->isEnableExtension()==1){
		      if ($this->StatusEnable()==1) {
		       	return '<link itemprop="availability" href="http://schema.org/OutOfStock">'; 
		       	    	 
		       	}
		       }

		  } 
	  public function getByPrice(){
		  	if ($this->isEnableExtension()==1){
		      if ($this->PriceEnable()==1) {
		       	return true; 
		       	    	 
		       	}else{return false;}
		       }else{return false;}

		  }

    public function getbysku(){

    	if ($this->isEnableExtension()==1){
		      if ($this->SkuEnable()==1) {
		       	
		       	return true; 
		       	    	 
		       	}else{return false;}
		       }else{return false;}
    }
   public function getbyUrl(){

    	if ($this->isEnableExtension()==1){
		      if ($this->UrlEnable()==1) {
		       	
		       	return true; 
		       	    	 
		       	}else{return false;}
		       }else{return false;}
    }

   public function getbyreview(){

   	if ($this->isEnableExtension()==1){
		      if ($this->ReviewEnable()==1) {
		       	return 'aggregate'; 
		       	    	 
		       	}else{return 'false'; }
		       }

       }
    public function Enablereview(){

   	if ($this->isEnableExtension()==1){
		      if ($this->ReviewEnable()==1) {
		       	return true; 
		       	    	 
		       	}else{return false; }
		       }

       }
public function getbyBreadcrumbsUrl()
       {

       	if ($this->isEnableExtension()==1){
       	  if ($this->BreadcrumbEnable()==1) {
       	    	//$url = 'itemscope '.' itemtype='.$this->BreadcrumbUrl();
       	  	$url = 'itemscope  itemtype="http://data-vocabulary.org/Breadcrumb"';
       	    	
       	    	return $url; 
       	       }
       	    	
       	    	
       	    }
       	}


    public function getEnaglebyBreadcrumbsUrl()
       {

       	if ($this->isEnableExtension()==1){
       	  if ($this->BreadcrumbEnable()==1) {
       	    	
       	    	return true; 
       	       }else{return false;}
       	    	
       	    	
       	    }else{return false;}
       	}
public function getbyBrand()
       {

       	if ($this->isEnableExtension()==1){
       	  if ($this->BrandEnable()==1) {
       	    	
       	    	

       	    	return 'itemprop="'.strtolower($this->SetBrandAtt()).'"'.'itemscope itemtype="http://schema.org/Organization"'; 
       	    	//return $this->SetBrandAtt(); 
       	       }else{return false;}
       	    	
       	    	
       	    }else{return false;}
       	}

public function getbyColor()
       {

       	if ($this->isEnableExtension()==1){
       	  if ($this->ColorEnable()==1) {
       	    	
       	    	return 'itemprop="'.strtolower($this->SetColorAtt()).'"'; 
       	    	//return $this->SetColorAtt(); 
       	       }else{return false;}
       	    	
       	    	
       	    }else{return false;}
       	}

public function getbyWeight()
       {

       	if ($this->isEnableExtension()==1){
       	  if ($this->WeightEnable()==1) {
       	    	
       	    	return 'itemprop="'.strtolower($this->SetWeightAtt()).'"'; 
       	       }else{return false;}
       	    	
       	    	
       	    }else{return false;}
       	}

   

}