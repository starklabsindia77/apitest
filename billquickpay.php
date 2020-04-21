<?php

//*********** Encryption Function *********************
function encrypt($plainText, $key) {
    $secretKey = hextobin(md5($key));
    $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
    $openMode = openssl_encrypt($plainText, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
    $encryptedText = bin2hex($openMode);
    return $encryptedText;
}

//*********** Decryption Function *********************
function decrypt($encryptedText, $key) {
    $key = hextobin(md5($key));
    $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
    $encryptedText = hextobin($encryptedText);
    $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
    return $decryptedText;
}

//*********** Padding Function *********************
function pkcs5_pad($plainText, $blockSize) {
    $pad = $blockSize - (strlen($plainText) % $blockSize);
    return $plainText . str_repeat(chr($pad), $pad);
}

//********** Hexadecimal to Binary function for php 4.0 version ********
function hextobin($hexString) {
    $length = strlen($hexString);
    $binString = "";
    $count = 0;
    while ($count < $length) {
        $subString = substr($hexString, $count, 2);
        $packedString = pack("H*", $subString);
        if ($count == 0) {
            $binString = $packedString;
        } else {
            $binString .= $packedString;
        }

        $count += 2;
    }
    return $binString;
}

//********** To generate ramdom String ********
function generateRandomString($length = 35) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function getBillfetch(){
		            $plainText = '<?xml version="1.0" encoding="UTF-8"?><billerInfoRequest><billerId>OTNS00005XXZ43</billerId></billerInfoRequest>';
                    $key = "76CA86D34787F65F6CDF86B268395B55";
					$key = "76CA86D34787F65F6CDF86B268395B55";
					$encrypt_xml_data = encrypt($plainText, $key);

					$data['accessCode'] = "AVMI42PE96HV29VNMC";
					$data['requestId'] = generateRandomString();
					$data['encRequest'] = $encrypt_xml_data;
					$data['ver'] = "1.0";
					$data['instituteId'] = "CO02";

					$parameters = http_build_query($data);

					$url = "https://stgapi.billavenue.com/billpay/extMdmCntrl/mdmRequest/xml";
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					$result = curl_exec($ch);
					//echo $result . "////////////////////";
					$response = decrypt($result, $key);
					//echo "<pre>";
					//echo htmlentities($response);
					
					
					$new = simplexml_load_string($response);
					
					$con = json_encode($new);
					
					$newArr = json_decode($con, true);
					
					if(000 == $newArr["responseCode"]){
						//print_r($newArr);
						if($newArr["biller"]["billerAdhoc"] == "true" && $newArr["biller"]["billerFetchRequiremet"] == "NOT_SUPPORTED"){
							if($newArr["biller"]["billerSupportBillValidation"] == "MANDATORY"){
								//print_r($newArr);
								getValidate($data['requestId']);
								
								//getPayment($data['requestId']);
								//getFtch($data['requestId']);
							}
							
						}
						
						if($newArr["biller"]["billerAdhoc"] == "true" && $newArr["biller"]["billerFetchRequiremet"] == "MANDATORY"){
							//getPayment($data['requestId']);
							//getFtch($data['requestId']);
						}
					}else{
						echo "error";
					}
}


function getFtch($reqid){
	$plainText = '<?xml version="1.0" encoding="UTF-8"?><billFetchRequest>
   <agentId>CC01CC01513515340681</agentId>
   <agentDeviceInfo>
      <ip>192.168.2.73</ip>
      <initChannel>AGT</initChannel>
      <mac>01-23-45-67-89-ab</mac>
   </agentDeviceInfo>
   <customerInfo>
      <customerMobile>9898990084</customerMobile>
      <customerEmail></customerEmail>
      <customerAdhaar></customerAdhaar>
      <customerPan></customerPan>
   </customerInfo>
   <billerId>OTNS00005XXZ43</billerId>
   <inputParams>
      <input>
         <paramName>a</paramName>
         <paramValue>10</paramValue>
      </input>
      <input>
         <paramName>a b</paramName>
         <paramValue>20</paramValue>
      </input>
      <input>
         <paramName>a b c</paramName>
         <paramValue>30</paramValue>
      </input>
      <input>
         <paramName>a b c d</paramName>
         <paramValue>40</paramValue>
      </input>
      <input>
         <paramName>a b c d e</paramName>
         <paramValue>50</paramValue>
      </input>
   </inputParams>
</billFetchRequest>';
$key = "76CA86D34787F65F6CDF86B268395B55";
$encrypt_xml_data = encrypt($plainText, $key);

$data['accessCode'] = "AVMI42PE96HV29VNMC";
$data['requestId'] = generateRandomString();
$data['encRequest'] = $encrypt_xml_data;
$data['ver'] = "1.0";
$data['instituteId'] = "CO02";

$parameters = http_build_query($data);

$url = "https://stgapi.billavenue.com/billpay/extBillCntrl/billFetchRequest/xml";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$result = curl_exec($ch);
echo $result . "////////////////////";
$response = decrypt($result, $key);
//echo "<pre>";
//echo htmlentities($response);

getPayment($data['requestId']);


}

function getPayment($reqid){
	
	$plainText = '<?xml version="1.0" encoding="UTF-8"?>
<billPaymentRequest>
    <agentId>CC01CC01513515340681</agentId>
    <billerAdhoc>true</billerAdhoc>
    <agentDeviceInfo>
        <ip>192.168.2.73</ip>
        <initChannel>AGT</initChannel>
        <mac>01-23-45-67-89-ab</mac>
    </agentDeviceInfo>
    <customerInfo>
        <customerMobile>9898990083</customerMobile>
        <customerEmail></customerEmail>
        <customerAdhaar></customerAdhaar>
        <customerPan></customerPan>
    </customerInfo>
    <billerId>OTNS00005XXZ43</billerId>
   <inputParams>
      <input>
         <paramName>a</paramName>
         <paramValue>10</paramValue>
      </input>
      <input>
         <paramName>a b</paramName>
         <paramValue>20</paramValue>
      </input>
      <input>
         <paramName>a b c</paramName>
         <paramValue>30</paramValue>
      </input>
      <input>
         <paramName>a b c d</paramName>
         <paramValue>40</paramValue>
      </input>
      <input>
         <paramName>a b c d e</paramName>
         <paramValue>50</paramValue>
      </input>
   </inputParams>
   <amountInfo>
       <amount>100000</amount>
       <currency>356</currency>
       <custConvFee>0</custConvFee>
       <amountTags></amountTags>
   </amountInfo>
   <paymentMethod>
       <paymentMode>Cash</paymentMode>
       <quickPay>Y</quickPay>
       <splitPay>N</splitPay>
   </paymentMethod>
   <paymentInfo>
       <info>
           <infoName>Remarks</infoName>
           <infoValue>Received</infoValue>
       </info>
   </paymentInfo>
</billPaymentRequest>';
$key = "76CA86D34787F65F6CDF86B268395B55";
$encrypt_xml_data = encrypt($plainText, $key);

$data['accessCode'] = "AVMI42PE96HV29VNMC";
$data['requestId'] = $reqid;

echo "request id: ".$data['requestId'];
echo "------------------\n";
$data['encRequest'] = $encrypt_xml_data;
$data['ver'] = "1.0";
$data['instituteId'] = "CO02";

$parameters = http_build_query($data);

$url = "https://stgapi.billavenue.com/billpay/extBillPayCntrl/billPayRequest/xml";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$result = curl_exec($ch);
echo $result . "////////////////////";
$response = decrypt($result, $key);
echo "<pre>";
echo htmlentities($response);

}

function getValidate($reqid){
	$plainText = '<?xml version="1.0" encoding="UTF-8"?><billValidationRequest>    <agentId>CC01CC01513515340681</agentId>    <billerId>OTNS00005XXZ43</billerId>    <inputParams>       <input>          <paramName>a</paramName>          <paramValue>10</paramValue>       </input>       <input>          <paramName>a b</paramName>          <paramValue>20</paramValue>       </input>       <input>          <paramName>a b c</paramName>          <paramValue>30</paramValue>       </input>       <input>          <paramName>a b c d</paramName>          <paramValue>40</paramValue>       </input>       <input>          <paramName>a b c d e</paramName>          <paramValue>50</paramValue>       </input>    </inputParams> </billValidationRequest>';
					$key = "76CA86D34787F65F6CDF86B268395B55";
					$encrypt_xml_data = encrypt($plainText, $key);

					$data['accessCode'] = "AVMI42PE96HV29VNMC";
					$data['requestId'] = generateRandomString();
					
					echo "request id:".$data['requestId'];
					$data['encRequest'] = $encrypt_xml_data;
					$data['ver'] = "1.0";
					$data['instituteId'] = "CO02";

					$parameters = http_build_query($data);

					$url = "https://stgapi.billavenue.com/billpay/extBillValCntrl/billValidationRequest/xml";
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					$result = curl_exec($ch);
					echo $result . "////////////////////";
					$response = decrypt($result, $key);
					echo "<pre>";
					echo htmlentities($response);
					
					getPayment($data['requestId']);
					/* $new = simplexml_load_string($response);
					
					$con = json_encode($new);
					
					$newArr = json_decode($con, true); */
					//print_r($newArr);
					
					
}

getBillfetch();
exit;
?>
