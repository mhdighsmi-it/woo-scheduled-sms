<?php
namespace WOOSS\sms;
class SmsKavenegar
{
    private $wooss_options = '';
    function __construct()
    {
        $this->wooss_options = get_option('wooss_wp');
    }
    function send($input_data)
    {
        $api_key =$this->wooss_options['sms']['kavenegar']['api'];
        $headers = [
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.kavenegar.com/v1/' . $api_key . '/verify/lookup.json');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $input_data);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return 0;
        } else {
            return 1;
        }
    }
}