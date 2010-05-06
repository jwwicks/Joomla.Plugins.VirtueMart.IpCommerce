<?php
/**
* Paymentech/IPCommerce payment module for Virtuemart
*
* Contains all the functionality needed to complete transactions with Paymentech/IPCommerce
*
* @version $Id: ps_ipcommerce.php jwwicks $
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
//error_reporting(E_ALL);

/**
 */
define('IPCOM_MAX_SEQUENCE_NUM', 998);

/**
* Paymentech plugin class
*
* Adds Paymentech/IPCommerce payment gateway plugin functionality to Virtuemart
*/
class ps_ipcommerce {

	var $payment_code = 'IPCOM';
	var $classname = 'ps_ipcommerce';

	/**
	* Check to see if configuration parameters are available.
	* @since VirtueMart 1.0.1
	* @param none
	* @return boolean FALSE if no configuration or TRUE otherwise
	*/
	function has_configuration() {
		$ret_val = true;
		return $ret_val;
	}

	/**
	* Check the writable status of the configuration file for this payment module.
	* @since VirtueMart 1.0.1
	* @return boolean value returned from is_writeable function
	*/
	function configfile_writeable() {
		return is_writeable( CLASSPATH.'payment/'.$this->classname.'.cfg.php' );
	}

	/**
	* Check the readable status of the configuration file for this payment module.
	* @since VirtueMart 1.0.1
	* @return boolean value returned from is_readable function
	*/
	function configfile_readable() {
		return is_readable( CLASSPATH.'payment/'.$this->classname.'.cfg.php' );
	}

	/**
	* Write current configuration parameters to config file.
	* @since VirtueMart 1.0.1
	* @param array d - An array of objects
	* @return boolean FALSE if writing configuration fails or TRUE otherwise
	*/
	function write_configuration( &$d ) {
		$ret_val = false;
		$my_config_array = array(
		'IPCOM_IS_TEST_TRXN' => $d['IPCOM_IS_TEST_TRXN'],
		'IPCOM_TEST_IPPF_SOCKET' => $d['IPCOM_TEST_IPPF_SOCKET'],
		'IPCOM_TEST_TRXN_HOST' => $d['IPCOM_TEST_TRXN_HOST'],
		'IPCOM_TEST_IPPFSERVICE_ID' => $d['IPCOM_TEST_IPPFSERVICE_ID'],
		'IPCOM_MERCHANT_ID' => $d['IPCOM_MERCHANT_ID'],
		'IPCOM_IPPFSERVICE_ID' => $d['IPCOM_IPPFSERVICE_ID'],		
		'IPCOM_IPPF_SOCKET' => $d['IPCOM_IPPF_SOCKET'],
		'IPCOM_PRIMARY_TRXN_HOST' => $d['IPCOM_PRIMARY_TRXN_HOST'],
		'IPCOM_SECONDARY_TRXN_HOST' => $d['IPCOM_SECONDARY_TRXN_HOST'],
		'IPCOM_PRIMARY_SRVC_HOST' => $d['IPCOM_PRIMARY_SRVC_HOST'],
		'IPCOM_SECONDARY_SRVC_HOST' => $d['IPCOM_SECONDARY_SRVC_HOST'],
		'IPCOM_TRXN_TYPE' => $d['IPCOM_TRXN_TYPE'],
		'IPCOM_CHECK_CARD_CODE' => $d['IPCOM_CHECK_CARD_CODE'],
		'IPCOM_VERIFIED_STATUS' => $d['IPCOM_VERIFIED_STATUS'],
		'IPCOM_INVALID_STATUS' => $d['IPCOM_INVALID_STATUS'],
		'IPCOM_EMAIL_MERCHANT' => $d['IPCOM_EMAIL_MERCHANT'],
		'IPCOM_EMAIL_CUSTOMER' => $d['IPCOM_EMAIL_CUSTOMER'],
		'IPCOM_SHOW_ERROR_CODE' => $d['IPCOM_SHOW_ERROR_CODE']
		);
		$config = "<?php\n";
		$config .= "if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); \n\n";
		foreach( $my_config_array as $key => $value ) {
			$config .= "define ('$key', '$value');\n";
		}

		$config .= "?>";

		if ($fp = fopen(CLASSPATH .'payment/'.$this->classname.'.cfg.php', 'w')) {
			fputs($fp, $config, strlen($config));
			fclose ($fp);
			$ret_val = true;
		}
		return $ret_val;
	}

