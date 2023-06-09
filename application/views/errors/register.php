<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP include "blocks/main_menu.php"; ?>

<div id="" class="w3-row-padding">
    <div class="alert  w3-pale-red w3-round">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        لطفا فرم زیر را تکمیل نمایید.
    </div>
</div>
<div id="register_form" class="container-fluid">
    <form class="w3-container" action="/action_page.php">
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s2"><?=lang('first_name')?></label>
            <input class="w3-col s10 w3-input w3-padding w3-border w3-round" name="first_name" type="text" placeholder="<?=lang('first_name')?>"></div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s2"><?=lang('last_name')?></label>
            <input class="w3-col s10 w3-input w3-padding w3-border w3-round" name="last_name" type="text" placeholder="<?=lang('last_name')?>">
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s2"><?=lang('birthday')?></label>
            <select class="w3-select w3-col s1 w3-input w3-padding w3-border w3-round w3-margin-left" name="year">
                <option value="" disabled selected><?=lang('year')?></option>
                <option value="1">زن</option>
                <option value="2">مرد</option>
                <option value="3">سایر</option>
            </select>
            <select class="w3-select w3-col s1 w3-input w3-padding w3-border w3-round w3-margin-left" name="month">
                <option value="" disabled selected><?=lang('month')?></option>
                <option value="1">زن</option>
                <option value="2">مرد</option>
                <option value="3">سایر</option>
            </select>
            <select class="w3-select w3-col s1 w3-input w3-padding w3-border w3-round w3-margin-left" name="day">
                <option value="" disabled selected><?=lang('day')?></option>
                <option value="1">زن</option>
                <option value="2">مرد</option>
                <option value="3">سایر</option>
            </select>
            <select class="w3-select w3-col s2 w3-input w3-padding w3-border w3-round" name="sex">
                <option value="" disabled selected><?=lang('sex')?></option>
                <option value="1">زن</option>
                <option value="2">مرد</option>
                <option value="3">سایر</option>
            </select>
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s2"><?=lang('user_position')?></label>
            <select class="w3-select w3-col s10 w3-input w3-padding w3-border w3-round" name="user_position">
                <option value="" disabled selected><?=lang('please_select')?></option>
                <option value="1">عادی</option>
                <option value="2">دانشجو</option>
                <option value="3">وکیل</option>
            </select>
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s2"><?=lang('email')?></label>
            <input class="w3-col s10 w3-input w3-padding w3-border w3-round" name="email" type="email" placeholder="<?=lang('email')?>">
        </div>

        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s2"><?=lang('mobile')?></label>
            <input class="w3-col s10 w3-input w3-padding w3-border w3-round" name="mobile" type="text" placeholder="<?=lang('mobile')?>">
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s2"><?=lang('tel')?></label>
            <input class="w3-col s10 w3-input w3-padding w3-border w3-round" name="tel" type="text" placeholder="<?=lang('tel')?>">
        </div>
    </form>

</div>

<?PHP include "blocks/pardahkt_amn.php"; ?>
<?PHP include "blocks/contact_us.php"; ?>
<?PHP include "blocks/footer.php"; ?>
