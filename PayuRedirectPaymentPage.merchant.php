<?php

/**********
Name: PayU Redirect Payment Page
Cart: Wordpress - WP eCommerce
From: PayU Payment Solutions (Pty) Ltd
**********/

// Set gateway variables for WP e-Commerce
$nzshpcrt_gateways[$num]['name'] = 'PayU Redirect Payment Page';
$nzshpcrt_gateways[$num]['display_name'] = 'Pay At PayU';
$nzshpcrt_gateways[$num]['internalname'] = 'Pay At PayU';
$nzshpcrt_gateways[$num]['function'] = 'gateway_payu_redirect';
$nzshpcrt_gateways[$num]['form'] = "form_payu_redirect";
$nzshpcrt_gateways[$num]['submit_function'] = "form_submit_payu_redirect";


function form_payu_redirect(){

    //Requiring the neccesary script for this function to work    
    require('library.payu/inc.wordpress/config.wordpress.php');
    
	// Getting stored values
	$options = array();
    $options['payuRedirect_systemToCall'] = get_option('payuRedirect_systemToCall');
    
    $options['payuRedirect_safekey'] = get_option('payuRedirect_safekey_'.strtolower($options['payuRedirect_systemToCall']) );
    $options['payuRedirect_username'] = get_option('payuRedirect_username_'.strtolower($options['payuRedirect_systemToCall']) );
    $options['payuRedirect_password'] = get_option('payuRedirect_password_'.strtolower($options['payuRedirect_systemToCall']) );
    $options['payuRedirect_enableLogging'] = get_option('payuRedirect_enableLogging');
    $options['payuRedirect_enableExtendedDebug'] = get_option('payuRedirect_enableExtendedDebug');
    $options['payuRedirect_selectedCurrency'] = get_option('payuRedirect_selectedCurrency');
    $options['payuRedirect_defaultOrderNumberPrepend'] = get_option('payuRedirect_defaultOrderNumberPrepend');
    
    if(empty($options['payuRedirect_currency'])) {
        $options['payuRedirect_selectedCurrency'] = $payuWpConfig['PayuRedirectPaymentPage']['payuRedirect_supportedCurrencies'];
    }
    if(empty($options['payuRedirect_defaultOrderNumberPrepend'])) {
        $options['payuRedirect_defaultOrderNumberPrepend'] = $payuWpConfig['PayuRedirectPaymentPage']['payuRedirect_defaultOrderNumberPrepend'];
    }
    
    $textBoxReadonly = "";
    if(strtolower($options['payuRedirect_systemToCall']) == "staging") {
        $textBoxReadonly = 'readonly="readonly"';
        $options['payuRedirect_enableLogging'] = "TRUE";
        $options['payuRedirect_enableExtendedDebug'] = "TRUE";
    }
    
    $options['payuRedirect_transactionType'] = get_option('payuRedirect_transactionType' );
    $options['payuRedirect_paymentMethod'] = get_option('payuRedirect_paymentMethod' );
	$options['payuRedirect_cancelURL'] = get_option('payuRedirect_cancelURL' );
    $options['payuRedirect_notificationURL'] = get_option('payuRedirect_notificationURL' );
	$options['payuRedirect_returnURL'] = get_option('payuRedirect_returnURL');
    
    
    if(empty($options['receiptURL'])) {
        $options['receiptURL'] = get_option('transact_url');
    }
    if(empty($options['payuRedirect_returnURL'])) {
        $options['payuRedirect_returnURL'] = get_option('transact_url');
    }
    
    if(empty($options['payuRedirect_enableLogging'])) {
        $options['payuRedirect_enableLogging'] = "FALSE";    
    }
    if(empty($options['payuRedirect_enableExtendedDebug'])) {
        $options['payuRedirect_enableExtendedDebug'] = "FALSE";    
    }
    
    //----------------------------------------------------------
    //-----   Generate form for admin console
    //----------------------------------------------------------        
    $output ='';
    $output .='<input type="hidden" id="payuRedirect_safekey_production_id" name="_hidden_payuRedirect_safekey" value="'.get_option('payuRedirect_safekey_production').'" />';
    $output .='<input type="hidden" id="payuRedirect_safekey_staging_id" name="_hidden_payuRedirect_safekey_staging" value="'.$payuWpConfig['PayuRedirectPaymentPage']['payuRedirect_safekey'].'" />';
    
    $output .='<input type="hidden" id="payuRedirect_username_production_id" name="_hidden_payuRedirect_username_production" value="'.get_option('payuRedirect_username_production').'" />';
    $output .='<input type="hidden" id="payuRedirect_username_staging_id" name="_hidden_payuRedirect_username_staging" value="'.$payuWpConfig['PayuRedirectPaymentPage']['payuRedirect_username'].'" />';
    
    $output .='<input type="hidden" id="payuRedirect_password_production_id"name="_hidden_payuRedirect_password_production" value="'.get_option('payuRedirect_password_production').'" />';
    $output .='<input type="hidden" id="payuRedirect_password_staging_id" name="_hidden_payuRedirect_password_staging" value="'.$payuWpConfig['PayuRedirectPaymentPage']['payuRedirect_password'].'" />';
    
    $output .='<input type="hidden" id="payuRedirect_enableLogging_production_id" name="_hidden_payuRedirect_enableLogging_production" value="FALSE" />';
    $output .='<input type="hidden" id="payuRedirect_enableLogging_staging_id" name="_hidden_payuRedirect_enableLogging_staging" value="TRUE" />';
    
    $output .='<input type="hidden" id="payuRedirect_enableExtendedDebug_production_id" name="_hidden_payuRedirect_enableExtendedDebug_production" value="FALSE" />';
    $output .='<input type="hidden" id="payuRedirect_enableExtendedDebug_staging_id" name="_hidden_payuRedirect_enableExtendedDebug_staging" value="TRUE" />';
            
    $output .='<script type="text/javascript">
                    function whichSystemToCallOnChange(nameOfSystem) {
                        var nameOfSystemLower = nameOfSystem.toLowerCase();
                        document.getElementById("payuRedirect_safekey_id").value = document.getElementById("payuRedirect_safekey_"+nameOfSystemLower+"_id").value;
                        document.getElementById("payuRedirect_username_id").value = document.getElementById("payuRedirect_username_"+nameOfSystemLower+"_id").value;
                        document.getElementById("payuRedirect_password_id").value = document.getElementById("payuRedirect_password_"+nameOfSystemLower+"_id").value;
                        document.getElementById("payuRedirect_enableLogging_id").value = document.getElementById("payuRedirect_enableLogging_"+nameOfSystemLower+"_id").value;
                        document.getElementById("payuRedirect_enableExtendedDebug_id").value = document.getElementById("payuRedirect_enableExtendedDebug_"+nameOfSystemLower+"_id").value
                        
                        if(nameOfSystemLower == "staging") {
                            document.getElementById("payuRedirect_safekey_id").readOnly = true;
                            document.getElementById("payuRedirect_username_id").readOnly = true;
                            document.getElementById("payuRedirect_password_id").readOnly = true;
                        }                        
                        else {
                            document.getElementById("payuRedirect_safekey_id").readOnly = false;
                            document.getElementById("payuRedirect_username_id").readOnly = false;
                            document.getElementById("payuRedirect_password_id").readOnly = false;
                        }
                    }
               </script>
                ';
    
	
	$options['currency'] = get_option('safeshop_Currency_pro');
	
	$output .='<tr><td colspan=2><br></td></tr>';
	$output .='<tr><td colspan=2><center><a href="http://www.payu.co.za" target="_blank"><img src="https://www.payu.co.za/payu/wp-content/themes/payu/images/payu_plugins_logo.png" width="146" ></a></center></td></tr>';
	$output .='<tr><td colspan=2><br></td></tr>';
    $output .='<tr><td colspan=2><br></td></tr>';

    //----------------------------------------------------------
    //-----   Which system to call - production or staging
    //----------------------------------------------------------
    $tempArray = array("Production", "Staging");
    $output.='<tr><td><label for="payuRedirect_systemToCall">Staging/Production<font color="red">*</font></label></td>';
    $output.='<td>';        
    $output.='<select value="'. $options['payuRedirect_systemToCall'] .'" name="payuRedirect_systemToCall" onChange="whichSystemToCallOnChange(this.options[selectedIndex].value);" style="width:100%">';        
    
    for($i=0; $i<sizeof($tempArray); $i++) {
		if($options['payuRedirect_systemToCall'] == $tempArray[$i]) {
			$output.="<option selected value='$tempArray[$i]'>$tempArray[$i]</option>";			
		}
		else {
			$output.="<option value='$tempArray[$i]'>$tempArray[$i]</option>";
		}
	}
    $output.='</select>';
    $output.='</td></tr>';        

    //----------------------------------------------------------
	//--------- SafeKey
    //----------------------------------------------------------
	$output.='<tr><td><label for="payuRedirect_safekey" title="Safekey (provided by PayU to merchant)">SafeKey<font color="red">*</font></label></td>';
	$output.='<td><input id="payuRedirect_safekey_id" name="payuRedirect_safekey" type="text" value="'. $options['payuRedirect_safekey'] .'" '.$textBoxReadonly.' style="width:100%"/></td></tr>';
	
    //----------------------------------------------------------
	//--------- Username
    //----------------------------------------------------------
	$output.='<tr><td><label for="payuRedirect_username" title="Username used in SOAP requests (provided by PayU to merchant)">SOAP Username<font color="red">*</font></label></td>';
	$output.='<td><input id="payuRedirect_username_id" name="payuRedirect_username" type="text" value="'. $options['payuRedirect_username'] .'" '.$textBoxReadonly.' style="width:100%"/></td></tr>';
	
    //----------------------------------------------------------
	//--------- Password
    //----------------------------------------------------------
	$output.='<tr><td><label for="payuRedirect_password" title="Password used in SOAP requests (provided by PayU to merchant)">SOAP Password<font color="red">*</font></label></td>';
	$output.='<td><input id="payuRedirect_password_id" name="payuRedirect_password" type="password" value="'. $options['payuRedirect_password'] .'" '.$textBoxReadonly.' style="width:100%"/></td></tr>';
	
    //----------------------------------------------------------
	//--------- Transaction Type
    //----------------------------------------------------------
    $output.='<tr><td><label for="payuRedirect_paymentMethod" title="Transaction type used in PayU transactions">Transaction Type<font color="red">*</font></label></td>';
	$output.='<td><input name="payuRedirect_transactionType" type="text" value="PAYMENT" style="width:100%" readonly/></td></tr>';
	
	
    //----------------------------------------------------------
	//--------- Payment Method
    //----------------------------------------------------------
	$output.='<tr><td><label for="payuRedirect_paymentMethod" title="Payment method used in PayU transactions">Payment methods<font color="red">*</font></label></td>';
	$output.='<td><input name="payuRedirect_paymentMethod" type="text" value="CREDITCARD" style="width:100%" readonly/></td></tr>';
    
    //----------------------------------------------------------
	//--------- Enable logging
    //----------------------------------------------------------
	$output.='<tr><td><label for="payuRedirect_paymentMethod" title="Log SOAP requests/responses to PayU">Enable Logging<font color="red">*</font></label></td>';
	$output.='<td><input id="payuRedirect_enableLogging_id" name="payuRedirect_enableLogging" type="text" value="'. $options['payuRedirect_enableLogging'] .'" readonly="readonly" style="width:100%"/></td></tr>';
    
    //----------------------------------------------------------
	//--------- Enable Extended Debudding  Info
    //----------------------------------------------------------
	$output.='<tr><td><label for="payuRedirect_paymentMethod" title="Log SOAP requests/responses to PayU">Enable Extended Debug<font color="red">*</font></label></td>';
	$output.='<td><input id="payuRedirect_enableExtendedDebug_id" name="payuRedirect_enableExtendedDebug" type="text" value="'. $options['payuRedirect_enableExtendedDebug'] .'" readonly="readonly" style="width:100%"/></td></tr>';
	
    //----------------------------------------------------------
	//--------- CURRENCY
    //----------------------------------------------------------
	$output.='<tr><td><label for="payuRedirect_returnURL" title="Currency code used in PayU transactions">Billing Currency:<font color="red">*</font></label></td>';
	$output.='<td><input name="payuRedirect_selectedCurrency" type="text" value="'. $options['payuRedirect_selectedCurrency'] .'" style="width:100%" readonly="readonly" /></td></tr>';
    
    //----------------------------------------------------------
	//--------- Order number prepend
    //----------------------------------------------------------
	$output.='<tr><td><label for="payuRedirect_returnURL" title="The prepend an order number with this value">Order number prepend:<font color="red">*</font></label></td>';
	$output.='<td><input name="payuRedirect_defaultOrderNumberPrepend" type="text" value="'. $options['payuRedirect_defaultOrderNumberPrepend'] .'" style="width:100%"  /></td></tr>';
    
    //----------------------------------------------------------
	//--------- Return URL
    //----------------------------------------------------------
	$output.='<tr><td><label for="payuRedirect_returnURL" title="PayU will redirect to this URL once payment process is complete">Return URL:<font color="red">*</font></label></td>';
	$output.='<td><input name="payuRedirect_returnURL" type="text" value="'. $options['payuRedirect_returnURL'] .'" style="width:100%" /></td></tr>';
    
    //----------------------------------------------------------
	//--------- Cancel URL
    //----------------------------------------------------------
	$output.='<tr><td><label for="payuRedirect_cancelURL" title="PayU will redirect to this URL if customer clicks \'cancel\' on payment interface">Cancel URL:<font color="red">*</font></label></td>';
	$output.='<td><input name="payuRedirect_cancelURL" type="text" value="'. $options['payuRedirect_cancelURL'] .'" style="width:100%" /></td></tr>';
	/*
    //----------------------------------------------------------
	//--------- Notification URL
    //----------------------------------------------------------
	$output.='<tr><td><label for="payuRedirect_notificationURL" title="PayU will post a notification to this URL regarding">Notification URL (IPN):<font color="red">*</font></label></td>';
	$output.='<td><input name="payuRedirect_notificationURL" type="text" value="'. $options['payuRedirect_notificationURL'] .'" style="width:100%" /></td></tr>';
     * 
     */
	
    
	
	return $output;
}

