# Örnekler 📝

Bu belge, CryptoPay Php SDK'sı için kullanım örnekleri sağlar, Demo çalıştırma, anahtar üretimi ve geri arama işleme dahil.

## 1 SDK Örnek Nesnesi 🛠️

### 1.1 Gerekli Yapılandırma ⚙️

1. İşletme adınızı kaydedin ve `ApiKey` ve `ApiSecret` elde edin;

2. Kendi `RSA` anahtar çiftinizi üretin;

3. Platformun `RSA` genel anahtarını hazırlayın;

### 1.2 İmza Nesnesi Oluşturma 🔏

1. Bir yapılandırma dosyası `config.yaml` ekleyin.

```yaml
# İşletme bilgilerini yapılandırın
ApiKey: ""
ApiSecret: ""
# Platform genel anahtarı
PlatformPubKey: ""
# Platformu engellemek için genel anahtar
PlatformRiskPubKey: ""
# Kendi özel anahtarınız
RsaPrivateKey: ""
```

2. Yapılandırma dosyasını yükleyin ve API nesnesini oluşturun.

```php

	viper.SetConfigFile("config.yaml")
	viper.AddConfigPath(".")
	if err := viper.ReadInConfig(); err != nil {
		panic(fmt.Sprintf("Yapılandırma yüklenemedi: %s", err))
	}
	apiObj := api.NewSDK(api.SDKConfig{
		ApiKey:             viper.GetString("ApiKey"),
		ApiSecret:          viper.GetString("ApiSecret"),
		PlatformPubKey:     viper.GetString("PlatformPubKey"),
		PlatformRiskPubKey: viper.GetString("PlatformRiskPubKey"),
		RsaPrivateKey:      viper.GetString("RsaPrivateKey"),
	})

```

### 1.3 İstek Verilerini Oluşturma ve İmzalama ✍️

Kullanıcı oluşturmayı örnek olarak kullanalım.

```php

  // ....
	openId := "HASH1756194148"

	reqBody, timestamp, sign, clientSign, err := apiObj.CreateUser(openId)
	if err != nil {
		logrus.Warnln("Hata: ", err)
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

### 1.4 İsteği Doldurma ve Başlatma 🚀

```php
  // ....
	
	finalURL, err := url.JoinPath(api.DevNetEndpoint, api.PathCreateWallet)
	if err != nil {
		logrus.Warnln("Hata: ", err)
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

### 1.5 Dönüş Verilerini Doğrulama ve Ayrıştırma ✅

```php

	rspCommon := response_define.ResponseCommon{}
	err = json.Unmarshal(body, &rspCommon)
	if err != nil {
		logrus.Warnln("Hata: ", err)
		return
	}
	logrus.Infoln("Yanıt: ", rspCommon)

	if rspCommon.Code != response_define.SUCCESS {
		logrus.Warnln("Yanıt başarısız Kodu", rspCommon.Code, "Msg", rspCommon.Msg)
		return
	}

	rspCreateUser := response_define.ResponseCreateUser{}
	err = json.Unmarshal(body, &rspCreateUser)
	if err != nil {
		logrus.Warnln("Hata: ", err)
		return
	}
	logrus.Infoln("ResponseCreateUser: ", rspCreateUser)

	mapObj := rsa_utils.ToStringMap(body)
	err = apiObj.VerifyRSAsignature(mapObj, rspCreateUser.Sign)
	if err != nil {
		logrus.Warnln("Hata: ", err)
		return
	}

```

1. Komut Çağırma 📞

2.1. Yeni Kullanıcı Kaydı 🆕

SDK’nin pay_sdk_php/ dizinine girin, $open_id değişkenini düzenleyin.

Ardından platformda yeni bir kullanıcı kaydetmek için php  example/create_user.php çalıştırın.

Zaten kayıtlı olan bir open_id ile kayıt yapmayı denerseniz hata dönecektir.

2.2. Cüzdan Kaydı 💼

SDK’nin pay_sdk_php/ dizinine girin, $open_id ve $chain_id değişkenlerini düzenleyin.

Ardından kullanıcının platformdaki cüzdan kaydını tamamlamak için php  example/create_wallet.php çalıştırın.

2.3. Yatırma Adresi Alma 📍

SDK’nin pay_sdk_php/ dizinine girin, $open_id ve $chain_ids değişkenlerini düzenleyin.

Ardından php  example/get_wallet_addresses.php çalıştırın.

2.4. Çekim 💸

SDK’nin pay_sdk_php/ dizinine girin, $open_id, $token_id, $amount, $address, $callback_url (opsiyonel), $sn (opsiyonel) değişkenlerini düzenleyin.

Ardından php  example/withdraw.php çalıştırın.