<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("order")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
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
            <a class="nav-link active" id="order_details" data-toggle="tab" href="#order" role="tab"><?=lang('order_details')?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="shipping_details" data-toggle="tab" href="#shipping" role="tab"><?=lang('shipping_details')?></a>
        </li>
        <!--<li class="nav-item">
            <a class="nav-link" id="trapaymen_details" data-toggle="tab" href="#payment" role="tab"><?=lang('payment_details')?></a>
        </li>-->
    </ul>
    <?PHP
    $attributes = array('class' => 'main_form', 'id' => 'main_form');
    echo form_open_multipart(base_url("order/edit"), $attributes);
    ?>
        <div class="tab-content" id="customer_details">
        <div class="tab-pane fade show active" id="order" role="tabpanel">
            <div class="container-fluid">
                <div><i class="fas fa-angle-double-left margin_l"></i>توجه: قیمت ها به تومان میباشد.</div>
                <table id="order_table" class="table table-responsive table-bordered">
                    <thead>
                    <tr>
                        <th scope="fit"><?=lang('product_pic')?></th>
                        <th scope="col" class="description"><?=lang('order_description')?></th>
                        <th class="fit" scope="col"><?=lang('quantity')?></th>
                        <th class="fit" scope="col"><?=lang('unit_price')?></th>
                        <th class="fit" scope="col"><?=lang('discounted_price')?></th>
                        <th scope="fit" style="width: 50px"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="image"><a href="#"><img src="http://amya.ir/demo/keshavarz/content/products/thumb/no_pic.jpg"></a></td>
                        <td>
                            <div class="mb-3">
                                <span><b>عنوان محصول</b></span>
                            </div>
                            <div>
                                <i class="fas fa-angle-double-left"></i>
                                <span><b>ارسال بصورت هدیه</b></span>
                            </div>

                            <div>
                                <i class="fas fa-angle-double-left"></i>
                                <span><b>نام فرستنده: </b></span>
                                <span>قاسمی</span>
                            </div>

                            <div>
                                <i class="fas fa-angle-double-left"></i>
                                <span><b>اسامی گیرندگان:</b></span>
                                <span> فلانی، بهمانی</span>
                            </div>

                            <div>
                                <i class="fas fa-angle-double-left"></i>
                                <span><b>امضای صفحه اول:</b></span>
                                <span> تولدت مبارک</span>
                            </div>

                            <div>
                                <i class="fas fa-angle-double-left"></i>
                                <span><b>نوع بسته بندی:</b></span>
                                <span>کادو</span>

                            </div>

                            <div>
                                <i class="fas fa-angle-double-left"></i>
                                <span><b>اقلام همراه هدیه:</b></span>
                                <span>کارت پستال</span>
                            </div>

                            <div>
                                <i class="fas fa-angle-double-left"></i>
                                <span><b>ارسال نسخه PDF کتاب</b></span>
                            </div>

                            <div>
                                <i class="fas fa-angle-double-left"></i>
                                <span><b>تصویر ارسالی همراه هدیه:</b></span>
                                <a href="#"><i class="fas fa-cloud-download-alt"></i>
                                </a></div><a href="#">
                            </a></td>
                        <td>1</td>
                        <td>1000</td>
                        <td>2000</td>
                        <td>
                            <div>
                                <i data-toggle="modal" data-target="#edit_btn" class="fas fa-pen-square" onclick="document.getElementById('edit_btn').style.display='block'"></i>
                            </div>
                            <div>
                                <i data-toggle="modal" data-target="#del_btn" class="fas fa-window-close" onclick="document.getElementById('del_btn').style.display='block'"></i>
                            </div
                                    <!-- Modal -->
                            <div class="modal fade" id="edit_btn" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">ویرایش سفارش</h4>
                                        </div>
                                        <div class="modal-body">
                                            <iframe src="http://amya.ir/demo/keshavarz/client/pages/single_product/7/15" width="100%" height="300px"></iframe>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default apply" data-dismiss="modal"><?=lang('apply_changes')?></button>
                                            <button type="button" class="btn btn-default cancel" data-dismiss="modal"><?=lang('cancel')?></button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="del_btn" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">حذف سفارش</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div>آیا از حذف سفارش خود مطمئن هستید؟</div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default apply" data-dismiss="modal"><?=lang('yes')?></button>
                                            <button type="button" class="btn btn-default cancel" data-dismiss="modal"><?=lang('no')?></button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="image"><a href="#"><img src="http://amya.ir/demo/keshavarz/content/products/thumb/no_pic.jpg"></a></td>
                        <td>
                            <div class="mb-3">
                                <span><b>عنوان محصول</b></span>
                            </div>
                            <div>
                                <i class="fas fa-angle-double-left"></i>
                                <span><b>ارسال بصورت هدیه</b></span>
                            </div>

                            <div>
                                <i class="fas fa-angle-double-left"></i>
                                <span><b>اسامی گیرندگان:</b></span>
                                <span> فلانی، بهمانی</span>
                            </div>

                            <div>
                                <i class="fas fa-angle-double-left"></i>
                                <span><b>ارسال نسخه PDF کتاب</b></span>
                            </div>

                            <div>
                                <i class="fas fa-angle-double-left"></i>
                                <span><b>تصویر ارسالی همراه هدیه:</b></span>
                                <a href="#"><i class="fas fa-cloud-download-alt"></i>
                                </a></div><a href="#">
                            </a></td>
                        <td>2</td>
                        <td>2000</td>
                        <td>2000</td>
                        <td>
                            <div>
                                <i class="fas fa-pen-square"></i>
                            </div>
                            <div>
                                <i class="fas fa-window-close"></i>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="shipping" role="tabpanel">
            <div class="row">
                <div class="col">
                    <div class="form-group row">
                        <label for="address_first_name" class="col-sm-2 col-form-label"><?=lang('first_name')?></label>
                        <div class="col-sm-10">
                            <input name="address_first_name" type="text" class="form-control" id="address_first_name" value="<?php echo set_value('address_first_name',$html_output['item_data']['address_first_name']); ?>" placeholder="<?=lang('first_name')?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address_last_name" class="col-sm-2 col-form-label"><?=lang('last_name')?></label>
                        <div class="col-sm-10">
                            <input name="address_last_name" type="text" class="form-control" id="address_last_name" value="<?php echo set_value('address_last_name',$html_output['item_data']['address_last_name']); ?>" placeholder="<?=lang('last_name')?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address_country" class="col-sm-2 col-form-label"><?=lang('country')?></label>
                        <div class="col-sm-10">
                            <select name="address_country" id="address_country" class="form-control">
								<?php echo $html_output['item_data']['address_country']; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address_state" class="col-sm-2 col-form-label"><?=lang('state')?></label>
                        <div class="col-sm-10">
                            <select name="address_state" id="address_state" class="form-control">
								<?php echo $html_output['item_data']['address_state']; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address_city" class="col-sm-2 col-form-label"><?=lang('city')?></label>
                        <div class="col-sm-10">
                            <select name="address_city" id="address_city" class="form-control">
								<?php echo $html_output['item_data']['address_city']; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="shipping_region" class="col-sm-2 col-form-label"><?=lang('region_name')?></label>
                        <div class="col-sm-10">
                            <select name="shipping_region" id="shipping_region" class="form-control">
                                <option>منطقه 1</option>
                                <option>منطقه 2</option>
                                <option>منطقه 3</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2" for="address"><?=lang('address')?></label>
                        <div class="col-sm-10">
                            <textarea name="address" class="form-control" id="address" rows="5"><?php echo set_value('address',$html_output['item_data']['address']); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address_postcode" class="col-sm-2 col-form-label"><?=lang('postcode')?></label>
                        <div class="col-sm-10">
                            <input name="address_postcode" type="text" class="form-control" id="address_postcode" value="<?php echo set_value('address_postcode',$html_output['item_data']['address_postcode']); ?>" placeholder="<?=lang('postcode')?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address_mobile" class="col-sm-2 col-form-label"><?=lang('mobile')?></label>
                        <div class="col-sm-10">
                            <input name="address_mobile" type="text" class="form-control" id="address_mobile" value="<?php echo set_value('address_mobile',$html_output['item_data']['address_mobile']); ?>" placeholder="<?=lang('mobile')?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address_tel" class="col-sm-2 col-form-label"><?=lang('tel')?></label>
                        <div class="col-sm-10">
                            <input name="address_tel" type="text" class="form-control" id="address_tel" value="<?php echo set_value('address_tel',$html_output['item_data']['address_tel']); ?>" placeholder="<?=lang('tel')?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="shipping_method" class="col-sm-2 col-form-label"><?=lang('shipping_method')?></label>
                        <div class="col-sm-10">
                            <select name="shipping_method" id="shipping_method" class="form-control">
                                <?php echo $html_output['view_shipping']; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--<div class="tab-pane fade" id="payment" role="tabpanel">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="fit" scope="col">#</th>
                    <th scope="col"><?=lang('product')?></th>
                    <th scope="col"><?=lang('code')?></th>
                    <th scope="col"><?=lang('quantity')?></th>
                    <th scope="col"><?=lang('unit_price')?></th>
                    <th scope="col"><?=lang('sum')?></th>

                </tr>
                </thead>
                <tbody>
                <tr>
                    <th scope="row">1</th>
                    <td>محصول اول</td>
                    <td>154</td>
                    <td>1</td>
                    <td>1000</td>
                    <td>1000</td>
                </tr>
                <tr>
                    <th scope="row">2</th>
                    <td>محصول دوم</td>
                    <td>325</td>
                    <td>2</td>
                    <td>2000</td>
                    <td>4000</td>
                </tr>
                <tr>
                    <th scope="row"></th>
                    <td>5000</td>
                </tr>
                </tbody>
            </table>
        </div>-->

    </div>
        <input type="hidden" id="task" name="task">
    </form>
</div>