function form_submit_payu_redirect(){
    /*
    Validates and stores values submitted from the function 'form_payu_redirect()'.
    */
    
    
    //Requiring the neccesary script for this function to work    
    require('library.payu/inc.wordpress/config.wordpress.php');
    
    //Always override the staging details with 
    if(strtolower($_POST['payuRedirect_systemToCall']) == 'staging') {        
        $_POST['payuRedirect_safekey']  = $payuWpConfig['PayuRedirectPaymentPage']['payuRedirect_safekey'];
        $_POST['payuRedirect_username'] = $payuWpConfig['PayuRedirectPaymentPage']['payuRedirect_username'];
        $_POST['payuRedirect_password'] = $payuWpConfig['PayuRedirectPaymentPage']['payuRedirect_password'];
    }
    
    $_POST['payuRedirect_safekey_'.strtolower($_POST['payuRedirect_systemToCall'])] = $_POST['payuRedirect_safekey'];
    $_POST['payuRedirect_username_'.strtolower($_POST['payuRedirect_systemToCall'])] = $_POST['payuRedirect_username'];
    $_POST['payuRedirect_password_'.strtolower($_POST['payuRedirect_systemToCall'])] = $_POST['payuRedirect_password'];
    
    foreach($_POST as $key => $value) {
        if( (!is_array($value))  ) {
            $tempArray = explode('_',$key,2);
            if($tempArray[0] == 'payuRedirect') {                
                update_option($key,$value);
            }            
        }        
    }    
    	
	return true;
}

