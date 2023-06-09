<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><div id="tool_bar" class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <button type="button" onclick="set_task('send', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?=lang('send')?></button>
            <button type="button" onclick="set_task('save', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('save')?></button>
            <button type="button" onclick="set_task('send_and_new', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('send_and_new')?></button>
            <button type="button" onclick="set_task('send_and_close', 'main_form', '')" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('send_and_close')?></button>
            <a href="<?php echo base_url("marketing/email")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
        </div>
    </div>
</div>

<div class="container-fluid">
	<?PHP echo $html_output['sys_msg']; ?>
    <div class="row">
        <div class="col">
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("marketing/add_email/".$html_output['item_data']['id']), $attributes);
            ?>

                <div class="form-group row">
                    <label for="title" class="col-sm-2 col-form-label"><?=lang('title')?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="title" name="title" placeholder="<?=lang('title')?>" value="<?php echo set_value('title',$html_output['item_data']['title']); ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email_from" class="col-sm-2 col-form-label">
                        <?=lang('from')?>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" name="email_from" class="form-control" placeholder="<?=lang('insert_email')?>" value="<?php echo set_value('email_from',$html_output['item_data']['email_from']); ?>">
                    </div>
                </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    <?=lang('to')?>
                </label>
                <div class="col-sm-10">
                    <select id="email_to" name="email_to" class="form-control" onchange="show_hide(this);">
						<?PHP echo $html_output['email_to_list']; ?>
                    </select>
                </div>
            </div>
            <div id="customer_group" class="form-group row d-none">
                <label for="customer_group" class="col-sm-2 col-form-label">
                    <?=lang('customer_group')?>
                </label>
                <div class="col-sm-10">
                    <select id="customer_group" class="form-control" name="customer_group">
                        <?PHP echo $html_output['categories_list']; ?>
                    </select>
                </div>
            </div>
            <div id="add_customer" class="form-group row d-none">
                <label class="col-sm-2 col-form-label"><?=lang('customer')?></label>
                <div class="col-sm-10 item_box">
                    <input oninput="find_item(this, 'customer');" type="text" class="form-control search_field" placeholder="<?=lang('customer')?>">
                    <div class="found_list mt-1">
                    </div>
                    <div class="selected_holder">
						<?php echo $html_output['item_data']['selected_customer']; ?>
                    </div>
                </div>
            </div>
            <div id="add_affiliates" class="form-group row d-none">
                <label class="col-sm-2 col-form-label"><?=lang('affiliates')?></label>
                <div class="col-sm-10 item_box">
                    <input oninput="find_item(this, 'affiliate');" type="text" class="form-control search_field" placeholder="<?=lang('affiliates')?>">
                    <div class="found_list mt-1">
                    </div>
                    <div class="selected_holder">
                    </div>
                </div>
            </div>
            <div id="add_product" class="form-group row d-none">
                <label class="col-sm-2 col-form-label"><?=lang('product')?>
                <a tabindex="0" class="" data-toggle="popover" data-trigger="focus" data-content="<?=lang('f1_add_marketing_email')?>"><i class="text-danger fas fa-question-circle"></i></a>
                </label>
                <div class="col-sm-10 item_box">
                    <input oninput="find_item(this, 'product');" type="text" class="form-control search_field" placeholder="<?=lang('product')?>">
                    <div class="found_list mt-1">
                    </div>
                    <div class="selected_holder">
						<?php echo $html_output['item_data']['items_id']; ?>
                    </div>
                </div>
            </div>


            <div class="form-group row">
                <label for="subject" class="col-sm-2 col-form-label"><?=lang('subject')?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="subject" name="subject" placeholder="<?=lang('subject')?>" value="<?php echo set_value('subject',$html_output['item_data']['subject']); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2" for="Textarea1"><?=lang('message')?></label>
                <div class="col-sm-10">
                    <textarea name="message" class="form-control ckeditor" id="Textarea1" rows="5"><?php echo set_value('message',$html_output['item_data']['message']); ?></textarea>
                </div>
            </div>
            <input type="hidden" id="task" name="task">
            </form>
        </div>
    </div>
    <script>
        var timer_peyda_kardan_item;
        function find_item(typed_input, item_type) {
            //console.log(typed_input, item_type);
            var node_asli = typed_input.closest('.item_box');
            var found_list_node = node_asli.getElementsByClassName('found_list')[0];

            //تایمرهای قبلی را پاک کن تا تایمر جدید در پایینتر تنظیم شود
            clearTimeout(timer_peyda_kardan_item);

            if (typed_input.value.length > 1)
            {
                //بنویس در حال جستجو
                found_list_node.innerHTML = '<?=lang('searching');?>';

                //اگه بیشتر از یک کاراکتر تایپ شد، مقدار تایپ شده را به سرور بفرست تا نتایج آیتم مشابه را برگرداند
                //قبل از فراخوانی AJAX، چند لحظه تاخیر قرار بده
                timer_peyda_kardan_item = setTimeout(function(){
                    //فیلد مخفیه selected_item را بگیر و مقادیر آنها را برای سرور ارسال کن تا اطلاعات تکراری را بازگشت ندهد
                    var current_items_list = node_asli.getElementsByClassName('selected_item');
                    var current_items_list_ips = [];
                    [].forEach.call(current_items_list, function(current_node,index,arr) {
                        current_items_list_ips.push(current_node.value);
                    });
                    //console.log("current_items_list_ips", current_items_list_ips.length, current_items_list_ips.join());
                    console.log({"req":"find_"+item_type, "key":typed_input.value, "exception":current_items_list_ips.join()});
                    $.ajax({
                        method: "POST",
                        url: '<?=base_url('api/index')?>',//'http://localhost/keshavarz/api/index',
                        data: {"req":"find_"+item_type, "key":typed_input.value, "exception":current_items_list_ips.join()},
                        success: function(result){
                            console.log("raw response:", result);
                            var found_items = JSON.parse(result);
                            //console.log("JSON response:", found_items);

                            //لیست محصولاتی که از سرور آمده است را بصورت option ایجاد کن
                            var option_list = "";
                            found_items.forEach(function (radife_mahsol) {
                                //option_list += '<option value="'+radife_mahsol.id+'" price="'+radife_mahsol.price+'" title="'+radife_mahsol.title+'">'+radife_mahsol.title+'</option>';
                                option_list += '<option value="'+radife_mahsol.id+'" title="'+radife_mahsol.title+'">'+radife_mahsol.title+'</option>';
                            });


                            if (option_list == "")
                            {
                                //موردی یافت نشد
                                found_list_node.innerHTML = "<?=lang('no_item_found')?>";
                            }
                            else
                            {
                                //موارد پیدا شده را بصورت لیست نمایش بده
                                found_list_node.innerHTML = '<select onclick="entekhabe_az_list(this.options[this.selectedIndex], \''+item_type+'\')" size="5" class="form-control">'+option_list+'</select>';
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

        function entekhabe_az_list(item_entekhab_shode, item_type) {
            //نود اصلی باکس محصول را بگیر
            var node_asli = item_entekhab_shode.closest('.item_box');

            //نود جدیدی که باید اضافه شود را میسازیم
            var new_node_string = '<div class="selected_item_holder">' +
                '                            <button type="button" class="close" onclick="this.parentNode.parentNode.removeChild(this.parentNode)">\n' +
                '                                <span>&times;</span>\n' +
                '                            </button>\n' +
                '                            <span>'+item_entekhab_shode.getAttribute("title")+'</span>\n' +
                '                            <input class="selected_item" value="'+item_entekhab_shode.value+'" name="selected_'+item_type+'_item[]" type="hidden">\n' +
                '                        </div>';

            //مقدار انتخاب شده را در لیست انتخابها اضافه میکنیم
            var selected_holder = node_asli.getElementsByClassName('selected_holder')[0];
            selected_holder.insertAdjacentHTML('afterbegin', new_node_string);

            //فیلدی که کاربر در حال تایپ عبارت مورد جستجو، در آن بوده است را خالی میکنیم
            var search_field = node_asli.getElementsByClassName('search_field')[0];
            search_field.value = '';

            //لیست بعد از انتخاب حذف میشود.
            //درصورتیکه کاربر مجدد تایپ کند، لیست دوباره ظاهر میشود (در فانکشن find_item لیست ظاهر میشود)
            var found_list_node = node_asli.getElementsByClassName('found_list')[0];
            found_list_node.innerHTML = '';
        }

        function show_hide(oni_ke_change_shode) {
            document.getElementById('customer_group').classList.add('d-none');
            document.getElementById('add_customer').classList.add('d-none');
            document.getElementById('add_affiliates').classList.add('d-none');
            document.getElementById('add_product').classList.add('d-none');

            if (oni_ke_change_shode.value == "customer_group") {
                document.getElementById('customer_group').classList.remove('d-none');
            }

            else if (oni_ke_change_shode.value=="selected_customers"){
                document.getElementById('add_customer').classList.remove('d-none');
            }

            else if (oni_ke_change_shode.value=="selected_affiliates"){
                document.getElementById('add_affiliates').classList.remove('d-none');
            }

            else if (oni_ke_change_shode.value=="products"){
                document.getElementById('add_product').classList.remove('d-none');
            }

        }

        if(window.addEventListener){
            window.addEventListener('DOMContentLoaded', function () {
                show_hide(document.getElementById("email_to"));
            })
        }else{
            window.attachEvent('onload', function () {
                show_hide(document.getElementById("email_to"));
            })
        }

    </script>
</div>