    /**
	* Shows configuration panel in Virtuemart Admin.
	* @since VirtueMart 1.0.1
	* @return boolean FALSE if no configuration or TRUE otherwise 
	*/
	function show_configuration() {

		global $VM_LANG, $sess;
		$ret_val = true;
		$db =& new ps_DB;
		$payment_method_id = vmGet( $_REQUEST, 'payment_method_id', null );
		/** Read current Configuration ***/
		require_once(CLASSPATH .'payment/'.$this->classname.'.cfg.php');
		
    ?>
      <table>
        <tr>
            <td style="width: 250px; font-weight: bold;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_ENABLE_TESTMODE') ?></td>
            <td style="width: 200px; text-align: right;">
                <select name="IPCOM_IS_TEST_TRXN" class="inputbox" >
					<option <?php if (IPCOM_IS_TEST_TRXN == 'TRUE') echo "selected=\"selected\""; ?> value="TRUE"><?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_YES') ?></option>
					<option <?php if (IPCOM_IS_TEST_TRXN == 'FALSE') echo "selected=\"selected\""; ?> value="FALSE"><?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_NO') ?></option>
                </select>
            </td>
            <td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_ENABLE_TESTMODE_EXPLAIN') ?>
            </td>
        </tr>
        <tr>
			<td style="font-weight: bold;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_TEST_SOCKET') ?>
			</td>
			<td style="text-align: right;"><input style="width: 200px; text-align: right;" type="text" name="IPCOM_TEST_IPPF_SOCKET" class="inputbox" value="<?php echo IPCOM_TEST_IPPF_SOCKET ?>" />
			</td>
			<td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_TEST_SOCKET_EXPLAIN') ?>
			</td>
        </tr>
        <tr>
			<td style="font-weight: bold;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_TEST_HOST') ?>
			</td>
			<td style="text-align: right;"><input style="width: 200px; text-align: right;" type="text" name="IPCOM_TEST_TRXN_HOST" class="inputbox" value="<?php echo IPCOM_TEST_TRXN_HOST ?>" />
			</td>
			<td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_TEST_HOST_EXPLAIN') ?>
			</td>
        </tr>
        <tr>
			<td style="font-weight: bold;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_TEST_SERVICE_ID') ?>
			</td>
			<td style="text-align: right;"><input style="width: 200px; text-align: right;" type="text" name="IPCOM_TEST_IPPFSERVICE_ID" class="inputbox" value="<?php echo IPCOM_TEST_IPPFSERVICE_ID ?>" />
			</td>
			<td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_TEST_SERVICE_ID_EXPLAIN') ?>
			</td>
        </tr>		
        <tr>
            <td style="font-weight: bold;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_KEY') ?></td>
            <td>
                <a class="button" id="changekey" href="<?php $sess->purl($_SERVER['PHP_SELF']."?page=store.payment_method_keychange&pshop_mode=admin&payment_method_id=$payment_method_id") ?>" >
                <?php echo $VM_LANG->_('PHPSHOP_CHANGE_TRANSACTION_KEY') ?><a/>
            </td>
            <td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_KEY_EXPLAIN') ?></td>
        </tr>
        <tr>
			<td style="font-weight: bold;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_MERCHANT_ID') ?>
			</td>
			<td style="text-align: right;"><input style="width: 200px; text-align: right;" type="text" name="IPCOM_MERCHANT_ID" class="inputbox" value="<?php echo IPCOM_MERCHANT_ID ?>" />
			</td>
			<td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_MERCHANT_ID_EXPLAIN') ?>
			</td>
        </tr>
        <tr>
			<td style="font-weight: bold;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_SERVICE_ID') ?>
			</td>
			<td style="text-align: right;"><input style="width: 200px; text-align: right;" type="text" name="IPCOM_IPPFSERVICE_ID" class="inputbox" value="<?php echo IPCOM_IPPFSERVICE_ID ?>" />
			</td>
			<td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_SERVICE_ID_EXPLAIN') ?>
			</td>
        </tr>						
        <tr>
			<td style="font-weight: bold;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_SOCKET') ?>
			</td>
			<td style="text-align: right;"><input style="width: 200px; text-align: right;" type="text" name="IPCOM_IPPF_SOCKET" class="inputbox" value="<?php echo IPCOM_IPPF_SOCKET ?>" />
			</td>
			<td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_SOCKET_EXPLAIN') ?>
			</td>
        </tr>
        <tr>
			<td style="font-weight: bold;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_TRXN_HOST') ?>
			</td>
			<td style="text-align: right;"><input style="width: 200px; text-align: right;" type="text" name="IPCOM_PRIMARY_TRXN_HOST" class="inputbox" value="<?php echo IPCOM_PRIMARY_TRXN_HOST ?>" />
			</td>
			<td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_TRXN_HOST_EXPLAIN') ?>
			</td>
        </tr>
        <tr>
			<td style="font-weight: bold;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_TRXN_HOST2') ?>
			</td>
			<td style="text-align: right;"><input style="width: 200px; text-align: right;" type="text" name="IPCOM_SECONDARY_TRXN_HOST" class="inputbox" value="<?php echo IPCOM_SECONDARY_TRXN_HOST ?>" />
			</td>
			<td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_TRXN_HOST2_EXPLAIN') ?>
			</td>
        </tr>        
        <tr>
			<td style="font-weight: bold;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_SRVC_HOST') ?>
			</td>
			<td style="text-align: right;"><input style="width: 200px; text-align: right;" type="text" name="IPCOM_PRIMARY_SRVC_HOST" class="inputbox" value="<?php echo IPCOM_PRIMARY_SRVC_HOST ?>" />
			</td>
			<td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_SRVC_HOST_EXPLAIN') ?>
			</td>
        </tr>
        <tr>
			<td style="font-weight: bold;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_SRVC2_HOST') ?>
			</td>
			<td style="text-align: right;"><input style="width: 200px; text-align: right;" type="text" name="IPCOM_SECONDARY_SRVC_HOST" class="inputbox" value="<?php echo IPCOM_SECONDARY_SRVC_HOST ?>" />
			</td>
			<td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_SRVC2_HOST_EXPLAIN') ?>
			</td>
        </tr>
       <tr>
            <td style="font-weight: bold;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_AUTHENTICATIONTYPE') ?></td>
            <td style="text-align: right;">
               <select style="width: 200px; text-align: right;" name="IPCOM_TRXN_TYPE" class="inputbox">
                <option <?php if (IPCOM_TRXN_TYPE == 'AUTH') echo "selected=\"selected\""; ?> value="AUTH"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_AUTHORIZE_CAPTURE')?></option>
               </select>
            </td>
            <td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_AUTHENTICATIONTYPE_EXPLAIN') ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;"><strong><?php echo $VM_LANG->_('PHPSHOP_PAYMENT_CVV2') ?></strong></td>
            <td style="text-align: right;">
                <select name="IPCOM_CHECK_CARD_CODE" class="inputbox">
                <option <?php if (IPCOM_CHECK_CARD_CODE == 'YES') echo "selected=\"selected\""; ?> value="YES">
                <?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_YES') ?></option>
                <option <?php if (IPCOM_CHECK_CARD_CODE == 'NO') echo "selected=\"selected\""; ?> value="NO">
                <?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_NO') ?></option>
                </select>
            </td>
            <td style="font-style: italic;"><?php echo $VM_LANG->_('PHPSHOP_PAYMENT_CVV2_TOOLTIP') ?></td>
        </tr>
        <tr><td colspan="3"><hr/></td></tr>
        <tr>
            <td style="font-weight: bold;"><strong><?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_PAYMENT_ORDERSTATUS_SUCC') ?></strong></td>
            <td style="text-align: right;">
                <select name="IPCOM_VERIFIED_STATUS" class="inputbox" >
                <?php
                $q = "SELECT `order_status_name`,`order_status_code` FROM `#__{vm}_order_status` ORDER BY `list_order`";
                $db->query($q);
                $order_status_code = Array();
                $order_status_name = Array();

                while ($db->next_record()) {
                	$order_status_code[] = $db->f('order_status_code');
                	$order_status_name[] =  $db->f('order_status_name');
                }
                for ($i = 0; $i < sizeof($order_status_code); $i++) {
                	echo "<option value=\"" . $order_status_code[$i];
                	if (IPCOM_VERIFIED_STATUS == $order_status_code[$i])
                	echo "\" selected=\"selected\">";
                	else
                	echo "\">";
                	echo $order_status_name[$i] . "</option>\n";
                    }?>
                    </select>
            </td>
            <td style="font-style: italic;"><?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_PAYMENT_ORDERSTATUS_SUCC_EXPLAIN') ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;"><strong><?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_PAYMENT_ORDERSTATUS_FAIL') ?></strong></td>
            <td style="text-align: right;">
                <select name="IPCOM_INVALID_STATUS" class="inputbox" >
                <?php
                for ($i = 0; $i < sizeof($order_status_code); $i++) {
                	echo "<option value=\"" . $order_status_code[$i];
                	if (IPCOM_INVALID_STATUS == $order_status_code[$i])
                	echo "\" selected=\"selected\">";
                	else
                	echo "\">";
                	echo $order_status_name[$i] . "</option>\n";
                    } ?>
                    </select>
            </td>
            <td style="font-style: italic;"><?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_PAYMENT_ORDERSTATUS_FAIL_EXPLAIN') ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;"><strong><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_RESPCODES') ?></strong></td>
            <td style="text-align: right;">
                <select name="IPCOM_SHOW_ERROR_CODE" class="inputbox" >
                <option <?php if (IPCOM_SHOW_ERROR_CODE == 'YES') echo "selected=\"selected\""; ?> value="YES">
                <?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_YES') ?></option>
                <option <?php if (IPCOM_SHOW_ERROR_CODE == 'NO') echo "selected=\"selected\""; ?> value="NO">
                <?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_NO') ?></option>
                </select>
            </td>
            <td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_RESPCODES_EXPLAIN') ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;"><strong><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_EMAIL_MERCHANT') ?></strong></td>
            <td style="text-align: right;">
                <select name="IPCOM_EMAIL_MERCHANT" class="inputbox">
                <option <?php if (IPCOM_EMAIL_MERCHANT == 'YES') echo "selected=\"selected\""; ?> value="YES">
                <?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_YES') ?></option>
                <option <?php if (IPCOM_EMAIL_MERCHANT == 'NO') echo "selected=\"selected\""; ?> value="NO">
                <?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_NO') ?></option>
                </select>
            </td>
            <td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_EMAIL_MERCHANT_EXPLAIN') ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;"><strong><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_EMAIL_CUSTOMER') ?></strong></td>
            <td style="text-align: right;">
                <select name="IPCOM_EMAIL_CUSTOMER" class="inputbox">
                <option <?php if (IPCOM_EMAIL_CUSTOMER == 'YES') echo "selected=\"selected\""; ?> value="YES">
                <?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_YES') ?></option>
                <option <?php if (IPCOM_EMAIL_CUSTOMER == 'NO') echo "selected=\"selected\""; ?> value="NO">
                <?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_NO') ?></option>
                </select>
            </td>
            <td style="font-style: italic;"><?php echo $VM_LANG->_('VM_ADMIN_CFG_IPCOMMERCE_EMAIL_CUSTOMER_EXPLAIN') ?></td>
        </tr>
      </table>
   <?php
	return $ret_val;
	}

	/**
	* Process payment details through Paymentech/IPCommerce gateway
	* @since VirtueMart 1.0.1
	* @param string order_number - unique id number for the order (Note: NOT the id of order in vm table)
	* @param float order_total - total value of items in cart, includes shipping etc...
	* @param array d - 
	* @return boolean FALSE if processsing is unsuccessful, TRUE otherwise
	*/
	function process_payment($order_number, $order_total, &$d) {
        global $vendor_name, $VM_LANG, $vmLogger;
        $auth = $_SESSION['auth'];
        $ret_val = false;
		$trxnData = null;
        
        /*** Get the Configuration File for eway ***/
        require_once(CLASSPATH .'payment/'.$this->classname.'.cfg.php');
    
		// Get the Transaction Key securely from the database
		$sktId = new ps_DB;
		$sktId->query( "SELECT ".VM_DECRYPT_FUNCTION."(`payment_passkey`,'".ENCODE_KEY."') as passkey FROM `#__{vm}_payment_method` WHERE `payment_class`='".$this->classname."' AND `shopper_group_id`='".$auth['shopper_group_id']."'" );
		$socketId = $sktId->record[0];
		if(empty($socketId->passkey)) {
			$vmLogger->err($VM_LANG->_('PHPSHOP_PAYMENT_ERROR',false).': '.'Technical Note - The required transaction key is empty! The payment method settings must be reviewed.' );
			return false;
		}
		    
        $ipcPayment = new ipcomPayment();
		
		/*  Gateway Location (URI) */
		if( IPCOM_IS_TEST_TRXN === 'TRUE' ){
			$ipcPayment->set('IPPFSocketId', IPCOM_TEST_IPPF_SOCKET);
			$ipcPayment->set('IPPFServiceId', IPCOM_TEST_IPPFSERVICE_ID);
			$ipcPayment->set('GatewayURL', IPCOM_TEST_TRXN_HOST);
			//Note the following item will change based on your testing configuration - IPCommerce supplied Id
			$ipcPayment->set('PTLSSocketId',$socketId->passkey);
			//The following code may change - Please consult with your IPCommerce test tech
			$ipcPayment->set('SocketSer','439B0E4D62F4589822C72C4');
			$ipcPayment->set('ppbcp:CntryCode', 'USA');
			$ipcPayment->set('ppbcp:LangInd', 'ENG');
			$ipcPayment->set('ppbcp:MerchAddress:Street1','1400 16th St');
			$ipcPayment->set('ppbcp:MerchAddress:Street2', 'Suite 220');
			$ipcPayment->set('ppbcp:MerchAddress:City','Denver');
			$ipcPayment->set('ppbcp:MerchAddress:StateProv','CO');
			$ipcPayment->set('ppbcp:MerchAddress:PostalCode','80202');
			$ipcPayment->set('ppbcp:MerchAddress:CntryCode','USA');
			$ipcPayment->set('ppbcp:MerchId','123123456456');
			$ipcPayment->set('ppbcp:MerchName','IPCTest');
			$ipcPayment->set('ppbcp:SIC',5999);
			$ipcPayment->set('ppbcp:SocketNum','001');
			$ipcPayment->set('ppbcp:CustServicePhone','720 3773700');
			$ipcPayment->set('ppbcp:IndustryType','Ecommerce');
			$ipcPayment->set('ppbcp:MerchPhone','720 3773700');
		}
		else{
			$ipcPayment->set('PTLSSocketId',$socketId->passkey);
			$ipcPayment->set('Protocol', 'ssl://');
			//The following code may change - Please consult with your IPCommerce rep
			$ipcPayment->set('SocketSer','439B0E4D62F4589822C72C4');
			$ipcPayment->set('IPPFServiceId', IPCOM_IPPFSERVICE_ID );
			$ipcPayment->set('ppbcp:CntryCode', 'USA');
			$ipcPayment->set('ppbcp:LangInd', 'ENG');
			$ipcPayment->set('ppbcp:SIC',5999);
			$ipcPayment->set('ppbcp:SocketNum','001');
			$venDb = new ps_DB;
			$q = "SELECT * from `#__{vm}_vendor` WHERE `vendor_name`='".$venDb->getEscaped($vendor_name)."'";
			$venDb->query($q);
			$vendor = $venDb->next_record();
			if($vendor){
			
				$ipcPayment->set('ppbcp:MerchAddress:Street1', $venDb->f('vendor_address_1'));
				$ipcPayment->set('ppbcp:MerchAddress:Street2', $venDb->f('vendor_address_2'));
				$ipcPayment->set('ppbcp:MerchAddress:City',$venDb->f('vendor_city'));
				$ipcPayment->set('ppbcp:MerchAddress:StateProv',$venDb->f('vendor_state'));
				$ipcPayment->set('ppbcp:MerchAddress:PostalCode',$venDb->f('vendor_zip'));
				$ipcPayment->set('ppbcp:MerchAddress:CntryCode',$venDb->f('vendor_country'));
				$ipcPayment->set('ppbcp:MerchId', IPCOM_MERCHANT_ID);
				$ipcPayment->set('ppbcp:MerchName',$vendor_name);
				$ipcPayment->set('ppbcp:CustServicePhone', preg_replace('/\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})/', '\1 \2\3', $venDb->f('vendor_phone')));
				$ipcPayment->set('ppbcp:IndustryType','Ecommerce');
				$ipcPayment->set('ppbcp:MerchPhone', preg_replace('/\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})/', '\1 \2\3', $venDb->f('contact_phone_1')));
			}
			else{
				$vmLogger->err('VendorErr: '.$vendor_name);
				}
		}
		
        $db = new ps_DB;
		$db->query("SELECT max(order_id)+1 as order_id FROM `#__{vm}_orders`");
		$db->next_record();
		$order_id = $db->f('order_id');
		if($order_id >= 1)
			$sn = $order_id % IPCOM_MAX_SEQUENCE_NUM;
		else{ //First order for store
			$order_id = 1;
			$sn = 1;
		}
		
		if( !(IPCOM_IS_TEST_TRXN === 'TRUE') ){ //Live Transaction
			// Get user billing information
			$billInfo = new ps_DB;
			$billInfo->query("SELECT * FROM `#__{vm}_user_info` WHERE `user_id`=".$auth['user_id']." AND `address_type`='BT'");
			$billInfo->next_record();
			$bill_info_id = $billInfo->f('user_info_id');
			if( $bill_info_id != $d['ship_to_info_id'] ) {
				// Get user shipping information
				$shipInfo =& new ps_DB;
				$shipInfo->query("SELECT * FROM `#__{vm}_user_info` WHERE `user_info_id`='".$d['ship_to_info_id']."' AND `address_type`='ST'");
				$shipInfo->next_record();
			}
			else {
				$shipInfo = $billInfo;
			}  
			
			$ccType = $ipcPayment->getCreditCardType( $_SESSION['ccdata']['creditcard_code'] );
				
			$trxnData = array(
			array(
				'ppbcp:SeqNum'=> $sn,
				'SeqNum'=> $order_id,
				'ppbcp:OrderNum' => date("Ymd").'T'.date("His").'OID'.$order_id,
				'ppbcp:CardholderName' => $_SESSION['ccdata']['order_payment_name'],
				'ppbcp:Street' => $billInfo->f('address_1'),
				'ppbcp:City' => $billInfo->f('city'),
				'ppbcp:State' => $billInfo->f('state'),
				'ppbcp:PostalCode' => $billInfo->f('zip'),
				'ppbcp:Amt' => $order_total,
				'ppbcp:TotalItems' => 1,
				'ppbcp:CardType' => $ccType,
				'ppbcp:PAN' => $_SESSION['ccdata']['order_payment_number'],
				'ppbcp:Expire' => $_SESSION['ccdata']['order_payment_expire_month'].substr( $_SESSION['ccdata']['order_payment_expire_year'], 2, 2 ),
				'ppbcp:CVDataInd' => 'Provided',
				'ppbcp:CVData' => $_SESSION['ccdata']['credit_card_code'],
				'ppbcp:AVSData' => true)
				);        
		}
		else{
			//Test Data Array for validation
			$billInfo = array('ppbcp:SeqNum'=> 994, 'SeqNum'=> 994, 'ppbcp:Amt' => 12.00, 'ppbcp:TotalItems' => 1, 'ppbcp:CVDataInd' => 'Provided', 'ppbcp:AVSData' => true, 'first_name' => 'John', 'last_name' => 'Smith', 'address_1' => '1400 16th Street', 'city' => 'Denver', 'state' => 'CO', 'zip' => '80202', 'credit_card_type' => 'Visa', 'order_payment_number' => '4111111111111111', 'order_payment_expire_month' => 12, 'order_payment_expire_year' => 2010, 'credit_card_code' => 111);

			$trxnData = array(
				array(
					'ppbcp:SeqNum'=> $billInfo['ppbcp:SeqNum'],
					'SeqNum'=> $order_id,
					'ppbcp:OrderNum' => date("Ymd").'T'.date("His").'OID'.$order_id,
					'ppbcp:CardholderName' => $billInfo['first_name'].' '.$billInfo['last_name'],
					'ppbcp:Street' => $billInfo['address_1'],
					'ppbcp:City' => $billInfo['city'],
					'ppbcp:State' => $billInfo['state'],
					'ppbcp:PostalCode' => $billInfo['zip'],
					'ppbcp:Amt' => $billInfo['ppbcp:Amt'],
					'ppbcp:TotalItems' => $billInfo['ppbcp:TotalItems'],
					'ppbcp:CardType' => $billInfo['credit_card_type'],
					'ppbcp:PAN' => $billInfo['order_payment_number'],
					'ppbcp:Expire' => $billInfo['order_payment_expire_month'].substr( $billInfo['order_payment_expire_year'], 2, 2 ),
					'ppbcp:CVDataInd' => $billInfo['ppbcp:CVDataInd'],
					'ppbcp:CVData' => $billInfo['credit_card_code'],
					'ppbcp:AVSData' => $billInfo['ppbcp:AVSData'])
				);
		}
		
		//Note: Reason for loop here is that it future release 
		//of plugin may allow batch transactions to system
        foreach( $trxnData as $trxn ){
			foreach( $trxn as $element => $val ){
				$ipcPayment->set("$element", (is_string($val) ? "$val": $val) );
			}
			$ipcPayment->doPayment();
		}
		 
        if( $ipcPayment->get('ppbcp:StatusCode') === '000' || $ipcPayment->get('ppbcp:StatusCode') === '00') { //Bank card status return code
			$d['order_payment_log'] = $VM_LANG->_('PHPSHOP_PAYMENT_TRANSACTION_SUCCESS');
            $d['order_payment_trans_id'] = $ipcPayment->get('ptls:TxnGUID');
			
			$orderInfo = new ps_DB;
			$q = "UPDATE `#__{vm}_order_payment` SET ";
			$q .="`order_payment_log`='".$d['order_payment_log']."',";
			$q .="`order_payment_trans_id`='".$d['order_payment_trans_id']."' ";
			$q .="WHERE `order_id`='".$order_number."' ";
			$orderInfo->query( $q );
			
			$vmLogger->debug('TrxnId: '.$d['order_payment_trans_id'].', OrderNum: '.$order_number);
			
            $ret_val = true;
		} 
        else {
			$vmLogger->err($VM_LANG->_('PHPSHOP_PAYMENT_ERROR',false).': '.$ipcPayment->getError() );
            $d['order_payment_trans_id'] = $ipcPayment->get('ptls:TxnGUID');
		}
		return $ret_val;
	}

	/**
	* Capture payment information through Paymentech/IPCommerce gateway
	* @since VirtueMart 1.0.1
	* @param array d - 
	* @return boolean FALSE if processsing is unsuccessful, TRUE otherwise
	*/
	function capture_payment( &$d ) {
		$ret_val = false;
	}
	
	/**
	* Calculates the discount for payment
	* @since VirtueMart 1.0.1
	* @param float subtotal - 
	* @return float fee/discount for the payment module
	*/
	function get_payment_rate( $subtotal ) {
		$ret_val = 0.00;
	}
}

