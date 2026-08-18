<?php
require __DIR__.'/../vendor/autoload.php';

use Cryptopay\Chain\CryptoPay;

$config = [
    'ApiKey' => 'your_api_key',
    'ApiSecret' => 'your_api_secret',
    'RsaPrivateKey' => 'your_rsa_private_key',
    'PlatformPubKey' => 'platform_public_key',
];

function main() {
    global $config;
    $cryptoPay = new CryptoPay($config);

    $address = 'rXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
    $contract_address = 'XRP';
    $chain_id = 5;

    $result = $cryptoPay->getWalletBalance($address, $contract_address, $chain_id);
    print_r($result);
    echo "\n";

    $postData = json_decode($result, true);

    if (!$postData || $postData['code'] != 1) {
        print_r('Response fail Code: ' . ($postData['code'] ?? '') . ' Msg: ' . ($postData['msg'] ?? ''));
        return;
    }

    if (!empty($postData['sign'])) {
        if(!$cryptoPay->verifyRsaSignature($postData)){
            print_r('verifyRsaSignature fail');
            return;
        }
        print_r("verifyRsaSignature success\n");
    }

    print_r("Balance: " . ($postData['data'] ?? '') . "\n");
}

main();
