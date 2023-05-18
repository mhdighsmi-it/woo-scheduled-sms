<?php
namespace WOOSS\sms;
class SmsFaraz
{
    private $wooss_options = '';

    function __construct()
    {
        $this->wooss_options = get_option('wooss_wp');
    }
    function send($mobile, $pattern, $input_data)
    {
        $pid = ltrim($pattern);
        $user_name = $this->wooss_options['sms']['ippanel']['user_name'];
        $password = $this->wooss_options['sms']['ippanel']['password'];
        $fnum = $this->wooss_options['sms']['ippanel']['from'];
        $headers = [
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://ippanel.com/patterns/pattern?username=' . $user_name . '&password=' . $password . '&pattern_code=' . $pid . '&from=' . $fnum . '&to=' . $mobile . '&input_data=' . urlencode(json_encode($input_data)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return $err;
        } else {
            return $response;
        }
    }
}