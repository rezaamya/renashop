<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP //include "blocks/main_menu.php"; ?>

<div id="confirm_recover">
<form method="post" class="confirm_recover form_holder" action="<?= base_url('recover/confirm_recover')?>">
    <div class="w3-container">
        <div class="w3-row w3-padding-64">
            <div class="w3-col m4">&nbsp;</div>
            <div class="w3-col m4">
                <header class="w3-container" style="background-color: #FFC105!important;">
                    <h3>تایید کد بازیابی</h3>
                </header>
                <div class="w3-padding" style="background-color: #f1fbf9;">
                    <div class="message_holder">
                        <?php echo $sys_msg; ?>
                    </div>

                    <div class="timer w3-center"></div>

                    <p class="w3-margin-top">
                        <label><?=lang('enter_the_confirm_code')?></label>
                        <input class="w3-input w3-border" name="recovery_code" type="text" autocomplete="off" oninput="validate_form(this);">
                    </p>

                    <div class="w3-center">
                        <a href="<?= base_url('recover');?>" class="w3-button w3-dark-grey w3-block resend w3-disabled" disabled="disabled" onclick="if(this.disabled) {return false;}" style="width: 100%;margin-bottom: 5px;"><?=lang('resend_code')?></a>
                    </div>
                    <button type="submit" class="w3-button w3-block w3-disabled w3-green submit_btn" disabled="disabled"><?=lang('confirm_code')?></button>
                </div>
            </div>
        </div>
    </div>
</form>
</div>

<script src="<?PHP echo base_url('assets/js/jquery-3.3.1.min.js');?>"></script>
<script src="<?=base_url('assets/js/sprintf.js');?>"></script>
<script>
    function validate_form (code_input) {
        var father = $(code_input).closest(".form_holder");
        var submit_btn = father.find("button[type='submit']");

        if(code_input.value.length > 5 && submit_btn.attr("timer") == "has_time")
        {
            submit_btn.prop("disabled",false);
            submit_btn.removeClass('w3-disabled');
        }
        else
        {
            submit_btn.prop("disabled",true);
            submit_btn.addClass('w3-disabled');
        }
    }

    var display_node = $('.confirm_recover .timer');
    var resend_btn = $('.confirm_recover .resend');
    var submit_btn = $('.confirm_recover .submit_btn');

    var temp_lang = "<?= lang('the_confirm_code_was_sent_to_email_phone_number'); ?>";
    var prefix_str = sprintf(temp_lang, "<?=$this->session->userdata('recovery_username')?>");

    startTimer_confirm_code(<?=$this->session->userdata('temp_expire_time_for_recovery_code')?>, display_node, resend_btn, submit_btn, prefix_str+"<?=lang('countdown_remained_time')?>", "<?=lang('countdown_timeout')?>");

    function startTimer_confirm_code(duration, display, resend_btn, submit_btn, countdown_remained_time, countdown_timeout) {
        //اگر قبلا تایمری تنظیم شده است، آنرا پاک میکنیم تا شمارش اشتباه نشود!
        if (typeof timer_countdown != "undefined")
        {
            clearInterval(timer_countdown);
        }

        var timer = duration, minutes, seconds;
        timer_countdown = setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.html("<div class='has_time w3-padding w3-pale-green'>"+ sprintf(countdown_remained_time,  minutes + ":" + seconds) +"</div>");

            if (--timer < 0) {
                //timer = duration;
                clearInterval(timer_countdown);

                display.html("<div class='time_finished w3-padding w3-pale-red'>"+ countdown_timeout +"</div>");

                //Enable resend Button
                resend_btn.prop("disabled",false);
                resend_btn.removeClass('w3-disabled');

                //Disable submit Button
                submit_btn.prop("disabled",true);
                submit_btn.addClass('w3-disabled');
                submit_btn.attr('timer', 'timeout');
            }
            else
            {
                //Disable resend Button
                resend_btn.prop("disabled",true);
                resend_btn.addClass('w3-disabled');

                //Enable submit Button
                submit_btn.attr('timer', 'has_time');
            }
        }, 1000);
    }

</script>

<?PHP //include "blocks/pardahkt_amn.php"; ?>
<?PHP //include "blocks/contact_us.php"; ?>
<?PHP //include "blocks/footer.php"; ?>