/**
* class ipcomPayment 
* Electronic Payment XML Interface for IPCommerce
*/
class ipcomPayment {
	/**
	* PTLS Request XML data
	*
    * @var      string of PTLS request
    * @access   protected
    * @since    1.0
    */
	var $_xmlRequest;
	/**
	* PTLS Response XML data
	*
    * @var      string of PTLS response
    * @access   protected
    * @since    1.0
    */
	var $_xmlResponse;
	/**
	* An XML Parser Object used for PTLS transactions
	*
    * @var      object of XML Parser
    * @access   protected
    * @since    1.0
    */
	var $_xmlParser;
	/**
	* XML Tag data
	*
    * @var      array of XML tag data
    * @access   protected
    * @since    1.0
    */
    var $_xmlData;
	/**
	* Current XML tag name
	*
    * @var      string of current XML tag
    * @access   protected
    * @since    1.0
    */
    var $_curTag;
	/**
	* An array of errors
	*
    * @var      array of error messages
    * @access   protected
    * @since    1.0
    */
   var $_errors = array();

   /**
	* Class Constructor.
    *
    * @access   public
    * @param    string $ippfSocketID Socket identifier assigned by ipcommerce
    * @param    string  $gatewayURL The default url for the transaction gateway
    * @since    1.0
    */
    function ipcomPayment( $ippfSocketID = IPCOM_IPPF_SOCKET, $gatewayURL = IPCOM_PRIMARY_TRXN_HOST ) {
        $this->set('IPPFSocketId', $ippfSocketID);
        $this->set('GatewayURL', $gatewayURL);
        $this->set('Protocol','ssl://');
        $this->set('Port','443');
    }

