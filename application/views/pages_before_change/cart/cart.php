<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include(APPPATH.'views/pages/blocks/header.php'); ?>
<?PHP include(APPPATH.'views/pages/blocks/main_menu.php'); ?>

<div id="cart" class="w3-container">
    <div>
        <div id="order_list" class="w3-row-padding">
            <div class="w3-row">
                <p class="w3-col w3-center">
                    <b>مشتری محترم، جهت خرید محصول عملیات ثبت سفارش را تا انتها انجام دهید.</b>
                </p>
            </div>
			<?php if($this->session->userdata('cart') == null){?>
				<div class="alert w3-pale-red w3-round w3-margin">
					<div>
						<p>هیچ محصولی در سبد خرید شما وجود ندارد.</p>
					</div>
				</div>
			<?php
			}?>
            <div class="w3-card-4">
                <div class="w3-row content w3-white">
                    <div class="w3-container w3-col">
                        <div class="w3-responsive">
                            <table id="cart_table" class="cart_table w3-table w3-bordered">
                                <tr>
                                    <th>تصویر محصول</th>
                                    <th  class="description_cart">شرح سفارش</th>
                                    <th>تعداد</th>
                                    <th>قیمت واحد (تومان)</th>
                                    <th>تخفیف (تومان)</th>
                                    <th>قیمت نهایی (تومان)</th>
                                    <th></th>
                                </tr>
                                <?php echo $product_in_cart;?>
                                <!--<tr class="product_holder product_item_37" product_title="PRODUCT_TITLE" product_link="PRODUCT_LINK" product_price="100" product_id="37" attr='{"tedad":1}' product_first_image_src="PRODUCT_FIRST_IMAGE_SRC">
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
                                    <td>
                                        <div class="number_incrementer">
                                            <div class="btns">
                                                <div class="add" onclick="refresh_incrementer(this, 'add', 'cart_page');">+</div>
                                                <div class="minus" onclick="refresh_incrementer(this, 'minus', 'cart_page');">-</div>
                                            </div>
                                            <div class="input_holder">
                                                <input class="w3-input" value="1" type="text">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="unit_price">2000</td>
                                    <td class="discount">500</td>
                                    <td class="final_item_price">1500</td>
                                    <td class="icon_bar">
                                        <a href="#"><i class="fas fa-pen-square w3-text-blue w3-left" onclick="closest_parent(this, 'product_holder').getElementsByClassName('edit_modal')[0].style.display = 'block';"></i></a>
                                        <a href="#"><i class="fas fa-window-close w3-text-red w3-left" onclick="closest_parent(this, 'product_holder').getElementsByClassName('delete_modal')[0].style.display = 'block';"></i></a>
                                        <div class="w3-modal edit_modal">
                                            <div class="w3-modal-content">
                                                <header class="w3-container w3-deep-purple">
        <span onclick="closest_parent(this, 'edit_modal').style.display='none';"
              class="w3-button w3-display-topleft">&times;</span>
                                                    <h5>ویرایش سفارش</h5>
                                                </header>
                                                <div class="w3-container">
                                                    <div class="w3-margin-top w3-margin-bottom ">
                                                        <iframe src="http://amya.ir/demo/keshavarz/client/pages/single_product/7/15?show_type=raw" height="400px" width="100%" style="border:none;"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w3-modal delete_modal">
                                            <div class="w3-modal-content">
                                                <header class="w3-container w3-red">
        <span onclick="closest_parent(this, 'delete_modal').style.display='none';"
              class="w3-button w3-display-topleft">&times;</span>
                                                    <h5>حذف سفارش</h5>
                                                </header>
                                                <div class="w3-container">
                                                    <p> آیا از حذف سفارش خود مطمئن هستید؟</p>
                                                    <button onclick="closest_parent(this, 'delete_modal').style.display='none'" type="button" class="w3-button w3-red w3-left w3-round w3-margin-bottom">لغو</button>
                                                    <button type="button" class="w3-button w3-round w3-margin-bottom w3-blue margin-left w3-left" onclick="remove_from_cart(PRODUCT_ID); closest_parent(this, 'delete_modal').style.display='none';">بله</button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                -->
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><b>مبلغ سفارش (تومان)</b></td>
                                    <td class="total_sum" total_sum="<?php echo $main_total_price; ?>"> <b><?php echo $total_price; ?></b></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <p>
                <a href="<?= base_url("cart/progress")?>" class="btn w3-button w3-round w3-green w3-left w3-margin-bottom">ثبت سفارش</a>
                <a href="<?= base_url("")?>"  class="btn w3-button w3-round w3-red w3-left w3-margin-bottom">برگشت به خرید</a>
            </p>
        </div>
    </div>
</div>

<?PHP include APPPATH."views/pages/blocks/pardahkt_amn.php"; ?>
<?PHP include APPPATH."views/pages/blocks/contact_us.php"; ?>
<?PHP include APPPATH."views/pages/blocks/footer.php"; ?>
