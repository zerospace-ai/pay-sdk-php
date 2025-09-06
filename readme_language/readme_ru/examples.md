# Примеры 📝

Этот документ предоставляет примеры использования CryptoPay Php SDK, включая запуск Demo, генерацию ключей и обработку обратных вызовов.

## 1 Объект экземпляра SDK 🛠️

### 1.1 Необходимая конфигурация ⚙️

1. Зарегистрируйте название вашего бизнеса и получите `ApiKey` и `ApiSecret`;

2. Сгенерируйте свою собственную пару ключей `RSA`;

3. Подготовьте публичный ключ `RSA` платформы;

### 1.2 Создание объекта подписи 🔏

1. Добавьте файл конфигурации `config.yaml`.

```yaml
# Настройка информации о бизнесе
ApiKey: ""
ApiSecret: ""
# Публичный ключ платформы
PlatformPubKey: ""
# Публичный ключ для блокировки платформы
PlatformRiskPubKey: ""
# Ваш собственный приватный ключ
RsaPrivateKey: ""
```

2. Загрузите файл конфигурации и создайте объект API.

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

### 1.3 Создание и подпись данных запроса. ✍️

Возьмем создание пользователя в качестве примера.

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

### 1.4 Заполнение и инициирование запроса 🚀

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

### 1.5 Проверка и разбор возвращаемых данных ✅

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

1. Вызов команды 📞

2.1. Регистрация нового пользователя 🆕

Перейдите в каталог SDK pay_sdk_php/ и измените переменную $open_id.

Затем выполните php  example/create_user.php, чтобы зарегистрировать нового пользователя на платформе.

Если попытаться зарегистрировать уже зарегистрированный open_id, будет возвращена ошибка.

2.2. Регистрация кошелька 💼

Перейдите в каталог SDK pay_sdk_php/ и измените переменные $open_id и $chain_id.

Затем выполните php  example/create_wallet.php, чтобы завершить регистрацию кошелька пользователя на платформе.

2.3. Получение адреса для пополнения 📍

Перейдите в каталог SDK pay_sdk_php/ и измените переменные $open_id и $chain_ids.

Затем выполните php  example/get_wallet_addresses.php.

2.4. Вывод средств 💸

Перейдите в каталог SDK pay_sdk_php/ и измените переменные $open_id, $token_id, $amount, $address, $callback_url (необязательно), $sn (необязательно).

Затем выполните php  example/withdraw.php.