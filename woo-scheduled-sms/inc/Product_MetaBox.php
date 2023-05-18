<?php
namespace WOOSS;
class Product_MetaBox
{
    function __construct()
    {
        add_action('add_meta_boxes',function (){
            add_meta_box(
                '_latri_information',       // $id
                'اطلاعات بیشتر',                  // $title
                array($this,'cm_field_cb'),  // $callback
                'product',                 // $page
                'normal',                  // $context
                'high'                     // $priority
            );
        });
        add_action( 'save_post', array($this,'soalwp_save_postdata') );
    }
    function cm_field_cb($post){
        $sms = get_post_meta( $post->ID, '_sms', true );
        $sms_step = get_post_meta($post->ID, 'sms_step', true);
        $sms_variables = get_post_meta( $post->ID, '_sms_variables', true );
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function( $ ){
                jQuery( '#add-row' ).on('click', function() {
                    var row = jQuery( '.empty-row.custom-repeter-text' ).clone(true);
                    row.removeClass( 'empty-row custom-repeter-text' ).css('display','table-row');
                    row.insertBefore( '#repeatable-fieldset-one tbody>tr:last' );
                    return false;
                });

                jQuery( '.remove-row' ).on('click', function() {
                    jQuery(this).parents('tr').remove();
                    return false;
                });
            });

        </script>
        <table id="repeatable-fieldset-one" >
            <tr style="border:1px">
                <td>
                    <label><?php _e('ارسال پیام فعال','latari');?></label>
                </td>
                <td>
                    <input type="checkbox" name="sms" <?php if(isset($_POST['sms'])&&$_POST['sms']=='on') echo 'checked'; else if($sms=='on') echo 'checked';?>/>
                </td>
            </tr>

            <tr style="border:1px">
                <td>
                    <br>
                    <br>

                    <?php _e('متغییر هایی که برای پترن استفاده کردید را هرکدام را در یک خط وارد کنید.','latari')?>
                    <p>
                        {user_phone},{user_name},{name},{first_name},{last_name},{order_id},{total},{product_name},{support_name},{support_phone}
                    </p>
                    <textarea name="sms_variables" cols="50" rows="5"><?php echo $sms_variables; ?></textarea>
                </td>
            </tr>
            <?php
            if ( $sms_step ) :
                foreach ( $sms_step as $field ) {
                    ?>
                    <tr>
                        <td><input type="text"  style="width:98%;" name="pattern[]" value="<?php if($field['pattern'] != '') echo esc_attr( $field['pattern'] ); ?>" placeholder=" پترن " /></td>
                        <td><input type="text"  style="width:15%;" name="time[]" value="<?php if($field['time'] != '') echo esc_attr( $field['time'] ); ?>" />
                            <span><?php _e('ساعت بعد از لحظه پرداخت','latari');?></span>
                        </td>
                        <td><a class="button remove-row" href="#1">Remove</a></td>
                    </tr>
                    <?php
                }
            else :
                ?>
                <tr>
                    <td><input type="text"  style="width:98%;" name="pattern[]" placeholder=" پترن " /></td>
                    <td><input type="text"  style="width:15%;" name="time[]" />
                        <span><?php _e('ساعت بعد از لحظه پرداخت','latari');?></span>
                    </td>
                    <td><a class="button  cmb-remove-row-button button-disabled" href="#">Remove</a></td>
                </tr>
            <?php endif; ?>
            <tr class="empty-row custom-repeter-text" style="display: none">
                <td><input type="text"  style="width:98%;" name="pattern[]" placeholder=" پترن " /></td>
                <td><input type="text"  style="width:15%;" name="time[]" />
                    <span><?php _e('ساعت بعد از لحظه پرداخت','latari');?></span>
                </td>
                <td><a class="button remove-row" href="#">Remove</a></td>
            </tr>
        </table>
        <p><a id="add-row" class="button" href="#">Add another</a></p>
        <div>
        </div>
        <br>
        <?php
    }
    function soalwp_save_postdata( $post_id ) {

        // verify if this is an auto save routine.
        // If it is our form has not been submitted, so we dont want to do anything
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

        // Check permissions
        if ( ( isset ( $_POST['post_type'] ) ) && ( 'page' == $_POST['post_type'] )  ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }
        }
        else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }
        update_post_meta( $post_id, '_sms', $_POST['sms'] );
        if ( isset ( $_POST['sms_variables'] ) ) {
            update_post_meta( $post_id, '_sms_variables', $_POST['sms_variables'] );
        }
        $old = get_post_meta($post_id, 'sms_step', true);
        $new = array();
        $pattern = $_POST['pattern'];
        $time = $_POST['time'];
        $count = count( $pattern );
        for ( $i = 0; $i < $count; $i++ ) {
            if ( $pattern[$i] != '' ) {
                $new[$i]['pattern'] = stripslashes( strip_tags( $pattern[$i] ) );
                $new[$i]['time'] = stripslashes( $time[$i] );
            }
        }
        if ( !empty( $new ) && $new != $old ){
            update_post_meta( $post_id, 'sms_step', $new );
        } elseif ( empty($new) && $old ) {
            delete_post_meta( $post_id, 'sms_step', $old );
        }
    }
}