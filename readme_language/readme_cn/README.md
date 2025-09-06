# CryptoPay Php SDK

![Php Version](https://img.shields.io/badge/php-8.4.12+-blue.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Telegram](https://img.shields.io/badge/chat-Telegram-blue?logo=telegram)](https://t.me/ZeroSerivce)

## 🌟 欢迎使用 CryptoPay Php SDK

CryptoPay Php SDK 是一个专业的加密货币服务 SDK，使用 Php 实现，提供用户注册、钱包生成、充值回调通知、提现等功能。

它经过长期使用，已被证明安全、稳定且易于扩展。

## ⚙️ 安装

```bash
git clone https://github.com/zerospace-ai/pay-sdk-php
```

注意：编译需要 PHP 8.4.12+ 🛠️。

## 🚀 快速入门
### 1. ⚙️ config.yaml

```yaml
ApiKey: "your_api_key"
ApiSecret: "your_api_secret"
PlatformPubKey: "platform_public_key"
PlatformRiskPubKey: "platform_risk_public_key"
RsaPrivateKey: "your_rsa_private_key"
```

字段描述：

• 🔑 ApiKey / ApiSecret：

在注册商户账户时由平台分配，用于 API 请求认证 ✅。

• 🛡️ PlatformPubKey / PlatformRiskPubKey：

平台提供的公钥，用于验证平台返回的数据或回调签名，确保信息来源可靠。PlatformRiskPubKey 主要用于风险控制相关事件验证 ⚠️。

• 🗝️ RsaPrivateKey：

商户生成的 RSA 私钥，用于对请求签名，确保请求内容未被篡改。重要提示：私钥必须保密 🔒，不要泄露 🚫。

### 2. 生成 RSA 密钥对 🔐

使用 RSA 密钥对对请求进行签名，确保数据安全。以下描述了在不同操作系统上生成密钥对并提取密钥字符串的方法。

#### 2.1 使用 OpenSSL 生成密钥对

```bash
# Generate 2048-bit private key
openssl genrsa -out rsa_private_key.pem 2048

# Generate public key from private key
openssl rsa -in rsa_private_key.pem -out rsa_public_key.pem -pubout
```

> 💡 Tip: 生成的公钥需要去掉开头和结尾的 -----BEGIN PUBLIC KEY----- / -----END PUBLIC KEY-----，去掉换行符，转换为单行字符串后提交到平台。
> 
> 提取关键字符串，并将公钥提交到平台。 📤。
>
> 在 Mac 和 Windows 上生成 RSA 密钥对的命令与 Linux 相同。

#### 2.2 提取密钥字符串 🔑

On Mac/Linux or Git Bash/WSL/Cygwin:

```bash
# Extract private key string
grep -v '^-----' rsa_private_key.pem | tr -d '\n'; echo

# Extract public key string
grep -v '^-----' rsa_public_key.pem | tr -d '\n'; echo
```

Windows

PowerShell 提取私钥和公钥字符串：

```powershell
# Private key
Write-Output ((Get-Content rsa_private_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")

# Public key
Write-Output ((Get-Content rsa_public_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")
```

> ⚠️ 注意：生成的私钥必须妥善保管，严禁泄露。


### 🛠️ 3. 创建 SDK 实例

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

## 🔑 关键概念

- 🆔 **OpenId**：用户的唯一标识符，例如 "HASH1756194148"。

- 🔐 **RSA Key**：用于签名和验证请求，以确保数据安全。

- ✍️ **API Signature**：使用 MD5 和 RSA 算法对请求进行签名，确保未被篡改。

有关详细的 API 描述，请参阅 [🧩 api-reference.md](./api-reference.md) 和 [🧩 examples.md](./examples.md)。

有关认证与安全，请参阅 [🧩 authentication.md](./authentication.md)

## 📎 附录

有关更多详细参考，请查看 [附录](./appendix.md) 文档，其中包括以下内容：

- [🧩 ChainID 列表](./appendix.md#-chainid-列表)

- [🏷️ Token 类型](./appendix.md#-token-类型)

- [🌐 公共信息](./appendix.md#-公共信息)

- [🔰 Token 基本信息](./appendix.md#-token-基本信息)

> 💡 **Tip**: 附录提供了支持的链信息、代币类型和公开的代币数据，使开发者更容易集成和使用 SDK。

## 📞 联系方式

如果您有任何问题，请联系服务提供商。  
💬 Telegram: [@ZeroSerivce](https://t.me/ZeroSerivce)