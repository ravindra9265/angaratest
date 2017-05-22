<?php
class Runa_Promotions_Block_Track extends Mage_Core_Block_Abstract
{
    /**
     * This html is supposed to appear at the bottom of every page as close to the end of <body></body> as possible
     * 
     * @see app/code/core/Mage/Core/Block/Mage_Core_Block_Abstract#_toHtml()
     */
    public function _toHtml()
    {
        Mage::getSingleton('runapromotions/log_debug')->info('Runa_Promotions_Block_Track::_toHtml() called');
        return $this->_getPageTypeSpan() . $this->_getTrackingJS();
    }
    
    /**
     * Return the page type span with the merchant type set as <MODULE>/<CONTROLLER>/<ACTION>
     * 
     * @return string
     */
    protected function _getPageTypeSpan()
    {
        $_helper = mage::helper('runapromotions');
        /*@var $_helper Runa_Promotions_Helper_Data*/
        $merchant_page_type = $_helper->getRouteKey();
        
        return "<span id=\"runa_raw_page_type\" type=\"$merchant_page_type\"/>";
    }
    
    /**
     * Return the tracker javascript with all merchant-specific variables filled in
     * 
     * @return string
     */
    protected function _getTrackingJS()
    {
        $config = Mage::getSingleton('runapromotions/config_settings');
        $merchant_id = $config->getMerchantId();
        $merchant_type = 'magento';
        $merchant_vertical = 'etailer';
        $merchant_host = Mage::getBaseUrl();
        $runa_base_url = '//' . $config->getServiceDomain();
        $runa_client_version = $config->getRunaModuleInfo()->version;
        $merchant_cart_url = $this->helper('checkout/cart')->getCartUrl();
        return "   
            <!-- Runa Tracking Implementation -->
            <script language=\"JavaScript\" type=\"text/javascript\">
                //<![CDATA[
                var merchant_config = {
                    merchant_id: \"$merchant_id\",
                    merchant_type: \"$merchant_type\",
                    merchant_vertical: \"$merchant_vertical\",
                    merchant_host: \"$merchant_host\",
                    runa_base_url: \"$runa_base_url\",
                    runa_client_version: \"$runa_client_version\",
                    merchant_cart_url: \"$merchant_cart_url\",
                    runa_cid: \"\",
                    site_cid: \"\"
                };
                var runa_all_js = merchant_config.runa_base_url + \"/client/boot/etailer/\" +
                merchant_config.merchant_type + \"/\" + merchant_config.merchant_id ;
                function start_runa_client(){
                    var element = document.createElement(\"script\");
                    element.src = runa_all_js;
                    element.type = \"text/javascript\";
                    document.body.appendChild(element);
                }
                (function(i) {
                    var u = navigator.userAgent.toLowerCase();
                    var ie = /*@cc_on!@*/false;
                    if (/webkit/.test(u)) {
                        // safari
                        timeout = setTimeout(function(){
                            if ( document.readyState == \"loaded\" || document.readyState == \"complete\" ) {
                                i();
                            } else {
                                setTimeout(arguments.callee,10);
                            }
                        }, 10);
                    } else if ((/mozilla/.test(u) && !/(compatible)/.test(u)) || (/opera/.test(u))) {
                        // opera/moz
                        document.addEventListener(\"DOMContentLoaded\",i,false);
                    } else if (ie) {
                        // IE
                        (function (){
                            var tempNode = document.createElement(\"document:ready\");
                            try {
                                tempNode.doScroll(\"left\");
                                i();
                                tempNode = null;
                            } catch(e) {
                                setTimeout(arguments.callee, 0);
                            }
                        })();
                    } else {
                        window.onload = i;
                    }
                })(start_runa_client);
                //]]>
            </script>
        ";
    }
}