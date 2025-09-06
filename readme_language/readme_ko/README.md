# CryptoPay Php SDK

![Php Version](https://img.shields.io/badge/php-8.4.12+-blue.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Telegram](https://img.shields.io/badge/chat-Telegram-blue?logo=telegram)](https://t.me/ZeroSerivce)

## 🌟 CryptoPay Php SDK에 오신 것을 환영합니다

CryptoPay Php SDK는 Golang으로 구현된 전문 암호화폐 서비스 SDK로, 사용자 등록, 지갑 생성, 입금 콜백 알림, 출금 등의 기능을 제공합니다.

장기간 사용을 통해 안전하고 안정적이며 확장하기 쉬운 것으로 입증되었습니다.

## ⚙️ 설치

```bash
git clone https://github.com/zerospace-ai/pay-sdk-php
```

참고: 컴파일에는 PHP 8.4.12+가 필요합니다 🛠️.
## 🚀 빠른 시작
### 1. ⚙️ config.yaml

```yaml
ApiKey: "your_api_key"
ApiSecret: "your_api_secret"
PlatformPubKey: "platform_public_key"
PlatformRiskPubKey: "platform_risk_public_key"
RsaPrivateKey: "your_rsa_private_key"
```

필드 설명:

• 🔑 ApiKey / ApiSecret:

상인 계정을 등록할 때 플랫폼에서 할당되며, API 요청 인증에 사용됩니다 ✅.

• 🛡️ PlatformPubKey / PlatformRiskPubKey:

플랫폼에서 제공하는 공개 키로, 플랫폼에서 반환된 데이터 또는 콜백 서명을 확인하여 정보 출처의 신뢰성을 보장합니다. PlatformRiskPubKey는 주로 위험 제어 관련 이벤트 확인에 사용됩니다 ⚠️.

• 🗝️ RsaPrivateKey:

상인이 생성한 RSA 개인 키로, 요청을 서명하여 요청 내용이 변조되지 않도록 보장합니다. 중요 참고: 개인 키는 기밀로 유지해야 하며, 공개하지 마십시오 🔒, 유출하지 마십시오 🚫.

### 2. RSA 키 쌍 생성 🔐

RSA 키 쌍을 사용하여 요청을 서명하면 데이터 보안을 보장합니다. 다음은 다른 운영 체제에서 키 쌍을 생성하고 키 문자열을 추출하는 방법을 설명합니다.

#### 2.1 OpenSSL을 사용하여 키 쌍 생성

```bash
# 2048비트 개인 키 생성
openssl genrsa -out rsa_private_key.pem 2048

# 개인 키에서 공개 키 생성
openssl rsa -in rsa_private_key.pem -out rsa_public_key.pem -pubout
```

> 💡 팁: 생성된 공개 키는 시작과 끝 -----BEGIN PUBLIC KEY----- / -----END PUBLIC KEY-----를 제거하고, 줄 바꿈을 제거하여 단일 줄 문자열로 변환한 후 플랫폼에 제출해야 합니다.
> 
> 키 문자열을 추출하고 공개 키를 플랫폼에 제출합니다 📤.
>
>Mac과 Windows에서 RSA 키 쌍을 생성하는 명령어는 Linux와 동일합니다.

#### 2.2 키 문자열 추출 🔑

Mac/Linux 또는 Git Bash/WSL/Cygwin에서:

```bash
# 개인 키 문자열 추출
grep -v '^-----' rsa_private_key.pem | tr -d '\n'; echo

# 공개 키 문자열 추출
grep -v '^-----' rsa_public_key.pem | tr -d '\n'; echo
```

Windows

PowerShell에서 개인 및 공개 키 문자열 추출:

```powershell
# 개인 키
Write-Output ((Get-Content rsa_private_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")

# 공개 키
Write-Output ((Get-Content rsa_public_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")
```

> ⚠️ 참고: 생성된 개인 키는 안전하게 보관해야 하며 유출되지 않아야 합니다.


### 🛠️ 3. SDK 인스턴스 생성

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

## 🔑 주요 개념

- 🆔 **OpenId**: 사용자의 고유 식별자, 예: "HASH1756194148".
- 🔐 **RSA 키**: 요청을 서명하고 확인하여 데이터 보안을 보장합니다.
- ✍️ **API 서명**: MD5 및 RSA 알고리즘을 사용하여 요청을 서명하여 변조되지 않도록 합니다.

자세한 API 설명은 [🧩 api-reference.md](./api-reference.md) 및 [🧩 examples.md](./examples.md)를 참조하십시오.

인증 및 보안에 대해서는 [🧩 authentication.md](./authentication.md)를 참조하십시오.

## 📎 부록

더 자세한 참조는 [부록](./appendix.md) 문서를 확인하십시오. 다음 내용을 포함합니다:

- [🧩 ChainID 목록](./appendix.md#-chainid-목록)
- [🏷️ 토큰 유형](./appendix.md#-토큰-유형)
- [🌐 공개 정보](./appendix.md#-공개-정보)
- [🔰 토큰 기본 정보](./appendix.md#-토큰-기본-정보)

> 💡 **팁**: 부록은 지원되는 체인 정보, 토큰 유형 및 공개 토큰 데이터를 제공하여 개발자가 SDK를 더 쉽게 통합하고 사용할 수 있도록 합니다.

## 📞 연락처

질문이 있으시면 서비스 제공자에게 문의하십시오.  
💬 Telegram: [@ZeroSerivce](https://t.me/ZeroSerivce)