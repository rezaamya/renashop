<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?PHP
$attributes = array('class' => 'main_form', 'id' => 'main_form');
echo form_open_multipart(base_url("users/".$this->uri->segment(2)), $attributes);
?>
<div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto tool_bar">
            <a href="<?php echo base_url("users/add")?>"><span class="btn btn-success btn-sm mb-1"><?=lang('new')?></span></a>
            <button type="button" onclick="set_task('active', 'main_form', 'check_checklist')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('active')?></button>
            <button type="button" onclick="set_task('inactive', 'main_form', 'check_checklist')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('inactive')?></button>
            <button type="button" onclick="set_task('delete', 'main_form', 'check_checklist')" class="btn btn-danger btn-sm mb-1"><?=lang('delete')?></button>
        </div>
        <div class="col-sm-auto">
            <div class="form-inline">
                <input class="form-control form mr-sm-2 col-10 col-sm-auto" type="search" name="search" placeholder="<?=lang('search')?>" value="<?php echo set_value('search',$html_output['search']); ?>">
                <button class="btn btn-outline-success btn-sm my-2 my-sm-0 col-2 col-sm-auto" type="submit" ><?=lang('do_search')?></button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <?PHP echo $html_output['sys_msg']; ?>
    <div class="row">
        <div class="col">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th class="fit" scope="col"><input type="checkbox" onclick="toggle_checklist(this)"></th>
                    <th scope="col"><?=lang('first_name')?></th>
                    <th scope="col"><?=lang('last_name')?></th>
                    <th scope="col"><?=lang('username')?></th>
                    <th scope="col"><?=lang('email')?></th>
                    <th scope="col"><?=lang('is_block')?></th>
                    <th class="fit" scope="col"><?=lang('condition')?></th>
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