   /**
	* Creates and processes the PTLS Payment transaction
    *
    * @access   public
    * @return   mixed The last error
    * @since    1.0
    */
    function doPayment() {
		global $VM_LANG, $vmLogger;
        $this->_xmlRequest ="<document xmlns:ptls=\"http://www.ippaymentsframework.com/PTLS/v1.17\" xmlns:ppreq=\"http://www.ippaymentsframework.com/PTLS/v1.17/PP/Request\" xmlns:ppbcp=\"http://www.ippaymentsframework.com/PTLS/v1.17/PP/BCP\" xmlns=\"http://www.ippaymentsframework.com/PTLS/v1.17\" xmlns:ppsva=\"http://www.ippaymentsframework.com/PTLS/v1.17/PP/SVA\" xmlns:ppeck=\"http://www.ippaymentsframework.com/PTLS/v1.17/PP/ECK\" xmlns:ppweb=\"http://www.ippaymentsframework.com/PTLS/v1.17/PP/WEB\" xmlns:rmidt=\"http://www.ippaymentsframework.com/PTLS/v1.17/RM/IDT\" xmlns:txnadd=\"http://www.ippaymentsframework.com/PTLS/v1.17TxnBodyAddenda\" xmlns:xenc=\"http://www.w3.org/2001/04/xmlenc#\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">".
				"<TxnMsg>". 
					$this->getPTLSHeader().
					"<Services>".
						"<Service IPPFServiceId=\"".$this->get('IPPFServiceId',IPCOM_IPPFSERVICE_ID)."\">".
							$this->getPTLSServiceHeader().
							"<Request>".
								"<ppreq:CREDIT>".
									"<ppreq:AUTH>".
										"<ppreq:ControlParams>".
											"<ppbcp:IsSignatureDebit>false</ppbcp:IsSignatureDebit>".
										"</ppreq:ControlParams>".
										$this->getPTLSCustomerData().
										$this->getPTLSMerchantData().
										$this->getPTLSSocketData().
										"<ppreq:TenderData>".
											$this->getPTLSCardData().
											"<ppbcp:POSEntryMode>Keyed</ppbcp:POSEntryMode>".
										"</ppreq:TenderData>".
										$this->getPTLSTrxnData().
									"</ppreq:AUTH>".
								"</ppreq:CREDIT>".
							"</Request>".
						"</Service>".
					"</Services>".
				"</TxnMsg>".
			"</document>";

        $fp  = @fsockopen($this->get('Protocol') . $this->get('GatewayURL'), $this->get('Port'), $errno, $errstr,5);
		if (!$fp) {
			$this->setError('Socket Open Error: '.$errno.' - '.$errstr);
			$vmLogger->err($VM_LANG->_('PEAR_LOG_ERR',false).': '.$this->getError() .'URL: '.$this->get('Protocol').$this->get('GatewayURL').':'.$this->get('Port'));
		}
		else{
			$length = strlen($this->_xmlRequest);
			fwrite($fp, pack('N',$length));   // http://us.php.net/pack - Add 4 byte header length
			fwrite($fp, $this->_xmlRequest);
			while (!feof($fp)) {
				$this->_xmlResponse .= fgets($fp, 4096);
			}
			fclose($fp);
	        
			$len = strlen($this->_xmlResponse);
	        
			if($len){
				$this->_xmlResponse = substr($this->_xmlResponse,4,$len); // strip the 4 byte header
				 			
				$this->_xmlParser = xml_parser_create();
	            
				// Disable XML tag capitalisation (Case Folding)
				xml_parser_set_option ($this->_xmlParser, XML_OPTION_CASE_FOLDING, FALSE);
	            
				// Define Callback functions for XML Parsing
				xml_set_object($this->_xmlParser, $this);
				xml_set_element_handler ($this->_xmlParser, 'ipcXmlElementStart', 'ipcXmlElementEnd');
				xml_set_character_data_handler ($this->_xmlParser, 'ipcXmlData');
	            
				xml_parse($this->_xmlParser, $this->_xmlResponse, TRUE);
				if( xml_get_error_code( $this->_xmlParser ) == XML_ERROR_NONE ){
           			if(isset($this->_xmlData['ptls:TxnGUID']) || isset($this->_xmlData['TxnGUID']) ){
            			$this->set('ptls:TxnGUID', (isset($this->_xmlData['ptls:TxnGUID'])? $this->_xmlData['ptls:TxnGUID']: $this->_xmlData['TxnGUID']));
            		}
					if(isset($this->_xmlData['ptls:StatusCode']))
						$this->set('ptls:StatusCode', $this->_xmlData['ptls:StatusCode']);
					if(isset($this->_xmlData['ppbcp:StatusCode']))
						$this->set('ppbcp:StatusCode', $this->_xmlData['ppbcp:StatusCode']);
					if(isset($this->_xmlData['ppbcp:StatusMsg']))
						$this->set('ppbcp:StatusMsg', $this->_xmlData['ppbcp:StatusMsg']);
					if(isset($this->_xmlData['ppbcp:ApprovalCode']))
						$this->set('ppbcp:ApprovalCode', $this->_xmlData['ppbcp:ApprovalCode']);
					//In case there's an error get the header level StatusCode/Msg	
					if(isset($this->_xmlData['StatusCode']))
						$this->set('StatusCode', $this->_xmlData['StatusCode']);				
					if(isset($this->_xmlData['StatusMsg']))
						$this->set('StatusMsg', $this->_xmlData['StatusMsg']);				
				}
				else{
					$this->setError('xmlParser Error : '.xml_get_error_code( $this->_xmlParser ));
					$vmLogger->err($VM_LANG->_('PEAR_LOG_ERR',false).': '.$this->getError());
				}            

				$vmLogger->info('Approval Code: '.$this->get('ppbcp:ApprovalCode', 'ApprovalCode Not Found...'));
				//$vmLogger->log($this->_xmlRequest);
				//$vmLogger->log($this->_xmlResponse);
				//$vmLogger->debug($this->_xmlRequest);
				//$vmLogger->debug($this->_xmlResponse);
				$vmLogger->debug('OrderNum: '. $this->get('ppbcp:OrderNum').' '.
					'TxnGuid: '. $this->get('ptls:TxnGUID').' '.
					'PTLSStatusCode: '. $this->get('ptls:StatusCode', $this->get('StatusCode')).' '.
					'PPBCPStatusCode: '. $this->get('ppbcp:StatusCode', $this->get('StatusCode')).' '.
					'StatusMessage: '. $this->get('ppbcp:StatusMsg', $this->get('StatusMsg')).' '.
					'ApprovalCode: '. $this->get('ppbcp:ApprovalCode', 'ApprovalCode Not Found...'));
					            
				xml_parser_free( $this->_xmlParser );
			}
			else{
				$this->setError('Response Error: No response from server received.');
				$vmLogger->err($VM_LANG->_('PEAR_LOG_ERR',false).': '.$this->getError());
			}
		}        
        return $this->getError();
    }
   
