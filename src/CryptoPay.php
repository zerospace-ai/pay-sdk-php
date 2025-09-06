<?php

namespace Cryptopay\Chain;

class CryptoPay
{

    private $baseUrl;
    private $client;

    public function __construct($config){
        $this->baseUrl = isset($config['is_dev']) && $config['is_dev'] ? 'https://sandbox-api.privatex.io/sdk/' : 'https://sandbox-api.privatex.io/sdk/';
        $this->client = new Client($config);
    }

    /**
     * @param $open_id
     * @return bool|string
     */
    public function createUser($open_id){
        $data = [
            'OpenId' => $open_id
        ];

        return $this->client->post("{$this->baseUrl}/user/create", $data);
    }

    /**
     * @param $open_id
     * @param $chain_id
     * @return bool|string
     */
    public function getWalletAddresses($open_id, $chain_ids){
        $data = [
            'ChainIDs' => $chain_ids,
            'OpenId' => $open_id,
        ];

        return $this->client->post("{$this->baseUrl}/wallet/getWalletAddresses", $data);
    }

    /**
     * @param $open_id
     * @param $chain_id
     * @return bool|string
     */
    public function createWallet($open_id, $chain_id){
        $data = [
            'ChainID' => $chain_id,
            'OpenId' => $open_id,
        ];

        return $this->client->post("{$this->baseUrl}/wallet/create", $data);
    }

    /**
     * @param $open_id
     * @param $token_id
     * @param $amount
     * @param $address
     * @param $callback_url
     * @return bool|string
     */
    public function withdraw($open_id, $token_id, $amount, $address, $callback_url, $sn){
        $data = [
            'OpenId' => $open_id,
            'TokenId' => $token_id,
            'Amount' => $amount,
            'AddressTo' => $address,
            'CallBackUrl' => $callback_url,
            'SafeCheckCode' => $sn,
        ];

        return $this->client->post("{$this->baseUrl}/partner/UserWithdrawByOpenID", $data);
    }

    /**
     * @param $data
     * @return bool
     */
    public function verifyRsaSignature($data){
        return $this->client->checkSignature($data, $data['sign']);
    }

    /**
     * @param $data
     * @return bool
     */
    public function verifyWithdrawRsaSignature($data){
        return $this->client->checkwithdrawSignature($data, $data['sign']);
    }


}