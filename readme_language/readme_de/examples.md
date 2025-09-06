     
# Beispiel 📝 

 Dieses Dokument bietet Beispiele für die Verwendung des CryptoPay PHP SDK, einschließlich Demo-Ausführung, Schlüsselerzeugung und Callback-Handhabung. 

 ## 1 SDK Instanzobjekt 🛠️ 

 ### 1.1 Erforderliche Konfiguration ⚙️ 

 1. Registrieren Sie Ihren Geschäftsnamen und erhalten Sie `ApiKey` und `ApiSecret`; 

 2. Generieren Sie Ihr eigenes `RSA`-Schlüsselpaar; 

 3. Bereiten Sie den `RSA`-öffentlichen Schlüssel der Plattform vor; 

 ### 1.2 Erstellen eines Signaturobjekts 🔏 

 1. Fügen Sie die Konfigurationsdatei `config.yaml` hinzu. 

 ```yaml 
 # Konfigurieren Sie Geschäftsinformationen 
 ApiKey: "" 
 ApiSecret: "" 
 # Plattform-öffentlicher Schlüssel 
 PlatformPubKey: "" 
 # Zum Blockieren des öffentlichen Schlüssels der Plattform 
 PlatformRiskPubKey: "" 
 # Ihr eigener privater Schlüssel 
 RsaPrivateKey: "" 
 ``` 

 2. Laden Sie die Konfigurationsdatei und erstellen Sie ein API-Objekt. 

 ```php 

 	 $config = [ 
 		 'key' => 'dkhl346iwonfw436', 
 		 'secret' => '11e15f2d36f4e61c8a46fd426ae2189bc0406c1e37c29d8b136f75268a1d4216', 
 		 'public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3CeXmnRGmDxfxYco1Xazf7PzcOVJjWO/NTXFff+xmZC8DUKzu/VI7tn8geY7mXKVWa1vHkfw/H1Hqd7KBqHDp2DDuOetV6P4+2/5HXNEyy5dPR/hhYrAEDJMviB3BKfQ/NTExGm5yfCMlwPmGHJVUeglsSC0dmQOKsZ3aLOLs79L89TdIWObczdxNSeg4mlDPntkGC+hrwrEkHq4kPO16PIzZYqlQyv/nmVORhMrh4n4WeXUQufS24U07IKO27nRkwVtZgp8Gdl9JnWH1P70L35wBBiWF78/5SY1tmPwASoV6tA11ImQVFUSx0Zl5RWgnjqXnow8TIhI010HyI328QIDAQAB', 
 		 'private_key' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDcJ5eadEaYPF/FhyjVdrN/s/Nw5UmNY781NcV9/7GZkLwNQrO79Uju2fyB5juZcpVZrW8eR/D8fUep3soGocOnYMO4561Xo/j7b/kdc0TLLl09H+GFisAQMky+IHcEp9D81MTEabnJ8IyXA+YYclVR6CWxILR2ZA4qxndos4uzv0vz1N0hY5tzN3E1J6DiaUM+e2QYL6GvCsSQeriQ87Xo8jNliqVDK/+eZU5GEyuHifhZ5dRC59LbhTTsgo7budGTBW1mCnwZ2X0mdYfU/vQvfnAEGJYXvz/lJjW2Y/ABKhXq0DXUiZBUVRLHRmXlFaCeOpeejDxMiEjTXQfIjfbxAgMBAAECggEANMRYlfCgqwRtgA6xPtjAlS8wfjK2umjZ/4rv1w9eJgyGxjbilX5pCLa+yvO//Tt1iJUFOSDNJfdxIcoAai6Dkq6iusLszUDEAKAJ+YETUA/A0VG+4B8tgbRMqJVncXo3oSHuN5WPrlM1n0yT7fAZexRFVHseRfIdYytGm5XNOjuBzzaqghodKGw/IvdADw8eNYBMHUBFjgvjJSVKd99rsiKRRkzIVtBcTs7RxFXVdhB/PhvNxWugb3r3ihX52ho5uAoIHUGE7fzdIPO4iCYv9MRzKuSVwrAPIiJvAP7duwoC/INMFsqdCTuX1NdPS+58Ubgkdkmbf+8BK12dzK8EQQKBgQDtvJL2+j6Nd7rEf0+DK8ENjhWf3ktOoWX6dmepxDrJI1nHulpjZSWy5qmztMGiHF5vU0e7ARZZeFFA4aGXcScTT+9ffuWJP6JA04OJryrWTTEF4qT2aTVHJDOYuOtr6pD7541QdpIVNq6TQu7zITaTBoTbHvNYFVFTdZgi8/Lb9QKBgQDtET57KgDBiedVCwIdODPTTsSGqWzZ5J2qE63CYN4nvkHbtzcPKADnOxq0yaYfrr1olYdqIzi0VWR4bPbvuP7D9jtGbxp7kDI6/ZkdcOIAG/0aFqJaqGGnnzgWQJXv8jn08Z90nrikDrYxBX8U1s/9fMdur//csZmjQPmdBOXtjQKBgQCvhHa4cv61sTypkBijDi2klU7vzc2pis1gggR8uQxxrXC+XZ4YHfgcQeHudDg1OF6cME8YCHB4s7TBgxOrXHXt8ykWRviuQNXIqKBHiZTFzQ2xe6gw6HHWSSryySu+a9qIsGaLjk7B7LIstND3nYDOQZTatdoRIQP+6yXcQGD/9QKBgEyTIlyEP8REOC33JVKs4ciii8Z3mYp0Vx0lyB2eToQF554B+03w/QGzzLeS3w8i0Vmj2x7Ei79sSczAXa8nUVuZAKKKpsI83IzDd57T5JxmbgXsQ7sG4qxTOLmvWP8tfd0J4xi3YCrV+bGx9c+UZ5CYqo6tWPc/gsIB7d7zQxXNAoGAcxv32TAh+eRrVgIC0LMDXyKQ7pKt58RTjL8/SsSwavCKznvAp8S1pEde1/OjUfTiL42muJj1DghytwPIaam57X7/Ikgyz5PxgPzABCWv1BY0P4m37Cv8MYeqKv6e/OtjJs2O+r3GP12SI9RMP1trj7DLt5Z2TUmD5xeDEpdbpbw=', 
 		 'chain_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlxPMHGzg5ci2qZU9SxkTvTlsKKq7svS84QuDFYRNxkDTeLmQOmuPpxRa5v8Ujeg1M8QooeQlc3h33BQJVo1jqtxsoh5qo0Cj1J9gFn8TUF7xBfCX8VZ+Hqy+O2xSpFJHi9Uv8jCT4bnjzGw/53qCivJ7R0BY6mEcv5twrSv+IPqWYw4R5DClmPGVZWf+Bn+s+nsuRAWe3fNODlmWGMsg1nTFGd9JMdLiniud/wa9i2xlIHticCCF0WSap4y3Kp/QmsK1tl66NgpPi8BUfalQHI74snP/BtKSvcvt8+2OkksuI56x1g9PRrmPIFgUKzcgiom6Avd8/4EzoAYaKp12DwIDAQAB', 
 	 ]; 
     global $config; 
     $cryptoPay = new CryptoPay($config); 

 ``` 

 ### 1.3 Erstellen und Signieren von Anfragedaten. ✍️ 

 Nehmen Sie die Benutzererstellung als Beispiel. 

 ```php 

   // .... 
 	 
     $open_id = 'project1757065502'; 

     $result = $cryptoPay->createUser($open_id); 
      public function post($url, $data) { 
         $sign = $this->sign($data); 
         $clientSign = $this->encryption($data); 

 ``` 

 ### 1.4 Füllen und Initiieren der Anfrage 🚀 

 ```php 
       public function post($url, $data){ 
         $sign = $this->sign($data); 
         $clientSign = $this->encryption($data); 
         $header = [ 
             "key:{$this->config['key']}", 
             "sign:{$sign}", 
             "clientSign:{$clientSign}", 
             "Content-Type:application/json", 
             "timestamp:{$this->timestamp}", 
         ]; 
         $curl = curl_init(); //initialisieren 
         curl_setopt($curl, CURLOPT_URL,$url); 
         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); 
         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); 
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
         curl_setopt($curl, CURLOPT_POST, 1); 
         curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data)); 
         curl_setopt($curl, CURLOPT_HEADER,0); 
         curl_setopt ($curl, CURLOPT_HTTPHEADER, $header); 
         $result = curl_exec($curl); 
         if (curl_errno($curl)) { 
             print curl_error($curl); 
         } 
         curl_close($curl); 

         return $result; 
     } 

 ``` 

 ### 1.5 Überprüfen und Parsen der Rückgabedaten ✅ 

 ```php 

 	 public function checkSignature($data, $sign) 
     { 
         $toSign = self::getSignString($data); 
         $publicKeyId = openssl_pkey_get_public($this->config['chain_public_key']); 
         $result = openssl_verify($toSign, base64_decode($sign), $publicKeyId, OPENSSL_ALGO_MD5); 
         // openssl_free_key($publicKeyId); 
         return $result === 1 ? true : false; 
     } 

 ``` 

 ## 2. Aufrufbefehle 📞 

 ### 2.1. Registrieren eines neuen Benutzers 🆕 

 
 Gehen Sie in das Verzeichnis pay_sdk_php/ des SDK, ändern Sie die Variable $open_id. 

 Führen Sie dann php example/create_user.php aus, um einen neuen Benutzer auf der Plattform zu registrieren. 

 Wenn Sie versuchen, eine bereits registrierte open_id zu registrieren, wird ein Fehler zurückgegeben. 

 
 ### 2.2. Wallet-Registrierung 💼 

 Gehen Sie in das Verzeichnis pay_sdk_php/ des SDK, ändern Sie die Variablen $open_id und $chain_id. 

 Führen Sie dann php example/create_wallet.php aus, um die Wallet-Registrierung des Benutzers auf der Plattform abzuschließen. 

 ### 2.3. Abrufen der Aufladeadresse 📍 

 Gehen Sie in das Verzeichnis pay_sdk_php/ des SDK, ändern Sie die Variablen $open_id und $chain_ids. 

 Führen Sie dann php example/get_wallet_addresses.php aus 
        