<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="shopping_cart">
    <div class="shopping_cart_btn" onclick="toggle_class(this.parentElement, 'open')">
        <i class="fas fa-cart-arrow-down"></i> <span class="items_inside_the_cart">0</span> کالا - <span class="total_price">0</span> تومان
    </div>
    <div class="w3-card-4 shopping_cart_items_holder">

        <table class="items">
            <thead>
            <tr class="header_row">
                <td class="remove_btn">حذف</td>
                <td class="photo">تصویر</td>
                <td class="title">عنوان</td>
                <td class="count">تعداد</td>
                <td class="price">قیمت (تومان)</td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="5">موردی وجود ندارد</td>
            </tr>
            </tbody>
        </table>

        <div class="calculation">
            جمع: <span class="total_sum" total_sum="0">0</span> تومان
        </div>

        <div class="decision_btns">
            <a href="<?= base_url("cart")?>" class="w3-button w3-purple"><i class="fas fa-shopping-cart"></i> مشاهده سبد خرید</a> <a href="<?= base_url("cart/progress")?>" class="w3-button w3-purple"><i class="far fa-credit-card"></i> تسویه حساب</a>
        </div>
    </div>
</div>

<?PHP if ($this->session->has_userdata('cart')) { ?>
    <script>
        if(window.addEventListener){
            window.addEventListener('DOMContentLoaded', function () {
                var current_cart = '<?=json_encode($this->session->userdata('cart'))?>';
                current_cart = JSON.parse(current_cart);
                refresh_cart(current_cart);
            })
        }else{
            window.attachEvent('onload', function () {
                var current_cart = '<?=json_encode($this->session->userdata('cart'))?>';
                current_cart = JSON.parse(current_cart);
                refresh_cart(current_cart);
            })
        }
    </script>
<?PHP } ?>
