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
class Northton_Smskaufen_Block_Adminhtml_Smskaufen extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
		$this->_controller = 'adminhtml_smskaufen';
		$this->_blockGroup = 'smskaufen';
		$this->_headerText = Mage::helper('smskaufen')->__('Smskaufen Log');
		parent::__construct();
		$this->_removeButton('add');
  }
}