// ************************************************************************************************
function gateway_payu_redirect($seperator, $sessionid){    
    
    /*
	This function prepares the data and sends order details to the PayU Redirect form.
	*/ 
    
    global $wpdb, $wpsc_cart;
    require('library.payu/classes/class.PayuRedirectPaymentPage.php');
    
	$_SESSION['payuOrderSessionId'] = $sessionid;
	
    //Get the redirect URL
    $transact_url = get_option('payuRedirect_returnURL'); 
    if(empty($transact_url)) {
        $tempArray = explode('?' , $_SERVER['REQUEST_URI'],2);
        $transact_url = $tempArray[0]."?page_id=7";
    }
    
    //----------------------------------------------------------
    //----- DO THE SET TRANSACTION API SOAP TO API HERE
    //----------------------------------------------------------
    
    try {
        
        //Get Purchase Log Data
        $purchase_log = $wpdb->get_row("SELECT * FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `sessionid`= ".$sessionid." LIMIT 1",ARRAY_A) ;
        $MerchantReference = get_option('payuRedirect_defaultOrderNumberPrepend').$purchase_log['id'];
        $MerchantOrderNr = '';
        
        //This grabs the users info using the $purchase_log from the previous SQL query
        $usersql = "SELECT `".WPSC_TABLE_SUBMITED_FORM_DATA."`.value,
                    `".WPSC_TABLE_CHECKOUT_FORMS."`.`name`,
                    `".WPSC_TABLE_CHECKOUT_FORMS."`.`unique_name` FROM
                    `".WPSC_TABLE_CHECKOUT_FORMS."` LEFT JOIN
                    `".WPSC_TABLE_SUBMITED_FORM_DATA."` ON
                    `".WPSC_TABLE_CHECKOUT_FORMS."`.id =
                    `".WPSC_TABLE_SUBMITED_FORM_DATA."`.`form_id` WHERE
                    `".WPSC_TABLE_SUBMITED_FORM_DATA."`.`log_id`=".$purchase_log['id']."
                    ";        
        $customerSubmittedInfo = $wpdb->get_results($usersql, ARRAY_A);
        
                
        //Generates customer inital form data array
        $customerDataArray = array();
        foreach((array)$customerSubmittedInfo as $key => $value){
            if(($value['unique_name']=='billingfirstname') && $value['value'] != '') {
                $customerDataArray['billFirstName']	= $value['value'];
            }
            if(($value['unique_name']=='billinglastname') && $value['value'] != '') {
                $customerDataArray['billLastName']	= $value['value'];
            }
            if(($value['unique_name']=='billingaddress') && $value['value'] != '') {
                $customerDataArray['billAddress']	= $value['value'];
            }
            if(($value['unique_name']=='billingemail') && $value['value'] != '') {
                $customerDataArray['billEmail']	= $value['value'];
            }
            if(($value['unique_name']=='billingphone') && $value['value'] != '') {
                $customerDataArray['billPhone']	= $value['value'];
            }
            if(($value['unique_name']=='shippingfirstname') && $value['value'] != '') { 
                $customerDataArray['shipFirstName']	= $value['value'];
            }
            if(($value['unique_name']=='shippinglastname') && $value['value'] != '') {
                $customerDataArray['shipLastName']	= $value['value'];
            }
            if(($value['unique_name']=='shippingphone') && $value['value'] != '') {
                $customerDataArray['shipPhone']	= $value['value'];
            }
            if(($value['unique_name']=='shippingemail') && $value['value'] != '') {
                $customerDataArray['shipEmail']	= $value['value'];
            }
            if(($value['unique_name']=='shippingaddress') && $value['value'] != '') {
                $customerDataArray['shipAddress']	= $value['value'];
            }
        }        
        
        //Ordered Products
        $orderedProductData = array();
        foreach($wpsc_cart->cart_items as $i => $Item) {
            $tempArray = array();
            $tempArray['name'] = $Item->product_name;
            $tempArray['amountInCents'] = ($Item->unit_price * 100);
            $tempArray['number']	= $i;
            $tempArray['quantity'] = $Item->quantity;
            $tempArray['taxAmountInCents']	= ($Item->tax * 100);
            $orderedProductData[$i] = $tempArray;
        }
        
        $setTransactionSoapDataArray = array();
        $setTransactionSoapDataArray['Safekey'] = get_option('payuRedirect_safekey');
        $setTransactionSoapDataArray['TransactionType'] = get_option('payuRedirect_transactionType');
        
        // Creating Basket Array
        $basketArray = array();
        $basketArray['amountInCents'] = $wpsc_cart->total_price*100;
        $basketArray['description'] = $MerchantReference;               
        $basketArray['currencyCode'] = get_option('payuRedirect_selectedCurrency');
        $setTransactionSoapDataArray = array_merge($setTransactionSoapDataArray, array('Basket' => $basketArray ));
        $basketArray = null; unset($basketArray);

        
        // Creating Customer Array
        $customerSubmitArray = array();
        if(isset($customerDataArray['billFirstName'])) {
            $customerSubmitArray['firstName'] = $customerDataArray['billFirstName'];
        }
        else {
            $customerSubmitArray['firstName'] = $customerDataArray['shipFirstName'];
        }
        
        if(isset($customerDataArray['billLastName'])) {
            $customerSubmitArray['lastName'] = $customerDataArray['billLastName'];
        }
        else {
            $customerSubmitArray['lastName'] = $customerDataArray['shipLastName'];
        }
        
        if(isset($customerDataArray['billPhone'])) {
            $customerSubmitArray['mobile'] = $customerDataArray['billPhone'];
        }
        else {
            $customerSubmitArray['mobile'] = $customerDataArray['shipPhone'];
        }
        
        if(isset($customerDataArray['billEmail'])) {
            $customerSubmitArray['email'] = $customerDataArray['billEmail'];
        }
        else {
            $customerSubmitArray['email'] = $customerDataArray['shipEmail'];
        }
        
        //$customerArray['regionalId'] = ''; - 
        //$customerArray['merchantUserId'] = ''; - dont have a merchant user id here        
        $setTransactionSoapDataArray = array_merge($setTransactionSoapDataArray, array('Customer' => $customerSubmitArray ));
        $customerSubmitArray = null; unset($customerSubmitArray);
        
        
        //Creating Additional Information Array
        $additionalInformationArray = array();
        $additionalInformationArray['supportedPaymentMethods'] = get_option('payuRedirect_paymentMethod');
        $additionalInformationArray['cancelUrl'] = get_option('payuRedirect_cancelURL' );
        //$additionalInformationArray['notificationUrl'] = get_option('payuRedirect_notificationURL' );
        $additionalInformationArray['returnUrl'] = get_option('payuRedirect_returnURL');
        $additionalInformationArray['merchantReference'] = $MerchantReference;
        $setTransactionSoapDataArray = array_merge($setTransactionSoapDataArray, array('AdditionalInformation' => $additionalInformationArray ));
        $additionalInformationArray = null; unset($additionalInformationArray);
        
        //Creating a constructor array for RPP instantiation
        $constructorArray = array();
        $constructorArray['username'] = get_option('payuRedirect_username');
        $constructorArray['password'] = get_option('payuRedirect_password');
        $constructorArray['logEnable'] = get_option('payuRedirect_enableLogging');
        $constructorArray['extendedDebugEnable'] = get_option('payuRedirect_enableExtendedDebug');
        
        
        if(strtolower($constructorArray['logEnable']) == "true") {
            $constructorArray['logEnable'] = true;
        } 
        else {
            $constructorArray['logEnable'] = false;
        }
        if(strtolower($constructorArray['extendedDebugEnable']) == "true") {
            $constructorArray['extendedDebugEnable'] = true;
        }
        else {
            $constructorArray['extendedDebugEnable'] = false;
        }
        if(strtolower(get_option('payuRedirect_systemToCall')) == 'production') {
            $constructorArray['production'] = true;
        }
        
        $payuRppInstance = new PayuRedirectPaymentPage($constructorArray);
        $setTransactionResponse = $payuRppInstance->doSetTransactionSoapCall($setTransactionSoapDataArray);
         
		 
        if(isset($setTransactionResponse['redirectPaymentPageUrl'])) {
            
            if( isset($setTransactionResponse['soapResponse']['payUReference'])) {
                $setTransactionNotes = "PayU Reference: ".$setTransactionResponse['soapResponse']['payUReference'];
                $sql = "UPDATE `".WPSC_TABLE_PURCHASE_LOGS.	"` SET `notes`= '".$setTransactionNotes."' WHERE `sessionid`=".$sessionid; 
                $wpdb->query($sql);                 
            }
            header('Location: '.$setTransactionResponse['redirectPaymentPageUrl']);
            die();
        }
        else {
            $_SESSION['WpscGatewayErrorMessage'] = __('Invalid reponse from payment gateway. Please retry payment'); 
                  
            header("Location: ".$transact_url);
            die();
        }        
    }
    catch(Exception $e) {
        
        //Place error code here 
        $exceptionErrorString = $e->getMessage(); 
        if(!empty($exceptionErrorString)) {
            $errorMessage = ' - '.$exceptionErrorString."<br /><br />";
        }
        $_SESSION['WpscGatewayErrorMessage'] = __('Sorry, payment for <b>Ref: '.$MerchantReference .'</b> was unsuccessfull.'.$errorMessage); 
        
        header("Location: ".$transact_url);
        die();
    }  
}



