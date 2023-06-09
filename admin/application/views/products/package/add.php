<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('save_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_new')?></button>
            <button type="button" onclick="set_task('save_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save_and_close')?></button>
            <a href="<?php echo base_url("products/packages")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>

<div class="container-fluid">
	<?PHP echo $html_output['sys_msg']; ?>
    <div class="row">
        <div class="col">
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("products/add_package/".$html_output['item_data']['id']), $attributes);
            ?>
            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label"><?=lang('title')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" name="title" placeholder="<?=lang('title')?>" value="<?php echo set_value('title',$html_output['item_data']['title']); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="description_package" class="col-sm-2 col-form-label"><?=lang('description_package')?></label>
                <div class="col-sm-10">
                    <textarea id="description_package" class="form-control" name="description_package" rows="3"><?php echo set_value('description_package',$html_output['item_data']['description_package']); ?></textarea>
                </div>
            </div>
            <div class="form-group row">
                <label for="how_to_apply_discounts" class="col-sm-2"><?=lang('how_to_apply_discounts')?></label>
                <div class="col-sm-10">
                    <select class="form-control" id="how_to_apply_discounts" name="how_to_apply_discounts" onchange="apply_discounts(this);">

                        <option value="discounted_components" <?= set_value('how_to_apply_discounts', $html_output['item_data']['how_to_apply_discounts']) == 'discounted_components' ? "selected" : ""; ?>><?=lang('discounted_components')?></option>

                        <option value="package_discount" <?= set_value('how_to_apply_discounts', $html_output['item_data']['how_to_apply_discounts']) == 'package_discount' ? "selected" : ""; ?>><?=lang('package_discount')?></option>

                    </select>
                </div>
            </div>

            <div id="box_package_discount" class="form-row <?PHP echo ($html_output['item_data']['how_to_apply_discounts'] == 'package_discount' ? "" : "d-none"); ?>">
                <div class="form-group col-md-2 offset-sm-2">
                    <label><?=lang('type_of_discount')?></label>
                    <select onchange="refresh_package_final_price ();" id="type_of_discount_on_whole_of_package" name="type_of_discount_on_whole_of_package" class="form-control">

                        <option value="percentage" <?= set_value('type_of_discount_on_whole_of_package', $html_output['item_data']['type_of_discount_on_whole_of_package']) == 'percentage' ? "selected" : ""; ?>><?=lang('percentage')?></option>

                        <option value="static_value" <?= set_value('type_of_discount_on_whole_of_package', $html_output['item_data']['type_of_discount_on_whole_of_package']) == 'static_value' ? "selected" : ""; ?>><?=lang('static_value')?></option>

                        <!--<option value="percentage" <?= set_value('type_of_discount', $html_output['item_data']['type_of_discount']) == 'percentage' ? "selected" : ""; ?>><?=lang('percentage')?></option>

                        <option value="static_value" <?= set_value('type_of_discount', $html_output['item_data']['type_of_discount']) == 'static_value' ? "selected" : ""; ?>><?=lang('static_value')?></option>-->

                    </select> 
                </div>
                <div class="form-group col-md-3 offset-sm-2 offset-md-0">
                    <label><?=lang('discounted_price')?></label>
                    <input oninput="refresh_package_final_price(); if(this.value == ''){this.value = 0;refresh_package_final_price();}" type="text" class="form-control" id="discounted_price_on_whole_of_package" name="discounted_price_on_whole_of_package" value="<?php
					if ($html_output['item_data']['discounted_price_on_whole_of_package'] == "")
					{
						$html_output['item_data']['discounted_price_on_whole_of_package'] = 0;
					}
					echo set_value('discounted_price_on_whole_of_package',$html_output['item_data']['discounted_price_on_whole_of_package']);
					?>">
                </div>
            </div>


            <div class="form-group row">
                <legend class="col-form-label col-sm-2 pt-0"><?=lang('publish')?></legend>
                <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="publish" id="publish1" value="yes" checked>
                        <label class="form-check-label" for="publish1">
                            <?=lang('yes')?>
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="publish" id="publish2" value="no">
                        <label class="form-check-label" for="publish2">
                            <?=lang('no')?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col">
                    <button type="button" class="col-md-2 offset-md-5 btn btn-success" onclick="add_product_box();"><i class="fas fa-plus"></i> <?=lang('add_product')?></button>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group col-md-4 offset-md-0">
                    <label><?=lang('package_total_price')?></label>
                    <input type="text" value="0" id="package_total_price" class="form-control" readonly>
                </div>
                <div class="form-group col-md-4 offset-md-0">
                    <label><?=lang('package_total_discount_price')?></label>
                    <input type="text" value="0" id="package_total_discount_price" class="form-control" readonly>
                </div>
                <div class="form-group col-md-4 offset-md-0">
                    <label><?=lang('package_final_price')?></label>
                    <input type="text" value="0" id="package_final_price" class="form-control" readonly>
                </div>
            </div>



            <div id="products_box_holder" class="m-1">

				<?php echo $html_output['products'];
				/*
				<div class="product_box border rounded bg-light p-2 mb-3">
					<button type="button" class="close" onclick="remove_product_box(this);">
						<span class="text-danger fa-2x align-items-sm-start">&times;</span>
					</button>
					<div class="form-row">
						<div class="form-group col-md-4">
							<label><?=lang('search_product')?></label>
                            <input type="hidden" class="product" name="product[]" />
							<input type="text" class="form-control search_product" name="search_product[]" oninput="find_products(this);">
							<div class="found_list mt-1">
							</div>
						</div>
						<div class="form-group col-md-3">
							<label><?=lang('price')?></label>
							<input type="text" value="0" class="form-control price" readonly name="price[]">
						</div>
					</div>
					<div class="form-row takhfif_box classe_felan">
						<div class="form-group col-md-4">
							<label><?=lang('type_of_discount')?></label>
							<select name="type_of_discount[]" class="form-control type_of_discount" onchange="refresh_product_final_price(this);">
								<option value="percentage"><?=lang('percentage')?></option>
								<option value="static_value"><?=lang('static_value')?></option>
							</select>
						</div>
						<div class="form-group col-md-3">
							<label><?=lang('discounted_price')?></label>
							<input type="text" value="0" class="form-control discounted_price" name="discounted_price[]" oninput="refresh_product_final_price(this); if(this.value == ''){this.value = 0;}">
						</div>
						<div class="form-group col-md-3">
							<label><?=lang('final_price')?></label>
							<input type="text" value="0" class="form-control final_price" readonly name="final_price[]">
						</div>
					</div>
				</div>
                */


			?>

            </div>

            <input type="hidden" id="task" name="task">
            </form>
        </div>
    </div>
