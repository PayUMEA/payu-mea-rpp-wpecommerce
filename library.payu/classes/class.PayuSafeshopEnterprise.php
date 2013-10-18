<?php

/**
 * This file contains the class for doign PayuRedirectPaymentPage transactions
 * Date:
 * 
 * @version 1.0
 * 
 * 
 */
class PayuSafeShopEnterprise {
    
    private $payuUrlArray = array( 'staging' => 'https://staging.payu.co.za' , 'production' => 'https://secure.payu.co.za'  );
    private $payuBaseUrlToUse = "";
    private $soapWdslUrl = "";    
    private $soapAuthHeader = "";
    
    private $merchantSoapUsername = "";
    private $merchantSoapPassword = "";
    private $soapApiVersion = "ONE_ZERO";
    public $soapClientInstance;
    
    
    public function __construct($constructorArray = array()) {
        
        //Setting the base url
        $this->payuBaseUrlToUse = $this->payuUrlArray['staging'];
        if(isset($constructorArray['production']) && ($constructorArray['production'] !== false) ) {
            $this->payuBaseUrlToUse = $this->payuUrlArray['production'];
        }
        
        if(isset($constructorArray['username']) && (!empty($constructorArray['username'])) ) {
            $this->merchantSoapUsername = $constructorArray['username'];
        }
        
        if(isset($constructorArray['password']) && (!empty($constructorArray['password'])) ) {
            $this->merchantSoapPassword = $constructorArray['password'];
        }
        
        //Setting the neccesary variables used in the class
        $this->setSoapWdslUrl();         
    }
    
    /**    
    *
    * Do the get transaction soap call against the PayU API and returns a url containing the RPP url with reference
    *
    * @param string soapFunctionToCall The Soap function the needs to be called
    * @param array soapDataArray The array containing the data to
    *
    * @return array Returns the set transaction response details
    */
    public function doGetTransactionSoapCall( $soapDataArray = array() ) {
        
        $returnData = $this->doSoapCallToApi('getTransaction',$soapDataArray);    
        
        $tempArray = array();
        $tempArray['soapResponse'] = $returnData['return'];
        $tempArray['redirectPaymentPageUrl'] = $this->getTransactionRedirectPageUrl($returnData['return']['payUReference']);
        return $tempArray;
        
        print "<pre>";
        var_dump($returnData);
        die();
        /*
        //If succesfull then pass back the payUreference  with return URL
        if( isset($returnData['return']['successful']) && ($returnData['return']['successful'] === true) ) {
            $returnData['return']['redirectPaymentPageUrl'] = $this->getTransactionRedirectPageUrl($returnData['return']['payUReference']);
            return $returnData['return'];
        }
        else {
            throw new exception('there was an issue here');
        }         
         */
    }
    
    
    /**    
    *
    * Do the set transaction soap call against the PayU API and returns a url containing the RPP url with reference
    *
    * @param string soapFunctionToCall The Soap function the needs to be called
    * @param array soapDataArray The array containing the data to
    *
    * @return array Returns the set transaction response details
    */
    public function doSetTransactionCall( $soapDataArray = array() ) {        
        
        $returnData = $this->doSoapCallToApi('setTransaction',$soapDataArray);        
        
        //If succesfull then pass back the payUreference  with return URL
        if( isset($returnData['return']['successful']) && ($returnData['return']['successful'] === true) ) {
            $tempArray = array();
            $tempArray['soapResponse'] = $returnData['return'];
            $tempArray['redirectPaymentPageUrl'] = $this->getTransactionRedirectPageUrl($returnData['return']['payUReference']);
            return $tempArray;
        }
        else {
            throw new exception('there was an issue here');
        }
    }
    
    /**    
    *
    * Do the soap call against the PayU API
    *
    * @param string soapFunctionToCall The Soap function the needs to be called
    * @param array soapDataArray The array containing the data to
    *
    * @return array Returns the soap result in array format
    */
    public function doSoapCallToApi( $soapFunctionToCall = null , $soapDataArray = array() ) {
        
        // A couple of validation business ruless before doing the soap call
        if(empty($soapDataArray)) {
            throw new Exception("Please provide data to be used on the soap call");
        }
        elseif(empty($soapFunctionToCall)) {
            throw new Exception("Please provide a soap function to call");
        }

        //Setting the soap header if not already set
        if(empty($this->soapAuthHeader)) {
            $this->setSoapHeader();
        }            
                
        //Make new instance of the PHP Soap client
        $this->soapClientInstance = new SoapClient($this->soapWdslUrl, array("trace" => 1, "exception" => 0)); 

        //Set the Headers of soap client. 
        $this->soapClientInstance->__setSoapHeaders($this->soapAuthHeader); 

        //Adding the api version to the soap data packet array
        $soapDataArray = array_merge($soapDataArray, array('Api' => $this->soapApiVersion ));
        
        //Do soap call
        $soapCallResult = $this->soapClientInstance->$soapFunctionToCall($soapDataArray); 
        //$soapCallResult = $this->soapClientInstance->__soapCall($soapFunctionToCall, $soapDataArray); 
        
        // Decode the Soap Call Result for returning
        $returnData = json_decode(json_encode($soapCallResult),true);

        return $returnData;
    }
    
    /**    
     * Set the soap header string used to call in the Soap to PayU API
     */        
    private function setSoapHeader() {
        
        if(empty($this->merchantSoapUsername)) {
            throw new exception('Please specify a merchant username for soap trasaction');
        }
        elseif(empty($this->merchantSoapPassword)) {
            throw new exception('Please specify a merchant password for soap trasaction');
        }
        
        //Creating a soap xml
        $headerXml = '<wsse:Security SOAP-ENV:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">';
        $headerXml .= '<wsse:UsernameToken wsu:Id="UsernameToken-9" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">';
        $headerXml .= '<wsse:Username>'.$this->merchantSoapUsername.'</wsse:Username>';
        $headerXml .= '<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$this->merchantSoapPassword.'</wsse:Password>';
        $headerXml .= '</wsse:UsernameToken>';
        $headerXml .= '</wsse:Security>';
        $headerbody = new SoapVar($headerXml, XSD_ANYXML, null, null, null);        
        
        $ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd'; //Namespace of the WS.         
        $this->soapAuthHeader = new SOAPHeader($ns, 'Security', $headerbody, true);        
    }
    
    
    /*     
     * Set the Base RPP Url to use      
     */        
    private function getTransactionRedirectPageUrl($payuReference = null) {
        if(empty($payuReference)) {
            throw new Exception('Please specify a valid payU Reference number');
        }
        return $this->payuBaseUrlToUse.'/rpp.do?PayUReference='.$payuReference;
    }

    /**    
     * Set the PayU soap WDSL URL for use in soap
     */        
    private function setSoapWdslUrl() {
        $this->soapWdslUrl = $this->payuBaseUrlToUse.'/service/PayUAPI?wsdl';        
    }

}

