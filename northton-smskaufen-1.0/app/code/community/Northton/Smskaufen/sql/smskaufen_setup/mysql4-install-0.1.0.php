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
$installer = $this;

$installer->startSetup();

$installer->run("
	-- DROP TABLE IF EXISTS {$this->getTable('northton_smskaufen_log')};
	CREATE TABLE {$this->getTable('northton_smskaufen_log')} (
	  `id` int(11) unsigned NOT NULL auto_increment,
	  `handy` varchar(255) NOT NULL,
	  `customer_email` varchar(500) NOT NULL,
	  `generated_password` varchar(255) NULL,
	  `handy_verified` boolean NOT NULL,
	  `created_time` datetime NULL,
	  `update_time` datetime NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();