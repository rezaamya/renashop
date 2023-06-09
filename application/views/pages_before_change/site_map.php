<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP include "blocks/main_menu.php"; ?>
<div id="single_page" class="w3-container">
    <div class="site_map w3-row-padding">
        <div class="w3-col m9 module_html">
            <div class="w3-row title_holder">
                <hr>
                <div class="title">نقشه سایت</div>
                <div class="w3-row">
                    <a class="w3-col s2 w3-right w3-left-align w3-small">اندازه قلم:</a>
                    <a class="w3-col s1 w3-right w3-left-align w3-small" href="#"><i class="fas fa-search-plus"></i></a>
                    <a class="w3-col s1 w3-right w3-left-align w3-small" href="#"><i class="fas fa-search-minus"></i></a>
                </div>
            </div>

            <div class="content">
				<?php echo $created_menus;?>
            </div>
        </div>
        <?PHP include "blocks/ketabe_hafte(s_slide).php"; ?>
    </div>
</div>
<?PHP include "blocks/pardahkt_amn.php"; ?>
<?PHP include "blocks/contact_us.php"; ?>
<?PHP include "blocks/footer.php"; ?>
