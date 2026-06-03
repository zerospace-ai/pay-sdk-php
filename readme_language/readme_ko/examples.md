# 코드 예제 및 도구

이 문서는 두 부분으로 나뉩니다.
1. **시나리오 기반 코드 예제:** 실제 코드에서 API 호출 및 검증을 처리하는 방법을 보여줍니다.
2. **CLI 도구 가이드:** 빠른 테스트를 위해 SDK에 포함된 스크립트를 사용하는 방법을 설명합니다.

---

## 1. 시나리오 기반 코드 예제

### 1.1 완전한 API 호출 및 응답 검증

다음 코드는 SDK를 사용하여 "사용자 생성" 요청을 구성하고, HTTP 요청을 보내고, 플랫폼에서 반환된 데이터 서명에 대해 보안 검증을 수행하는 방법을 보여줍니다.

```php
<?php
require __DIR__.'/../vendor/autoload.php';

use Cryptopay\Chain\CryptoPay;

function main() {
    // 1. 구성 초기화
    $config = [
        'ApiKey' => 'your_api_key',
        'ApiSecret' => 'your_api_secret',
        'RsaPrivateKey' => 'your_rsa_private_key',
        'PlatformPubKey' => 'platform_public_key',
    ];

    // 2. SDK 인스턴스 생성
    $cryptoPay = new CryptoPay($config);

    // 3. API 호출: 사용자 생성
    $openId = 'php_user_' . time();
    $result = $cryptoPay->createUser($openId);

    if (!$result) {
        echo "요청 실패\n";
        return;
    }

    // 4. 응답 구문 분석 및 검증
    $postData = json_decode($result, true);

    if ($postData['code'] != 1) {
        echo "응답 실패! 코드: " . $postData['code'] . ", 메시지: " . $postData['msg'] . "\n";
        return;
    }

    // 플랫폼 서명 검증
    if ($cryptoPay->verifyRsaSignature($postData)) {
        echo "✅ 요청 성공 및 검증 완료! OpenId: " . $postData['data']['OpenId'] . "\n";
    } else {
        echo "❌ 서명 검증 실패!\n";
    }
}

main();
```


---

## 2. CLI 도구 가이드

SDK는 다양한 인터페이스를 빠르게 테스트할 수 있는 명령줄 스크립트를 제공합니다.

### 2.1 종속성 설치

SDK 루트 디렉토리에서 `composer install` 명령을 실행하여 필요한 종속성을 설치합니다.

### 2.2 인터페이스 명령 테스트

#### 새 사용자 등록
1. `example/create_user.php`에서 `OpenId`를 수정합니다.
2. `php example/create_user.php`를 실행합니다.

#### 지갑 등록
1. `example/create_wallet.php`에서 `UserOpenId`와 `ChainID`를 지정합니다.
2. `php example/create_wallet.php`를 실행합니다.

#### 입금 주소 가져오기
1. `example/get_wallet_addresses.php`에서 `UserOpenId`와 조회할 `ChainIDs`(예: "1,56")를 지정합니다.
2. `php example/get_wallet_addresses.php`를 실행합니다.

#### 출금 신청
1. `example/withdraw.php`에서 `UserOpenId`, `TokenId`, `Amount`, `AddressTo`, `SafeCheckCode`, `CallBackUrl`을 지정합니다.
2. `php example/withdraw.php`를 실행합니다.

#### 카셔 주문 생성
1. `example/new_order.php`에서 `outOrderNo`, `tokenId`, `quantity`, `notifyUrl`을 지정합니다.
2. `php example/new_order.php`를 실행합니다.