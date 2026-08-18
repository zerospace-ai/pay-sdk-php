# 示例代碼與工具

本文檔分為兩部分：
1. **場景化代碼示例：** 演示在實際代碼中如何處理 API 的調用與驗證。
2. **命令行工具指南：** 介紹如何使用 SDK 附帶的腳本進行快速測試。

---

## 1. 場景化代碼示例

### 1.1 完整的 API 調用與響應驗證

以下代碼展示了如何利用 SDK 構建一個“創建用戶”請求，發送 HTTP 請求，並對平台返回的數據簽名進行安全驗證。

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

## 2. 命令行工具使用指南

SDK 提供了快速測試各接口的命令行腳本。

### 2.1 安裝依賴

在 SDK 根目錄下執行 `composer install` 命令安裝必要依賴。

### 2.2 測試各接口命令

#### 註冊新用戶
1. 在 `example/create_user.php` 中修改 `OpenId`。
2. 運行 `php example/create_user.php`。

#### 錢包註冊
1. 在 `example/create_wallet.php` 中指定 `UserOpenId` 和 `ChainID`。
2. 運行 `php example/create_wallet.php`。

#### 獲取充值地址
1. 在 `example/get_wallet_addresses.php` 中指定 `UserOpenId` 和需要查詢的 `ChainIDs` (例如 "1,56")。
2. 運行 `php example/get_wallet_addresses.php`。

#### 申請提現
1. 在 `example/withdraw.php` 中指定 `UserOpenId`, `TokenId`, `Amount`, `AddressTo`, `SafeCheckCode`, `CallBackUrl`。
2. 運行 `php example/withdraw.php`。

#### 創建收銀台訂單
1. 在 `example/new_order.php` 中指定 `outOrderNo`, `tokenId`, `quantity`, `notifyUrl`。
2. 運行 `php example/new_order.php`。

#### 查詢錢包餘額
1. 在 `example/wallet_balance.php` 中指定 `address`, `contract_address`, `chain_id`。
2. 運行 `php example/wallet_balance.php`。