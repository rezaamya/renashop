<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP //include "blocks/main_menu.php"; ?>


<div id="cancel_form">
    <form class="w3-container" action="/action_page.php" method="post">
        <div class="w3-row-padding w3-margin-top">
            <span>لطفا علت لغو سفارش خود را در کادر پایین تشریح نمایید.</span>
            <span class="w3-button w3-left w3-round w3-red w3-margin-bottom" onclick="this.parentElement.style.display='none';">&times;</span>
            <textarea class="w3-input w3-padding w3-border w3-round w3-margin-bottom" name="description" type="textarea" rows="3"></textarea>
        </div>
    </form>
</div>

<?PHP //include "blocks/pardahkt_amn.php"; ?>
<?PHP //include "blocks/contact_us.php"; ?>
<?PHP //include "blocks/footer.php"; ?>
