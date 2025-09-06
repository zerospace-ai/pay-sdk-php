# CryptoPay Php SDK

![Php Sürümü](https://img.shields.io/badge/php-8.4.12+-blue.svg)
[![Lisans: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Telegram](https://img.shields.io/badge/chat-Telegram-blue?logo=telegram)](https://t.me/ZeroSerivce)

## 🌟 CryptoPay Php SDK'ya Hoş Geldiniz

CryptoPay Php SDK, Php ile uygulanan profesyonel bir kripto para hizmeti SDK'sıdır ve kullanıcı kaydı, cüzdan oluşturma, para yatırma geri çağrı bildirimleri, para çekme ve diğer işlevleri sağlar.

Uzun süreli kullanım ile güvenli, istikrarlı ve genişletilmesi kolay olduğu kanıtlanmıştır.

## ⚙️ Kurulum

```bash
git clone https://github.com/zerospace-ai/pay-sdk-php
```

Not: Derleme için PHP 8.4.12+ gereklidir 🛠️.
## 🚀 Hızlı Başlangıç
### 1. ⚙️ config.yaml

```yaml
ApiKey: "your_api_key"
ApiSecret: "your_api_secret"
PlatformPubKey: "platform_public_key"
PlatformRiskPubKey: "platform_risk_public_key"
RsaPrivateKey: "your_rsa_private_key"
```

Alan açıklamaları:

• 🔑 ApiKey / ApiSecret:

Tüccar hesabı kaydederken platform tarafından atanır, API isteği kimlik doğrulaması için kullanılır ✅.

• 🛡️ PlatformPubKey / PlatformRiskPubKey:

Platform tarafından sağlanan genel anahtarlar, platform tarafından döndürülen verileri veya geri çağrı imzalarını doğrulamak için kullanılır, bilgi kaynaklarının güvenilir olmasını sağlar. PlatformRiskPubKey esas olarak risk kontrolü ile ilgili olay doğrulaması için kullanılır ⚠️.

• 🗝️ RsaPrivateKey:

Tüccar tarafından oluşturulan RSA özel anahtarı, istekleri imzalamak için kullanılır, istek içeriğinin değiştirilmediğini sağlar. Önemli not: Özel anahtar gizli tutulmalıdır 🔒, ifşa etmeyin 🚫.

### 2. RSA Anahtar Çifti Oluşturma 🔐

İstekleri imzalamak için RSA anahtar çifti kullanarak veri güvenliğini sağlar. Aşağıda farklı işletim sistemlerinde anahtar çifti oluşturma ve anahtar dizelerini çıkarma açıklanmaktadır.

#### 2.1 OpenSSL Kullanarak Anahtar Çifti Oluşturma

```bash
# 2048-bit özel anahtar oluştur
openssl genrsa -out rsa_private_key.pem 2048

# Özel anahtardan genel anahtar oluştur
openssl rsa -in rsa_private_key.pem -out rsa_public_key.pem -pubout
```

> 💡 İpucu: Oluşturulan genel anahtar, başlangıç ve bitiş -----BEGIN PUBLIC KEY----- / -----END PUBLIC KEY----- kaldırılmalı, satır sonları kaldırılmalı, tek satır dizesine dönüştürülmeli ve platforma gönderilmelidir.
> 
> Anahtar dizelerini çıkarın ve genel anahtarı platforma gönderin 📤.
>
>Mac ve Windows'ta RSA anahtar çiftlerini oluşturma komutları Linux ile aynıdır.

#### 2.2 Anahtar Dizelerini Çıkarma 🔑

Mac/Linux veya Git Bash/WSL/Cygwin'da:

```bash
# Özel anahtar dizesini çıkar
grep -v '^-----' rsa_private_key.pem | tr -d '\n'; echo

# Genel anahtar dizesini çıkar
grep -v '^-----' rsa_public_key.pem | tr -d '\n'; echo
```

Windows

PowerShell ile özel ve genel anahtar dizelerini çıkarma:

```powershell
# Özel anahtar
Write-Output ((Get-Content rsa_private_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")

# Genel anahtar
Write-Output ((Get-Content rsa_public_key.pem | Where-Object {$_ -notmatch "^-----"}) -join "")
```

> ⚠️ Not: Oluşturulan özel anahtar güvenli tutulmalı ve sızdırılmamalıdır.


### 🛠️ 3. SDK Örneği Oluşturma

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

## 🔑 Ana Kavramlar

- 🆔 **OpenId**: Kullanıcının benzersiz tanımlayıcısı, örneğin "HASH1756194148".
- 🔐 **RSA Anahtarı**: İstekleri imzalamak ve doğrulamak için kullanılır, veri güvenliğini sağlar.
- ✍️ **API İmzası**: İstekleri imzalamak için MD5 ve RSA algoritmalarını kullanır, değiştirilmediğini sağlar.

Ayrıntılı API açıklamaları için lütfen [🧩 api-reference.md](./api-reference.md) ve [🧩 examples.md](./examples.md) dosyalarına bakın.

Kimlik Doğrulama ve Güvenlik için lütfen [🧩 authentication.md](./authentication.md) dosyasına bakın

## 📎 Ek

Daha ayrıntılı referanslar için lütfen [Ek](./appendix.md) belgesine bakın, aşağıdaki içerikleri içerir:

- [🧩 ChainID Listesi](./appendix.md#-chainid-listesi)
- [🏷️ Token Türleri](./appendix.md#-token-türü)
- [🌐 Genel Bilgiler](./appendix.md#-genel-bilgiler)
- [🔰 Token Temel Bilgileri](./appendix.md#-token-temel-bilgileri)

> 💡 **İpucu**: Ek, desteklenen zincir bilgilerini, token türlerini ve genel token verilerini sağlar, geliştiricilerin SDK'yi entegre etmesini ve kullanmasını kolaylaştırır.

## 📞 İletişim

Herhangi bir sorunuz varsa, lütfen hizmet sağlayıcısıyla iletişime geçin.  
💬 Telegram: [@ZeroSerivce](https://t.me/ZeroSerivce)