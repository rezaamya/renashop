<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!-- Side bar toolbar -->
<div id="side_toolbars">
	<div class="logo">
		<a href="<?=base_url()?>"><img src="assets/images/pargar_book_logo.png"></a>
	</div>
	<div class="open_menu_btn" onclick="toggle_sidebar('main_menu_holder');">
		<i class="fas fa-bars"></i>
	</div>
	<div class="shopping_cart" onclick="toggle_sidebar('shopping_cart_holder');">
		<div id="how_many_itmes_is_in_cart_menu_counter">
			<span>0</span>
		</div>
		<i class="far fa-shopping-basket"></i>
	</div>
	<div class="social_media_icon">
		<i class="fab fa-instagram"></i>
	</div>
	<div class="social_media_icon">
		<i class="fab fa-telegram-plane"></i>
	</div>
</div>

<div id="main_menu_holder" class="sidebar_holder">
	<i onclick="toggle_sidebar('main_menu_holder');" class="far fa-times close_btn"></i>
	<!-- Menu Position-->
	<position type="raw" class="w3-hide-small" id="position_3">position_3</position>
</div>

<div id="shopping_cart_holder" class="sidebar_holder">
	<i onclick="toggle_sidebar('shopping_cart_holder');" class="far fa-times close_btn"></i>
	<!-- sabad_kharid -->
	<position id="position_8" type="raw">position_8</position>
</div>

<!--<div id="section_1" class="w3-container">
    <div class="w3-row">
        <div class="w3-col m4">
            <position id="position_1" type="raw">position_1</position>
        </div>
        <div class="w3-col m8 w3-left-align margin-bottom">
            <position class="w3-row w3-margin-top" type="raw" id="position_2">position_2</position>
        </div>
    </div>
</div>-->
