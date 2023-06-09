<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("customer/comments_list")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>

<div class="container-fluid">
	<?PHP echo $html_output['sys_msg']; ?>
    <div class="row">
        <div class="col">
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("customer/edit_comment/".$html_output['item_data']['id']), $attributes);
            ?>
                <div class="form-group row">
                    <div class="col-sm-2 col-form-label"><?=lang('first_name')?></div>
                    <div class="col-sm-10">
                        <input name="first_name" class="form-control" id="first_name" value="<?php echo set_value('first_name',$html_output['item_data']['first_name']); ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label"><?=lang('email')?></div>
                    <div class="col-sm-10">
                        <input name="email" class="form-control" id="email" value="<?php echo set_value('email',$html_output['item_data']['email']); ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-2 col-form-label"><?=lang('full_comment')?></div>
                    <div class="col-sm-10">
                        <textarea name="full_comment" class="form-control" id="full_comment" rows="5"><?php echo set_value('full_comment',$html_output['item_data']['full_comment']); ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2" for="Textarea1"><?=lang('answer')?></label>
                    <div class="col-sm-10">
                        <textarea name="answer" class="form-control ckeditor" id="Textarea1" rows="5"><?php echo set_value('answer',$html_output['item_data']['answer']); ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <legend class="col-form-label col-sm-2 pt-0"><?=lang('Do you want to be marked?')?></legend>
                    <div class="col-sm-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="marked" id="marked" value="yes" checked <?= set_value('marked', $html_output['item_data']['marked'])== 'yes' ? "" : ""; ?>>
                            <label class="form-check-label" for="marked">
                                <?=lang('yes')?>
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="marked" id="marked2" value="no" <?= set_value('marked', $html_output['item_data']['marked']) == 'no' ? "checked" : ""; ?>>
                            <label class="form-check-label" for="marked2">
                                <?=lang('no')?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <legend class="col-form-label col-sm-2 pt-0"><?=lang('publish')?></legend>
                    <div class="col-sm-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="publish" id="publish" value="yes" checked <?= set_value('publish', $html_output['item_data']['publish'])== 'yes' ? "" : ""; ?>>
                            <label class="form-check-label" for="publish">
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
                <input type="hidden" id="task" name="task">
            </form>
        </div>
    </div>
</div>
