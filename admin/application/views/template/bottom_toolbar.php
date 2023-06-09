<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="row justify-content-sm-center">
    <div class="col-auto">
        <div class="form-inline">
            <label for="per_page" class="pr-1"><?=lang('show_in_page')?></label>
            <select onchange="set_task('change_per_page', 'main_form', '')" id="per_page" name="per_page" class="form-control-sm">

                <option value="5" <?= set_value('per_page', $this->session->userdata($this->uri->segment(1).$this->uri->segment(2).'per_page')) == '5' ? "selected" : ""; ?>>5</option>

                <option value="10" <?= set_value('per_page', $this->session->userdata($this->uri->segment(1).$this->uri->segment(2).'per_page')) == '10' ? "selected" : ""; ?>>10</option>

                <option value="20" <?= set_value('per_page', $this->session->userdata($this->uri->segment(1).$this->uri->segment(2).'per_page')) == '20' ? "selected" : ""; ?>>20</option>

                <option value="50" <?= set_value('per_page', $this->session->userdata($this->uri->segment(1).$this->uri->segment(2).'per_page')) == '50' ? "selected" : ""; ?>>50</option>

                <option value="100" <?= set_value('per_page', $this->session->userdata($this->uri->segment(1).$this->uri->segment(2).'per_page')) == '100' ? "selected" : ""; ?>>100</option>
            </select>
        </div>
    </div>
    <div class="col-auto pagination-sm">
        <?PHP echo $html_output['pagination']; ?>
    </div>
</div>