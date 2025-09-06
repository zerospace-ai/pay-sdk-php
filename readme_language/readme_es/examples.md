# Ejemplos 📝

Este documento proporciona ejemplos de uso para el CryptoPay Php SDK, incluyendo la ejecución de Demo, generación de claves y manejo de callbacks.

## 1 Objeto de Instancia SDK 🛠️

### 1.1 Configuración Requerida ⚙️

1. Registre su nombre de negocio y obtenga el `ApiKey` y `ApiSecret`;

2. Genere su propio par de claves `RSA`;

3. Prepare la clave pública `RSA` de la plataforma;

### 1.2 Creando un Objeto de Firma 🔏

1. Agregue un archivo de configuración `config.yaml`.

```yaml
# Configurar información de negocio
ApiKey: ""
ApiSecret: ""
# Clave pública de la plataforma
PlatformPubKey: ""
# Clave pública para bloquear la plataforma
PlatformRiskPubKey: ""
# Su propia clave privada
RsaPrivateKey: ""
```

2. Cargue el archivo de configuración y cree el objeto API.

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

### 1.3 Crear y firmar los datos de solicitud. ✍️

Usemos la creación de usuario como ejemplo.

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

### 1.4 Rellenar e Iniciar la Solicitud 🚀

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

### 1.5 Verificar el análisis de los datos de retorno ✅

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

1. Llamada de Comando 📞

2.1. Registro de Nuevo Usuario 🆕

Ingrese al directorio SDK pay_sdk_php/ y modifique la variable $open_id.

Luego ejecute php  example/create_user.php para registrar un nuevo usuario en la plataforma.

Si intenta registrar un open_id que ya está registrado, se devolverá un error.

2.2. Registro de Wallet 💼

Ingrese al directorio SDK pay_sdk_php/ y modifique las variables $open_id y $chain_id.

Luego ejecute php  example/create_wallet.php para completar el registro de la wallet del usuario en la plataforma.

2.3. Obtener Dirección de Depósito 📍

Ingrese al directorio SDK pay_sdk_php/ y modifique las variables $open_id y $chain_ids.

Luego ejecute php  example/get_wallet_addresses.php.

2.4. Retiro 💸

Ingrese al directorio SDK pay_sdk_php/ y modifique las variables $open_id, $token_id, $amount, $address, $callback_url(opcional), $sn(opcional).

Luego ejecute php  example/withdraw.php.