   /**
	* XML Parser callback function for beginning of XML tag processing
    *
    * @access   public
    * @param    object $parser XML Parser
    * @param    string $tag current XML tag
    * @param    mixed  $attributes current attributes for XML tag
    * @since    1.0
    */	
	function ipcXmlElementStart ($parser, $tag, $attributes) {
        $this->_curTag = $tag;
    }

   /**
	* XML Parser callback function for end of XML tag processing
    *
    * @access   public
    * @param    object $parser XML Parser
    * @param    string $tag current XML tag
    * @since    1.0
    */	
    function ipcXmlElementEnd ($parser, $tag) {
        $this->_curTag = "";
    }

   /**
	* XML Parser callback function for handling XML tag data
    *
    * @access   public
    * @param    object $parser XML Parser
    * @param    mixed  $cdata current data for XML tag
    * @since    1.0
    */	
    function ipcXmlData ($parser, $cdata) {
        $this->_xmlData[$this->_curTag] = $cdata;
    }
	
   /**
	* Gets the current Date and Time for the transaction
    *
    * @access   public
    * @return   string of UTC date
    * @since    1.0
    */	    
    function getTrxnTime(){
		$ret_val = date("Y-m-d")."T".date("H:i:s.uP");
		return $ret_val;
	}
	
   /**
	* Currently not used
    *
    * @access   public
    * @todo		Fix bug when using this method in getPTLSHeader
    * @see		getPTLSHeader
    */		
	function getSocketTime(){
		$ret_val = date("Y-M-D")."T".date("H:i:s.u")."-06:00";
		return $ret_val;
	}

