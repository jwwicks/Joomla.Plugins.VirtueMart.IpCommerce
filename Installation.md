# Introduction #
<p>This readme assumes you have a Joomla 1.5.x and Virtuemart 1.1.x installation already setup. It also assumes you're familiar with editing PHP files. If you're shakey on the latter this process might not be best for you.</p>
<p>This ZIP file contains all the files necessary to begin the Certification Process for the IPCommerce/Paymentech Payment Platform.</p>
<p>Note: this plugin is no longer being actively developed since IPCommerce has shutdown their API.</p>
<p>May 6, 2013<br>
<br>
RE:  Transaction Processing End of Service<br>
<br>
Since IP Commerce’s inception, we have provided our customers with convenient access to both payment processing and commerce services.  As the commoditization of payment processing services accelerates, we have determined that it is no longer economically viable for us to provide payment processing services.  As a result, IP Commerce will cease operation of payment processing services on June 30, 2013. We regret any difficulty this presents for your business; however, we are committed to assist you in making a successful migration to another provider through this date.<br>
<br>
Should you seek additional time for migration, I encourage you to contact your existing merchant acquirer.  We have provided merchant acquirers an opportunity to extend IP Commerce-based services on behalf of their customers beyond June 30.<br>
<br>
Going forward, IP Commerce will focus its attention on the development and deployment of Commerce Services such as Commerce Reconciliation, Commerce Boarding and Online2Onsite (O2O). These services provide value to our customers above and beyond the payment transaction. We look forward to serving you with these capabilities as we work to ensure that your migration off of the IP Commerce Payments service is as seamless as possible.<br>
<br>
Sincerely,<br>
Charlie Wilson<br>
President</p>

# Details #
<p>Merchants can not begin testing until they receive testing ID's and URL's via email from the certification group.</p>
<p>If you need to begin certification, please logon to the IPCommerce/ChasePaymentech Labs site <a href='http://lab.chasepaymentechxpress.com/'>http://lab.chasepaymentechxpress.com/</a>. Create an account and you'll be contacted by a representative who'll ask about your Merchant status etc. You should already have a merchant account with Chase so you'll be able to provide that information to the IPCommerce rep. They'll ask about the platform etc...</p>
<p>For this you can just tell them it's going to be:<br>
<ul>
<li>PTLS Direct Integration </li>
<li>Target Industry/Vertical -  Ecommerce</li>
<li>Development Languages -  PHP, Perl</li>
<li>Merchant Configuration - Single socket, single merchant</li>
<li>Deployment Topology  - Server</li>
</ul>
</p>
<p>They'll then send you a set of instructions with your ID's and URL's for testing and a sample xml with a tag called PTLSSocketId. This should be placed in the Virtuemart configuration for the payment module. We'll discuss this later.</p>

# Files and Descriptions #

  1. readme.txt - You are currently reading the Read Me File.  This file describes all the other files within this zip.
  1. ps\_ipcommerce.php - Virtuemart Payment module for IPCommerce/Paymentech.
  1. ps\_ipcommerce.cfg.php - Configuration file for Virtuemart Payment module for IPCommerce/Paymentech.
  1. english.php - English messages for Virtuemart Payment module for IPCommerce/Paymentech.

