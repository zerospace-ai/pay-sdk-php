# Code Examples and Tools

This document is divided into two parts:
1. **Scenario-based Code Examples:** Demonstrates how to handle API calls and verification in actual code.
2. **CLI Tools Guide:** Explains how to use the scripts included with the SDK for quick testing.

---

## 1. Scenario-based Code Examples

### 1.1 Complete API Call and Response Verification

The following code shows how to use the SDK to construct a "Create User" request, send an HTTP request, and perform security verification on the data signature returned by the platform.

```php
<?php
require __DIR__.'/../vendor/autoload.php';

use Cryptopay\Chain\CryptoPay;

function main() {
    // 1. Initialize configuration
    $config = [
        'ApiKey' => 'your_api_key',
        'ApiSecret' => 'your_api_secret',
        'RsaPrivateKey' => 'your_rsa_private_key',
        'PlatformPubKey' => 'platform_public_key',
    ];

    // 2. Create SDK instance
    $cryptoPay = new CryptoPay($config);

    // 3. Call API: Create User
    $openId = 'php_user_' . time();
    $result = $cryptoPay->createUser($openId);

    if (!$result) {
        echo "Request failed\n";
        return;
    }

    // 4. Parse and verify response
    $postData = json_decode($result, true);

    if ($postData['code'] != 1) {
        echo "Response failed! Code: " . $postData['code'] . ", Msg: " . $postData['msg'] . "\n";
        return;
    }

    // Verify platform signature
    if ($cryptoPay->verifyRsaSignature($postData)) {
        echo "✅ Request successful and verified! OpenId: " . $postData['data']['OpenId'] . "\n";
    } else {
        echo "❌ Signature verification failed!\n";
    }
}

main();
```


---

## 2. CLI Tools Guide

The SDK provides command-line scripts for quick testing of various interfaces.

### 2.1 Install Dependencies

Execute the `composer install` command in the SDK root directory to install the necessary dependencies.

### 2.2 Test Interface Commands

#### Register New User
1. Modify `OpenId` in `example/create_user.php`.
2. Run `php example/create_user.php`.

#### Wallet Registration
1. Specify `UserOpenId` and `ChainID` in `example/create_wallet.php`.
2. Run `php example/create_wallet.php`.

#### Get Deposit Address
1. Specify `UserOpenId` and the `ChainIDs` to query (e.g., "1,56") in `example/get_wallet_addresses.php`.
2. Run `php example/get_wallet_addresses.php`.

#### Apply for Withdrawal
1. Specify `UserOpenId`, `TokenId`, `Amount`, `AddressTo`, `SafeCheckCode`, and `CallBackUrl` in `example/withdraw.php`.
2. Run `php example/withdraw.php`.

#### Create Cashier Order
1. Specify `outOrderNo`, `tokenId`, `quantity`, and `notifyUrl` in `example/new_order.php`.
2. Run `php example/new_order.php`.

#### Query Wallet Balance
1. Specify `address`, `contract_address`, and `chain_id` in `example/wallet_balance.php`.
2. Run `php example/wallet_balance.php`.
