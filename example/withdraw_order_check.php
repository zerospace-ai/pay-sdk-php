<?php
require __DIR__.'/../vendor/autoload.php';

use Cryptopay\Chain\CryptoPay;
use Cryptopay\Chain\Client;

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
    'PlatformRiskPubKey' => 'platform_risk_public_key',
];

$cryptoPay = new CryptoPay($config);

if(!$cryptoPay->verifyWithdrawRsaSignature($postData)){
    //Failed to verify signature
    exit;
}


//Verify the order information requested by the platform here. If the information is correct, the corresponding information will be as follows
$response = [
    'code' => 0,
    'timestamp' => time(),
    'message' => '',
];
$client = new Client($config);
$sign = $client->encryption($response);
$response['sign'] = $sign;

exit(json_encode($response));