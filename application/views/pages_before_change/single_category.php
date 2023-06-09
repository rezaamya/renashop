<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP include "blocks/main_menu.php"; ?>


<div id="single_category">
    <div class="w3-row-padding">
        <form class="w3-col s12 m9 module_html">
            <div class="content">
                <div class="title_holder">
                    <hr>
                    <div class="title"><?php echo $category_name; ?></div>
                </div>
                <select class="w3-left w3-small w3-padding-small w3-margin-left w3-select w3-border w3-round" name="per_page" style="width: 65px;">
                    <option value="5" <?= set_value('per_page', $item_data_per_page) == '5' ? "selected" : ""; ?>>5</option>
                    <option value="10" <?= set_value('per_page', $item_data_per_page) == '10' ? "selected" : ""; ?>>10</option>
                    <option value="20" <?= set_value('per_page', $item_data_per_page) == '20' ? "selected" : ""; ?>>20</option>
                    <option value="50" <?= set_value('per_page', $item_data_per_page) == '50' ? "selected" : ""; ?>>50</option>
                    <option value="100" <?= set_value('per_page', $item_data_per_page) == '100' ? "selected" : ""; ?>>100</option>
                </select>
                <?= $content; ?>
				<?= $pagination;?>

				<?php if($content == ''){ ?>
					صفحه‌ی مورد نظر یافت نشد.
				<?php }?>

				<?php if($content != ''){ ?>

				<?php }?>

            </div>
            <input type="hidden" id="task" name="task">
        </form>
        <div class="w3-col m3">
            <!-- KEtabe hafte & Takhfife Hafte -->
            <position id="position_12">position_12</position>
        </div>
    </div>
</div>

<?PHP include "blocks/pardahkt_amn.php"; ?>
<?PHP include "blocks/contact_us.php"; ?>
<?PHP include "blocks/footer.php"; ?>