function nzshpcrt_payuredirect_callback(){
	
    
    if(isset($_GET['PayUReference']) && !empty($_GET['PayUReference'])) {                
        
        //global $wpdb;
		global $wpdb, $wpsc_cart;
        
        //Get the redirect URL
        $transact_url = get_option('payuRedirect_returnURL'); 
        if(empty($transact_url)) {
            $tempArray = explode('?' , $_SERVER['REQUEST_URI'],2);
            $transact_url = $tempArray[0]."?page_id=7";                        
        }
        
        //Requiring the neccesary script for this function to work            
        require('library.payu/classes/class.PayuRedirectPaymentPage.php');
        		
        //$sessionid = $_SESSION['wpsc_sessionid'];
		$sessionid = $_SESSION['payuOrderSessionId'];
		
		//print "<pre>";
		//var_dump($_SESSION);
		//var_dump($wpsc_cart);
		//die();
        
        //----------------------------------------------------------
        //----- DO THE GET TRANSACTION API SOAP TO API HERE
        //----------------------------------------------------------
 
        //Setting a default failed trasaction state for this trasaction
        $transactionState = "failure";    
        try {
            
            //Creating get transaction soap data array
            $getTransactionSoapDataArray = array();
            $getTransactionSoapDataArray['Safekey'] = get_option('payuRedirect_safekey');
            $getTransactionSoapDataArray['AdditionalInformation']['payUReference'] = $_GET['PayUReference'];        

            //Creating constructor array for the payURedirect and instantiating 
            $constructorArray = array();
            $constructorArray['username'] = get_option('payuRedirect_username');
            $constructorArray['password'] = get_option('payuRedirect_password');    
            $constructorArray['logEnable'] = (bool) get_option('payuRedirect_enableLogging');
            $constructorArray['extendedDebugEnable'] = get_option('payuRedirect_enableExtendedDebug');
            if(strtolower($constructorArray['logEnable']) == "true") {
                $constructorArray['logEnable'] = true;
            } 
            else {
                $constructorArray['logEnable'] = false;
            }
            if(strtolower($constructorArray['extendedDebugEnable']) == "true") {
                $constructorArray['extendedDebugEnable'] = true;
            } 
            else {
                $constructorArray['extendedDebugEnable'] = false;
            }

            if(strtolower(get_option('payuRedirect_systemToCall')) == 'production') {
                $constructorArray['production'] = true;
            }    
            $payuRppInstance = new PayuRedirectPaymentPage($constructorArray);
            $getTransactionResponse = $payuRppInstance->doGetTransactionSoapCall($getTransactionSoapDataArray); 
            
            //Set merchant reference
            if( isset($getTransactionResponse['soapResponse']['merchantReference']) && !empty($getTransactionResponse['soapResponse']['merchantReference']) ) {
                $MerchantReference = $getTransactionResponse['soapResponse']['merchantReference'];
            }
            
            //Checking the response from the SOAP call to see if successfull
            if(isset($getTransactionResponse['soapResponse']['successful']) && ($getTransactionResponse['soapResponse']['successful']  === true)) {

                if(isset($getTransactionResponse['soapResponse']['transactionType']) && (strtolower($getTransactionResponse['soapResponse']['transactionType']) == 'reserve') ) {
                    if(isset($getTransactionResponse['soapResponse']['transactionState']) && (strtolower($getTransactionResponse['soapResponse']['transactionState']) == 'successful') ) {                    
                        $transactionState = "reserve"; //funds reserved need to finalize in the admin box                    
                    }            
                }
                if(isset($getTransactionResponse['soapResponse']['transactionType']) && (strtolower($getTransactionResponse['soapResponse']['transactionType']) == 'payment') ) {                    
                    if(isset($getTransactionResponse['soapResponse']['transactionState']) && (strtolower($getTransactionResponse['soapResponse']['transactionState']) == 'successful') ) {                    
                        $transactionState = "paymentSuccessfull"; //funds reserved need to finalize in the admin box
                    }            
                }            
                else {
                    $errorMessage = $getTransactionResponse['soapResponse']['displayMessage'];
                }
            }
            else {
                $errorMessage = $getTransactionResponse['soapResponse']['displayMessage'];
            }
        }
        catch(Exception $e) {
            $errorMessage = $e->getMessage();            
        }    
        
        // resetting error message if it existed for the session
        if(isset($_SESSION['WpscGatewayErrorMessage'])) {
            unset($_SESSION['WpscGatewayErrorMessage']);
        }
        
		//var_dump($sessionid);
		//var_dump($transactionState);
		//die();
		
        //Now doing db updates for the orders 
        if( ($transactionState == "paymentSuccessfull") && (!empty($sessionid)) )
        {
            //Payment Successful
            //redirect to  transaction page and store in DB as a order with accepted payment
            $transactionNotes = "PayU Reference: ".$getTransactionResponse['soapResponse']['payUReference'].", GatewayReference: ".$getTransactionResponse['soapResponse']['paymentMethodsUsed']['gatewayReference'];            
            $sql = "UPDATE `".WPSC_TABLE_PURCHASE_LOGS.	"` SET `processed`= '3',`notes`= '".$transactionNotes."' WHERE `sessionid`=".$sessionid;
            $wpdb->query($sql);	  
            
            $urlJoinString = '?';
            if (strpos($transact_url, '?') !== false) {
                $urlJoinString = "&";
            }
            header("Location: ".$transact_url.$urlJoinString."sessionid=".$sessionid);
            die();
        }    
        else if($transactionState == "failure")
        {
            //Payment Failed
            //redirect back to checkout page with errors             
            $transactionNotes = "PayU Reference: ".$getTransactionResponse['soapResponse']['payUReference'].", Error: ".addslashes($errorMessage).", Point Of Failure: ".$getTransactionResponse['soapResponse']['pointOfFailure'].", Result Code:".$getTransactionResponse['soapResponse']['resultCode'] ;            
            $sql = "UPDATE `".WPSC_TABLE_PURCHASE_LOGS.	"` SET `processed`= '6',`notes`= '".$transactionNotes."' WHERE `sessionid`=".$sessionid;                        
            $wpdb->query($sql);  
            
            if(!empty($errorMessage)) {
                $errorMessage = ' - '.$errorMessage."<br /><br />";
            }
            
            $_SESSION['WpscGatewayErrorMessage'] = __('Sorry, payment for <b>Ref: '.$MerchantReference .'</b> was unsuccessfull.'.$errorMessage); 
            $transact_url = get_option('shopping_cart_url');
            header("Location: ".$transact_url);
            die();
        }
        die();
    }
}

// Callback function - used when returning from PayU RPP
add_action( 'init', 'nzshpcrt_payuredirect_callback' );