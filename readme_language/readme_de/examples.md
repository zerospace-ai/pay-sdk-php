# Codebeispiele und Tools

Dieses Dokument ist in zwei Teile gegliedert:
1. **Szenariobasierte Codebeispiele:** Zeigt, wie API-Aufrufe und Verifizierungen in tatsächlichem Code gehandhabt werden.
2. **CLI-Tools-Leitfaden:** Erklärt, wie Sie die im SDK enthaltenen Skripte für schnelle Tests verwenden.

---

## 1. Szenariobasierte Codebeispiele

### 1.1 Vollständiger API-Aufruf und Antwortverifizierung

Der folgende Code zeigt, wie das SDK verwendet wird, um eine "Benutzer erstellen"-Anfrage zu konstruieren, eine HTTP-Anfrage zu senden und eine Sicherheitsverifizierung der von der Plattform zurückgegebenen Datensignatur durchzuführen.

```php
<?php
require __DIR__.'/../vendor/autoload.php';

use Cryptopay\Chain\CryptoPay;

function main() {
    // 1. Konfiguration initialisieren
    $config = [
        'ApiKey' => 'your_api_key',
        'ApiSecret' => 'your_api_secret',
        'RsaPrivateKey' => 'your_rsa_private_key',
        'PlatformPubKey' => 'platform_public_key',
    ];

    // 2. SDK-Instanz erstellen
    $cryptoPay = new CryptoPay($config);

    // 3. API aufrufen: Benutzer erstellen
    $openId = 'php_user_' . time();
    $result = $cryptoPay->createUser($openId);

    if (!$result) {
        echo "Anfrage fehlgeschlagen\n";
        return;
    }

    // 4. Antwort analysieren und verifizieren
    $postData = json_decode($result, true);

    if ($postData['code'] != 1) {
        echo "Antwort fehlgeschlagen! Code: " . $postData['code'] . ", Nachricht: " . $postData['msg'] . "\n";
        return;
    }

    // Plattform-Signatur verifizieren
    if ($cryptoPay->verifyRsaSignature($postData)) {
        echo "✅ Anfrage erfolgreich und verifiziert! OpenId: " . $postData['data']['OpenId'] . "\n";
    } else {
        echo "❌ Signaturverifizierung fehlgeschlagen!\n";
    }
}

main();
```


---

## 2. CLI-Tools-Leitfaden

Das SDK bietet Befehlszeilenskripte für schnelle Tests verschiedener Schnittstellen.

### 2.1 Abhängigkeiten installieren

Führen Sie den Befehl `composer install` im SDK-Stammverzeichnis aus, um die erforderlichen Abhängigkeiten zu installieren.

### 2.2 Schnittstellenbefehle testen

#### Neuen Benutzer registrieren
1. Ändern Sie `OpenId` in `example/create_user.php`.
2. Führen Sie `php example/create_user.php` aus.

#### Wallet-Registrierung
1. Geben Sie `UserOpenId` und `ChainID` in `example/create_wallet.php` an.
2. Führen Sie `php example/create_wallet.php` aus.

#### Einzahlungsadresse abrufen
1. Geben Sie `UserOpenId` und die abzufragenden `ChainIDs` (z. B. "1,56") in `example/get_wallet_addresses.php` an.
2. Führen Sie `php example/get_wallet_addresses.php` aus.

#### Auszahlung beantragen
1. Geben Sie `UserOpenId`, `TokenId`, `Amount`, `AddressTo`, `SafeCheckCode` und `CallBackUrl` in `example/withdraw.php` an.
2. Führen Sie `php example/withdraw.php` aus.

#### Kassierer-Auftrag erstellen
1. Geben Sie `outOrderNo`, `tokenId`, `quantity` und `notifyUrl` in `example/new_order.php` an.
2. Führen Sie `php example/new_order.php` aus.