# To install the payment module #
<ol>
<li>Copy the ps_ipcommerce.php and ps_ipcommerce.cfg.php files to the "wwwroot"\administrator\components\com_virtuemart\classes\payment directory. wwwroot is the root directory for your Joomla Website.</li>
<li>Locate and Backup the following file "wwwroot"\administrator\components\com_virtuemart\languages\common\english.php</li>
<li>Edit the "wwwroot"\administrator\components\com_virtuemart\languages\common\english.php. Locate the line with the following text near line 1331:<br />
); $VM_LANG->initModule( 'common', $langvars );<br />
Add a blank line before this line. Add a comma. Then copy the strings in this payment module's english.php to the end of that file after the comma but before the "); $VM_LANG->initModule( 'common', $langvars );" line.</li>
<li>Log into the VirtueMart admin area and create a new Payment Method.<br>
<ol>
<li> Store, List Payment Methods, New</li>
<li>The Payment Method Form is displayed. In the Payment Method Name field type IPCommerce</li>
<li>In the Code field type IPCOM</li>
<li>Select the "ps_ipcommerce" module in the Payment class name drop-down list.</li>
<li>Select the radio-button labeled "Use Payment Processor" for the "Payment method type"</li>
<li>Save</li>
</ol>
</li>
<li>Gather all the configuration information from your IPCommerce representative. IP's URL's MerchantID etc...</li>
<li>This new Payment Method should appear in the List of Payment Methods in the Virtuemart Store administration area.</li>
<li>Select the IPCommerce payment method and then the Configuration tab.</li>
<li>Enter the PTLSSocketID in the form it's something like 0123456789012345.</li>
<li>Enter the Test (Sandbox) Host URL's etc... it should be something like<br>
txn-01.sandbox.ipcommerce.com</li>
<li>Enter the Service ID it's something like 0123456789.</li>
<li>Enter the Transaction Key, this is a very long hex string and uniquely<br>
identifies your transactions.</li>
<li>Enter the Merchant ID it's something like 01234.</li>
<li>Lastly make sure Test Mode is set to Yes</li>
<li>Save</li>
<li>Turn on Debugging in VirtueMart. This is needed for the TrxnCodes.</li>
<li>Change the Credit Card short codes<br>
<ol>
<li>Store, Credit Cards List</li>
<li>Change the short code for American Express from amex to American_Express</li>
<li>Change the short code for Visa from VISA to Visa</li>
<li>Change the short code for Mastercard from MC to MasterCard</li>
<li>Change the short code for Diners from diners to DinersCart_Blanche</li>
<li>Change the short code for JCB from jcb to JCB</li>
<li>Change the short code for Discover from discover to Discover</li>
<li>Save</li>
</ol>
</li>
</ol>

# What's Next - Testing/Certification #
<p>The certification process is rather involved so be patient. You'll need to edit the ps_commerce.php file several times during this process since Virtuemart doesn't allow test cards through on the front-end. </p>
<p>Near line 470 of ps_ipcommerce.php you'll notice an array of test data elements calls $billInfo. I've set this to the first test data they required me to send to the sandbox testing server. You'll need to edit this line several times to get through the entire testing process.</p>
<p>
I used the following data for part of my certification but yours may be different...<br>
<pre>
<pre><code>$billInfo = array('ppbcp:SeqNum'=&gt; 994, 'SeqNum'=&gt; 994, 'ppbcp:Amt' =&gt; 12.00, 'ppbcp:TotalItems' =&gt; 1, 'ppbcp:CVDataInd' =&gt; 'Provided', 'ppbcp:AVSData' =&gt; true, 'first_name' =&gt; 'John', 'last_name' =&gt; 'Smith', 'address_1' =&gt; '1400 16th Street', 'city' =&gt; 'Denver', 'state' =&gt; 'CO', 'zip' =&gt; '80202', 'credit_card_type' =&gt; 'Visa', 'order_payment_number' =&gt; '4111111111111111', 'order_payment_expire_month' =&gt; 12, 'order_payment_expire_year' =&gt; 2010, 'credit_card_code' =&gt; 111);<br>
</code></pre>
</pre>
</p>
<p>Now just run through the normal checkout process in VirtueMart. You'll need to enter a real CC number or VM will complain during the checkout. Don't worry you won't get billed since the test data is what gets sent in Test Mode. Capture the result of each transaction from the debug information and paste it into the certification spreadsheet your representative should have sent you. Then when you've completed the test suite send it to your tech contact. They'll go over the data and pass or fail any wrong tests. You'll need to repeat any failed tests.</p>
<p>The debug info will look something like this:<br>
<pre>
Debug: OrderNum: 20101113T053428OID2 TxnGuid: XXXXXXXXXXX PTLSStatusCode: 000 PPBCPStatusCode: 000 StatusMessage: APPROVED ApprovalCode: 701877<br>
Debug: TrxnId: A3A9892B8AD54150999F0A23CAC1FFB6, OrderNum: 62_0d747bdad08606e1227a53e06df5b<br>
</pre>
</p>
<p>What I did was setup a $1.00 Test product in VM. I added it to my cart each time and went through the checkout process adding my real CC data when needed.</p>