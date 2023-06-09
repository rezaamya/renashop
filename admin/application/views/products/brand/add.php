<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div id="tool_bar" class="container-fluid">
	<div class="row justify-content-between">
		<div class="col-sm-auto">
			<button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
			<button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
			<button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
			<a href="<?php echo base_url("products/brands")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
		</div>
	</div>
</div>

<div class="container-fluid">
	<?PHP echo $html_output['sys_msg']; ?>
	<div class="row">
		<div class="col">
			<?PHP
			$attributes = array('class' => 'main_form', 'id' => 'main_form');
			echo form_open_multipart(base_url("products/add_brand/".$html_output['item_data']['id']), $attributes);
			?>
			<div class="form-group row">
				<label for="title" class="col-sm-2 col-form-label"><?=lang('title')?></label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="title" name="title" placeholder="<?=lang('title')?>" value="<?php echo set_value('title',$html_output['item_data']['title']); ?>">
				</div>
			</div>
			<div class="form-group row">
				<div class="col-sm-2 col-form-label"><?=lang('title_alias_url')?>
					<a class="fix_popover" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_add_categories_url')?>">
						<i class="text-danger fas fa-question-circle"></i>
					</a>
				</div>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="title_alias_url" placeholder="<?=lang('title_alias_url')?>" value="<?php echo set_value('title_alias_url',$html_output['item_data']['title_alias_url']); ?>">
				</div>
			</div>

			<div class="form-group row">
				<label class="col-sm-2 col-form-label">
					<?=lang('category')?>
				</label>
				<div class="col-sm-10">
					<select class="form-control" name="parent">
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
				<div class="col-sm-2 col-form-label"><?=lang('meta_tag_title')?>
					<a class="fix_popover" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_add_categories_meta_tag_title')?>">
						<i class="text-danger fas fa-question-circle"></i>
					</a>
				</div>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="meta_tag_title" placeholder="<?=lang('meta_tag_title')?>" value="<?php echo set_value('meta_tag_title',$html_output['item_data']['meta_tag_title']); ?>">
				</div>
			</div>

			<div class="form-group row">
				<div class="col-sm-2 col-form-label"><?=lang('meta_tag_keywords')?>
					<a class="fix_popover" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_add_categories_meta_tag_keywords')?>">
						<i class="text-danger fas fa-question-circle"></i>
					</a>
				</div>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="meta_tag_keywords" placeholder="<?=lang('meta_tag_keywords')?>" value="<?php echo set_value('meta_tag_keywords',$html_output['item_data']['meta_tag_keywords']); ?>">
				</div>
			</div>

			<div class="form-group row">
				<div class="col-sm-2 col-form-label"><?=lang('meta_tag_description')?>
					<a class="fix_popover" tabindex="0" data-container="body" data-toggle="popover" data-placement="left" data-trigger="focus" data-content="<?=lang('f1_add_categories_meta_tag_description')?>">
						<i class="text-danger fas fa-question-circle"></i>
					</a>
				</div>
				<div class="col-sm-10">
					<textarea name="meta_tag_description" class="form-control" id="meta_tag_description" rows="3"><?php echo set_value('meta_tag_description',$html_output['item_data']['meta_tag_description']); ?></textarea>
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
