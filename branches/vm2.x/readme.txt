Last Updated 2009.12.13

This readme assumes you have a Joomla 1.5.x and Virtuemart 1.1.x installation already setup. It also assumes you're familiar with editing PHP files. If you're shakey on the latter this process might not be best for you.

This ZIP file contains all the files necessary to begin the Certification Process for the IPCommerce/Paymentech Payment Platform.

Merchants can not begin testing until they receieve testing ID's and URL's via email from the certification group.

If you need to begin certification, please logon to the IPCommerce/ChasePaymentech Labs site http://lab.chasepaymentechxpress.com/.
Create an account and you'll be contacted by a representative who'll ask about your Merchant status etc. You should already have a
merchant account with Chase so you'll be able to provide that information to the IPCommerce rep. They'll ask about the platform etc...

For this you can just tell them it's going to be:

PTLS Direct Integration 
Target Industry/Vertical -  Ecommerce 
Development Languages -  PHP, Perl
Merchant Configuration - Single socket, single merchant
Deployment Topology  - Server

They'll then send you a set of instructions with your ID's and URL's for testing and a sample xml with a tag called PTLSSocketId. This should be placed in the Virtuemart configuration for the payment module. We'll discuss this later.


Files and Descriptions:

1. readme.txt - You are currently reading the Read Me File.  This file describes all the other files within this zip.

2. ps_ipcommerce.php - Virtuemart Payment module for IPCommerce/Paymentech.

3. ps_ipcommerce.cfg.php - Configuration file for Virtuemart Payment module for IPCommerce/Paymentech.

4. english.php - English messages for Virtuemart Payment module for IPCommerce/Paymentech.


To install the payment module:

1. Copy the ps_ipcommerce.php and ps_ipcommerce.cfg.php files to the "wwwroot"\administrator\components\com_virtuemart\classes\payment directory. wwwroot is the root directory for your Joomla Website.

2. Locate and Backup the following file "wwwroot"\administrator\components\com_virtuemart\languages\common\english.php

3. Edit the "wwwroot"\administrator\components\com_virtuemart\languages\common\english.php. Locate the line with the following text near line 1331: 

); $VM_LANG->initModule( 'common', $langvars );

Add a blank line before this line. Add a comma. Then copy the strings in this payment module's english.php to the end of that file after the comma but before the "); $VM_LANG->initModule( 'common', $langvars );" line.

4. Log into the VirtueMart admin area and create a new Payment Method. Store/List Payment Methods/New

5. The Payment Method Form is displayed. In the Payment Method Name field type IPCommerce
6. In the Code filed type IPCOM
7. Select the "ps_ipcommerce" module in the Payment class name drop-down list.
8. Select the radio-button labeled "Use Payment Processor" for the "Payment method type"
9. Save

Gather all the configuration information from your IPCommerce representative. IP's URL's MerchantID etc...

10. This new Payment Method should appear in the List of Payment Methods in the Virtuemart Store administration area.
11. Select the IPCommerce payment method and then the Configuration tab.
12. Enter the approriate data in the fields provided. Add the PTLSSocketId as you Virtuemart Transaction key.

The certification process is rather involved so be patient. You'll need to edit the ps_commerce.php file several times during this process since Virtuemart doesn't allow test cards through on the front-end. 

Near line 470 of ps_ipcommerce.php you'll notice an array of test data elements calls $billInfo. I've set this to the first test data they required me to send to the sandbox testing server. You'll need to edit this line several times to get through the entire testing process.

I used the following data for part of my certification but yours may be different...
$billInfo = array('ppbcp:SeqNum'=> 994, 'SeqNum'=> 994, 'ppbcp:Amt' => 12.00, 'ppbcp:TotalItems' => 1, 'ppbcp:CVDataInd' => 'Provided', 'ppbcp:AVSData' => true, 'first_name' => 'John', 'last_name' => 'Smith', 'address_1' => '1400 16th Street', 'city' => 'Denver', 'state' => 'CO', 'zip' => '80202', 'credit_card_type' => 'Visa', 'order_payment_number' => '4111111111111111', 'order_payment_expire_month' => 12, 'order_payment_expire_year' => 2010, 'credit_card_code' => 111);

Jw