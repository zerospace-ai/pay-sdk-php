<?php

namespace Cryptopay\Chain;

class Client
{

    private $config;
    private $timestamp;

    public function __construct($config)
    {
        $this->config = $config;

        $this->config['public_key'] = isset($this->config['public_key']) ? $this->handelPublicKey($this->config['public_key']) : '';
        $this->config['private_key'] = isset($this->config['private_key']) ? $this->handelPrivateKey($this->config['private_key']) : '';
        $this->config['chain_public_key'] = isset($this->config['chain_public_key']) ? $this->handelPublicKey($this->config['chain_public_key']) : '';
        $this->config['chain_withdraw_public_key'] = isset($this->config['chain_withdraw_public_key']) ? $this->handelPublicKey($this->config['chain_withdraw_public_key']) : '';

        $this->timestamp = $this->getMillisecond();
    }

    public function handelPublicKey($public_key)
    {
        $pem = chunk_split($public_key, 64, "\n");
        return "-----BEGIN PUBLIC KEY-----\n" . $pem . "-----END PUBLIC KEY-----\n";
    }

    public function handelPrivateKey($private_key)
    {
        $pem = chunk_split($private_key, 64, "\n");
        return "-----BEGIN PRIVATE KEY-----\n" . $pem . "-----END PRIVATE KEY-----\n";
    }

    /**
     * @return false|string
     */
    public function getMillisecond()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float) sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return substr($msectime, 0, 13);
    }

    /**
     * @param $data
     * @return string
     */
    public function sign($data)
    {

        ksort($data);
        $dataArray = [];
        foreach ($data as $key => $value) {
            $dataArray[] = "{$key}={$value}";
        }
        $dataStr = implode('&', $dataArray);

        return md5($this->config['secret'] . $dataStr . $this->timestamp);
    }

    /**
     * @param $data
     * @return string
     */
    public function encryption($data)
    {
        if (is_array($data))
            $signString = self::getSignString($data);
        else
            $signString = $data;
        $privKeyId = openssl_pkey_get_private($this->config['private_key']);
        $signature = '';
        openssl_sign($signString, $signature, $privKeyId, OPENSSL_ALGO_MD5);
        // openssl_free_key($privKeyId);
        return base64_encode($signature);
    }


    /**
     * @param $data
     * @param $sign
     * @return bool
     */
    public function checkSignature($data, $sign)
    {
        $toSign = self::getSignString($data);
        $publicKeyId = openssl_pkey_get_public($this->config['chain_public_key']);
        $result = openssl_verify($toSign, base64_decode($sign), $publicKeyId, OPENSSL_ALGO_MD5);
        // openssl_free_key($publicKeyId);
        return $result === 1 ? true : false;
    }


    /**
     * @param $data
     * @param $sign
     * @return bool
     */
    public function checkWithdrawSignature($data, $sign)
    {
        $toSign = self::getSignString($data);
        $publicKeyId = openssl_pkey_get_public($this->config['chain_withdraw_public_key']);
        $result = openssl_verify($toSign, base64_decode($sign), $publicKeyId, OPENSSL_ALGO_MD5);
        // openssl_free_key($publicKeyId);
        return $result === 1 ? true : false;
    }
    /**
     * 递归把值转成字符串
     */
    private static function valueAsString($value): string
    {
        if (is_null($value)) {
            return '';
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_numeric($value)) {
            return (string) $value;
        } elseif (is_string($value)) {
            return $value;
        } elseif (is_array($value)) {
            // 区分数组（索引）还是对象（关联数组）
            if (array_keys($value) === range(0, count($value) - 1)) {
                // 索引数组
                $str = '';
                foreach ($value as $item) {
                    $str .= self::valueAsString($item);
                }
                return $str;
            } else {
                // 关联数组（object），按 key 排序（忽略大小写）
                uksort($value, function ($a, $b) {
                    return strcasecmp($a, $b);
                });
                $str = '';
                foreach ($value as $v) {
                    $str .= self::valueAsString($v);
                }
                return $str;
            }
        }
        return '';
    }

    /**
     * 等价 Go 的 getSignString
     */
    public static function getSignString(array $data): string
    {
        unset($data['sign']); // 去掉 sign

        // 按 key 排序（忽略大小写）
        uksort($data, function ($a, $b) {
            return strcasecmp($a, $b);
        });

        $pairs = [];
        foreach ($data as $k => $v) {
            $pairs[] = $k . '=' . self::valueAsString($v);
        }

        return implode('&', $pairs);
    }
    /**
     * @param $url
     * @param $data
     * @return bool|string
     */
    public function post($url, $data)
    {
        $sign = $this->sign($data);
        $clientSign = $this->encryption($data);
        $header = [
            "key:{$this->config['key']}",
            "sign:{$sign}",
            "clientSign:{$clientSign}",
            "Content-Type:application/json",
            "timestamp:{$this->timestamp}",
        ];
        $curl = curl_init(); //初始化
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            print curl_error($curl);
        }
        curl_close($curl);

        return $result;
    }
}