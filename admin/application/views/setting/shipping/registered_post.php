<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("setting/shipping")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <?PHP echo $html_output['sys_msg']; ?>
    <div class="row">
        <div class="col">
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("setting/registered_post/".$html_output['item_data']['id']), $attributes);
            ?>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('post_insurance_amount')?></div>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="post_insurance_amount" placeholder="<?=lang('post_insurance_amount')?>" value="">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('post_packing_amount')?></div>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="post_packing_amount" placeholder="<?=lang('post_packing_amount')?>" value="">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('other_costs')?></div>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="other_costs" placeholder="<?=lang('other_costs')?>" value="">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('default_product_weight')?></div>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="default_product_weight" placeholder="<?=lang('default_product_weight')?>" value="">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    <?=lang('round_prices')?>
                </label>
                <div class="col-sm-10">
                    <select class="form-control" name="round_prices">
                        <option value="1">فعال</option>
                        <option value="2">غیرفعال</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('sort')?></div>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="sort" placeholder="<?=lang('sort')?>" value="">
                </div>
            </div>

            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('publish')?></legend>
                <div class="col-sm-10">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="publish" id="publish1" value="yes" checked>
                        <label class="form-check-label" for="publish1">
                            <?=lang('yes')?>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="publish" id="publish2" value="no">
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
