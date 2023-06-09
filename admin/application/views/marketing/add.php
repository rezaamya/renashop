<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("marketing")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>

<div class="container-fluid">
	<?PHP echo $html_output['sys_msg']; ?>
    <div class="row">
        <div class="col">
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("marketing/add/".$html_output['item_data']['id']), $attributes);
            ?>
            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label"><?=lang('campaign_name')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" name="campaign_name" placeholder="<?=lang('campaign_name')?>"  value="<?php echo set_value('campaign_name',$html_output['item_data']['campaign_name']); ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('campaign_description')?></div>
                <div class="col-sm-10">
                    <textarea name="campaign_description" class="form-control" id="campaign_description" rows="3"><?php echo set_value('campaign_description',$html_output['item_data']['campaign_description']); ?></textarea>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('tracking_code')?>
                    <a class="fix_popover" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_add_campaign_tracking_code')?>">
                        <i class="text-danger fas fa-question-circle"></i>
                    </a>
                </div>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="tracking_code" oninput="set_example(this);" placeholder="<?=lang('tracking_code')?>" value="<?php echo set_value('tracking_code',$html_output['item_data']['tracking_code']); ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('example')?>
                    <a class="fix_popover" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_add_campaign_example')?>">
                        <i class="text-danger fas fa-question-circle"></i>
                    </a>
                </div>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="example" id="example" readonly value="<?php echo set_value('example',$html_output['item_data']['example']); ?>">
                </div>
                <script>
                    function set_example (changed_input_obj)
                    {
                        var obj = $(changed_input_obj);
                        //http://localhost/keshavarz/?tr=123
                        $('#example').val('<?php echo base_url()?>?tr='+obj.val());
                        //obj.closest('.address_holder').find(".address_title").html(obj.val());
                    }
                </script>
            </div>

            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('publish')?></legend>
                <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="publish" id="publish1" value="yes" checked <?= set_value('publish', $html_output['item_data']['publish']) == 'yes' ? "checked" : ""; ?>>
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
            <input type="hidden" id="task" name="task">
            </form>
        </div>
    </div>
</div>
