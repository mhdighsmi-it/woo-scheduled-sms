<?php
namespace WOOSS;
class Order
{
    function __construct()
    {
        add_action('woocommerce_order_status_processing', array($this,'soalwp_woocommerce_order_status_completed'), 9999, 1);
    }
    function soalwp_woocommerce_order_status_completed($order_id)
    {
        $order = wc_get_order($order_id);
        $user = get_userdata($order->get_customer_id());
        $items = $order->get_items();
        if(get_post_meta($order_id, '_billing_phone', true)){
            $user_phone = get_post_meta($order_id, '_billing_phone', true);
        }
        else{
            if(is_numeric($user->user_login)){
                $user_phone=$user->user_login;
            }
        }
        $userName = get_post_meta($order_id, '_billing_first_name', true);
        if (!$userName && ($user->first_name || $user->last_name)) {
            $userName = $user->first_name . ' ' . $user->last_name;
        }
        elseif ($user->display_name){
            $userName=$user->display_name;
        }
        foreach ($items as $item) {
            if($item->get_product_id()>1){
                $product_id = $item->get_product_id();
            }
            $sms = get_post_meta($product_id, '_sms', true );
            $sms_step = get_post_meta($product_id, 'sms_step', true);
            if ($sms=='on') {
                global $wpdb;
                $date_noww = new \DateTime('now', new \DateTimeZone('Asia/Tehran'));
                $order_sms_sender = $wpdb->prefix . "order_sms_sender";
                $product_user = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "order_sms_sender WHERE user_id = '" . $user->ID . "' AND product_id = '" . $product_id . "' ", ARRAY_A);
                if (empty($product_user)) {
                    $wpdb->insert(
                        $order_sms_sender,
                        array(
                            'user_id' => $user->ID,
                            'product_id' => $product_id,
                            'order_id' => $order_id,
                            'qty_sms_send' => 1,
                            'time_register' =>$date_noww->format('Y-m-d H:i:s'),
                        ),
                        array(
                            '%d',
                            '%d',
                            '%d',
                            '%d',
                            '%s'
                        )
                    );
                }
                if(!empty($sms_step)) {
                    $sms_variables = get_post_meta( $product_id, '_sms_variables', true );
                    $variables = explode("\n",
                        str_replace(["\r\n","\n\r","\r"],"\n",$sms_variables)
                    );
                    if($variables){
                        $data=[];
                        foreach ($variables as $variable){
                            if($variable=='total'){
                                $data['total']=$order->get_total();
                            }
                            if($variable=='cost'){
                                $data['cost']=$order->get_total();
                            }
                            if($variable=='code'){
                                $data['code']=$order->get_id();
                            }
                            if($variable=='last_name'){
                                $data['last_name']=$user->last_name;
                            }
                            if($variable=='first_name'){
                                $data['first_name']=$user->first_name;
                            }
                            if($variable=='name'){
                                $data['name']=$user->first_name .' '.$user->last_name;
                            }
                            if($variable=='user_name'){
                                $data['user_name']=$user->user_login;
                            }
                            if($variable=='user_phone'){
                                $data['user_phone']=$user_phone;
                            }
                            if($variable=='product_name'){
                                $data['product_name']=$item->get_name();
                            }
                            if($variable=='support_phone'){
                                $data['support_phone']=get_post_meta($order_id,'_support_phone',true);
                            }
                            if($variable=='support_name'){
                                $data['support_name']=get_post_meta($order_id,'_support_name',true);
                            }
                        }
                    }
                    $result=(new Sender())->router_check_panel_sms( $user_phone,$sms_step[0]['pattern'],$data);
                    if($result){
                        $order->add_order_note('پیامک با موفقیت برای مشتری ارسال شد.');
                    }
                }

            }
        }

    }
}