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
class Northton_Smskaufen_Model_Smskaufen extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('smskaufen/smskaufen');
    }
    
    public function checkEmail($customerEmail)
    {
		if(empty($customerEmail))
		{
			return false;
		}
        $sql = 'SELECT id FROM northton_smskaufen_log WHERE customer_email="'.$customerEmail.'"';
        $allResult = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);
        foreach($allResult as $result)
        {
                $logId = $result['id'];
                if(is_numeric($logId) && ($logId>0))
				{
					return $logId;
				}
        }
		return false;
    }
    
    public function updateCustomer($customerId)
    {
		$sql = 'UPDATE northton_smskaufen_log SET handy_verified=1 WHERE id='.$customerId;
		$sqlWrite = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);
    }

    public function customerVerification($customerEmail,$mobileCode)
    {
        $sql = 'SELECT id FROM northton_smskaufen_log WHERE customer_email="'.$customerEmail.'" AND generated_password="'.$mobileCode.'"';
        $allResult = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);
        foreach($allResult as $result)
        {
			$customerId = $result['id'];
			if(is_numeric($customerId) && ($customerId>0))
			{
				$this->updateCustomer($customerId);
				return '1';
			}
        }
		return '0';
    }
    
    public function checkAccountVerified($customerEmail)
    {
        $sql = 'SELECT handy_verified FROM northton_smskaufen_log WHERE customer_email="'.$customerEmail.'"';
        $allResult = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);
        $accountVerified=0;
        foreach($allResult as $result)
        {
                
                $accountVerified = $result['handy_verified'];
        }
		return $accountVerified;
    }
 
    public function checkNumberExists($customerHandy)
    {
        $sql = 'SELECT id FROM northton_smskaufen_log WHERE handy="'.$customerHandy.';" AND handy_verified=1';
        $allResult = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);
		$numberExists=0;
        foreach($allResult as $result)
        {
                
                $numberExists = $result['id'];
        }
		return $numberExists;
    }
    
    public function sendSms($mobileNumber,$customerEmail)
    {
		//Initiate variables
		$data=array();
		$request = "";
		$store = Mage::app()->getStore();
		$store_id = $store->getId();
		//Test the variables
		if(empty($customerEmail))
		{
			$customerEmail='-';
		}
		if($mobileNumber == 'test')
		{
			$mobileNumber = Mage::getStoreConfig('smskaufen_options/messages/sendtestmessage', $store_id);
			$customerEmail='-';
		}
		if(!is_numeric($mobileNumber))
		{
			return 'Please enter a valid mobile number!';
		}
		else
		{
			$numberExists = $this->checkNumberExists($mobileNumber);
		
			$numberForTesting = Mage::getStoreConfig('smskaufen_options/messages/sendtestmessage', $store_id);
			$isPhoneTesting = strcmp($numberForTesting, $mobileNumber);
			if(!empty($numberExists) && (strcmp($numberForTesting, $mobileNumber) != '0'))
			{
				return 'Number was already used for the verification process';
			}
		}
		
		$mobileNumber .= ';';
		// Fetch admin panel data and send it to smskaufen's API
		// User name
		$data["id"] = Mage::getStoreConfig('smskaufen_options/messages/usernamefield', $store_id);
		
		// Password
		$data["pw"] = Mage::getStoreConfig('smskaufen_options/messages/passwordfield', $store_id);
		
		// Recipient(s) separated by semicolons
		$data["empfaenger"] = $mobileNumber;
		
		// Sender
		$data["absender"] = Mage::getStoreConfig('smskaufen_options/messages/sentfrom', $store_id);
		
		// Content of SMS
		$data["text"] = Mage::getStoreConfig('smskaufen_options/messages/messagearea', $store_id);
		$data['generated_password']='';
		if(strpos($data["text"], '{{password}}') !== false)
		{
			$generatedPassword = $this->generatePassword(5);
			$data["text"] = str_replace("{{password}}", $generatedPassword, $data["text"]);
		}
		
		// Constants - not in use here
		$data["type"]=4;
		$data["termin"] = "TT.MM.JJJJ-SS:MM";
		$data["id_status"] = "1";
		$data["massen"] = "1";
		$data["reply"] = "0";
		$data["reply_email"] = "";
		
		//send request
		foreach($data as $key=>$val){
		$request.= $key."=".urlencode($val);
		
		//append the ampersand (&) sign after each value pair
		$request.= "&";
		}
		$url = "http://www.smskaufen.com/sms/gateway/sms.php";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		
		//fetch response
		$response = curl_exec($ch);
		curl_close($ch);
		$response = (int)$response;
		
		//Response is OK, log the details
		if($response == 100)
		{
			$data['generated_password']=$generatedPassword;
			$data['customer_email']=$customerEmail;
			$this->saveRecord($data);
		}
		
		return $response;
    }
    
    protected function saveRecord($data)
    {
		$customerExists = $this->checkEmail($data['customer_email']); //gets the customer ID back
		if(empty($customerExists))
		{
		
		      $sql = 'INSERT INTO northton_smskaufen_log (handy, customer_email, generated_password, created_time, update_time) 
			    VALUES(:handy, :customer_email, :generated_password, NOW(), NOW())';
			$binds = array(
			    'handy'      => $data["empfaenger"],
			    'generated_password' => $data["generated_password"],
			    'customer_email' => $data["customer_email"],
			);
		}
		else
		{
		      $sql = 'UPDATE northton_smskaufen_log SET handy=:handy, generated_password=:generated_password, created_time=NOW(), update_time=NOW() WHERE id='.$customerExists;
			$binds = array(
			    'handy'      => $data["empfaenger"],
			    'generated_password' => $data["generated_password"],
			);

		}
	    $sqlWrite = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql,$binds);
    }
    
    protected function generatePassword($length = 5) 
    { //fabricate random password
	    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	    $count = mb_strlen($chars);

	    for ($i = 0, $result = ''; $i < $length; $i++) {
			$index = rand(0, $count - 1);
			$result .= mb_substr($chars, $index, 1);
	    }

	    return $result;
    }

}