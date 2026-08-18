# Ejemplos de Código y Herramientas

Este documento se divide en dos partes:
1. **Ejemplos de Código Basados en Escenarios:** Muestra cómo manejar las llamadas a la API y la verificación en código real.
2. **Guía de Herramientas de CLI:** Explica cómo usar los scripts incluidos en el SDK para pruebas rápidas.

---

## 1. Ejemplos de Código Basados en Escenarios

### 1.1 Llamada Completa a la API y Verificación de Respuesta

El siguiente código muestra cómo usar el SDK para construir una solicitud de "Crear Usuario", enviar una solicitud HTTP y realizar la verificación de seguridad en la firma de datos devuelta por la plataforma.

```php
<?php
require __DIR__.'/../vendor/autoload.php';

use Cryptopay\Chain\CryptoPay;

function main() {
    // 1. Inicializar configuración
    $config = [
        'ApiKey' => 'your_api_key',
        'ApiSecret' => 'your_api_secret',
        'RsaPrivateKey' => 'your_rsa_private_key',
        'PlatformPubKey' => 'platform_public_key',
    ];

    // 2. Crear instancia del SDK
    $cryptoPay = new CryptoPay($config);

    // 3. Llamar a la API: Crear Usuario
    $openId = 'php_user_' . time();
    $result = $cryptoPay->createUser($openId);

    if (!$result) {
        echo "Solicitud fallida\n";
        return;
    }

    // 4. Analizar y verificar respuesta
    $postData = json_decode($result, true);

    if ($postData['code'] != 1) {
        echo "¡Respuesta fallida! Código: " . $postData['code'] . ", Msg: " . $postData['msg'] . "\n";
        return;
    }

    // Verificar firma de la plataforma
    if ($cryptoPay->verifyRsaSignature($postData)) {
        echo "✅ ¡Solicitud exitosa y verificada! OpenId: " . $postData['data']['OpenId'] . "\n";
    } else {
        echo "❌ ¡La verificación de la firma falló!\n";
    }
}

main();
```


---

## 2. Guía de Herramientas de CLI

El SDK proporciona scripts de línea de comandos para pruebas rápidas de varias interfaces.

### 2.1 Instalar Dependencias

Ejecute el comando `composer install` en el directorio raíz del SDK para instalar las dependencias necesarias.

### 2.2 Probar Comandos de Interfaz

#### Registrar Nuevo Usuario
1. Modifique `OpenId` en `example/create_user.php`.
2. Ejecute `php example/create_user.php`.

#### Registro de Billetera
1. Especifique `UserOpenId` y `ChainID` en `example/create_wallet.php`.
2. Ejecute `php example/create_wallet.php`.

#### Obtener Dirección de Depósito
1. Especifique `UserOpenId` y los `ChainIDs` a consultar (p. ej., "1,56") en `example/get_wallet_addresses.php`.
2. Ejecute `php example/get_wallet_addresses.php`.

#### Solicitar Retiro
1. Especifique `UserOpenId`, `TokenId`, `Amount`, `AddressTo`, `SafeCheckCode` y `CallBackUrl` en `example/withdraw.php`.
2. Ejecute `php example/withdraw.php`.

#### Crear Orden de Cajero
1. Especifique `outOrderNo`, `tokenId`, `quantity` y `notifyUrl` en `example/new_order.php`.
2. Ejecute `php example/new_order.php`.

#### Consultar Saldo de Billetera
1. Especifique `address`, `contract_address` y `chain_id` en `example/wallet_balance.php`.
2. Ejecute `php example/wallet_balance.php`.