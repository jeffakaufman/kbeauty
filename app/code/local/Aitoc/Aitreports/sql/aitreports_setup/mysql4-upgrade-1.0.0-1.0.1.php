<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$profile = Mage::getModel('aitreports/profile');
$default_name = 'Profit Report.';
$profile->load($default_name,'name');
if(!$profile->getId()) {
    $profile->setData(array('store_id' => '0','name' => $default_name,'config' => 'a:19:{s:8:"form_key";s:16:"vVmtV8LhSBNQY638";s:5:"store";s:1:"0";s:14:"parse_xsl_file";a:1:{s:4:"name";s:13:"ordercost.xsl";}s:6:"filter";a:10:{s:11:"orderstatus";s:0:"";s:13:"order_id_from";s:0:"";s:11:"order_id_to";s:0:"";s:16:"customer_id_from";s:0:"";s:14:"customer_id_to";s:0:"";s:15:"product_id_from";s:0:"";s:13:"product_id_to";s:0:"";s:5:"range";s:6:"custom";s:9:"date_from";s:0:"";s:7:"date_to";s:0:"";}s:4:"file";a:3:{s:4:"type";s:4:"file";s:8:"filename";s:0:"";s:4:"path";s:16:"var/smartreports";}s:3:"ftp";a:5:{s:4:"path";s:0:"";s:4:"host";s:0:"";s:4:"user";s:0:"";s:8:"password";s:0:"";s:7:"passive";s:0:"";}s:5:"email";a:3:{s:6:"sendto";s:0:"";s:6:"sender";s:7:"general";s:8:"template";s:19:"aitreports_template";}s:4:"auto";a:1:{s:11:"export_type";s:1:"0";}s:11:"entity_type";a:7:{s:10:"order_item";s:1:"1";s:13:"order_address";s:1:"1";s:13:"order_payment";a:2:{s:13:"order_payment";s:1:"1";s:25:"order_payment_transaction";s:1:"1";}s:19:"order_statushistory";s:1:"1";s:7:"invoice";a:3:{s:7:"invoice";s:1:"1";s:15:"invoice_comment";s:1:"1";s:12:"invoice_item";s:1:"1";}s:8:"shipment";a:4:{s:8:"shipment";s:1:"1";s:16:"shipment_comment";s:1:"1";s:13:"shipment_item";s:1:"1";s:17:"shipment_tracking";s:1:"1";}s:10:"creditmemo";a:3:{s:10:"creditmemo";s:1:"1";s:18:"creditmemo_comment";s:1:"1";s:15:"creditmemo_item";s:1:"1";}}s:11:"order_field";a:146:{s:19:"adjustment_negative";s:1:"1";s:19:"adjustment_positive";s:1:"1";s:16:"applied_rule_ids";s:1:"1";s:24:"base_adjustment_negative";s:1:"1";s:24:"base_adjustment_positive";s:1:"1";s:18:"base_currency_code";s:1:"1";s:23:"base_custbalance_amount";s:1:"1";s:20:"base_discount_amount";s:1:"1";s:22:"base_discount_canceled";s:1:"1";s:22:"base_discount_invoiced";s:1:"1";s:22:"base_discount_refunded";s:1:"1";s:16:"base_grand_total";s:1:"1";s:22:"base_hidden_tax_amount";s:1:"1";s:24:"base_hidden_tax_invoiced";s:1:"1";s:24:"base_hidden_tax_refunded";s:1:"1";s:20:"base_shipping_amount";s:1:"1";s:22:"base_shipping_canceled";s:1:"1";s:29:"base_shipping_discount_amount";s:1:"1";s:29:"base_shipping_hidden_tax_amnt";s:1:"1";s:22:"base_shipping_incl_tax";s:1:"1";s:22:"base_shipping_invoiced";s:1:"1";s:22:"base_shipping_refunded";s:1:"1";s:24:"base_shipping_tax_amount";s:1:"1";s:26:"base_shipping_tax_refunded";s:1:"1";s:13:"base_subtotal";s:1:"1";s:22:"base_subtotal_canceled";s:1:"1";s:22:"base_subtotal_incl_tax";s:1:"1";s:22:"base_subtotal_invoiced";s:1:"1";s:22:"base_subtotal_refunded";s:1:"1";s:15:"base_tax_amount";s:1:"1";s:17:"base_tax_canceled";s:1:"1";s:17:"base_tax_invoiced";s:1:"1";s:17:"base_tax_refunded";s:1:"1";s:19:"base_to_global_rate";s:1:"1";s:18:"base_to_order_rate";s:1:"1";s:19:"base_total_canceled";s:1:"1";s:14:"base_total_due";s:1:"1";s:19:"base_total_invoiced";s:1:"1";s:24:"base_total_invoiced_cost";s:1:"1";s:27:"base_total_offline_refunded";s:1:"1";s:26:"base_total_online_refunded";s:1:"1";s:15:"base_total_paid";s:1:"1";s:22:"base_total_qty_ordered";s:1:"1";s:19:"base_total_refunded";s:1:"1";s:18:"billing_address_id";s:1:"1";s:18:"can_ship_partially";s:1:"1";s:23:"can_ship_partially_item";s:1:"1";s:11:"coupon_code";s:1:"1";s:16:"coupon_rule_name";s:1:"1";s:10:"created_at";s:1:"1";s:16:"currency_base_id";s:1:"1";s:13:"currency_code";s:1:"1";s:13:"currency_rate";s:1:"1";s:18:"custbalance_amount";s:1:"1";s:12:"customer_dob";s:1:"1";s:14:"customer_email";s:1:"1";s:18:"customer_firstname";s:1:"1";s:15:"customer_gender";s:1:"1";s:17:"customer_group_id";s:1:"1";s:11:"customer_id";s:1:"1";s:17:"customer_is_guest";s:1:"1";s:17:"customer_lastname";s:1:"1";s:19:"customer_middlename";s:1:"1";s:13:"customer_note";s:1:"1";s:20:"customer_note_notify";s:1:"1";s:15:"customer_prefix";s:1:"1";s:15:"customer_suffix";s:1:"1";s:15:"customer_taxvat";s:1:"1";s:15:"discount_amount";s:1:"1";s:17:"discount_canceled";s:1:"1";s:20:"discount_description";s:1:"1";s:17:"discount_invoiced";s:1:"1";s:17:"discount_refunded";s:1:"1";s:14:"edit_increment";s:1:"1";s:10:"email_sent";s:1:"1";s:9:"entity_id";s:1:"1";s:15:"ext_customer_id";s:1:"1";s:12:"ext_order_id";s:1:"1";s:28:"forced_shipment_with_invoice";s:1:"1";s:15:"gift_message_id";s:1:"1";s:20:"global_currency_code";s:1:"1";s:11:"grand_total";s:1:"1";s:17:"hidden_tax_amount";s:1:"1";s:19:"hidden_tax_invoiced";s:1:"1";s:19:"hidden_tax_refunded";s:1:"1";s:17:"hold_before_state";s:1:"1";s:18:"hold_before_status";s:1:"1";s:7:"is_hold";s:1:"1";s:16:"is_multi_payment";s:1:"1";s:10:"is_virtual";s:1:"1";s:19:"order_currency_code";s:1:"1";s:21:"original_increment_id";s:1:"1";s:23:"payment_auth_expiration";s:1:"1";s:28:"payment_authorization_amount";s:1:"1";s:28:"paypal_ipn_customer_notified";s:1:"1";s:12:"protect_code";s:1:"1";s:16:"quote_address_id";s:1:"1";s:8:"quote_id";s:1:"1";s:13:"real_order_id";s:1:"1";s:17:"relation_child_id";s:1:"1";s:22:"relation_child_real_id";s:1:"1";s:18:"relation_parent_id";s:1:"1";s:23:"relation_parent_real_id";s:1:"1";s:9:"remote_ip";s:1:"1";s:19:"shipping_address_id";s:1:"1";s:15:"shipping_amount";s:1:"1";s:17:"shipping_canceled";s:1:"1";s:20:"shipping_description";s:1:"1";s:24:"shipping_discount_amount";s:1:"1";s:26:"shipping_hidden_tax_amount";s:1:"1";s:17:"shipping_incl_tax";s:1:"1";s:17:"shipping_invoiced";s:1:"1";s:15:"shipping_method";s:1:"1";s:17:"shipping_refunded";s:1:"1";s:19:"shipping_tax_amount";s:1:"1";s:21:"shipping_tax_refunded";s:1:"1";s:5:"state";s:1:"1";s:6:"status";s:1:"1";s:19:"store_currency_code";s:1:"1";s:8:"store_id";s:1:"1";s:10:"store_name";s:1:"1";s:18:"store_to_base_rate";s:1:"1";s:19:"store_to_order_rate";s:1:"1";s:8:"subtotal";s:1:"1";s:17:"subtotal_canceled";s:1:"1";s:17:"subtotal_incl_tax";s:1:"1";s:17:"subtotal_invoiced";s:1:"1";s:17:"subtotal_refunded";s:1:"1";s:10:"tax_amount";s:1:"1";s:12:"tax_canceled";s:1:"1";s:12:"tax_invoiced";s:1:"1";s:11:"tax_percent";s:1:"1";s:12:"tax_refunded";s:1:"1";s:14:"total_canceled";s:1:"1";s:9:"total_due";s:1:"1";s:14:"total_invoiced";s:1:"1";s:16:"total_item_count";s:1:"1";s:22:"total_offline_refunded";s:1:"1";s:21:"total_online_refunded";s:1:"1";s:10:"total_paid";s:1:"1";s:17:"total_qty_ordered";s:1:"1";s:14:"total_refunded";s:1:"1";s:16:"tracking_numbers";s:1:"1";s:10:"updated_at";s:1:"1";s:6:"weight";s:1:"1";s:15:"x_forwarded_for";s:1:"1";}s:4:"page";s:1:"1";s:5:"limit";s:2:"20";s:10:"massaction";s:0:"";s:8:"filename";s:0:"";s:13:"is_ftp_upload";s:0:"";s:8:"is_email";s:0:"";s:7:"is_cron";s:0:"";s:8:"store_id";s:0:"";s:2:"dt";a:3:{s:4:"from";s:0:"";s:2:"to";s:0:"";s:6:"locale";s:5:"en_AU";}}','xsl' => '<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
         xmlns:exsl="http://exslt.org/common"
        exclude-result-prefixes="exsl">
 <xsl:output  encoding="UTF-8" omit-xml-declaration="yes" indent="yes" method="html" media-type="text/html"/>


<xsl:key name="kStmtByKeys" match="order"
      use="concat(generate-id(..), fields/increment_id)"/>



 <xsl:key name="kStmtByKeys" match="items/item"
      use="concat(generate-id(../../..), sku)"/>

 <xsl:template match="node()|@*">
    <xsl:apply-templates select="node()|@*"/>
 </xsl:template>

<xsl:template match="orders">
<xsl:variable name="report_range">
<h3><xsl:text>Profit report per order.</xsl:text></h3><hr /><b style="color: #333333;
    font:normal Tahoma,sans-serif,Verdana;">
<xsl:if test="/orders/@date_from or /orders/@date_to">
<xsl:text>Report range:  </xsl:text> 
<xsl:if test="/orders/@date_from">
<xsl:text> from : </xsl:text><xsl:value-of select="/orders/@date_from" disable-output-escaping="yes"/>
</xsl:if>
<xsl:if test="/orders/@date_to">
<xsl:text> to : </xsl:text><xsl:value-of select="/orders/@date_to" disable-output-escaping="yes"/>
</xsl:if>
</xsl:if>
<xsl:if test="not(/orders/@date_from) and not(/orders/@date_to)">
<xsl:text>Report range: all time</xsl:text>
</xsl:if>
</b>
</xsl:variable>

<xsl:variable name="apos"><xsl:text>\'</xsl:text></xsl:variable>
<xsl:variable name="double_quote"><xsl:text>`</xsl:text></xsl:variable>



<xsl:variable name="currentProductCurrency">
<xsl:if test="normalize-space(order[1]/fields/order_currency_code)=\'USD\'">
<xsl:text><![CDATA[$]]></xsl:text>
</xsl:if>
<xsl:if test="normalize-space(order[1]/fields/order_currency_code)=\'GBP\'">
<xsl:text disable-output-escaping="no"><![CDATA[\\xA3]]></xsl:text>
</xsl:if>
<xsl:if test="normalize-space(order[1]/fields/order_currency_code)=\'EUR\'">
<xsl:text disable-output-escaping="no"><![CDATA[\\u20AC]]></xsl:text>
</xsl:if>
<xsl:if test="not(normalize-space(order[1]/fields/order_currency_code)=\'EUR\') and not(normalize-space(order[1]/fields/order_currency_code)=\'USD\') and not(normalize-space(order[1]/fields/order_currency_code)=\'GBP\')">
<xsl:value-of select="fields/order_currency_code" />
</xsl:if>
</xsl:variable>


<xsl:variable name="orderList">
    <xsl:for-each select=
     "order[generate-id()
          =
           generate-id(key(\'kStmtByKeys\',
                           concat(generate-id(..), fields/increment_id)
                           )[1]
                       )
           ]
     ">
        <xsl:variable name="vkeyGroup" select=
        "key(\'kStmtByKeys\', concat(generate-id(..), fields/increment_id))"/>
<xsl:if test="$vkeyGroup/fields/base_total_invoiced > 0">
        <order>
            <fields>
                <increment_id><xsl:value-of select="$vkeyGroup/fields/increment_id" /></increment_id>
                <base_discount><xsl:value-of select="normalize-space($vkeyGroup/fields/base_discount_invoiced)" /></base_discount>
                <base_discount_invoiced><xsl:value-of select="normalize-space($vkeyGroup/fields/base_discount_invoiced)"/></base_discount_invoiced>
                <base_tax_invoiced><xsl:value-of select="normalize-space($vkeyGroup/fields/base_tax_invoiced)" disable-output-escaping="yes"/></base_tax_invoiced>
                <base_total_invoiced_cost>0<xsl:value-of select="normalize-space($vkeyGroup/fields/base_total_invoiced_cost[format-number(number(),\'#\')!=\'NaN\'])" disable-output-escaping="yes"/></base_total_invoiced_cost>
                <base_total_invoiced><xsl:value-of select="normalize-space($vkeyGroup/fields/base_total_invoiced)" disable-output-escaping="yes"/></base_total_invoiced>
                <base_shipping_invoiced>0<xsl:value-of select="normalize-space($vkeyGroup/fields/base_shipping_invoiced)" disable-output-escaping="yes"/></base_shipping_invoiced>
                <base_subtotal_invoiced><xsl:value-of select="normalize-space($vkeyGroup/fields/base_subtotal_invoiced[format-number(number(),\'#\')!=\'NaN\'])" disable-output-escaping="yes"/></base_subtotal_invoiced>
                <base_subtotal_canceled>0<xsl:value-of select="normalize-space($vkeyGroup/fields/base_subtotal_canceled[format-number(number(),\'#\')!=\'NaN\'])" disable-output-escaping="yes"/></base_subtotal_canceled>

            </fields>
            <items>
            <xsl:for-each select="$vkeyGroup/items/item">
                <item>
                <sku><xsl:value-of select="sku" disable-output-escaping="yes"/></sku>
                <name><xsl:value-of select="name" disable-output-escaping="yes"/></name>
                
                <qty_invoiced><xsl:value-of select="qty_invoiced" disable-output-escaping="yes"/></qty_invoiced>
                <base_cost>0<xsl:value-of select="base_cost" disable-output-escaping="yes"/></base_cost>
                <base_cost_amount><xsl:value-of select="base_cost * qty_invoiced" disable-output-escaping="yes"/></base_cost_amount>
                <base_price><xsl:value-of select="base_price" disable-output-escaping="yes"/></base_price>
                <base_price_amount><xsl:value-of select="base_price * qty_invoiced" disable-output-escaping="yes"/></base_price_amount>
                <base_discount_amount><xsl:value-of select="base_discount_amount" disable-output-escaping="yes"/></base_discount_amount>
                <base_row_total><xsl:value-of select="base_row_total" disable-output-escaping="yes"/></base_row_total>
                <base_tax_invoiced><xsl:value-of select="base_tax_invoiced" disable-output-escaping="yes"/></base_tax_invoiced>
                </item>
            </xsl:for-each>
            </items>
        </order>
        </xsl:if>
    </xsl:for-each>
</xsl:variable>

<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>
      <xsl:text>Profit Report</xsl:text>
    </title>
    <script type="text/javascript" src="http://www.google.com/jsapi"><xsl:text><![CDATA[ ]]></xsl:text></script>
    <script type="text/javascript">
      <xsl:text>google.load(\'visualization\', \'1\', {packages: [\'controls\',\'table\',\'corechart\']});</xsl:text>
    </script>
    <script type="text/javascript">
      <xsl:text>function drawVisualization() {
        // Prepare the data.
        //var data = google.visualization.arrayToDataTable([
        var dataTable = new google.visualization.DataTable();        
        </xsl:text>    
        
        <xsl:text>
        dataTable.addColumn(\'number\', \'Order ID\');
        dataTable.addColumn(\'number\', \'Cost amount\');
        dataTable.addColumn(\'string\', \'Cost description\');
        dataTable.addColumn(\'number\', \'Items amount\');
        dataTable.addColumn(\'number\', \'Discount for items\');
        dataTable.addColumn(\'number\', \'Total discount\');
        dataTable.addColumn(\'number\', \'Tax amount\');
        dataTable.addColumn(\'number\', \'Shipment amount\');
        dataTable.addColumn(\'number\', \'Total amount\');
        dataTable.addColumn(\'number\', \'Profit\');
        dataTable.addRows([</xsl:text>
        <xsl:for-each select="exsl:node-set($orderList)/order">
<!--        <xsl:copy-of select="current()"/>-->
        <xsl:if test="position()&gt;1"><xsl:text>,</xsl:text></xsl:if>
       <xsl:text>[</xsl:text>
       <xsl:value-of select="normalize-space(fields/increment_id)" disable-output-escaping="yes"/><xsl:text>,</xsl:text>
       <xsl:value-of select="sum(items/item/base_cost_amount[format-number(number(),\'#\')!=\'NaN\'])"/><xsl:text>,</xsl:text>
        <xsl:text>\'</xsl:text>
        <xsl:for-each select="items/item[base_cost>0]"><xsl:value-of select="normalize-space(translate(sku,$apos,$double_quote))" disable-output-escaping="yes"/><xsl:text> : </xsl:text><xsl:value-of select="format-number(normalize-space(base_cost),\'#.00\')" disable-output-escaping="yes"/><xsl:text> x </xsl:text><xsl:value-of select="format-number(normalize-space(qty_invoiced),\'#\')" disable-output-escaping="yes"/><xsl:text>&lt;br /&gt;\\u000A</xsl:text></xsl:for-each>
        <xsl:text>\',</xsl:text>
       <xsl:value-of select="sum(items/item/base_price_amount)"/><xsl:text>,</xsl:text>
       <xsl:value-of select="sum(items/item/base_discount_amount)"/><xsl:text>,</xsl:text>
       <xsl:value-of select="format-number(-fields/base_discount_invoiced,\'#.00\')"/><xsl:text>,</xsl:text>
       <xsl:value-of select="format-number(fields/base_tax_invoiced,\'#.00\')"/><xsl:text>,</xsl:text>
       <xsl:value-of select="format-number(fields/base_shipping_invoiced,\'#.00\')"/><xsl:text>,</xsl:text>
       <xsl:value-of select="sum(items/item/base_row_total[format-number(number(),\'#\')!=\'NaN\'])-sum(items/item/base_discount_amount[format-number(number(),\'#\')!=\'NaN\'])+sum(items/item/base_tax_amount[format-number(number(),\'#\')!=\'NaN\'])"/><xsl:text>,</xsl:text>
<!--
       <xsl:text>{v:\'</xsl:text><xsl:value-of select="sum(items/item/base_row_total[format-number(number(),\'#\')!=\'NaN\'])-sum(items/item/base_discount_amount[format-number(number(),\'#\')!=\'NaN\']) - fields/base_total_invoiced_cost[format-number(number(),\'#\')!=\'NaN\'] - fields/base_subtotal_canceled[format-number(number(),\'#\')!=\'NaN\']"/>
       <xsl:text>\',f:\'</xsl:text><xsl:value-of select="sum(items/item/base_row_total[format-number(number(),\'#\')!=\'NaN\'])"/><xsl:text>-</xsl:text><xsl:value-of select="sum(items/item/base_discount_amount[format-number(number(),\'#\')!=\'NaN\'])"/><xsl:text>-</xsl:text><xsl:value-of select="fields/base_total_invoiced_cost[format-number(number(),\'#\')!=\'NaN\']"/><xsl:text>-</xsl:text><xsl:value-of select="fields/base_subtotal_canceled[format-number(number(),\'#\')!=\'NaN\']"/><xsl:text>\'}</xsl:text>
-->
       <xsl:value-of select="sum(items/item/base_row_total[format-number(number(),\'#\')!=\'NaN\'])-sum(items/item/base_discount_amount[format-number(number(),\'#\')!=\'NaN\']) - fields/base_total_invoiced_cost[format-number(number(),\'#\')!=\'NaN\'] - fields/base_subtotal_canceled[format-number(number(),\'#\')!=\'NaN\']"/>
       <xsl:text>]
    </xsl:text>
       
    </xsl:for-each>
<xsl:text> ]);

          var formatter = new google.visualization.NumberFormat(
              {prefix: "</xsl:text><xsl:value-of select="$currentProductCurrency" disable-output-escaping="yes"/><xsl:text>", negativeColor: \'red\', negativeParens: true});
          formatter.format(dataTable, 1); // Apply formatter to second column
          formatter.format(dataTable, 2); // Apply formatter to second column
          formatter.format(dataTable, 3); // Apply formatter to second column
          formatter.format(dataTable, 4); // Apply formatter to second column
          formatter.format(dataTable, 5); // Apply formatter to second column
          formatter.format(dataTable, 6); // Apply formatter to second column
          formatter.format(dataTable, 7); // Apply formatter to second column
          formatter.format(dataTable, 8); // Apply formatter to second column
          formatter.format(dataTable, 9); // Apply formatter to second column
          
          //var formatter = new google.visualization.TableArrowFormat();
            //formatter.format(data, 4); // Apply formatter to second column

//          dataTable.draw(dataTable, {allowHtml: true, showRowNumber: true});
      
        // Define a StringFilter control for the \'Name\' column
         var options = {
    allowHtml: true
  };
        
        var stringFilter = new google.visualization.ControlWrapper({
          \'controlType\': \'StringFilter\',
          \'containerId\': \'control1\',
          \'options\': {
            \'filterColumnLabel\': \'Cost description\',\'ui\': {\'label\': \'Filter table by SKU\'},\'matchType\':\'any\'
          }
        });
      
        // Define a table visualization
        var table = new google.visualization.ChartWrapper({
          \'chartType\': \'Table\',
          \'containerId\': \'chart1\',
          
          \'options\': {\'allowHtml\': true, \'height\': \'40em\', \'width\': \'75em\'}
          
        });
        //var table = new google.visualization.Table(document.getElementById(\'chart1\'));
        //table.draw(dataTable, {showRowNumber: false, allowHtml: true});
      
        // Create the dashboard.
        var dashboard = new google.visualization.Dashboard(document.getElementById(\'dashboard\')).
          // Configure the string filter to affect the table contents
          bind(stringFilter, table).
          // Draw the dashboard
          draw(dataTable, {allowHtml: true})
          }
      

      google.setOnLoadCallback(drawVisualization);
</xsl:text>
    </script>
  </head>
<body style="font-family: Arial;border: 0 none;">
  <xsl:copy-of select="$report_range"/>
    <div id="dashboard">
      <table>
        <tr style=\'vertical-align: top\'>
          <td style=\'width: 800px; font-size: 0.9em;\'>
            <div id="control1"><xsl:text><![CDATA[ ]]></xsl:text></div>
            <div id="control2"><xsl:text><![CDATA[ ]]></xsl:text></div>
            <div id="control3"><xsl:text><![CDATA[ ]]></xsl:text></div>
          </td>
         </tr>
         <tr>
          <td style=\'width: 800px\'>
            <div style="float: left;" id="chart1"><xsl:text><![CDATA[ ]]></xsl:text></div>
            <div style="float: left;" id="chart2"><xsl:text><![CDATA[ ]]></xsl:text></div>
            <div style="float: left;" id="chart3"><xsl:text><![CDATA[ ]]></xsl:text></div>
          </td>
        </tr>
      </table>
    </div>
  </body>
</html>
</xsl:template>
</xsl:stylesheet>','date' => '2014-01-28 13:46:40','flag_auto' => '0','crondate' => NULL))->save();
}


$installer->endSetup();
