<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("setting/shipping")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <?PHP echo $html_output['sys_msg']; ?>
    <div class="row">
        <div class="col">
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("setting/add_shipping/".$html_output['item_data']['id']), $attributes);
            ?>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    <?=lang('delivery_type')?>
                </label>
                <div class="col-sm-10">
                    <select id="delivery_type" class="form-control" name="delivery_type" onchange="show_hide(this);">
                        <option value=""><?=lang('please_select')?></option>

                        <option value="express_post" <?= set_value('delivery_type', $html_output['item_data']['delivery_type']) == 'express_post' ? "selected" : ""; ?>><?=lang('express_post')?></option>

                        <option value="registered_post" <?= set_value('delivery_type', $html_output['item_data']['delivery_type']) == 'registered_post' ? "selected" : ""; ?>><?=lang('registered_post')?></option>

                        <option value="peyk_delivery" <?= set_value('delivery_type', $html_output['item_data']['delivery_type']) == 'peyk_delivery' ? "selected" : ""; ?>><?=lang('peyk_delivery')?></option>
                    </select>
                </div>
            </div>

            <div class="peyk_delivery registered_post express_post delivery_type form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('state_of_origin_send')?></div>
                <div class="col-sm-10">
					<?PHP echo $html_output['state_of_origin_send_list']; ?>
                </div>
            </div>

            <script>
                function send_to_box (montakhab) {
                    var shahre_montakhab = montakhab.value;
                    var new_option = '<option value="' + shahre_montakhab + '">' + shahre_montakhab + '</option>';
                    var all_ostan_e_box = document.getElementsByClassName("ostan_e_box");

                    [].forEach.call(all_ostan_e_box, function(ostan_e_box){
                        var is_option_in_ostan_list = false;

                        for (var i = 0; i < ostan_e_box.options.length; i++)
                        {
                            if (shahre_montakhab == ostan_e_box.options[i].value)
                            {
                                is_option_in_ostan_list = true;
                            }
                        }

                        if (montakhab.checked)
                        {
                            //آپشن تیک خورده است
                            //بنابراین باید به لیست اضافه شود

                            if (is_option_in_ostan_list)
                            {
                                //آپشن قبلا در لیست اضافه شده است (ممکن است کاربر در حال ویرایش است و آپشنها از دیتابیس لود شده است)
                            }
                            else
                            {
                                ostan_e_box.insertAdjacentHTML("beforeend", new_option);
                                //ostan_e_box.innerHTML += new_option;
                            }
                        }
                        else
                        {
                            //تیک آپشن برداشته شده است
                            //بنابراین باید از لیست حذف شود

                            if (is_option_in_ostan_list)
                            {
                                //آپشن قبلا در لیست درج شده بوده است. حال میبایست آنرا حذف کنیم
                                if (confirm('این شهر در لیست درج شده است. آیا مطمئن هستید میخواهید آنرا حذف کنید؟')) {
                                    // کاربر تائید کرده است که میخواهد شهر را حذف کند.
                                    //شهر مورد نظر را از تمامی آپشن لیستها حذف میکنیم
                                    [].forEach.call(all_ostan_e_box, function(temp_ostan_e_box){
                                        for (var i = 0; i < temp_ostan_e_box.options.length; i++)
                                        {
                                            if (shahre_montakhab == temp_ostan_e_box.options[i].value)
                                            {
                                                //این آپشن باید از لیست حذف شود.
                                                temp_ostan_e_box.removeChild(temp_ostan_e_box.options[i]);
                                            }
                                        }
                                    });

                                } else {
                                    // Do nothing!
                                }
                            }
                            else
                            {
                                //آپشن قبلا در لیست نبوده است!
                                //نمیتوان چیزی که در لیست نیست را حذف کرد!
                            }
                        }
                    });
                }
            </script>

            <div class="peyk_delivery delivery_type form-group row">
                <div class="col">
                    <button type="button" class="col-md-2 offset-md-5 btn btn-success" onclick="add_peyk_box();"><i class="fas fa-plus"></i> <?=lang('add_tariff')?></button>
                </div>
            </div>

            <div class="peyk_delivery delivery_type" id="peyk_holder">
				<?= $html_output['peyk_box']; ?>
            </div>

            <script>
                function peyk_tariff_box(node_click_shode) {
                    var node_asli = node_click_shode.closest('.peyk_box');

                    var peyk_tariff_box = node_asli.getElementsByClassName('peyk_tariff_box')[0];

                    var temp_str =
                        '<div class="row tariff_box_2 mb-3">'+
                        '<div class="col">'+
                        '<div class="row">'+
                        '<div class="col">'+
                        '<a href="#" class="close mb-3" data-dismiss="alert" aria-label="close" onclick="this.parentElement.parentElement.parentElement.parentElement.parentElement.removeChild(this.parentElement.parentElement.parentElement.parentElement);">×</a>'+
                        '</div>'+
                        '</div>'+
                        '<div class="row">'+
                        '<div class="col-md-3">'+
                        '<label><?=lang('from_weight')?></label>'+
                        '<input type="text" class="from_weight form-control" name="from_weight[]" placeholder="<?=lang('from_weight')?>" value="">'+
                        '</div>'+
                        '<div class="col-md-3">'+
                        '<label><?=lang('to_weight')?></label>'+
                        '<input type="text" class="to_weight form-control" name="to_weight[]" placeholder="<?=lang('to_weight')?>" value="">'+
                        '</div> <div class="col-md-3">'+
                        '<label><?=lang('delivery_cost')?></label>'+
                        '<input type="text" class="delivery_cost form-control" name="delivery_cost[]" placeholder="<?=lang('delivery_cost')?>" value="">'+
                        '</div> <div class="col-md-3"> <label><?=lang('other_costs')?></label>'+
                        '<input type="text" class="other_costs form-control" name="other_costs[]" placeholder="<?=lang('other_costs')?>" value="">'+
                        '</div>'+
                        '</div>'+
                        '</div>'+
                        '</div>';

                    peyk_tariff_box.insertAdjacentHTML("afterbegin", temp_str);
                }

                function add_peyk_box() {
                    var peyk_holder = document.getElementById("peyk_holder");

                    //لیست شهرهایی که در Select خواهند بود را میسازیم
                    var option_list = "";
                    //همه شهرهای مبدا را میگیریم
                    var all_shahre_mabda = document.getElementsByClassName('shahre_mabda');
                    [].forEach.call(all_shahre_mabda, function (shahre_mabda) {
                        if (shahre_mabda.checked)
                        {
                            //شهر مبدا تیک خورده است، بنابراین آنرا به لیست جدید اضافه میکنیم
                            option_list += '<option value="' + shahre_mabda.value + '">' + shahre_mabda.value + '</option>';
                        }
                    });

                    var temp_str =
                        '<div class="peyk_box form-group tariff_box mb-3">'+
                        '<div class="tariff_box_tool_bar">'+
                        '<a href="javascript:void(0)" onclick="peyk_tariff_box(this); refresh_fields_names(this);"><i class="fas fa-plus-square fa-1x text-success"></i></a> '+
                        '<a href="javascript:void(0)" onclick="this.parentElement.parentElement.parentElement.removeChild(this.parentElement.parentElement);"><i class="fas fa-window-close fa-1x text-danger"></i></a>'+
                        '</div>'+
                        '<div class="container-fluid">'+
                        '<div class="row">'+
                        '<div class="form-group col-md-2">'+
                        '<label><?=lang('state')?></label>'+
                        '<select name="state[]" class="ostan_e_box form-control" onchange="refresh_fields_names(this);">'+
                        option_list +
                        '</select>'+
                        '</div>'+
                        '<div class="form-group col-md-2">'+
                        '<label><?=lang('region_name')?></label>'+
                        '<input type="text" class="region_name form-control" name="region_name[]" oninput="refresh_fields_names(this);" placeholder="<?=lang('region_name')?>" value="">'+
                        '</div>'+
                        '<div class="form-group col-md-8 peyk_tariff_box">'+
                        '</div>'+
                        '</div>'+
                        '</div>'+
                        '</div>';
                    peyk_holder.insertAdjacentHTML("afterbegin", temp_str);

                }
            </script>


            <div class="registered_post express_post delivery_type form-group row">
                <div class="col">
                    <button type="button" class="col-md-2 offset-md-5 btn btn-success" onclick="add_post_box();"><i class="fas fa-plus"></i> <?=lang('add_tariff')?></button>
                </div>
            </div>

            <script>
                function refresh_fields_names(selected_state) {
                    var node_asli = selected_state.closest('.peyk_box');
                    var ostan_e_box = node_asli.getElementsByClassName("ostan_e_box")[0];

                    if (ostan_e_box.value !=""){
                        var region_name_input = node_asli.getElementsByClassName("region_name")[0];
                        var name = region_name_input.setAttribute("name", "region_name["+ ostan_e_box.value +"][]");

                        if (region_name_input.value !=""){
                            var from_weight_input = node_asli.getElementsByClassName("from_weight");
                            [].forEach.call(from_weight_input, function (from_weight) {
                                from_weight.setAttribute("name", "from_weight["+ ostan_e_box.value +"]["+ region_name_input.value +"][]");
                            });

                            var to_weight_input = node_asli.getElementsByClassName("to_weight");
                            [].forEach.call(to_weight_input, function (to_weight) {
                                to_weight.setAttribute("name", "to_weight["+ ostan_e_box.value +"]["+ region_name_input.value +"][]");
                            });

                            var delivery_cost_input = node_asli.getElementsByClassName("delivery_cost");
                            [].forEach.call(delivery_cost_input, function (delivery_cost) {
                                delivery_cost.setAttribute("name", "delivery_cost["+ ostan_e_box.value +"]["+ region_name_input.value +"][]");
                            });

                            var other_costs_input = node_asli.getElementsByClassName("other_costs");
                            [].forEach.call(other_costs_input, function (other_costs) {
                                other_costs.setAttribute("name", "other_costs["+ ostan_e_box.value +"]["+ region_name_input.value +"][]");
                            });
                        }

                    }

                }

                /*function refresh_fields_names_2(from_weight_typed) {
                    var node_asli = from_weight_typed.closest('.post_box');
                    var from_weight_input = node_asli.getElementsByClassName("from_weight")[0];
                    var to_weight_input = node_asli.getElementsByClassName("to_weight")[0];

                    if (from_weight_input.value !="" || to_weight_input.value !="" ){
                        var within_the_province_input = node_asli.getElementsByClassName("within_the_province")[0];
                        var name = within_the_province_input.setAttribute("name", "within_the_province['"+ from_weight_input.value +"']['"+ to_weight_input.value +"'][]");

                    }

                    if (from_weight_input.value !="" || to_weight_input.value !="" ){
                        var tax_within_input = node_asli.getElementsByClassName("tax_within")[0];
                        var name = tax_within_input.setAttribute("name", "tax_within['"+ from_weight_input.value +"']['"+ to_weight_input.value +"'][]");

                    }

                    if (from_weight_input.value !="" || to_weight_input.value !="" ){
                        var insurance_within_input = node_asli.getElementsByClassName("insurance_within")[0];
                        var name = insurance_within_input.setAttribute("name", "insurance_within['"+ from_weight_input.value +"']['"+ to_weight_input.value +"'][]");

                    }

                    if (from_weight_input.value !="" || to_weight_input.value !="" ){
                        var other_costs_within_input = node_asli.getElementsByClassName("other_costs_within")[0];
                        var name = other_costs_within_input.setAttribute("name", "other_costs_within['"+ from_weight_input.value +"']['"+ to_weight_input.value +"'][]");

                    }

                    if (from_weight_input.value !="" || to_weight_input.value !="" ){
                        var out_of_the_province_input = node_asli.getElementsByClassName("out_of_the_province")[0];
                        var name = out_of_the_province_input.setAttribute("name", "out_of_the_province['"+ from_weight_input.value +"']['"+ to_weight_input.value +"'][]");

                    }

                    if (from_weight_input.value !="" || to_weight_input.value !="" ){
                        var tax_out_of_input = node_asli.getElementsByClassName("tax_out_of")[0];
                        var name = tax_out_of_input.setAttribute("name", "tax_out_of['"+ from_weight_input.value +"']['"+ to_weight_input.value +"'][]");

                    }

                    if (from_weight_input.value !="" || to_weight_input.value !="" ){
                        var insurance_out_of_input = node_asli.getElementsByClassName("insurance_out_of")[0];
                        var name = insurance_out_of_input.setAttribute("name", "insurance_out_of['"+ from_weight_input.value +"']['"+ to_weight_input.value +"'][]");

                    }


                    if (from_weight_input.value !="" || to_weight_input.value !="" ){
                        var other_costs_out_of_input = node_asli.getElementsByClassName("other_costs_out_of")[0];
                        var name = other_costs_out_of_input.setAttribute("name", "other_costs_out_of['"+ from_weight_input.value +"']['"+ to_weight_input.value +"'][]");

                    }

                }*/

                function add_post_box() {
                    var post_box_holder = document.getElementById("post_box_holder");
                    var temp_str =
                        '<div class="post_box form-group tariff_box">'+
                        '<div class="tariff_box_tool_bar">'+
                        '<a href="#" onclick="this.parentElement.parentElement.parentElement.removeChild(this.parentElement.parentElement); return false;"><i class="fas fa-window-close fa-1x text-danger"></i></a>'+
                        '</div>'+
                        '<div class="container-fluid">'+
                        '<div class="row">'+
                        '<div class="form-group col-md-2">'+
                        '<label><?=lang('from_weight')?></label>'+
                        '<input type="text" class="from_weight form-control" name="from_weight[]" placeholder="<?=lang('from_weight')?>" value="">'+
                        '</div>'+
                        '<div class="form-group col-md-2">'+
                        '<label><?=lang('to_weight')?></label>'+
                        '<input type="text" class="to_weight form-control" name="to_weight[]" placeholder="<?=lang('to_weight')?>" value="">'+
                        '</div>'+
                        '<div class="form-group col-md-8 tariff_box_2">'+
                        '<div class="container-fluid">'+
                        '<div class="row">'+
                        '<div class="form-group col-md-3">'+
                        '<label><?=lang('within_the_province')?></label>'+
                        '<input type="text" class="within_the_province form-control" name="within_the_province[]" placeholder="<?=lang('within_the_province')?>" value="">'+
                        '</div>'+
                        '<div class="form-group col-md-3">'+
                        '<label><?=lang('tax')?></label>'+
                        '<input type="text" class="tax_within form-control" name="tax_within[]" placeholder="<?=lang('tax')?>" value="">'+
                        '</div>'+
                        '<div class="form-group col-md-3">'+
                        '<label><?=lang('insurance')?></label>'+
                        '<input type="text" class="insurance_within form-control" name="insurance_within[]" placeholder="<?=lang('insurance')?>" value="">'+
                        '</div>'+
                        '<div class="form-group col-md-3">'+
                        '<label><?=lang('other_costs')?></label>'+
                        '<input type="text" class="other_costs_within form-control" name="other_costs_within[]" placeholder="<?=lang('other_costs')?>" value="">'+
                        '</div>'+
                        '</div>'+
                        '</div>'+
                        '</div>'+
                        '<div class="form-group col-md-8 offset-md-4 tariff_box_2">'+
                        '<div class="container-fluid">'+
                        '<div class="row">'+
                        '<div class="form-group col-md-3">'+
                        '<label><?=lang('out_of_the_province')?></label>'+
                        '<input type="text" class="out_of_the_province form-control" name="out_of_the_province[]" placeholder="<?=lang('out_of_the_province')?>" value="">'+
                        '</div>'+
                        '<div class="form-group col-md-3">'+
                        '<label><?=lang('tax')?></label>'+
                        '<input type="text" class="tax_out_of form-control" name="tax_out_of[]" placeholder="<?=lang('tax')?>" value="">'+
                        '</div>'+
                        '<div class="form-group col-md-3">'+
                        '<label><?=lang('insurance')?></label>'+
                        '<input type="text" class="insurance_out_of form-control" name="insurance_out_of[]" placeholder="<?=lang('insurance')?>" value="">'+
                        '</div>'+
                        '<div class="form-group col-md-3">'+
                        '<label><?=lang('other_costs')?></label>'+
                        '<input type="text" class="other_costs_out_of form-control" name="other_costs_out_of[]" placeholder="<?=lang('other_costs')?>" value="">'+
                        '</div>'+
                        '</div>'+
                        '</div>'+
                        '</div>'+
                        '</div>'+
                        '</div>'+
                        '</div>';
                    post_box_holder.insertAdjacentHTML("afterbegin", temp_str);

                }
            </script>

            <div class="registered_post express_post delivery_type" id="post_box_holder">
				<?= $html_output['post_box']; ?>
            </div>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('sort')?></div>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="sort" placeholder="<?=lang('sort')?>" value="<?php echo set_value('sort',$html_output['item_data']['sort']); ?>">
                </div>
            </div>

            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('publish')?></legend>
                <div class="col-sm-10">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="publish" id="publish1" value="yes" checked <?= set_value('publish', $html_output['item_data']['publish'])== 'yes' ? "" : ""; ?>>
                        <label class="form-check-label" for="publish1">
                            <?=lang('yes')?>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="publish" id="publish2" value="no" <?= set_value('publish', $html_output['item_data']['publish']) == 'no' ? "checked" : ""; ?>>
                        <label class="form-check-label" for="publish2">
                            <?=lang('no')?>
                        </label>
                    </div>
                </div>
            </div>
            <input type="hidden" id="task" name="task">
            </form>
        </div>
    </div>
</div>


<script>

    show_hide(document.getElementById("delivery_type"));

    function show_hide(selected_delivery_type) {
        var delivery_type = selected_delivery_type.value;
        var all_delivery_type = document.getElementsByClassName("delivery_type");
        [].forEach.call(all_delivery_type, function (delivery_node) {
            delivery_node.classList.add("d-none");

            if (delivery_type != '' && delivery_node.className.indexOf(delivery_type) > -1)
            {
                delivery_node.classList.remove("d-none");
            }

        });
    }

</script>
