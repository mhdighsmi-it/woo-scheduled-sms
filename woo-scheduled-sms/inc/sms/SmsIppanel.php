<?php
namespace WOOSS\sms;
class SmsIppanel
{
    private $wooss_options = '';

    function __construct()
    {
        $this->wooss_options = get_option('wooss_wp');
    }
    function send($mobile, $pattern, $input_data)
    {
        $ipuser_name    =$this->wooss_options['sms']['ippanel']['user_name'];
        $ippassword     =$this->wooss_options['sms']['ippanel']['password'];
        $ipfnum         = $this->wooss_options['sms']['ippanel']['from'];
        $to             = array($mobile);
        $url = "https://ippanel.com/patterns/pattern?username=" . $ipuser_name . "&password=" . urlencode($ippassword) . "&from=$ipfnum&to=" . json_encode($to) . "&input_data=" . urlencode(json_encode($input_data)) . "&pattern_code=$pattern";
        $handler = curl_init($url);
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($handler, CURLOPT_POSTFIELDS, $input_data);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handler);
        $err = curl_error($handler);
        curl_close($handler);
        if ($err) {
            return $err;
        } else {
            return $response;
        }
    }
}