<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP //include "blocks/main_menu.php"; ?>

<div id="single_page">
    <div class="w3-row-padding">
		<div class="w3-col m2">&nbsp;</div>
        <div class="w3-col m9999 m8 module_html">
            <div class="w3-row title_holder">
                <hr>
                <div class="title"><?php echo $title;?></div>
                <div class="w3-row">
                    <a class="w3-col s2 w3-right w3-left-align w3-small">اندازه قلم:</a>
                    <a href="javascript:void(0)" onclick="large_font(this);" class="w3-col s1 w3-right w3-left-align w3-small" href="#"><i class="fas fa-search-plus"></i></a>
                    <a href="javascript:void(0)" onclick="small_font(this);" class="w3-col s1 w3-right w3-left-align w3-small" href="#"><i class="fas fa-search-minus"></i></a>
                </div>
            </div>

			<?php if($title != ''){?>

			<?php }?>
            <div class="content"><?php echo $full_content;?></div>
            <div class="w3-row">
                <div class="w3-bar bottom_link">
					<?php if($title != ''){?>
                    <a class="w3-bar-item w3-right">محتوای مرتبط با این بخش:</a>
					<?php }?>

					<?php if($title == ''){?>
					<div>صفحه‌ی مورد نظر یافت نشد.</div>
					<?php }?>

                    <?php if($is_next != 0){echo '<a href=';}?><?php if($is_next != 0){echo base_url('pages/single_page/'.$next_article_id."/".$next_article_menu_id);}?> <?php if($is_next != 0){echo 'class="w3-bar-item w3-right">'.$next_title.'</a>';}?>

                    <?php if($is_previous != 0){echo '<a href=';}?><?php if($is_previous != 0){echo base_url('pages/single_page/'.$previous_article_id."/".$previous_article_menu_id);}?> <?php if($is_previous != 0){echo 'class="w3-bar-item w3-right">'.$previous_title.'</a>';}?>

                </div>
            </div>
        </div>
        <?PHP include "blocks/ketabe_hafte(s_slide).php"; ?>
    </div>

</div>

<?PHP //include "blocks/pardahkt_amn.php"; ?>
<?PHP //include "blocks/contact_us.php"; ?>
<?PHP //include "blocks/footer.php"; ?>
