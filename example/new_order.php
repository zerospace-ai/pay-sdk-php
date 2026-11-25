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

    $outOrderNo = 'order_' . time();
    $outUserId = 'user_10086'; // Required in fixed-address mode; pass null only if address-pool mode is enabled
    $tokenId = 6; // USDT-BSC
    $quantity = 0.01;
    $notifyUrl = 'https://your-domain.com/callback/cashier';
    $orderDetail = 'VIP membership recharge';

    echo "Creating new cashier order...\n";
    $result = $cryptoPay->newOrder($outOrderNo, $tokenId, $quantity, $notifyUrl, $outUserId, $orderDetail);
    
    print_r($result);
    echo "\n";

    $postData = json_decode($result, true);

    if (!$postData || $postData['code'] != 1) {
        $msg = isset($postData['msg']) ? $postData['msg'] : 'Unknown error';
        die('Response fail. Code: ' . (isset($postData['code']) ? $postData['code'] : 'N/A') . ' Msg: ' . $msg . "\n");
    }

    if (!$cryptoPay->verifyRsaSignature($postData)) {
        die("verifyRsaSignature fail\n");
    }

    echo "verifyRsaSignature success\n";
    echo "Order No: " . $postData['data']['orderNo'] . "\n";
    echo "Deposit Address: " . ($postData['data']['addresses'] ?? '') . "\n";
    echo "Cashier URL: " . ($postData['data']['cashierUrl'] ?? '') . "\n";
}

main();
