# CryptoPay PHP SDK

![PHP Version](https://img.shields.io/badge/php-8.4.12+-blue.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Telegram](https://img.shields.io/badge/chat-Telegram-blue?logo=telegram)](https://t.me/ZeroSerivce)

## Bienvenido a CryptoPay PHP SDK

CryptoPay PHP SDK es un SDK de servicio de criptomonedas profesional implementado en PHP, que proporciona funciones como registro de usuarios, generación de billeteras, notificaciones de devolución de llamadas de depósitos y retiros.
Ha sido ampliamente utilizado y ha demostrado ser seguro, estable y fácilmente ampliable.

## Instalación

```bash
git clone https://github.com/zerospace-ai/pay-sdk-php
```

> **Nota:** La compilación requiere PHP 8.4.12+.

## Inicio Rápido

### 1. Preparar la Configuración

Antes de utilizar el SDK, debe preparar el archivo de configuración `config.yaml`, que contiene la información de autenticación del comerciante y las claves públicas/privadas:

```yaml
ApiKey: "your_api_key"
ApiSecret: "your_api_secret"
PlatformPubKey: "platform_public_key"
PlatformRiskPubKey: "platform_risk_public_key"
RsaPrivateKey: "your_rsa_private_key"
```

> **💡 Consejo:** Para obtener detalles sobre cómo generar el propio par de claves RSA del comerciante (`RsaPrivateKey`) y los mecanismos detallados de autenticación y seguridad, lea [Autenticación y Seguridad (authentication.md)](./authentication.md).

### 2. Inicializar el SDK y Enviar Solicitud

Aquí hay un ejemplo completo que demuestra cómo inicializar la instancia del SDK y llamar a la API "Crear Nuevo Usuario":

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

## Conceptos Clave y Navegación

Para utilizar mejor este SDK, le recomendamos que lea los documentos restantes en el siguiente orden:

1. **[Autenticación y Seguridad (authentication.md)](./authentication.md)**: Aprenda cómo generar pares de claves RSA y el mecanismo de verificación de firmas entre el SDK y la plataforma.
2. **[Referencia de API (api-reference.md)](./api-reference.md)**: Contiene instrucciones detalladas para todos los puntos finales de API compatibles (por ejemplo, creación de billetera, retiro) y formatos de webhook.
3. **[Ejemplos y Herramientas (examples.md)](./examples.md)**: Vea ejemplos de código basados en escenarios más complejos e instrucciones sobre el uso de las herramientas CLI integradas del SDK.
4. **[Apéndice (appendix.md)](./appendix.md)**: Información de diccionario estático, como ChainIDs admitidos, tipos de tokens y direcciones de contratos.

## Contacto

Si tiene alguna pregunta, comuníquese con el proveedor de servicios:  
Telegram: [@ZeroSerivce](https://t.me/ZeroSerivce)