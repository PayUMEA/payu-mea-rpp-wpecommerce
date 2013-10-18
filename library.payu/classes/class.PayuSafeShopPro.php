<?php

Class PayuSafeShopPro {
    
    private $merchantSafeKey = null;
    private static $payuSafeShopUrlCurlUrlArray  = array('production' => 'https://secure.safeshop.co.za/s2s/SafePay.asp');
    private static $payuSafeShopUrlHtmlUrlArray  = array('production' => 'https://secure.SafeShop.co.za/SafePay/Lite/Index.asp');
    private $payuSafeShopCurlUrlToQuery = null;
    private $payuSafeShopFormUrlToQuery = null;
    
    public function __construct($optionsArray = array()) {
        
        $this->payuSafeShopFormUrlToQuery = self::$payuSafeShopUrlHtmlUrlArray['production'];
        
        //instatiate which ss url to query
        if(isset($payuSafeShopCurlUrlToQuery['staging'])) {            
            $this->payuSafeShopFormUrlToQuery = self::$payuSafeShopUrlHtmlUrlArray['production'];
        }
        
        if(isset($optionsArray['safeKey']) && (!empty($optionsArray['safeKey'])) ) {
            $this->merchantSafeKey = $optionsArray['safeKey'];
        }
        else {
            throw new exception("please specify a merchant safeKey");
        }
        
    }

    
    public function getFormHtmlData($transactionDetailsArray = array() ) {
        
        $transactionDetailsArray['SafeKey'] = $this->merchantSafeKey;        
        $formName = "frmPay_".rand(0,100);
        $htmlString = "";
        $htmlString .= '<form action="'.$this->payuSafeShopFormUrlToQuery.'" method="post" id="'.$formName.'" name="'.$formName.'">'."\r\n";
        foreach($transactionDetailsArray as $key => $value) {
            $htmlString .= '<input type="hidden" name="'.$key.'" value="'.$value.'">'."\r\n";
        }
        $htmlString .= '</form>'."\r\n";
        
        $returnArray = array('htmlString' => $htmlString, 'formName' => $formName);
        return $returnArray; 
        /*
        <form action="https://secure.safeshop.co.za/SafePay/Lite/Index.asp" method="post" id=frmPay name=frmPay>
            <input type="hidden" name="SafeKey" value="{XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX}"> <!-- The Safe Key-->
            <input type="hidden" name="MerchantReferenceNumber" value="Strawberry_2005/03/29 09:32:57 AM"> <!-- Merchant Transaction Reference-->
            <input type="hidden" name="TransactionAmount" value="599">  <!-- Transaction Amount in Cents -->
            <input type="hidden" name="CurrencyCode" value="ZAR">  <!-- Transaction Amount in Cents -->
            <input type="hidden" name="SafeTrack" value=""> <!-- Optional - SafeTrack GUID -->
            <input type="hidden" name="ReceiptURL" value="http://strawberry.safeshop.co.za/ThankYou.asp"> <!-- Transaction Redirect Url-->
            <input type="hidden" name="FailURL" value="http://strawberry.safeshop.co.za/Failed.asp"> <!-- Transaction Failure Redirect Url-->
            <input type="hidden" name="TransactionType" value="Auth">  <!-- Transaction  Type (Auth, Auth_Settle) -->
        </form>
        */
        
    }
    
}
