# Примеры кода и инструменты

Этот документ разделен на две части:
1. **Примеры кода на основе сценариев:** Демонстрирует, как обрабатывать вызовы API и проверку в реальном коде.
2. **Руководство по инструментам CLI:** Объясняет, как использовать скрипты, включенные в SDK, для быстрого тестирования.

---

## 1. Примеры кода на основе сценариев

### 1.1 Полный вызов API и проверка ответа

Следующий код показывает, как использовать SDK для создания запроса «Создать пользователя», отправки HTTP-запроса и выполнения проверки безопасности подписи данных, возвращенной платформой.

```php
<?php
require __DIR__.'/../vendor/autoload.php';

use Cryptopay\Chain\CryptoPay;

function main() {
    // 1. Инициализация конфигурации
    $config = [
        'ApiKey' => 'your_api_key',
        'ApiSecret' => 'your_api_secret',
        'RsaPrivateKey' => 'your_rsa_private_key',
        'PlatformPubKey' => 'platform_public_key',
    ];

    // 2. Создание экземпляра SDK
    $cryptoPay = new CryptoPay($config);

    // 3. Вызов API: Создать пользователя
    $openId = 'php_user_' . time();
    $result = $cryptoPay->createUser($openId);

    if (!$result) {
        echo "Запрос не удался\n";
        return;
    }

    // 4. Парсинг и проверка ответа
    $postData = json_decode($result, true);

    if ($postData['code'] != 1) {
        echo "Ошибка ответа! Код: " . $postData['code'] . ", Сообщение: " . $postData['msg'] . "\n";
        return;
    }

    // Проверка подписи платформы
    if ($cryptoPay->verifyRsaSignature($postData)) {
        echo "✅ Запрос успешен и подтвержден! OpenId: " . $postData['data']['OpenId'] . "\n";
    } else {
        echo "❌ Ошибка проверки подписи!\n";
    }
}

main();
```


---

## 2. Руководство по инструментам CLI

SDK предоставляет скрипты командной строки для быстрого тестирования различных интерфейсов.

### 2.1 Установка зависимостей

Выполните команду `composer install` в корневом каталоге SDK, чтобы установить необходимые зависимости.

### 2.2 Команды тестирования интерфейса

#### Регистрация нового пользователя
1. Измените `OpenId` в `example/create_user.php`.
2. Запустите `php example/create_user.php`.

#### Регистрация кошелька
1. Укажите `UserOpenId` и `ChainID` в `example/create_wallet.php`.
2. Запустите `php example/create_wallet.php`.

#### Получить адрес для пополнения
1. Укажите `UserOpenId` и `ChainIDs` для запроса (например, "1,56") в `example/get_wallet_addresses.php`.
2. Запустите `php example/get_wallet_addresses.php`.

#### Подать заявку на вывод средств
1. Укажите `UserOpenId`, `TokenId`, `Amount`, `AddressTo`, `SafeCheckCode` и `CallBackUrl` в `example/withdraw.php`.
2. Запустите `php example/withdraw.php`.

#### Создать ордер кассира
1. Укажите `outOrderNo`, `tokenId`, `quantity` и `notifyUrl` в `example/new_order.php`.
2. Запустите `php example/new_order.php`.