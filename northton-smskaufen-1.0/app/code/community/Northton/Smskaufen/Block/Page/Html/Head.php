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
 *
 * @category   Northton
 * @package    Northton_Smskaufen
 * @copyright  Copyright (c) 2013 Northton UG (http://www.xanix.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @smskaufen https://www.smskaufen.com/sms/werben/anmeld.php?bn_nr=13993
 */
class Northton_Smskaufen_Block_Page_Html_Head extends Mage_Page_Block_Html_Head
{
    /**
     * Initialize template
     *
     */
    protected function _construct()
    {
	$customerEmail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
	if(!empty($customerEmail)) {
		$smsModel = Mage::getModel('smskaufen/smskaufen');
		$customerVerified = $smsModel->checkAccountVerified($customerEmail);
		$verification_location = Mage::getUrl('customer/account/accountverification/');
		$currentUrl = $this->helper('core/url')->getCurrentUrl();
		
		$store = Mage::app()->getStore();
		$store_id = $store->getId();
		$enabled = Mage::getStoreConfig('smskaufen_options/messages/enabledfield', $store_id);
		if($enabled == '2')
		{
			if($customerVerified != '1')
			{ //if customer is not already sms-verified, relocate him to the verification page
				if(strcmp($currentUrl,$verification_location) != 0)
				{
					header('location: '.$verification_location);
					die();
				}
			}
		}
	}
        $this->setTemplate('page/html/head.phtml');
    }
}