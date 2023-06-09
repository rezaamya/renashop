<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP //include "blocks/main_menu.php"; ?>


<div id="single_product_category">
    <form id="single_product_category_form" method="get">
        <div class="w3-row-padding">
            <div class="w3-col l9 m7 module_html">
                <div class="title_holder">
                    <hr>
                    <div class="title"><?=$product_category_title?></div>
                </div>
				<?php if($temp_products == ''){ ?>
				صفحه‌ی مورد نظر یافت نشد.
				<?php } ?>

				<?php if($show == 'yes'){ ?>
                <div class="w3-row-padding toolbar">
                    <label class="toolbar_title w3-small" style="width: 102px;"><?=lang('sort')?>:</label>
                    <select onchange="submit_form(this);" class="w3-select w3-small w3-padding-small w3-borderrrr w3-roundddd" name="sort" style="width: 115px;">
                        <option value="most_popular" <?= set_value('sort', $item_data_sort) == 'most_popular' ? "selected" : ""; ?>>محبوب ترین</option>
                        <option value="newest" <?= set_value('sort', $item_data_sort) == 'newest' ? "selected" : ""; ?>>تازه ترین</option>
                        <option value="the_oldest" <?= set_value('sort', $item_data_sort) == 'the_oldest' ? "selected" : ""; ?>>قدیمی ترین</option>
						<option value="lowest_price" <?= set_value('sort', $item_data_sort) == 'lowest_price' ? "selected" : ""; ?>>کمترین قیمت</option>
						<option value="highest_price" <?= set_value('sort', $item_data_sort) == 'highest_price' ? "selected" : ""; ?>>بیشترین قیمت</option>
                    </select>
					<select onchange="submit_form(this);" class="w3-left w3-small w3-padding-small w3-select w3-borderrrr w3-roundddd" name="per_page" style="width: 60px;">
						<option value="5" <?= set_value('per_page', $item_data_per_page) == '5' ? "selected" : ""; ?>>5</option>
						<option value="10" <?= set_value('per_page', $item_data_per_page) == '10' ? "selected" : ""; ?>>10</option>
						<option value="20" <?= set_value('per_page', $item_data_per_page) == '20' ? "selected" : ""; ?>>20</option>
						<option value="50" <?= set_value('per_page', $item_data_per_page) == '50' ? "selected" : ""; ?>>50</option>
						<option value="100" <?= set_value('per_page', $item_data_per_page) == '100' ? "selected" : ""; ?>>100</option>
					</select>
                    <label class="toolbar_title w3-left w3-small" style="width: 40px;">نمایش:</label>
                </div>
				<?php } ?>
                <div class="w3-row-padding">
                    <?php echo $temp_products; ?>
                </div>
                <?= $pagination; ?>
            </div>
			<?php if($show == 'yes'){ ?>
            <div class="w3-col l3 m5 module_html search_box w3-responsive w3-margin-bottom">
                <div class="title_holder">
                    <hr>
                    <div class="title"><?=lang('search')?></div>
                </div>
                <div class="border w3-padding-small">
                    <div>
                        <input class="search_input w3-padding-small w3-borderrrr w3-roundddd" name="search" type="text" value="<?php echo set_value('search',$search); ?>" placeholder="عبارت مورد نظر خود را جستجو کنید.">
                    </div>

                    <label class="w3-row w3-small w3-margin-bottom checkbox_container">
                        <input type="checkbox" name="products_available" value="yes" <?= set_value('products_available', $products_available) == 'yes' ? "checked" : ""; ?>>
						<span class="checkmark"></span>
						<span>فقط کالاهای موجود</span>
                    </label>
                    <!--<div class="w3-light-gray w3-padding-small">
                        <a href="#" class="w3-left w3-small w3-text-red" onclick="this.parentElement.style.display='none'">پاک کردن همه</a>
                        <label class="w3-small">انتخاب شما:</label>

                        <div class="selected">
                            <div class="w3-border w3-white fit">
                                <span>فقط کالاهای موجود</span>
                                <button onclick="this.parentElement.style.display='none'">&times</button>
                            </div>
                            <div class="w3-border w3-white fit">
                                <span>قانون</span>
                                <button onclick="this.parentElement.style.display='none'">&times</button>
                            </div>
                            <div class="w3-border w3-white fit">
                                <span>جزا</span>
                                <button onclick="this.parentElement.style.display='none'">&times</button>
                            </div>
                        </div>
                    </div>-->
                    <?php echo $search_field; ?>
                    <button type="submit" class="w3-button w3-amberrrr w3-gray w3-hover-dark-gray w3-text-black " style="width: 100%"><?=lang('search')?></button>
                </div>
            </div>
			<?php } ?>
        </div>
    </form>
    <script>
        function submit_form(sort_type) {
            var form = document.getElementById('single_product_category_form');
            //console.log(form);
            form.submit();
        }
    </script>
</div>

<?PHP //include "blocks/pardahkt_amn.php"; ?>
<?PHP //include "blocks/contact_us.php"; ?>
<?PHP //include "blocks/footer.php"; ?>
