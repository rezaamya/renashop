<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<?PHP include APPPATH."views/pages/blocks/header.php"; ?>
<?PHP //include APPPATH."views/pages/blocks/main_menu.php"; ?>

<div id="finishing_message" class="w3-container">
    <!--
    <div class="w3-card-4 w3-center w3-pale-green w3-padding-64 message">
        <div class="w3-container">
            <p><b>با تشکر از خرید شما</b></p>
            <p> سفارش شما با کد 111 ثبت شد. در صورتی که عضو سایت هستید، میتوانید از طریق <a href="<?= base_url("profile")?>" class="w3-text-blue">پیگیری سفارش</a> وضعیت سفارش خود را مشاهده نمایید. </p>
        </div>
        <div class="w3-margin-top">
            <a href="<?= base_url("login")?>" class="btn w3-button w3-round w3-green w3-margin-bottom" style="width: 150px">ثبت نام</a>
            <a href="<?= base_url("")?>" class="btn w3-button w3-round w3-red w3-margin-bottom" style="width: 150px">برگشت به سایت</a>
        </div>
    </div>

    <div class="w3-card-4 w3-center w3-pale-red w3-padding-64 message">
        <div class="w3-container">
            <p><b>.متاسفانه خرید شما ناموفق بود</b></p>
            <p>لطفا مجددا امتحان نمایید و در صورتی که مبلغ سفارش از حساب شما کسر شده است، طی 72 ساعت برگشت داده خواهد شد در غیر اینصورت با همکاران ما تماس حاصل نمایید.</p>
        </div>

        <div class="w3-margin-top">
            <a href="<?= base_url("/pages/single_page/53/36")?>" class="btn w3-button w3-round w3-green w3-margin-bottom" style="width: 150px">تماس با ما</a>
            <a href="<?= base_url("")?>" class="btn w3-button w3-round w3-red w3-margin-bottom" style="width: 150px">برگشت به سایت</a>
        </div>
    </div>
    -->
    <?=$message?>
</div>

<?PHP //include APPPATH."views/pages/blocks/pardahkt_amn.php"; ?>
<?PHP //include APPPATH."views/pages/blocks/contact_us.php"; ?>
<?PHP //include APPPATH."views/pages/blocks/footer.php"; ?>
