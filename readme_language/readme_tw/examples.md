# 範例 📝

本文件提供 CryptoPay Php SDK 的使用範例，包括 Demo 運行、金鑰生成和回調處理。

## 1 SDK 實例物件 🛠️

### 1.1 所需配置 ⚙️

1. 註冊您的業務名稱並獲取 `ApiKey` 和 `ApiSecret`；

2. 生成您自己的 `RSA` 金鑰對；

3. 準備平台的 `RSA` 公鑰；

### 1.2 創建簽名物件 🔏

1. 添加配置文件 `config.yaml`。

```yaml
# 配置業務信息
ApiKey: ""
ApiSecret: ""
# 平台公鑰
PlatformPubKey: ""
# 用於封鎖平台的公鑰
PlatformRiskPubKey: ""
# 您自己的私鑰
RsaPrivateKey: ""
```

2. 加載配置文件並創建 API 物件。

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

### 1.3 創建並簽名請求數據。 ✍️

讓我們以用戶創建為例。

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

### 1.4 填充並發起請求 🚀

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

### 1.5 驗證解析返回數據 ✅

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

1. 呼叫命令 📞

2.1. 註冊新使用者 🆕

進入 SDK 的 pay_sdk_php/ 目錄，修改 $open_id 變數。

然後執行 php  example/create_user.php 來在平台上註冊一個新使用者。

如果嘗試註冊一個已經註冊過的 open_id，將會返回錯誤。

2.2. 錢包註冊 💼

進入 SDK 的 pay_sdk_php/ 目錄，修改 $open_id 和 $chain_id 變數。

然後執行 php  example/create_wallet.php 來完成使用者在平台上的錢包註冊。

2.3. 獲取充值地址 📍

進入 SDK 的 pay_sdk_php/ 目錄，修改 $open_id 和 $chain_ids 變數。

然後執行 php  example/get_wallet_addresses.php

2.4. 提現 💸

進入 SDK 的 pay_sdk_php/ 目錄，修改 $open_id, $token_id, $amount, $address, $callback_url(可選), $sn(可選) 變數。

然後執行 php  example/withdraw.php
