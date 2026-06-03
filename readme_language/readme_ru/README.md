# CryptoPay PHP SDK

![PHP Version](https://img.shields.io/badge/php-8.4.12+-blue.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Telegram](https://img.shields.io/badge/chat-Telegram-blue?logo=telegram)](https://t.me/ZeroSerivce)

## Добро пожаловать в CryptoPay PHP SDK

CryptoPay PHP SDK — это профессиональный SDK для криптовалютных сервисов, реализованный на PHP. Он предоставляет такие функции, как регистрация пользователей, создание кошельков, уведомления об обратном вызове (callback) при пополнении и вывод средств.
Он широко используется и доказал свою безопасность, стабильность и простоту расширения.

## Установка

```bash
git clone https://github.com/zerospace-ai/pay-sdk-php
```

> **Примечание:** Для компиляции требуется PHP 8.4.12+.

## Быстрый старт

### 1. Подготовка конфигурации

Перед использованием SDK необходимо подготовить файл конфигурации `config.yaml`, который содержит информацию для аутентификации продавца и открытые/закрытые ключи:

```yaml
ApiKey: "your_api_key"
ApiSecret: "your_api_secret"
PlatformPubKey: "platform_public_key"
PlatformRiskPubKey: "platform_risk_public_key"
RsaPrivateKey: "your_rsa_private_key"
```

> **💡 Подсказка:** Подробную информацию о том, как сгенерировать собственную пару ключей RSA продавца (`RsaPrivateKey`), а также о подробных механизмах аутентификации и безопасности, читайте в разделе [Аутентификация и безопасность (authentication.md)](./authentication.md).

### 2. Инициализация SDK и отправка запроса

Ниже приведен полный пример, демонстрирующий инициализацию экземпляра SDK и вызов API "Создать нового пользователя":

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

## Ключевые концепции и навигация

Для более эффективного использования этого SDK мы рекомендуем прочитать остальные документы в следующем порядке:

1. **[Аутентификация и безопасность (authentication.md)](./authentication.md)**: Узнайте, как генерировать пары ключей RSA и как работает механизм проверки подписи между SDK и платформой.
2. **[Справочник по API (api-reference.md)](./api-reference.md)**: Содержит подробные инструкции для всех поддерживаемых конечных точек API (например, создание кошелька, вывод средств) и форматов вебхуков.
3. **[Примеры и инструменты (examples.md)](./examples.md)**: Посмотрите более сложные примеры кода для различных сценариев и инструкции по использовании встроенных инструментов CLI SDK.
4. **[Приложение (appendix.md)](./appendix.md)**: Статическая словарная информация, такая как поддерживаемые ChainID, типы токенов и адреса контрактов.

## Контакты

Если у вас есть какие-либо вопросы, обратитесь к поставщику услуг:  
Telegram: [@ZeroSerivce](https://t.me/ZeroSerivce)