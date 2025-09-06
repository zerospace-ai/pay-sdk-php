# CryptoPay Php SDK

![Php Version](https://img.shields.io/badge/php-8.4.12+-blue.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Telegram](https://img.shields.io/badge/chat-Telegram-blue?logo=telegram)](https://t.me/ZeroSerivce)

## 🌟 Willkommen zum CryptoPay Php SDK

CryptoPay Php SDK ist ein professionelles Kryptowährungs-Service-SDK, das in Php implementiert ist und Funktionen wie Benutzerregistrierung, Wallet-Generierung, Einzahlungs-Callback-Benachrichtigungen, Auszahlungen und mehr bietet.

Es hat sich durch langfristige Nutzung als sicher, stabil und leicht erweiterbar erwiesen.

## ⚙️ Installation

```bash
git clone https://github.com/zerospace-ai/pay-sdk-php
```

Hinweis: Die Kompilierung erfordert PHP 8.4.12+ 🛠️.
## 🚀 Schnellstart
### 1. ⚙️ config.yaml

```yaml
ApiKey: "your_api_key"
ApiSecret: "your_api_secret"
PlatformPubKey: "platform_public_key"
PlatformRiskPubKey: "platform_risk_public_key"
RsaPrivateKey: "your_rsa_private_key"
```

Feld-Beschreibungen:

• 🔑 ApiKey / ApiSecret:

Wird von der Plattform bei der Registrierung eines Händlerkontos zugewiesen, verwendet für die API-Anfrage-Authentifizierung ✅.

• 🛡️ PlatformPubKey / PlatformRiskPubKey:

Öffentliche Schlüssel, die von der Plattform bereitgestellt werden, verwendet zur Überprüfung von Daten oder Callback-Signaturen, die von der Plattform zurückgegeben werden, um zuverlässige Informationsquellen zu gewährleisten. PlatformRiskPubKey wird hauptsächlich für die Überprüfung risikobezogener Ereignisse verwendet ⚠️.

• 🗝️ RsaPrivateKey:

RSA-Privatschlüssel, der vom Händler generiert wird, verwendet zum Signieren von Anfragen, um sicherzustellen, dass der Anfrageinhalt nicht manipuliert wird. Wichtiger Hinweis: Der Privatschlüssel muss vertraulich gehalten werden 🔒, nicht preisgeben 🚫.

### 2. RSA-Schlüsselpaar generieren 🔐

Die Verwendung eines RSA-Schlüsselpaars zum Signieren von Anfragen gewährleistet die Datensicherheit. Im Folgenden wird beschrieben, wie ein Schlüsselpaar generiert und Schlüssel-Strings auf verschiedenen Betriebssystemen extrahiert werden.

#### 2.1 Schlüsselpaar mit OpenSSL generieren

```bash
# 2048-Bit-Privatschlüssel generieren
openssl genrsa -out rsa_private_key.pem 2048

# Öffentlichen Schlüssel aus Privatschlüssel generieren
openssl rsa -in rsa_private_key.pem -out rsa_public_key.pem -pubout
```

> 💡 Tipp: Der generierte öffentliche Schlüssel muss den Anfang und das Ende -----BEGIN PUBLIC KEY----- / -----END PUBLIC KEY----- entfernen, Zeilenumbrüche entfernen, in eine einzeilige Zeichenkette umwandeln und an die Plattform übermitteln.
> 
> Schlüssel-Strings extrahieren und den öffentlichen Schlüssel an die Plattform übermitteln 📤.
>
>Die Befehle zum Generieren von RSA-Schlüsselpaaren auf Mac und Windows sind die gleichen wie auf Linux.

#### 2.2 Schlüssel-Strings extrahieren 🔑

Auf Mac/Linux oder Git Bash/WSL/Cygwin:

```bash
# Privatschlüssel-String extrahieren
grep -v '^-----' rsa_private_key.pem | tr -d '\n'; echo

# Öffentlichen Schlüssel-String extrahieren
grep -v '^-----' rsa_public_key.pem | tr -d '\n'; echo
```

Windows

PowerShell extrahiert private und public key strings:

```powershell
# Privatschlüssel
Write-Output ((Get-Content rsa_private_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")

# Öffentlicher Schlüssel
Write-Output ((Get-Content rsa_public_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")
```

> ⚠️ Hinweis: Der generierte Privatschlüssel muss sicher aufbewahrt werden und darf nicht durchsickern.


### 🛠️ 3. SDK-Instanz erstellen

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

## 🔑 Wichtige Konzepte

- 🆔 **OpenId**: Einzigartige Benutzerkennung, z.B. "HASH1756194148".
- 🔐 **RSA-Schlüssel**: Wird zum Signieren und Überprüfen von Anfragen verwendet, um die Datensicherheit zu gewährleisten.
- ✍️ **API-Signatur**: Verwenden Sie MD5- und RSA-Algorithmen zum Signieren von Anfragen, um sicherzustellen, dass sie nicht manipuliert werden.

Für detaillierte API-Beschreibungen siehe [🧩 api-reference.md](./api-reference.md) und [🧩 examples.md](./examples.md).

Für Authentifizierung & Sicherheit siehe [🧩 authentication.md](./authentication.md)

## 📎 Anhang

Für detailliertere Referenzen schauen Sie bitte in das [Anhang](./appendix.md)-Dokument, das folgende Inhalte enthält:

- [🧩 ChainID-Liste](./appendix.md#-chainid-liste)
- [🏷️ Token-Typen](./appendix.md#-token-typ)
- [🌐 Öffentliche Informationen](./appendix.md#-öffentliche-informationen)
- [🔰 Token-Grundinformationen](./appendix.md#-token-grundinformationen)

> 💡 **Tipp**: Der Anhang bietet unterstützte Ketteninformationen, Token-Typen und öffentliche Token-Daten, was es Entwicklern erleichtert, das SDK zu integrieren und zu verwenden.

## 📞 Kontakt

Falls Sie Fragen haben, kontaktieren Sie bitte den Dienstleister.  
💬 Telegram: [@ZeroSerivce](https://t.me/ZeroSerivce)