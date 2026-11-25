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
        return $this->client->checkWithdrawSignature($data, $data['sign']);
    }

    /**
     * Build cashier newOrder request payload, omitting null/empty optional fields.
     *
     * @param string $out_order_no
     * @param int|string $token_id
     * @param string|int|float $quantity
     * @param string|null $notify_url
     * @param string|null $out_user_id
     * @param string|null $order_detail
     * @return array
     */
    public function buildNewOrderPayload($out_order_no, $token_id, $quantity, $notify_url = null, $out_user_id = null, $order_detail = null){
        $data = [
            'outOrderNo' => $out_order_no,
            'tokenId' => (int) $token_id,
            'quantity' => $quantity,
        ];

        if ($out_user_id !== null && $out_user_id !== '') {
            $data['outUserId'] = $out_user_id;
        }
        if ($notify_url !== null && $notify_url !== '') {
            $data['notifyUrl'] = $notify_url;
        }
        if ($order_detail !== null && $order_detail !== '') {
            $data['orderDetail'] = $order_detail;
        }

        return $data;
    }

    /**
     * Create cashier order
     *
     * @param string $out_order_no Merchant unique order number
     * @param int|string $token_id Token ID
     * @param string|int|float $quantity Payable token quantity
     * @param string|null $notify_url Optional async callback URL
     * @param string|null $out_user_id Merchant user ID (required in fixed-address mode; omit in address-pool mode)
     * @param string|null $order_detail Optional order description
     * @return bool|string
     */
    public function newOrder($out_order_no, $token_id, $quantity, $notify_url = null, $out_user_id = null, $order_detail = null){
        $data = $this->buildNewOrderPayload($out_order_no, $token_id, $quantity, $notify_url, $out_user_id, $order_detail);

        return $this->client->post("{$this->baseUrl}api/v2/exchange/cashier/newOrder", $data);
    }

    /**
     * @param $address
     * @param $contract_address
     * @param $chain_id
     * @return bool|string
     */
    public function getWalletBalance($address, $contract_address, $chain_id){
        $data = [
            'address' => $address,
            'contractAddress' => $contract_address,
            'chainId' => $chain_id,
        ];

        return $this->client->post("{$this->baseUrl}/wallet/balance", $data);
    }

    /**
     * @param $address
     * @param $contract_address
     * @param $chain_id
     * @return bool|string
     */
    public function walletBalance($address, $contract_address, $chain_id){
        return $this->getWalletBalance($address, $contract_address, $chain_id);
    }

}