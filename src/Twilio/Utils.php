<?php

namespace App\Classes\Twilio;

class Utils
{
    public static function cleanNumber($number){
        $num = null;
        try{
            $n = str_replace('+','',$number);
            $n = str_replace('-','',$n);
            $n = str_replace(',','',$n);
            $n = str_replace(' ','',$n);
            $n = str_replace('.','',$n);
            $valid = is_numeric($n);
            if($valid){
                $num = $n;
            }
        }catch (\Exception $e){
        }

        return $num;
    }

    public static function getPhoneNumberWithCountryCode($countryCode, $number) {
        $countryCode = Utils::cleanNumber($countryCode);
        $number = Utils::cleanNumber($number);
        $combined = null;

        if($countryCode!==null && $number!==null){
            $combined = '+'.$countryCode.' '.$number;
        }

        return $combined;
    }
}