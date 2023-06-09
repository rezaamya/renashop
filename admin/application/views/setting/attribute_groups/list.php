<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?PHP
$attributes = array('class' => 'main_form', 'id' => 'main_form');
echo form_open_multipart(base_url("setting/attribute_groups/".$this->uri->segment(3)), $attributes);
?>
    <div id="tool_bar" class="container-fluid">
        <div class="row justify-content-between">
            <div class="col-sm-auto tool_bar">
                <a href="<?php echo base_url("setting/add_attribute_groups")?>"><span class="btn btn-success btn-sm mb-1"><?=lang('new')?></span></a>
                <button type="button" onclick="set_task('edit', 'main_form', 'check_checklist')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('edit')?></button>
                <button type="button" onclick="set_task('publish', 'main_form', 'check_checklist')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('publish')?></button>
                <button type="button" onclick="set_task('unpublish', 'main_form', 'check_checklist')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('unpublish')?></button>
                <button type="button" onclick="set_task('delete', 'main_form', 'check_checklist')" class="btn btn-danger btn-sm mb-1"><?=lang('delete')?></button>
            </div>
            <div class="col-sm-auto">
                <div class="col-sm-auto form-inline">
                    <div class="form-group">
                        <div class="input-group">
                            <select class="form-control form-control-sm col-sm-auto" name = "category">
								<?PHP echo $html_output['categories_list']; ?>
                            </select>
                        </div>
                    </div>
                    <input class="form-control col-10 col-sm-auto fix_popover" type="search" name="search" placeholder="<?=lang('search')?>" value="<?php echo set_value('search',$html_output['search']); ?>" placeholder="<?=lang('search')?>" >
                    <button class="btn btn-outline-success btn-sm col-2 col-sm-auto fix_popover" type="submit"><?=lang('do_search')?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
		<?PHP echo $sys_msg; ?>
        <div class="row">
            <div class="col">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="fit" scope="col"><input type="checkbox" onclick="toggle_checklist(this)"></th>
                        <th scope="col"><?=lang('attribute_groups_name')?></th>
                        <th class="fit" scope="col"><?=lang('category')?></th>
                        <th class="fit" scope="col"><?=lang('publish')?></th>
                    </tr>
                    </thead>
                    <tbody>
					<?php echo $html_output['main_table_rows']; ?>
                    </tbody>
                </table>
            </div>
        </div>
		<?PHP $this->load->view('template/bottom_toolbar'); ?>
    </div>
    <input type="hidden" id="task" name="task">
</form>
