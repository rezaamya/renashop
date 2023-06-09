<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP include "blocks/main_menu.php"; ?>

<div id="change_pass" class="w3-row w3-padding-64">
    <div class="w3-col s1 m3 l4">&nbsp;</div>
    <div class="w3-col s10 m6 l4">
        <form method="post" action="<?php echo base_url('recover/change_password');?>">
            <header class="w3-container" style="background-color: #FFC105!important;">
                <h3>تغییر رمز عبور</h3>
            </header>
            <div class="w3-container w3-margin-top" style="background-color: #f1fbf9;">
                <div class="message_holder">
                    <?php echo $sys_msg; ?>
                </div>
                <div class="w3-padding-32">
                    <label><b><?=lang('password')?></b></label>
                    <input name="password" class="w3-input w3-border" type="password" placeholder="<?=lang('enter_your_new_password')?>">

                    <label><b><?=lang('password_copy')?></b></label>
                    <input name="password_copy" class="w3-input w3-border" type="password" placeholder="<?=lang('enter_your_password_copy')?>">
                    <button class="w3-button w3-margin-top w3-block w3-dark-grey" type="submit" name="confirm_password" style="width: 100%"><?=lang('confirm_password')?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>

</script>
<?PHP include "blocks/pardahkt_amn.php"; ?>
<?PHP include "blocks/contact_us.php"; ?>
<?PHP include "blocks/footer.php"; ?>