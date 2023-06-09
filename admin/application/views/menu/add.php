<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("menu")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>
<div class="container-fluid">
    <?PHP echo $html_output['sys_msg']; ?>
	<div class="row">
        <div class="col">
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("menu/add/".$html_output['item_data']['id']), $attributes);
            ?>
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
                <label class="col-sm-2 col-form-label"><?=lang('parent')?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="parent_id">
						<?PHP echo $html_output['parent_list']; ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><?=lang('menu_category')?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="category_id">
						<?PHP echo $html_output['menu_category_list']; ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><?=lang('type')?></label>
                <div class="col-sm-10">
                    <select id="menu_type" onchange="show_target(this);" class="form-control" name="type">
						<?PHP echo $html_output['type_list']; ?>
                    </select>
                </div>
            </div>

            <div class="form-group row single_page target_holder d-none d-none">
                <label class="col-sm-2 col-form-label"><?=lang('list_pages')?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="list_pages">
						<?PHP echo $html_output['pages_list']; ?>
                    </select>
                </div>
            </div>

            <div class="form-group row single_category target_holder d-none">
                <label class="col-sm-2 col-form-label"><?=lang('list_categories')?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="list_categories">
						<?PHP echo $html_output['categories_list']; ?>
                    </select>
                </div>
            </div>

            <div class="form-group row single_product target_holder d-none">
                <label class="col-sm-2 col-form-label"><?=lang('list_products')?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="list_products">
						<?PHP echo $html_output['products_list']; ?>
                    </select>
                </div>
            </div>

            <div class="form-group row single_product_category target_holder d-none">
                <label class="col-sm-2 col-form-label"><?=lang('products_categories')?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="products_categories">
						<?PHP echo $html_output['products_categories_list']; ?>
                    </select>
                </div>
            </div>

            <div class="form-group row link target_holder d-none">
                <label class="col-sm-2 col-form-label"><?=lang('link_address')?></label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="link_address" name="link_address" value="<?php echo set_value('link_address',$html_output['item_data']['link_address']); ?>" placeholder="<?=lang('please_insert_link_address')?>">
                </div>
                <div class="col-sm-4">
                    <select class="form-control" name="page_open_type">

                        <option value="open_in_new_window" <?= set_value('page_open_type', $html_output['item_data']['page_open_type']) == 'open_in_new_window' ? "selected" : ""; ?>><?=lang('open_in_new_window')?></option>

                        <option value="open_in_this_window" <?= set_value('page_open_type', $html_output['item_data']['page_open_type']) == 'open_in_this_window' ? "selected" : ""; ?>><?=lang('open_in_this_window')?></option>

                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><?=lang('access_level')?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="access">

                        <option value="all" <?= set_value('access', $html_output['item_data']['access']) == 'all' ? "selected" : ""; ?>>همه</option>

                        <option value="guest" <?= set_value('access', $html_output['item_data']['access']) == 'guest' ? "selected" : ""; ?>>مهمان</option>

                        <option value="registered" <?= set_value('access', $html_output['item_data']['access']) == 'registered' ? "selected" : ""; ?>>عضو</option>

                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="class" class="col-sm-2 col-form-label"><?=lang('sort')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="sort" name="sort" value="<?php echo set_value('sort',$html_output['item_data']['sort']); ?>" placeholder="<?=lang('sort')?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="class" class="col-sm-2 col-form-label"><?=lang('add_class')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="class" name="class" value="<?php echo set_value('class',$html_output['item_data']['class']); ?>" placeholder="<?=lang('class')?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="icon" class="col-sm-2 col-form-label"><?=lang('add_icon')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="icon" name="icon" value="<?php echo set_value('icon',$html_output['item_data']['icon']); ?>" placeholder="<?=lang('icon')?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><?=lang('position_icon')?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="icon_position">

                        <option value="left" <?= set_value('icon_position', $html_output['item_data']['icon_position']) == 'left' ? "selected" : ""; ?>>چپ</option>

                        <option value="right" <?= set_value('icon_position', $html_output['item_data']['icon_position']) == 'right' ? "selected" : ""; ?>>راست</option>

                    </select>
                </div>
            </div>


            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('meta_tag_title')?>
                    <a class="fix_popover" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_add_categories_meta_tag_keywords')?>">
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

<script>
    function show_target(type_list) {
        var all_target_holders = document.getElementsByClassName("target_holder");
        [].forEach.call(all_target_holders, function (node_target_holder) {
            node_target_holder.classList.add("d-none");
        });

        var type_value = type_list.value;
        //var target_holder = document.getElementsByClassName(type_value+"_target_holder")[0];
        var target_holder = document.querySelector('.'+type_value+'.target_holder');
        if (target_holder != null){
            target_holder.classList.remove("d-none");
        }
    }

    window.addEventListener('load', function () {
        show_target(document.getElementById("menu_type"));
    });
</script>

