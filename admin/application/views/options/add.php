<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("options")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <?PHP echo $html_output['sys_msg']; ?>
    <div class="row">
        <div class="col">
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("options/add/".$html_output['item_data']['id']), $attributes);
            ?>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><?=lang('option_name')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="title" placeholder="<?=lang('option_name')?>" value="<?php echo set_value('title',$html_output['item_data']['title']); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="category" class="col-sm-2 col-form-label">
                    <?=lang('category')?>
                </label>
                <div class="col-sm-10">
                    <select id="category" class="form-control" name="category">
						<?PHP echo $html_output['categories_list']; ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="type" class="col-sm-2 col-form-label">
                    <?=lang('type')?>
                </label>
                <div class="col-sm-10">
                    <select id="type" name="type" class="form-control" onchange="show_hide(this);">
						<?PHP echo $html_output['type_list']; ?>
                    </select>
                </div>
            </div>

            <div id="insert_value" class="form-group row d-none">
                <label class="col-sm-2 col-form-label"><?=lang('insert_value')?>
                    <a tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-content="<?=lang('f1_add_setting_insert_value')?>"><i class="text-danger fas fa-question-circle"></i></a>
                </label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="insert_value" rows="5"><?php echo set_value('insert_value',$html_output['item_data']['insert_value']); ?></textarea>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><?=lang('sort')?>
                    <a tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-content="<?=lang('f1_add_setting_sort')?>"><i class="text-danger fas fa-question-circle"></i></a>
                </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="sort" placeholder="<?=lang('please_enter_numeric')?>" value="<?php echo set_value('sort',$html_output['item_data']['sort']); ?>">
                </div>
            </div>

            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('publish')?></legend>
                <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="publish" id="publish1" value="yes" checked<?= set_value('publish', $html_output['item_data']['publish'])== 'yes' ? "" : ""; ?>>
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

<script>
    show_hide(document.getElementById('type'));
    function show_hide(oni_ke_change_shode) {
        if (oni_ke_change_shode.value=="select"){
            document.getElementById('insert_value').classList.remove('d-none');
        }


        else if (oni_ke_change_shode.value=="checkbox"){
            document.getElementById('insert_value').classList.remove('d-none');
        }

        else if (oni_ke_change_shode.value=="upload"){
            //document.getElementById('insert_value').classList.remove('d-none');
        }


        else {
            document.getElementById('insert_value').classList.add('d-none');
        }

    }
    window.addEventListener('load', function () {
        refresh_categories(document.getElementById("category"), '<?=isset($html_output['attribute_groups']) ? $html_output['attribute_groups'] : ""?>');
    });
</script>
