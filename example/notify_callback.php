<?php
require __DIR__.'/../vendor/autoload.php';

use Cryptopay\Chain\CryptoPay;

$postData = file_get_contents('php://input');
if(!$postData){
    exit;
}
$postData = json_decode($postData, true);

$config = [
    'ApiKey' => 'your_api_key',
    'ApiSecret' => 'your_api_secret',
    'RsaPrivateKey' => 'your_rsa_private_key',
    'PlatformPubKey' => 'platform_public_key',
];

$cryptoPay = new CryptoPay($config);

if(!$cryptoPay->verifyRsaSignature($postData)){
    //Failed to verify signature
    exit;
}

//The signature verification is successful, and the following business logic is processed
if($postData['type'] == 1){
    //Recharge transaction

}elseif($postData['type'] == 2){
    //Withdrawal transaction

}else{
    //Type Error
    exit;
}


$response = [
    'code' => 0,
    'msg' => 'ok',
    'data' => null,
];
exit(json_encode($response));