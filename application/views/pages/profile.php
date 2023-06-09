<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP //include "blocks/main_menu.php"; ?>

<div id="profile" class="w3-container">
    <div class="w3-card-4 info w3-white">
        <div class="w3-container">
            <div class="w3-show-inline-block"><i class="fas fa-user w3-xlarge"></i><?php echo $first_name.' '.$last_name; ?></div>
            <a href="<?=base_url('profile/edit')?>" class="w3-button w3-green w3-round w3-left w3-show-inline-block w3-margin-top">ویرایش مشخصات</a>
            <hr>
            <div class="w3-row">
                <div class="w3-col m6">
					<i class="fas fa-user"></i><?php echo 'نام کاربری: '.$username;?><br>
					<i class="fas fa-envelope"></i><?php echo 'ایمیل: '.$email;?><br>
					<i class="fas fa-user-secret"></i><?php echo 'موقعیت کاربری: '.$customer_group;?>
				</div>
                <div class="w3-col m6">
					<i class="far fa-calendar-alt"></i><?php echo 'تاریخ تولد: '.$year.'/'.$month.'/'.$day;?><br/>
					<i class="fas fa-mobile-alt"></i><?php echo 'موبایل: '.$mobile;?>
				</div>
            </div>
        </div>
        <div class="w3-padding-24 navar"></div>
    </div>

    <div class="w3-row">
        <div class="w3-bar tab-title">
            <div class="tablink w3-bar-item w3-buttonn w3-right active pointer" onclick="change_tabs(event,'orders')">سفارشات</div>
            <div class="w3-bar-item w3-buttonn tablink w3-right pointer" onclick="change_tabs(event,'wishlist')">لیست علاقه مندی ها</div>
            <!--<div class="w3-bar-item w3-buttonn tablink w3-right" onclick="change_tabs(event,'massage')">پرسش و پاسخ</div>
            <div class="w3-bar-item w3-buttonn tablink w3-right" onclick="change_tabs(event,'comment')">نظرات</div>-->
            <div class="w3-bar-item w3-buttonn tablink w3-right pointer" onclick="change_tabs(event,'address')">آدرس ها</div>
            <div class="w3-bar-item w3-buttonn tablink w3-right pointer" onclick="change_tabs(event,'files')">فایل ها</div>
        </div>

        <div id="orders" class="w3-container tab">
            <div class="accordion_holder">
             <?php echo $orders_view; ?>
            </div>
            <div class="w3-modal w3-animate-opacity cancel_order">
                <div class="w3-modal-content w3-card-4">
                    <div class="modal_header w3-red">
                        <span onclick="closest_parent(this, 'cancel_order').style.display='none'" class="w3-button w3-large w3-left">&times;</span>
                        <div class="modal_title w3-right w3-large">لغو سفارش</div>
                    </div>
                    <div class="w3-container modal_content">
                        <div class="messages_holder"></div>
                        <p>درصورتیکه اطمینان دارید میخواهید سفارش خود را لغو کنید، لطفا علت آن را در کادر پایین تشریح نمایید.</p>
                        <textarea class="w3-input w3-padding w3-border w3-round w3-margin-bottom description" type="textarea" rows="3"></textarea>
                        <p>شرح سفارش</p>
                        <div class="order_details"></div>
                        <p class="w3-left-align">
                            <button order_id="" class="w3-button w3-red agree_btn" onclick="cancel_order('cancel', this);">سفارش لغو شود</button>
                            <button class="w3-button w3-green" onclick="closest_parent(this, 'cancel_order').style.display='none'">منصرف شدم</button>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div id="wishlist" class="w3-container tab" style="display:none">
            <div class="w3-row-padding w3-margin-top">
				<?php echo $view_favorite; ?>
            </div>
            <div class="w3-modal w3-animate-opacity delete_favorites_popup">
                <div class="w3-modal-content w3-card-4">
                    <div class="modal_header w3-red">
                        <span onclick="closest_parent(this, 'delete_favorites_popup').style.display='none'" class="w3-button w3-large w3-left">&times;</span>
                        <div class="modal_title w3-right w3-large">حذف علاقه‌مندی</div>
                    </div>
                    <div class="w3-container modal_content">
                        <p>آیا میخواهید «<a class="product_title product_link" href="#">عنوان محصول</a>» را از لیست علاقه‌مندی‌هایتان حذف کنید؟</p>
                        <p class="w3-left-align">
                            <button product_id="" class="w3-button w3-red agree_btn" onclick="delete_favorites('delete', this)">بله</button>
                            <button class="w3-button w3-green" onclick="closest_parent(this, 'delete_favorites_popup').style.display='none'">خیر</button>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div id="files" class="w3-container tab" style="display:none">
            <div class="w3-row-padding content w3-light-gray">
                <div class="w3-col">
                    <div class="w3-responsive">
                        <table class="files_table w3-table w3-bordered">
                            <tr>
                                <th>عنوان فایل</th>
                                <th>تاریخ دریافت</th>
                                <th>دانلود</th>
                            </tr>
                         <?php echo $file_view; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
	<?php echo $add_new_address; ?>
	<?php echo $address_view; ?>
