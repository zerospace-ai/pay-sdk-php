# 示例代码与工具

本文档分为两部分：
1. **场景化代码示例：** 演示在实际代码中如何处理 API 的调用与验证。
2. **命令行工具指南：** 介绍如何使用 SDK 附带的编译后执行文件进行快速测试。

---

## 1. 场景化代码示例

### 1.1 完整的 API 调用与响应验证

以下代码展示了如何利用 SDK 构建一个“创建用户”请求，发送 HTTP 请求，并对平台返回的数据签名进行安全验证。

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

SDK 提供了快速测试各接口的命令行脚本。

### 2.1 安装依赖

在 SDK 根目录下执行 `composer install` 命令安装必要依赖。

### 2.2 测试各接口命令

#### 注册新用户
1. 在 `example/create_user.php` 中修改 `OpenId`。
2. 运行 `php example/create_user.php`。

#### 钱包注册
1. 在 `example/create_wallet.php` 中指定 `UserOpenId` 和 `ChainID`。
2. 运行 `php example/create_wallet.php`。

#### 获取充值地址
1. 在 `example/get_wallet_addresses.php` 中指定 `UserOpenId` 和需要查询的 `ChainIDs` (例如 "1,56")。
2. 运行 `php example/get_wallet_addresses.php`。

#### 申请提现
1. 在 `example/withdraw.php` 中指定 `UserOpenId`, `TokenId`, `Amount`, `AddressTo`, `SafeCheckCode`, `CallBackUrl`。
2. 运行 `php example/withdraw.php`。

#### 创建收银台订单
1. 在 `example/new_order.php` 中指定 `outOrderNo`, `tokenId`, `quantity`, `notifyUrl`。
2. 运行 `php example/new_order.php`。