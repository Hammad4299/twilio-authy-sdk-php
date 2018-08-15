<?php

namespace App\Classes\Twilio;

use App\Classes\AppResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class TwilioAuthySDK {
    protected $httpClient;
    protected $endpoint;
    public $apiKey;
    public $appId;

    public function __construct($appId, $apiKey) {
        $this->appId = $appId;
        $this->apiKey = $apiKey;
        $this->httpClient = new Client([
            'verify'=>false,
            'base_uri'=>'https://api.authy.com/protected/json/'
        ]);
    }

    /**
     * @param $country
     * @param $number
     * @return AppResponse|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendVerificationSMS($country, $number) {
        $resp = $this->executeRequest('phones/verification/start','POST',[
            'via'=>'sms',
            'phone_number'=>$number,
            'country_code'=>$country
        ]);

        return $resp;
    }

    /**
     * @param $country
     * @param $number
     * @param $code
     * @return AppResponse|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function verifyNumber($country, $number, $code) {
        $resp = $this->executeRequest('phones/verification/check','GET',[
            'verification_code'=>$code,
            'phone_number'=>$number,
            'country_code'=>$country
        ]);

        return $resp;
    }

    /**
     * @param $url
     * @param string $method
     * @param array $data
     * @return AppResponse|null decoded json
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected  function executeRequest($url , $method = "GET",$data = []){
        $resp = new AppResponse(true);
        $requestParams = [];
        if($data==null)
            $data = [];

        if(strtolower($method) == "get" || strtolower($method) == "delete"){
            $requestParams['query'] = $data;
        }else{
            $requestParams['form_params'] = $data;
        }
        $requestParams['headers'] = [
            'X-Authy-API-Key'=>$this->apiKey
        ];

        $decoded = null;
        try{
            $response = $this->httpClient->request($method,$url,$requestParams);
            $decoded = json_decode($response->getBody()->getContents(),true);
            $resp->setStatus($decoded['success']);
            $resp->data = $decoded;
        }
        catch (ClientException $e){
            $decoded = json_decode($e->getResponse()->getBody()->getContents(),true);
            $resp->setStatus(false);
            $resp->message = $decoded['message'];
        }
        catch (\Exception $e){
            $resp->setStatus(false);
            $resp->message = $e->getMessage();
        }

        return $resp;
    }
}
?>