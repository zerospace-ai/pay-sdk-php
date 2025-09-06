# CryptoPay Php SDK

![Versión de Php](https://img.shields.io/badge/php-8.4.12+-blue.svg)
[![Licencia: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Telegram](https://img.shields.io/badge/chat-Telegram-blue?logo=telegram)](https://t.me/ZeroSerivce)

## 🌟 Bienvenido al CryptoPay Php SDK

CryptoPay Php SDK es un SDK profesional de servicios de criptomonedas implementado en Php, que proporciona registro de usuarios, generación de billeteras, notificaciones de callbacks de depósitos, retiros y otras funciones.

Ha sido probado como seguro, estable y fácil de extender a través de un uso a largo plazo.

## ⚙️ Instalación

```bash
git clone https://github.com/zerospace-ai/pay-sdk-php
```

Nota: La compilación requiere PHP 8.4.12+ 🛠️.
## 🚀 Inicio Rápido
### 1. ⚙️ config.yaml

```yaml
ApiKey: "your_api_key"
ApiSecret: "your_api_secret"
PlatformPubKey: "platform_public_key"
PlatformRiskPubKey: "platform_risk_public_key"
RsaPrivateKey: "your_rsa_private_key"
```

Descripciones de campos:

• 🔑 ApiKey / ApiSecret:

Asignados por la plataforma al registrar una cuenta de comerciante, utilizados para la autenticación de solicitudes API ✅.

• 🛡️ PlatformPubKey / PlatformRiskPubKey:

Claves públicas proporcionadas por la plataforma, utilizadas para verificar datos o firmas de callbacks devueltos por la plataforma, asegurando fuentes de información confiables. PlatformRiskPubKey se usa principalmente para verificación de eventos relacionados con control de riesgos ⚠️.

• 🗝️ RsaPrivateKey:

Clave privada RSA generada por el comerciante, utilizada para firmar solicitudes, asegurando que el contenido de la solicitud no sea alterado. Nota importante: La clave privada debe mantenerse confidencial 🔒, no la divulgue 🚫.

### 2. Generar Par de Claves RSA 🔐

Usar un par de claves RSA para firmar solicitudes asegura la seguridad de los datos. A continuación se describe cómo generar un par de claves y extraer cadenas de claves en diferentes sistemas operativos.

#### 2.1 Generar Par de Claves Usando OpenSSL

```bash
# Generar clave privada de 2048 bits
openssl genrsa -out rsa_private_key.pem 2048

# Generar clave pública a partir de la clave privada
openssl rsa -in rsa_private_key.pem -out rsa_public_key.pem -pubout
```

> 💡 Consejo: La clave pública generada necesita eliminar el principio y el final -----BEGIN PUBLIC KEY----- / -----END PUBLIC KEY-----, eliminar saltos de línea, convertir a una cadena de una sola línea y enviar a la plataforma.
> 
> Extraer cadenas de claves y enviar la clave pública a la plataforma 📤.
>
>Los comandos para generar pares de claves RSA en Mac y Windows son los mismos que en Linux.

#### 2.2 Extraer Cadenas de Claves 🔑

En Mac/Linux o Git Bash/WSL/Cygwin:

```bash
# Extraer cadena de clave privada
grep -v '^-----' rsa_private_key.pem | tr -d '\n'; echo

# Extraer cadena de clave pública
grep -v '^-----' rsa_public_key.pem | tr -d '\n'; echo
```

Windows

PowerShell extraer cadenas de clave privada y pública:

```powershell
# Clave privada
Write-Output ((Get-Content rsa_private_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")

# Clave pública
Write-Output ((Get-Content rsa_public_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")
```

> ⚠️ Nota: La clave privada generada debe mantenerse segura y no filtrarse.


### 🛠️ 3. Crear Instancia SDK

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

## 🔑 Conceptos Clave

- 🆔 **OpenId**: Identificador único del usuario, por ejemplo "HASH1756194148".
- 🔐 **Clave RSA**: Usada para firmar y verificar solicitudes para asegurar la seguridad de los datos.
- ✍️ **Firma API**: Usa algoritmos MD5 y RSA para firmar solicitudes, asegurando que no sean alteradas.

Para descripciones detalladas de API, consulte [🧩 api-reference.md](./api-reference.md) y [🧩 examples.md](./examples.md).

Para Autenticación y Seguridad, consulte [🧩 authentication.md](./authentication.md)

## 📎 Apéndice

Para referencias más detalladas, consulte el documento [Apéndice](./appendix.md), que incluye el siguiente contenido:

- [🧩 Lista de ChainID](./appendix.md#-lista-de-chainid)
- [🏷️ Tipos de Token](./appendix.md#-tipos-de-token)
- [🌐 Información Pública](./appendix.md#-🌐-public-information)
- [🔰 Información Básica de Token](./appendix.md#-🔰-token-basic-information)

> 💡 **Consejo**: El apéndice proporciona información de cadenas soportadas, tipos de tokens y datos de tokens públicos, facilitando a los desarrolladores integrar y usar el SDK.

## 📞 Contacto

Si tiene alguna pregunta, contacte al proveedor de servicios.  
💬 Telegram: [@ZeroSerivce](https://t.me/ZeroSerivce)