<?php
/*
 *
 * Plugin Name:       woo scheduled sms
 * Plugin URI:        n/a
 * Description:       woocommerce send sms
 * Version:           1.0.0
 * Author:            soalwp
 * Author URI:        https://soalwp.com
 * License:           GPL-2.0+
 * License URI:       n/a
 * Text Domain:       wooss
 * Domain Path:       /languages
 *
 */

if( ! defined('ABSPATH') ) {
    return;
}
if(file_exists(dirname(__FILE__).'/vendor/autoload.php')){
    require_once dirname(__FILE__).'/vendor/autoload.php';
}
/*
 * const
 *
 */
define('WOOSS_PLUGIN', __FILE__ );
define('WOOSS_PATH', wp_normalize_path( plugin_dir_path( __FILE__ ) . DIRECTORY_SEPARATOR ));
define('WOOSS_URI', plugin_dir_url( __FILE__ ));

new WOOSS\Activate();
new WOOSS\Menu();
new WOOSS\Product_MetaBox();
new WOOSS\Order;
new WOOSS\Sender();