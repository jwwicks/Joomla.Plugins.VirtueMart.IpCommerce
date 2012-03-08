<?php
/**
* Paymentech/IPCommerce payment module for Virtuemart
*
* Contains all the functionality needed to complete transactions with Paymentech/IPCommerce
*
* @version $Id: ps_ipcommerce.cfg.php jwwicks $
* @package VirtueMart
* @subpackage payment
* @author John Wicks <me@jwwicks.com>
* @copyright Copyright (C) 2009 jwwicks - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

define ('IPCOM_IS_TEST_TRXN', 'TRUE');
define ('IPCOM_TEST_IPPF_SOCKET', 'ABC123456789ABC1');
define ('IPCOM_TEST_TRXN_HOST', 'txn-01.sandbox.ipcommerce.com');
define ('IPCOM_TEST_IPPFSERVICE_ID', '1234567890');
define ('IPCOM_IPPF_SOCKET', 'AB1234567890ABC1');
define ('IPCOM_MERCHANT_ID','12345');
define ('IPCOM_IPPFSERVICE_ID','123456789A');
define ('IPCOM_PRIMARY_TRXN_HOST', 'txn-01.ipcommerce.com');
define ('IPCOM_SECONDARY_TRXN_HOST', 'txn-02.ipcommerce.com');
define ('IPCOM_PRIMARY_SRVC_HOST', 'cws-01.ipcommerce.com');
define ('IPCOM_SECONDARY_SRVC_HOST', 'cws-02.ipcommerce.com');
define ('IPCOM_TRXN_TYPE', 'AUTH');
define ('IPCOM_CHECK_CARD_CODE', 'YES');
define ('IPCOM_VERIFIED_STATUS', 'P');
define ('IPCOM_INVALID_STATUS', 'X');
define ('IPCOM_EMAIL_MERCHANT', 'YES');
define ('IPCOM_EMAIL_CUSTOMER', 'YES');
define ('IPCOM_SHOW_ERROR_CODE', 'YES');

?>