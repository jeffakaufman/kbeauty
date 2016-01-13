<?php

/**
 * Created by PhpStorm.
 * User: aleksey
 * Date: 05/01/16
 * Time: 14:10
 */
//die();
class Valkyrie_Catimage_Block_Adminhtml_Helper_Color extends Varien_Data_Form_Element_Text
{
    public function getElementHtml() {
    $html = parent::getElementHtml();
    $html .= "<script type=\"text/javascript\">
            (function() {

                function addNewStyle(newStyle) {
                    var styleElement = document.getElementById('styles_js');
                    if (!styleElement) {
                        styleElement = document.createElement('style');
                        styleElement.type = 'text/css';
                        styleElement.id = 'styles_js';
                        document.getElementsByTagName('head')[0].appendChild(styleElement);
                    }
                    styleElement.appendChild(document.createTextNode(newStyle));
                }


                var _inpId = '".$this->getHtmlId()."';
                var _inpEl = $(_inpId);
                _inpEl.color = new jscolor.color(_inpEl);
//console.log($(_inpEl));
                addNewStyle('#'+_inpId+' {width:50px !important;}')
            })();
        </script>";
    return $html;
}

//    public function getAfterElementHtml()
//    {
//        $html = parent::getAfterElementHtml();
//        return $html."  <script>
//        				$('".$this->getHtmlId()."').disable();
//        				</script>";
//    }
}