</div>

<script>
    var timer_peyda_kardan_products;
    function find_products(oni_ke_type_shode) {
        var node_asli = oni_ke_type_shode.closest('.product_box');
        var found_list_node = node_asli.getElementsByClassName('found_list')[0];

        //تایمرهای قبلی را پاک کن تا تایمر جدید در پایینتر تنظیم شود
        clearTimeout(timer_peyda_kardan_products);

        if (oni_ke_type_shode.value.length > 1)
        {
            //بنویس در حال جستجو
            found_list_node.innerHTML = '<?=lang('searching');?>';

            //اگه بیشتر از سه کاراکتر تایپ شد، مقدار تایپ شده را به سرور بفرست تا نتایج محصولات مشابه را برگرداند
            //قبل از فراخوانی AJAX، چند لحظه تاخیر قرار بده
            timer_peyda_kardan_products = setTimeout(function(){
                //فیلد مخفیه product_id را بگیر و مقادیر آنها را برای سرور ارسال کن تا اطلاعات تکراری را بازگشت ندهد
                var current_products_list = document.getElementsByClassName('product_id');
                var current_product_list_ips = [];
                [].forEach.call(current_products_list, function(current_node,index,arr) {
                    //برای اولین بار که میخواهیم اولین محصول را به لیست اضافه کنیم
                    //مقدار value برابر با '' است. به همین دلیل میبایست مطمئن شویم مقدار تهی را در لیست IDها اضافه نمیکنیم
                    if (current_node.value != "")
                    {
                        current_product_list_ips.push(current_node.value);
                    }
                });
                console.log(current_product_list_ips.length, current_product_list_ips.join());
                console.log({"req":"find_product", "key":oni_ke_type_shode.value, "exception":current_product_list_ips.join()});
                $.ajax({
                    method: "POST",
                    url: '<?=base_url('api/index')?>',//'http://localhost/keshavarz/api/index',
                    data: {"req":"find_product", "key":oni_ke_type_shode.value, "exception":current_product_list_ips.join()},
                    success: function(result){
                        console.log("raw response:", result);
                        var mahsolat_peydashode = JSON.parse(result);
                        //("JSON response:", mahsolat_peydashode);



                        //لیست محصولاتی که از سرور آمده است را بصورت option ایجاد کن
                        var option_list = "";
                        mahsolat_peydashode.forEach(function (radife_mahsol) {
                            option_list += '<option value="'+radife_mahsol.id+'" price="'+radife_mahsol.price+'" title="'+radife_mahsol.title+'">'+radife_mahsol.title+'</option>';
                        });


                        if (option_list == "")
                        {
                            //موردی یافت نشد
                            found_list_node.innerHTML = "<?=lang('no_item_found')?>";
                        }
                        else
                        {
                            //موارد پیدا شده را بصورت لیست نمایش بده
                            found_list_node.innerHTML = '<select onclick="entekhabe_az_list_mahsolat(this.options[this.selectedIndex])" size="5" class="form-control">'+option_list+'</select>';
                        }

                    },
                    error: function(result){
                        //console.log(result);
                        //var error = '<div class="alert alert-error">server is not available. please check your connection.</div>';
                        //$('#notification_bar').append(error);
                        alert ('server is not available. please check your connection.');
                    }
                });
            }, 1000);
        }
        else
        {
            //لیست جستجوهای قبلی را مخفی کن
            found_list_node.innerHTML = '';
        }
    }

    function entekhabe_az_list_mahsolat(producte_entekhab_shode) {
        //نود اصلی باکس محصول را بگیر
        var node_asli = producte_entekhab_shode.closest('.product_box');

        //فیلد مخفیه product_id را بگیر و مقدار آنرا برابر با ID محصول انتخاب شده قرار بده
        var product_id = node_asli.getElementsByClassName('product_id')[0];
        product_id.value = producte_entekhab_shode.value;

        //قیمت را در فیلد قیمت قرار میدهیم
        var price_field = node_asli.getElementsByClassName('price')[0];
        price_field.value = producte_entekhab_shode.getAttribute("price");

        //عنوان محصول را در فیلدی که کاربر در حال تایپ کردن در آن بود، قرار میدهیم
        var search_product_field = node_asli.getElementsByClassName('search_product')[0];
        search_product_field.value = producte_entekhab_shode.getAttribute("title");

        //مقدار قیمت محصول را با توجه به نوع و مقدار تخفیف تغییر بده
        refresh_product_final_price (producte_entekhab_shode);

        //لیست بعد از انتخاب حذف میشود.
        //درصورتیکه کاربر مجدد تایپ کند، لیست دوباره ظاهر میشود (در فانکشن find_products لیست ظاهر میشود)
        var found_list_node = node_asli.getElementsByClassName('found_list')[0];
        found_list_node.innerHTML = '';
    }

    //با صدا کردن تابع refresh_product_final_price، مقدار final_price را تنظیم و تغییر میدهیم
    function refresh_product_final_price (producte_entekhab_shode)
    {
        //نود اصلی باکس محصول را بگیر
        var node_asli = producte_entekhab_shode.closest('.product_box');

        //قیمت را در فیلد قیمت قرار میدهیم
        var price_field = node_asli.getElementsByClassName('price')[0];

        //نوع تخفیف و مقدار تخفیف را میگیریم
        var type_of_discount = node_asli.getElementsByClassName('type_of_discount')[0];
        var discounted_price = node_asli.getElementsByClassName('discounted_price')[0];
        //بر اساس نوع تخفیف و مقدار آن، قیمت نهایی را محاسبه کرده و درج میکنیم
        var final_price = node_asli.getElementsByClassName('final_price')[0];
        var temp_final_price = 0;
        if (type_of_discount.value == 'percentage')
        {
            //نوع تخفیف بصورت درصدی میباشد
            temp_final_price = price_field.value - ((discounted_price.value / 100) * price_field.value);
        }
        else
        {
            //تخفیف بصورت مقدار ثابت میباشد.
            temp_final_price = price_field.value - discounted_price.value;
        }

        //با توجه به اینکه برخی موارد عدد حاصل بصورت اعشاری میشود، آنرا تبدیل به INT میکنیم
        //که فقط مقدار عددی آن باقی بماند
        temp_final_price = parseInt(temp_final_price);

        if (temp_final_price < 0)
        {
            temp_final_price = 0;
        }
        final_price.value = temp_final_price;

        refresh_package_final_price();
    }

    //در این تابع قیمت نهایی بسته را رفرش میکنیم
    function refresh_package_final_price ()
    {
        //تمامی محصولاتی که تا این لحظه در صفحه اضافه شده است را بگیر
        var hameye_product_boxha = document.getElementsByClassName('product_box');

        if (hameye_product_boxha.length == 0)
        {
            //هیچ موردی با کلاس product_box پیدا نشد

            //مبلغ کل بسته را تنظیم کن
            document.getElementById("package_total_price").value = 0;
            //مبلغ نهایی تخفیف را تنظیم کن
            document.getElementById("package_total_discount_price").value = 0;
            //مبلغ نهایی بسته را تنظیم کن
            document.getElementById("package_final_price").value = 0;
        }
        else
        {
            //نحوه اعمال تخفیف را بگیر
            var how_to_apply_discounts = document.getElementById("how_to_apply_discounts").value;

            var temp_package_total_price = 0;
            var temp_package_total_discount_price = 0;
            //فیلدهای موجود در هر باکس را مورد بررسی قرار بده
            [].forEach.call(hameye_product_boxha, function(current_node,index,arr) {
                //مقدار فیلد قیمت را بگیر
                var price = parseInt(current_node.getElementsByClassName('price')[0].value);
                temp_package_total_price += price;

                //بررسی میکنیم که اگر نحوه محاسبه تخفیف به صورت «جز به جز برای هر محصول» تعیین شده است
                //در این حالت میبایست «مبلغ کل تخفیف» را با جمع تخفیفهای ارائه شده برای هر محصول
                //بدست بیاوریم. به همین منظور مبلغ هر تخفیف را  با توجه به نوع تخفیف اعمال شده محاسبه میکنیم
                if (how_to_apply_discounts == "discounted_components")
                {
                    //مقدار درج شده در فیلد «مبلغ تخفیف» را بگیر
                    var discounted_price = parseInt(current_node.getElementsByClassName('discounted_price')[0].value);

                    //درصورتیکه نحوه اعمال تخفیف بصورت «تخفیف بر روی هر محصول» است، میبایست مبلغ تخفیف را
                    //برای هر محصول محاسبه کنیم
                    var type_of_discount = current_node.getElementsByClassName('type_of_discount')[0].value;
                    if (type_of_discount == "percentage")
                    {
                        //تخفیف هر محصول بصورت «درصدی» درنظر گرفته شده است
                        temp_package_total_discount_price += parseInt((discounted_price / 100) * price);
                    }
                    else
                    {
                        //تخفیف هر محصول بصورت «مبلغ ثابت» در نظر گرفته شده است
                        temp_package_total_discount_price += discounted_price;
                    }
                }
                else
                {
                    //نحوه اعمال تخفیف بصورت «تعیین تخفیف بر روی کل بسته» تعیین شده است
                    //به همین دلیل، اینجا نیازی نیست برای بدست آوردن «مبلغ کل تخفیف»، مبلغ تخفیف بر روی هرکدام از بسته ها را محاسبه کنیم
                }
            });


            //مبلغ نهایی بسته را اینجا تعیین میکنیم


            //مبلغ کل بسته را تنظیم کن
            document.getElementById("package_total_price").value = temp_package_total_price;

            //مبلغ کل تخفیف را تنظیم کن
            if (how_to_apply_discounts == "discounted_components")
            {
                //مبلغ نهایی تخفیف را تنظیم کن
                document.getElementById("package_total_discount_price").value = temp_package_total_discount_price;
            }
            else
            {
                //با توجه به مبلغ کل بسته و مقداری که کاربر برای تخفیف کل بسته تعیین کرده است
                //مقدار تخفیف را مشخص میکنیم

                //مقدار «مبلغ تخفیف روی کل پروژه» را بگیر
                var discounted_price_on_whole_of_package = document.getElementById("discounted_price_on_whole_of_package").value;

                //نحوه اعمال تخفیف بر روی کل بسته را بگیر
                var type_of_discount_on_whole_of_package = document.getElementById("type_of_discount_on_whole_of_package").value;
                if (type_of_discount_on_whole_of_package == "percentage")
                {
                    //تخفیف کلی روی بسته بصورت «درصدی» درنظر گرفته شده است
                    temp_package_total_discount_price = (discounted_price_on_whole_of_package / 100) * temp_package_total_price;
                }
                else
                {
                    //تخفیف کلی بر روی بسته، بصورت «مقدار ثابت» درنظر گرفته شده است
                    temp_package_total_discount_price = discounted_price_on_whole_of_package;
                }

                //اعشار را از مقدار تخفیف حذف میکنیم
                temp_package_total_discount_price = parseInt(temp_package_total_discount_price);

                //مبلغ نهایی تخفیف را تنظیم کن
                document.getElementById("package_total_discount_price").value = temp_package_total_discount_price;
            }

            //مبلغ نهایی بسته را محاسبه کن
            var temp_package_final_price = temp_package_total_price - temp_package_total_discount_price;
            if (temp_package_final_price < 0)
            {
                //درصورتیکه مقدار تخفیف بیشتر از مقدار کل بسته است! مبلغ نهایی بسته را «صفر» درنظر بگیر
                temp_package_final_price = 0;
            }
            //مبلغ نهایی بسته را تنظیم کن
            document.getElementById("package_final_price").value = temp_package_final_price;
        }
    }

    function apply_discounts(oni_ke_change_shode) {
        //این فانکشن زمانی که اعمال تخفبف بر روی کل بسته باشد فراخوانی خواهد شد
        if (oni_ke_change_shode.value=="package_discount"){
            document.getElementById('box_package_discount').classList.remove('d-none');

            //تمامی divهایی که کلاس takhfif_box دارند را مخفی کن
            var hameye_takhfif_boxha = document.getElementsByClassName('takhfif_box');

            if (hameye_takhfif_boxha.length == 0)
            {
                //هیچ موردی با کلاس takhfif_box پیدا نشد
            }
            else
            {
                //مواردی که پیدا شده است را مخفی کن
                [].forEach.call(hameye_takhfif_boxha, function(value,index,arr) {
                    value.classList.add('d-none');
                })
            }
        }
         else {
            document.getElementById('box_package_discount').classList.add('d-none');

            //تمامی divهایی که کلاس takhfif_box دارند را نمایش بده
            var hameye_takhfif_boxha = document.getElementsByClassName('takhfif_box');

            if (hameye_takhfif_boxha.length == 0)
            {
                //هیچ موردی با کلاس takhfif_box پیدا نشد
            }
            else
            {
                //مواردی که پیدا شده است را نمایش بده
                [].forEach.call(hameye_takhfif_boxha, function(value,index,arr) {
                    value.classList.remove('d-none');
                })
            }
        }

        //قیمت نهایی بسته را رفرش کن
        refresh_package_final_price ()
    }

    function add_product_box(submitted_products) {
        if (typeof submitted_products == 'undefined')
        {
            submitted_products = false;
        }
        else if (submitted_products == '')
        {
            //این حالت زمانی اتفاق میافتد که صفحه برای اولین بار باز شده است و کاربر میخواهد
            //بسته جدید اضافه کند
            //در این حالت، در انتهای صفحه در پایان Load صفحه، کدهایی که قبلا submit شده بودند را
            //برای تابع میفرستیم که بطور پیشفرض متن تهی است.
            return false;
        }
        else
        {
            //یک استرینگ بصورت json برای ما ارسال شده است
            submitted_products = JSON.parse(submitted_products);
            //console.log(submitted_products);
        }

        var visiblity_class = '';
        if (document.getElementById('how_to_apply_discounts').value == 'package_discount')
        {
            visiblity_class = 'd-none';
        }

        if (submitted_products)
        {
            submitted_products.forEach(function (submitted_product_row) {
                var selecte_pishfarz_percent = 'selected';
                var selecte_pishfarz_static_value = '';
                if (submitted_product_row.type_of_discount == "static_value"){
                    selecte_pishfarz_percent = '';
                    selecte_pishfarz_static_value = 'selected';
                }

                var node_string = '<div class="product_box border rounded bg-light p-2 mb-3"> <button type="button" class="close" onclick="remove_product_box(this);"> <span class="text-danger fa-2x align-items-sm-start">&times;</span> </button> <div class="form-row"> <div class="form-group col-md-4"> <label><?=lang('search_product')?></label> <input type="hidden" class="product_id" name="product_id[]" value ="'+submitted_product_row.product_id+'"/> <input type="text" autocomplete="off" value="'+submitted_product_row.title+'" class="form-control search_product" name="search_product[]" oninput="find_products(this);"> <div class="found_list mt-1"> </div> </div> <div class="form-group col-md-3"> <label><?=lang('price')?></label> <input type="text" value="'+submitted_product_row.price+'" class="form-control price" readonly name="price[]"> </div> </div> <div class="form-row takhfif_box '+visiblity_class+'"> <div class="form-group col-md-4"> <label><?=lang('type_of_discount')?></label> <select name="type_of_discount[]" class="form-control type_of_discount" onchange="refresh_product_final_price(this);"> <option value="percentage" '+selecte_pishfarz_percent+'><?=lang('percentage')?></option> <option value="static_value" '+selecte_pishfarz_static_value+' ><?=lang('static_value')?></option> </select> </div> <div class="form-group col-md-3"> <label><?=lang('discounted_price')?></label> <input type="text" value="'+submitted_product_row.discounted_price+'" class="form-control discounted_price" name="discounted_price[]" oninput="refresh_product_final_price(this); if(this.value == \'\'){this.value = 0;refresh_package_final_price();}"> </div> <div class="form-group col-md-3"> <label><?=lang('final_price')?></label> <input type="text" value="0" class="form-control final_price" readonly name="final_price[]"> </div> </div> </div>';

                document.getElementById("products_box_holder").insertAdjacentHTML('afterbegin', node_string);
            });

            var all_type_of_discounts = document.getElementsByClassName("type_of_discount");
            [].forEach.call(all_type_of_discounts, function(select_row,index,arr) {
                refresh_product_final_price(select_row);
            });
        }
        else
        {
            var node_string = '<div class="product_box border rounded bg-light p-2 mb-3"> <button type="button" class="close" onclick="remove_product_box(this);"> <span class="text-danger fa-2x align-items-sm-start">&times;</span> </button> <div class="form-row"> <div class="form-group col-md-4"> <label><?=lang('search_product')?></label> <input type="hidden" class="product_id" name="product_id[]" /> <input type="text" autocomplete="off" class="form-control search_product" name="search_product[]" oninput="find_products(this);"> <div class="found_list mt-1"> </div> </div> <div class="form-group col-md-3"> <label><?=lang('price')?></label> <input type="text" value="0" class="form-control price" readonly name="price[]"> </div> </div> <div class="form-row takhfif_box '+visiblity_class+'"> <div class="form-group col-md-4"> <label><?=lang('type_of_discount')?></label> <select name="type_of_discount[]" class="form-control type_of_discount" onchange="refresh_product_final_price(this);"> <option value="percentage"><?=lang('percentage')?></option> <option value="static_value"><?=lang('static_value')?></option> </select> </div> <div class="form-group col-md-3"> <label><?=lang('discounted_price')?></label> <input type="text" value="0" class="form-control discounted_price" name="discounted_price[]" oninput="refresh_product_final_price(this); if(this.value == \'\'){this.value = 0;refresh_package_final_price();}"> </div> <div class="form-group col-md-3"> <label><?=lang('final_price')?></label> <input type="text" value="0" class="form-control final_price" readonly name="final_price[]"> </div> </div> </div>';

            document.getElementById("products_box_holder").insertAdjacentHTML('afterbegin', node_string);
        }
    }

    function remove_product_box (nodi_ke_klick_shode) {
        var nod = nodi_ke_klick_shode.closest('.product_box');
        nod.parentNode.removeChild(nod);

        //قیمت نهایی بسته را رفرش کن
        refresh_package_final_price ();
    }


    <?php  //$te = json_decode($html_output['item_data']['view_product_item']); print_r($te); ?>

    window.addEventListener('load', function () {
        add_product_box('<?=$html_output['item_data']['view_product_item']?>');
    });
</script>
