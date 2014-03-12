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
class Northton_Smskaufen_Adminhtml_SmskaufenController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('system')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Smskaufen Log'), Mage::helper('adminhtml')->__('Smskaufen Log'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	

	public function sendtestAction() {
		//send a test sms via admin panel > system > configuration > northton
		$data='test';
		$smsModel = Mage::getModel('smskaufen/smskaufen');
		$response = $smsModel->sendSms($data);
		if($response == 100)
		{
			$message = $this->__('Message sent!');
			Mage::getSingleton('adminhtml/session')->addSuccess($message);
		}
		else
		{
			$message = $this->__('Error code: '.$response.'. Please check the interface documentation for the error code description.');
			Mage::getSingleton('adminhtml/session')->addError($message);
		}
		$this->_redirect('*/*/');
	}
}