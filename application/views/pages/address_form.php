<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP //include "blocks/main_menu.php"; ?>

<div class="w3-row-padding">
    <?php echo $sys_msg ?>
</div>
<div id="address_form" class="container-fluid address_form">
	<?PHP
	$attributes = array('class' => 'main_form', 'id' => 'main_form');
	if ($is_new_address)
	{
		echo form_open_multipart(base_url("profile/new_address"), $attributes);
	}
	else
	{
		echo form_open_multipart(base_url("profile/edit_address/".$id_address), $attributes);
	}

	?>
        <div class="w3-center w3-margin-bottom w3-margin-top"><b>مرسولات شما به این آدرس ارسال میگردد، لطفا فرم زیر را با دقت تکمیل نمایید.</b></div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s3"><?=lang('address_title')?></label>
            <input class="w3-col s9 w3-input w3-padding w3-border w3-roundddd" name="address_title" type="text" placeholder="<?=lang('address_title')?>" value="<?php echo set_value('address_title', $address_title); ?>">
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s3"><?=lang('first_name')?></label>
            <input class="w3-col s9 w3-input w3-padding w3-border w3-roundddd" name="address_first_name" type="text" placeholder="<?=lang('first_name')?>" value="<?php echo set_value('address_first_name', $first_name); ?>">
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s3"><?=lang('last_name')?></label>
            <input class="w3-col s9 w3-input w3-padding w3-border w3-roundddd" name="address_last_name" type="text" placeholder="<?=lang('last_name')?>" value="<?php echo set_value('address_last_name', $last_name); ?>">
        </div>


        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s3"><?=lang('mobile')?></label>
            <input class="w3-col s9 w3-input w3-padding w3-border w3-roundddd" name="address_mobile" type="text" placeholder="<?=lang('mobile')?>" value="<?php echo set_value('address_mobile', $mobile); ?>">
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s3"><?=lang('tel')?></label>
            <input class="w3-col s9 w3-input w3-padding w3-border w3-roundddd" name="address_tel" type="text" placeholder="<?=lang('tel')?>" value="<?php echo set_value('address_tel', $tel); ?>">
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s3"><?=lang('postcode')?></label>
            <input class="w3-col s9 w3-input w3-padding w3-border w3-roundddd" name="address_postcode" type="text" placeholder="<?=lang('postcode')?>" value="<?php echo set_value('address_postcode', $postcode); ?>">
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s3"><?=lang('address')?></label>
            <div class="w3-col s9" style="padding: 0px!important;">
                <div class="w3-third">
                    <select class="w3-select w3-col w3-input w3-padding w3-border w3-roundddd" name="address_country">
                        <option value="" disabled selected><?=lang('country')?></option>
                        <?PHP echo $country_name; ?>
                    </select>
                </div>
                <div class="w3-third">
                    <select onchange="refresh_cities();" class="w3-select w3-col w3-input w3-padding w3-border w3-roundddd" name="address_state">
                        <option value="" disabled selected><?=lang('state')?></option>
                        <?PHP echo $state_name; ?>
                    </select>
                </div>
                <div class="w3-third">
                    <select class="w3-select w3-col w3-input w3-padding w3-border w3-roundddd" name="address_city">
                        <option value="" disabled selected><?=lang('city')?></option>
                        <?PHP echo $city_name; ?>
                    </select>
                </div>
				<script>
					function refresh_cities ()
					{
						var address_state = document.querySelector("select[name='address_state']");
						var selected_state = address_state.options[address_state.selectedIndex];

						if (address_state.value != '')
						{
							var address_city = document.querySelector("select[name='address_city']");
							var selected_city = address_city.options[address_city.selectedIndex];

							var temp_cities = selected_state.getAttribute('cities');

							temp_cities = temp_cities.split("::separator::");

							var options = '<option value="" disabled selected>شهر</option>';
							for (var i = 0; i < temp_cities.length; i++)
							{
								var selected = "";
								if (selected_city.value == temp_cities[i])
								{
									//این آپشن قبلا انتخاب شده است
									selected = " selected ";
								}
								options += '<option value="'+temp_cities[i]+'" '+selected+'>'+temp_cities[i]+'</option>';
							}

							address_city.innerHTML = options;
						}
					}

					refresh_cities ();
				</script>
            </div>
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s3"><?=lang('complete_address')?></label>
            <textarea class="w3-col s9 w3-input w3-padding w3-border w3-roundddd" rows="3" name="address"><?php echo set_value('address',$complete_address); ?></textarea>
        </div>
        <div class="w3-row-padding">
            <label class="w3-col w3-margin-top s3">&nbsp;</label>
            <button type="submit" onclick="document.getElementById('task').value = 'save';" class="w3-button w3-round w3-green w3-margin-bottom w3-hover-gray"><?=lang('save')?></button>
            <button type="submit" onclick="document.getElementById('task').value = 'save_and_close';" class="w3-button w3-blue-gray w3-round w3-margin-bottom w3-hover-gray"><?=lang('save_and_close')?></button>
            <a href="<?= base_url("profile")?>" class="w3-button w3-round w3-red w3-margin-bottom w3-hover-gray"><?=lang('cancel')?></a>
        </div>
        <input type="hidden" id="task" name="task">
    </form>
	
</div>

<?PHP //include "blocks/pardahkt_amn.php"; ?>
<?PHP //include "blocks/contact_us.php"; ?>
<?PHP //include "blocks/footer.php"; ?>
