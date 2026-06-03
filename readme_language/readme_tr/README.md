# CryptoPay PHP SDK

![PHP Version](https://img.shields.io/badge/php-8.4.12+-blue.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Telegram](https://img.shields.io/badge/chat-Telegram-blue?logo=telegram)](https://t.me/ZeroSerivce)

## CryptoPay PHP SDK'ya Hoş Geldiniz

CryptoPay PHP SDK, PHP ile uygulanan ve kullanıcı kaydı, cüzdan oluşturma, para yatırma geri çağırma (callback) bildirimleri ve para çekme gibi işlevler sağlayan profesyonel bir kripto para hizmeti SDK'sıdır.
Yaygın olarak kullanılmış olup güvenli, kararlı ve kolayca genişletilebilir olduğu kanıtlanmıştır.

## Kurulum

```bash
git clone https://github.com/zerospace-ai/pay-sdk-php
```

> **Not:** Derleme için PHP 8.4.12+ gerekir.

## Hızlı Başlangıç

### 1. Yapılandırmayı Hazırlama

SDK'yı kullanmadan önce, tüccarın kimlik doğrulama bilgilerini ve genel/özel anahtarlarını içeren `config.yaml` yapılandırma dosyasını hazırlamanız gerekir:

```yaml
ApiKey: "your_api_key"
ApiSecret: "your_api_secret"
PlatformPubKey: "platform_public_key"
PlatformRiskPubKey: "platform_risk_public_key"
RsaPrivateKey: "your_rsa_private_key"
```

> **💡 İpucu:** Tüccarın kendi RSA anahtar çiftini (`RsaPrivateKey`) nasıl oluşturacağı ve ayrıntılı kimlik doğrulama ile güvenlik mekanizmaları hakkında ayrıntılar için lütfen [Kimlik Doğrulama ve Güvenlik (authentication.md)](./authentication.md) bölümünü okuyun.

### 2. SDK'yı Başlatma ve İstek Gönderme

Aşağıda, SDK örneğini nasıl başlatacağınızı ve "Yeni Kullanıcı Oluştur" API'sini nasıl çağıracağınızı gösteren eksiksiz bir örnek bulunmaktadır:

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

## Temel Kavramlar ve Gezinme

Bu SDK'yı daha iyi kullanabilmek için geri kalan belgeleri aşağıdaki sırayla okumanızı öneririz:

1. **[Kimlik Doğrulama ve Güvenlik (authentication.md)](./authentication.md)**: RSA anahtar çiftlerinin nasıl oluşturulacağını ve SDK ile platform arasındaki imza doğrulama mekanizmasını öğrenin.
2. **[API Referansı (api-reference.md)](./api-reference.md)**: Desteklenen tüm API uç noktaları (örneğin, cüzdan oluşturma, para çekme) ve Webhook formatları için ayrıntılı talimatlar içerir.
3. **[Örnekler ve Araçlar (examples.md)](./examples.md)**: Daha karmaşık senaryo tabanlı kod örneklerini ve SDK'nın yerleşik CLI araçlarının kullanım talimatlarını görün.
4. **[Ek (appendix.md)](./appendix.md)**: Desteklenen ChainID'ler, token türleri ve sözleşme adresleri gibi statik sözlük bilgileri.

## İletişim

Herhangi bir sorunuz varsa, lütfen servis sağlayıcısıyla iletişime geçin:  
Telegram: [@ZeroSerivce](https://t.me/ZeroSerivce)