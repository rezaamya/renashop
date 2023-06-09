<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP include "blocks/main_menu.php"; ?>
<div id="submit_complaint" class="w3-container">
    <div class="w3-row-padding">
        <div class="line-height">
            <div><i class="fas fa-angle-double-left"></i>
                لطفا انتقادات، پیشنهادات و شکایات خود را از طریق فرم زیر با ما در میان بگذارید.
            </div>
        </div>
        <form action="" method="post">
            <div class="w3-row-padding">
                <div class="w3-half">
                    <input name="name" class="w3-input w3-border" type="text" placeholder="نام خود را وارد نمایید." value="">
                </div>
                <div class="w3-half w3-round">
                    <input name="email" class="w3-input w3-border" type="text" placeholder="ایمیل خود را وارد نمایید.">
                </div>
            </div>
            <div class="w3-container">
                <div class="w3-row">
                    <textarea name="message" class="w3-input w3-border textarea" type="textarea" rows="5" placeholder="شکایات، انتقادات و پیشنهادات  خود را وارد نمایید."></textarea>
                </div>
                <div class="w3-row">
                    <button class="w3-left submit" type="submit">ارسال</button>
                </div>
            </div>
        </form>
        <?PHP include "blocks/ketabe_hafte(s_slide).php"; ?>
    </div>

</div>
<?PHP include "blocks/pardahkt_amn.php"; ?>
<?PHP include "blocks/contact_us.php"; ?>
<?PHP include "blocks/footer.php"; ?>
