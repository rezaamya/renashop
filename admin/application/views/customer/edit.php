<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
			<button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("customer")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>
<div class="container-fluid">
	<?PHP echo $html_output['sys_msg']; ?>
</div>
<div class="container-fluid">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="personal_profile" data-toggle="tab" href="#personal" role="tab"><?=lang('personal_profile')?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="contact_details" data-toggle="tab" href="#contact" role="tab"><?=lang('addresses')?></a>
        </li>
        <!--
        <li class="nav-item">
            <a class="nav-link" id="history_customer" data-toggle="tab" href="#history" role="tab"><?=lang('history_customer')?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="transactions" data-toggle="tab" href="#transaction" role="tab"><?=lang('transactions')?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="reward_points" data-toggle="tab" href="#reward" role="tab"><?=lang('reward_points')?></a>
        </li>
        -->
    </ul>
    <?PHP
    $attributes = array('class' => 'main_form', 'id' => 'main_form');
    echo form_open_multipart(base_url("customer/edit/".$html_output['item_data']['id']), $attributes);
    ?>
    <div class="tab-content" id="customer_details">
        <div class="tab-pane fade show active" id="personal" role="tabpanel">
            <div class="row">
                <div class="col">
                    <div class="form-group row">
                        <label for="first_name" class="col-sm-2 col-form-label"><?=lang('first_name')?></label>
                        <div class="col-sm-10">
                            <input name="first_name" type="text" class="form-control" id="first_name" placeholder="<?=lang('first_name')?>" value="<?php echo set_value('first_name',$html_output['item_data']['first_name']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="last_name" class="col-sm-2 col-form-label"><?=lang('last_name')?></label>
                        <div class="col-sm-10">
                            <input name="last_name" type="text" class="form-control" id="last_name" placeholder="<?=lang('last_name')?>" value="<?php echo set_value('last_name',$html_output['item_data']['last_name']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="user_name" class="col-sm-2 col-form-label"><?=lang('username')?></label>
                        <div class="col-sm-10">
                            <input name="username" type="text" class="form-control" id="username" placeholder="<?=lang('username')?>" value="<?php echo set_value('username',$html_output['item_data']['username']); ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label"><?=lang('birthday')?></label>
                        <div class="col-sm-2">
                            <select id="year" class="form-control" name="year">
                                <option value="" disabled selected><?=lang('year')?></option>
								<?PHP echo $html_output['year']; ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <select class="form-control" id="month" name="month">
                                <option value="" disabled selected><?=lang('month')?></option>
								<?PHP echo $html_output['month']; ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <select class="form-control" id="day" name="day">
                                <option value="" disabled selected><?=lang('day')?></option>
								<?PHP echo $html_output['day']; ?>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <select class="form-control" id="sex" name="sex">
                                <option value="" disabled selected><?=lang('sex')?></option>
                                <option value="female" <?= set_value('sex', $html_output['item_data']['sex']) == 'female' ? "selected" : ""; ?>><?=lang('female')?></option>
                                <option value="man" <?= set_value('sex', $html_output['item_data']['sex']) == 'man' ? "selected" : ""; ?>><?=lang('man')?></option>
                                <option value="others" <?= set_value('sex', $html_output['item_data']['sex']) == 'others' ? "selected" : ""; ?>><?=lang('others')?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-sm-2 col-form-label"><?=lang('email')?></label>
                        <div class="col-sm-10">
                            <input name="email" type="email" class="form-control" id="email" placeholder="<?=lang('email')?>" value="<?php echo set_value('email',$html_output['item_data']['email']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="mobile" class="col-sm-2 col-form-label"><?=lang('mobile')?></label>
                        <div class="col-sm-10">
                            <input name="mobile" type="text" class="form-control" id="mobile" placeholder="<?=lang('mobile')?>" value="<?php echo set_value('mobile',$html_output['item_data']['mobile']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="customer_group" class="col-sm-2 col-form-label"><?=lang('user_position')?></label>
                        <div class="col-sm-10">
                            <select name="customer_group" id="customer_group" class="form-control">
								<?PHP echo $html_output['categories_list']; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-sm-2 col-form-label"><?=lang('password')?></label>
                        <div class="col-sm-10">
                            <input name="password" type="password" class="form-control" id="password" placeholder="<?=lang('password')?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="confirm_password" class="col-sm-2 col-form-label"><?=lang('confirm_password')?></label>
                        <div class="col-sm-10">
                            <input name="confirm_password" type="password" class="form-control" id="confirm_password" placeholder="<?=lang('confirm_password')?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <legend class="col-form-label col-sm-2 pt-0"><?=lang('condition')?></legend>
                        <div class="col-sm-10">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="condition" id="gridRadios1" value="active" checked <?= set_value('condition', $html_output['item_data']['condition']) == 'active' ? "checked" : ""; ?>>
                                <label class="form-check-label" for="gridRadios1">
                                    <?=lang('active')?>
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="condition" id="gridRadios2" value="inactive" <?= set_value('condition', $html_output['item_data']['condition']) == 'inactive' ? "checked" : ""; ?>>
                                <label class="form-check-label" for="gridRadios2">
                                    <?=lang('inactive')?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="contact" role="tabpanel">
            <script>
                function add_new_address ()
                {
                    $("#addresses_container").prepend('<?=$html_output['empty_address_html_form']?>');
                    refresh_cities ();
                }

                function remove_address (clicked_obj)
                {
                    $(clicked_obj).closest('.address_holder').remove();
                }

                function refresh_title (changed_input_obj)
                {
                    var obj = $(changed_input_obj);
                    obj.closest('.address_holder').find(".address_title").html(obj.val());
                }
            </script>
            <button type="button" onclick="add_new_address();" class="btn btn-success btn-sm mb-1"><?=lang('new')?></button>

            <div id="addresses_container">
				<?PHP echo $html_output['addresses']; ?>
            </div>
        </div>

        <!--
        <div id="addresses_container">
                <div class="row address_holder">
                    <div class="col">
                        <div class="h5 address_title">onvan</div>
                        <div class="form-group row">
                            <label for="address_title" class="col-sm-2 col-form-label"><?=lang('address_title')?></label>
                            <div class="col-sm-10">
                                <input name="address_title" type="text" class="form-control" id="address_title" placeholder="<?=lang('address_title')?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="first_name_2" class="col-sm-2 col-form-label"><?=lang('first_name')?></label>
                            <div class="col-sm-10">
                                <input name="first_name_2" type="text" class="form-control" id="first_name_2" placeholder="<?=lang('first_name')?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="last_name_2" class="col-sm-2 col-form-label"><?=lang('last_name')?></label>
                            <div class="col-sm-10">
                                <input name="last_name_2" type="text" class="form-control" id="last_name_2" placeholder="<?=lang('last_name')?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="country" class="col-sm-2 col-form-label"><?=lang('country')?></label>
                            <div class="col-sm-10">
                                <select name="country" id="country" class="form-control">
                                    <option>ایران</option>
                                    <option>فرانسه</option>
                                    <option>آلمان</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="state" class="col-sm-2 col-form-label"><?=lang('state')?></label>
                            <div class="col-sm-10">
                                <select name="state" id="state" class="form-control">
                                    <option>خوزستان</option>
                                    <option>تهران</option>
                                    <option>شیراز</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="city" class="col-sm-2 col-form-label"><?=lang('city')?></label>
                            <div class="col-sm-10">
                                <select name="city" id="city" class="form-control">
                                    <option>اهواز</option>
                                    <option>تهران</option>
                                    <option>شیراز</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2" for="address"><?=lang('address')?></label>
                            <div class="col-sm-10">
                                <textarea name="address" class="form-control" id="address" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="postcode" class="col-sm-2 col-form-label"><?=lang('postcode')?></label>
                            <div class="col-sm-10">
                                <input name="postcode" type="text" class="form-control" id="postcode" placeholder="<?=lang('postcode')?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="mobile" class="col-sm-2 col-form-label"><?=lang('mobile')?></label>
                            <div class="col-sm-10">
                                <input name="mobile" type="text" class="form-control" id="mobile" placeholder="<?=lang('mobile')?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tel" class="col-sm-2 col-form-label"><?=lang('tel')?></label>
                            <div class="col-sm-10">
                                <input name="tel" type="text" class="form-control" id="tel" placeholder="<?=lang('tel')?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        -->
        <!--
        <div class="tab-pane fade" id="history" role="tabpanel">.3..</div>
        <div class="tab-pane fade" id="transaction" role="tabpanel">.4..</div>
        <div class="tab-pane fade" id="reward" role="tabpanel">.5..</div>
        -->
    </div>
        <input type="hidden" id="task" name="task">
    </form>
</div>

<script>
    function refresh_cities ()
    {
        $.each($('.address_holder'), function (inx, address_holder) {
            var address_state = address_holder.querySelector("select[name='address_state[]']");
            var selected_state = address_state.options[address_state.selectedIndex];

            if (address_state.value != '')
            {
                var address_city = address_holder.querySelector("select[name='address_city[]']");
                var selected_city = address_city.options[address_city.selectedIndex];

                var temp_cities = selected_state.getAttribute('cities');

                temp_cities = temp_cities.split("::separator::");

                var options = '<option value="" disabled selected>شهر</option>';
                for (var i = 0; i < temp_cities.length; i++)
                {
                    var selected = "";
                    if (selected_city && selected_city.value == temp_cities[i])
                    {
                        //این آپشن قبلا انتخاب شده است
                        selected = " selected ";
                    }
                    options += '<option value="'+temp_cities[i]+'" '+selected+'>'+temp_cities[i]+'</option>';
                }

                address_city.innerHTML = options;
            }
        });
    }

    window.addEventListener('load', function () {
        refresh_cities ();
    });
</script>