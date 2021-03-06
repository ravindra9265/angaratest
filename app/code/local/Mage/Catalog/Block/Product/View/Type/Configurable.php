<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog super product configurable part block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Abstract
{
    /**
     * Prices
     *
     * @var array
     */
    protected $_prices      = array();

    /**
     * Prepared prices
     *
     * @var array
     */
    protected $_resPrices   = array();

    /**
     * Get allowed attributes
     *
     * @return array
     */
    public function getAllowAttributes()
    {
        return $this->getProduct()->getTypeInstance(true)
            ->getConfigurableAttributes($this->getProduct());
    }

    /**
     * Check if allowed attributes have options
     *
     * @return bool
     */
    public function hasOptions()
    {
        $attributes = $this->getAllowAttributes();
        if (count($attributes)) {
            foreach ($attributes as $attribute) {
                /** @var Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute */
                if ($attribute->getData('prices')) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get Allowed Products
     *
     * @return array
     */
    public function getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = array();
            $skipSaleableCheck = Mage::helper('catalog/product')->getSkipSaleableCheck();
            $allProducts = $this->getProduct()->getTypeInstance(true)
                ->getUsedProducts(null, $this->getProduct());
            foreach ($allProducts as $product) {
				//	S:VA	Commented to show out of stock products with all variations
                //if ($product->isSaleable() || $skipSaleableCheck) {
                    $products[] = $product;
               //}
			   //	E:VA
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }

    /**
     * retrieve current store
     *
     * @return Mage_Core_Model_Store
     */
    public function getCurrentStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Returns additional values for js config, con be overriden by descedants
     *
     * @return array
     */
    protected function _getAdditionalConfig()
    {
        return array();
    }

    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig()
    {
		if(Mage::registry('current_json_config')){
			return Mage::registry('current_json_config');
		}
		
        $attributes = array();
        $options    = array();
        $store      = $this->getCurrentStore();
        $taxHelper  = Mage::helper('tax');
        $currentProduct = $this->getProduct();

        $preconfiguredFlag = $currentProduct->hasPreconfiguredValues();
        if ($preconfiguredFlag) {
            $preconfiguredValues = $currentProduct->getPreconfiguredValues();
            $defaultValues       = array();
        }

        foreach ($this->getAllowProducts() as $product) {
            $productId  = $product->getId();

            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute   = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue     = $product->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttributeId])) {
                    $options[$productAttributeId] = array();
                }

                if (!isset($options[$productAttributeId][$attributeValue])) {
                    $options[$productAttributeId][$attributeValue] = array();
                }
                $options[$productAttributeId][$attributeValue][] = $productId;
            }
        }

        $this->_resPrices = array(
            $this->_preparePrice($currentProduct->getFinalPrice())
        );

        foreach ($this->getAllowAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
			$defaultValue = $this->getDefaultAttributeValue($productAttribute->getAttributeCode());
            $info = array(
               'id'				=> $productAttribute->getId(),
               'code'			=> $productAttribute->getAttributeCode(),
               'label'			=> $attribute->getLabel(),
               'options'		=> array(),
			   'defaultOption'	=> ''
            );

            $optionPrices = array();
            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if(!$this->_validateAttributeValue($attributeId, $value, $options)) {
                        continue;
                    }
                    $currentProduct->setConfigurablePrice(
                        $this->_preparePrice($value['pricing_value'], $value['is_percent'])
                    );
                    $currentProduct->setParentId(true);
                    Mage::dispatchEvent(
                        'catalog_product_type_configurable_price',
                        array('product' => $currentProduct)
                    );
                    $configurablePrice = $currentProduct->getConfigurablePrice();

                    if (isset($options[$attributeId][$value['value_index']])) {
                        $productsIndex = $options[$attributeId][$value['value_index']];
                    } else {
                        $productsIndex = array();
                    }

                    $info['options'][] = array(
                        'id'        => $value['value_index'],
                        'label'     => $value['label'],
                        'price'     => $configurablePrice,
                        'oldPrice'  => $this->_prepareOldPrice($value['pricing_value'], $value['is_percent']),
                        'products'  => $productsIndex,
                    );
                    $optionPrices[] = $configurablePrice;
					
					if($defaultValue == $value['label']){
						$info['defaultOption'] = $value['value_index'];
						$defaultValues[$attributeId] = $value['value_index'];
					}
                }
            }
            /**
             * Prepare formated values for options choose
             */
            foreach ($optionPrices as $optionPrice) {
                foreach ($optionPrices as $additional) {
                    $this->_preparePrice(abs($additional-$optionPrice));
                }
            }
            if($this->_validateAttributeInfo($info)) {
               $attributes[$attributeId] = $info;
            }

            // Add attribute default value (if set)
            if ($preconfiguredFlag) {
                $configValue = $preconfiguredValues->getData('super_attribute/' . $attributeId);
                if ($configValue) {
                    $defaultValues[$attributeId] = $configValue;
                }
            }
        }

        $taxCalculation = Mage::getSingleton('tax/calculation');
        if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
            $taxCalculation->setCustomer(Mage::registry('current_customer'));
        }

        $_request = $taxCalculation->getRateRequest(false, false, false);
        $_request->setProductClassId($currentProduct->getTaxClassId());
        $defaultTax = $taxCalculation->getRate($_request);

        $_request = $taxCalculation->getRateRequest();
        $_request->setProductClassId($currentProduct->getTaxClassId());
        $currentTax = $taxCalculation->getRate($_request);

        $taxConfig = array(
            'includeTax'        => $taxHelper->priceIncludesTax(),
            'showIncludeTax'    => $taxHelper->displayPriceIncludingTax(),
            'showBothPrices'    => $taxHelper->displayBothPrices(),
            'defaultTax'        => $defaultTax,
            'currentTax'        => $currentTax,
            'inclTaxTitle'      => Mage::helper('catalog')->__('Incl. Tax')
        );

        $config = array(
            'attributes'        => $attributes,
            'template'          => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
            'basePrice'         => $this->_registerJsPrice($this->_convertPrice($currentProduct->getFinalPrice())),
            'oldPrice'          => $this->_registerJsPrice($this->_convertPrice($currentProduct->getPrice())),
            'productId'         => $currentProduct->getId(),
            'chooseText'        => Mage::helper('catalog')->__('Choose an Option...'),
            'taxConfig'         => $taxConfig
        );

		$selectedId = array();
		foreach($defaultValues as $attr_id=>$attr_option_id) {
			if(isset($options[$attr_id][$attr_option_id])) {
				if(count($selectedId) > 0) {
					$selectedId = array_intersect($selectedId,$options[$attr_id][$attr_option_id]);
					$selectedId = array_unique($selectedId);
					$selectedId = array_values($selectedId);
				} else {
					$selectedId = $options[$attr_id][$attr_option_id];
				}
			}
		}
		$selectedProduct= Mage::getModel('catalog/product')->load($selectedId[0]);
		if($selectedProduct && $selectedProduct->getId()) {
			if(Mage::registry('current_selected_product')) Mage::unregister('current_selected_product');
			Mage::register('current_selected_product',$selectedProduct);
		}
		//print_r($selectedId);die;
        if ($preconfiguredFlag && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }

        $config = array_merge($config, $this->_getAdditionalConfig());
		$currentData = Mage::helper('core')->jsonEncode($config);
		Mage::register('current_json_config',$currentData);
        return $currentData;
    }

    /**
     * Validating of super product option value
     *
     * @param array $attributeId
     * @param array $value
     * @param array $options
     * @return boolean
     */
    protected function _validateAttributeValue($attributeId, &$value, &$options)
    {
        if(isset($options[$attributeId][$value['value_index']])) {
            return true;
        }

        return false;
    }

    /**
     * Validation of super product option
     *
     * @param array $info
     * @return boolean
     */
    protected function _validateAttributeInfo(&$info)
    {
        if(count($info['options']) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Calculation real price
     *
     * @param float $price
     * @param bool $isPercent
     * @return mixed
     */
    protected function _preparePrice($price, $isPercent = false)
    {
        if ($isPercent && !empty($price)) {
            $price = $this->getProduct()->getFinalPrice() * $price / 100;
        }

        return $this->_registerJsPrice($this->_convertPrice($price, true));
    }

    /**
     * Calculation price before special price
     *
     * @param float $price
     * @param bool $isPercent
     * @return mixed
     */
    protected function _prepareOldPrice($price, $isPercent = false)
    {
        if ($isPercent && !empty($price)) {
            $price = $this->getProduct()->getPrice() * $price / 100;
        }

        return $this->_registerJsPrice($this->_convertPrice($price, true));
    }

    /**
     * Replace ',' on '.' for js
     *
     * @param float $price
     * @return string
     */
    protected function _registerJsPrice($price)
    {
        return str_replace(',', '.', $price);
    }

    /**
     * Convert price from default currency to current currency
     *
     * @param float $price
     * @param boolean $round
     * @return float
     */
    protected function _convertPrice($price, $round = false)
    {
        if (empty($price)) {
            return 0;
        }

        $price = $this->getCurrentStore()->convertPrice($price);
        if ($round) {
            $price = $this->getCurrentStore()->roundPrice($price);
        }

        return $price;
    }
	
	protected function getDefaultAttributeValue($attributeCode){
		
		// Added for selecting default product variations
		$value = $this->getRequest()->getParam($attributeCode);
		if(is_string( $value )){
			$value = urldecode($value);
			$attributes = $this->getProduct()->getTypeInstance(true)->getConfigurableAttributesAsArray($this->getProduct());
			foreach($attributes as $attribute){
				if($attribute['attribute_code'] == $attributeCode){
					foreach($attribute['values'] as $attributeValue){
						if($attributeCode == 'stone1_grade' && stripos( $value, $attributeValue['label'] ) !== false){
							$label = $attributeValue['label'];
						}
						elseif(stripos( $attributeValue['label'],  $value) !== false){
							return $attributeValue['label'];
						}
					}
					if(isset($label))
						return $label;
				}
			}
		}
		
		if(!empty($value)){
			$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attributeCode);
			$label = $attribute->getSource()->getOptionText($value);
			if($label)
				return $label;
		}
		
		$value = ($this->getProduct()->getAttributeText('default_'.$attributeCode));
		if($value){
			return $value;
		}
		else{
			// at least try to send 1 default value
			$attributes = $this->getProduct()->getTypeInstance(true)->getConfigurableAttributesAsArray($this->getProduct());
			foreach($attributes as $attribute){
				if($attribute['attribute_code'] == $attributeCode){
					foreach($attribute['values'] as $attributeValue){
						return $attributeValue['label'];
					}
				}
			}
			return '';
		}
	}
}
