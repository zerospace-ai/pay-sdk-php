<?php
require __DIR__.'/../vendor/autoload.php';

use Cryptopay\Chain\CryptoPay;

$postData = file_get_contents('php://input');
if(!$postData){
    exit;
}
$postData = json_decode($postData, true);

$config = [
    'key' => 'l6qzcjnsxo4hmnod',
    'secret' => 'a79783c8114eb6a37fc1f04b215a427c47a4b80d5a1d6ac7b3d6db0f66c69e91',
    'public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwY3Azwj6rpa/BNNRDed/K9LTBPaqvLYd8x+zLeTO3cAS0ni87GWsywlrOfPRe+Ei/05AQ6p4OKx6M3R0rI/sUPe4R0LIyUT6M34a74kvyTyGSOXMzj0nB28IAhQ6QtPG4lgNJTTvBPW1+l0Gl42BQDotsEcrfimOTMarrsGmVpeOPtRuJOQ4pYkvkM85KVPmd0kvDyBU41krT2qBY2ynKyFo6s4xHH2qaBkoRN304U7FuLf1u/p3XO/wT7J6PQQPfCnz2gwDuLHqTgd6XJ59DqB4lj8sPKkEjpAIcraNSKkJTy4C1vki6SA3/IbUOdS7jnhgxOcK4CGMks+sHcGkOwIDAQAB',
    'private_key' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDBjcDPCPqulr8E01EN538r0tME9qq8th3zH7Mt5M7dwBLSeLzsZazLCWs589F74SL/TkBDqng4rHozdHSsj+xQ97hHQsjJRPozfhrviS/JPIZI5czOPScHbwgCFDpC08biWA0lNO8E9bX6XQaXjYFAOi2wRyt+KY5MxquuwaZWl44+1G4k5DiliS+QzzkpU+Z3SS8PIFTjWStPaoFjbKcrIWjqzjEcfapoGShE3fThTsW4t/W7+ndc7/BPsno9BA98KfPaDAO4sepOB3pcnn0OoHiWPyw8qQSOkAhyto1IqQlPLgLW+SLpIDf8htQ51LuOeGDE5wrgIYySz6wdwaQ7AgMBAAECggEALelLfnChUe+FEQZ1GAi9BC6mimteVZQXZ5ex32WCYlxQuqcUHTkC80hhLGZ23t0o+YlcuhBCIyNae0EY+ePLyOrxxcmEKiXX+YXkqsQOVzwpmDoWSid8Tgmayy90IGzupLsBJz22oqUMDP6q9SEGMy95rfI70VnXHMQVbkcWBzzSf3evyWiCa7Ym7S+MKPOX4uvu13DDK+UstyPeNElmCtlklKx9PYJ4HwOdqE0Uq1hJ0obD8nWcHOM1SeezovXlfxyn/5p8l70MWBmY2eYhdJhePwTT0zUd2lliopy3eHo2Y1JD+3mbQJwG88twhUiqiWjnNwi2JKfA13mlUh4zWQKBgQD8Gjw81odUxlyjDfXwzuHRAaBdDbd45wvot5+PlImqXmAnEdkW4p5w8ZIvPly4iNpmLnZpdrmFbOllMytT9wAkAFB6mdP/0G4q+MlaIBeZ6OXqLhQyzbZbGXTVxCJFW1KnOLvMfwqgN4TI6/8XjciKpioJtspx/XIrvfrEzup1yQKBgQDEi8uLD+24CgOGfJEVCA2ZnQU7tMIx+k2x2Bkcih2Q9NjIWNhZ4d92Cj9kh0ukTLa4G1xKxCUyFoFoG3dGHH3B60BnWrUeKVW0Q6GgBsr1lFAWHqvBSTAvQj/yM85abwbHs5y4CBYN3iPC6+Y/qVGyQJ+uPq9F22qPNYn4YXcb4wKBgHK8cfvyWydPHCwtl2hgj8+y4MxZCM6iwP3KTHlpTfpW94Xwjo/m1dtrZm/P/x0RU+y7arL3ENpTximDz01olgzMg9Q4nI7JysoN7n3xKyymHbWmARVaIIt3m4AKwto0BPsMTBR3IVvnyKw1FCUhJ0tK8sj3A0473jFgRng5/+VZAoGAWpaMI/YdfNuwsEWtoOHrUsfaZ6ByeRKCyNtvB4ZpBiyz1fONFDBVDFTAzxbEuF9bQBPsP9GTyzgwQMmX+cqb88r4Dirym4o1pDYfwmAfH31SRD+yrg2LldyVGI7kJy5RE64nJ3MipxWhqe+MWf8yVlQOxQLDShFNVeA5TS+u9D0CgYEAyGD+zML8sjA9tXSpP0CCik97s7/VM3sOM6rvuDicJCDR624B816ap1yQ7homq5llJahzAQGMMfDxRBg2ZTM7MekSdrdDLaS0Wf3vanx9vrSHaq5mHrxyaHrRP2ngMUfJNzzlcnwDxsXXYU9GZAgKDD3g7aTnE6joePrQfKI5gzM=',
    'chain_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtT0mrv6E3GbhzJdBIjFtldbTisu5qMhXt6wI0ijL92Xw6h/mhbRhTGp+pn+bLpixqr1H7JtiutF62Ip7Ca7W9TP7BD5Dgk53t21+IHsLyyqYXmocWvl7wAPg/qDqpl/utBXRNeH0qvC2lAiPsg1nwHs0bKEmW98XxvaDQwaVNUrbgHLvGCDi+LF9zTloAxCzqS2CMVzCqiN8ZFdOeW4vJXNa66wzvUxPpY1iIVlvXq6UZ5SRemoyDXueRWqv+zSn+WpbTn4BxpJtDfnlTSVocsyKSgI+CklO1Sfk7V9I1RpiVcROfZOWVhTbEQqnkKIFomb0Gm/2lSCMwAj9J38NYQIDAQAB',
];
$cryptoPay = new CryptoPay($config);

if(!$cryptoPay->verifyRsaSignature($postData)){
    //Failed to verify signature
    exit;
}

//The signature verification is successful, and the following business logic is processed
if($postData['type'] == 1){
    //Recharge transaction

}elseif($postData['type'] == 2){
    //Withdrawal transaction

}else{
    //Type Error
    exit;
}


$response = [
    'code' => 0,
    'msg' => 'ok',
    'data' => null,
];
exit(json_encode($response));