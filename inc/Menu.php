<?php
namespace WOOSS;

if(!class_exists('Menu')){
    class Menu
    {
        protected $wooss_options;
        public function __construct() {
            $this->wooss_options= get_option('wooss_wp');
            add_action( 'admin_menu', array($this,'wooss_plugin_menu'),10);
        }
        public function wooss_plugin_menu() {
            $hook= add_menu_page(
                __( 'پنل پیامکی', 'wooss' ),
                __( 'پنل پیامکی', 'wooss' ),
                'manage_options',
                'wooss-wp',
                array($this,'wp_wooss_settings'),
                'dashicons-awards',
                6
            );
        }
        public function wp_wooss_settings(){
            ?>
            <div class="wooss-warp wrap">
                <nav class="nav-tab-wrapper">
                    <?php
                    foreach ( $this->wooss_allowed_tab() as $tab_key => $tab_label ) {
                        echo '<a href="' . esc_url( add_query_arg( array( 'tab' => $tab_key ) ) ) . '" class="nav-tab ' .$this->wooss_get_active_class( $tab_key ) . ' '.$tab_key.'">' . $tab_label . '</a>';
                    }
                    ?>
                </nav>
                <?php $this->wooss_get_tab_content(); ?>
            </div>
            <?php
        }
        /*
         * add submenu to setting page plugin
         */
        public function wooss_allowed_tab() {
            return array(
                'sms' => __('تنظیمات پیامک', 'wooss'),
            );
        }
        /*
        * add class active to active tab
        */
        public function wooss_get_active_class( $tab ) {
            return $this->wooss_get_active_tab() == $tab ? 'nav-tab-active' : null;
        }
        /*
        * include tab content in setting page
        */
        public function wooss_get_tab_content() {
            $file = WOOSS_PATH . 'templates/admin/tpl-' . $this->wooss_get_active_tab() . '.php';
            if ( is_file( $file ) && file_exists( $file ) ) {
                $options=$this->wooss_options;
                include $file;
                if($this->wooss_get_active_tab()!='no_match'){
                    $save_function = 'wooss_wp_save_' . str_replace( '-', '_', $this->wooss_get_active_tab() ) . '_options';
                    call_user_func(array(__NAMESPACE__ .'\Menu', $save_function));
                }
            }
        }
        /*
       * find active tab in all submenu tabs
       */
        public function wooss_get_active_tab() {
            $tab = array_keys( $this->wooss_allowed_tab())[0];

            if ( isset( $_GET['tab'] ) && in_array( $_GET['tab'], array_keys($this->wooss_allowed_tab() ) ) ) {
                $tab = $_GET['tab'];
            }

            return $tab;
        }

        public function wooss_wp_save_sms_options() {
            if ( isset( $_POST['_save_sms'] ) ) {
                if ( ! isset( $_POST['_sms_nonce'] ) || ! wp_verify_nonce( $_POST['_sms_nonce'], '_save_sms_nonce' ) ) {
                    exit( __('Sorry, your nonce did not verify!','wooss') );
                } else {
                    $this->wooss_update_option( 'sms', false );
                    _e('به روز رسانی با موفقیت انجام شد','wooss');
                }
            }
        }
        
        public function wooss_update_option( $key,$sanitize = true, $html = false, $value='' ) {
            $options = get_option( 'wooss_wp' );
            if ( isset( $_POST[ $key ] )&& $_POST[ $key ]!='' ) {
                if ( $sanitize ) {
                    $options[ $key ] = sanitize_text_field( $_POST[ $key ] );
                } elseif ( $html ) {
                    $options[ $key ] = stripslashes( wp_filter_post_kses( addslashes( $_POST[ $key ] ) ) );
                } else {
                    $options[ $key ] = $_POST[ $key ];
                }
            }
            else if ($value!=''){
                $options[ $key ] = $value;
            }
            else {
                if ( is_array( $options ) && array_key_exists( $key, $options ) ) {
                    unset( $options[ $key ] );
                }
            }
            update_option( 'wooss_wp', $options );
            $this->wooss_options= get_option('wooss_wp');
        }
    }
}
