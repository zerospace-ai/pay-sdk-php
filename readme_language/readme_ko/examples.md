# 예시 📝

이 문서는 CryptoPay Php SDK의 사용 예시를 제공합니다. 데모 실행, 키 생성 및 콜백 처리 등을 포함합니다.

## 1 SDK 인스턴스 객체 🛠️

### 1.1 필수 구성 ⚙️

1. 비즈니스 이름을 등록하고 `ApiKey`와 `ApiSecret`을 얻습니다;

2. 자신의 `RSA` 키 쌍을 생성합니다;

3. 플랫폼의 `RSA` 공개 키를 준비합니다;

### 1.2 서명 객체 생성 🔏

1. 구성 파일 `config.yaml`을 추가합니다.

```yaml
# 비즈니스 정보 구성
ApiKey: ""
ApiSecret: ""
# 플랫폼 공개 키
PlatformPubKey: ""
# 플랫폼 차단용 공개 키
PlatformRiskPubKey: ""
# 자신의 개인 키
RsaPrivateKey: ""
```

2. 구성 파일을 로드하고 API 객체를 생성합니다.

```php

	viper.SetConfigFile("config.yaml")
	viper.AddConfigPath(".")
	if err := viper.ReadInConfig(); err != nil {
		panic(fmt.Sprintf("Failed to load config: %s", err))
	}
	apiObj := api.NewSDK(api.SDKConfig{
		ApiKey:             viper.GetString("ApiKey"),
		ApiSecret:          viper.GetString("ApiSecret"),
		PlatformPubKey:     viper.GetString("PlatformPubKey"),
		PlatformRiskPubKey: viper.GetString("PlatformRiskPubKey"),
		RsaPrivateKey:      viper.GetString("RsaPrivateKey"),
	})

```

### 1.3 요청 데이터 생성 및 서명 ✍️

사용자 생성을 예로 들어 보겠습니다.

```php

  // ....
	openId := "HASH1756194148"

	reqBody, timestamp, sign, clientSign, err := apiObj.CreateUser(openId)
	if err != nil {
		logrus.Warnln("Error: ", err)
		return
	}

```

```php
    dataStr := rsa_utils.ComposeParams(mapData)

	timestamp = strconv.FormatInt(time.Now().UnixMilli(), 10)
	sign = s.public function sign($data)(dataStr, timestamp)

	jStr, err := json.Marshal(&req)
	if err != nil {
		return nil, timestamp, sign, clientSign, err
	}

	reqMapObj := rsa_utils.ToStringMap(jStr)
	clientSign, err = s.public function encryption($data)(reqMapObj)
```

### 1.4 요청 채우기 및 시작 🚀

```php
  // ....
	
	finalURL, err := url.JoinPath(api.DevNetEndpoint, api.PathCreateWallet)
	if err != nil {
		logrus.Warnln("Error: ", err)
		return
	}

	resp, err := client.R().
		SetHeader("Content-Type", "application/json").
		SetBody(reqBody).
		SetHeader("key", apiObj.GetApiKey()).
		SetHeader("timestamp", timestamp).
		SetHeader("sign", sign).
		SetHeader("clientSign", clientSign).
		Post(finalURL)

```

### 1.5 반환 데이터 검증 및 파싱 ✅

```php

	rspCommon := response_define.ResponseCommon{}
	err = json.Unmarshal(body, &rspCommon)
	if err != nil {
		logrus.Warnln("Error: ", err)
		return
	}
	logrus.Infoln("Response: ", rspCommon)

	if rspCommon.Code != response_define.SUCCESS {
		logrus.Warnln("Response fail Code", rspCommon.Code, "Msg", rspCommon.Msg)
		return
	}

	rspCreateUser := response_define.ResponseCreateUser{}
	err = json.Unmarshal(body, &rspCreateUser)
	if err != nil {
		logrus.Warnln("Error: ", err)
		return
	}
	logrus.Infoln("ResponseCreateUser: ", rspCreateUser)

	mapObj := rsa_utils.ToStringMap(body)
	err = apiObj.VerifyRSAsignature(mapObj, rspCreateUser.Sign)
	if err != nil {
		logrus.Warnln("Error: ", err)
		return
	}

```
好的，這裡是翻譯成韓文的版本（保留英文字樣和格式不變）：

⸻

2. 명령 호출 📞

2.1. 새 사용자 등록 🆕

SDK의 pay_sdk_php/ 디렉토리로 이동하여 $open_id 변수를 수정합니다.

그런 다음 플랫폼에서 새 사용자를 등록하려면 php  example/create_user.php 를 실행합니다.

이미 등록된 open_id로 등록을 시도하면 오류가 반환됩니다.

2.2. 지갑 등록 💼

SDK의 pay_sdk_php/ 디렉토리로 이동하여 $open_id 및 $chain_id 변수를 수정합니다.

그런 다음 사용자의 플랫폼 지갑 등록을 완료하려면 php  example/create_wallet.php 를 실행합니다.

2.3. 입금 주소 가져오기 📍

SDK의 pay_sdk_php/ 디렉토리로 이동하여 $open_id 및 $chain_ids 변수를 수정합니다.

그런 다음 php  example/get_wallet_addresses.php 를 실행합니다.

2.4. 출금 💸

SDK의 pay_sdk_php/ 디렉토리로 이동하여 $open_id, $token_id, $amount, $address, $callback_url(선택 사항), $sn(선택 사항) 변수를 수정합니다.

그런 다음 php  example/withdraw.php 를 실행합니다.