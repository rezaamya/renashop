<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP
$show_type = 'normal';
if (isset($_GET["show_type"]))
{
	$show_type = $_GET["show_type"];
}
?>

<div id="footer" class="w3-container w3-tiny w3-black w3-text-gray <?=$show_type?>">
	<div class="w3-row w3-padding-small">
		<div class="w3-col s6">
			<div>تمامی حقوق برای کتاب پرگار محفوظ میباشد.</div>
		</div>
		<div class="w3-col s6 w3-left-align">
			<div>طراحی سایت و اجرا: <a href="http://www.amya.ir" target="_blank">AMYA</a></div>
		</div>
	</div>
</div>

<div class="added_to_cart_popup_holder"></div>

<div id="overlay_for_sidebars"></div>

</body>
<script src="<?PHP echo base_url("assets/js/swiper.min.js"); ?>"></script>
<script src="<?PHP echo base_url("assets/js/clamp.min.js"); ?>"></script>
<script src="<?PHP echo base_url("assets/js/custom.js"); ?>"></script>

<!-- Initialize Swiper inam ke tage scriptiey ke baes mishe slide shoro be kar kone. dar vaghe tanzimate slide inja anjam mishe -->
<script>
    <?PHP echo isset($bottom_scripts) ? $bottom_scripts : '';?>
</script>

<!-- Initialize Swiper -->
<script>
	/*var swiper = new Swiper('.swiper-container', {
		autoHeight: true, //enable auto height
		spaceBetween: 20,
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
	});*/
</script>

</html>
