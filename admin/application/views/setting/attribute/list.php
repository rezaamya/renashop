<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?PHP
$attributes = array('class' => 'main_form', 'id' => 'main_form');
echo form_open_multipart(base_url("setting/attribute"), $attributes);
?>
    <div id="tool_bar" class="container-fluid">
        <div class="row justify-content-between">
            <div class="col-sm-auto tool_bar">
                <a href="<?php echo base_url("setting/add_attribute")?>"><span class="btn btn-success btn-sm mb-1"><?=lang('new')?></span></a>
                <button type="button" onclick="set_task('edit', 'main_form', 'check_checklist')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('edit')?></button>
                <button type="button" onclick="set_task('publish', 'main_form', 'check_checklist')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('publish')?></button>
                <button type="button" onclick="set_task('unpublish', 'main_form', 'check_checklist')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('unpublish')?></button>
                <button type="button" onclick="set_task('delete', 'main_form', 'check_checklist')" class="btn btn-danger btn-sm mb-1"><?=lang('delete')?></button>
            </div>
            <div class="col-sm-auto">
                <div class="form-inline">
                    <input class="form-control form mr-sm-2 col-10 col-sm-auto"" type="search" placeholder="<?=lang('search')?>">
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
                        <th class="fit" scope="col"><input type="checkbox"></th>
                        <th scope="col"><?=lang('attribute_name')?></th>
                        <th scope="col"><?=lang('attribute_groups_name')?></th>
                        <th class="fit" scope="col"><?=lang('publish')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th scope="row"><input type="checkbox"></th>
                        <td><a href="#">اولین ویژگی</a></td>
                        <td><a href="#"> ویدئو</a></td>
                        <td class="text-center"><i class="fas fa-star"></i></td>
                    </tr>
                    <tr>
                        <th scope="row"><input type="checkbox"></th>
                        <td><a href="#">دومین ویژگی</a></td>
                        <td><a href="#"> کتاب</a></td>
                        <td class="text-center"><i class="far fa-star"></i></td>
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