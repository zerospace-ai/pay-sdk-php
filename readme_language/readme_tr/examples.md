# Kod Örnekleri ve Araçlar

Bu belge iki bölüme ayrılmıştır:
1. **Senaryo Tabanlı Kod Örnekleri:** API çağrılarının ve doğrulamanın gerçek kodda nasıl işlendiğini gösterir.
2. **CLI Araçları Kılavuzu:** Hızlı testler için SDK'ya dahil edilen betiklerin nasıl kullanılacağını açıklar.

---

## 1. Senaryo Tabanlı Kod Örnekleri

### 1.1 Tam API Çağrısı ve Yanıt Doğrulaması

Aşağıdaki kod, bir "Kullanıcı Oluştur" isteği oluşturmak, bir HTTP isteği göndermek ve platform tarafından döndürülen veri imzası üzerinde güvenlik doğrulaması gerçekleştirmek için SDK'nın nasıl kullanılacağını gösterir.

```php
<?php
require __DIR__.'/../vendor/autoload.php';

use Cryptopay\Chain\CryptoPay;

function main() {
    // 1. Yapılandırmayı başlat
    $config = [
        'ApiKey' => 'your_api_key',
        'ApiSecret' => 'your_api_secret',
        'RsaPrivateKey' => 'your_rsa_private_key',
        'PlatformPubKey' => 'platform_public_key',
    ];

    // 2. SDK örneğini oluştur
    $cryptoPay = new CryptoPay($config);

    // 3. API'yi çağır: Kullanıcı Oluştur
    $openId = 'php_user_' . time();
    $result = $cryptoPay->createUser($openId);

    if (!$result) {
        echo "İstek başarısız oldu\n";
        return;
    }

    // 4. Yanıtı çözümle ve doğrula
    $postData = json_decode($result, true);

    if ($postData['code'] != 1) {
        echo "Yanıt başarısız! Kod: " . $postData['code'] . ", Mesaj: " . $postData['msg'] . "\n";
        return;
    }

    // Platform imzasını doğrula
    if ($cryptoPay->verifyRsaSignature($postData)) {
        echo "✅ İstek başarılı ve doğrulandı! OpenId: " . $postData['data']['OpenId'] . "\n";
    } else {
        echo "❌ İmza doğrulaması başarısız oldu!\n";
    }
}

main();
```


---

## 2. CLI Araçları Kılavuzu

SDK, çeşitli arayüzlerin hızlı testi için komut satırı betikleri sağlar.

### 2.1 Bağımlılıkları Yükle

Gerekli bağımlılıkları yüklemek için SDK kök dizininde `composer install` komutunu çalıştırın.

### 2.2 Arayüz Komutlarını Test Et

#### Yeni Kullanıcı Kaydet
1. `example/create_user.php` dosyasındaki `OpenId`yi değiştirin.
2. `php example/create_user.php` komutunu çalıştırın.

#### Cüzdan Kaydı
1. `example/create_wallet.php` dosyasında `UserOpenId` ve `ChainID` belirtin.
2. `php example/create_wallet.php` komutunu çalıştırın.

#### Para Yatırma Adresini Al
1. `example/get_wallet_addresses.php` dosyasında `UserOpenId` ve sorgulanacak `ChainIDs` (ör. "1,56") değerlerini belirtin.
2. `php example/get_wallet_addresses.php` komutunu çalıştırın.

#### Para Çekme Başvurusu
1. `example/withdraw.php` dosyasında `UserOpenId`, `TokenId`, `Amount`, `AddressTo`, `SafeCheckCode` ve `CallBackUrl` değerlerini belirtin.
2. `php example/withdraw.php` komutunu çalıştırın.

#### Kasiyer Siparişi Oluştur
1. `example/new_order.php` dosyasında `outOrderNo`, `tokenId`, `quantity` ve `notifyUrl` değerlerini belirtin.
2. `php example/new_order.php` komutunu çalıştırın.