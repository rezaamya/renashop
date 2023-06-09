<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="tool_bar" class="container-fluid">
	<div class="row justify-content-between">
		<div class="col-sm-auto">
			<button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
			<a href="<?php echo base_url("")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
		</div>
	</div>
</div>

<div class="container-fluid">
	<?PHP echo $html_output['sys_msg']; ?>
	<div class="row">
		<div class="col">
			<?PHP
			$attributes = array('class' => 'main_form', 'id' => 'main_form');
			echo form_open_multipart(base_url("setting/main_settings/".$html_output['item_data']['id']), $attributes);
			?>
			<div class="form-group row">
				<label for="store_name" class="col-sm-2 col-form-label"><?=lang('store_name')?></label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="store_name" name="store_name" placeholder="<?=lang('store_name')?>" value="<?php echo set_value('store_name',$html_output['item_data']['store_name']); ?>">
				</div>
			</div>

			<div class="form-group row">
				<label for="admin_email" class="col-sm-2 col-form-label"><?=lang('admin_email')?></label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="admin_email" name="admin_email" placeholder="<?=lang('admin_email')?>" value="<?php echo set_value('admin_email',$html_output['item_data']['admin_email']); ?>">
				</div>
			</div>

			<div class="form-group row">
				<label for="system_email" class="col-sm-2 col-form-label"><?=lang('system_email')?></label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="system_email" name="system_email" placeholder="<?=lang('system_email')?>" value="<?php echo set_value('system_email',$html_output['item_data']['system_email']); ?>">
				</div>
			</div>


            <!--
            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('price_show')?>
                    <a class="fix_popover" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_price_show_in_shop')?>">
                        <i class="text-danger fas fa-question-circle"></i>
                    </a>
                </legend>

                <div class="form-check-inline ml-3">
                    <input class="form-check-input" type="radio" name="price_show" id="price_show1" value="yes" checked >
                    <label class="form-check-label" for="price_show1">
                        <?=lang('yes')?>
                    </label>
                </div>
                <div class="form-check-inline ml-3">
                    <input class="form-check-input" type="radio" name="price_show" id="price_show2" value="no">
                    <label class="form-check-label" for="price_show2">
                        <?=lang('no')?>
                    </label>
                </div>
            </div>
            <div id="show_price_description" class="form-group row">
                <label class="col-sm-2 col-form-label"><?=lang('not_show_price_description')?>
                    <a class="fix_popover" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_not_show_price_description')?>">
                        <i class="text-danger fas fa-question-circle"></i>
                    </a>
                </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="not_show_price_description" placeholder="<?=lang('not_show_price_description')?>" value="">
                </div>
            </div>
            -->


			<div class="form-group row">
				<label for="img_width" class="col-sm-2 col-form-label"><?=lang('img_width')?></label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="img_width" name="img_width" placeholder="<?=lang('img_width')?>" value="<?php echo set_value('img_width',$html_output['item_data']['img_width']); ?>">
				</div>
			</div>

			<div class="form-group row">
				<label for="img_height" class="col-sm-2 col-form-label"><?=lang('img_height')?></label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="img_height" name="img_height" placeholder="<?=lang('img_height')?>" value="<?php echo set_value('img_height',$html_output['item_data']['img_height']); ?>">
				</div>
			</div>

			<div class="form-group row">
				<label for="thumb_width" class="col-sm-2 col-form-label"><?=lang('thumb_width')?></label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="thumb_width" name="thumb_width" placeholder="<?=lang('thumb_width')?>" value="<?php echo set_value('thumb_width',$html_output['item_data']['thumb_width']); ?>">
				</div>
			</div>

            <div class="form-group row">
				<label for="thumb_height" class="col-sm-2 col-form-label"><?=lang('thumb_height')?></label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="thumb_height" name="thumb_height" placeholder="<?=lang('thumb_height')?>" value="<?php echo set_value('thumb_height',$html_output['item_data']['thumb_height']); ?>">
				</div>
			</div>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('message_text_for_confirmation_code_email')?>
                    <a class="fix_popover" data-html="true" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_message_text_for_confirmation_code_email')?>">
                        <i class="text-danger fas fa-question-circle"></i>
                    </a>
                </div>
                <div class="col-sm-10">
                    <textarea name="message_text_for_confirmation_code_email" class="form-control" id="message_text_for_confirmation_code_email" rows="5"><?php echo set_value('message_text_for_confirmation_code_email',$html_output['item_data']['message_text_for_confirmation_code_email']); ?></textarea>
                </div>
            </div>


            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('subject_for_confirmation_code_email')?>
                    <a class="fix_popover" data-html="true" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_subject_for_confirmation_code_email')?>">
                        <i class="text-danger fas fa-question-circle"></i>
                    </a>
                </div>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="subject_for_confirmation_code_email" name="subject_for_confirmation_code_email" placeholder="<?=lang('subject_for_confirmation_code_email')?>" value="<?php echo set_value('subject_for_confirmation_code_email',$html_output['item_data']['subject_for_confirmation_code_email']); ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('message_text_for_confirmation_code_sms')?>
                    <a class="fix_popover" data-html="true" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_message_text_for_confirmation_code_sms')?>">
                        <i class="text-danger fas fa-question-circle"></i>
                    </a>
                </div>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="message_text_for_confirmation_code_sms" name="message_text_for_confirmation_code_sms" placeholder="<?=lang('message_text_for_confirmation_code_sms')?>" value="<?php echo set_value('message_text_for_confirmation_code_sms',$html_output['item_data']['message_text_for_confirmation_code_sms']); ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('subject_for_order_status_email')?>
                    <a class="fix_popover" data-html="true" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_subject_for_order_status_email')?>">
                        <i class="text-danger fas fa-question-circle"></i>
                    </a>
                </div>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="subject_for_order_status_email" name="subject_for_order_status_email" placeholder="<?=lang('subject_for_order_status_email')?>" value="<?php echo set_value('subject_for_order_status_email',$html_output['item_data']['subject_for_order_status_email']); ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('message_text_for_order_status_email')?>
                    <a class="fix_popover" data-html="true" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_message_text_for_order_status_email')?>">
                        <i class="text-danger fas fa-question-circle"></i>
                    </a></div>
                <div class="col-sm-10">
                    <textarea name="message_text_for_order_status_email" class="form-control" id="message_text_for_order_status_email" rows="5"><?php echo set_value('message_text_for_order_status_email',$html_output['item_data']['message_text_for_order_status_email']); ?></textarea>
                </div>
            </div>

			<!--<div class="form-group row">
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
            </div>-->
			<input type="hidden" id="task" name="task">
			</form>
		</div>
	</div>
</div>
