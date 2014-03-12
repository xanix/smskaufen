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
class Northton_Smskaufen_Block_Adminhtml_Smskaufen_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('smskaufenGrid');
      $this->setDefaultSort('id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
      
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('smskaufen/smskaufen')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('id', array(
          'header'    => Mage::helper('smskaufen')->__('Log #'),
          'align'     =>'left',
          'index'     => 'id',
      ));
      $this->addColumn('customer_email', array(
          'header'    => Mage::helper('smskaufen')->__('Customer Email'),
          'align'     =>'left',
          'index'     => 'customer_email',
      ));
      $this->addColumn('handy', array(
          'header'    => Mage::helper('smskaufen')->__('Handy'),
          'align'     =>'left',
          'index'     => 'handy',
      ));
      $this->addColumn('generated_password', array(
          'header'    => Mage::helper('smskaufen')->__('Generated Password'),
          'align'     =>'left',
          'index'     => 'generated_password',
      ));

      $this->addColumn('handy_verified', array(
          'header'    => Mage::helper('smskaufen')->__('Activated'),
          'align'     =>'left',
          'index'     => 'handy_verified',
      ));
      
      return parent::_prepareColumns();
  }

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('smskaufen');
		return $this;
	}

  public function getRowUrl($row)
  {
      return '';
  }

}