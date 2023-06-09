<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("products")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>
<div class="container-fluid">
    <?PHP echo $html_output['sys_msg']; ?>
	<div class="row">
        <div class="col">
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("products/add/".$html_output['item_data']['id']), $attributes);
            ?>

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#product_tab" role="tab"><?=lang('products')?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#gallery_tab" role="tab"><?=lang('gallery')?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#type_tab" role="tab"><?=lang('type_of_product')?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#category_tab" role="tab"><?=lang('category')?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#option_tab" role="tab"><?=lang('options')?></a>
                </li>
            </ul>

            <div class="tab-content" id="customer_details">
                <div class="tab-pane fade show active" id="product_tab" role="tabpanel">
                    <div class="form-group row">
                        <label for="title" class="col-sm-2 col-form-label"><?=lang('title')?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo set_value('title',$html_output['item_data']['title']); ?>" placeholder="<?=lang('title')?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label"><?=lang('title_alias_url')?>
                            <a class="fix_popover" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_add_categories_url')?>">
                                <i class="text-danger fas fa-question-circle"></i>
                            </a>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="title_alias_url" placeholder="<?=lang('title_alias_url')?>" value="<?php echo set_value('title_alias_url',$html_output['item_data']['title_alias_url']); ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label"><?=lang('description')?></div>
                        <div class="col-sm-10">
                            <textarea name="description" class="form-control" id="description" rows="3"><?php echo set_value('description',$html_output['item_data']['description']); ?></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label"><?=lang('meta_tag_title')?>
                            <a class="fix_popover" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_add_categories_meta_tag_title')?>">
                                <i class="text-danger fas fa-question-circle"></i>
                            </a>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="meta_tag_title" placeholder="<?=lang('meta_tag_title')?>" value="<?php echo set_value('meta_tag_title',$html_output['item_data']['meta_tag_title']); ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label"><?=lang('meta_tag_keywords')?>
                            <a class="fix_popover" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_add_categories_meta_tag_keywords')?>">
                                <i class="text-danger fas fa-question-circle"></i>
                            </a>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="meta_tag_keywords" placeholder="<?=lang('meta_tag_keywords')?>" value="<?php echo set_value('meta_tag_keywords',$html_output['item_data']['meta_tag_keywords']); ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label"><?=lang('meta_tag_description')?>
                            <a class="fix_popover" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_add_categories_meta_tag_description')?>">
                                <i class="text-danger fas fa-question-circle"></i>
                            </a>
                        </div>
                        <div class="col-sm-10">
                            <textarea name="meta_tag_description" class="form-control" id="meta_tag_description" rows="3"><?php echo set_value('meta_tag_description',$html_output['item_data']['meta_tag_description']); ?></textarea>
                        </div>
                    </div>

                    <div class="form-group row offset-2">
                        <div class="col-sm-5 ml-2">
                            <input class="form-check-input" type="checkbox" name="the_comment_registration_section_is_enabled" id="the_comment_registration_section_is_enabled" value="yes" <?= set_value('the_comment_registration_section_is_enabled', $html_output['item_data']['the_comment_registration_section_is_enabled']) == 'yes' ? "checked" : ""; ?>>
                            <label class="form-check-label" for="the_comment_registration_section_is_enabled"><?=lang('the_comment_registration_section_is_enabled')?></label>
                        </div>
                        <div class="col-sm-5 ml-2">
                            <input class="form-check-input" type="checkbox" name="there_is_a_possibility_to_register_new_comments_for_the_user" value="yes" id="there_is_a_possibility_to_register_new_comments_for_the_user"<?= set_value('there_is_a_possibility_to_register_new_comments_for_the_user', $html_output['item_data']['there_is_a_possibility_to_register_new_comments_for_the_user']) == 'yes' ? "checked" : ""; ?>>
                            <label class="form-check-label" for="there_is_a_possibility_to_register_new_comments_for_the_user"><?=lang('there_is_a_possibility_to_register_new_comments_for_the_user')?></label>
                        </div>
                    </div>


                    <div class="form-group row offset-2">
                        <div class="col-sm-5 ml-2">
                            <input class="form-check-input" type="checkbox" name="the_questions_and_answers_registration_section_is_active" id="the_questions_and_answers_registration_section_is_active" value="yes" <?= set_value('the_questions_and_answers_registration_section_is_active', $html_output['item_data']['the_questions_and_answers_registration_section_is_active']) == 'yes' ? "checked" : ""; ?>>
                            <label class="form-check-label" for="the_questions_and_answers_registration_section_is_active"><?=lang('the_questions_and_answers_registration_section_is_active')?></label>
                        </div>
                        <div class="col-sm-5 ml-2">
                            <input class="form-check-input" type="checkbox" name="possibility_to_register_new_questions_and_answers_for_the_user" value="yes" id="possibility_to_register_new_questions_and_answers_for_the_user"<?= set_value('possibility_to_register_new_questions_and_answers_for_the_user', $html_output['item_data']['possibility_to_register_new_questions_and_answers_for_the_user']) == 'yes' ? "checked" : ""; ?>>
                            <label class="form-check-label" for="possibility_to_register_new_questions_and_answers_for_the_user"><?=lang('there_is_a_possibility_to_register_new_questions_and_answers_for_the_user')?></label>
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
                </div>

                <div class="tab-pane fade" id="gallery_tab" role="tabpanel">
                    <div class="form-group row">
                        <label for="title" class="col-sm-2 col-form-label"><?php if($html_output['picture'] != ''){echo lang('uploaded_pictures');}?></label>
                        <div class="col-sm-10">
                            <?php if($html_output['picture'] != ''){echo lang("to_remove_picture_select_it");}?>
                            <?php echo $html_output['picture']; ?>
                        </div>
                    </div>
                    <button type="button" onclick="add_gallery();" class="btn btn-success col-sm-2 btn-sm mb-1"><?=lang('add_pic')?></button>
                    <div id="upload_ax_holder" class="form-group row"></div>

                    <script>
                        function add_gallery ()
                        {
                            document.getElementById('upload_ax_holder').insertAdjacentHTML('afterbegin', '<div class="upload_ax_unit input-group offset-sm-2 col-sm-10 mb-1"> <div class="custom-file"> <input type="file" class="custom-file-input" name="primary_pic[]" required> <label class="custom-file-label" ><?=lang('choose_file')?></label> </div> <button type="button" class="close" onclick="delete_ax_holder(this);"> <span aria-hidden="true">×</span> </button> </div>');
                        }

                        function delete_ax_holder(nodi_ke_klick_shode) {
                            var nod = nodi_ke_klick_shode.closest('.upload_ax_unit');
                            nod.parentNode.removeChild(nod);
                        }

                    </script>
                </div>

                <div class="tab-pane fade" id="type_tab" role="tabpanel">
                    <div class="form-group row">
                        <label for="type_of_category" class="col-sm-2 col-form-label">
                            <?=lang('type_of_category')?>
                        </label>
                        <div class="col-sm-10">
                            <select id="type_of_category" class="form-control" name="type_of_category" onchange="refresh_noe_product(this);">
                                <option value="please_select"><?=lang('please_select')?></option>

                                <option value="physical" <?= set_value('type_of_category', $html_output['item_data']['type_of_category']) == 'physical' ? "selected" : ""; ?>><?=lang('physical')?></option>

                                <option value="virtual"  <?= set_value('type_of_category', $html_output['item_data']['type_of_category']) == 'virtual' ? "selected" : ""; ?>><?=lang('virtual')?></option>

                            </select>
                        </div>
                    </div>

                    <script>
                        function refresh_noe_product(noe_montahkab) {
                            var noe_montahkab = noe_montahkab.value;
                            var father_element = document.getElementById("noe_product_fields");

                            //ابتدا همه فرزندان physical و virtual را مخفی میکنیم
                            [].forEach.call(father_element.childNodes, function(node_row) {
                                if (node_row.nodeType == 1 && node_row.className.indexOf('physical') >= 0)
                                {
                                    node_row.className = node_row.className += " d-none";
                                }
                            });

                            [].forEach.call(father_element.getElementsByClassName(noe_montahkab), function(node_row) {
                                node_row.className = node_row.className.replace(/\bd-none\b/g, "");
                                node_row.className = node_row.className.replace(/  /g, " ");
                            });
                        }

                    </script>

                    <div id="noe_product_fields">
                        <div class="form-group row physical d-none">
                            <label class="col-sm-2 col-form-label"><?=lang('appearance')?></label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="weight" placeholder="<?=lang('weight')?>" value="<?php echo set_value('weight',$html_output['item_data']['weight']); ?>">
                            </div>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="length" placeholder="<?=lang('length')?>" value="<?php echo set_value('length',$html_output['item_data']['length']); ?>">
                            </div>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="width" placeholder="<?=lang('width')?>" value="<?php echo set_value('width',$html_output['item_data']['width']); ?>">
                            </div><div class="col-sm-2">
                                <input type="text" class="form-control" name="height" placeholder="<?=lang('height')?>" value="<?php echo set_value('height',$html_output['item_data']['height']); ?>">
                            </div>
                        </div>

                        <div class="form-group row physical d-none">
                            <label class="col-sm-2 col-form-label"><?=lang('number')?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="number" placeholder="<?=lang('number')?>" value="<?php echo set_value('number',$html_output['item_data']['number']); ?>">
                            </div>
                        </div>

                        <div class="form-group row physical d-none">
                            <label class="col-sm-2 col-form-label"><?=lang('min_number')?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="min_number" placeholder="<?=lang('min_number')?>" value="<?php echo set_value('min_number',$html_output['item_data']['min_number']); ?>">
                            </div>
                        </div>

                        <div class="form-group row physical d-none">
                            <label class="col-sm-2 col-form-label"><?=lang('finish')?></label>
                            <div class="col-sm-10">
                                <select class="form-control" name="finish">

                                    <option value="1"  <?= set_value('finish', $html_output['item_data']['finish']) == '1' ? "selected" : ""; ?>>غیر قابل مشاهده</option>

                                    <option value="2"  <?= set_value('finish', $html_output['item_data']['finish']) == '2' ? "selected" : ""; ?>>قابل مشاهده و غیرقابل سفارش</option>

                                    <option value="3"  <?= set_value('finish', $html_output['item_data']['finish']) == '3' ? "selected" : ""; ?>>قابل مشاهده و قابل سفارش</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"><?=lang('price')?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="price" value="<?php echo set_value('price',$html_output['item_data']['price']); ?>">
                            </div>
                        </div>

                        <!--
                        <div class="form-group row">
                            <legend class="col-form-label col-sm-2 pt-0"><?=lang('price_show')?>
                                <a class="fix_popover" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_price_show')?>">
                                    <i class="text-danger fas fa-question-circle"></i>
                                </a>
                            </legend>

                            <div class="form-check-inline ml-3">
                                <input class="form-check-input" type="radio" name="price_show" id="price_show1" value="yes" checked >
                                <label class="form-check-label" for="price_show1">
                                    <?=lang('yes')?>
                                </label>
                            </div>
                            <div class="form-check-inline ml-3">
                                <input class="form-check-input" type="radio" name="price_show" id="price_show2" value="no">
                                <label class="form-check-label" for="price_show2">
                                    <?=lang('no')?>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <legend class="col-form-label col-sm-2 pt-0"><?=lang('is_this_product_free')?>
                                <a class="fix_popover" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_is_this_product_free')?>">
                                    <i class="text-danger fas fa-question-circle"></i>
                                </a>
                            </legend>

                            <div class="form-check-inline ml-3">
                                <input class="form-check-input" type="radio" name="free_product" id="is_this_product_free1" value="yes">
                                <label class="form-check-label" for="is_this_product_free1">
                                    <?=lang('yes')?>
                                </label>
                            </div>
                            <div class="form-check-inline ml-3">
                                <input class="form-check-input" type="radio" name="free_product" id="is_this_product_free2" value="no" checked>
                                <label class="form-check-label" for="is_this_product_free2">
                                    <?=lang('no')?>
                                </label>
                            </div>
                        </div>
                        -->

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"><?=lang('type_of_discount')?></label>
                            <div class="col-sm-10">
                                <select class="form-control" name="type_of_discount">
                                    <option value="percentage" <?= set_value('type_of_discount', $html_output['item_data']['type_of_discount']) == 'percentage' ? "selected" : ""; ?>><?=lang('percentage')?></option>

                                    <option value="static_value" <?= set_value('type_of_discount', $html_output['item_data']['type_of_discount']) == 'static_value' ? "selected" : ""; ?>><?=lang('static_value')?></option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"><?=lang('discount_amount')?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="discount_amount" value="<?php echo set_value('discount_amount',$html_output['item_data']['discount_amount']); ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"><?=lang('points_buy')?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="points_buy" value="<?php echo set_value('points_buy',$html_output['item_data']['points_buy']); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="category_tab" role="tabpanel">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label"><?=lang('brand')?></label>
                        <div class="col-sm-10">
                            <select class="form-control" name="brand">

                                <?PHP echo $html_output['brand_list']; ?>

                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="category" class="col-sm-2 col-form-label">
                            <?=lang('category')?>
                        </label>
                        <div class="col-sm-10">
                            <select id="category" class="form-control" name="category" onchange="refresh_fields(this);refresh_options(this);">
                                <?PHP echo $html_output['categories_list']; ?>
                            </select>
                        </div>
                    </div>
                    <div id="main_fields_holder"></div>
                </div>
                <div class="tab-pane fade" id="option_tab" role="tabpanel">

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col">
                                <div id="main_options_holder"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="task" name="task">
            </form>
        </div>
    </div>
