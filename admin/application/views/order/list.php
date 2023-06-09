<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?PHP
$attributes = array('class' => 'main_form', 'id' => 'main_form');
echo form_open_multipart(base_url("order/".$this->uri->segment(2)), $attributes);
?>
<div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto tool_bar">
            <button type="button" onclick="set_task('view', 'main_form', 'check_checklist')" class="btn btn-success btn-sm mb-1"><?=lang('view_order')?></button>
            <a class="btn btn-outline-secondary btn-sm mb-1" href="<?=base_url('order/export')?>" ><?=lang('export_to_excel')?></a>
            <button type="button" onclick="set_task('delete', 'main_form', 'check_checklist')" class="btn btn-danger btn-sm mb-1"><?=lang('delete')?></button>
        </div>
        <div class="col-sm-auto">
            <div class="form-inline">
                <input class="form-control form mr-sm-2 col-10 col-sm-auto" type="search" name="search" value="<?php echo set_value('search',$html_output['search']); ?>" placeholder="<?=lang('search')?>">
                <button class="btn btn-outline-success btn-sm my-2 my-sm-0 col-2 col-sm-auto" type="submit"><?=lang('do_search')?></button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
	<?PHP echo $html_output['sys_msg'];?>
    <div class="row">
        <div class="col">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th class="fit" scope="col"><input type="checkbox" onclick="toggle_checklist(this)"></th>
                    <th scope="col"><?=lang('order')?></th>
                    <th scope="col"><?=lang('status')?></th>
                    <th scope="col"><?=lang('total')?></th>
                    <th scope="col"><?=lang('date_added')?></th>
                    <th scope="col"><?=lang('date_modified')?></th>
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
