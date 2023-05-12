<?php

namespace WOOSS;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if(!class_exists("Activate")) {
    class Activate
    {
        public function __construct()
        {
            add_action( 'init',  array($this, 'soalwp_cron_scheduler'));
            $this->my_plugin_create_db();
        }

        public function active()
        {
            /*
             * check woocommerce and  plugin is active or deActive
             */
            if( !class_exists( 'WooCommerce' ) ) {
                deactivate_plugins( plugin_basename( __FILE__ ) );
                wp_die( __( 'Please install and Activate WooCommerce.', 'wooss' ), 'Plugin dependency check', array( 'back_link' => true ) );
            }
        }
        function soalwp_cron_scheduler(){
            if ( ! wp_next_scheduled( 'wss_cron_sms_end' ) ) {
                wp_schedule_event( time(), 'hourly', 'wss_cron_sms_end' );
            }
        }
        function my_plugin_create_db() {

            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $order_sms_sender = $wpdb->prefix . 'order_sms_sender';
            $sql1 = "CREATE TABLE IF NOT EXISTS $order_sms_sender (
            id mediumint(100) NOT NULL AUTO_INCREMENT,
            user_id INT NOT NULL,
            product_id INT NOT NULL,
            order_id INT NOT NULL,
            qty_sms_send INT (100)  ,
            time_register datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,      
            UNIQUE KEY id (id)
        ) $charset_collate";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql1 );

        }

    }
}