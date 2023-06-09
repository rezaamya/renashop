<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?PHP
$attributes = array('class' => 'main_form', 'id' => 'main_form');
echo form_open_multipart(base_url("marketing/affiliates"), $attributes);
?>
    <div id="tool_bar" class="container-fluid">
        <div class="row justify-content-between">
            <div class="col-sm-auto tool_bar">
                <a href="<?php echo base_url("marketing/add_affiliates")?>"><span class="btn btn-success btn-sm mb-1"><?=lang('new')?></span></a>
                <button type="button" onclick="set_task('view', 'main_form', 'check_checklist')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('view')?></button>
                <button type="button" onclick="set_task('edit_affiliates', 'main_form', 'check_checklist')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('edit')?></button>
                <button type="button" onclick="set_task('delete', 'main_form', 'check_checklist')" class="btn btn-danger btn-sm mb-1"><?=lang('delete')?></button>
            </div>
            <div class="col-sm-auto">
                <div class="form-inline">
                    <input class="form-control form mr-sm-2 col-10 col-sm-auto" type="search" name="search" placeholder="<?=lang('search')?>">
                    <button class="btn btn-outline-success btn-sm my-2 my-sm-0 col-2 col-sm-auto" type="submit"><?=lang('do_search')?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="fit" scope="col"><input type="checkbox" onclick="toggle_checklist(this)"></th>
                        <th scope="col"><?=lang('affiliates_name')?></th>
                        <th scope="col"><?=lang('email')?></th>
                        <th scope="col"><?=lang('created')?></th>
                        <th class="fit" scope="col"><?=lang('condition')?></th>

                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th scope="row"><input type="checkbox" list_items[]></th>
                        <td><a href="#">خانم محمدی</a></td>
                        <td><a href="#">Ms.mohammadi@yahoo.com</a></td>
                        <td><a href="#">97/02/12</a></td>
                        <td class="text-center"><i class="fa fa-unlock"></i></td>
                    </tr>
                    <tr>
                        <th scope="row"><input type="checkbox" list_items[]></th>
                        <td><a href="#">آقای فراهانی</a></td>
                        <td><a href="#">Mr.farahani@yahoo.com</a></td>
                        <td><a href="#">97/02/24</a></td>
                        <td class="text-center"><i class="fa fa-lock"></i></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-auto">
                <nav>
                    <ul class="pagination">
                        <li class="page-item">
                            <a class="page-link" href="#">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only"><?=lang('previous')?></span>
                            </a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only"><?=lang('next')?></span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <input type="hidden" id="task" name="task">
</form>
