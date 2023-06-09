<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("setting/status_order")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>

<div class="container-fluid">
	<?PHP echo $html_output['sys_msg']; ?>
    <div class="row">
        <div class="col">
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("setting/add_status_order/".$html_output['item_data']['id']), $attributes);
            ?>
                <div class="form-group row">
                    <label for="status_order" class="col-sm-2 col-form-label"><?=lang('status_order')?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="status_order" name="status_order" placeholder="<?=lang('status_order')?>" value="<?php echo set_value('status_order',$html_output['item_data']['status_order']); ?>">
                    </div>
                </div>
            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('publish')?></legend>
                <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="publish" name="publish" value="yes" checked<?= set_value('publish', $html_output['item_data']['publish'])== 'yes' ? "" : ""; ?>>
                        <label for="publish" class="form-check-label">
                            <?=lang('yes')?>
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="publish_2" name="publish" value="no" <?= set_value('publish', $html_output['item_data']['publish']) == 'no' ? "checked" : ""; ?>>
                        <label for="publish_2" class="form-check-label">
                            <?=lang('no')?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('order_will_be_finished_in_this_status')?></legend>
                <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="be_finished" name="order_will_be_finished_in_this_status" value="yes" checked<?= set_value('order_will_be_finished_in_this_status', $html_output['item_data']['order_will_be_finished_in_this_status'])== 'yes' ? "" : ""; ?>>
                        <label for="be_finished" class="form-check-label">
                            <?=lang('yes')?>
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="be_finished_2" name="order_will_be_finished_in_this_status" value="no" <?= set_value('order_will_be_finished_in_this_status', $html_output['item_data']['order_will_be_finished_in_this_status']) == 'no' ? "checked" : ""; ?>>
                        <label for="be_finished_2" class="form-check-label">
                            <?=lang('no')?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('can_customer_cancel_the_order_in_this_situation')?></legend>
                <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="cancel_the_order" name="can_customer_cancel_the_order_in_this_situation" value="yes" checked <?= set_value('can_customer_cancel_the_order_in_this_situation', $html_output['item_data']['can_customer_cancel_the_order_in_this_situation'])== 'yes' ? "" : ""; ?>>
                        <label for="cancel_the_order" class="form-check-label">
                            <?=lang('yes')?>
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="cancel_the_order_2" name="can_customer_cancel_the_order_in_this_situation" value="no" <?= set_value('can_customer_cancel_the_order_in_this_situation', $html_output['item_data']['can_customer_cancel_the_order_in_this_situation']) == 'no' ? "checked" : ""; ?>>
                        <label for="cancel_the_order_2" class="form-check-label">
                            <?=lang('no')?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('are_virtual_products_accessible_by_customers_in_this_status')?></legend>
                <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="virtual_products_accessible" name="are_virtual_products_accessible_by_customers_in_this_status" value="yes" checked<?= set_value('are_virtual_products_accessible_by_customers_in_this_status', $html_output['item_data']['are_virtual_products_accessible_by_customers_in_this_status'])== 'yes' ? "" : ""; ?>>
                        <label for="virtual_products_accessible" class="form-check-label">
                            <?=lang('yes')?>
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="virtual_products_accessible_2" name="are_virtual_products_accessible_by_customers_in_this_status" value="no" <?= set_value('are_virtual_products_accessible_by_customers_in_this_status', $html_output['item_data']['are_virtual_products_accessible_by_customers_in_this_status']) == 'no' ? "checked" : ""; ?>>
                        <label for="virtual_products_accessible_2" class="form-check-label">
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
