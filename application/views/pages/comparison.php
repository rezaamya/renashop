<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP //include "blocks/main_menu.php"; ?>

<div id="comparison" class="w3-row module_html">
    <div class="w3-container content w3-center">
        <div class="title_holder">
            <hr>
            <div class="title">مقایسه محصولات</div>
        </div>

        <form action="<?=base_url('pages/comparison')?>" class="comparison_search_module" method="post">
        <div class="w3-row w3-margin-top w3-margin-bottom">
            <label class="w3-col s1">&nbsp</label>
            <div class="w3-col s9">
                <input class="w3-input w3-border w3-padding search_field" placeholder="محصول مورد نظر خود را جستجو نمایید." type="text" oninput="find_item_to_compare(this, 'product');">
                <div class="found_list">
                </div>
            </div>
            <button type="submit" class="w3-button w3-col s1 w3-padding-small w3-green w3-border add_to_compare_list" disabled><i class="fas fa-plus"></i></button>
            <input name="selected_item" type="hidden">
            <input name="task" type="hidden">
        </div>

        <table class="w3-responsive w3-table w3-centered w3-striped w3-bordered w3-margin-bottom comparison_table">
            <thead>

<?php echo $comparison_view;?>
        </table>
        </form>
    </div>
</div>

<script>
    var timer_peyda_kardan_item_jahate_moghayese;
    function find_item_to_compare(typed_input, item_type) {
        //console.log(typed_input, item_type);
        var node_asli = closest_parent(typed_input, 'comparison_search_module');
        var found_list_node = node_asli.getElementsByClassName('found_list')[0];

        //دکمه اضافه کردن مقایسه را غیرفعال میکنیم
        var add_to_compare_list = node_asli.querySelector('button.add_to_compare_list');
        add_to_compare_list.disabled = true;

        //تایمرهای قبلی را پاک کن تا تایمر جدید در پایینتر تنظیم شود
        clearTimeout(timer_peyda_kardan_item_jahate_moghayese);

        if (typed_input.value.length > 1)
        {
            //بنویس در حال جستجو
            found_list_node.innerHTML = '<?=lang('searching');?>';

            //اگه بیشتر از یک کاراکتر تایپ شد، مقدار تایپ شده را به سرور بفرست تا نتایج آیتم مشابه را برگرداند
            //قبل از فراخوانی AJAX، چند لحظه تاخیر قرار بده
            timer_peyda_kardan_item_jahate_moghayese = setTimeout(function(){
                //فیلد مخفیه selected_before_to_compare را بگیر و مقادیر آنها را برای سرور ارسال کن تا اطلاعات تکراری را بازگشت ندهد
                var current_items_list = node_asli.getElementsByClassName('selected_before_to_compare');
                var current_items_list_ips = [];
                [].forEach.call(current_items_list, function(current_node,index,arr) {
                    console.log(current_node);
                    current_items_list_ips.push(current_node.value);
                });
                //console.log("current_items_list_ips", current_items_list_ips.length, current_items_list_ips.join());
                console.log({"req":"find_"+item_type, "key":typed_input.value, "exception":current_items_list_ips.join()});
                //Send find request to server
                var send_to_server = {"req":"find_"+item_type, "key":typed_input.value, "exception":current_items_list_ips.join()};
                var url = '<?=base_url('api/index')?>';
                postAjax(
                    url,
                    send_to_server,
                    function (result, passed_params) {
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
                            found_list_node.innerHTML = '<select onclick="entekhabe_az_list_to_compare(this.options[this.selectedIndex], \''+item_type+'\')" size="5" class="form-control">'+option_list+'</select>';
                        }
                    },
                    undefined,
                    function (result, passed_params) {
                        //Failure state
                        alert('در ارتباط با سرور خطایی رخ داده است.');
                    },
                    undefined
                );
            }, 1000);
        }
        else
        {
            //لیست جستجوهای قبلی را مخفی کن
            found_list_node.innerHTML = '';
        }
    }

    function entekhabe_az_list_to_compare(item_entekhab_shode, item_type) {
        //نود اصلی باکس محصول را بگیر
        var node_asli = closest_parent(item_entekhab_shode, 'comparison_search_module');

        //مقدار مخفی task را تنظیم میکنیم
        var task_node = node_asli.querySelector('input[name="task"]');
        task_node.value = 'add';

        //مقدار مخفی selected_item را تنظیم میکنیم
        var selected_item_node = node_asli.querySelector('input[name="selected_item"]');
        selected_item_node.value = item_entekhab_shode.value;

        //فیلدی که کاربر در حال تایپ عبارت مورد جستجو، در آن بوده است را تکمیل میکنیم
        var search_field = node_asli.getElementsByClassName('search_field')[0];
        search_field.value = item_entekhab_shode.getAttribute('title');

        //لیست بعد از انتخاب حذف میشود.
        //درصورتیکه کاربر مجدد تایپ کند، لیست دوباره ظاهر میشود (در فانکشن find_item_to_compare لیست ظاهر میشود)
        var found_list_node = node_asli.getElementsByClassName('found_list')[0];
        found_list_node.innerHTML = '';

        //دکمه اضافه کردن مقایسه را فعال میکنیم
        var add_to_compare_list = node_asli.querySelector('button.add_to_compare_list');
        add_to_compare_list.disabled = false;
    }

    function delete_from_compare_list(clicked_item, item_id) {
        //نود اصلی باکس محصول را بگیر
        var node_asli = closest_parent(clicked_item, 'comparison_search_module');

        //مقدار مخفی task را تنظیم میکنیم
        var task_node = node_asli.querySelector('input[name="task"]');
        task_node.value = 'delete';

        //مقدار مخفی selected_item را تنظیم میکنیم
        var selected_item_node = node_asli.querySelector('input[name="selected_item"]');
        selected_item_node.value = item_id;

        node_asli.submit();
    }

</script>

<?PHP //include "blocks/pardahkt_amn.php"; ?>
<?PHP //include "blocks/contact_us.php"; ?>
<?PHP //include "blocks/footer.php"; ?>





