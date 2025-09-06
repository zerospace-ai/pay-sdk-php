# CryptoPay Php SDK

![Php Version](https://img.shields.io/badge/php-8.4.12+-blue.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Telegram](https://img.shields.io/badge/chat-Telegram-blue?logo=telegram)](https://t.me/ZeroSerivce)

## 🌟 CryptoPay Php SDKへようこそ

CryptoPay Php SDKは、Golangで実装されたプロフェッショナルな暗号通貨サービスSDKで、ユーザー登録、ウォレット生成、入金コールバック通知、出金などの機能を提供します。

長期使用を通じて、安全で安定し、拡張しやすいことが証明されています。

## ⚙️ インストール

```bash
git clone https://github.com/zerospace-ai/pay-sdk-php
```

注: コンパイルにはGo 1.18+が必要です 🛠️。
## 🚀 クイックスタート
### 1. ⚙️ config.yaml

```yaml
ApiKey: "your_api_key"
ApiSecret: "your_api_secret"
PlatformPubKey: "platform_public_key"
PlatformRiskPubKey: "platform_risk_public_key"
RsaPrivateKey: "your_rsa_private_key"
```

フィールドの説明:

• 🔑 ApiKey / ApiSecret:

マーチャントアカウント登録時にプラットフォームから割り当てられ、APIリクエスト認証に使用されます ✅。

• 🛡️ PlatformPubKey / PlatformRiskPubKey:

プラットフォームから提供される公開鍵で、プラットフォームから返されるデータやコールバックの署名を検証し、情報の信頼性を確保します。PlatformRiskPubKeyは主にリスクコントロール関連のイベント検証に使用されます ⚠️。

• 🗝️ RsaPrivateKey:

マーチャントが生成したRSA秘密鍵で、リクエストに署名し、リクエスト内容が改ざんされていないことを保証します。重要な注意: 秘密鍵は機密に保つ必要があります 🔒、公開しないでください 🚫。

### 2. RSAキーペアの生成 🔐

リクエストに署名するためにRSAキーペアを使用してデータセキュリティを確保します。以下では、異なるオペレーティングシステムでキーペアを生成し、キー文字列を抽出する方法を説明します。

#### 2.1 OpenSSLを使用してキーペアを生成

```bash
# 2048ビットの秘密鍵を生成
openssl genrsa -out rsa_private_key.pem 2048

# 秘密鍵から公開鍵を生成
openssl rsa -in rsa_private_key.pem -out rsa_public_key.pem -pubout
```

> 💡 Tip: 生成された公開鍵は、始めと終わりの -----BEGIN PUBLIC KEY----- / -----END PUBLIC KEY----- を削除し、改行を削除して1行の文字列に変換し、プラットフォームに提出します。
> 
> キー文字列を抽出し、公開鍵をプラットフォームに提出します 📤。
>
>MacとWindowsでのRSAキーペア生成コマンドはLinuxと同じです。

#### 2.2 キー文字列の抽出 🔑

Mac/LinuxまたはGit Bash/WSL/Cygwinで:

```bash
# 秘密鍵文字列を抽出
grep -v '^-----' rsa_private_key.pem | tr -d '\n'; echo

# 公開鍵文字列を抽出
grep -v '^-----' rsa_public_key.pem | tr -d '\n'; echo
```

Windows

PowerShellで秘密鍵と公開鍵文字列を抽出:

```powershell
# 秘密鍵
Write-Output ((Get-Content rsa_private_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")

# 公開鍵
Write-Output ((Get-Content rsa_public_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")
```

> ⚠️ Note: 生成された秘密鍵は安全に保管し、漏洩しないようにしてください。


### 🛠️ 3. SDKインスタンスの作成

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

## 🔑 キーコンセプト

- 🆔 **OpenId**: ユーザーの一意の識別子、例えば "HASH1756194148"。
- 🔐 **RSA Key**: リクエストの署名と検証に使用され、データセキュリティを確保します。
- ✍️ **API Signature**: MD5とRSAアルゴリズムを使用してリクエストに署名し、改ざんされていないことを保証します。

詳細なAPI説明については、[🧩 api-reference.md](./api-reference.md) と [🧩 examples.md](./examples.md) を参照してください。

認証とセキュリティについては、[🧩 authentication.md](./authentication.md) を参照してください

## 📎 付録

より詳細な参照については、[付録](./appendix.md) ドキュメントを確認してください。以下の内容を含みます:

- [🧩 ChainID List](./appendix.md#-chainid-リスト)
- [🏷️ Token Types](./appendix.md#-トークンタイプ)
- [🌐 Public Information](./appendix.md#-公開情報)
- [🔰 Token Basic Information](./appendix.md#-トークン基本情報)

> 💡 **Tip**: 付録では、サポートされているチェーン情報、トークンタイプ、および公開トークンデータを提供し、開発者がSDKを統合して使用しやすくします。

## 📞 連絡先

質問がある場合は、サービスプロバイダーに連絡してください。  
💬 Telegram: [@ZeroSerivce](https://t.me/ZeroSerivce)