   /**
	* GUID for PTLS transaction identification
    *
    * @access   public
    * @return   mixed The value of uniqid
    * @since    1.0
    */		
    function getSocketTxnId(){
		$ret_val = str_replace('.', '', uniqid($_SERVER['REMOTE_ADDR'], TRUE));
    	return $ret_val;
    } 

   /**
	* Returns the IPCommerce compatible Credit Card Type 
    *
    * @access   public
    * @return   
    * @since    1.0
    */		
    function getCreditCardType( $vm_ccType ){
		$ret_val;
		
		$db = new ps_DB;
		$db->query("SELECT `creditcard_name` as ccName FROM `#__{vm}_creditcard` WHERE `creditcard_code`='".$vm_ccType."'");
		$db->next_record();
		$ret_val = $db->f('ccName');
    	return $ret_val;
    } 
	
   /**
	* PTLS Header XML data
    *
    * @access   public
    * @return   string The PTLS XML header
    * @since    1.0
    * @todo		Fix bug in getSocketTime
    */		
    function getPTLSHeader(){
    
    	$ret_val =	'<PTLSHeader>'.
						'<PTLSVer>1.17.9</PTLSVer>'.
						'<PTLSSocketId>'.$this->get('PTLSSocketId').'</PTLSSocketId>'.
						'<SocketSer>'.$this->get('SocketSer').'</SocketSer>'.
						'<IPPFSocketId>'.$this->get('IPPFSocketId').'</IPPFSocketId>'.
						'<Op>MSG</Op>'.
						'<SocketTime>2009-05-26T14:43:26.462-06:00</SocketTime>'.
						// @BUG: The following isn't working for some reason
						// Hard coding socket time from example IPCommerce transaction
						//'<SocketTime>'.$this->getSocketTime().'</SocketTime>'.
					'</PTLSHeader>';
		return $ret_val;
    }

