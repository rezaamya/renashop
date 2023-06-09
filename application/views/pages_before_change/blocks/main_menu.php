<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div id="section_2" class="w3-container">
    <div class="w3-row">
        <div class="w3-col m9 s6">
            <!-- Menu Position-->
            <position type="raw" class="w3-hide-small" id="position_3">position_3</position>

            <a href="#" class="w3-button menu_button w3-hide-large w3-hide-medium" onclick="show_hide();"><i class="fas fa-bars w3-text-white w3-xlarge"></i></a>
        </div>
        <div class="w3-col m3 s6 w3-left-align">
            <!-- sabad_kharid -->
            <position id="position_8" type="raw">position_8</position>
        </div>
    </div>
    <div class="w3-row">
        <position type="raw" class="mobile_menu w3-hide-large w3-hide-medium w3-hide" id="position_3_1">position_3</position>
    </div>
</div>
<script>
    function show_hide() {
        var mobile_menu = document.getElementById("position_3_1");
        console.log(mobile_menu);
        if (mobile_menu.className.indexOf("w3-show") == -1){
            mobile_menu.className += " w3-show";
        }
        else {
            mobile_menu.className = mobile_menu.className.replace(" w3-show", "");
        }
    }
</script>