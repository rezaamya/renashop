<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("setting")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>

<div class="container-fluid">
	<?PHP echo $html_output['sys_msg']; ?>
    <div class="row">
        <div class="col">
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("setting/add/".$html_output['item_data']['id']), $attributes);
            ?>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><?=lang('title')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="title" placeholder="<?=lang('title')?>" value="<?php echo set_value('title',$html_output['item_data']['title']); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="category" class="col-sm-2 col-form-label">
                    <?=lang('category')?>
                </label>
                <div class="col-sm-10">
                    <select id="category" class="form-control" onchange="refresh_attribute_categories(this);" name="category">
						<?PHP echo $html_output['categories_list']; ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="attribute_groups" class="col-sm-2 col-form-label">
                    <?=lang('attribute_groups')?>
                </label>
                <div class="col-sm-10">
                    <select id="attribute_groups" class="form-control"  name="attribute_groups">
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><?=lang('sort')?>
                    <a tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-content="<?=lang('f1_add_setting_sort')?>"><i class="text-danger fas fa-question-circle"></i></a>
                </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="sort" placeholder="<?=lang('please_enter_numeric')?>" value="<?php echo set_value('sort',$html_output['item_data']['sort']); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="type" class="col-sm-2 col-form-label">
                    <?=lang('type')?>
                </label>
                <div class="col-sm-10">
                    <select id="type" name="type" class="form-control" onchange="show_hide(this);">

                        <option value="single_case" <?= set_value('type', $html_output['item_data']['type']) == 'single_case' ? "selected" : ""; ?>><?=lang('single_case')?></option>

                        <option value="multiple_case" <?= set_value('type', $html_output['item_data']['type']) == 'multiple_case' ? "selected" : ""; ?>><?=lang('multiple_case')?></option>

                        <option value="textarea" <?= set_value('type', $html_output['item_data']['type']) == 'textarea' ? "selected" : ""; ?>><?=lang('paragraph')?></option>

                        <option value="select" <?= set_value('type', $html_output['item_data']['type']) == 'select' ? "selected" : ""; ?>><?=lang('select')?></option>

                        <option value="checkbox" <?= set_value('type', $html_output['item_data']['type']) == 'checkbox' ? "selected" : ""; ?>><?=lang('checkbox')?></option>
                        <option value="file" <?= set_value('type', $html_output['item_data']['type']) == 'file' ? "selected" : ""; ?>><?=lang('file')?></option>

                    </select>
                </div>
            </div>
            <div id="insert_value" class="form-group row d-none">
                <label class="col-sm-2 col-form-label"><?=lang('insert_value')?>
                    <a tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-content="<?=lang('f1_add_setting_insert_value')?>"><i class="text-danger fas fa-question-circle"></i></a>
                </label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="insert_value" rows="5"><?php echo set_value('insert_value',$html_output['item_data']['insert_value']); ?></textarea>
                </div>
            </div>
            <div id="check_box" class="form-check offset-2 d-none">
                <div>
                    <input class="form-check-input" id="show_list" name="show_list" type="checkbox" value="yes" <?= set_value('show_list', $html_output['item_data']['show_list']) == 'yes' ? "checked" : ""; ?>>
                    <label class="form-check-label" for="show_list"><?=lang('show_list')?></label>
                    <a tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-content="<?=lang('f1_add_setting_show_list')?>"><i class="text-danger fas fa-question-circle"></i></a>
                </div>
                <div>
                    <input class="form-check-input" id="show_not_check" name="show_not_check" type="checkbox" value="yes" <?= set_value('show_not_check', $html_output['item_data']['show_not_check']) == 'yes' ? "checked" : ""; ?>>
                    <label class="form-check-label" for="show_not_check" ><?=lang('show_not_check')?></label>
                    <a tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-content="<?=lang('f1_add_setting_show_not_check')?>"><i class="text-danger fas fa-question-circle"></i></a>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    <?=lang('location')?>
                    <a tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-content="<?=lang('f1_add_setting_location')?>"><i class="text-danger fas fa-question-circle"></i></a>
                </label>
                <div class="col-sm-10" >
                    <select class="form-control" name="location">
						<?PHP echo $html_output['location_list']; ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('required')?>
                    <a tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-content="<?=lang('f1_add_setting_required')?>"><i class="text-danger fas fa-question-circle"></i></a>
                </legend>
                <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="required" id="required1" value="yes" checked <?= set_value('required', $html_output['item_data']['required'])== 'yes' ? "" : ""; ?>>
                        <label class="form-check-label" for="required1"><?=lang('yes')?></label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="required" id="required2" value="no" <?= set_value('required', $html_output['item_data']['required']) == 'no' ? "checked" : ""; ?>>
                        <label class="form-check-label" for="required2">
                            <?=lang('no')?>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('publish')?></legend>
                <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="publish" id="publish1" value="yes" checked <?= set_value('publish', $html_output['item_data']['publish'])== 'yes' ? "" : ""; ?>>
                        <label class="form-check-label" for="publish1">
                            <?=lang('yes')?>
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="publish" id="publish2" value="no" <?= set_value('publish', $html_output['item_data']['publish']) == 'no' ? "checked" : ""; ?>>
                        <label class="form-check-label" for="publish2">
                            <?=lang('no')?>
                        </label>
                    </div>
                </div>
            </div>
            <div class="row offset-2">
                <div class="form-check col-md-3">
                    <input class="form-check-input" id="special_characteristic" name="special_characteristic" type="checkbox" value="yes" <?= set_value('special_characteristic', $html_output['item_data']['special_characteristic']) == 'yes' ? "checked" : ""; ?>>
                    <label class="form-check-label" for="special_characteristic"><?=lang('special_characteristic')?></label>
                    <a tabindex="0" role="button" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_add_setting_special_characteristic')?>"><i class="text-danger fas fa-question-circle"></i></a>
                </div>
                <div class="form-check col-md-3">
                    <input class="form-check-input" id="comparability" name="comparability" type="checkbox" value="yes" <?= set_value('comparability', $html_output['item_data']['comparability']) == 'yes' ? "checked" : ""; ?>>
                    <label class="form-check-label" for="comparability"><?=lang('comparability')?></label>
                    <a tabindex="0" role="button" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_add_setting_comparability')?>"><i class="text-danger fas fa-question-circle"></i></a>
                </div>
                <div class="form-check col-md-3">
                    <input class="form-check-input" id="linkable" name="linkable" type="checkbox" value="yes" <?= set_value('linkable', $html_output['item_data']['linkable']) == 'yes' ? "checked" : ""; ?>>
                    <label class="form-check-label" for="linkable" ><?=lang('linkable')?></label>
                    <a tabindex="0" role="button" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_add_setting_linkable')?>"><i class="text-danger fas fa-question-circle"></i></a>
                </div>
                <div class="form-check col-md-3">
                    <input class="form-check-input" id="searchable" name="searchable" type="checkbox" value="yes" <?= set_value('searchable', $html_output['item_data']['searchable']) == 'yes' ? "checked" : ""; ?>>
                    <label class="form-check-label" for="searchable" ><?=lang('searchable')?></label>
                    <a tabindex="0" role="button" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_add_setting_searchable')?>"><i class="text-danger fas fa-question-circle"></i></a>
                </div>

            </div>
            <input type="hidden" id="task" name="task">
            </form>
        </div>
    </div>
