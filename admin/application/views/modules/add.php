<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("modules")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>
<div class="container-fluid">
    <?PHP echo $html_output['sys_msg']; ?>
    <div class="row">
        <div class="col">
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("modules/add/".$html_output['item_data']['id']), $attributes);
            ?>
            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label"><?=lang('title')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo set_value('title',$html_output['item_data']['title']); ?>" placeholder="<?=lang('title')?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><?=lang('module_type')?></label>
                <div class="col-sm-10">
                    <select class="form-control" id="module_type" name="type" onchange="refresh_module_type(this);">
						<?PHP echo $html_output['type_list']; ?>
                    </select>
                </div>
            </div>

            <div class="form-group row module_type custom_html">
                <label class="col-sm-2 col-form-label"><?=lang('content')?></label>
                <div class="col-sm-10">
                    <textarea class="form-control ckeditor" name="content" rows="5"><?php echo set_value('content',$html_output['item_data']['content']); ?></textarea>
                </div>
            </div>

            <div class="form-group module_type custom_html">
                <button type="button" onclick="add_file();" class="btn btn-success col-sm-2 btn-sm mb-1"><?=lang('add_file')?></button>
                <div id="upload_module_holder" class="form-group row"><?PHP echo $html_output['item_data']['files_view'] ?></div>

                <script>
                    function add_file() {
                        document.getElementById('upload_module_holder').insertAdjacentHTML('afterbegin', '<div class="upload_module_holder input-group offset-sm-2 col-sm-10 mb-1"><div class="loading_holder"> <div class="content"> <div class="loader_spin"></div> <span><?=lang('please_wait')?></span> </div> </div> <div class="upload_module"> <div class="message_holder"></div> <input class="w3-border" type="file"> <div class="btn btn-success btn-sm" onclick="upload_file(this, \'module\');"><?=lang('upload')?> </div><button type="button" class="close ml-2 mt-1" onclick="delete_parent(this, \'upload_module_holder\');"> <span aria-hidden="true">×</span> </button> </div> <div class="uploaded_module"> <div class="message_holder"></div> <div class="btn btn-danger btn-sm remove_btn" onclick="remove_uploaded_file(this, \'module\')"><?=lang('delete_file')?></div> <input class="uploaded_file" name="files[]" type="hidden" value=""> <div class="btn btn-success btn-sm copy_to_clipboard"><?=lang('copy_file_address_in_memory')?></div> </div> </div>');
                    }
                </script>
            </div>



            <div class="form-group row module_type menu">
                <label class="col-sm-2 col-form-label"><?=lang('menu_categories')?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="menu_categories">
						<?PHP echo $html_output['menu_category_list']; ?>
                    </select>
                </div>
            </div>

            <div class="form-group row module_type slide">
                <label for="title" class="col-sm-2 col-form-label"><?php if($html_output['picture'] != ''){echo lang('uploaded_pictures');}?></label>
                <div class="col-sm-10">
                    <?php if($html_output['picture'] != ''){echo lang("to_remove_picture_select_it");}?>
                    <?php echo $html_output['picture']; ?>
                </div>
                <button type="button" onclick="add_pic();" class="btn btn-success col-sm-2 btn-sm mb-1"><?=lang('add_pic')?></button>
            </div>
            <div id="upload_ax_holder" class="form-group row module_type slide"></div>

            <script>
                function add_pic ()
                {
                    document.getElementById('upload_ax_holder').insertAdjacentHTML('afterbegin', '<div class="upload_ax_unit container-fluid mb-3"><div class="row border rounded bg-light p-2"><label class="col-sm-2 col-form-label"><?=lang('choose_file')?></label> <div class="input-group col-sm-10"> <div class="custom-file"> <input type="file" class="custom-file-input" name="primary_pic[]" required=""> <label class="custom-file-label">فایل مورد نظر را انتخاب نمایید.</label> </div> <button type="button" class="close" onclick="delete_parent(this, \'upload_ax_unit\');"> <span aria-hidden="true">×</span> </button> </div> <label class="col-sm-2 col-form-label"><?=lang('slide_content')?></label> <div class="col-sm-10"><textarea class="form-control mb-1" name="content[]" rows="5"></textarea> </div></div></div>');
                }

            </script>

            <div class="form-group row module_type slide">
                <label class="col-sm-2 col-form-label"><?=lang('slide_width')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="slide_width" name="slide_width" value="<?php echo set_value('slide_width',$html_output['item_data']['slide_width']); ?>" placeholder="<?=lang('slide_width')?>">
                </div>
            </div>

            <div class="form-group row module_type slide">
                <label class="col-sm-2 col-form-label"><?=lang('slide_height')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="slide_height" name="slide_height" value="<?php echo set_value('slide_height',$html_output['item_data']['slide_height']); ?>" placeholder="<?=lang('slide_height')?>">
                </div>
            </div>

            <div id="add_product" class="form-group row module_type selected_items">
                <label class="col-sm-2 col-form-label"><?=lang('product')?>
                    <a tabindex="0" class="" data-toggle="popover" data-trigger="focus" data-content="<?=lang('f1_add_products_for_selected_items_modules')?>" data-original-title="" title=""><i class="text-danger fas fa-question-circle"></i></a>
                </label>
                <div class="col-sm-10 item_box">
                    <input oninput="find_item(this, 'product');" type="text" class="form-control search_field" placeholder="محصول">
                    <div class="found_list mt-1">
                    </div>
                    <div class="selected_holder">
						<?php echo $html_output['item_data']['items_id']?>
                    </div>
                </div>
            </div>

            <div class="form-group row module_type latest most_popular best_sales weekly_discount">
                <label class="col-sm-2 col-form-label"><?=lang('total_number_of_items')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="total_number_of_items" name="number_of_items" value="<?php echo set_value('number_of_items',$html_output['item_data']['number_of_items']); ?>" placeholder="<?=lang('total_number_of_items')?>">
                </div>
            </div>

            <div class="form-group row module_type latest most_popular best_sales weekly_discount selected_items">
                <label class="col-sm-2 col-form-label"><?=lang('number_of_items_per_view')?></label>
                <div class="col-sm-10">
                    <div class="row">
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="view_in_1024" name="view_in_1024" value="<?php echo set_value('view_in_1024',$html_output['item_data']['view_in_1024']); ?>" placeholder="<?=lang('view_in_1024')?>">
                        </div>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="view_in_768" name="view_in_768" value="<?php echo set_value('view_in_768',$html_output['item_data']['view_in_768']); ?>" placeholder="<?=lang('view_in_768')?>">
                        </div>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="view_in_640" name="view_in_640" value="<?php echo set_value('view_in_640',$html_output['item_data']['view_in_640']); ?>" placeholder="<?=lang('view_in_640')?>">
                        </div>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="view_in_320" name="view_in_320" value="<?php echo set_value('view_in_320',$html_output['item_data']['view_in_320']); ?>" placeholder="<?=lang('view_in_320')?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row module_type latest most_popular best_sales weekly_discount selected_items">
                <label class="col-sm-2 col-form-label"><?=lang('number_of_items_per_group')?></label>
                <div class="col-sm-10">
                    <div class="row">
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="group_in_1024" name="group_in_1024" value="<?php echo set_value('group_in_1024',$html_output['item_data']['group_in_1024']); ?>" placeholder="<?=lang('group_in_1024')?>">
                        </div>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="group_in_768" name="group_in_768" value="<?php echo set_value('group_in_768',$html_output['item_data']['group_in_768']); ?>" placeholder="<?=lang('group_in_768')?>">
                        </div>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="group_in_640" name="group_in_640" value="<?php echo set_value('group_in_640',$html_output['item_data']['group_in_640']); ?>" placeholder="<?=lang('group_in_640')?>">
                        </div>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="group_in_320" name="group_in_320" value="<?php echo set_value('group_in_320',$html_output['item_data']['group_in_320']); ?>" placeholder="<?=lang('group_in_320')?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row module_type latest most_popular best_sales weekly_discount selected_items">
                <label class="col-sm-2 col-form-label"><?=lang('space_between')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="space_between" name="space_between" value="<?php echo set_value('space_between',$html_output['item_data']['space_between']); ?>" placeholder="<?=lang('space_between')?>">
                </div>
            </div>

            <div class="form-group row module_type latest most_popular best_sales weekly_discount selected_items">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('loop')?></legend>
                <div class="form-check-inline ml-3">
                    <input class="form-check-input" type="radio" name="loop" id="loop1" value="true" checked <?= set_value('loop', $html_output['item_data']['loop'])== 'true' ? "" : ""; ?>>
                    <label class="form-check-label" for="loop1">
                        <?=lang('yes')?>
                    </label>
                </div>
                <div class="form-check-inline ml-3">
                    <input class="form-check-input" type="radio" name="loop" id="loop2" value="false" <?= set_value('loop', $html_output['item_data']['loop']) == 'false' ? "checked" : ""; ?>>
                    <label class="form-check-label" for="loop2">
                        <?=lang('no')?>
                    </label>
                </div>
            </div>

            <div class="form-group row module_type latest most_popular best_sales weekly_discount selected_items">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('loop_fill_group_with_blank')?></legend>
                <div class="form-check-inline ml-3">
                    <input class="form-check-input" type="radio" name="loop_fill_group_with_blank" id="loop_fill_group_with_blank1" value="true" checked <?= set_value('loop_fill_group_with_blank', $html_output['item_data']['loop_fill_group_with_blank'])== 'true' ? "" : ""; ?>>
                    <label class="form-check-label" for="loop_fill_group_with_blank1">
                        <?=lang('yes')?>
                    </label>
                </div>
                <div class="form-check-inline ml-3">
                    <input class="form-check-input" type="radio" name="loop_fill_group_with_blank" id="loop_fill_group_with_blank2" value="false" <?= set_value('loop_fill_group_with_blank', $html_output['item_data']['loop_fill_group_with_blank']) == 'false' ? "checked" : ""; ?>>
                    <label class="form-check-label" for="loop_fill_group_with_blank2">
                        <?=lang('no')?>
                    </label>
                </div>
            </div>

            <div class="form-group row module_type selected_items">
                <label class="col-sm-2 col-form-label"><?=lang('show_type')?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="show_type">
                        <option value="slide" <?= set_value('show_type', $html_output['item_data']['show_type']) == 'slide' ? "selected" : ""; ?>><?=lang('slide')?></option>
                        <option value="descriptive" <?= set_value('show_type', $html_output['item_data']['show_type']) == 'descriptive' ? "selected" : ""; ?>><?=lang('descriptive')?></option>
						<option value="pargar" <?= set_value('show_type', $html_output['item_data']['show_type']) == 'pargar' ? "selected" : ""; ?>><?=lang('pargar')?></option>
                    </select>
                </div>
            </div>

            <div class="form-group row module_type selected_article_category">
                <label class="col-sm-2 col-form-label"><?=lang('number_of_items_per_page')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="number_of_items_per_page" name="number_of_items_per_page" value="<?php echo set_value('number_of_items_per_page',$html_output['item_data']['number_of_items_per_page']); ?>" placeholder="<?=lang('number_of_items_per_page')?>">
                </div>
            </div>

            <div class="form-group row module_type map">
                <label class="col-sm-2 col-form-label"><?=lang('longitude')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="longitude" name="longitude" value="<?php echo set_value('longitude',$html_output['item_data']['longitude']); ?>" placeholder="<?=lang('longitude')?>">
                </div>
            </div>

            <div class="form-group row module_type map">
                <label class="col-sm-2 col-form-label"><?=lang('latitude')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="latitude" name="latitude" value="<?php echo set_value('latitude',$html_output['item_data']['latitude']); ?>" placeholder="<?=lang('latitude')?>">
                </div>
            </div>

            <div class="form-group row module_type map">
                <label class="col-sm-2 col-form-label"><?=lang('map_height')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="map_height" name="map_height" value="<?php echo set_value('map_height',$html_output['item_data']['map_height']); ?>" placeholder="<?=lang('map_height')?>">
                </div>
            </div>

            <div class="form-group row module_type map">
                <label class="col-sm-2 col-form-label"><?=lang('map_width')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="map_width" name="map_width" value="<?php echo set_value('map_width',$html_output['item_data']['map_width']); ?>" placeholder="<?=lang('map_width')?>">
                </div>
            </div>

            <div class="form-group row module_type map">
                <label class="col-sm-2 col-form-label"><?=lang('zoom')?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="zoom">
						<?PHP echo $html_output['zoom_list']; ?>
                    </select>
                </div>
            </div>

            <div class="form-group row module_type map">
                <label class="col-sm-2 col-form-label"><?=lang('map_type')?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="map_type">
                        <option value="ROADMAP" <?= set_value('map_type', $html_output['item_data']['map_type']) == 'ROADMAP' ? "selected" : ""; ?>>roadmap</option>
                        <option value="SATELLITE" <?= set_value('map_type', $html_output['item_data']['map_type']) == 'SATELLITE' ? "selected" : ""; ?>>satellite</option>
                        <option value="HYBRID" <?= set_value('map_type', $html_output['item_data']['map_type']) == 'HYBRID' ? "selected" : ""; ?>>hybrid</option>
                        <option value="TERRAIN" <?= set_value('map_type', $html_output['item_data']['map_type']) == 'TERRAIN' ? "selected" : ""; ?>>terrain</option>
                    </select>
                </div>
            </div>

            <div class="form-group row module_type map">
                <label class="col-sm-2 col-form-label"><?=lang('api_key')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="api_key" name="api_key" value="<?php echo set_value('api_key',$html_output['item_data']['api_key']); ?>" placeholder="<?=lang('api_key')?>">
                </div>
            </div>

            <div class="form-group row module_type map">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('marker')?></legend>
                <div class="form-check-inline ml-3">
                    <input class="form-check-input" type="radio" name="marker" id="marker1" value="yes" checked <?= set_value('marker', $html_output['item_data']['marker'])== 'yes' ? "" : ""; ?>>
                    <label class="form-check-label" for="marker1">
                        <?=lang('yes')?>
                    </label>
                </div>
                <div class="form-check-inline ml-3">
                    <input class="form-check-input" type="radio" name="marker" id="marker2" value="no" <?= set_value('marker', $html_output['item_data']['marker']) == 'no' ? "checked" : ""; ?>>
                    <label class="form-check-label" for="marker2">
                        <?=lang('no')?>
                    </label>
                </div>
            </div>

            <div class="form-group row module_type map">
                <label class="col-sm-2 col-form-label"><?=lang('marker_title')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="marker_title" name="marker_title" value="<?php echo set_value('marker_title',$html_output['item_data']['marker_title']); ?>" placeholder="<?=lang('marker_title')?>">
                </div>
            </div>

            <div class="form-group row module_type map">
                <label class="col-sm-2 col-form-label"><?=lang('marker_description')?></label>
                <div class="col-sm-10">
                    <textarea class="form-control ckeditorrrrr" name="marker_description" rows="5"><?php echo set_value('marker_description',$html_output['item_data']['marker_description']); ?></textarea>
                </div>
            </div>

            <div class="form-group row module_type selected_article_category">
                <label class="col-sm-2 col-form-label"><?=lang('article_category')?></label>
                <div class="col-sm-10">
                    <ul class="remove_default_list_style">
						<?PHP echo $html_output['categories_list']; ?>
                    </ul>
                </div>
            </div>

            <script>
                function refresh_module_type(chenged_node) {
                    var module_type = chenged_node.value;
                    var all_module_type_nodes = document.getElementsByClassName("module_type");
                    [].forEach.call(all_module_type_nodes, function (module_type_node) {
                        module_type_node.classList.add("d-none");

                        var classes = module_type_node.className;
                        if (classes.indexOf(module_type) > -1) {
                            module_type_node.classList.remove("d-none");
                        }
                    });
                }

                var timer_peyda_kardan_item;
                function find_item(typed_input, item_type) {
                    //console.log(typed_input, item_type);
                    var node_asli = typed_input.closest('.item_box');
                    var found_list_node = node_asli.getElementsByClassName('found_list')[0];

                    //تایمرهای قبلی را پاک کن تا تایمر جدید در پایینتر تنظیم شود
                    clearTimeout(timer_peyda_kardan_item);

                    if (typed_input.value.length > 1)
                    {
                        //بنویس در حال جستجو
                        found_list_node.innerHTML = '<?=lang('searching');?>';

                        //اگه بیشتر از یک کاراکتر تایپ شد، مقدار تایپ شده را به سرور بفرست تا نتایج آیتم مشابه را برگرداند
                        //قبل از فراخوانی AJAX، چند لحظه تاخیر قرار بده
                        timer_peyda_kardan_item = setTimeout(function(){
                            //فیلد مخفیه selected_item را بگیر و مقادیر آنها را برای سرور ارسال کن تا اطلاعات تکراری را بازگشت ندهد
                            var current_items_list = node_asli.getElementsByClassName('selected_item');
                            var current_items_list_ips = [];
                            [].forEach.call(current_items_list, function(current_node,index,arr) {
                                current_items_list_ips.push(current_node.value);
                            });
                            //console.log("current_items_list_ips", current_items_list_ips.length, current_items_list_ips.join());
                            console.log({"req":"find_"+item_type, "key":typed_input.value, "exception":current_items_list_ips.join()});
                            $.ajax({
                                method: "POST",
                                url: '<?=base_url('api/index')?>',//'http://localhost/keshavarz/api/index',
                                data: {"req":"find_"+item_type, "key":typed_input.value, "exception":current_items_list_ips.join()},
                                success: function(result){
                                    console.log("raw response:", result);
                                    var found_items = JSON.parse(result);
                                    //console.log("JSON response:", found_items);

                                    //لیست محصولاتی که از سرور آمده است را بصورت option ایجاد کن
                                    var option_list = "";
                                    found_items.forEach(function (radife_mahsol) {
                                        //option_list += '<option value="'+radife_mahsol.id+'" price="'+radife_mahsol.price+'" title="'+radife_mahsol.title+'">'+radife_mahsol.title+'</option>';
                                        option_list += '<option value="'+radife_mahsol.id+'" title="'+radife_mahsol.title+'">'+radife_mahsol.title+'</option>';
                                    });


                                    if (option_list == "")
                                    {
                                        //موردی یافت نشد
                                        found_list_node.innerHTML = "<?=lang('no_item_found')?>";
                                    }
                                    else
                                    {
                                        //موارد پیدا شده را بصورت لیست نمایش بده
                                        found_list_node.innerHTML = '<select onclick="entekhabe_az_list(this.options[this.selectedIndex], \''+item_type+'\')" size="5" class="form-control">'+option_list+'</select>';
                                    }

                                },
                                error: function(result){
                                    //console.log(result);
                                    //var error = '<div class="alert alert-error">server is not available. please check your connection.</div>';
                                    //$('#notification_bar').append(error);
                                    alert ('server is not available. please check your connection.');
                                }
                            });
                        }, 1000);
                    }
                    else
                    {
                        //لیست جستجوهای قبلی را مخفی کن
                        found_list_node.innerHTML = '';
                    }
                }

                function entekhabe_az_list(item_entekhab_shode, item_type) {
                    //نود اصلی باکس محصول را بگیر
                    var node_asli = item_entekhab_shode.closest('.item_box');

                    //نود جدیدی که باید اضافه شود را میسازیم
                    var new_node_string = '<div class="selected_item_holder">' +
                        '                            <button type="button" class="close" onclick="this.parentNode.parentNode.removeChild(this.parentNode)">\n' +
                        '                                <span>&times;</span>\n' +
                        '                            </button>\n' +
                        '                            <span>'+item_entekhab_shode.getAttribute("title")+'</span>\n' +
                        '                            <input class="selected_item" value="'+item_entekhab_shode.value+'" name="selected_'+item_type+'_item[]" type="hidden">\n' +
                        '                        </div>';

                    //مقدار انتخاب شده را در لیست انتخابها اضافه میکنیم
                    var selected_holder = node_asli.getElementsByClassName('selected_holder')[0];
                    selected_holder.insertAdjacentHTML('afterbegin', new_node_string);

                    //فیلدی که کاربر در حال تایپ عبارت مورد جستجو، در آن بوده است را خالی میکنیم
                    var search_field = node_asli.getElementsByClassName('search_field')[0];
                    search_field.value = '';

                    //لیست بعد از انتخاب حذف میشود.
                    //درصورتیکه کاربر مجدد تایپ کند، لیست دوباره ظاهر میشود (در فانکشن find_item لیست ظاهر میشود)
                    var found_list_node = node_asli.getElementsByClassName('found_list')[0];
                    found_list_node.innerHTML = '';
                }

                if (window.addEventListener) {
                    window.addEventListener('DOMContentLoaded', function () {
                        refresh_module_type(document.getElementById("module_type"));
                    })
                }
                else {
                    window.attachEvent('onload', function () {
                        refresh_module_type(document.getElementById("module_type"));
                    })
                }

            </script>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><?=lang('display')?></label>
                <div class="col-sm-10">
                    <select id="menu_assignment" onchange="show_hide(this);" class="form-control" name="menu_assignment">
                        <option value="all" <?= set_value('menu_assignment', $html_output['item_data']['menu_assignment']) == 'all' ? "selected" : ""; ?>><?=lang('all')?></option>

                        <option value="selected_pages" <?= set_value('menu_assignment', $html_output['item_data']['menu_assignment']) == 'selected_pages' ? "selected" : ""; ?>><?=lang('selected_pages')?></option>

                        <option value="all_except_selected" <?= set_value('menu_assignment', $html_output['item_data']['menu_assignment']) == 'all_except_selected' ? "selected" : ""; ?>><?=lang('all_except_selected')?></option>
                    </select>
                </div>
            </div>

            <div id="display" class="form-group display d-none row">
                <div class="col-sm-2 col-form-label">
                    <?=lang('selected_pages')?>
                </div>
                <div class="col-sm-10 selected_items">
                    <ul>
						<?PHP echo $html_output['selected_pages_list']; ?>
                    </ul>
                </div>
            </div>

            <script>

                show_hide(document.getElementById('menu_assignment'));
                function show_hide(oni_ke_change_shode) {

                    if (oni_ke_change_shode.value=="selected_pages"){
                        document.getElementById('display').classList.remove('d-none');
                    }
                    else if (oni_ke_change_shode.value=="all_except_selected"){
                        document.getElementById('display').classList.remove('d-none');
                    }
                    else {
                        document.getElementById('display').classList.add('d-none');
                    }

                }

            </script>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><?=lang('position')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="position" name="position" value="<?php echo set_value('position',$html_output['item_data']['position']); ?>" placeholder="<?=lang('position')?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><?=lang('sort')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="sort" name="sort" value="<?php echo set_value('sort',$html_output['item_data']['sort']); ?>" placeholder="<?=lang('sort')?>">
                </div>
            </div>

            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('publish')?></legend>
                <div class="form-check-inline ml-3">
                    <input class="form-check-input" type="radio" name="publish" id="publish1" value="yes" checked <?= set_value('publish', $html_output['item_data']['publish'])== 'yes' ? "" : ""; ?>>
                    <label class="form-check-label" for="publish1">
                        <?=lang('yes')?>
                    </label>
                </div>
                <div class="form-check-inline ml-3">
                    <input class="form-check-input" type="radio" name="publish" id="publish2" value="no" <?= set_value('publish', $html_output['item_data']['publish']) == 'no' ? "checked" : ""; ?>>
                    <label class="form-check-label" for="publish2">
                        <?=lang('no')?>
                    </label>
                </div>
            </div>
            <input type="hidden" id="task" name="task">
            </form>
        </div>
    </div>
</div>

