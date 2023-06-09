<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("setting/attribute_groups")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>

<div class="container-fluid">
	<?PHP echo $html_output['sys_msg']; ?>
    <div class="row">
        <div class="col">
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("setting/add_attribute_groups/".$html_output['item_data']['id']), $attributes);
            ?>
            <div class="form-group row">
                <label for="attribute_groups_name" class="col-sm-2 col-form-label"><?=lang('attribute_groups_name')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="attribute_groups_name" name="attribute_groups_name" value="<?php echo set_value('attribute_groups_name',$html_output['item_data']['attribute_groups_name']); ?>" placeholder="<?=lang('attribute_groups_name')?>">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    <?=lang('category')?>
                </label>
                <div class="col-sm-10">
                    <select class="form-control" name="product_category">
						<?PHP echo $html_output['categories_list']; ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('condition')?></legend>
                <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="gridRadios1" name="publish" value="yes" checked <?= set_value('publish', $html_output['item_data']['publish']) == 'yes' ? "checked" : ""; ?>>
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
