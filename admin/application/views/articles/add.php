<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("articles")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div> 

<div class="container-fluid">
    <?PHP echo $html_output['sys_msg']; ?>
    <div class="row">
        <div class="col"> 
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("articles/add/".$html_output['item_data']['id']), $attributes);
            ?>
            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label"><?=lang('title')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" name="title" placeholder="<?=lang('title')?>" value="<?php echo set_value('title',$html_output['item_data']['title']); ?>">
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
                <label for="select_1" class="col-sm-2 col-form-label">
                    <?=lang('category')?>
                </label>
                <div class="col-sm-10">
                    <select id="select_1" class="form-control" name="parent">
                        <?PHP echo $html_output['categories_list']; ?>
                    </select>
                </div>
            </div>

            <button type="button" onclick="add_file();" class="btn btn-success col-sm-2 btn-sm mb-1"><?=lang('add_file')?></button>
            <div id="upload_module_holder" class="form-group row"><?PHP echo $html_output['item_data']['files_view'] ?></div>

            <script>
                function add_file() {
                    document.getElementById('upload_module_holder').insertAdjacentHTML('afterbegin', '<div class="upload_module_holder input-group offset-sm-2 col-sm-10 mb-1"><div class="loading_holder"> <div class="content"> <div class="loader_spin"></div> <span><?=lang('please_wait')?></span> </div> </div> <div class="upload_module"> <div class="message_holder"></div> <input class="w3-border" type="file"> <div class="btn btn-success btn-sm" onclick="upload_file(this, \'article\');"><?=lang('upload')?> </div><button type="button" class="close ml-2 mt-1" onclick="delete_parent(this, \'upload_module_holder\');"> <span aria-hidden="true">×</span> </button> </div> <div class="uploaded_module"> <div class="message_holder"></div> <div class="btn btn-danger btn-sm remove_btn" onclick="remove_uploaded_file(this, \'article\')"><?=lang('delete_file')?></div> <input class="uploaded_file" name="files[]" type="hidden" value=""> <div class="btn btn-success btn-sm copy_to_clipboard"><?=lang('copy_file_address_in_memory')?></div> </div> </div>');
                }
            </script>

            <div class="form-group row">
                <label class="col-sm-2" for="Textarea1"><?=lang('intro')?></label>
                <div class="col-sm-10">
                    <textarea name="intro" class="form-control ckeditor" id="Textarea1" rows="3"><?php echo set_value('intro',$html_output['item_data']['intro']); ?></textarea>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2" for="Textarea1"><?=lang('full_content')?></label>
                <div class="col-sm-10">
                    <textarea name="full_content" class="form-control ckeditor" id="Textarea1" rows="3"><?php echo set_value('full_content',$html_output['item_data']['full_content']); ?></textarea>
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
            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('publish')?></legend>
                <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="publish" id="gridRadios1" value="yes" checked<?= set_value('publish', $html_output['item_data']['publish'])== 'yes' ? "" : ""; ?>>
                        <label class="form-check-label" for="gridRadios1">
                            <?=lang('yes')?>
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="publish" id="gridRadios2" value="no" <?= set_value('publish', $html_output['item_data']['publish']) == 'no' ? "checked" : ""; ?>>
                        <label class="form-check-label" for="gridRadios2">
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
