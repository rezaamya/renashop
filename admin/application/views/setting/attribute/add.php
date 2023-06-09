<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("setting/attribute")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>اخطار!</strong></br> شما باید فرم زیر را تکمیل کنید.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("setting/add_attribute"), $attributes);
            ?>
                <div class="form-group row">
                    <label for="attribute_name" class="col-sm-2 col-form-label"><?=lang('attribute_name')?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="attribute_name" placeholder="<?=lang('attribute_name')?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="attribute_groups" class="col-sm-2 col-form-label">
                        <?=lang('attribute_groups')?>
                    </label>
                    <div class="col-sm-10">
                        <select id="attribute_groups" class="form-control">
                            <option>کتاب</option>
                            <option>ویدئو</option>
                            <option>صوت</option>
                        </select>
                    </div>
                </div>
            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('condition')?></legend>
                <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="condition" id="gridRadios1" value="active" checked>
                        <label class="form-check-label" for="gridRadios1">
                            <?=lang('active')?>
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="condition" id="gridRadios2" value="inactive">
                        <label class="form-check-label" for="gridRadios2">
                            <?=lang('inactive')?>
                        </label>
                    </div>
                </div>
            </div>
                <input type="hidden" id="task" name="task">
            </form>
        </div>
    </div>
</div>
