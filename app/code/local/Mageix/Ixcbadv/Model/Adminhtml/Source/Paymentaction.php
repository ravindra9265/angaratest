<?php
/**
 * Mageix LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to Mageix LLC's  End User License Agreement
 * that is bundled with this package in the file LICENSE.pdf
 * It is also available through the world-wide-web at this URL:
 * http://ixcba.com/index.php/license-guide/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to webmaster@mageix.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 * 
 * Magento Mageix IXCBADV Module
 *
 * @category	Checkout & Payments, Customer Registration & Login
 * @package 	Mageix_Ixcbadv
 * @copyright   Copyright (c) 2014 -  Mageix LLC 
 * @author      Brian Graham
 * @license	    http://ixcba.com/index.php/license-guide/   End User License Agreement
 *
 *
 *
 * Magento Mageix IXCBA Module
 * 
 * @category   Checkout & Payments
 * @package	   Mageix_Ixcba
 * @copyright  Copyright (c) 2011 Mageix LLC
 * @author     Brian Graham
 * @license   http://mageix.com/index.php/license-guide/   End User License Agreement
 */

class Mageix_Ixcbadv_Model_Adminhtml_Source_Paymentaction {


	public function toOptionArray()
	{
		return array(
		        array('value' => 'Sale', 'label' => Mage::helper('adminhtml')->__('Sale (Instant Payment)')),
				
				
		        array('value' => 'Authorize',   'label' => Mage::helper('adminhtml')->__('Authorize (Capture with Single Invoice)')),
				
				
		        array('value' => 'Confirm',   'label' => Mage::helper('adminhtml')->__('Deferred (Capture with Single or Multiple Invoices)')),
			
			
			
		);
	}
}



?>