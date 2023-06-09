<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
        <a href="<?php echo base_url("marketing/affiliates")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
    </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>اخطار!</strong></br> شما باید فرم زیر را تکمیل کنید.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="personal_profile" data-toggle="tab" href="#personal" role="tab"><?=lang('personal_profile')?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="contact_details" data-toggle="tab" href="#contact" role="tab"><?=lang('contact_details')?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="history_customer" data-toggle="tab" href="#history" role="tab"><?=lang('history_customer')?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="transactions" data-toggle="tab" href="#transaction" role="tab"><?=lang('transactions')?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="reward_points" data-toggle="tab" href="#reward" role="tab"><?=lang('reward_points')?></a>
        </li>
    </ul>
    <?PHP
    $attributes = array('class' => 'main_form', 'id' => 'main_form');
    echo form_open_multipart(base_url("marketing/edit_affiliates"), $attributes);
    ?>
        <div class="tab-content" id="customer_details">
            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                <div class="row">
                    <div class="col">
                        <div class="form-group row">
                            <label for="customer_first_name" class="col-sm-2 col-form-label"><?=lang('customer_first_name')?></label>
                            <div class="col-sm-10">
                                <input name="customer_first_name" type="text" class="form-control" id="customer_first_name" placeholder="<?=lang('customer_first_name')?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="customer_last_name" class="col-sm-2 col-form-label"><?=lang('customer_last_name')?></label>
                            <div class="col-sm-10">
                                <input name="customer_last_name" type="text" class="form-control" id="customer_last_name" placeholder="<?=lang('customer_last_name')?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="user_name" class="col-sm-2 col-form-label"><?=lang('user_name')?></label>
                            <div class="col-sm-10">
                                <input name="user_55name" type="text" class="form-control" id="user_name" placeholder="<?=lang('user_name')?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="select_1" class="col-sm-2 col-form-label"><?=lang('sex')?></label>
                            <div class="col-sm-10">
                                <select name="sex" id="select_1" class="form-control">
                                    <option><?=lang('female')?></option>
                                    <option><?=lang('man')?></option>
                                    <option><?=lang('others')?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="title" class="col-sm-2 col-form-label"><?=lang('email')?></label>
                            <div class="col-sm-10">
                                <input name="email" type="email" class="form-control" id="email" placeholder="<?=lang('email')?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="select_1" class="col-sm-2 col-form-label"><?=lang('user_position')?></label>
                            <div class="col-sm-10">
                                <select name="user_position" id="select_1" class="form-control">
                                    <option>کاربر عادی</option>
                                    <option>دانشجو</option>
                                    <option>وکیل</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-sm-2 col-form-label"><?=lang('password')?></label>
                            <div class="col-sm-10">
                                <input name="password" type="password" class="form-control" id="password" placeholder="<?=lang('password')?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-sm-2 col-form-label"><?=lang('confirm_password')?></label>
                            <div class="col-sm-10">
                                <input name="confirm_password" type="password" class="form-control" id="confirm_password" placeholder="<?=lang('confirm_password')?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <legend class="col-form-label col-sm-2 pt-0"><?=lang('condition')?></legend>
                            <div class="col-sm-10">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="condition" id="condition1" value="active" checked>
                                    <label class="form-check-label" for="condition1">
                                        <?=lang('active')?>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="condition" id="condition2" value="inactive">
                                    <label class="form-check-label" for="condition2">
                                        <?=lang('inactive')?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="contact" role="tabpanel">
                <div class="row">
                    <div class="col">
                        <div class="form-group row">
                            <label for="customer_first_name" class="col-sm-2 col-form-label"><?=lang('customer_first_name')?></label>
                            <div class="col-sm-10">
                                <input name="customer_first_name" type="text" class="form-control" id="customer_first_name" placeholder="<?=lang('customer_first_name')?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="customer_last_name" class="col-sm-2 col-form-label"><?=lang('customer_last_name')?></label>
                            <div class="col-sm-10">
                                <input name="customer_last_name" type="text" class="form-control" id="customer_last_name" placeholder="<?=lang('customer_last_name')?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="select_1" class="col-sm-2 col-form-label"><?=lang('country')?></label>
                            <div class="col-sm-10">
                                <select name="country" id="select_1" class="form-control">
                                    <option>ایران</option>
                                    <option>فرانسه</option>
                                    <option>آلمان</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="select_1" class="col-sm-2 col-form-label"><?=lang('state')?></label>
                            <div class="col-sm-10">
                                <select name="state" id="select_1" class="form-control">
                                    <option>خوزستان</option>
                                    <option>تهران</option>
                                    <option>شیراز</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="select_1" class="col-sm-2 col-form-label"><?=lang('city')?></label>
                            <div class="col-sm-10">
                                <select name="city" id="select_1" class="form-control">
                                    <option>اهواز</option>
                                    <option>تهران</option>
                                    <option>شیراز</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address" class="col-sm-2 col-form-label"><?=lang('address')?></label>
                            <div class="col-sm-10">
                                <input name="address" type="text" class="form-control" id="address" placeholder="<?=lang('address')?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="postcode" class="col-sm-2 col-form-label"><?=lang('postcode')?></label>
                            <div class="col-sm-10">
                                <input name="postcode" type="text" class="form-control" id="postcode" placeholder="<?=lang('postcode')?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tel" class="col-sm-2 col-form-label"><?=lang('tel')?></label>
                            <div class="col-sm-10">
                                <input name="tel" type="text" class="form-control" id="tel" placeholder="<?=lang('tel')?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="history" role="tabpanel">.3..</div>
            <div class="tab-pane fade" id="transaction" role="tabpanel">.4..</div>
            <div class="tab-pane fade" id="reward" role="tabpanel">.5..</div>
        </div>
    <input type="hidden" id="task" name="task">
    </form>
</div>