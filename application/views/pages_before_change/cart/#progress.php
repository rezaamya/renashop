<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
$client_current_url = $base_url . $_SERVER["REQUEST_URI"];
?>

<?PHP //include "blocks/header.php"; ?>
<?PHP //include "blocks/main_menu.php"; ?>

<div id="cart_progress" class="w3-display-container">

    <?php if($this->session->userdata('id') == null){?>
    <div class="accordion_holder type_of_user">
        <button class="accordion w3-btn w3-block w3-right-align w3-border w3-border-white">تعیین نوع کاربری</button>
        <div id="type_of_user" class="w3-row-padding tab_content">
            <div class="w3-col m6">
                <form class="w3-container">
                    <h2><h5>من مشتری جدید هستم</h5></h2>
                    <p>
                        <input onclick="guest_form();" class="w3-radio" type="radio" name="user_type" value="guest">
                        <label for="login">تسویه حساب به صورت مهمان</label></p>
                    <p>
                        <input onclick="register_form();" class="w3-radio" type="radio" name="user_type" value="register" checked>
                        <label for="register">ثبت نام</label></p>
                    <p class="description">
                        با ثبت نام و ايجاد يک حساب کاربری می توانید سريع تر خريد نموده، تاريخچه سفارش های خود را مشاهده کرده و همچنين از وضعيت سفارش خود آگاهی پيدا کرده و آن را پیگیری نمایید.</p>
                    <p>
                        <button onclick="close_open('type_of_user', 'type_of_user', 'account_information');" type="button" class="btn w3-button w3-round w3-green w3-left w3-margin-bottom"><?=lang('continuation')?></button>
                        <a href="<?=base_url("cart")?>" class="btn w3-button w3-round w3-red w3-left w3-margin-bottom"><?=lang('return')?></a>
                    </p>
                </form>

            </div>
            <div class="w3-col m6">
                <form class="w3-container" action="<?=base_url('login?redirect='.urlencode($client_current_url))?>" method="post">
                    <h5>من مشتری قدیمی هستم</h5>
					<?PHP echo $sys_msg; ?>
                    <div class="w3-row">
                        <label class="w3-margin-top"><b><?=lang('username')?></b></label>
                        <input class="w3-input w3-round w3-margin-left w3-padding w3-border" name="username" type="text">
                    </div>
                    <div class="w3-row">
                        <label class="w3-margin-top"><b><?=lang('password')?></b></label>
                        <input class="w3-input w3-round w3-margin-left w3-padding w3-border" name="password" type="password">
                        <label class="remember_me w3-small"><input name="remember_me" class="remember_me_checkbox" type="checkbox">
                        <?=lang('remember_me')?></label>
                        <div class="w3-small" href="#">رمز عبور را فراموش کرده ام</div>
                    </div>
                    <p class="w3-row">
						<button type="submit" class="btn w3-button w3-left w3-round w3-padding w3-green"><?=lang('login')?></button>
                        <a href="<?=base_url("cart")?>" class="btn w3-button w3-round w3-red w3-left w3-margin-bottom"><?=lang('return')?></a>
                    </p>
                </form>

            </div>
        </div>
    </div>
    <?php }?>

    <div class="accordion_holder account_information">
        <button class="accordion w3-btn w3-block w3-right-align w3-border w3-border-white">اطلاعات حساب</button>
        <div id="account_information" class="w3-row-padding tab_content <?php if($this->session->userdata('id') == null){?>w3-hide<?php }?>">
            <div class="loading_holder">
                <div class="content">
                    <div class="loader_spin">&nbsp;</div>
                    <span><?=lang('please_wait');?></span>
                </div>
            </div>
            <form>
                <div class="w3-row-padding message_holder"></div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top m2 s4"><?=lang('first_name')?></label>
                    <input class="w3-col m10 s8 w3-input w3-padding w3-border w3-round" name="first_name" type="text" placeholder="<?=lang('first_name')?>" value="<?php echo set_value('first_name',$first_name); ?>"></div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top m2 s4"><?=lang('last_name')?></label>
                    <input class="w3-col m10 s8 w3-input w3-padding w3-border w3-round" name="last_name" type="text" placeholder="<?=lang('last_name')?>" value="<?php echo set_value('last_name',$last_name); ?>">
                </div>
                <div class="w3-row-padding margin-bottom birthday">
                    <label class="w3-col w3-margin-top m2 s4"><?=lang('birthday')?></label>
                    <select class="w3-select w3-col s2 m3 w3-input w3-border w3-round" name="year">
						<?php echo $year; ?>

                    </select>
                    <select class="w3-select w3-col s2 m3 w3-input w3-border w3-round" name="month">
						<?php echo $month; ?>

                    </select>
                    <select class="w3-select w3-col s2 m3 w3-input w3-border w3-round" name="day">
						<?php echo $day; ?>

                    </select>
                </div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top m2 s4"><?=lang('sex')?></label>
                    <select class="w3-select w3-col m10 s8 w3-input w3-padding w3-border w3-round" name="sex">
                        <option value="" disabled selected><?=lang('sex')?></option>
                        <option value="female" <?= set_value('sex', $sex) == 'female' ? "selected" : ""; ?>><?=lang('female')?></option>
                        <option value="man" <?= set_value('sex', $sex) == 'man' ? "selected" : ""; ?>><?=lang('man')?></option>
                        <option value="others" <?= set_value('sex', $sex) == 'others' ? "selected" : ""; ?>><?=lang('others')?></option>

                    </select>
                </div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top m2 s4"><?=lang('customer_group')?></label>
                    <select class="w3-select w3-col m10 s8 w3-input w3-padding w3-border w3-round" name="customer_group">
                        <?php echo $customer_group_list; ?>
                    </select>
                </div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top m2 s4"><?=lang('email')?></label>
                    <input class="w3-col m10 s8 w3-input w3-padding w3-border w3-round" name="email" type="email" placeholder="<?=lang('email')?>" value="<?php echo set_value('email',$email); ?>">
                </div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top m2 s4"><?=lang('mobile')?></label>
                    <input class="w3-col m10 s8 w3-input w3-padding w3-border w3-round" name="mobile" type="text" placeholder="<?=lang('mobile')?>" value="<?php echo set_value('mobile',$mobile); ?>">
                </div>
                <?php if($this->session->userdata('id') == null){?>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top m2 s4"><?=lang('username')?></label>
                    <input class="w3-col m10 s8 w3-input w3-padding w3-border w3-round" name="username" type="text" placeholder="<?=lang('username')?>" value="<?php echo set_value('username',$username); ?>">
                </div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top m2 s4"><?=lang('password')?></label>
                    <input class="w3-col m10 s8 w3-input w3-padding w3-border w3-round" name="password" type="password" placeholder="<?=lang('password')?>">
                </div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top m2 s4"><?=lang('confirm_password')?></label>
                    <input class="w3-col m10 s8 w3-input w3-padding w3-border w3-round" name="confirm_password" type="password" placeholder="<?=lang('password')?>">
                </div>
                <?PHP } ?>
                <!--<div class="w3-col m6">
                    <div class="w3-row-padding margin-bottom">
                        <label class="w3-col w3-margin-top s3"><?=lang('postcode')?></label>
                        <input class="w3-col s9 w3-input w3-padding w3-border w3-round w3-margin-bottom" name="postcode" type="text" placeholder="<?=lang('postcode')?>">
                    </div>
                    <div class="w3-row-padding margin-bottom">
                        <label class="w3-col w3-margin-top s3"><?=lang('address')?></label>
                        <select class=" w3-select w3-col s3 w3-input w3-padding w3-border w3-round w3-margin-bottom" name="country">
                            <option value="" disabled selected><?=lang('country')?></option>
                            <option value="1">ایران</option>
                            <option value="2">کانادا</option>
                            <option value="3">فرانسه</option>
                        </select>
                        <select class="w3-select w3-col s3 w3-input w3-padding w3-border w3-round w3-margin-bottom" name="state">
                            <option value="" disabled selected><?=lang('state')?></option>
                            <option value="1">خوزستان</option>
                            <option value="2">تهران</option>
                            <option value="3">فارس</option>

                        </select>
                        <select class="w3-select w3-col s3 w3-input w3-padding w3-border w3-round w3-margin-bottom" name="city">
                            <option value="" disabled selected><?=lang('city')?></option>
                            <option value="1">اهواز</option>
                            <option value="2">تهران</option>
                            <option value="3">شیراز</option>
                        </select>
                    </div>
                    <div class="w3-row-padding margin-bottom">
                        <label class="w3-col w3-margin-top s3"><?=lang('complete_address')?></label>
                        <textarea class="w3-col s9 w3-input w3-padding w3-border w3-round w3-margin-bottom" rows="3" name="complete_address"></textarea>
                    </div>
                </div>-->
                <div class="w3-row-padding margin-bottom">
                    <a onclick="update_profile(this);" class="btn w3-button w3-round w3-green w3-left w3-margin-bottom"><?=lang('continuation')?></a>
					<?php if($this->session->userdata('id') == null){?>
					<a onclick="close_open('return_back', 'account_information', 'type_of_user');" class="btn w3-button w3-round w3-red w3-left w3-margin-bottom"><?=lang('return')?></a>
					<?php }?>
                </div>
            </form>
        </div>
    </div>

    <div class="accordion_holder address">
        <button class="accordion w3-btn w3-block w3-right-align w3-border w3-border-white">آدرس ارسال مرسوله</button>
        <div id="address" class="tab_content w3-hideeeeeeeeeeeeee">
            <div class="w3-row-padding">
                <p class="w3-col w3-margin-top">لطفا آدرس ارسال مرسوله را انتخاب کنید.</p>
            </div>

            <!--<div address_id="ADDRESS_ID" address_title="ADDRESS_TITLE" address_link="ADDRESS_LINK" class="address_item w3-container">
                <div class="w3-card w3-white w3-margin-bottom">
                    <a href="<?= base_url("profile/edit_address")?>" class="w3-button w3-light-blue w3-round w3-left w3-show-inline-block w3-margin-top w3-margin-left"><i class="fas fa-edit"></i></a>
                    <h5 class="w3-show-inline-block w3-margin-right w3-margin-bottom">منزل خودم</h5>
                    <div class="w3-margin-right"><i class="fas fa-user w3-large w3-margin-left w3-margin-bottom"></i>دریافت کننده: فلانی</div>
                    <div class="w3-margin-right"><i class="fas fa-mobile-alt w3-large w3-margin-left w3-margin-bottom"></i>شماره تماس: 09106005555</div>
                    <div class="w3-margin-right"><i class="fas fa-map-marker-alt w3-large w3-margin-left w3-margin-bottom"></i> آدرس: تهران، جمالزاده شمالی، کوچه سوسن پلاک 8 واحد4</div>
                    <div class="w3-padding-16 navar"></div>
                </div>
            </div>-->

            <div class="loading_holder">
                <div class="content">
                    <div class="loader_spin">&nbsp;</div>
                    <span><?=lang('please_wait');?></span>
                </div>
            </div>

            <form class="add_address">
                <div class="w3-row-padding message_holder"></div>
                <div class="w3-row-padding margin-bottom">
                        <div class="w3-col m3">
                            <select class="w3-select w3-input w3-padding w3-border w3-margin-bottom w3-round" name="address_status" onchange="set_address(this);">
                                <option value="new_address" selected><?=lang('new_address')?></option>
                                <?php echo $view_address; ?>
                            </select>
                        </div>
                        <!--<div class="w3-col m2">
                        <a href="<?= base_url("profile/edit_address")?>" class="w3-mobile w3-button w3-round w3-green w3-padding w3-margin-bottom">ارسال به آدرس جدید</a>
                    </div>-->
                    </div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top s3"><?=lang('address_title')?></label>
                    <input class="w3-col s9 w3-input w3-padding w3-border w3-round" name="address_title" type="text" placeholder="<?=lang('address_title')?>" value="">
                </div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top s3"><?=lang('first_name')?></label>
                    <input class="w3-col s9 w3-input w3-padding w3-border w3-round" name="address_first_name" type="text" placeholder="<?=lang('first_name')?>" value="">
                </div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top s3"><?=lang('last_name')?></label>
                    <input class="w3-col s9 w3-input w3-padding w3-border w3-round" name="address_last_name" type="text" placeholder="<?=lang('last_name')?>" value="">
                </div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top s3"><?=lang('mobile')?></label>
                    <input class="w3-col s9 w3-input w3-padding w3-border w3-round" name="address_mobile" type="text" placeholder="<?=lang('mobile')?>" value="">
                </div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top s3"><?=lang('tel')?></label>
                    <input class="w3-col s9 w3-input w3-padding w3-border w3-round" name="address_tel" type="text" placeholder="<?=lang('tel')?>" value="">
                </div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top s3"><?=lang('postcode')?></label>
                    <input class="w3-col s9 w3-input w3-padding w3-border w3-round" name="address_postcode" type="text" placeholder="<?=lang('postcode')?>" value="">
                </div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top s3"><?=lang('region')?></label>
                    <div class="w3-col s9">
                        <div class="w3-third">
                            <select class="w3-select w3-col w3-input w3-padding w3-border w3-round" name="address_country">
                                <option value="" disabled selected><?=lang('country')?></option>
                                <?PHP echo $country_name; ?>
                            </select>
                        </div>
                        <div class="w3-third">
                            <select class="w3-select w3-col w3-input w3-padding w3-border w3-round" name="address_state">
                                <option value="" disabled selected><?=lang('state')?></option>
                                <?PHP echo $state_name; ?>
                            </select>
                        </div>
                        <div class="w3-third">
                            <select class="w3-select w3-col w3-input w3-padding w3-border w3-round" name="address_city">
                                <option value="" disabled selected><?=lang('city')?></option>
                                <?PHP echo $city_name; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="w3-row-padding margin-bottom">
                    <label class="w3-col w3-margin-top s3"><?=lang('address')?></label>
                    <textarea class="w3-col s9 w3-input w3-padding w3-border w3-round" rows="3" name="address"></textarea>
                </div>

                <input type="hidden" id="task" name="task">

                <div class="w3-row-padding">
                <button onclick="update_address(this);" type="button" class="btn w3-button w3-round w3-green w3-left w3-margin-bottom"><?=lang('continuation')?></button>
                <button onclick="close_open('return_back_from_address', 'address', 'account_information');" type="button" class="btn w3-button w3-round w3-red w3-left w3-margin-bottom"><?=lang('return')?></button>
            </div>
            </form>

        </div>
    </div>

    <div class="accordion_holder shipping_method">
        <button class="w3-btn w3-block accordion w3-right-align w3-border w3-border-white">نحوه ارسال مرسوله</button>
        <div id="shipping_method" class="tab_content w3-hideeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee">
            <div class="loading_holder">
                <div class="content">
                    <div class="loader_spin">&nbsp;</div>
                    <span><?=lang('please_wait');?></span>
                </div>
            </div>

            <div class="w3-container">
                <p class="w3-col w3-margin-top">لطفا نحوه ارسال مرسوله را انتخاب کنید.</p>
            </div>

            <div class="w3-row-padding message_holder"></div>

            <div class="w3-row-padding">
                <div class="w3-col m3">
                    <select onchange="post_calculation(this);" class="w3-select w3-input w3-padding w3-border w3-margin-bottom w3-margin-left w3-round" name="shipping_method">
                        <option value="" disabled selected><?=lang('please_select')?></option>
                        <?php echo $view_shipping; ?>
                    </select>
                </div>
                <p class="w3-margin-bottom w3-col m3 price_holder"></p>
            </div>

            <div class="w3-row-padding shipping_region w3-hide">
                <div class="w3-col m3">
                    <select onchange="post_calculation(this, true);" class="w3-select w3-input w3-padding w3-border w3-margin-bottom w3-margin-left w3-round" name="shipping_region">
                    </select>
                </div>
                <p class="w3-margin-bottom w3-col m3 price_holder"></p>
            </div>
            <div class="w3-row-padding">
                <button onclick="if(this.disabled){return false;} close_open('shipping_method', 'shipping_method', 'payment');" class="btn w3-button w3-round w3-green w3-left w3-margin-bottom goto_next_step_btn"><?=lang('continuation')?></button>
                <button onclick="close_open('return_back', 'shipping_method', 'address');" class="btn w3-button w3-round w3-red w3-left w3-margin-bottom"><?=lang('return')?></button>
            </div>
        </div>
    </div>

    <div class="accordion_holder payment">
        <button class="w3-btn w3-block accordion w3-right-align w3-border w3-border-white">نحوه پرداخت مرسوله</button>
        <div id="payment" class="tab_content w3-hide">
            <div class="w3-container">
                <p class="w3-col w3-margin-top">لطفا نحوه پرداخت را انتخاب کنید.</p>
            </div>
            <div class="w3-row-padding w3-padding">
             <?php echo $view_payment; ?>
            </div>
            <div class="w3-row-padding">
                <button onclick="close_open('payment', 'coupon');" class="btn w3-button w3-round w3-green w3-left w3-margin-bottom"><?=lang('continuation')?></button>
                <button onclick="close_open('return_back', 'payment', 'shipping_method');" class="btn w3-button w3-round w3-red w3-left w3-margin-bottom"><?=lang('return')?></button>
            </div>
        </div>
    </div>

    <!--<div class="accordion_holder coupon">
        <button class="accordion w3-btn w3-block accordion w3-right-align w3-border w3-border-white">استفاده از کد تخفیف</button>
        <div id="coupon" class="tab_content w3-hide">
            <form class="w3-container" action="/action_page.php">
                <p class="w3-row-padding">
                    <label class="w3-col m4 w3-margin-top"><b>در صورت داشتن کوپن تخفیف، کد مربوطه را وارد نمایید.</b></label>
                </p>
                <p class="w3-row-padding">
                    <input class="w3-col m4 w3-input w3-round w3-margin-left w3-padding w3-border w3-margin-bottom" name="coupon_code" type="text">
                    <button type="submit" class="w3-col m1 w3-right w3-round w3-padding w3-btn w3-blue">اعمال کد</button>
                </p>
            </form>
            <div class="w3-row-padding">
                <button onclick="close_open('coupon', 'order_confirmation');" class="btn w3-button w3-round w3-green w3-left w3-margin-bottom"><?=lang('continuation')?></button>
                <button onclick="close_open('return_back', 'coupon', 'payment');" class="btn w3-button w3-round w3-red w3-left w3-margin-bottom"><?=lang('return')?></button>
            </div>
        </div>
    </div>-->

    <div class="accordion_holder order_confirmation">
        <button class="w3-btn w3-block accordion w3-right-align w3-border w3-border-white">تایید سفارش</button>
        <div id="order_confirmation" class="tab_content w3-hide">

            <div class="w3-row-padding">
                <div class="w3-card w3-white w3-margin-bottom">
                    <div class="w3-row content w3-margin-bottom">
                        <h4 class="w3-center w3-margin-bottom">صورتحساب</h4>
                        <div class="w3-responsive w3-container">
                            <table class="order_table w3-table w3-bordered">
                                <tr>
                                    <th>تصویر محصول</th>
                                    <th  class="description_cart">شرح سفارش</th>
                                    <th>تعداد</th>
                                    <th>قیمت واحد(تومان)</th>
                                    <th>تخفیف(تومان)</th>
                                    <th>قیمت نهایی(تومان)</th>
                                </tr>
								<?php echo $product_in_cart; ?>
                                <tr>
                                    <td>
                                        <a class="image" href="#">
                                            <img src="http://amya.ir/demo/keshavarz/content/products/thumb/no_pic.jpg">
                                        </a>
                                    </td>
                                    <td>
                                        <div class="w3-margin-bottom">
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
                                        </div>
                                    </td>
                                    <td>1</td>
                                    <td>2000</td>
                                    <td>500</td>
                                    <td>1500</td>
                                </tr>

                                <tr>
                                    <td class="w3-left-align" colspan="5"><b>مبلغ سفارش(تومان)</b></td>
                                    <td><b>6500</b></td>
                                </tr>
                                <tr>
                                    <td class="w3-left-align" colspan="5"><b>هزینه ارسال سفارش(تومان)</b></td>
                                    <td><b>8000</b></td>
                                </tr>
                                <tr>
                                    <td class="w3-left-align" colspan="5"><b>جمع کل سفارش(تومان)</b></td>
                                    <td><b>14500</b></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="w3-row-padding cart_details">
                        <div class="w3-col m6">
                            <div><i class="fas fa-user w3-margin w3-large"></i>تحویل گیرنده: فلانی</div>
                            <div><i class="fas fa-mobile-alt w3-margin w3-large"></i>شماره تماس: 09106005555</div>
                            <div><i class="fas fa-map-marker-alt w3-margin w3-large"></i> آدرس: تهران، جمالزاده شمالی، کوچه سوسن پلاک 8 واحد4</div>
                        </div>
                        <div class="w3-col m6">
                            <div><i class="fas fa-truck w3-margin w3-large"></i>نحوه ارسال: پیک موتوری</div>
                            <div><i class="fas fa-credit-card w3-margin w3-large"></i>نحوه پرداخت: درگاه پرداخت ملت</div>
                        </div>
                    </div>

                    <div class="w3-padding-16 navar"></div>
                </div>
                <a href="<?= base_url('cart/finishing_message')?>" class="btn w3-button w3-round w3-green w3-left w3-margin-bottom"><?=lang('final_registration_and_payment')?></a>
                <button onclick="close_open('return_back', 'order_confirmation', 'coupon');" class="btn w3-button w3-round w3-red w3-left w3-margin-bottom"><?=lang('return')?></button>
            </div>
        </div>
    </div>

    <?PHP //include "blocks/pardahkt_amn.php"; ?>
    <?PHP //include "blocks/contact_us.php"; ?>
    <?PHP //include "blocks/footer.php"; ?>

    <script>
        function user_status()
        {
            var type_of_user = 'logged_in';

            var all_user_type_radio_inputs = document.querySelectorAll('input[type="radio"][name="user_type"]');

            if (all_user_type_radio_inputs.length > 0)
            {
                console.log(all_user_type_radio_inputs[0]);

                for (var i = 0; i < all_user_type_radio_inputs.length; i++)
                {
                    var temp_radio_input = all_user_type_radio_inputs[i];
                    if (temp_radio_input.checked)
                    {
                        type_of_user = temp_radio_input.value;
                    }
                }
            }

            return type_of_user;
        }

        function post_calculation(changed_el, request_is_from_region_select_field) {
            if (typeof request_is_from_region_select_field == 'undefined')
            {
                request_is_from_region_select_field = false;
            }

            var father = closest_parent(changed_el, 'tab_content');

            var goto_next_step_btn = father.querySelector('.goto_next_step_btn');
            goto_next_step_btn.disabled = true;

            //Get required items
            var address_country = document.querySelector('select[name="address_country"]');
            var address_state = document.querySelector('select[name="address_state"]');
            var address_city = document.querySelector('select[name="address_city"]');

            var shipping_region = father.querySelector('select[name="shipping_region"]');
            var shipping_region_module = closest_parent(shipping_region, 'shipping_region');

            //مقدار قیمتهایی که قبلا نمایش داده بودیم را پاک میکنیم
            var price_holder = father.querySelector('.price_holder');
            price_holder.innerHTML = '';

            //ابتدا چک میکنیم که کاربر لاگین کرده است یا میخواهد به عنوان مهمان خرید کند یا میخواهد ثبت نام کند؟
            //var type_of_user = user_status();

            //پیغامهایی که تا این لحظه نمایش داده شده اند را پاک میکنیم
            var message_holder = father.querySelector('.message_holder');
            message_holder.innerHTML = '';

            if (! request_is_from_region_select_field)
            {
                //ابتدا مقدار فیلد انتخاب region را مخفی میکنیم
                add_class(shipping_region_module, 'w3-hide');
            }

            var shipping_method = father.querySelector('select[name="shipping_method"]');

            if (shipping_method.value == '')
            {
                alert ("شما میبایست یک نوع ارسال را انتخاب نمایید.");
                return false;
            }
            else if (shipping_method.value == 'peyk_delivery' && ! request_is_from_region_select_field)
            {
                //میبایست regionها را نمایش دهیم تا کاربر بتواند از بین آنها انتخاب کند
                //ابتدا regionها را از state میگیریم و optionها را ایجاد میکنیم
                var selected_state = address_state.options[address_state.selectedIndex];
                if (selected_state.value == '')
                {
                    alert('شما میبایست برای مرحله «آدرس ارسال مرسوله» مقدار استان را مشخص نمایید.');
                    //آپشن را به حالت قبلی set میکنیم
                    shipping_method.value = '';
                    return false;
                }

                var temp_regions = selected_state.getAttribute('regions');

                temp_regions = temp_regions.split("::separator::");

                var options = '<option value="" disabled selected>لطفا منطقه را انتخاب کنید</option>';
                for (var i = 0; i < temp_regions.length; i++)
                {
                    options += '<option value="'+temp_regions[i]+'">'+temp_regions[i]+'</option>';
                }

                shipping_region.innerHTML = options;

                remove_class(shipping_region_module, 'w3-hide');

                //ادامه مراحل را متوقف میکنیم. چراکه کاربر حالت پیک را انتخاب کرده و حالا میبایست منطقه مورد نظر را نیز انتخاب کند
                return false;
            }

            add_class(father, 'on_loading');

            var send_to_server = {"req":"post_calculation", "delivery_type":shipping_method.value, "country": address_country.value,  "state": address_state.value, "city": address_city.value, "region": shipping_region.value};
            console.log(send_to_server);
            var url = base_url + 'api';

            postAjax(url, send_to_server, function(result){
                console.log('successful:', result);
                result = JSON.parse(result);
                remove_class(father, 'on_loading');

                if (result.status == 'successful')
                {
                    //هزینه با موفقیت محاسبه شده است
                    //مقدار هزینه دریافتی را به کاربر نمایش میدهیم
                    price_holder.innerHTML = 'هزینه ارسال: '+convert_to_currency(result.shipping_price)+' تومان';

                    //امکان رفتن به مرحله بعد را فعال میکنیم
                    goto_next_step_btn.disabled = false;
                }
                else
                {
                    //ذخیره سازی اطلاعات موفقیت آمیز نبوده است
                    //set message
                    message_holder.innerHTML = '<div class="alert w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">×</span><div>'+result.message+'</div></div>';
                }
            }, undefined, function (result) {
                //failure
                console.log('failure',result);
                remove_class(father, 'on_loading');

                message_holder.innerHTML = 'در ارتباط با سرور خطایی رخ داده است و فایل آپلود نشد. لطفا در صورتیکه چندمین مرتبه است این خطا را مشاهده میکنید. با مدیریت تماس بگیرید..';
            });
        }

        function set_address(oni_ke_change_shode) {
            var father = closest_parent(oni_ke_change_shode, 'add_address');
            var selected_option = oni_ke_change_shode.options[oni_ke_change_shode.selectedIndex];
            var address_title = father.querySelector('input[name="address_title"]');
            var address_first_name = father.querySelector('input[name="address_first_name"]');
            var address_last_name = father.querySelector('input[name="address_last_name"]');
            var address_mobile = father.querySelector('input[name="address_mobile"]');
            var address_tel = father.querySelector('input[name="address_tel"]');
            var address_postcode = father.querySelector('input[name="address_postcode"]');
            var address_country = father.querySelector('select[name="address_country"]');
            var address_state = father.querySelector('select[name="address_state"]');
            var address_city = father.querySelector('select[name="address_city"]');
            var complete_address = father.querySelector('textarea[name="address"]');

            if (oni_ke_change_shode.value == "new_address"){
                address_title.value = '';
                address_first_name.value = '';
                address_last_name.value = '';
                address_mobile.value = '';
                address_tel.value = '';
                address_postcode.value = '';
                address_country.value = '';
                address_state.value = '';
                address_city.value = '';
                complete_address.value = '';
            }
            else {
                address_title.value = selected_option.getAttribute('address_title');
                address_first_name.value = selected_option.getAttribute('first_name');
                address_last_name.value = selected_option.getAttribute('last_name');
                address_mobile.value = selected_option.getAttribute('mobile');
                address_tel.value = selected_option.getAttribute('tel');
                address_postcode.value = selected_option.getAttribute('postcode');
                address_country.value = selected_option.getAttribute('country');
                address_state.value = selected_option.getAttribute('state');
                address_city.value = selected_option.getAttribute('city');
                complete_address.value = selected_option.getAttribute('address');
            }

        }

        /***********************
         ** Set Accordion Tab **
         ***********************/
        function close_open(current_accordion, close_this, open_this) {
            var close_this_tab = document.getElementById(close_this);
            var open_this_tab = document.getElementById(open_this);
            var guest_user_type_radio_inputs = document.querySelector('input[type="radio"][name="user_type"][value="guest"]');

            switch (current_accordion)
            {
                case 'type_of_user':
                    if (guest_user_type_radio_inputs.checked)
                    {
                        //نیازی به تکمیل «بخش اطلاعات حساب» نیست و میبایست مستقیم به صفحه «آدرس ارسال مرسوله» برویم
                        remove_class(document.getElementById('address'), "w3-hide");
                    }
                    else
                    {
                        remove_class(open_this_tab, "w3-hide");
                    }
                    add_class(close_this_tab, "w3-hide");
                    break;
                case 'return_back_from_address':
                    if (guest_user_type_radio_inputs != null && guest_user_type_radio_inputs.checked)
                    {
                        //نیازی به تکمیل «بخش اطلاعات حساب» نیست و میبایست مستقیم به صفحه «نوع کاربر برگشت داده شویم» برویم
                        remove_class(document.getElementById('type_of_user'), "w3-hide");
                    }
                    else
                    {
                        remove_class(open_this_tab, "w3-hide");
                    }
                    add_class(close_this_tab, "w3-hide");
                    break;
                case 'return_back':
                default:
                    add_class(close_this_tab, "w3-hide");
                    remove_class(open_this_tab, "w3-hide");
                    break;
            }
        }

        /*** Set Guest Or Register User In account_information_tab ***/
        function guest_form() {
            /*var login_inputs = document.getElementsByClassName("login");
            //console.log(login_inputs);

            [].forEach.call(login_inputs, function (login_input) {
                login_input.classList.add("w3-hide");
            });*/

            var account_information = document.querySelector('.accordion_holder.account_information');
            add_class(account_information, 'w3-hide');
        }

        function register_form() {
            /*var login_inputs = document.getElementsByClassName("login");
            //console.log(login_inputs);

            [].forEach.call(login_inputs, function (login_input) {
                login_input.classList.remove("w3-hide");
            });*/

            var account_information = document.querySelector('.accordion_holder.account_information');
            remove_class(account_information, 'w3-hide');
        }

        function update_profile (clicked_btn)
        {
            var father = closest_parent(clicked_btn, 'tab_content');
            add_class(father, 'on_loading');

            //ابتدا چک میکنیم که کاربر لاگین کرده است یا میخواهد به عنوان مهمان خرید کند یا میخواهد ثبت نام کند؟
            var type_of_user = user_status();

            //پیغامهایی که تا این لحظه نمایش داده شده اند را پاک میکنیم
            var message_holder = father.querySelector('.message_holder');
            message_holder.innerHTML = '';

            //Get form with all it's data
            var form = father.querySelector('form');
            var formData = new FormData(form);
            formData.append('in_progress', type_of_user);

            var url = base_url + 'profile/edit';

            var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
            xhr.open('POST', url);
            xhr.onreadystatechange = function() {
                if (xhr.readyState>3 && xhr.status==200) {
                    //successful
                    console.log('successful',xhr.responseText);
                    remove_class(father, 'on_loading');

                    var result = JSON.parse(xhr.responseText);
                    if (result.status == 'successful')
                    {
                        //اطلاعات حساب با موفقیت ذخیره شده است
                        //فیلد username را غیرفعال میکنیم که کاربر نتواند با جلو عقب کردن آکاردئونهای progress، دوباره username جدید بسازد.
                        var username_field = form.querySelector('input[name="username"]');

                        //از مرحله دوم که کاربر دوباره برگردد به صفحه اطلاعات حساب (به صفحه آدرس برود و مجدد برگردد عقب)
                        //چون در مرحله قبل، مقدار name را از فیلد مربوطه حذف کرده ایم، بنابراین مقدار حال حاضر username برابر با null میباشد
                        if (username_field != null)
                        {
                            username_field.disabled = true;
                            username_field.removeAttribute('name');
                        }

                        //به مرحله بعدی میرویم
                        close_open('', 'account_information', 'address');
                    }
                    else
                    {
                        //ذخیره سازی اطلاعات موفقیت آمیز نبوده است
                        //set message
                        message_holder.innerHTML = '<div class="alert w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">×</span><div>'+result.message+'</div></div>';
                    }

                }
                else if (xhr.readyState == 4 && xhr.status!= 200) {
                    //failure
                    console.log('failure',xhr.responseText);
                    remove_class(father, 'on_loading');

                    message_holder.innerHTML = 'در ارتباط با سرور خطایی رخ داده است و فایل آپلود نشد. لطفا در صورتیکه چندمین مرتبه است این خطا را مشاهده میکنید. با مدیریت تماس بگیرید..';
                }
            };
            //xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.send(formData);
        }

        function update_address (clicked_btn)
        {
            var father = closest_parent(clicked_btn, 'tab_content');
            add_class(father, 'on_loading');

            //ابتدا چک میکنیم که کاربر لاگین کرده است یا میخواهد به عنوان مهمان خرید کند یا میخواهد ثبت نام کند؟
            var type_of_user = user_status();

            //پیغامهایی که تا این لحظه نمایش داده شده اند را پاک میکنیم
            var message_holder = father.querySelector('.message_holder');
            message_holder.innerHTML = '';

            //Get form with all it's data
            var form = father.querySelector('form');
            var formData = new FormData(form);
            formData.append('in_progress', type_of_user);

            //var address_status = 'new_address';

            var address_status = form.querySelector('select[name="address_status"]').value;

            var url = base_url + 'profile/new_address';

            if (address_status != 'new_address')
            {
                url = base_url + 'profile/edit_address/'+address_status;
            }

            var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
            xhr.open('POST', url);
            xhr.onreadystatechange = function() {
                if (xhr.readyState>3 && xhr.status==200) {
                    //successful
                    console.log('successful',xhr.responseText);
                    remove_class(father, 'on_loading');

                    var result = JSON.parse(xhr.responseText);
                    if (result.status == 'successful')
                    {
                        //اطلاعات حساب با موفقیت ذخیره شده است
                        //به مرحله بعدی میرویم
                        close_open('', 'address', 'shipping_method');
                    }
                    else
                    {
                        //ذخیره سازی اطلاعات موفقیت آمیز نبوده است
                        //set message
                        message_holder.innerHTML = '<div class="alert w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">×</span><div>'+result.message+'</div></div>';
                    }
                }
                else if (xhr.readyState == 4 && xhr.status!= 200) {
                    //failure
                    console.log('failure',xhr.responseText);
                    remove_class(father, 'on_loading');

                    message_holder.innerHTML = 'در ارتباط با سرور خطایی رخ داده است و فایل آپلود نشد. لطفا در صورتیکه چندمین مرتبه است این خطا را مشاهده میکنید. با مدیریت تماس بگیرید..';
                }
            };
            //xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.send(formData);
        }
    </script>
