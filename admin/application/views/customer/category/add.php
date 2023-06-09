<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("customer/categories")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <?PHP echo $html_output['sys_msg']; ?>
    <div class="row">
        <div class="col">

            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("customer/add_category/".$html_output['item_data']['id']), $attributes);
            ?>
            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label"><?=lang('title')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="title" id="title" placeholder="<?=lang('title')?>" value="<?php echo set_value('title',$html_output['item_data']['title']); ?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="select_1" class="col-sm-2 col-form-label">
                    <?=lang('parent')?>
                </label>
                <div class="col-sm-10">
                    <select id="select_1" class="form-control" name="parent">
                        <?PHP echo $html_output['categories_list']; ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2" for="Textarea1"><?=lang('description')?></label>
                <div class="col-sm-10">
                    <textarea name="description" class="form-control ckeditor" id="Textarea1" rows="3"><?php echo set_value('description',$html_output['item_data']['description']); ?></textarea>
                </div>
            </div>

            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('publish')?></legend>
                <div class="col-sm-10">
                    <div class="form-check-inline">
                        <input class="form-check-input" type="radio" name="publish" id="publish" value="yes" checked <?= set_value('publish', $html_output['item_data']['publish']) == 'yes' ? "checked" : ""; ?>>
                        <label class="form-check-label" for="publish">
                            <?=lang('yes')?>
                        </label>
                    </div>
                    <div class="form-check-inline">
                        <input class="form-check-input" type="radio" name="publish" id="publish_2" value="no" <?= set_value('publish', $html_output['item_data']['publish']) == 'no' ? "checked" : ""; ?>>
                        <label class="form-check-label" for="publish_2">
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
