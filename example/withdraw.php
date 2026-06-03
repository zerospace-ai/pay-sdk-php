<?php
require __DIR__.'/../vendor/autoload.php';

use Cryptopay\Chain\CryptoPay;

$config = [
    'ApiKey' => 'your_api_key',
    'ApiSecret' => 'your_api_secret',
    'RsaPrivateKey' => 'your_rsa_private_key',
    'PlatformPubKey' => 'platform_public_key',
];

$cryptoPay = new CryptoPay($config);

$open_id = 'project1757162715';
$token_id = 4;
$amount = 1;
$address = 'TQ33qyLenhYxqMDPtVwdS92UhZwRWdD1VL';
$callback_url = '';
$sn = '202408080808081';

$result = $cryptoPay->withdraw($open_id, $token_id, $amount, $address, $callback_url, $sn);

print_r($result);
echo "\n";


