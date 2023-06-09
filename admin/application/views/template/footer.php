<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div></div>
<div id="footer_bar" class="container-fluid fixed-bottom">
    <div class="row justify-content-between">
        <div class="col-auto">
            <small>تمامی حقوق این سایت متعلق به نشر کتاب پرگار میباشد.</small>
        </div>
        <div class="col-auto">
            <small><a class="btn btn-sm" href="<?=base_url("../"); ?>" target="_blank"><?=lang('view_site')?></a> <a class="btn btn-sm" href="<?php echo base_url("logout"); ?>"><?=lang('logout')?></a></small>
        </div>
    </div>
</div>
<!--<script src="<?PHP echo base_url("assets/js/jquery-3.2.1.slim.min.js"); ?>"></script>-->
<script src="<?PHP echo base_url("assets/js/jquery-3.3.1.min.js"); ?>"></script>
<script src="<?PHP echo base_url("assets/js/popper.min.js"); ?>"></script>
<script src="<?PHP echo base_url("assets/js/bootstrap.min.js"); ?>"></script>
<script src="<?PHP echo base_url("assets/js/bootstrap-4-navbar.js"); ?>"></script>
<script src="<?PHP echo base_url("assets/js/ckeditor/ckeditor.js"); ?>"></script>
<script src="<?PHP echo base_url("assets/js/custom.js"); ?>"></script>

</body>

</html>
