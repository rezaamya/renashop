<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP //include "blocks/main_menu.php"; ?>

<?PHP
//###############################################################################################
//سی اس اس ها بصورت عمومی درج نشده اند. بنابراین اینجا مجبورم همچنان از آی دی رجیستر استفاده کنم!
//###############################################################################################
?>
<div id="login_register" class="w3-row">
    <div class="w3-col s1 m3 l4">&nbsp;</div>
    <div class="w3-col s10 m6 l4 register">
        <div class="loading w3-hide">
            <div class="loader_spin"></div>
            <?=lang('please_wait');?>
        </div>
        <form method="post" action="<?= base_url('profile/confirm_code'); ?>">
            <header class="w3-container">
                <h3><?=lang('change_username')?></h3>
            </header>

            <div class="w3-container">
                <div class="temp_messages_holder">
                    <div class="alert w3-pale-green w3-round"> کد تایید به <b><?=$this->session->userdata('username_edit_profile')?></b> ارسال شد. </div>
                </div>
                <div class="messages_holder">
                    <?=$sys_msg?>
                </div>

                <div class="timer"></div>

                <p class="w3-margin-top">
                    <label>کد تایید را وارد نمایید</label>
                    <input class="w3-input w3-border w3-round" name="code_for_edit_username" type="text">
                </p>

                <div class="w3-center">
                    <button class="w3-button w3-round w3-margin resend w3-disabled" onclick="resend_confirm_code_from_profile(this); return false;">ارسال مجدد کد</button>
                    <a class="w3-button w3-round w3-red w3-margin" href="<?= base_url('profile/edit'); ?>">بازگشت</a>
                </div>
            </div>
            <button class="w3-button w3-block w3-dark-grey" type="submit"><?=lang('confirm_code')?></button>
        </form>
    </div>
</div>
<script>
    /*********************
     ** On Windows Load **
     *********************/
    if(window.addEventListener){
        window.addEventListener('DOMContentLoaded', function () {
            startTimer(<?=$this->session->userdata('expire_time_code_for_edit_username')?>, document.querySelector("#login_register .timer"));
        })
    }else{
        window.attachEvent('onload', function () {
            startTimer(<?=$this->session->userdata('expire_time_code_for_edit_username')?>, document.querySelector("#login_register .timer"));
        })
    }

    function resend_confirm_code_from_profile (resend_btn) {
        if (resend_btn.className.indexOf("w3-disabled") < 0)
        {
            //resend btn is active
            //inactive resend Button
            resend_btn.className += ' w3-disabled';

            //show loading
            var loading = document.querySelector("#login_register .loading");
            loading.className = loading.className.replace(/w3-hide/g, "");
            loading.className = loading.className.replace(/  /g, " ");

            //Send request to server
            var send_to_server = {"req": "resend_confirm_code_for_edit_username_in_profile"};
            var url = '<?=base_url('api/index')?>';
            postAjax(
                url,
                send_to_server,
                function (result) {
                    //Success state
                    var messages_holder = document.querySelector("#login_register .messages_holder");
                    console.log("success:", result);
                    result = JSON.parse(result);

                    if (result.status == 'sent')
                    {
                        //Message has been send
                        var msg = '<div class="alert  w3-pale-yellow w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>کد مجددا ارسال شد.</div>';
                        messages_holder.innerHTML = msg;

                        startTimer(parseInt(result.expire_in), document.querySelector("#login_register .timer"));
                        resend_btn.className += ' w3-disabled ';
                    }
                    else if (result.status == 'please_wait')
                    {
                        //کاربر میبایست صبر کند
                        var msg = '<div class="alert  w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>تا دریافت کد بعدی میبایست حداقل '+result.how_much+' صبر کنید</div>';
                        messages_holder.innerHTML = msg;

                        //disable resend btn
                        //var resend_btn = document.querySelector("#login_register .resend");
                        //resend_btn.className += 'w3-disabled';
                    }
                    else
                    {
                        //خطایی در ارسال کد رخ داده است
                        var msg = '<div class="alert  w3-pale-yellow w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>هنگام ارسال مجدد کد، خطایی رخ داده است. لطفا مجدد تلاش نمایید و در صورتیکه چندمین مرتبه است که با این خطا مواجه میشوید، لطفا آنرا به مدیر سایت اطلاع دهید.</div>';
                        messages_holder.innerHTML = msg;
                    }

                    loading.className += " w3-hide";
                },
                undefined,
                function (result) {
                    //Failure state
                    var messages_holder = document.querySelector("#login_register .messages_holder");
                    console.log("falied:", result);
                    var msg = '<div class="alert  w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>در ارتباط با سرور، خطایی رخ داده است. لطفا مجدد تلاش نمایید و در صورتیکه این خطا به مدت طولانی تکرار شد، لطفا آنرا به مدیر سایت اطلاع دهید.</div>';
                    messages_holder.innerHTML = msg;
                    loading.className += " w3-hide";
                }
            );
        }
    }
</script>

<?PHP //include "blocks/pardahkt_amn.php"; ?>
<?PHP //include "blocks/contact_us.php"; ?>
<?PHP //include "blocks/footer.php"; ?>





