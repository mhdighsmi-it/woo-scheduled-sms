<?php
/**
 * Created by PhpStorm.
 * User: PALAPAL DELL
 * Date: 12/14/2020
 * Time: 12:15 AM
 */

namespace WOOSS;

use WOOSS\sms\SmsFaraz;
use WOOSS\sms\SmsKavenegar;
use WOOSS\sms\SmsIppanel;
class Sender
{
    function __construct()
    {
        $date = new \DateTime("now", new \DateTimeZone("Asia/Tehran"));
        if(strtotime('08:00:00') < strtotime($date->format('G:i:s')) &&strtotime($date->format('G:i:s')) < strtotime('23:00:00')){
            add_action('wss_cron_sms_end',array($this,'soalwp_sender_sms_check'));
        }
        add_action('soalwp_send_sms_ippanel',array($this,'router_check_panel_sms'),10,4);
        add_action('sms_status_pattern_variable',array($this,'soalwp_sms_status_pattern_variable'),10,4);
    }
    function soalwp_sender_sms_check(){
        $date = new \DateTime("now", new \DateTimeZone("Asia/Tehran"));
        $send_date = $date->format('Y-m-d G:i:s');
        $late_accounts = $this->get_list_phone();
        foreach ($late_accounts as $record) {
            if($record['order_id']) {
                if($record['product_id']){
                    $product_id = $record['product_id'];
                }
                else{
                    return;
                }
                $product=wc_get_product($product_id);
                $sms = get_post_meta($product_id, '_sms', true );
                $sms_step = get_post_meta($product_id, 'sms_step', true);
                if(!empty($sms_step)) {
                    if ($sms=='on') {
                        $next=$record['qty_sms_send']+1;
                        if($sms_step[$next-1]) {
                            $day_after_popup = $sms_step[$next - 1]['time'];
                            $date2 = strtotime($send_date);
                            $date1 = strtotime($record['time_register']);
                            if (round($date2 - $date1) / 3600 > 1 * $day_after_popup) {
                                $order = wc_get_order($record['order_id']);
                                $order_id = $record['order_id'];
                                $user = get_userdata($order->get_customer_id());
                                if (get_post_meta($order_id, '_billing_phone', true)) {
                                    $user_phone = get_post_meta($order_id, '_billing_phone', true);
                                } else {
                                    if (is_numeric($user->user_login)) {
                                        $user_phone = $user->user_login;
                                    }
                                }
                                $userName = get_post_meta($order_id, '_billing_first_name', true);
                                if (!$userName && ($user->first_name || $user->last_name)) {
                                    $userName = $user->first_name . ' ' . $user->last_name;
                                } elseif ($user->display_name) {
                                    $userName = $user->display_name;
                                }
                                $sms_variables = get_post_meta($product_id, '_sms_variables', true);
                                $variables = explode("\n",
                                    str_replace(["\r\n", "\n\r", "\r"], "\n", $sms_variables)
                                );
                                if ($variables) {
                                    $data = [];
                                    foreach ($variables as $variable) {
                                        if ($variable == 'total') {
                                            $data['total'] = $order->get_total();
                                        }
                                        if ($variable == 'order_id') {
                                            $data['order_id'] = $order->get_id();
                                        }
                                        if ($variable == 'last_name') {
                                            $data['last_name'] = $user->last_name;
                                        }
                                        if ($variable == 'first_name') {
                                            $data['first_name'] = $user->first_name;
                                        }
                                        if ($variable == 'user_name') {
                                            $data['user_name'] = $user->user_login;
                                        }
                                        if ($variable == 'user_phone') {
                                            $data['user_phone'] = $user_phone;
                                        }
                                        if ($variable == 'product_name') {
                                            $data['product_name'] = $product->get_title();
                                        }
                                        if ($variable == 'support_phone') {
                                            $data['support_phone'] = get_post_meta($order_id, '_support_phone', true);
                                        }
                                        if ($variable == 'support_name') {
                                            $data['support_name'] = get_post_meta($order_id, '_support_name', true);
                                        }
                                    }
                                }
                                $this->update_qty_order_sms_sender($record['id'], $next);
                                (new Sender())->router_check_panel_sms($user_phone, $sms_step[$next - 1]['pattern'], $data);
                            }
                        }
                    }
                }
            }
        }
    }
    function get_list_phone(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'order_sms_sender';
        $late_date = date( 'Y-m-d G:i:s', strtotime( '-30 days' ) ); // 30 days ago
        $sql = 'SELECT * FROM ' . $table_name . ' WHERE time_register >="' . $late_date . '" ';
        $result = $wpdb->get_results( $sql, ARRAY_A );
        return $result;
    }
    function update_qty_order_sms_sender($id,$qty){
        global $wpdb;
        $table_name = $wpdb->prefix . 'order_sms_sender';
        $wpdb->update(
            $table_name,
            array(
                'qty_sms_send' => $qty
            ),
            array('id' => $id),
            array('%s', '%s'), array('%s'));
    }

    function router_check_panel_sms($phone,$pattern,$input_data,$panel_sms=''){
        
        if($panel_sms==''){
            $panel_sms=get_option('wooss_wp')['sms']['sender'];
        }
        if($panel_sms=='farazsms'){
            return (new SmsFaraz())->send($phone,$pattern,$input_data);
        }
        if($panel_sms=='kavenegar'){
            return (new SmsKavenegar())->send($input_data);
        }
        if($panel_sms=='ippanel'){
            return (new SmsIppanel())->send($phone,$pattern,$input_data);
        }
    }
    function soalwp_sms_status_pattern_variable($user_id,$new_status,$old_status,$confirmation){
        $sms_variables = get_option('wooss_wp')['status_sms'];
        $user=get_userdata($user_id);
        if(get_user_meta($user_id, 'billing_phone', true)){
            $user_phone = get_user_meta($user_id, 'billing_phone', true);
        }
        else{
            if(is_numeric($user->user_login)){
                $user_phone=$user->user_login;
            }
        }
        $status_sms_active=$sms_variables[$new_status]['active'];
        if($status_sms_active=='on') {
            $status_sms_pattern = trim($sms_variables[$new_status]['pattern']);
            $status_sms_variables = $sms_variables[$new_status]['variables'];
            $variables = explode("\n",
                str_replace(["\r\n", "\n\r", "\r"], "\n", $status_sms_variables)
            );
            if ($variables) {
                $data = [];
                foreach ($variables as $variable) {
                    if (trim($variable) == 'last_name') {
                        $data['last_name'] = $user->last_name;
                    }
                    if (trim($variable) == 'first_name') {
                        $data['first_name'] = $user->first_name;
                    }
                    if (trim($variable) == 'user_name') {
                        $data['user_name'] = $user->user_login;
                    }
                    if (trim($variable) == 'user_phone') {
                        $data['user_phone'] = $user_phone;
                    }
                }
            }
            do_action('soalwp_send_sms_ippanel', $user_phone, $status_sms_pattern, $data, '');
        }
    }
}