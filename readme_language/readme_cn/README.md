# CryptoPay PHP SDK

![PHP Version](https://img.shields.io/badge/php-8.4.12+-blue.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Telegram](https://img.shields.io/badge/chat-Telegram-blue?logo=telegram)](https://t.me/ZeroSerivce)

## 欢迎使用 CryptoPay PHP SDK

CryptoPay PHP SDK 是一个专业的加密货币服务 SDK，使用 PHP 实现，提供用户注册、钱包生成、充值回调通知、提现等功能。
它经过长期使用，已被证明安全、稳定且易于扩展。

## 安装

```bash
git clone https://github.com/zerospace-ai/pay-sdk-php
```

> **注意：** 编译需要 PHP 8.4.12+。

## 快速入门

### 1. 准备配置

在使用 SDK 之前，您需要准备 `config.yaml` 配置文件，其中包含商户的认证信息和公私钥：

```yaml
ApiKey: "your_api_key"
ApiSecret: "your_api_secret"
PlatformPubKey: "platform_public_key"
PlatformRiskPubKey: "platform_risk_public_key"
RsaPrivateKey: "your_rsa_private_key"
```

> **💡 提示：** 关于如何生成商户自己的 RSA 密钥对（private_key），以及详细的认证与安全机制，请务必阅读 [认证与安全 (authentication.md)](./authentication.md)。

### 2. 初始化 SDK 并发送请求

以下是一个完整的示例，展示如何初始化 SDK 实例并调用“注册新用户”接口：

```php
<?php
require __DIR__.'/vendor/autoload.php';

use Cryptopay\Chain\CryptoPay;

// 1. Prepare Configuration
$config = [
    'ApiKey' => 'your_api_key',
    'ApiSecret' => 'your_api_secret',
    'RsaPrivateKey' => 'your_rsa_private_key',
    'PlatformPubKey' => 'platform_public_key',
];

// 2. Create SDK instance
$cryptoPay = new CryptoPay($config);

// 3. Call API: Build the create new user request
$openId = "PT00001"; // User's unique identifier
$result = $cryptoPay->createUser($openId);

if (!$result) {
    echo "Request failed\n";
    exit;
}

// Parse and verify response
$postData = json_decode($result, true);
if ($postData['code'] == 1) {
    if ($cryptoPay->verifyRsaSignature($postData)) {
        echo "Successfully registered user! OpenId: " . $postData['data']['OpenId'] . "\n";
    } else {
        echo "Signature verification failed\n";
    }
} else {
    echo "Registration failed: " . $postData['msg'] . "\n";
}
```

## 关键概念与导航

为了更好地使用本 SDK，建议您按照以下顺序阅读其余文档：

1. **[认证与安全 (authentication.md)](./authentication.md)**：学习如何生成 RSA 密钥对，以及 SDK 与平台之间的签名验证机制。
2. **[API 参考 (api-reference.md)](./api-reference.md)**：包含所有受支持的 API 接口的详细说明（如创建钱包、提现等）和回调通知格式。
3. **[示例代码与工具 (examples.md)](./examples.md)**：查看更复杂的场景化示例代码，以及 SDK 内置命令行工具的使用方法。
4. **[附录 (appendix.md)](./appendix.md)**：支持的 ChainID、代币类型、合约地址等静态字典信息。

## 联系方式

如果您有任何问题，请联系服务提供商：  
Telegram: [@ZeroSerivce](https://t.me/ZeroSerivce)