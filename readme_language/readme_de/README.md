# CryptoPay PHP SDK

![PHP Version](https://img.shields.io/badge/php-8.4.12+-blue.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Telegram](https://img.shields.io/badge/chat-Telegram-blue?logo=telegram)](https://t.me/ZeroSerivce)

## Willkommen beim CryptoPay PHP SDK

Das CryptoPay PHP SDK ist ein professionelles Kryptowährungs-Service-SDK, das in PHP implementiert ist. Es bietet Funktionen wie Benutzerregistrierung, Wallet-Erstellung, Einzahlungs-Rückrufbenachrichtigungen und Auszahlungen.
Es ist weit verbreitet und hat sich als sicher, stabil und leicht erweiterbar erwiesen.

## Installation

```bash
git clone https://github.com/zerospace-ai/pay-sdk-php
```

> **Hinweis:** Die Kompilierung erfordert PHP 8.4.12+.

## Schnellstart

### 1. Konfiguration vorbereiten

Bevor Sie das SDK verwenden, müssen Sie die Konfigurationsdatei `config.yaml` vorbereiten, die die Authentifizierungsinformationen des Händlers und die öffentlichen/privaten Schlüssel enthält:

```yaml
ApiKey: "your_api_key"
ApiSecret: "your_api_secret"
PlatformPubKey: "platform_public_key"
PlatformRiskPubKey: "platform_risk_public_key"
RsaPrivateKey: "your_rsa_private_key"
```

> **💡 Tipp:** Für Details zur Generierung des eigenen RSA-Schlüsselpaars (`RsaPrivateKey`) und zu den detaillierten Authentifizierungs- und Sicherheitsmechanismen lesen Sie bitte [Authentifizierung und Sicherheit (authentication.md)](./authentication.md).

### 2. SDK initialisieren und Anfrage senden

Hier ist ein vollständiges Beispiel, das zeigt, wie die SDK-Instanz initialisiert und die API "Neuen Benutzer erstellen" aufgerufen wird:

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

## Wichtige Konzepte und Navigation

Um dieses SDK optimal nutzen zu können, empfehlen wir, die restlichen Dokumente in dieser Reihenfolge zu lesen:

1. **[Authentifizierung und Sicherheit (authentication.md)](./authentication.md)**: Erfahren Sie, wie Sie RSA-Schlüsselpaare generieren und den Signatur-Verifizierungsmechanismus zwischen SDK und Plattform verstehen.
2. **[API-Referenz (api-reference.md)](./api-reference.md)**: Enthält detaillierte Anweisungen für alle unterstützten API-Endpunkte (z. B. Wallet-Erstellung, Auszahlung) und Webhook-Formate.
3. **[Beispiele und Tools (examples.md)](./examples.md)**: Zeigt komplexere szenariobasierte Codebeispiele und Anweisungen zur Verwendung der integrierten CLI-Tools.
4. **[Anhang (appendix.md)](./appendix.md)**: Statische Wörterbuchinformationen wie unterstützte ChainIDs, Token-Typen und Vertragsadressen.

## Kontakt

Wenn Sie Fragen haben, kontaktieren Sie bitte den Dienstanbieter:  
Telegram: [@ZeroSerivce](https://t.me/ZeroSerivce)