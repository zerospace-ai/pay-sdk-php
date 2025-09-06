# CryptoPay Php SDK

![Php Version](https://img.shields.io/badge/php-8.4.12+-blue.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Telegram](https://img.shields.io/badge/chat-Telegram-blue?logo=telegram)](https://t.me/ZeroSerivce)

## 🌟 歡迎使用 CryptoPay Php SDK

CryptoPay Php SDK 是一個使用 Php 實現的專業加密貨幣服務 SDK，提供用戶註冊、錢包生成、充值回調通知、提現等功能。

經過長期使用，已證明其安全、穩定且易於擴展。

## ⚙️ 安裝

```bash
git clone https://github.com/zerospace-ai/pay-sdk-php
```

注意：編譯需要 PHP 8.4.12+ 🛠️。
## 🚀 快速入門
### 1. ⚙️ config.yaml

```yaml
ApiKey: "your_api_key"
ApiSecret: "your_api_secret"
PlatformPubKey: "platform_public_key"
PlatformRiskPubKey: "platform_risk_public_key"
RsaPrivateKey: "your_rsa_private_key"
```

欄位說明：

• 🔑 ApiKey / ApiSecret:

註冊商家帳戶時由平台分配，用於 API 請求認證 ✅。

• 🛡️ PlatformPubKey / PlatformRiskPubKey:

平台提供的公鑰，用於驗證平台返回的資料或回調簽名，確保資訊來源可靠。PlatformRiskPubKey 主要用於風險控制相關事件驗證 ⚠️。

• 🗝️ RsaPrivateKey:

商家生成的 RSA 私鑰，用於簽署請求，確保請求內容未被篡改。重要注意：私鑰必須保密 🔒，勿洩露 🚫。

### 2. 生成 RSA 密鑰對 🔐

使用 RSA 密鑰對簽署請求以確保資料安全。以下說明在不同作業系統上如何生成密鑰對並提取密鑰字串。

#### 2.1 使用 OpenSSL 生成密鑰對

```bash
# 生成 2048 位私鑰
openssl genrsa -out rsa_private_key.pem 2048

# 從私鑰生成公鑰
openssl rsa -in rsa_private_key.pem -out rsa_public_key.pem -pubout
```

> 💡 提示：生成的公鑰需要移除開頭和結尾 -----BEGIN PUBLIC KEY----- / -----END PUBLIC KEY-----，移除換行，轉換為單行字串並提交給平台。
> 
> 提取密鑰字串並將公鑰提交給平台 📤。
>
>在 Mac 和 Windows 上生成 RSA 密鑰對的命令與 Linux 相同。

#### 2.2 提取密鑰字串 🔑

在 Mac/Linux 或 Git Bash/WSL/Cygwin 上：

```bash
# 提取私鑰字串
grep -v '^-----' rsa_private_key.pem | tr -d '\n'; echo

# 提取公鑰字串
grep -v '^-----' rsa_public_key.pem | tr -d '\n'; echo
```

Windows

PowerShell 提取私鑰和公鑰字串：

```powershell
# 私鑰
Write-Output ((Get-Content rsa_private_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")

# 公鑰
Write-Output ((Get-Content rsa_public_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")
```

> ⚠️ 注意：生成的私鑰必須安全保管，勿洩露。


### 🛠️ 3. 建立 SDK 實例

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

## 🔑 關鍵概念

- 🆔 **OpenId**：用戶的唯一識別碼，例如 "HASH1756194148"。
- 🔐 **RSA 密鑰**：用於簽署和驗證請求以確保資料安全。
- ✍️ **API 簽名**：使用 MD5 和 RSA 演算法簽署請求，確保未被篡改。

有關詳細 API 說明，請參閱 [🧩 api-reference.md](./api-reference.md) 和 [🧩 examples.md](./examples.md)。

有關認證與安全，請參閱 [🧩 authentication.md](./authentication.md)

## 📎 附錄

有關更多詳細參考，請查看 [附錄](./appendix.md) 文件，其中包括以下內容：

- [🧩 ChainID 清單](./appendix.md#-chainid-清單)
- [🏷️ Token 類型](./appendix.md#-token-類型)
- [🌐 公共資訊](./appendix.md#-公共信息)
- [🔰 Token 基本資訊](./appendix.md#-token-基本信息)

> 💡 **提示**：附錄提供了支援的鏈資訊、token 類型和公共 token 資料，便於開發人員整合和使用 SDK。

## 📞 聯絡

如果您有任何問題，請聯絡服務提供商。  
💬 Telegram: [@ZeroSerivce](https://t.me/ZeroSerivce)