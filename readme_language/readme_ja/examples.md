# 例 📝

このドキュメントは、CryptoPay Php SDKの使用例を提供します。Demoの実行、キーの生成、およびコールバックの処理を含みます。

## 1 SDKインスタンスオブジェクト 🛠️

### 1.1 必要な設定 ⚙️

1. ビジネス名を登録し、`ApiKey` と `ApiSecret` を取得します；

2. 独自の `RSA` キーペアを生成します；

3. プラットフォームの `RSA` 公開鍵を準備します；

### 1.2 署名オブジェクトの作成 🔏

1. 設定ファイル `config.yaml` を追加します。

```yaml
# ビジネス情報を設定
ApiKey: ""
ApiSecret: ""
# プラットフォーム公開鍵
PlatformPubKey: ""
# プラットフォームのブロック公開鍵
PlatformRiskPubKey: ""
# 独自の秘密鍵
RsaPrivateKey: ""
```

2. 設定ファイルをロードし、APIオブジェクトを作成します。

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

### 1.3 リクエストデータの作成と署名 ✍️

ユーザー作成を例にします。

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

### 1.4 リクエストの入力と開始 🚀

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

### 1.5 返却データの検証と解析 ✅

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

1. コマンド呼び出し 📞

2.1. 新規ユーザー登録 🆕

SDK の pay_sdk_php/ ディレクトリに移動し、$open_id 変数を修正します。

その後、プラットフォーム上で新しいユーザーを登録するために php  example/create_user.php を実行します。

既に登録されている open_id を登録しようとすると、エラーが返されます。

2.2. ウォレット登録 💼

SDK の pay_sdk_php/ ディレクトリに移動し、$open_id と $chain_id 変数を修正します。

その後、ユーザーのプラットフォーム上でのウォレット登録を完了するために php  example/create_wallet.php を実行します。

2.3. 入金アドレスの取得 📍

SDK の pay_sdk_php/ ディレクトリに移動し、$open_id と $chain_ids 変数を修正します。

その後、php  example/get_wallet_addresses.php を実行します。

2.4. 出金 💸

SDK の pay_sdk_php/ ディレクトリに移動し、$open_id, $token_id, $amount, $address, $callback_url(オプション), $sn(オプション) 変数を修正します。

その後、php  example/withdraw.php を実行します。