</div>

<script>
    show_hide(document.getElementById('type'));
    function show_hide(oni_ke_change_shode) {
        if (oni_ke_change_shode.value=="select"){
            document.getElementById('insert_value').classList.remove('d-none');
            document.getElementById('check_box').classList.add('d-none');
        }


        else if (oni_ke_change_shode.value=="checkbox"){
            document.getElementById('insert_value').classList.remove('d-none');
            document.getElementById('check_box').classList.remove('d-none');
        }
        else {
            document.getElementById('insert_value').classList.add('d-none');
            document.getElementById('check_box').classList.add('d-none');
        }

    }
    window.addEventListener('load', function () {
        refresh_attribute_categories(document.getElementById("category"), '<?=isset($html_output['attribute_groups']) ? $html_output['attribute_groups'] : ""?>');
    });
    function refresh_attribute_categories (oni_ke_change_shode, selected_item_id) {
        document.getElementById('attribute_groups').innerHTML = '';
        document.getElementById('attribute_groups').disabled = true;

        $.ajax({
            method: "POST",
            url: '<?=base_url('api/index')?>',//'http://localhost/keshavarz/api/index',
            data: {"req":"get_attribute_groups_for_special_product_category", "category_id":oni_ke_change_shode.value},
            success: function(result){
                var json = JSON.parse(result);
                console.log("response:", json);

                var option_list = '<option value=""><?=lang('please_select')?></option>';

                $.each(json, function( index, value ) {
                    var selected = '';
                    if (selected_item_id == index)
                    {
                        //this option is selected
                        selected = 'selected="selected"';
                    }
                    option_list = option_list + '<option '+selected+' value="'+index+'">'+value+'</option>';
                    console.log( index , value);
                });

                document.getElementById('attribute_groups').innerHTML = option_list;
                document.getElementById('attribute_groups').disabled = false;
            },
            error: function(result){
                //var error = '<div class="alert alert-error">server is not available. please check your connection.</div>';
                //$('#notification_bar').append(error);
                alert ('server is not available. please check your connection.');

            }
        });
    }
</script>
