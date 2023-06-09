<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP
$show_type = 'normal';
if (isset($_GET["show_type"]))
{
    $show_type = $_GET["show_type"];
}

if ($show_type != 'raw')
{
    include "blocks/header.php";
    include "blocks/main_menu.php";
}
?>

<div id="product_section">
    <?PHP echo $product_html_container; ?>
</div>


<script>
    function add_single_product_to_cart(product_id) {
        var is_any_option_required = false;
        var product_attributes = {};

        //محصول پایه (base_product) محصولیست که سایر آپشنها برای آن ایجاد شده اند
        //به عنوان مثال، اگر مدیر سایت در حال فروش یک کتاب میباشد و در کنار آن PDF زبانهای مختلف آنرا نیز
        //به عنوان Option جانبی میفروشد، محصول پایه میشود کتاب و سایر موارد میشوند optionها
        var base_product = document.getElementsByClassName('base_product')[0];

        var options_holder = document.getElementById('options_holder');
        if (options_holder)
        {
            remove_class(options_holder, 'required_error');

            //همه optionهای موجود را انتخاب میکنیم که وضعیت آنها را بررسی کنیم
            var all_options_fields = document.querySelectorAll('#options_holder .option');
            var is_any_option_selected = false;

            all_options_fields.forEach(function (option_field) {
                var option_holder = closest_parent(option_field, 'option_holder');
                //هر فیلد Option یک سری خصوصیتها دارد که مانند زیر میباشند
                //تمامی خصوصیت های زیر بجز option_type برای فیلد Select بر روی تگ Option قرار گرفته اند
                //option_type="checkbox" onchange="refresh_options_effects();" value="yes" can_option_be_purchased_separately="no" is_option_required="no" option_point_situation="-" option_point="0" option_price_situation="-" option_price="0" option_weight_situation="-" option_weight="0" reduce_total_inventory="yes"
                var option_type = option_field.getAttribute('option_type');

                var option_is_selected = false;
                var is_option_required;

                //همیشه ابتدا کلاس اجباری بودن فیلد را حذف میکنیم، تا در صورتیکه قبلا این کلاس را به هرکدام از آپشنها داده بودیم، حذف شود
                //و بتوانیم در شرطهای آتی، مشخص کنیم که نیاز هست کلاس مذکور مجددا اضافه شود یا خیر
                remove_class(option_field, 'required_field');
                remove_class(option_holder, 'required_field');
                if (option_type == 'checkbox')
                {
                    remove_class(option_field.parentElement, 'required_field');
                }

                switch (option_type)
                {
                    case 'single_entry':
                    case 'multiple_entry':
                    case 'textarea':
                        is_option_required = option_field.getAttribute('is_option_required');

                        if (option_field.value !== '')
                        {
                            //موردی توسط کاربر تایپ شده است، بنابراین این Option فعال است و توسط کاربر انتخاب شده است
                            option_is_selected = true;
                            is_any_option_selected = true;
                        }
                        else if (is_option_required == 'yes')
                        {
                            //آپشن اجباریست اما توسط کاربر تعیین نشده است
                            add_class(option_holder, 'required_field');
                            is_any_option_required = true;
                        }
                        break;
                    case 'upload':
                        is_option_required = option_field.getAttribute('is_option_required');

                        if (option_field.value !== '')
                        {
                            //موردی توسط کاربر تایپ شده است، بنابراین این Option فعال است و توسط کاربر انتخاب شده است
                            option_is_selected = true;
                            is_any_option_selected = true;
                        }
                        else if (is_option_required == 'yes')
                        {
                            //آپشن اجباریست اما توسط کاربر تعیین نشده است
                            add_class(option_holder, 'required_field');
                            is_any_option_required = true;
                        }
                        break;
                    case 'select':
                        var BreakException = {};

                        try {
                            [].forEach.call(option_field.options, function(temp_option_field){
                                var temp_is_required = temp_option_field.getAttribute('is_option_required');

                                if (temp_is_required == 'yes')
                                {
                                    is_option_required = 'yes';
                                    throw BreakException
                                }
                            });
                        } catch (e) {
                            if (e !== BreakException) throw e;
                        }

                        if (option_field.value !== '')
                        {
                            //موردی توسط کاربر تایپ شده است، بنابراین این Option فعال است و توسط کاربر انتخاب شده است
                            option_is_selected = true;
                            is_any_option_selected = true;
                        }
                        else if (is_option_required == 'yes')
                        {
                            //آپشن اجباریست اما توسط کاربر تعیین نشده است
                            add_class(option_holder, 'required_field');
                            is_any_option_required = true;
                        }
                        break;
                    case 'checkbox':
                        is_option_required = option_field.getAttribute('is_option_required');

                        if (option_field.checked)
                        {
                            //این Option فعال است و توسط کاربر انتخاب شده است
                            option_is_selected = true;
                            is_any_option_selected = true;
                        }
                        else if (is_option_required == 'yes')
                        {
                            //آپشن اجباریست اما توسط کاربر تعیین نشده است
                            add_class(option_field.parentElement, 'required_field');
                            is_any_option_required = true;
                        }
                        break;
                    case 'file':
                        is_option_required = option_field.getAttribute('is_option_required');

                        if (option_field.checked)
                        {
                            //موردی توسط کاربر تایپ شده است، بنابراین این Option فعال است و توسط کاربر انتخاب شده است
                            option_is_selected = true;
                            is_any_option_selected = true;
                        }
                        else if (is_option_required == 'yes')
                        {
                            //آپشن اجباریست اما توسط کاربر تعیین نشده است
                            add_class(option_holder, 'required_field');
                            is_any_option_required = true;
                        }
                        break;
                }

                if (option_is_selected)
                {
                    var temp_index = option_field.name;

                    //فاصله های اضافه را حذف میکنیم و enterها را به ::new_line:: تبدیل میکنیم
                    var temp_fields_array = [];
                    var temp_value = option_field.value;
                    temp_value = temp_value.replace(/\r\n/g, "\n");
                    temp_value = temp_value.replace(/\r/g, "\n");
                    temp_value = temp_value.replace("  ", " ");//همه فاصلههای بیشتر از یک را تبدیل به یکی میکنیم
                    temp_value = temp_value.split('\n');

                    temp_value.forEach(function (multiple_case_row) {
                        //console.log(multiple_case_row);
                        if (multiple_case_row != "" && multiple_case_row != " ")
                        {
                            //تمامی فاصله های قبل و بعد از نوشته را حذف میکنیم
                            multiple_case_row = multiple_case_row.replace(/^\s+|\s+$/gm,'');
                            temp_fields_array.push(multiple_case_row);
                        }
                    });

                    var temp_value = temp_fields_array.join("::new_line::");
                    //console.log(field_value);

                    if (option_type == 'select')
                    {
                        option_field = option_field.options[option_field.selectedIndex];
                    }

                    if (option_type == 'checkbox')
                    {
                        //هرکدام از checkboxها یک index دارند که با توجه به index، آنها را در یک object جداگانه ذخیره میکنیم
                        if (! product_attributes[temp_index])
                        {
                            product_attributes[temp_index] = {};
                        }
                        product_attributes[temp_index][option_field.getAttribute('index')] = temp_value;
                    }
                    else
                    {
                        product_attributes[temp_index] = temp_value;
                    }
                }
            });
        }

        if (is_any_option_required)
        {
            //حداقل یک آپشن وجود دارد که اجباری بوده و توسط کاربر تعیین وضعیت نشده است
            //امکان ثبت کالا را متوقف کرده و پیغام خطای اجباری بودن را به کاربر نمایش میدهیم
            add_class(options_holder, 'required_error');
        }
        else
        {
            //هیچ فیلد اجباری ای وجود ندارد که کاربر وضعیت آنرا تعیین نکرده باشد
            //بنابراین میتوانیم اطلاعات درخواست را به سرور ارسال کنیم

            //فیلد تعیین کننده تعداد محصول را میگیریم تا تعداد درخواستی کاربر را دریافت کنیم
            product_attributes.tedad = document.querySelector('.number_incrementer input').value;

            if (! base_product.checked)
            {
                //محصول پایه خریداری نشده است
                product_attributes.base_product = 'not_sold';
            }

            //{"req":"add_to_cart", "id":product_id, 'attr':product_attributes};
            //
            product_attributes = JSON.stringify(product_attributes);

            var send_to_server = {"req":"add_to_cart", "id":product_id, 'attr':product_attributes, 'replace_all_attrs': true};
            console.log(send_to_server);
            //console.log(JSON.stringify(send_to_server));
            var url = base_url + 'api/index';

            postAjax(url, send_to_server, function(result){
                console.log(result);

                /*if (typeof pp.callback != 'undefined')
                {
                    pp.callback (result);
                }
                */
                result = JSON.parse(result);
                refresh_cart (result.cart, result.added);

            });
        }

        /*if (base_product_is_selectable)
        {
            //حداقل یک option فعال هست که به کاربر اجازه خرید جداگانه را میدهد
            base_product.disabled = false;
        }
        else
        {
            base_product.disabled = true;
        }

        if (base_product_should_check)
        {
            //حداقل یک option فعال است که میبایست حتما base_product خریداری شود تا این آپشن در اختیار کاربر قرار بگیرد
            //بنابراین باید در این حالت حتما base_product را فعال کنیم
            base_product.checked = true;
        }

        if (!is_any_option_selected)
        {
            //هیچکدام از optionها توسط کاربر انتخاب نشده است
            base_product.checked = true;
        }*/
    }

    function refresh_options_effects() {
        //محصول پایه (base_product) محصولیست که سایر آپشنها برای آن ایجاد شده اند
        //به عنوان مثال، اگر مدیر سایت در حال فروش یک کتاب میباشد و در کنار آن PDF زبانهای مختلف آنرا نیز
        //به عنوان Option جانبی میفروشد، محصول پایه میشود کتاب و سایر موارد میشوند optionها
        var base_product = document.getElementsByClassName('base_product')[0];
        var base_product_should_check = false;
        var base_product_is_selectable = false;

        //همه optionهای موجود را انتخاب میکنیم که وضعیت آنها را بررسی کنیم
        var all_options_fields = document.querySelectorAll('#options_holder .option');
        var is_any_option_selected = false;

        //میبایست در انتها به قیمت نهایی محصول، قیمت خصوصیت های انتخاب شده را اضافه کنیم
        var temp_total_price_for_selected_option = 0;
        all_options_fields.forEach(function (option_field) {
            //هر فیلد Option یک سری خصوصیتها دارد که مانند زیر میباشند
            //option_type="checkbox" onchange="refresh_options_effects();" value="yes" can_option_be_purchased_separately="no" is_option_required="no" option_point_situation="-" option_point="0" option_price_situation="-" option_price="0" option_weight_situation="-" option_weight="0" reduce_total_inventory="yes"
            var option_type = option_field.getAttribute('option_type');

            var option_is_selected = false;

            switch (option_type)
            {
                case 'single_entry':
                case 'multiple_entry':
                case 'textarea':
                case 'select':
                case 'upload':
                    if (option_field.value !== '')
                    {
                        //موردی توسط کاربر تایپ شده است، بنابراین این Option فعال است و توسط کاربر انتخاب شده است
                        option_is_selected = true;
                        is_any_option_selected = true;
                    }
                    break;
                case 'checkbox':
                case 'file':
                    if (option_field.checked)
                    {
                        //موردی توسط کاربر تایپ شده است، بنابراین این Option فعال است و توسط کاربر انتخاب شده است
                        option_is_selected = true;
                        is_any_option_selected = true;
                    }
                    break;
            }

            if (option_is_selected)
            {
                if (option_type == 'select')
                {
                    option_field = option_field.options[option_field.selectedIndex];
                }

                var can_option_be_purchased_separately = option_field.getAttribute('can_option_be_purchased_separately');
                if (can_option_be_purchased_separately == 'yes')
                {
                    base_product_is_selectable = true;
                }
                else if (can_option_be_purchased_separately == 'no')
                {
                    base_product_should_check = true;
                }

                var option_price_situation = option_field.getAttribute('option_price_situation');
                var option_price = parseInt(option_field.getAttribute('option_price'));

                if (option_price_situation == "+")
                {
                    temp_total_price_for_selected_option += option_price;
                }
                else
                {
                    temp_total_price_for_selected_option -= option_price;
                }
            }
        });

        if (base_product_is_selectable)
        {
            //حداقل یک option فعال هست که به کاربر اجازه خرید جداگانه را میدهد
            base_product.disabled = false;
        }
        else
        {
            base_product.disabled = true;
        }

        if (base_product_should_check)
        {
            //حداقل یک option فعال است که میبایست حتما base_product خریداری شود تا این آپشن در اختیار کاربر قرار بگیرد
            //بنابراین باید در این حالت حتما base_product را فعال کنیم
            base_product.checked = true;
        }

        if (!is_any_option_selected)
        {
            //هیچکدام از optionها توسط کاربر انتخاب نشده است
            /*if(BASE_ITEM_IS_NOT_AVAILABE_IN_STOCK)
            {
                //محصول اصلی در انبار موجود نمیباشد. بنابراین کاربر نمیتواند محصول را به تنهایی سفارش دهد و حتما میبایست برخی از Optionهای آنرا
                //انتخاب کند
            }
            else
            {*/
                //محصول اصلی در انبار موجود میباشد و کاربر میتواند آنرا به تنهایی تهیه کند
                //بنابراین ازآنجائیکه هیچ optionای انتخاب نشده است، محصول اصلی میبایست تیک داشته باشد
                base_product.checked = true;
            //}
        }

        //قیمت اصلی محصول را میگیریم که بر اساس خصوصیت هایی که انتخاب شده اند، آنرا افزایش یا کاهش دهیم
        var father = document.getElementById('product_section');

        var price_holder = father.querySelector('.price_holder');
        var discount_price_holder = father.querySelector('.discount_price_holder');

        var discounted_price_node = father.querySelector('.discounted_price');
        var discounted_price = discounted_price_node.innerHTML;
        discounted_price = discounted_price.replace(/,/g, '');
        discounted_price = parseInt(discounted_price);

        if (! discounted_price_node.getAttribute('discounted_price'))
        {
            discounted_price_node.setAttribute('discounted_price', discounted_price);
        }

        if (base_product.checked)
        {
            remove_class(price_holder, 'w3-hide');
            remove_class(discount_price_holder, 'w3-hide');
            discounted_price = parseInt(discounted_price_node.getAttribute('discounted_price'));
        }
        else
        {
            add_class(price_holder, 'w3-hide');
            add_class(discount_price_holder, 'w3-hide');
            discounted_price = 0;
        }

        discounted_price += temp_total_price_for_selected_option;

        if (discounted_price < 0)
        {
            discounted_price = 0;
        }

        discounted_price_node.innerHTML = convert_to_currency(discounted_price);
    }

    function upload_option_file(upload_btn)
    {
        //برخی از فیلدها از نوع upload هستند. در این فیلدها کاربر میتواند یک فایل را از سیستم خود انتخاب
        //سپس آپلود کند. این عملیات بصورت ajax انجام میشود تا صفحه رفرش نشده و فرم سفارش کاربر بطور ثابت باقی بماند
        //فایلها بصورت موقت آپلود میشوند و name فایل آپلود شده به سرور فرستاده خواهد شد
        //درصورتیکه کاربر به هر دلیلی بخواهد فایل دیگری را جایگزین فایل آپلود شده قبلی کند
        //فایل آپلود شده قبلی از سرور حذف شده و فایل جدید جایگزین میشود
        //سیستم باید طوری طراحی شود که
        //در صورتیکه کاربر سفارش خود را ثبت نکند، فایلهای آپلود شده قبلی که بدون استفاده هستند، حذف گردند
        var father = closest_parent(upload_btn, 'option_holder');
        var upload_field = father.querySelector('input[type="file"]');
        if (! upload_field.disabled)
        {
            //چک میکنیم که فیلد آپلود غیرفعال نباشد
            var selected_file = upload_field.files;
            if (selected_file.length === 0)
            {
                //فایلی انتخاب نشده است
                alert('لطفا ابتدا فایل مورد نظر را انتخاب نمایید');
            }
            else
            {
                //فایل انتخاب شده است، اقدام به آپلود فایل میکنیم
                //ابتدا کلاس در حال آپلود را به نود والد اضافه میکنیم
                add_class(father, 'on_loading');
                var url = base_url + 'upload/temp';
                var formData = new FormData();
                formData.append('file', selected_file[0]);
                formData.append('req', 'upload');

                //پیغامهایی که تا این لحظه نمایش داده شده اند را پاک میکنیم
                var uploaded_module_message_holder = father.querySelector('.uploaded_module .message_holder');
                uploaded_module_message_holder.innerHTML = '';
                var upload_module_message_holder = father.querySelector('.upload_module .message_holder');
                upload_module_message_holder.innerHTML = '';

                var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
                xhr.open('POST', url);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState>3 && xhr.status==200) {
                        //successful
                        //console.log('successful',xhr.responseText);
                        remove_class(father, 'on_loading');

                        var result = JSON.parse(xhr.responseText);
                        if (result.status == 'successful')
                        {
                            //فایل با موفقیت آپلود شده است
                            add_class(father, 'uploaded');

                            uploaded_module_message_holder.innerHTML = 'فایل «'+upload_field.files[0].name+'» با موفقیت ذخیره شد.';

                            var option_hidden_input = father.querySelector('input.option');
                            option_hidden_input.value = result.file_name;

                            //تاثیر فایل آپلود شده میبایست اعمال شود
                            refresh_options_effects();
                        }
                        else
                        {
                            //فایل آپلود نشده است
                            upload_module_message_holder.innerHTML = 'فایل «'+upload_field.files[0].name+'» آپلود نشد. لطفا در صورتیکه چندمین مرتبه است این خطا را مشاهده میکنید. با مدیریت تماس بگیرید..';
                        }

                    }
                    else if (xhr.readyState == 4 && xhr.status!= 200) {
                        //failure
                        console.log('failure',xhr.responseText);
                        remove_class(father, 'on_loading');

                        upload_module_message_holder.innerHTML = 'در ارتباط با سرور خطایی رخ داده است و فایل آپلود نشد. لطفا در صورتیکه چندمین مرتبه است این خطا را مشاهده میکنید. با مدیریت تماس بگیرید..';
                    }
                };
                //xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.send(formData);
            }
        }
    }

    function remove_uploaded_option_file(delete_btn)
    {
        //کاربر میخواهد فایلی را که قبلا آپلود کرده بوده است را حذف کند
        var father = closest_parent(delete_btn, 'option_holder');
        var option_hidden_input = father.querySelector('input.option');
        var file_name = option_hidden_input.value;

        var uploaded_module_message_holder = father.querySelector('.uploaded_module .message_holder');
        uploaded_module_message_holder.innerHTML = '';

        if (file_name != "")
        {
            add_class(father, 'on_loading');

            var send_to_server = {"req":"delete", "file_name":file_name};
            var url = base_url + 'upload/temp';

            postAjax(url, send_to_server, function(result) {
                //console.log(result);
                result = JSON.parse(result);
                remove_class(father, 'on_loading');

                if (result.status == 'successful')
                {
                    //فایل با موفقیت حذف شد
                    remove_class(father, 'uploaded');
                    option_hidden_input.value = '';

                    //تاثیر فایل حذف شده میبایست اعمال شود
                    refresh_options_effects();
                }
                else
                {
                    //فایل حذف نشده است
                    uploaded_module_message_holder.innerHTML = 'فایل حذف نشد. در صورتیکه چندمین مرتبه است این خطا را مشاهده میکنید، لطفا با مدیریت تماس حاصل نمایید.';
                }
            }, undefined, function (result) {
                //console.log(result);
                remove_class(father, 'on_loading');
                uploaded_module_message_holder.innerHTML = 'ارتباط با سرور قطع شده است. در صورتیکه چندمین مرتبه است این خطا را مشاهده میکنید، لطفا با مدیریت تماس حاصل نمایید.';
            });
        }
    }

    function create_options (all_possible_option_fields, selected_options_by_admin) {

        function set_option_attribute(option)
        {
            //مقدار attributeهایی که میبایست بر روی تگ آپشن قرار بگیرد را میسازیم
            var holder_tag_attributes = 'can_option_be_purchased_separately="'+option.can_option_be_purchased_separately+'" is_option_required="'+option.is_option_required+'" option_point_situation="'+option.option_point_situation+'" option_point="'+option.option_point+'" option_price_situation="'+option.option_price_situation+'" option_price="'+option.option_price+'" option_weight_situation="'+option.option_weight_situation+'" option_weight="'+option.option_weight+'" reduce_total_inventory="'+option.reduce_total_inventory+'"';
            //holder_tag_attributes += ' product_quantity_with_option="'+option.product_quantity_with_option+'"';

            //نحوه درج قیمت کنار عنوان هرکدام از آپشن ها را میسازیم
            var price_next_to_title = ' (رایگان)';
            if (option.option_price != 0)
            {
                price_next_to_title = ' ('+convert_to_currency(option.option_price)+option.option_price_situation+' '+currency_name+')';
            }

            //چک میکنیم که اگر از این خصوصیت باقی نمانده است، آنرا غیرفعال کنیم
            //مقدار -1 به معنی بینهاست است.
            var disabled = "";
            if (!(parseInt(option.product_quantity_with_option) > 0 || parseInt(option.product_quantity_with_option) == -1))
            {
                //آپشن مورد نظر موجود نمیباشد.
                disabled = " disabled "
            }

            return {"holder_tag_attributes": holder_tag_attributes, "price_next_to_title": price_next_to_title, "disabled": disabled}
        }

        all_possible_option_fields = JSON.parse(all_possible_option_fields);
        selected_options_by_admin = JSON.parse(selected_options_by_admin);

        //console.log('all_possible_option_fields', all_possible_option_fields);
        //console.log('selected_options_by_admin', selected_options_by_admin);

        var output_html = '';

        //بین مقادیری که submit شده است حلقه میزنیم
        for (var index in all_possible_option_fields) {
            var curr_possible_option_field = all_possible_option_fields[index];
            //console.log(index, curr_possible_option_field);

            if (selected_options_by_admin[curr_possible_option_field.id])
            {
                var selected_option_by_admin = selected_options_by_admin[curr_possible_option_field.id];
                /*
                //مشخصات کلی هرکدام از فیلدها را آماده سازی میکنیم
                //از آنجائیکه در مورد فیلد SELECT یا CHECKBOX مشخصات هر آپشن با مابقی آپشنها متفاوت است،
                //آن مورد را جداگانه در خود switch مورد بررسی قرار میدهیم.
                var holder_tag_attributes = '';
                var price_next_to_title = '';
                if (curr_possible_option_field.type != "select" || curr_possible_option_field.type != 'checkbox')
                {
                    holder_tag_attributes = 'can_option_be_purchased_separately="'+selected_option_by_admin.can_option_be_purchased_separately+'" is_option_required="'+selected_option_by_admin.is_option_required+'" option_point_situation="'+selected_option_by_admin.option_point_situation+'" option_point="'+selected_option_by_admin.option_point+'" option_price_situation="'+selected_option_by_admin.option_price_situation+'" option_price="'+selected_option_by_admin.option_price+'" option_weight_situation="'+selected_option_by_admin.option_weight_situation+'" option_weight="'+selected_option_by_admin.option_weight+'" reduce_total_inventory="'+selected_option_by_admin.reduce_total_inventory+'"';
                    //holder_tag_attributes += ' product_quantity_with_option="'+selected_option_by_admin.product_quantity_with_option+'"';

                    if (selected_option_by_admin.option_price == 0)
                    {
                        price_next_to_title = ' (رایگان)';
                    }
                    else
                    {
                        price_next_to_title = ' ('+convert_to_currency(selected_option_by_admin.option_price)+selected_option_by_admin.option_price_situation+' '+currency_name+')';
                    }

                }*/
                /*##################################################################################
                * ## SINGLE ENTRY | MULTIPLE ENTRY | TEXTAREA | UPLOAD | FILE | SELECT | CHECKBOX ##
                * ##################################################################################*/
                switch (curr_possible_option_field.type)
                {
                    case 'single_entry':
                        var opt = set_option_attribute(selected_option_by_admin);
                        output_html +=
                            '<div class="w3-margin-bottom option_holder '+opt.disabled+'">' +
                            '<div class="title">'+curr_possible_option_field.title+opt.price_next_to_title+':</div>' +
                            '<input oninput="refresh_options_effects();" class="w3-border w3-round w3-padding option" option_type="'+curr_possible_option_field.type+'" type="text" name="option_'+curr_possible_option_field.id+'"  '+opt.holder_tag_attributes+opt.disabled+'>' +
                            '</div>';
                        break;

                    case 'multiple_entry':
                        var opt = set_option_attribute(selected_option_by_admin);
                        output_html +=
                            '<div class="w3-margin-bottom option_holder '+opt.disabled+'">' +
                            '<div class="title">'+curr_possible_option_field.title+opt.price_next_to_title+':</div>' +
                            '<textarea oninput="refresh_options_effects();" class="w3-border w3-round w3-padding option" option_type="'+curr_possible_option_field.type+'" type="textarea" rows="2" name="option_'+curr_possible_option_field.id+'"  '+opt.holder_tag_attributes+opt.disabled+'></textarea>' +
                            '</div>';
                        break;

                    case 'textarea':
                        var opt = set_option_attribute(selected_option_by_admin);
                        output_html +=
                            '<div class="w3-margin-bottom option_holder '+opt.disabled+'">' +
                            '<div class="title">'+curr_possible_option_field.title+opt.price_next_to_title+':</div>' +
                            '<textarea oninput="refresh_options_effects();" class="w3-border w3-round w3-padding option" option_type="'+curr_possible_option_field.type+'" type="textarea" rows="3" name="option_'+curr_possible_option_field.id+'"  '+opt.holder_tag_attributes+opt.disabled+'></textarea>' +
                            '</div>';
                        break;

                    case 'upload':
                        var opt = set_option_attribute(selected_option_by_admin);
                        output_html +=
                            '<div class="w3-margin-bottom option_holder '+opt.disabled+'">' +
                            '<div class="loading_holder">\n' +
                            '<div class="content">\n' +
                            '<div class="loader_spin"></div>\n' +
                            '<span>لطفا کمی صبر کنید...</span>\n' +
                            '</div>\n' +
                            '</div>\n'+
                            '<div class="title">'+curr_possible_option_field.title+opt.price_next_to_title+':</div>' +
                            '<div class="upload_module">'+
                            '<div class="message_holder"></div>'+
                            '<input class="w3-border" type="file" '+opt.disabled+'>' +
                            '<button '+opt.disabled+' onclick="upload_option_file(this);">آپلود فایل</button>'+
                            '</div>'+
                            '<div class="uploaded_module">'+
                                '<div class="message_holder"></div>'+
                                '<button class="remove_btn" onclick="remove_uploaded_option_file(this)">حذف فایل</button>'+
                                '<input class="option" type="hidden" option_type="'+curr_possible_option_field.type+'" name="option_'+curr_possible_option_field.id+'" '+opt.holder_tag_attributes+opt.disabled+' value="">'+
                            '</div>'+
                            '</div>';
                        break;

                    case 'file':
                        var opt = set_option_attribute(selected_option_by_admin);
                        output_html +=
                            '<div class="w3-margin-bottom option_holder '+opt.disabled+'">' +
                            '<label class="w3-margin-left">'+
                            '<input onchange="refresh_options_effects();" class="option" option_type="'+curr_possible_option_field.type+'" type="checkbox" name="option_'+curr_possible_option_field.id+'" '+opt.holder_tag_attributes+opt.disabled+' value="selected">'+
                            ' <i class="fas fa-cloud-download-alt"></i> '+
                            curr_possible_option_field.title+opt.price_next_to_title+
                            '</label>'+
                            '</div>';
                        break;

                    case 'select':
                        var possible_select_values = curr_possible_option_field.insert_value.split('::new_line::');
                        var optionlist = '';
                        //بطور پیشفرض درنظر میگیریم که همه فیلدها غیرفعال هستند.
                        //سپس در طول ساخت فیلدها بررسی میکنیم که اگر حداقل یک فیلد فعال بود
                        //مقدار «غیرفعال بودن همه فیلدها» را به تهی تغییر میدهیم
                        var all_are_disabled = ' disabled';

                        possible_select_values.forEach(function (curr_possible_select_value) {
                            curr_possible_select_value = curr_possible_select_value.split('|');
                            if (curr_possible_select_value.length == 1)
                            {
                                curr_possible_select_value[1] = curr_possible_select_value[0];
                            }

                            //آیا ادمین، این آیتم را برای محصول حال حاضر درنظر گرفته است؟
                            if (selected_option_by_admin[curr_possible_select_value[1]])
                            {
                                var selected_item = selected_option_by_admin[curr_possible_select_value[1]];
                                var opt = set_option_attribute(selected_item);
                                optionlist += '<option '+opt.holder_tag_attributes+opt.disabled+' value="'+curr_possible_select_value[1]+'"> '+curr_possible_select_value[0]+opt.price_next_to_title+'</option>';

                                if (opt.disabled === '')
                                {
                                    all_are_disabled = '';
                                }
                            }
                        });

                        if (optionlist != '')
                        {
                            optionlist = "<option value=''>انتخاب کنید</option>" + optionlist;
                            output_html +=
                                '<div class="w3-margin-bottom option_holder '+all_are_disabled+'">' +
                                '<div>'+curr_possible_option_field.title+':</div>' +
                                '<select name="option_'+curr_possible_option_field.id+'" onchange="refresh_options_effects();" class="w3-select w3-border w3-round w3-padding option" option_type="'+curr_possible_option_field.type+'" '+all_are_disabled+'>'+
                                optionlist +
                                '<select>'+
                                '</div>';
                        }

                        break;

                    case 'checkbox':
                        var possible_checkbox_values = curr_possible_option_field.insert_value.split('::new_line::');
                        var checklist = '';
                        //بطور پیشفرض درنظر میگیریم که همه فیلدها غیرفعال هستند.
                        //سپس در طول ساخت فیلدها بررسی میکنیم که اگر حداقل یک فیلد فعال بود
                        //مقدار «غیرفعال بودن همه فیلدها» را به تهی تغییر میدهیم
                        var all_are_disabled = ' disabled';

                        possible_checkbox_values.forEach(function (curr_possible_checkbox_value, checkbox_index) {
                            curr_possible_checkbox_value = curr_possible_checkbox_value.split('|');
                            if (curr_possible_checkbox_value.length == 1)
                            {
                                curr_possible_checkbox_value[1] = curr_possible_checkbox_value[0];
                            }

                            //آیا ادمین، این آیتم را برای محصول حال حاضر درنظر گرفته است؟
                            if (selected_option_by_admin[curr_possible_checkbox_value[1]])
                            {
                                var selected_item = selected_option_by_admin[curr_possible_checkbox_value[1]];
                                var opt = set_option_attribute(selected_item);
                                checklist += '<label class="w3-margin-left '+opt.disabled+'"><input class="option" name="option_'+curr_possible_option_field.id+'" index="'+checkbox_index+'" option_type="'+curr_possible_option_field.type+'" onchange="refresh_options_effects();" value="'+curr_possible_checkbox_value[1]+'" '+opt.holder_tag_attributes+opt.disabled+' type="checkbox"> '+curr_possible_checkbox_value[0]+opt.price_next_to_title+'</label>';


                                if (opt.disabled === '')
                                {
                                    all_are_disabled = '';
                                }
                            }
                        });

                        if (checklist != '')
                        {
                            output_html +=
                                '<div class="w3-margin-bottom option_holder '+all_are_disabled+'">' +
                                '<div>'+curr_possible_option_field.title+':</div>' +
                                checklist +
                                '</div>';
                        }

                        break;
                }
            }

        }
        
        var options_holder_node = document.getElementById('options_holder');

        if (options_holder_node)
        {
            options_holder_node.innerHTML = output_html;
        }
    }

    //Start Creating Options Selectable part
    //console.log(<?= $option_fields ?>);
    //console.log(<?= $submitted_options ?>);

    /*********************
     ** On Windows Load **
     *********************/
    if(window.addEventListener){
        window.addEventListener('DOMContentLoaded', function () {
            create_options ('<?= $option_fields ?>', '<?= $submitted_options; ?>');
        })
    }else{
        window.attachEvent('onload', function () {
            create_options ('<?= $option_fields ?>', '<?= $submitted_options; ?>');
        })
    }
</script>

<?PHP
if ($show_type != 'raw') {
	include "blocks/pardahkt_amn.php";
	include "blocks/contact_us.php";
	include "blocks/footer.php";
}
?>
