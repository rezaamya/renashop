<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP //include "blocks/main_menu.php"; ?>

<div id="register_form" class="container-fluid edit_profile_form">
	<div id="" class="w3-row-padding">
		<div class="module_html">
			<div class="title_holder margin-top margin-bottom">
				<hr>
				<div class="title">ویرایش اطلاعات حساب کاربری</div>
			</div>

			<div>
				<?php echo $sys_msg ?>
			</div>
		</div>
	</div>

	<?PHP
	$attributes = array('class' => 'main_form', 'id' => 'main_form');
	echo form_open_multipart(base_url("profile/edit"), $attributes);
	?>

        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top m2"><?=lang('first_name')?></label>
            <input class="w3-col m10 w3-input w3-padding w3-border w3-roundddd" name="first_name" type="text" placeholder="<?=lang('first_name')?>" value="<?php echo set_value('first_name', $first_name); ?>"></div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top m2"><?=lang('last_name')?></label>
            <input class="w3-col m10 w3-input w3-padding w3-border w3-roundddd" name="last_name" type="text" placeholder="<?=lang('last_name')?>" value="<?php echo set_value('last_name', $last_name); ?>">
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top m2"><?=lang('username')?></label>
            <input class="w3-col m10 w3-input w3-padding w3-border w3-roundddd" name="username" type="text" placeholder="<?=lang('username')?>" value="<?php echo set_value('username', $username); ?>">
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top m2"><?=lang('birthday')?></label>
            <div class="w3-col m10 birthday_input">
                <select class="w3-select w3-col s4444 m3 w3-input w3-padding w3-border w3-roundddd margin-left" name="year">
                    <option value="" disabled selected><?=lang('year')?></option>
                    <?PHP echo $year; ?>
                </select>
                <select class="w3-select w3-col s4444 m4 w3-input w3-padding w3-border w3-roundddd margin-left" name="month">
                    <option value="" disabled selected><?=lang('month')?></option>
                    <?PHP echo $month; ?>
                </select>
                <select class="w3-select w3-col s3333 m3 w3-input w3-padding w3-border w3-roundddd margin-left" name="day">
                    <option value="" disabled selected><?=lang('day')?></option>
                    <?PHP echo $day; ?>
                </select>
                <select class="w3-select w3-col m3333 w3-input w3-padding w3-border w3-roundddd" name="sex">
                    <option value="" disabled selected><?=lang('sex')?></option>

                    <option value="female" <?= set_value('sex', $sex) == 'female' ? "selected" : ""; ?>><?=lang('female')?></option>
                    <option value="man" <?= set_value('sex', $sex) == 'man' ? "selected" : ""; ?>><?=lang('man')?></option>
                    <option value="others" <?= set_value('sex', $sex) == 'others' ? "selected" : ""; ?>><?=lang('others')?></option>

                </select>
            </div>
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top m2"><?=lang('customer_group')?></label>
            <select class="w3-select w3-col m10 w3-input w3-padding w3-border w3-roundddd" name="customer_group">
                <option value="" disabled selected><?=lang('please_select')?></option>
				<?PHP echo $customer_group; ?>
            </select>
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top m2"><?=lang('email')?></label>
            <input class="w3-col m10 w3-input w3-padding w3-border w3-roundddd" name="email" type="email" placeholder="<?=lang('email')?>" value="<?php echo set_value('email', $email); ?>">
        </div>

        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top m2"><?=lang('mobile')?></label>
            <input class="w3-col m10 w3-input w3-padding w3-border w3-roundddd" name="mobile" type="text" placeholder="<?=lang('mobile')?>" value="<?php echo set_value('mobile', $mobile); ?>">
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top m2"><?=lang('password')?></label>
            <input class="w3-col m10 w3-input w3-padding w3-border w3-roundddd" name="password" type="password" placeholder="<?=lang('password')?>">
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top m2"><?=lang('confirm_password')?></label>
            <input class="w3-col m10 w3-input w3-padding w3-border w3-roundddd" name="confirm_password" type="password" placeholder="<?=lang('password')?>">
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top m2 w3-hide-small">&nbsp;</label>
            <p>در صورتی که مایل به تغییر <b>رمز عبور</b> نمیباشید فیلد مربوطه را خالی بگذارید.</p>
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top m2">&nbsp;</label>
            <button type="submit" onclick="set_task('save', 'main_form', '')" class="w3-button w3-round w3-green w3-margin-bottom w3-hover-gray"><?=lang('save')?></button>
			<a href="<?= base_url("profile")?>" class="w3-button w3-round w3-red w3-margin-bottom w3-hover-gray"><?=lang('cancel')?></a>
        </div>
		<input type="hidden" id="task" name="task">
    </form>

</div>

<?PHP //include "blocks/pardahkt_amn.php"; ?>
<?PHP //include "blocks/contact_us.php"; ?>
<?PHP //include "blocks/footer.php"; ?>
