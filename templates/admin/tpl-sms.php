<?php
/**
 * Exit if accessed directly
 */
defined( 'ABSPATH' ) || exit( 'دسترسی غیر مجاز!' );
?>
<form method="post">
        <div class="setting">
            <div class="input-control">
                <label class="label">سرویس دهنده پیامک</label>
                <select class="type-sms" name="sms[sender]">
                    <option value="kavenegar"
                        <?php
                        if (isset($_POST['sms']['sender'])&&'kavenegar'==$_POST['sms']['sender']) {
                            echo "selected";
                        }
                        else if(isset($options['sms']['sender']) && 'kavenegar'==$options['sms']['sender'])
                        {
                            echo "selected";
                        }?>
                    >کاوه نگار</option>
                    <option value="farazsms"
                    <?php
                    if (isset($_POST['sms']['sender'])&&'farazsms'==$_POST['sms']['sender']) {
                        echo "selected";
                    }
                    else if(isset($options['sms']['sender']) && 'farazsms'==$options['sms']['sender'])
                    {
                        echo "selected";
                    }?>
                    >فراز اس ام اس</option>
                    <option value="ippanel"
                        <?php
                        if (isset($_POST['sms']['sender'])&&'ippanel'==$_POST['sms']['sender']) {
                            echo "selected";
                        }
                        else if(isset($options['sms']['sender']) && 'ippanel'==$options['sms']['sender'])
                        {
                            echo "selected";
                        }?>
                    >ippanel</option>
                </select>
            </div>
            <div class="ippanel-box
             <?php
            if (isset($_POST['sms']['sender'])&&'ippanel'==$_POST['sms']['sender']) {
                echo "active";
            }
            else if(isset($options['sms']['sender']) && 'ippanel'==$options['sms']['sender'])
            {
                echo "active";
            }?>">
                <div class="input-control">
                    <label class="label">نام کاربری</label>
                    <input class="input" type="text" name="sms[ippanel][user_name]"
                           value="<?php if(isset($_POST['sms']['ippanel']['user_name'])) echo $_POST['sms']['ippanel']['user_name']; elseif(isset($options['sms']['ippanel']['user_name'])) echo $options['sms']['ippanel']['user_name'];?>" />
                </div>
                <div class="input-control">
                    <label class="label"> کلمه عبور</label>
                    <input type="text" name="sms[ippanel][password]"
                           value="<?php if(isset($_POST['sms']['ippanel']['password'])) echo $_POST['sms']['ippanel']['password']; elseif(isset($options['sms']['ippanel']['password'])) echo $options['sms']['ippanel']['password'];?>"
                    />
                </div>
              
                <div class="input-control">
                    <label class="label"> شماره ارسال کننده</label>
                    <input type="text" name="sms[ippanel][from]"
                           value="<?php if(isset($_POST['sms']['ippanel']['from'])) echo $_POST['sms']['ippanel']['from']; elseif(isset($options['sms']['ippanel']['from'])) echo $options['sms']['ippanel']['from'];?>"
                    />
                </div>
             
            </div>
            <div class="farazsms-box
             <?php
            if (isset($_POST['sms']['sender'])&&'farazsms'==$_POST['sms']['sender']) {
                echo "active";
            }
            else if(isset($options['sms']['sender']) && 'farazsms'==$options['sms']['sender'])
            {
                echo "active";
            }?>">
                <div class="input-control">
                    <label class="label">نام کاربری</label>
                    <input class="input" type="text" name="sms[farazsms][user_name]"
                           value="<?php if(isset($_POST['sms']['farazsms']['user_name'])) echo $_POST['sms']['farazsms']['user_name']; elseif(isset($options['sms']['farazsms']['user_name'])) echo $options['sms']['farazsms']['user_name'];?>" />
                </div>
                <div class="input-control">
                    <label class="label"> کلمه عبور</label>
                    <input type="text" name="sms[farazsms][password]"
                           value="<?php if(isset($_POST['sms']['farazsms']['password'])) echo $_POST['sms']['farazsms']['password']; elseif(isset($options['sms']['farazsms']['password'])) echo $options['sms']['farazsms']['password'];?>"
                    />
                </div>
              
                <div class="input-control">
                    <label class="label"> شماره ارسال کننده</label>
                    <input type="text" name="sms[farazsms][from]"
                           value="<?php if(isset($_POST['sms']['farazsms']['from'])) echo $_POST['sms']['farazsms']['from']; elseif(isset($options['sms']['farazsms']['from'])) echo $options['sms']['farazsms']['from'];?>"
                    />
                </div>
               
            </div>

            <div class="kavenegar-box <?php
            if (isset($_POST['sms']['sender'])&&'kavenegar'==$_POST['sms']['sender']) {
                echo "active";
            }
            else if(isset($options['sms']['sender']) && 'kavenegar'==$options['sms']['sender'])
            {
                echo "active";
            }?>">
                <div class="input-control">
                    <label class="label">کلید api </label>
                    <input type="text" name="sms[kavenegar][api]"
                           value="<?php if(isset($_POST['sms']['kavenegar']['api'])) echo $_POST['sms']['kavenegar']['api']; elseif(isset($options['sms']['kavenegar']['api'])) echo $options['sms']['kavenegar']['api'];?>"
                    />
                </div>
                
            </div>
        </div>
    <?php
    wp_nonce_field( '_save_sms_nonce', '_sms_nonce' );

    submit_button( 'ذخیره تغییرات', 'primary', '_save_sms', true );
    ?>
</form>
