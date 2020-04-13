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

/* * ************************************************************ */
/* * ************************************************************ */
/* * ************************************************************ */
$plainText = '<?xml version="1.0" encoding="UTF-8"?><billFetchRequest>
   <agentId>CC01CC01513515340681</agentId>
   <agentDeviceInfo>
      <ip>1156.67.222.206</ip>
      <initChannel>AGT</initChannel>
      <mac>01-23-45-67-89-ab</mac>
   </agentDeviceInfo>
   <customerInfo>
      <customerMobile>9898990084</customerMobile>
      <customerEmail></customerEmail>
      <customerAdhaar></customerAdhaar>
      <customerPan></customerPan>
   </customerInfo>
   <billerId>OTME00005XXZ43</billerId>
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
echo "<pre>";
echo htmlentities($response);
exit;
?>