   /**
	* PTLS XML Service Header Data
    *
    * @access   public
    * @return   string of PTLS Service XML header
    * @since    1.0
    * @todo		Modify to allow other types of transactions
    */	    
    function getPTLSServiceHeader(){
		$ret_val =	'<ServiceHeader>'.
						'<SeqNum>'.$this->get('SeqNum').'</SeqNum>'.
						'<Format>XML</Format>'.
						'<TxnClass>CREDIT</TxnClass>'.
						'<TxnType>AUTH</TxnType>'.
						'<TxnMeta>'.
							'<Amt>'.number_format($this->get('ppbcp:Amt'), 2).'</Amt>'.
							'<TotalItems>'.$this->get('ppbcp:TotalItems').'</TotalItems>'.
							'<IsSignatureDebit>false</IsSignatureDebit>'.
						'</TxnMeta>'.
					'</ServiceHeader>';
		return $ret_val;
    }

   /**
	* PTLS Customer XML Data section
    *
    * @access   public
    * @return   string of PTLS Customer data
    * @since    1.0
    */	    
    function getPTLSCustomerData(){
		$ret_val = "";
		if($this->get('ppbcp:AVSData')!=null){
			$ret_val = 
				'<ppreq:CustomerData>'.
					'<ppbcp:AVSData>';
							if($this->get('ppbcp:CardholderName')!=null) 
								$ret_val .= '<ppbcp:CardholderName>'.$this->get('ppbcp:CardholderName').'</ppbcp:CardholderName>';
							if($this->get('ppbcp:Street')!=null)
								$ret_val .= '<ppbcp:Street>'.$this->get('ppbcp:Street').'</ppbcp:Street>';
							if($this->get('ppbcp:City')!=null)
								$ret_val .= '<ppbcp:City>'.$this->get('ppbcp:City').'</ppbcp:City>';
							if($this->get('ppbcp:State')!=null)	
								$ret_val .= '<ppbcp:State>'.$this->get('ppbcp:State').'</ppbcp:State>';
							if($this->get('ppbcp:PostalCode')!=null)	
								$ret_val .= '<ppbcp:PostalCode>'.$this->get('ppbcp:PostalCode').'</ppbcp:PostalCode>';
			$ret_val .= 
					'</ppbcp:AVSData>'. 
				'</ppreq:CustomerData>';
		}
		return $ret_val;
    }

   /**
	* PTLS Merchant XML Data 
    *
    * @access   public
    * @return   string of PTLS Merchant data
    * @since    1.0
    */	   
   function getPTLSMerchantData(){
		$ret_val = '<ppreq:MerchantData>'.
						'<ppbcp:CntryCode>'.$this->get('ppbcp:CntryCode', 'USA').'</ppbcp:CntryCode>'.
						'<ppbcp:LangInd>'.$this->get('ppbcp:LangInd', 'ENG').'</ppbcp:LangInd>'.
						'<ppbcp:MerchAddress>'.
							'<ppbcp:Street1>'.$this->get('ppbcp:MerchAddress:Street1', '1400 16th St').'</ppbcp:Street1>'.
							'<ppbcp:Street2>'.$this->get('ppbcp:MerchAddress:Street2', 'Suite 220').'</ppbcp:Street2>'.
							'<ppbcp:City>'.$this->get('ppbcp:MerchAddress:City', 'Denver').'</ppbcp:City>'.
							'<ppbcp:StateProv>'.$this->get('ppbcp:MerchAddress:StateProv', 'CO').'</ppbcp:StateProv>'.
							'<ppbcp:PostalCode>'.$this->get('ppbcp:MerchAddress:PostalCode', '80202').'</ppbcp:PostalCode>'.
							'<ppbcp:CntryCode>'.$this->get('ppbcp:MerchAddress:CountryCode', 'USA').'</ppbcp:CntryCode>'.
						'</ppbcp:MerchAddress>'.
						'<ppbcp:MerchId>'.$this->get('ppbcp:MerchId', '123123456456').'</ppbcp:MerchId>'.
						'<ppbcp:MerchName>'.$this->get('ppbcp:MerchName', 'IPCTest').'</ppbcp:MerchName>'.
						'<ppbcp:SIC>'.$this->get('ppbcp:SIC', 5999).'</ppbcp:SIC>'.
						'<ppbcp:SocketNum>'.$this->get('ppbcp:SocketNum', 001).'</ppbcp:SocketNum>'.
						'<ppbcp:CustServicePhone>'.$this->get('ppbcp:CustServicePhone', '720 3773700').'</ppbcp:CustServicePhone>'.
						'<ppbcp:IndustryType>'.$this->get('ppbcp:IndustryType', 'Ecommerce').'</ppbcp:IndustryType>'.
						'<ppbcp:MerchPhone>'.$this->get('ppbcp:MerchPhone', '720 3773700').'</ppbcp:MerchPhone>'.
					'</ppreq:MerchantData>';
		return $ret_val;
   }

   /**
	* PTLS Socket XML Data
    *
    * @access   public
    * @return   string of XML data for this software
    * @since    1.0
    */	   
   function getPTLSSocketData(){
		$ret_val = '<ppreq:SocketData>'.
						'<ppbcp:AppName>Virtuemart IPCommerce Payment Module</ppbcp:AppName>'.
						'<ppbcp:SerialNum>12346578901</ppbcp:SerialNum>'.
						'<ppbcp:SocketLocation>On_Premises</ppbcp:SocketLocation>'.
						'<ppbcp:SocketType>PC</ppbcp:SocketType>'.
						'<ppbcp:SoftwareVer>0.1.99</ppbcp:SoftwareVer>'.
						'<ppbcp:SoftwareVerDate>2009-10-26</ppbcp:SoftwareVerDate>'.
						'<ppbcp:PINCap>Does_Not_Support_PIN</ppbcp:PINCap>'.
						'<ppbcp:SocketCap>Key_Only</ppbcp:SocketCap>'.
						'<ppbcp:TermAttnd>Attended</ppbcp:TermAttnd>'.
						'<ppbcp:TokenCap>Unsupported</ppbcp:TokenCap>'.
					'</ppreq:SocketData>';
		return $ret_val;
   }

