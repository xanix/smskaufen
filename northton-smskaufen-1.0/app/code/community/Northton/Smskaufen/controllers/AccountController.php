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
require_once('Mage/Customer/controllers/AccountController.php');
class Northton_Smskaufen_AccountController extends Mage_Customer_AccountController
{
    public function accountverificationAction() {
	     $customerEmail = Mage::getSingleton('customer/session')->getCustomer()->getEmail(); //customer email
	     $mobileNumber = $this->getRequest()->getParam('mobile_number'); //customer mobile phone
	     $mobileVerificationCode = $this->getRequest()->getParam('mobile_code'); //sms code sent to the mobile phone
	     $mobileNumber = str_replace('+','00',$mobileNumber); //in case the customer used the sign +, replace it with 00
	     $smsModel = Mage::getModel('smskaufen/smskaufen');
	     Mage::getSingleton('core/session')->setVerificationphase('mobile'); //load phase one, by default
	     if($mobileNumber)
	     {
			$smsResponse = (int)$smsModel->sendSms($mobileNumber,$customerEmail);
			if($smsResponse != 100)
			{ //if the 1st phase wasn't successful, return to phase 1
				if(empty($smsResponse))
				{
					$message = $this->__('Error: Please enter a valid mobile number');
				}
				else
				{
					if(is_numeric($smsResponse))
					{
						$message = $this->__('Error code: '.$smsResponse);
					}
					else
					{
						$message = $this->__('Error: '.$smsResponse);
					}
				}
				Mage::getSingleton('core/session')->addError($message);
			}
			else
			{ // successful, verification code (phase 2) 
				Mage::getSingleton('core/session')->setVerificationphase('code');
			}
	     }
	     if($mobileVerificationCode)
	     {
			$verificationCompleted = (int)$smsModel->customerVerification($customerEmail,$mobileVerificationCode);
			if($verificationCompleted != 1)
			{ // failed
				$message = $this->__('Verification Failed!');
				Mage::getSingleton('core/session')->addError($message);
			}
	     }
	     
	     $this->loadLayout();
	     $this->renderLayout();   
    }
}
