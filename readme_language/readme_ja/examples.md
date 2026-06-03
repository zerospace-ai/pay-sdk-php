# コード例とツール

このドキュメントは 2 つの部分で構成されています。
1. **シナリオベースのコード例:** 実際のコードでの API 呼び出しと検証の処理方法を示します。
2. **CLI ツールガイド:** 素早いテストのために SDK に含まれているスクリプトの使用方法を説明します。

---

## 1. シナリオベースのコード例

### 1.1 完全な API 呼び出しと応答の検証

次のコードは、SDK を使用して「ユーザー作成」リクエストを構築し、HTTP リクエストを送信し、プラットフォームから返されたデータ署名のセキュリティ検証を実行する方法を示しています。

```php
<?php
require __DIR__.'/../vendor/autoload.php';

use Cryptopay\Chain\CryptoPay;

function main() {
    // 1. 設定の初期化
    $config = [
        'ApiKey' => 'your_api_key',
        'ApiSecret' => 'your_api_secret',
        'RsaPrivateKey' => 'your_rsa_private_key',
        'PlatformPubKey' => 'platform_public_key',
    ];

    // 2. SDK インスタンスの作成
    $cryptoPay = new CryptoPay($config);

    // 3. API 呼び出し: ユーザー作成
    $openId = 'php_user_' . time();
    $result = $cryptoPay->createUser($openId);

    if (!$result) {
        echo "リクエストに失敗しました\n";
        return;
    }

    // 4. 応答の解析と検証
    $postData = json_decode($result, true);

    if ($postData['code'] != 1) {
        echo "応答に失敗しました。コード: " . $postData['code'] . "、メッセージ: " . $postData['msg'] . "\n";
        return;
    }

    // プラットフォーム署名の検証
    if ($cryptoPay->verifyRsaSignature($postData)) {
        echo "✅ リクエストは成功し、検証されました。OpenId: " . $postData['data']['OpenId'] . "\n";
    } else {
        echo "❌ 署名の検証に失敗しました。\n";
    }
}

main();
```


---

## 2. CLI ツールガイド

SDK は、さまざまなインターフェースを素早くテストするためのコマンドラインスクリプトを提供しています。

### 2.1 依存関係のインストール

SDK のルートディレクトリで `composer install` コマンドを実行して、必要な依存関係をインストールします。

### 2.2 インターフェースコマンドのテスト

#### 新規ユーザー登録
1. `example/create_user.php` の `OpenId` を変更します。
2. `php example/create_user.php` を実行します。

#### ウォレット登録
1. `example/create_wallet.php` で `UserOpenId` と `ChainID` を指定します。
2. `php example/create_wallet.php` を実行します。

#### 入金アドレスの取得
1. `example/get_wallet_addresses.php` で `UserOpenId` と照会する `ChainIDs` (例: "1,56") を指定します。
2. `php example/get_wallet_addresses.php` を実行します。

#### 出金の申請
1. `example/withdraw.php` で `UserOpenId`, `TokenId`, `Amount`, `AddressTo`, `SafeCheckCode`, `CallBackUrl` を指定します。
2. `php example/withdraw.php` を実行します。

#### レジ注文の作成
1. `example/new_order.php` で `outOrderNo`, `tokenId`, `quantity`, `notifyUrl` を指定します。
2. `php example/new_order.php` を実行します。