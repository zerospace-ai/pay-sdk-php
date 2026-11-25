<?php
require __DIR__ . '/../vendor/autoload.php';

use Cryptopay\Chain\Client;
use Cryptopay\Chain\CryptoPay;

function assertEquals($expected, $actual, $label) {
    if ($expected !== $actual) {
        fwrite(STDERR, "FAIL [{$label}]: expected " . var_export($expected, true) . ", got " . var_export($actual, true) . "\n");
        exit(1);
    }
}

// Generate temporary RSA keypair
$res = openssl_pkey_new([
    'private_key_bits' => 2048,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
]);
openssl_pkey_export($res, $privPem);
$pubDetails = openssl_pkey_get_details($res);
$pubPem = $pubDetails['key'];

$config = [
    'ApiKey' => 'your_api_key',
    'ApiSecret' => 'your_api_secret',
    'RsaPrivateKey' => $privPem,
    'PlatformPubKey' => $pubPem,
];

$cryptoPay = new CryptoPay($config);
$client = new Client($config);

// 1. Official MD5 vector with all 6 fields
$payload = $cryptoPay->buildNewOrderPayload(
    '1234623',
    6,
    0.01,
    'https://your-domain.com/callback',
    'user_10086',
    'VIP会员充值'
);
assertEquals(6, $payload['tokenId'], 'tokenId int');
assertEquals('user_10086', $payload['outUserId'], 'outUserId');
assertEquals('VIP会员充值', $payload['orderDetail'], 'orderDetail');

$signStr = Client::getSignString($payload);
assertEquals(
    'notifyUrl=https://your-domain.com/callback&orderDetail=VIP会员充值&outOrderNo=1234623&outUserId=user_10086&quantity=0.01&tokenId=6',
    $signStr,
    'getSignString full'
);
$md5Sign = $client->sign($payload, '1770606706659');
assertEquals('1b5b885633560f447f38073f964ad51b', $md5Sign, 'official md5 vector');

$rsaSign = $client->encryption($payload);
assertEquals(true, $client->checkSignature($payload, $rsaSign), 'rsa verify full');

// 2. Address-pool mode omitting optional fields
$minPayload = $cryptoPay->buildNewOrderPayload('1234623', '6', '0.01', '', null, '');
assertEquals(false, array_key_exists('outUserId', $minPayload), 'omit outUserId');
assertEquals(false, array_key_exists('notifyUrl', $minPayload), 'omit notifyUrl');
assertEquals(false, array_key_exists('orderDetail', $minPayload), 'omit orderDetail');
assertEquals(6, $minPayload['tokenId'], 'cast tokenId int');
assertEquals('outOrderNo=1234623&quantity=0.01&tokenId=6', Client::getSignString($minPayload), 'getSignString minimal');

$minRsaSign = $client->encryption($minPayload);
assertEquals(true, $client->checkSignature($minPayload, $minRsaSign), 'rsa verify minimal');

echo "PHP newOrder tests passed!\n";
