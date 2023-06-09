<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("users")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>
<div class="container-fluid"> 
    <?PHP echo $html_output['sys_msg']; ?>
</div>
<div class="container-fluid">
    <?PHP
    $attributes = array('class' => 'main_form', 'id' => 'main_form');
    echo form_open_multipart(base_url("users/add/".$html_output['item_data']['id']), $attributes);
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
                        <label for="username" class="col-sm-2 col-form-label"><?=lang('username')?></label>
                        <div class="col-sm-10">
                            <input name="username" type="text" class="form-control" id="username" placeholder="<?=lang('username')?>" value="<?php echo set_value('username',$html_output['item_data']['username']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="title" class="col-sm-2 col-form-label"><?=lang('email')?></label>
                        <div class="col-sm-10">
                            <input name="email" type="email" class="form-control" id="email" placeholder="<?=lang('email')?>" value="<?php echo set_value('email',$html_output['item_data']['email']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="status" class="col-sm-2 col-form-label"><?=lang('status')?></label>
                        <div class="col-sm-10">
                            <select name="status" id="status" class="form-control">

                                <option value="accepted" <?= set_value('status', $html_output['item_data']['status']) == 'accepted' ? "selected" : ""; ?>>تایید شده</option>

                                <option value="need_to_change_password" <?= set_value('status', $html_output['item_data']['status']) == 'need_to_change_password' ? "selected" : ""; ?>>نیاز به تغییر رمز عبور</option>

                                <option value="need_to_confirm_email" <?= set_value('status', $html_output['item_data']['status']) == 'need_to_confirm_email' ? "selected" : ""; ?>>نیاز به تایید ایمیل</option>

                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-sm-2 col-form-label"><?=lang('password')?></label>
                        <div class="col-sm-10">
                            <input name="password" type="password" class="form-control" id="password" placeholder="<?=lang('password')?>" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-sm-2 col-form-label"><?=lang('confirm_password')?></label>
                        <div class="col-sm-10">
                            <input name="confirm_password" type="password" class="form-control" id="confirm_password" placeholder="<?=lang('confirm_password')?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <legend class="col-form-label col-sm-2 pt-0"><?=lang('is_block')?></legend>
                        <div class="col-sm-10">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_block" id="gridRadios1" value="yes" checked <?= set_value('is_block', $html_output['item_data']['is_block']) == 'yes' ? "checked" : ""; ?>>
                                <label class="form-check-label" for="gridRadios1">
                                    <?=lang('yes')?>
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_block" id="gridRadios2" value="no" <?= set_value('is_block', $html_output['item_data']['is_block']) == 'no' ? "checked" : ""; ?>>
                                <label class="form-check-label" for="gridRadios2">
                                    <?=lang('no')?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <legend class="col-form-label col-sm-2 pt-0"><?=lang('send_email?')?></legend>
                        <div class="col-sm-10">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="send_email" id="gridRadios1" value="yes" checked <?= set_value('send_email', $html_output['item_data']['send_email']) == 'yes' ? "checked" : ""; ?>>
                                <label class="form-check-label" for="gridRadios1">
                                    <?=lang('yes')?>
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="send_email" id="gridRadios2" value="no" <?= set_value('send_email', $html_output['item_data']['send_email']) == 'no' ? "checked" : ""; ?>>
                                <label class="form-check-label" for="gridRadios2">
                                    <?=lang('no')?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <input type="hidden" id="task" name="task">
    </form>
</div>
