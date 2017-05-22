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
 * @package     Mage_AmazonPayments
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * AmazonPayments ASP Payment Action Dropdown source
 *
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @author     Amazon Payments Developer
 */
class Mage_AmazonPayments_Model_Payment_Cba_Source_Country
{
    public function toOptionArray()
    {
	// Countries currently supported for CBA are US, UK and DE
        return array(
            array(
                'value' => 'UK',
                'label' => Mage::helper('amazonpayments')->__('UK')
            ),
            array(
                'value' => 'US',
                'label' => Mage::helper('amazonpayments')->__('US')
            ),
            array(
                'value' => 'DE',
                'label' => Mage::helper('amazonpayments')->__('DE')
            ),
        );
    }
}
