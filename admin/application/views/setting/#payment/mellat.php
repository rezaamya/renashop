<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="tool_bar" class="container-fluid">
	<div class="row justify-content-between">
		<div class="col-sm-auto">
			<button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
			<button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
			<a href="<?php echo base_url("setting/payment")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
		</div>
	</div>
</div>

<div class="container-fluid">
	<?PHP echo $html_output['sys_msg']; ?>
	<div class="row">
		<div class="col">
			<?PHP
			$attributes = array('class' => 'main_form', 'id' => 'main_form');
			echo form_open_multipart(base_url("setting/mellat_payment/".$html_output['item_data']['id']), $attributes);
			?>
			<div class="form-group row">
				<label for="title" class="col-sm-2 col-form-label"><?=lang('title')?></label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="title" name="title" placeholder="<?=lang('title')?>" value="<?php echo set_value('title',$html_output['item_data']['title']); ?>">
				</div>
			</div>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('businessman_code')?></div>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="businessman_code" placeholder="<?=lang('businessman_code')?>" value="<?php echo set_value('businessman_code',$html_output['item_data']['businessman_code']); ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('terminal_code')?></div>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="terminal_code" placeholder="<?=lang('terminal_code')?>" value="<?php echo set_value('terminal_code',$html_output['item_data']['terminal_code']); ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('private_key')?></div>
                <div class="col-sm-10">
                    <textarea name="private_key" class="form-control" id="private_key" rows="3"></textarea>
                </div>
            </div>

			<div class="form-group row">
				<label class="col-sm-2 col-form-label">
					<?=lang('status_order')?>
				</label>
				<div class="col-sm-10">
					<select class="form-control" name="status_order">
						<option value="1">در حال بررسی</option>
                        <option value="2">در حال انجام</option>
                        <option value="3">اتمام سفارش</option>
					</select>
				</div>
			</div>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label"><?=lang('sort')?></div>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="sort" placeholder="<?=lang('sort')?>" value="<?php echo set_value('sort',$html_output['item_data']['sort']); ?>">
                </div>
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
