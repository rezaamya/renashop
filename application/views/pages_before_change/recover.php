<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP include "blocks/main_menu.php"; ?>

<div id="recover" class="w3-row w3-padding-64">
    <div class="w3-col s1 m3 l4">&nbsp;</div>
    <div class="w3-col s10 m6 l4">
        <form method="post" action="<?php echo base_url('recover');?>">
            <header class="w3-container" style="background-color: #FFC105!important;">
                <h3>بازیابی حساب کاربری</h3>
            </header>
            <div class="w3-container w3-padding-32" style="background-color: #f1fbf9;">
                <div class="message_holder">
                    <?php echo $sys_msg; ?>
                </div>

                <label><b><?=lang('username')?></b></label>
                <input name="username" class="w3-input w3-border" type="text" placeholder="<?=lang('enter_your_email_or_mobile_number_to_receive_the_login_code')?>">
                <button class="w3-button w3-margin-top w3-block w3-dark-grey" type="submit" name="recover" style="width: 100%"><?=lang('recover')?></button>
            </div>
        </form>
    </div>
</div>
<script>

</script>
<?PHP include "blocks/pardahkt_amn.php"; ?>
<?PHP include "blocks/contact_us.php"; ?>
<?PHP include "blocks/footer.php"; ?>





