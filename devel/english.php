<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
*
* @package VirtueMart
* @subpackage languages
* @copyright Copyright (C) 2004-2008 soeren - All rights reserved.
* @translator soeren
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
global $VM_LANG;
$langvars = array (
	'VM_ADMIN_CFG_IPCOMMERCE_ENABLE_TESTMODE' => 'Test mode ?',
	'VM_ADMIN_CFG_IPCOMMERCE_ENABLE_TESTMODE_EXPLAIN' => 'Select \'Yes\' while testing. Select \'No\' to enable live transactions.',
    'VM_ADMIN_CFG_IPCOMMERCE_TEST_SOCKET' => 'Test Socket ID',
	'VM_ADMIN_CFG_IPCOMMERCE_TEST_SOCKET_EXPLAIN' => 'This is the IPPF Socket ID assigned for the certification/testing process',
    'VM_ADMIN_CFG_IPCOMMERCE_TEST_HOST' => 'Test Host',
	'VM_ADMIN_CFG_IPCOMMERCE_TEST_HOST_EXPLAIN' => 'This is the URL of the test server assigned for the certification/testing process',
	'VM_ADMIN_CFG_IPCOMMERCE_MERCHANT_ID' => 'Merchant ID',
	'VM_ADMIN_CFG_IPCOMMERCE_MERCHANT_ID_EXPLAIN' => 'This is the Live Merchant Identification Number assigned by your IPCommerce representative',
    'VM_ADMIN_CFG_IPCOMMERCE_SOCKET' => 'Live Socket ID',
	'VM_ADMIN_CFG_IPCOMMERCE_SOCKET_EXPLAIN' => 'This is the IPPF Socket ID assigned for live transactions',
    'VM_ADMIN_CFG_IPCOMMERCE_TRXN_HOST' => 'Live Transaction Host',
	'VM_ADMIN_CFG_IPCOMMERCE_TRXN_HOST_EXPLAIN' => 'This is the URL of the primary server assigned for live transactions',
    'VM_ADMIN_CFG_IPCOMMERCE_TRXN_HOST2' => 'Secondary Transaction Host',
	'VM_ADMIN_CFG_IPCOMMERCE_TRXN_HOST2_EXPLAIN' => 'This is the URL of the secondary server assigned for live transactions',
	'VM_ADMIN_CFG_IPCOMMERCE_KEY' => 'IPCommerce Transaction Key',
	'VM_ADMIN_CFG_IPCOMMERCE_KEY_EXPLAIN' => 'This is your IPCommerce Transaction Key',
	'VM_ADMIN_CFG_IPCOMMERCE_AUTHENTICATIONTYPE' => 'Authentication Type',
	'VM_ADMIN_CFG_IPCOMMERCE_AUTHENTICATIONTYPE_EXPLAIN' => 'This is the IPCommerce authentication type.',
    'VM_ADMIN_CFG_IPCOMMERCE_SRVC_HOST' => 'Live Service Host',
	'VM_ADMIN_CFG_IPCOMMERCE_SRVC_HOST_EXPLAIN' => 'This is the URL of the primary server assigned for live service information',
    'VM_ADMIN_CFG_IPCOMMERCE_SRVC2_HOST' => 'Secondary Service Host',
	'VM_ADMIN_CFG_IPCOMMERCE_SRVC2_HOST_EXPLAIN' => 'This is the URL of the secondary server assigned for live service information',
	'VM_ADMIN_CFG_IPCOMMERCE_AUTHORIZE_CAPTURE' => 'Authorize &amp; Capture',
	'VM_ADMIN_CFG_IPCOMMERCE_AUTHORIZE_ONLY' => 'Authorize Only',
	'VM_ADMIN_CFG_IPCOMMERCE_RESPCODES' => 'Show Response Codes for Failed Transactions?',
	'VM_ADMIN_CFG_IPCOMMERCE_RESPCODES_EXPLAIN' => 'If set to YES then the customer will see the IPCommerce Transaction Response Reason Codes. This can be useful for troubleshooting failed or declined transactions.
		For further details see the IPCommerce Implementation Guide',
	'VM_ADMIN_CFG_IPCOMMERCE_EMAIL_MERCHANT' => 'Send Email Confirmation from Gateway to Merchant?',
	'VM_ADMIN_CFG_IPCOMMERCE_EMAIL_MERCHANT_EXPLAIN' => 'Send copy of the customer confirmation email to the Merchant? If yes then an email will
		be sent to the Merchant address as well as the address(es) configured in the Merchant Interface.',
	'VM_ADMIN_CFG_IPCOMMERCE_EMAIL_CUSTOMER' => 'Send Email Confirmation from Gateway to Customer?',
	'VM_ADMIN_CFG_IPCOMMERCE_EMAIL_CUSTOMER_EXPLAIN' => 'Send a confirmation email to the customer? If yes then an email will
		be sent to the customer from the Merchant Interface.',
    'VM_ADMIN_CFG_IPCOMMERCE_TEST_SERVICE_ID' => 'Test IPPF Service ID',
	'VM_ADMIN_CFG_IPCOMMERCE_TEST_SERVICE_ID_EXPLAIN' => 'This is the ID of the IPPF Service assigned for testing transaction',				
    'VM_ADMIN_CFG_IPCOMMERCE_SERVICE_ID' => 'Live IPPF Service ID',
	'VM_ADMIN_CFG_IPCOMMERCE_SERVICE_ID_EXPLAIN' => 'This is the ID of the IPPF Service assigned for live transactions'	
); $VM_LANG->initModule( 'common', $langvars );
?>