   /**
	* PTLC Credit Card XML Data
    *
    * @access   public
    * @return   string of PTLS Credit Card XML data
    * @since    1.0
    */	
   function getPTLSCardData(){
		$ret_val = '<ppbcp:CardData>'.
						'<ppbcp:CardType>'.$this->get('ppbcp:CardType').'</ppbcp:CardType>'.
						'<ppbcp:PAN>'.$this->get('ppbcp:PAN').'</ppbcp:PAN>'.
						'<ppbcp:Expire>'.$this->get('ppbcp:Expire').'</ppbcp:Expire>';
						if( $this->get('ppbcp:CVDataInd') != null )
							$ret_val .= '<ppbcp:CVDataInd>'.$this->get('ppbcp:CVDataInd').'</ppbcp:CVDataInd>';
						if($this->get('ppbcp:CVDataInd') === 'Provided')
							$ret_val .= '<ppbcp:CVData>'.$this->get('ppbcp:CVData').'</ppbcp:CVData>'.'</ppbcp:CardData>';
						else
							$ret_val .= '</ppbcp:CardData>';
		return $ret_val;
	}
   
   /**
	* PTLS Transaction XML Data
    *
    * @access   public
    * @return   string of PTLS XML Transaction data
    * @since    1.0
    */	
	function getPTLSTrxnData(){
		$ret_val = '<ppreq:TxnData>'.
						'<ppbcp:SocketTxnId>'.$this->getSocketTxnId().'</ppbcp:SocketTxnId>'.
						'<ppbcp:Order>'.
							'<ppbcp:Id>'.$this->get('ppbcp:OrderNum').'</ppbcp:Id>'.
							'<ppbcp:PreviousInd>false</ppbcp:PreviousInd>'.
						'</ppbcp:Order>'.
						'<ppbcp:TxnCode>Standard_Purchase</ppbcp:TxnCode>'.
						'<ppbcp:TxnDateTime>'.$this->getTrxnTime().'</ppbcp:TxnDateTime>'.
						'<ppbcp:CustPresentFlag>Ecommerce</ppbcp:CustPresentFlag>'.
						'<ppbcp:Amt>'.number_format($this->get('ppbcp:Amt'), 2).'</ppbcp:Amt>'.
						'<ppbcp:CurrencyCode>USD</ppbcp:CurrencyCode>'.
						'<ppbcp:ReqACI>Is_CPSMerit_Capable</ppbcp:ReqACI>'.
						'<ppbcp:SeqNum>'.$this->get('ppbcp:SeqNum').'</ppbcp:SeqNum>'.
						'<ppbcp:SigCaptured>false</ppbcp:SigCaptured>'.
						'<ppbcp:EcommerceData>'.
							'<ppbcp:OrderNum>'.$this->get('ppbcp:OrderNum').'</ppbcp:OrderNum>'.
							'<ppbcp:PayTypeInd>Non_authenticated_Security_With_SSL</ppbcp:PayTypeInd>'.
						'</ppbcp:EcommerceData>'.
					'</ppreq:TxnData>';
		return $ret_val;
	}
         
   /**
	* Returns a property of the object or the default value if the property is not set.
    *
    * @access   public
    * @param    string $property The name of the property
    * @param    mixed  $default The default value
    * @return   mixed The value of the property
    * @see		getProperties
    * @since    1.0
    */
    function get($property, $default=null){
		if(isset($this->$property)) {
			return $this->$property;
		}
		return $default;
	}
	
	/**
     * Modifies a property of the object, creating it if it does not already exist.
     *
     * @access   public
     * @param    string $property The name of the property
     * @param    mixed  $value The value of the property to set
     * @return   mixed Previous value of the property
     * @see		 setProperties
     * @since    1.0
     */
    function set( $property, $value = null ){
        $previous = isset($this->$property) ? $this->$property : null;
        $this->$property = $value;
        return $previous;
    }

 	/**
	 * Returns an associative array of object properties
	 *
	 * @access	public
	 * @param	boolean $public If true, returns only the public properties
	 * @return	array
	 * @see		get()
	 * @since	1.0
 	 */
	function getProperties( $public = true )
	{
		$vars  = get_object_vars($this);

        if($public)
		{
			foreach ($vars as $key => $value)
			{
				if ('_' == substr($key, 0, 1)) {
					unset($vars[$key]);
				}
			}
		}
        return $vars;
	}

	/**
	 * Get the most recent error message
	 *
	 * @param	integer	$i Option error index
	 * @param	boolean	$toString Indicates
	 * @return	string	Error message
	 * @access	public
	 * @since	1.0
	 */
	function getError($i = null, $toString = true )
	{
		// Find the error
		if ( $i === null) {
			// Default, return the last message
			$error = end($this->_errors);
		}
		else
		if ( ! array_key_exists($i, $this->_errors) ) {
			// If $i has been specified but does not exist, return false
			return false;
		}
		else {
			$error	= $this->_errors[$i];
		}
		return $error;
	}

	/**
	 * Return all errors, if any
	 *
	 * @access	public
	 * @return	array	Array of error messages or JErrors
	 * @since	1.5
	 */
	function getErrors()
	{
		return $this->_errors;
	}

	/**
	* Set the object properties based on a named array/hash
	*
	* @access	protected
	* @param	$array  mixed Either and associative array or another object
	* @return	boolean
	* @see		set()
	* @since	1.0
	*/
	function setProperties( $properties )
	{
		$ret_val = false;
		$properties = (array) $properties; //cast to an array

		if (is_array($properties))
		{
			foreach ($properties as $k => $v) {
				$this->$k = $v;
			}
			$ret_val = true;
		}
		return $ret_val;
	}

	/**
	 * Add an error message
	 *
	 * @param	string $error Error message
	 * @access	public
	 * @since	1.0
	 */
	function setError($error)
	{
		array_push($this->_errors, $error);
	}

	/**
	 * Object-to-string conversion.
	 * Each class can override it as necessary.
	 *
	 * @access	public
	 * @return	string This name of this class
	 * @since	1.5
 	 */
	function toString()
	{
		return get_class($this);
	} 
}
?>