</div>

<script>
    function refresh_fields (oni_ke_change_shode, submited_values) {
        if (typeof submited_values == 'undefined')
        {
            submited_values = '';
        }
        //document.getElementById('attribute_groups').innerHTML = '';
        //document.getElementById('attribute_groups').disabled = true;
        var main_fields_holder = document.getElementById("main_fields_holder");
        main_fields_holder.innerHTML = '<?=lang('please_wait')?>';
        console.log("submited_values:", submited_values);
        if (submited_values != "")
        {
            //submited_values = submited_values.replace(/\r?\n|\r/g, "");
            submited_values = escapeSpecialChars(submited_values);

            var submited_values_json = JSON.parse(submited_values);
            //console.log(submited_values);
        }

        //وقتی صفحه لود میشود، ما تلاش میکنیم تا فیلدهای مجموعه ی انتخاب شده را بسازیم
        //بنابراین وقتی صفحه برای اولین بار باز میشود (افزودن محصول جدید)، هنوز دسته بندی محصول
        //انتخاب نشده است و ما میبایست مطمئن شویم که درخواست تهی برای سرور ارسال نکنیم
        if (oni_ke_change_shode.value == '')
        {
            main_fields_holder.innerHTML = '';
        }
        else
        {
            $.ajax({
                method: "POST",
                url: '<?=base_url('api/index')?>',//'http://localhost/keshavarz/api/index',
                data: {"req":"get_fields", "category_id":oni_ke_change_shode.value},
                success: function(result){
                    console.log("raw response Fields:", result);
                    var json_obj = JSON.parse(result);
                    //console.log("JSON response Fields:", json_obj);
                    main_fields_holder.innerHTML = "";
                    //بین همه ردیفهای جیسون، حلقه میزنیم تا عملیات اضافه کردن فیلد را برای هرکدام از ردیفها
                    //اعمال کنیم
                    //array.forEach(function(currentValue, index, arr), thisValue)
                    for (var field in json_obj) {
                        //console.log(json_obj[field]);
                        add_title(json_obj[field]);
                    }
                    //json_obj.forEach(add_title);

                    //add_title(json_obj[0], 0, json_obj);
                    //add_title(json_obj[1], 1, json_obj);
                    //add_title(json_obj[0]);

                    function add_title(line) {
                        //console.log(line.attribute_group_name);
                        //var line = json_obj[0];
                        var fields = line.fields;

                        if (fields.length == 0)
                        {
                            //عنوانش رو چاپ نکن
                        }
                        else
                        {
                            //عنوانش رو چاپ کن
                            main_fields_holder.innerHTML += '<div class="form-group row h5"><label class="col-sm-2 col-form-label">'+ line.attribute_group_name +'</label></div>';
                            // اینجا حلقه ای قرار میگیرد که تمامی سلولهای فیلد رو چاپ میکنه

                            fields.forEach(add_field);


                            function add_field(cell_field) {
                                //مقداری را که قبلا سابمیت شده بوده است را میگیریم
                                //console.log('submited_values_json', submited_values_json);
                                var submitted_value = '';
                                //if(submited_values_json != undefined)
                                if(submited_values_json != undefined && submited_values_json[cell_field.id] != undefined)
                                {
                                    submitted_value = submited_values_json[cell_field.id];
                                }
                                //console.log("submitted_value", submitted_value);
                                var field_value = '';

                                //console.log(cell_field.type);
                                //main_fields_holder.innerHTML += 'fields';
                                if (cell_field.type == 'single_case')
                                {
                                    //چک میکنیم که مقداریکه قبلا سابمیت شده بوده است، تهی نباشد
                                    if (submitted_value != "")
                                    {
                                        field_value = submitted_value;
                                    }

                                    //این کد برای درج و اجرا فیلدهایی با نوع فیلد single_case میباشد
                                    main_fields_holder.innerHTML += '<div class="form-group row"><label class="col-sm-2 col-form-label">'+cell_field.title+'</label><div class="col-sm-10"><input type="text" value="'+field_value+'" class="form-control" name="single_case_'+cell_field.id+'"></div></div>';
                                }

                                if (cell_field.type == 'multiple_case')
                                {
                                    var temp_fields_array = [];
                                    //console.log('submitted_value_shooo', submitted_value);
                                    //چک میکنیم که مقداریکه قبلا سابمیت شده بوده است، تهی یا تعریف نشده، نباشد
                                    if (submitted_value != "")
                                    {
                                        var temp_field_value = submitted_value;
                                        temp_field_value = temp_field_value.replace(/\r\n/g, "\n");
                                        temp_field_value = temp_field_value.replace(/\r/g, "\n");
                                        temp_field_value = temp_field_value.replace("  ", " ");//همه فاصلههای بیشتر از یک را تبدیل به یکی میکنیم
                                        temp_field_value = temp_field_value.split('::new_line::');

                                        temp_field_value.forEach(function (multiple_case_row) {
                                            if (multiple_case_row != "" && multiple_case_row != " ")
                                            {
                                                //تمامی فاصله های قبل و بعد از نوشته را حذف میکنیم
                                                multiple_case_row = multiple_case_row.replace(/^\s+|\s+$/gm,'');
                                                temp_fields_array.push(multiple_case_row);
                                            }
                                        });
                                    }

                                    field_value = temp_fields_array.join("\n");
                                    //این کد برای درج و اجرا فیلدهایی با نوع فیلد multiple_case میباشد
                                    main_fields_holder.innerHTML += '<div class="form-group row"><label class="col-sm-2 col-form-label">'+cell_field.title+'</label><div class="col-sm-10"><textarea class="form-control" name="multiple_case_'+cell_field.id+'" rows="5">'+field_value+'</textarea></div></div>';
                                }

                                if (cell_field.type == 'textarea')
                                {
                                    //چک میکنیم که مقداریکه قبلا سابمیت شده بوده است، تهی یا تعریف نشده، نباشد
                                    if (submitted_value != "")
                                    {
                                        field_value = submitted_value;
                                        field_value = field_value.replace(/::new_line::/g, '\n');
                                    }

                                    //این کد برای درج و اجرا فیلدهایی با نوع فیلد textarea میباشد
                                    main_fields_holder.innerHTML += '<div class="form-group row"><label class="col-sm-2 col-form-label">'+cell_field.title+'</label><div class="col-sm-10"><textarea id="'+cell_field.id+'_editor" class="form-control ckeditor" name="textarea_'+cell_field.id+'" rows="5">'+field_value+'</textarea></div></div>';
                                    //ویرایشگر را فعال کن
                                    //document.getElementById(cell_field.id + '_editor').ckeditor();
                                    //CKEDITOR.replace(cell_field.id + '_editor');
                                    //CKEDITOR.replace('ckeditor');
                                }

                                if (cell_field.type == 'checkbox')
                                {
                                    var submitted_array = [];
                                    //چک میکنیم که مقداریکه قبلا سابمیت شده بوده است، تهی نباشد
                                    if (submitted_value != "")
                                    {
                                        submitted_array = submitted_value.split("~||~");
                                    }

                                    //مقادیری که میبایست بصورت چکباکس نمایش داده شوند را بگیر
                                    var checkbox_values = cell_field.insert_value;
                                    //console.log("meghdar: ", checkbox_values);
                                    //console.log('submit:', submited_values_json);

                                    //هر خط از مقادیر چکباکس را بصورت یک آیتم در نظر میگیریم
                                    //به همین دلیل، مقادیر insert_valueای که بالاتر گرفتیم را نسبت به هر کاراکتر (خط جدید) از هم جدا میکنیم
                                    //و نتیجه را در آرایه ذخیره میکنیم
                                    //به دلیل وجود مشکل ارسال کاراکترهای خط جدید و کاراکتر Enter
                                    //manzor karakterhaye /r (enter) va /n (new line) hastand
                                    //ابتدا در صورت وجود کاراکتر enter آنرا از متنمان حذف میکنیم
                                    checkbox_values = checkbox_values.replace(/\r\n/g, "\n");
                                    checkbox_values = checkbox_values.replace(/\r/g, "\n");
                                    var checkbox_items = checkbox_values.split("\n");

                                    checkbox_items.forEach(create_checkbox_list);

                                    function create_checkbox_list (checkbox_item, index) {
                                        //console.log('checkbox_item:', checkbox_item);
                                        if (index == 0)
                                        {
                                            //عنوان مجموعه چکباکسها را اضافه کن
                                            main_fields_holder.innerHTML += '<div class="form-group row mb-0"><label class="col-sm-2 col-form-label">'+ cell_field.title +'</label></div>';
                                        }


                                        /*
                                         console.log("submitted_array", submitted_array);
                                         console.log("indexOf ", checkbox_item, " IS:", submitted_array.indexOf(checkbox_item));
                                         console.log("Pahnaye Item:", checkbox_item.length, " Pahnaye khone 0:", submitted_array[0].length);
                                         console.log("hiddenha:", JSON.stringify(checkbox_item));
                                         */
                                        //چک میکنیم که مقداریکه قبلا سابمیت شده بوده است، تهی یا تعریف نشده، نباشد
                                        var checked = '';
                                        if(submitted_array.indexOf(checkbox_item) >= 0) {
                                            checked = 'checked';
                                        }

                                        //خود فیلد چکباکس را اضافه کن
                                        main_fields_holder.innerHTML += '<div class="form-group row offset-sm-2"><div class="col-sm-5 ml-3 ml-sm-2"><input class="form-check-input" type="checkbox" name="checkbox_'+cell_field.id+'[]" id="'+checkbox_item+'_id" value="'+checkbox_item+'" '+checked+'> <label class="form-check-label" for="'+checkbox_item+'_id">'+checkbox_item+'</label></div></div>';
                                    }
                                }

                                if (cell_field.type == 'select')
                                {
                                    var selected_value = '';

                                    //چک میکنیم که مقداریکه قبلا سابمیت شده بوده است، تهی نباشد
                                    if (submitted_value != "")
                                    {
                                        selected_value = submitted_value;
                                    }

                                    //مقادیر آپشنهای داخل سلکت را بگیر
                                    var option_values = cell_field.insert_value;
                                    //console.log("مقادیر چکباکسها: ", checkbox_values);

                                    //هر خط از آپشنها را بصورت یک آیتم در نظر میگیریم
                                    //به همین دلیل، مقادیر insert_valueای که بالاتر گرفتیم را نسبت به هر کاراکتر (خط جدید) از هم جدا میکنیم
                                    //و نتیجه را در آرایه ذخیره میکنیم
                                    option_values = option_values.replace(/\r\n/g, "\n");
                                    option_values = option_values.replace(/\r/g, "\n");
                                    var option_items = option_values.split("\n");
                                    //console.log('option_items_show', option_items);

                                    var option_tag = ""; //این متغیر قرار است هرکدام از تگهای option را در خود نگه دارد
                                    option_items.forEach(create_option_list);

                                    function create_option_list (option_item, index) {


                                        var selected = '';
                                        if(selected_value == option_item)
                                        {
                                            selected = ' selected';
                                        }
                                        option_tag += '<option'+ selected + '>' + option_item + '</option>';
                                    }

                                    main_fields_holder.innerHTML += '<div class="form-group row"><label class="col-sm-2 col-form-label">'+cell_field.title+'</label> <div class="col-sm-10"> <select class="form-control" name="select_'+cell_field.id+'">'+option_tag+'</select> </div> </div>';
                                }

                                if (cell_field.type == 'file')
                                {
                                    var upload_html_module = '<div class="product_box border rounded bg-light p-2 mb-3"><div class="form-group row"><label class="col-sm-2 col-form-label">'+cell_field.title+'</label><div class="input-group col-sm-10"><div class="custom-file"> <input type="file" class="custom-file-input" id="'+cell_field.id+'_id" name="file_'+cell_field.id+'"><label class="custom-file-label" for="'+cell_field.id+'_id"><?=lang('choose_file')?></label></div></div></div><div class="form-group row"><label class="col-sm-2 col-form-label"><?=lang('name_file')?></label><div class="col-sm-10"><input type="text" class="form-control" name="file_name_'+cell_field.id+'"></div></div></div>';
                                    //چک میکنیم که مقداریکه قبلا سابمیت شده بوده است، تهی یا تعریف نشده، نباشد
                                    /*{file_name: "56e072d8be9d99a116ffb86c9267177d.json", file_type: "text/plain", file_path: "/home/amya/public_html/demo/keshavarz/content/file/", full_path: "/home/amya/public_html/demo/keshavarz/content/file/56e072d8be9d99a116ffb86c9267177d.json", raw_name: "56e072d8be9d99a116ffb86c9267177d", …}*/
                                    //console.log(submitted_value);
                                    if (submitted_value != "")
                                    {
                                        upload_html_module = '<div class="product_box border rounded bg-light p-2 mb-3"><div class="form-group row"><label class="col-sm-2 col-form-label">'+cell_field.title+'</label><div class="input-group col-sm-10"><label>' +
                                            '<div class="custom-file"><a target="_blank" href="<?=$field_file_link?>/'+submitted_value.file_name+'">'+submitted_value.orig_name+'</a></div>' +
                                            '<input type="checkbox" value="'+submitted_value.file_name+'" name="delete_file_'+cell_field.id+'">' +
                                            '</label></div></div></div>'+
                                        '<input type="hidden" name="uploaded_fields_id[]" value="'+cell_field.id+'">';
                                    }

                                    //این کد برای درج و اجرا فیلدهایی با نوع فیلد file میباشد
                                    //main_fields_holder.innerHTML += '<div class="product_box border rounded bg-light p-2 mb-3"><div class="form-group row"><label class="col-sm-2 col-form-label">'+cell_field.title+'</label><div class="input-group col-sm-10"><div class="custom-file"> <input type="file" class="custom-file-input" id="'+cell_field.id+'_id" name="file_'+cell_field.id+'"><label class="custom-file-label" for="'+cell_field.id+'_id"><?=lang('choose_file')?></label></div></div></div><div class="form-group row"><label class="col-sm-2 col-form-label"><?=lang('name_file')?></label><div class="col-sm-10"><input type="text" class="form-control" name="file_name_'+cell_field.id+'"></div></div><div class="form-group row"><label class="col-sm-2 col-form-label"><?=lang('type')?></label><div class="col-sm-10"><select class="form-control" name="file_type_'+cell_field.id+'"><option value=""><?=lang('video')?></option><option value=""><?=lang('image')?></option><option value=""><?=lang('document')?></option></select></div></div></div>';


                                    //main_fields_holder.innerHTML += '<div class="product_box border rounded bg-light p-2 mb-3"><div class="form-group row"><label class="col-sm-2 col-form-label">'+cell_field.title+'</label><div class="input-group col-sm-10"><div class="custom-file"> <input type="file" class="custom-file-input" id="'+cell_field.id+'_id" name="file_'+cell_field.id+'"><label class="custom-file-label" for="'+cell_field.id+'_id"><?=lang('choose_file')?></label></div></div></div><div class="form-group row"><label class="col-sm-2 col-form-label"><?=lang('name_file')?></label><div class="col-sm-10"><input type="text" class="form-control" name="file_name_'+cell_field.id+'"></div></div></div>';

                                    main_fields_holder.innerHTML += upload_html_module;
                                }
                            }

                        }
                    }
                },
                error: function(result){
                    console.log(result);
                    //var error = '<div class="alert alert-error">server is not available. please check your connection.</div>';
                    //$('#notification_bar').append(error);
                    alert ('server is not available. please check your connection.');
                }
            });
        }
    }

    function refresh_options (oni_ke_change_shode, submited_values) {
        if (typeof submited_values == 'undefined')
        {
            submited_values = '';
        }
        //document.getElementById('attribute_groups').innerHTML = '';
        //document.getElementById('attribute_groups').disabled = true;
        var main_options_holder = document.getElementById("main_options_holder");
        main_options_holder.innerHTML = '<?=lang('please_wait')?>';
        //console.log("options submited_values:", submited_values);
        if (submited_values != "")
        {
            //submited_values = submited_values.replace(/\r?\n|\r/g, "");
            submited_values = escapeSpecialChars(submited_values);
            var submited_values_json = JSON.parse(submited_values);
            //console.log("options submited_values json:", submited_values_json);
        }

        //وقتی صفحه لود میشود، ما تلاش میکنیم تا آپشنهای «دسته بندی» انتخاب شده را بسازیم
        //بنابراین وقتی صفحه برای اولین بار باز میشود (افزودن محصول جدید)، هنوز دسته بندی محصول
        //انتخاب نشده است و ما میبایست مطمئن شویم که درخواست تهی برای سرور ارسال نکنیم
        if (oni_ke_change_shode.value == '')
        {
            main_options_holder.innerHTML = '';
        }
        else
        {
            $.ajax({
                method: "POST",
                url: '<?=base_url('api/index')?>',//'http://localhost/keshavarz/api/index',
                data: {"req":"get_options", "category_id":oni_ke_change_shode.value},
                success: function(result){
                    //console.log("raw loaded Options:", result);
                    var json_obj = JSON.parse(result);
                    //console.log("JSON loaded Options:", json_obj);
                    main_options_holder.innerHTML = "";
                    var temp_html_output = '';
                    //بین همه ردیفهای جیسون، حلقه میزنیم تا عملیات اضافه کردن فیلد را برای هرکدام از ردیفها
                    //اعمال کنیم

                    for (var field in json_obj) {
                        //console.log(json_obj[field]);
                        //json_obj[field] is something like this:
                        //{"id":"1","title":"test","category":"3","type":"single_entry","insert_value":"","sort":"1","publish":"no"}
                        add_field(json_obj[field]);
                    }
                    //json_obj.forEach(add_title);

                    function add_field(cell_field) {
                        //مقداری را که قبلا سابمیت شده بوده است را میگیریم
                        //console.log('submited_values_json', submited_values_json);
                        var submitted_value = '';
                        if(submited_values_json != undefined && submited_values_json[cell_field.id] != undefined)
                        {
                            submitted_value = submited_values_json[cell_field.id];
                        }
                        //console.log("submitted_value", submitted_value);

                        //console.log(cell_field.type);
                        //main_options_holder.innerHTML += 'fields';
                        /*#######################################################
                        * ## SINGLE ENTRY | MULTIPLE ENTRY | TEXTAREA | UPLOAD ##
                        * #######################################################*/
                        if (cell_field.type == 'single_entry' || cell_field.type == 'multiple_entry' || cell_field.type == 'textarea' || cell_field.type == 'upload')
                        {
                            //چک میکنیم که مقداریکه قبلا سابمیت شده بوده است، تهی نباشد
                            var reduce_total_inventory_yes = '';
                            var reduce_total_inventory_no = 'selected';
                            var product_quantity_with_option = '0';
                            var option_price_situation_plus = '';
                            var option_price_situation_minus = 'selected';
                            var option_price = '0';
                            var option_point_situation_plus = '';
                            var option_point_situation_minus = 'selected';
                            var option_point = '0';
                            var option_weight_situation_plus = '';
                            var option_weight_situation_minus = 'selected';
                            var option_weight = '0';
                            var is_option_required_yes = '';
                            var is_option_required_no = 'selected';
                            var can_option_be_purchased_separately_yes = '';
                            var can_option_be_purchased_separately_no = 'selected';

                            var option_is_checked = ''
                            var option_row = 'disabled';

                            if (submitted_value != "")
                            {
                                option_is_checked = 'checked';
                                option_row = 'enabled';

                                if (submitted_value.reduce_total_inventory == 'yes')
                                {
                                    reduce_total_inventory_yes = 'selected';
                                    reduce_total_inventory_no = '';
                                }

                                if (submitted_value.product_quantity_with_option)
                                {
                                    product_quantity_with_option = submitted_value.product_quantity_with_option;
                                }

                                if (submitted_value.option_price_situation == '+')
                                {
                                    option_price_situation_plus = 'selected';
                                    option_price_situation_minus = '';
                                }

                                if (submitted_value.option_price)
                                {
                                    option_price = submitted_value.option_price;
                                }

                                if (submitted_value.option_point_situation == '+')
                                {
                                    option_point_situation_plus = 'selected';
                                    option_point_situation_minus = '';
                                }

                                if (submitted_value.option_point)
                                {
                                    option_point = submitted_value.option_point;
                                }

                                if (submitted_value.option_weight_situation == '+')
                                {
                                    option_weight_situation_plus = 'selected';
                                    option_weight_situation_minus = '';
                                }

                                if (submitted_value.option_weight)
                                {
                                    option_weight = submitted_value.option_weight;
                                }

                                if (submitted_value.is_option_required == 'yes')
                                {
                                    is_option_required_yes = 'selected';
                                    is_option_required_no = '';
                                }

                                if (submitted_value.can_option_be_purchased_separately == 'yes')
                                {
                                    can_option_be_purchased_separately_yes = 'selected';
                                    can_option_be_purchased_separately_no = '';
                                }
                            }

                            //این کد برای درج و اجرا فیلدهایی با نوع فیلد single_entry میباشد
                            temp_html_output +=
                                '<tr class="option_holder '+option_row+'">'+
                                '<td scope="row"><input type="checkbox" onchange="$(this).is(\':checked\') ? $(this).closest(\'.option_holder\').addClass(\'enabled\').removeClass(\'disabled\') : $(this).closest(\'.option_holder\').addClass(\'disabled\').removeClass(\'enabled\')" value="'+cell_field.id+'" name="list_options[]" '+option_is_checked+'></td>'+
                                '<td>'+cell_field.title+'</td>'+
                                '<td>'+
                                '<table class="table table-bordered influence_on_product">'+
                                '<thead class="title_table">'+
                                '<tr>'+
                                '<th scope="col" class="fit"><?=lang('subtract_stock')?></th>'+
                                '<th scope="col" class="fit"><?=lang('product_quantity')?></th>'+
                                '<th scope="col" class="fit"><?=lang('price')?></th>'+
                                '<th scope="col" class="fit"><?=lang('point')?></th>'+
                                '<th scope="col" class="fit"><?=lang('weight')?></th>'+
                                '<th scope="col"><?=lang('required')?></th>'+
                                '<th scope="col" class="fit"><?=lang('separate_buy')?></th>'+
                                '</th>'+
                                '</tr>'+
                                '</thead>'+
                                '<tbody>'+
                                '<tr>'+
                                '<td scope="row">'+
                                '<select name="reduce_total_inventory_'+cell_field.id+'" class="form-control">'+
                                '<option '+reduce_total_inventory_yes+' value="yes">بله</option>'+
                                '<option '+reduce_total_inventory_no+' value="no">خیر</option>'+
                                '</select>'+
                                '</td>'+
                                '<td scope="row">'+
                                '<input class="form-control" type="number" min="0" value="'+product_quantity_with_option+'" name="product_quantity_with_option_'+cell_field.id+'">'+
                                '</td>'+
                                '<td scope="row">'+
                                '<select name="option_price_situation_'+cell_field.id+'" class="form-control">'+
                                '<option '+option_price_situation_plus+'>+</option>'+
                                '<option '+option_price_situation_minus+'>-</option>'+
                                '</select>'+
                                '<input class="form-control" type="text" value="'+option_price+'" name="option_price_'+cell_field.id+'">'+
                                '</td>'+
                                '<td scope="row">'+
                                '<select name="option_point_situation_'+cell_field.id+'" class="form-control">'+
                                '<option '+option_point_situation_plus+'>+</option>'+
                                '<option '+option_point_situation_minus+'>-</option>'+
                                '</select>'+
                                '<input class="form-control" type="text" value="'+option_point+'" name="option_point_'+cell_field.id+'">'+
                                '</td>'+
                                '<td scope="row">'+
                                '<select name="option_weight_situation_'+cell_field.id+'" class="form-control">'+
                                '<option '+option_weight_situation_plus+'>+</option>'+
                                '<option '+option_weight_situation_minus+'>-</option>'+
                                '</select>'+
                                '<input class="form-control" type="text" value="'+option_weight+'" name="option_weight_'+cell_field.id+'">'+
                                '</td>'+
                                '<td scope="row">'+
                                '<select name="is_option_required_'+cell_field.id+'" class="form-control">'+
                                '<option '+is_option_required_yes+' value="yes">بله</option>'+
                                '<option '+is_option_required_no+' value="no">خیر</option>'+
                                '</select>'+
                                '</td>'+
                                '<td scope="row">'+
                                '<select name="can_option_be_purchased_separately_'+cell_field.id+'" class="form-control">'+
                                '<option '+can_option_be_purchased_separately_yes+' value="yes">بله</option>'+
                                '<option '+can_option_be_purchased_separately_no+' value="no">خیر</option>'+
                                '</select>'+
                                '</td>'+
                                '</tr>'+
                                '</tbody>'+
                                '</table>'+
                                '</td>'+
                                '</tr>';
                        }

                        /*##########
                        * ## FILE ##
                        * ##########*/
                        if (cell_field.type == 'file')
                        {
                            var upload_html_module = '<input type="file" name="option_file_'+cell_field.id+'" class="form-control">'+
                                '<input class="form-control" placeholder="<?=lang('name_file')?>" type="text" name="option_file_name_'+cell_field.id+'">';

                            //چک میکنیم که مقداریکه قبلا سابمیت شده بوده است، تهی نباشد
                            var reduce_total_inventory_yes = '';
                            var reduce_total_inventory_no = 'selected';
                            var product_quantity_with_option = '0';
                            var option_price_situation_plus = '';
                            var option_price_situation_minus = 'selected';
                            var option_price = '0';
                            var option_point_situation_plus = '';
                            var option_point_situation_minus = 'selected';
                            var option_point = '0';
                            var option_weight_situation_plus = '';
                            var option_weight_situation_minus = 'selected';
                            var option_weight = '0';
                            var is_option_required_yes = '';
                            var is_option_required_no = 'selected';
                            var can_option_be_purchased_separately_yes = '';
                            var can_option_be_purchased_separately_no = 'selected';

                            var option_is_checked = ''
                            var option_row = 'disabled';

                            if (submitted_value != "")
                            {
                                option_is_checked = 'checked';
                                option_row = 'enabled';

                                if (submitted_value.reduce_total_inventory == 'yes')
                                {
                                    reduce_total_inventory_yes = 'selected';
                                    reduce_total_inventory_no = '';
                                }

                                if (submitted_value.product_quantity_with_option)
                                {
                                    product_quantity_with_option = submitted_value.product_quantity_with_option;
                                }

                                if (submitted_value.option_price_situation == '+')
                                {
                                    option_price_situation_plus = 'selected';
                                    option_price_situation_minus = '';
                                }

                                if (submitted_value.option_price)
                                {
                                    option_price = submitted_value.option_price;
                                }

                                if (submitted_value.option_point_situation == '+')
                                {
                                    option_point_situation_plus = 'selected';
                                    option_point_situation_minus = '';
                                }

                                if (submitted_value.option_point)
                                {
                                    option_point = submitted_value.option_point;
                                }

                                if (submitted_value.option_weight_situation == '+')
                                {
                                    option_weight_situation_plus = 'selected';
                                    option_weight_situation_minus = '';
                                }

                                if (submitted_value.option_weight)
                                {
                                    option_weight = submitted_value.option_weight;
                                }

                                if (submitted_value.is_option_required == 'yes')
                                {
                                    is_option_required_yes = 'selected';
                                    is_option_required_no = '';
                                }

                                if (submitted_value.can_option_be_purchased_separately == 'yes')
                                {
                                    can_option_be_purchased_separately_yes = 'selected';
                                    can_option_be_purchased_separately_no = '';
                                }

                                upload_html_module = '<a target="_blank" href="<?=$option_file_link?>/'+submitted_value.file_name+'">'+submitted_value.orig_name+'</a></br><label><input type="checkbox" value="'+submitted_value.file_name+'" name="delete_options_file_'+cell_field.id+'"> <?=lang('delete_file')?></label>';
                            }

                            //این کد برای درج و اجرا فیلدهایی با نوع فیلد single_entry میباشد
                            temp_html_output +=
                                '<tr class="option_holder '+option_row+'">'+
                                '<td scope="row"><input type="checkbox" onchange="$(this).is(\':checked\') ? $(this).closest(\'.option_holder\').addClass(\'enabled\').removeClass(\'disabled\') : $(this).closest(\'.option_holder\').addClass(\'disabled\').removeClass(\'enabled\')" value="'+cell_field.id+'" name="list_options[]" '+option_is_checked+'></td>'+
                                '<td>'+cell_field.title+'</td>'+
                                '<td>'+
                                '<table class="table table-bordered influence_on_product">'+
                                '<thead class="title_table">'+
                                '<tr>'+
                                '<th scope="col" class="fittt"><?=lang('file')?></th>'+
                                '<th scope="col" class="fit"><?=lang('subtract_stock')?></th>'+
                                '<th scope="col" class="fit"><?=lang('product_quantity')?></th>'+
                                '<th scope="col" class="fit"><?=lang('price')?></th>'+
                                '<th scope="col" class="fit"><?=lang('point')?></th>'+
                                '<th scope="col" class="fit"><?=lang('weight')?></th>'+
                                '<th scope="col"><?=lang('required')?></th>'+
                                '<th scope="col" class="fit"><?=lang('separate_buy')?></th>'+
                                '</th>'+
                                '</tr>'+
                                '</thead>'+
                                '<tbody>'+
                                '<tr>'+
                                '<td scope="row">'+
                                upload_html_module+
                                '</select>'+
                                '</td>'+
                                '<td scope="row">'+
                                '<select name="reduce_total_inventory_'+cell_field.id+'" class="form-control">'+
                                '<option '+reduce_total_inventory_yes+' value="yes">بله</option>'+
                                '<option '+reduce_total_inventory_no+' value="no">خیر</option>'+
                                '</select>'+
                                '</td>'+
                                '<td scope="row">'+
                                '<input class="form-control" type="number" min="0" value="'+product_quantity_with_option+'" name="product_quantity_with_option_'+cell_field.id+'">'+
                                '</td>'+
                                '<td scope="row">'+
                                '<select name="option_price_situation_'+cell_field.id+'" class="form-control">'+
                                '<option '+option_price_situation_plus+'>+</option>'+
                                '<option '+option_price_situation_minus+'>-</option>'+
                                '</select>'+
                                '<input class="form-control" type="text" value="'+option_price+'" name="option_price_'+cell_field.id+'">'+
                                '</td>'+
                                '<td scope="row">'+
                                '<select name="option_point_situation_'+cell_field.id+'" class="form-control">'+
                                '<option '+option_point_situation_plus+'>+</option>'+
                                '<option '+option_point_situation_minus+'>-</option>'+
                                '</select>'+
                                '<input class="form-control" type="text" value="'+option_point+'" name="option_point_'+cell_field.id+'">'+
                                '</td>'+
                                '<td scope="row">'+
                                '<select name="option_weight_situation_'+cell_field.id+'" class="form-control">'+
                                '<option '+option_weight_situation_plus+'>+</option>'+
                                '<option '+option_weight_situation_minus+'>-</option>'+
                                '</select>'+
                                '<input class="form-control" type="text" value="'+option_weight+'" name="option_weight_'+cell_field.id+'">'+
                                '</td>'+
                                '<td scope="row">'+
                                '<select name="is_option_required_'+cell_field.id+'" class="form-control">'+
                                '<option '+is_option_required_yes+' value="yes">بله</option>'+
                                '<option '+is_option_required_no+' value="no">خیر</option>'+
                                '</select>'+
                                '</td>'+
                                '<td scope="row">'+
                                '<select name="can_option_be_purchased_separately_'+cell_field.id+'" class="form-control">'+
                                '<option '+can_option_be_purchased_separately_yes+' value="yes">بله</option>'+
                                '<option '+can_option_be_purchased_separately_no+' value="no">خیر</option>'+
                                '</select>'+
                                '</td>'+
                                '</tr>'+
                                '</tbody>'+
                                '</table>'+
                                '</td>'+
                                '</tr>';
                        }

                        /*#######################
                        * ## SELECT | CHECKBOX ##
                        * #######################*/
                        if (cell_field.type == 'select' || cell_field.type == 'checkbox')
                        {
                            //مقادیر آپشنهای داخلی را بگیر
                            var option_values = cell_field.insert_value;
                            //console.log("مقادیر insert_value: ", option_values);

                            //هر خط از آپشنها را بصورت یک آیتم در نظر میگیریم
                            //به همین دلیل، مقادیر insert_valueای که بالاتر گرفتیم را نسبت به هر کاراکتر (خط جدید) از هم جدا میکنیم
                            //و نتیجه را در آرایه ذخیره میکنیم
                            option_values = option_values.replace(/\r\n/g, "\n");
                            option_values = option_values.replace(/\r/g, "\n");
                            var option_items = option_values.split("\n");
                            //console.log('option_items_show', option_items);

                            var main_option_row = '';
                            var main_option_is_checked = '';

                            var option_tag = ""; //این متغیر قرار است هرکدام یک ردیف از جدول «زیر خصوصیت ها» را تشکیل دهند
                            option_items.forEach(function (option_item, index) {
                                option_item = option_item.split('|');
                                if (option_item.length == 1)
                                {
                                    option_item[1] = option_item[0];
                                }

                                //option_item[1] = option_item[1].replace(/ /g, "_");
                                //option_item[1] = option_item[1].replace(/[^ابپتثجچحخدذرزژسشصضطظعغفقکگلمنوهیءآاًهٔة\w]/gi, '_');

                                //چک میکنیم که مقداریکه قبلا سابمیت شده بوده است، تهی نباشد
                                var reduce_total_inventory_yes = '';
                                var reduce_total_inventory_no = 'selected';
                                var product_quantity_with_option = '0';
                                var option_price_situation_plus = '';
                                var option_price_situation_minus = 'selected';
                                var option_price = '0';
                                var option_point_situation_plus = '';
                                var option_point_situation_minus = 'selected';
                                var option_point = '0';
                                var option_weight_situation_plus = '';
                                var option_weight_situation_minus = 'selected';
                                var option_weight = '0';
                                var is_option_required_yes = '';
                                var is_option_required_no = 'selected';
                                var can_option_be_purchased_separately_yes = '';
                                var can_option_be_purchased_separately_no = 'selected';

                                var option_is_checked = ''
                                var option_row = 'disabled';

                                //console.log("submitted_value", submitted_value);
                                var current_option_row_value = '';

                                if(submitted_value != '' && submitted_value[option_item[1]] != undefined)
                                {
                                    current_option_row_value = submitted_value[option_item[1]];
                                    main_option_row = 'enabled';
                                    main_option_is_checked = 'checked';
                                }

                                if (current_option_row_value != "")
                                {
                                    option_is_checked = 'checked';
                                    option_row = 'enabled';

                                    if (current_option_row_value.reduce_total_inventory == 'yes')
                                    {
                                        reduce_total_inventory_yes = 'selected';
                                        reduce_total_inventory_no = '';
                                    }

                                    if (current_option_row_value.product_quantity_with_option)
                                    {
                                        product_quantity_with_option = current_option_row_value.product_quantity_with_option;
                                    }

                                    if (current_option_row_value.option_price_situation == '+')
                                    {
                                        option_price_situation_plus = 'selected';
                                        option_price_situation_minus = '';
                                    }

                                    if (current_option_row_value.option_price)
                                    {
                                        option_price = current_option_row_value.option_price;
                                    }

                                    if (current_option_row_value.option_point_situation == '+')
                                    {
                                        option_point_situation_plus = 'selected';
                                        option_point_situation_minus = '';
                                    }

                                    if (current_option_row_value.option_point)
                                    {
                                        option_point = current_option_row_value.option_point;
                                    }

                                    if (current_option_row_value.option_weight_situation == '+')
                                    {
                                        option_weight_situation_plus = 'selected';
                                        option_weight_situation_minus = '';
                                    }

                                    if (current_option_row_value.option_weight)
                                    {
                                        option_weight = current_option_row_value.option_weight;
                                    }

                                    if (current_option_row_value.is_option_required == 'yes')
                                    {
                                        is_option_required_yes = 'selected';
                                        is_option_required_no = '';
                                    }

                                    if (current_option_row_value.can_option_be_purchased_separately == 'yes')
                                    {
                                        can_option_be_purchased_separately_yes = 'selected';
                                        can_option_be_purchased_separately_no = '';
                                    }
                                }

                                option_tag += '<tr class="'+option_row+'">'+
                                    '<td class="selectable" scope="row"><input type="checkbox" value="'+option_item[1]+'" name="sub_list_options['+cell_field.id+'_'+option_item[1]+']" onchange="$(this).is(\':checked\') ? $(this).closest(\'tr\').addClass(\'enabled\').removeClass(\'disabled\') : $(this).closest(\'tr\').addClass(\'disabled\').removeClass(\'enabled\')" class="sub_list_options" '+option_is_checked+'></td>'+
                                    '<td scope="row" class="option_value">'+option_item[0]+'</td>'+
                                    '<td scope="row">'+
                                    '<select name="reduce_total_inventory_'+cell_field.id+'_'+option_item[1]+'" class="form-control">'+
                                    '<option '+reduce_total_inventory_yes+' value="yes">بله</option>'+
                                    '<option '+reduce_total_inventory_no+' value="no">خیر</option>'+
                                    '</select>'+
                                    '</td>'+
                                    '<td scope="row">'+
                                    '<input class="form-control" type="number" min="0" value="'+product_quantity_with_option+'" name="product_quantity_with_option_'+cell_field.id+'_'+option_item[1]+'">'+
                                    '</td>'+
                                    '<td scope="row">'+
                                    '<select name="option_price_situation_'+cell_field.id+'_'+option_item[1]+'" class="form-control">'+
                                    '<option '+option_price_situation_plus+'>+</option>'+
                                    '<option '+option_price_situation_minus+'>-</option>'+
                                    '</select>'+
                                    '<input class="form-control" type="text" value="'+option_price+'" name="option_price_'+cell_field.id+'_'+option_item[1]+'">'+
                                    '</td>'+
                                    '<td scope="row">'+
                                    '<select name="option_point_situation_'+cell_field.id+'_'+option_item[1]+'" class="form-control">'+
                                    '<option '+option_point_situation_plus+'>+</option>'+
                                    '<option '+option_point_situation_minus+'>-</option>'+
                                    '</select>'+
                                    '<input class="form-control" type="text" value="'+option_point+'" name="option_point_'+cell_field.id+'_'+option_item[1]+'">'+
                                    '</td>'+
                                    '<td scope="row">'+
                                    '<select name="option_weight_situation_'+cell_field.id+'_'+option_item[1]+'" class="form-control">'+
                                    '<option '+option_weight_situation_plus+'>+</option>'+
                                    '<option '+option_weight_situation_minus+'>-</option>'+
                                    '</select>'+
                                    '<input class="form-control" type="text" value="'+option_weight+'" name="option_weight_'+cell_field.id+'_'+option_item[1]+'">'+
                                    '</td>'+
                                    '<td scope="row">'+
                                    '<select name="is_option_required_'+cell_field.id+'_'+option_item[1]+'" class="form-control">'+
                                    '<option '+is_option_required_yes+' value="yes">بله</option>'+
                                    '<option '+is_option_required_no+' value="no">خیر</option>'+
                                    '</select>'+
                                    '</td>'+
                                    '<td scope="row">'+
                                    '<select name="can_option_be_purchased_separately_'+cell_field.id+'_'+option_item[1]+'" class="form-control">'+
                                    '<option '+can_option_be_purchased_separately_yes+' value="yes">بله</option>'+
                                    '<option '+can_option_be_purchased_separately_no+' value="no">خیر</option>'+
                                    '</select>'+
                                    '</td>'+
                                    '</tr>';
                            });

                            temp_html_output +=
                                '<tr class="option_holder '+main_option_row+'">'+
                                '<td scope="row"><input type="checkbox" onchange="$(this).is(\':checked\') ? $(this).closest(\'.option_holder\').addClass(\'enabled\').removeClass(\'disabled\') : $(this).closest(\'.option_holder\').addClass(\'disabled\').removeClass(\'enabled\')" value="'+cell_field.id+'" name="list_options[]" '+main_option_is_checked+'></td>'+
                                '<td>'+cell_field.title+'</td>'+
                                '<td>'+
                                '<table class="table table-bordered influence_on_product">'+
                                '<thead class="title_table">'+
                                '<tr>'+
                                '<th scope="col" class="fit"><input type="checkbox" onchange="toggle_sub_options_list(this);" class="toggle_sub_options_list"></th>'+
                                '<th scope="col"><?=lang('value')?></th>'+
                                '<th scope="col" class="fit"><?=lang('subtract_stock')?></th>'+
                                '<th scope="col" class="fit"><?=lang('product_quantity')?></th>'+
                                '<th scope="col" class="fit"><?=lang('price')?></th>'+
                                '<th scope="col" class="fit"><?=lang('point')?></th>'+
                                '<th scope="col" class="fit"><?=lang('weight')?></th>'+
                                '<th scope="col"><?=lang('required')?></th>'+
                                '<th scope="col" class="fit"><?=lang('separate_buy')?></th>'+
                                '</th>'+
                                '</tr>'+
                                '</thead>'+
                                '<tbody>'+
                                option_tag+
                                '</tbody>'+
                                '</table>'+
                                '</td>'+
                                '</tr>';
                        }
                    }

                    main_options_holder.innerHTML =
                    '<table class="table table-responsive table-light">'+
                    '<thead>'+
                    '<tr>'+
                    '<th scope="col" class="fit"><input type="checkbox" onclick="toggle_options_list(this)"></th>'+
                    '<th scope="col"><?=lang('options')?></th>'+
                    '<th scope="col"><input type="checkbox" onclick="toggle_all_sub_options_list(this)"><?=lang('check_all_options')?></th>'+
                    '</tr>'+
                    '</thead>'+
                    '<tbody>'+
                        temp_html_output +
                    '</tbody>'+
                    '</table>';
                },
                error: function(result){
                    console.log(result);
                    //var error = '<div class="alert alert-error">server is not available. please check your connection.</div>';
                    //$('#notification_bar').append(error);
                    alert ('server is not available. please check your connection.');
                }
            });
        }
    }

    if(window.addEventListener){
        window.addEventListener('DOMContentLoaded', function () {
            refresh_noe_product(document.getElementById("type_of_category"));
            refresh_fields(document.getElementById("category"), '<?=$html_output['item_data']['fields']?>');
            refresh_options(document.getElementById("category"), '<?=$html_output['item_data']['options']?>');
        })
    }else{
        window.attachEvent('onload', function () {
            refresh_noe_product(document.getElementById("type_of_category"));
            refresh_fields(document.getElementById("category"), '<?=$html_output['item_data']['fields']?>');
            refresh_options(document.getElementById("category"), '<?=$html_output['item_data']['options']?>');
        })
    }

    //console.log('fields', <?=$html_output['item_data']['fields']?>);

    //console.log('submited_values');


    /*function add_checkbox() {
     var element = document.getElementById("add_comment");
     console.log(element);
     if (element.classList) {
     element.classList.toggle("d-none");
     }
     else
     {
     // For IE9
     var classes = element.className.split(" ");
     var i = classes.indexOf("d-none");

     if (i >= 0)
     classes.splice(i, 1);
     else
     classes.push("d-none");
     element.className = classes.join(" ");
     }


     function add_checkbox_2() {

     document.getElementById('add_question').classList.remove('d-none');
     }





     } else {
     // For IE9
     var classes = element.className.split(" ");
     var i = classes.indexOf("mystyle");

     if (i >= 0)
     classes.splice(i, 1);
     else
     classes.push("mystyle");
     element.className = classes.join(" ");
     }*/
</script>