</div>

<script>
    /********************
     ** DELETE ADDRESS **
     *******************/
    function delete_address (status, selected_element)
    {
        if (status == "initialize")
        {
            var address_item_holder = closest_parent(selected_element, 'address_item');
            var address_id = address_item_holder.getAttribute('address_id');
            var address_title = address_item_holder.getAttribute('address_title');
            var address_link = address_item_holder.getAttribute('address_link');

            document.querySelector('#address .delete_address_popup .address_title').innerHTML = address_title;
            document.querySelector('#address .delete_address_popup .address_link').href = address_link;
            document.querySelector('#address .delete_address_popup .agree_btn').setAttribute('address_id', address_id);

            //show popup
            document.querySelector('#address .delete_address_popup').style.display='block';
        }
        else if (status == "delete")
        {
            var address_id = selected_element.getAttribute('address_id');
            var select_item = '#address .address_item[address_id="'+address_id+'"]';
            var address_item_node = document.querySelector(select_item);

            //hide Address temporary
            address_item_node.style.display = 'none';
            //Hide PopUp
            document.querySelector('#address .delete_address_popup').style.display = 'none';

            //Send Delete request to server
            var send_to_server = {"req": "delete_address", "id":address_id};
            var url = '<?=base_url('api/index')?>';
            postAjax(
                url,
                send_to_server,
                function (result, passed_params) {
                    //console.log("success response:", result);
                    result = JSON.parse(result);

                    if (result.status == 'success')
                    {
                        //Remove Element node
                        passed_params.address_item_node.parentElement.removeChild(passed_params.address_item_node);
                    }
                    else
                    {
                        //We could not delete element
                        passed_params.address_item_node.style.display = 'block';
                    }
                },
                {"address_item_node":address_item_node},
                function (result, passed_params) {
                    //Failure state
                    alert('در ارتباط با سرور خطایی رخ داده است.');
                    //We could not delete element
                    passed_params.address_item_node.style.display = 'none';
                },
                {"address_item_node":address_item_node}
            );
        }

    }

    /******************
     ** Cancel Order **
     *****************/
    function cancel_order (status, selected_element)
    {
        var messages_holder = document.querySelector(".cancel_order .messages_holder");
        messages_holder.innerHTML = "";

        if (status == "initialize")
        {
            var order_item = closest_parent(selected_element, 'accordion_item');
            var order_content = order_item.getElementsByClassName('content')[0];

            var order_id = order_item.getAttribute('order_id');
            //var product_title = order_item.getAttribute('product_title');
            //var product_link = order_item.getAttribute('product_link');

            document.querySelector('#orders .cancel_order .order_details').innerHTML = order_content.outerHTML;
            //document.querySelector('#orders .cancel_order .product_link').href = product_link;
            document.querySelector('#orders .cancel_order .agree_btn').setAttribute('order_id', order_id);

            //show popup
            document.querySelector('#orders .cancel_order').style.display='block';
        }
        else if (status == "cancel")
        {
            var cancel_description = document.querySelector('.cancel_order textarea.description');

            if (cancel_description.value.length == 0)
            {
                //Description is not filled!
                //We should show message to client to notice him that he should add description
                var timestamp = new Date().getTime();

                var msg = '<a name="'+timestamp+'"></a><div class="alert  w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>لطفا علت لغو سفارش را شرح دهید.</div>';
                messages_holder.innerHTML = msg;

                window.location.href = "#"+timestamp;
                return false;
            }
            else
            {
                //Hide PopUp
                document.querySelector('#orders .cancel_order').style.display = 'none';

                var order_id = selected_element.getAttribute('order_id');
                var select_item = '#orders .order_item[order_id="'+order_id+'"]';
                var order_item_node = document.querySelector(select_item);
                order_item_node.className += ' canceled';

                //Send Cancel request to server
                var send_to_server = {"req": "cancel_order", "id":order_id, "description":cancel_description.value};
                console.log(send_to_server);
                var url = '<?=base_url('api/index')?>';
                postAjax(
                    url,
                    send_to_server,
                    function (result, passed_params) {
                        console.log(result);
                        //console.log("success response:", passed_params.order_item_node);
                        result = JSON.parse(result);

                        if (result.status == 'success')
                        {
                            //Nothing to do!
                            //Order Canceled and we don't need to show any notification to the user
                            //we will refresh the page to add changes
                            location.reload();
                        }
                        else
                        {
                            //We could not cancel element
                            passed_params.order_item_node.className = passed_params.order_item_node.className.replace(/ canceled/g, "");
                        }
                    },
                    {"order_item_node":order_item_node},
                    function (result, passed_params) {
                        //Failure state
                        alert('در ارتباط با سرور خطایی رخ داده است.');
                        //We could not cancel element
                        passed_params.order_item_node.className = passed_params.order_item_node.className.replace(/ canceled/g, "");
                    },
                    {"order_item_node":order_item_node}
                );
            }
        }

    }

    /**********************
     ** DELETE FAVORITES **
     *********************/
    function delete_favorites (status, selected_element)
    {
        if (status == "initialize")
        {
            var favorite_item_holder = closest_parent(selected_element, 'favorite_item');
            var product_id = favorite_item_holder.getAttribute('product_id');
            var product_title = favorite_item_holder.getAttribute('product_title');
            var product_link = favorite_item_holder.getAttribute('product_link');

            document.querySelector('#wishlist .delete_favorites_popup .product_title').innerHTML = product_title;
            document.querySelector('#wishlist .delete_favorites_popup .product_link').href = product_link;
            document.querySelector('#wishlist .delete_favorites_popup .agree_btn').setAttribute('product_id', product_id);

            //show popup
            document.querySelector('#wishlist .delete_favorites_popup').style.display='block';
        }
        else if (status == "delete")
        {
            var product_id = selected_element.getAttribute('product_id');
            var select_item = '#wishlist .favorite_item[product_id="'+product_id+'"]';
            var favorite_item_node = document.querySelector(select_item);

            //hide item temporary
            favorite_item_node.style.display = 'none';
            //Hide PopUp
            document.querySelector('#wishlist .delete_favorites_popup').style.display = 'none';

            //Send Delete request to server
            var send_to_server = {"req": "delete_favorite", "id":product_id};
            var url = '<?=base_url('api/index')?>';
            postAjax(
                url,
                send_to_server,
                function (result, passed_params) {
                    //console.log("success response:", result);
                    result = JSON.parse(result);

                    if (result.status == 'success')
                    {
                        //Remove Element node
                        passed_params.favorite_item_node.parentElement.removeChild(passed_params.favorite_item_node);
                    }
                    else
                    {
                        //We could not delete element
                        passed_params.favorite_item_node.style.display = 'block';
                    }
                },
                {"favorite_item_node":favorite_item_node},
                function (result, passed_params) {
                    //Failure state
                    alert('در ارتباط با سرور خطایی رخ داده است.');
                    //We could not delete element
                    passed_params.favorite_item_node.style.display = 'none';
                },
                {"favorite_item_node":favorite_item_node}
            );
        }

    }
</script>

<?PHP //include "blocks/pardahkt_amn.php"; ?>
<?PHP //include "blocks/contact_us.php"; ?>
<?PHP //include "blocks/footer.php"; ?>

