<?php
/**
 * Created by PhpStorm.
 * User: talha
 * Date: 8/8/2017
 * Time: 4:16 PM
 */

namespace App\Classes;

use App\Classes\Twilio\TwilioAuthySDK;
use App\Classes\Twilio\Utils;

class TwilioDriver
{
    protected $authySdk;
    public function __construct($appId, $apiKey)
    {
        $this->authySdk = new TwilioAuthySDK($appId,$apiKey);
    }

    /**
     * @param $data
     * @return AppResponse|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendCode($data){
        $countryCode = Helper::defaultOnEmptyKey($data,'countrycode');
        $phone = Helper::defaultOnEmptyKey($data,'phonenumber');
        $combined = Utils::getPhoneNumberWithCountryCode($countryCode,$phone);
        $resp = new AppResponse(true);
        if($combined===null){
            $resp->addError('phonenumber','Invalid phone number');
        }else{
            $resp = $this->authySdk->sendVerificationSMS($countryCode,$phone);
            if(!$resp->getStatus()){
                $resp->addError('phonenumber','Unable to send sms to this number');
            }
        }

        return $resp;
    }
}