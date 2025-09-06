# CryptoPay Php SDK

![Php Version](https://img.shields.io/badge/php-8.4.12+-blue.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Telegram](https://img.shields.io/badge/chat-Telegram-blue?logo=telegram)](https://t.me/ZeroSerivce)

## 🌟 Добро пожаловать в CryptoPay Php SDK

CryptoPay Php SDK - это профессиональный SDK для услуг криптовалюты, реализованный на Php, предоставляющий регистрацию пользователей, генерацию кошельков, уведомления о депозитах, вывод средств и другие функции.

Он доказал свою безопасность, стабильность и легкость расширения через долгосрочное использование.

## ⚙️ Установка

```bash
git clone https://github.com/zerospace-ai/pay-sdk-php
```

Примечание: Компиляция требует PHP 8.4.12+ 🛠️.
## 🚀 Быстрый старт
### 1. ⚙️ config.yaml

```yaml
ApiKey: "your_api_key"
ApiSecret: "your_api_secret"
PlatformPubKey: "platform_public_key"
PlatformRiskPubKey: "platform_risk_public_key"
RsaPrivateKey: "your_rsa_private_key"
```

Описание полей:

• 🔑 ApiKey / ApiSecret:

Назначается платформой при регистрации учетной записи мерчанта, используется для аутентификации API-запросов ✅.

• 🛡️ PlatformPubKey / PlatformRiskPubKey:

Публичные ключи, предоставляемые платформой, используются для проверки данных или подписей колбэков, возвращаемых платформой, обеспечивая надежность источников информации. PlatformRiskPubKey в основном используется для проверки событий, связанных с контролем рисков ⚠️.

• 🗝️ RsaPrivateKey:

RSA-приватный ключ, сгенерированный мерчантом, используется для подписи запросов, обеспечивая отсутствие подделки содержимого запроса. Важное примечание: Приватный ключ должен храниться в секрете 🔒, не раскрывайте его 🚫.

### 2. Генерация пары ключей RSA 🔐

Использование пары ключей RSA для подписи запросов обеспечивает безопасность данных. Ниже описано, как сгенерировать пару ключей и извлечь строки ключей на разных операционных системах.

#### 2.1 Генерация пары ключей с помощью OpenSSL

```bash
# Генерация 2048-битного приватного ключа
openssl genrsa -out rsa_private_key.pem 2048

# Генерация публичного ключа из приватного ключа
openssl rsa -in rsa_private_key.pem -out rsa_public_key.pem -pubout
```

> 💡 Совет: Сгенерированный публичный ключ нужно удалить начало и конец -----BEGIN PUBLIC KEY----- / -----END PUBLIC KEY-----, удалить разрывы строк, преобразовать в однострочную строку и отправить на платформу.
> 
> Извлечь строки ключей и отправить публичный ключ на платформу 📤.
>
>Команды для генерации пары ключей RSA на Mac и Windows такие же, как на Linux.

#### 2.2 Извлечение строк ключей 🔑

На Mac/Linux или Git Bash/WSL/Cygwin:

```bash
# Извлечение строки приватного ключа
grep -v '^-----' rsa_private_key.pem | tr -d '\n'; echo

# Извлечение строки публичного ключа
grep -v '^-----' rsa_public_key.pem | tr -d '\n'; echo
```

Windows

PowerShell извлечение строк приватного и публичного ключей:

```powershell
# Приватный ключ
Write-Output ((Get-Content rsa_private_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")

# Публичный ключ
Write-Output ((Get-Content rsa_public_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")
```

> ⚠️ Примечание: Сгенерированный приватный ключ должен храниться в безопасности и не утечь.


### 🛠️ 3. Создание экземпляра SDK

```php

	$config = [
		'key' => 'dkhl346iwonfw436',
		'secret' => '11e15f2d36f4e61c8a46fd426ae2189bc0406c1e37c29d8b136f75268a1d4216',
		'public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3CeXmnRGmDxfxYco1Xazf7PzcOVJjWO/NTXFff+xmZC8DUKzu/VI7tn8geY7mXKVWa1vHkfw/H1Hqd7KBqHDp2DDuOetV6P4+2/5HXNEyy5dPR/hhYrAEDJMviB3BKfQ/NTExGm5yfCMlwPmGHJVUeglsSC0dmQOKsZ3aLOLs79L89TdIWObczdxNSeg4mlDPntkGC+hrwrEkHq4kPO16PIzZYqlQyv/nmVORhMrh4n4WeXUQufS24U07IKO27nRkwVtZgp8Gdl9JnWH1P70L35wBBiWF78/5SY1tmPwASoV6tA11ImQVFUSx0Zl5RWgnjqXnow8TIhI010HyI328QIDAQAB',
		'private_key' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDcJ5eadEaYPF/FhyjVdrN/s/Nw5UmNY781NcV9/7GZkLwNQrO79Uju2fyB5juZcpVZrW8eR/D8fUep3soGocOnYMO4561Xo/j7b/kdc0TLLl09H+GFisAQMky+IHcEp9D81MTEabnJ8IyXA+YYclVR6CWxILR2ZA4qxndos4uzv0vz1N0hY5tzN3E1J6DiaUM+e2QYL6GvCsSQeriQ87Xo8jNliqVDK/+eZU5GEyuHifhZ5dRC59LbhTTsgo7budGTBW1mCnwZ2X0mdYfU/vQvfnAEGJYXvz/lJjW2Y/ABKhXq0DXUiZBUVRLHRmXlFaCeOpeejDxMiEjTXQfIjfbxAgMBAAECggEANMRYlfCgqwRtgA6xPtjAlS8wfjK2umjZ/4rv1w9eJgyGxjbilX5pCLa+yvO//Tt1iJUFOSDNJfdxIcoAai6Dkq6iusLszUDEAKAJ+YETUA/A0VG+4B8tgbRMqJVncXo3oSHuN5WPrlM1n0yT7fAZexRFVHseRfIdYytGm5XNOjuBzzaqghodKGw/IvdADw8eNYBMHUBFjgvjJSVKd99rsiKRRkzIVtBcTs7RxFXVdhB/PhvNxWugb3r3ihX52ho5uAoIHUGE7fzdIPO4iCYv9MRzKuSVwrAPIiJvAP7duwoC/INMFsqdCTuX1NdPS+58Ubgkdkmbf+8BK12dzK8EQQKBgQDtvJL2+j6Nd7rEf0+DK8ENjhWf3ktOoWX6dmepxDrJI1nHulpjZSWy5qmztMGiHF5vU0e7ARZZeFFA4aGXcScTT+9ffuWJP6JA04OJryrWTTEF4qT2aTVHJDOYuOtr6pD7541QdpIVNq6TQu7zITaTBoTbHvNYFVFTdZgi8/Lb9QKBgQDtET57KgDBiedVCwIdODPTTsSGqWzZ5J2qE63CYN4nvkHbtzcPKADnOxq0yaYfrr1olYdqIzi0VWR4bPbvuP7D9jtGbxp7kDI6/ZkdcOIAG/0aFqJaqGGnnzgWQJXv8jn08Z90nrikDrYxBX8U1s/9fMdur//csZmjQPmdBOXtjQKBgQCvhHa4cv61sTypkBijDi2klU7vzc2pis1gggR8uQxxrXC+XZ4YHfgcQeHudDg1OF6cME8YCHB4s7TBgxOrXHXt8ykWRviuQNXIqKBHiZTFzQ2xe6gw6HHWSSryySu+a9qIsGaLjk7B7LIstND3nYDOQZTatdoRIQP+6yXcQGD/9QKBgEyTIlyEP8REOC33JVKs4ciii8Z3mYp0Vx0lyB2eToQF554B+03w/QGzzLeS3w8i0Vmj2x7Ei79sSczAXa8nUVuZAKKKpsI83IzDd57T5JxmbgXsQ7sG4qxTOLmvWP8tfd0J4xi3YCrV+bGx9c+UZ5CYqo6tWPc/gsIB7d7zQxXNAoGAcxv32TAh+eRrVgIC0LMDXyKQ7pKt58RTjL8/SsSwavCKznvAp8S1pEde1/OjUfTiL42muJj1DghytwPIaam57X7/Ikgyz5PxgPzABCWv1BY0P4m37Cv8MYeqKv6e/OtjJs2O+r3GP12SI9RMP1trj7DLt5Z2TUmD5xeDEpdbpbw=',
		'chain_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlxPMHGzg5ci2qZU9SxkTvTlsKKq7svS84QuDFYRNxkDTeLmQOmuPpxRa5v8Ujeg1M8QooeQlc3h33BQJVo1jqtxsoh5qo0Cj1J9gFn8TUF7xBfCX8VZ+Hqy+O2xSpFJHi9Uv8jCT4bnjzGw/53qCivJ7R0BY6mEcv5twrSv+IPqWYw4R5DClmPGVZWf+Bn+s+nsuRAWe3fNODlmWGMsg1nTFGd9JMdLiniud/wa9i2xlIHticCCF0WSap4y3Kp/QmsK1tl66NgpPi8BUfalQHI74snP/BtKSvcvt8+2OkksuI56x1g9PRrmPIFgUKzcgiom6Avd8/4EzoAYaKp12DwIDAQAB',
	];
    global $config;
    $cryptoPay = new CryptoPay($config);
```

## 🔑 Ключевые концепции

- 🆔 **OpenId**: Уникальный идентификатор пользователя, например "HASH1756194148".
- 🔐 **RSA Key**: Используется для подписи и проверки запросов для обеспечения безопасности данных.
- ✍️ **API Signature**: Используйте алгоритмы MD5 и RSA для подписи запросов, обеспечивая отсутствие подделки.

Для подробных описаний API, пожалуйста, обратитесь к [🧩 api-reference.md](./api-reference.md) и [🧩 examples.md](./examples.md).

Для Аутентификации и Безопасности, пожалуйста, обратитесь к [🧩 authentication.md](./authentication.md)

## 📎 Приложение

Для более подробных ссылок, пожалуйста, проверьте документ [Приложение](./appendix.md), который включает следующее содержание:

- [🧩 Список ChainID](./appendix.md#-список-chainid)
- [🏷️ Типы токенов](./appendix.md#-тип-токена)
- [🌐 Общая информация](./appendix.md#-общая-информация)
- [🔰 Базовая информация о токенах](./appendix.md#-базовая-информация-о-токене)

> 💡 **Совет**: Приложение предоставляет информацию о поддерживаемых цепях, типах токенов и общих данных токенов, облегчая интеграцию и использование SDK разработчиками.

## 📞 Контакты

Если у вас есть вопросы, пожалуйста, свяжитесь с поставщиком услуг.  
💬 Telegram: [@ZeroSerivce](https://t.me/ZeroSerivce)