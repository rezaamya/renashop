/**
 * Created by Office on 3/11/2018.
 */
/**************************
 ** Handle Ajax Requestes **
 **************************/
function postAjax(url, data, success, success_parameters, failure, failure_parameters) {
    success = typeof success !== 'undefined' ? success : false;
    success_parameters = typeof success_parameters !== 'undefined' ? success_parameters : false;
    failure = typeof failure !== 'undefined' ? failure : false;
    failure_parameters = typeof failure_parameters !== 'undefined' ? failure_parameters : false;
    /*var params = typeof data == 'string' ? data : Object.keys(data).map(
     function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
     ).join('&');
     */
    //var params = serialize_obj({ p1: 1, p2: {'test_name':'Hello World'} });
    var params = serialize_obj(data);

    var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xhr.open('POST', url);
    xhr.onreadystatechange = function() {
        if (xhr.readyState>3 && xhr.status==200) {
            if (success)
            {
                if (success_parameters)
                {
                    success(xhr.responseText, success_parameters);
                }
                else
                {
                    success(xhr.responseText);
                }
            }
        }
        else if (xhr.readyState == 4 && xhr.status!= 200) {
            if (failure)
            {
                if (failure_parameters)
                {
                    failure(xhr.responseText, failure_parameters);
                }
                else
                {
                    failure(xhr.responseText);
                }
            }
        }
    };
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(params);
    return xhr;
}

/*********************************
 ** Convert JSON to QueryString **
 ********************************/
function serialize_obj (obj, prefix) {
    var str = [],
        p;
    for (p in obj) {
        if (obj.hasOwnProperty(p)) {
            var k = prefix ? prefix + "[" + p + "]" : p,
                v = obj[p];
            str.push((v !== null && typeof v === "object") ?
                serialize_obj(v, k) :
                encodeURIComponent(k) + "=" + encodeURIComponent(v));
        }
    }
    return str.join("&");
}

/****************************
 ** Plus/Minus Incrementer **
 ***************************/
function refresh_incrementer(clicked_element, what_to_do, which_page) {
    var father = clicked_element.closest('.number_incrementer');
    var incrementer_input_element = father.getElementsByTagName('input')[0];

    var temp_value = parseInt(incrementer_input_element.value);

    if (what_to_do == 'add')
    {
        temp_value += 1;
    }
    else if (what_to_do == 'minus')
    {
        temp_value -= 1;
    }

    if (temp_value <= 0)
    {
        temp_value = 1;
    }

    incrementer_input_element.value = temp_value;

    if (typeof which_page != 'undefined')
    {
        if (which_page == 'cart_page')
        {
            //we are in cart page and we need to refresh table
            //تغییر وضعیت جدول به حالت «در حال بروزرسانی جدول»
            var cart_table =  closest_parent(clicked_element, 'cart_table');
            var product_holder =  closest_parent(clicked_element, 'product_holder');

            add_class(cart_table, 'refreshing');
            add_class(product_holder, 'refreshing');

            var product_id = product_holder.getAttribute('product_id');

            var product_attributes = product_holder.getAttribute('attr');

            //با توجه به اینکه در سبد خرید هستیم، تعداد محصول توسط کاربر کم و زیاد میشود
            //بنابراین تعداد درخواستی را بصورت فورس برای اضافه کردن به سبد خرید میفرستیم
            add_to_cart (product_id, clicked_element, incrementer_input_element.value, function (result) {
                remove_class(cart_table, 'refreshing');
                remove_class(product_holder, 'refreshing');
            });
        }
    }
}

/*********************
 ** Show_suggestion **
 ********************/
function show_suggestion(clicked_thumbnail) {
    var father = closest_parent(clicked_thumbnail, "our_suggestion");
    var picture = father.getElementsByClassName("picture")[0];
    picture.innerHTML = "<img src='"+clicked_thumbnail.getAttribute("image_source")+"' />";

    var description = father.getElementsByClassName("description")[0];
    description.innerHTML = clicked_thumbnail.getAttribute("item_description");

    var read_more = father.querySelector('.read_more a');
    read_more.href = clicked_thumbnail.getAttribute("product_link");

    var add_to_cart_btn = father.querySelector('.add_to_cart_btn');
    add_to_cart_btn.setAttribute( "onClick", "add_to_cart("+clicked_thumbnail.getAttribute("product_id")+", this);" );

    if (clicked_thumbnail.getAttribute("can_be_purchased") == 'yes')
    {
        remove_class(add_to_cart_btn, 'w3-hide');
    }
    else
    {
        add_class(add_to_cart_btn, 'w3-hide');
    }

    var add_to_favorite_btn = father.querySelector('.add_to_favorite_btn');
    add_to_favorite_btn.setAttribute( "onClick", "add_to_favorite("+clicked_thumbnail.getAttribute("product_id")+", this);" );

    var product_holder = father.querySelector('.product_holder');
    product_holder.className = "product_holder product_item_" + clicked_thumbnail.getAttribute("product_id");
    product_holder.setAttribute( "product_title", clicked_thumbnail.getAttribute("product_title") );
    product_holder.setAttribute( "product_link", clicked_thumbnail.getAttribute("product_link") );
    product_holder.setAttribute( "product_price", clicked_thumbnail.getAttribute("product_price") );
    product_holder.setAttribute( "product_id", clicked_thumbnail.getAttribute("product_id") );
    product_holder.setAttribute( "attr", clicked_thumbnail.getAttribute("attr") );
    product_holder.setAttribute( "product_first_image_src", clicked_thumbnail.getAttribute("product_first_image_src") );
    product_holder.setAttribute( "has_required_option", clicked_thumbnail.getAttribute("has_required_option") );
}

/************************************
 ** Find Closest Parent with Class **
 ***********************************/
function closest_parent (current_element, class_name) {
    var parent = current_element.parentElement;
    if (parent) {
        if (parent.className.indexOf(class_name) >= 0)
        {
            //We found requested parent
            return parent;
        }
        else
        {
            return closest_parent (parent, class_name);
        }
    }
    else
    {
        return false;
    }
}

/*****************************
 ** Show/Hide Products Tabs **
 ****************************/
function change_tabs(evt, tab_name) {
    var i, x, tablinks;
    x = document.getElementsByClassName("tab");
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tab_name).style.display = "block";
    evt.currentTarget.className += " active";
}

/**********************
 ** TOGGLE ACCORDION **
 *********************/
function toggle_accordion(clicked_element) {
    var duration = 30;//milliseconds
    if (typeof accordion_timer != "undefined")
    {
        clearInterval(accordion_timer);
    }

    var father = closest_parent (clicked_element, 'accordion_item');
    var accordion_content = father.getElementsByClassName("content")[0];
    var destination_height = 0;

    var current_height = accordion_content.offsetHeight;

    if (current_height == 0)
    {
        accordion_content.style.height = "auto";
        destination_height = accordion_content.offsetHeight;

        accordion_content.style.height = "0px";
    }

    //accordion_content.style.height = destination_height+"px";

    if (current_height > destination_height)
    {
        //accordion should close
        father.className += " close";
        father.className = father.className.replace(/open/g, "");

        //var timer = duration, minutes, seconds;
        accordion_timer = setInterval(function () {
            current_height = parseInt(current_height / 2);
            accordion_content.style.height = current_height+"px";

            if (current_height <= 0) {
                clearInterval(accordion_timer);
            }
        }, duration);
    }
    else
    {
        //accordion should open
        father.className += " open";
        father.className = father.className.replace(/close/g, "");
        accordion_timer = setInterval(function () {
            var jump_step = parseInt((destination_height - current_height) / 2);
            jump_step = (jump_step == 0) ? 1 : jump_step;
            current_height = current_height + jump_step;

            accordion_content.style.height = current_height+"px";

            if (current_height >= destination_height) {
                clearInterval(accordion_timer);
            }
        }, duration);
    }

    father.className = father.className.replace(/  /g, " ");
}

/********************************
 ** Convert Number to Currency **
 *******************************/
function convert_to_currency(number) {
    if (isNaN(number))
    {
        number = 0;
    }
    number = number + '';//make sure it's string
    //console.log("typeof " + number, typeof number);
    var number_length = number.length;
    var temp_output = "";

    var index_from_right = 1;
    for (var i = number_length - 1; i >= 0; i--)
    {
        temp_output = number[i] + temp_output;
        //console.log(temp_output, index_from_right, index_from_right % 3);
        if (index_from_right % 3 === 0 && index_from_right !== number_length)
        {
            temp_output = ',' + temp_output;
        }

        index_from_right ++;
    }

    return temp_output;
}

/******************
 ** Toggle Class **
 *****************/
function toggle_class(element, class_name) {
    if (element.classList) {
        element.classList.toggle(class_name);
    } else {
        // For IE9
        var classes = element.className.split(" ");
        var i = classes.indexOf(class_name);

        if (i >= 0)
            classes.splice(i, 1);
        else
            classes.push(class_name);

        element.className = classes.join(" ");
    }
}

/******************
 ** Remove Class **
 *****************/
function remove_class(element, class_name) {
    //console.log(element, class_name);
    var regexp = new RegExp("\\b"+class_name+"\\b", "g");
    element.className = element.className.replace(regexp, "");
    element.className = element.className.replace("/  /g", " ");
}

/***************
 ** Add Class **
 **************/
function add_class(element, class_name) {
    remove_class(element, class_name);

    element.className += " "+class_name;
    element.className = element.className.replace(/  /g, " ");
}

/*****************
 ** Add To Cart **
 ****************/
function add_to_cart_old (product_id, product_attributes) {
    product_id = parseInt(product_id);
    //convert to json format
    //{"id":7, "attribute":[{"tedad":1},{"color":"red"}]}
    //var product_id = clicked_obj.getAttribute('product_id');
    //var product_attributes = JSON.parse(clicked_obj.getAttribute('attr'));
    if (typeof product_attributes == "string")
    {
        product_attributes = JSON.parse(product_attributes);
    }

    //Check shopping cart to know if there is any product added before
    var shopping_cart = document.getElementById('shopping_cart');

    var products = shopping_cart.getElementsByClassName('item_holder');
    [].forEach.call(products, function (product_row) {
        var row_product_id = product_row.getAttribute('product_id');
        var row_product_count = parseInt(product_row.getAttribute('product_count'));

        if (row_product_id == product_id)
        {
            product_attributes.tedad = parseInt(product_attributes.tedad) + row_product_count;
        }
    });
    product_attributes = JSON.stringify(product_attributes);
    //console.log(product_attributes);
    var send_to_server = {"req":"add_to_cart", "id":product_id, 'attr':product_attributes};
    //console.log(JSON.stringify(send_to_server));
    var url = base_url + 'api/index';//"<?=base_url('api/index')?>";//'http://localhost/keshavarz/client/api/index'

    postAjax(url, send_to_server, function(result, pp){
        //console.log(result);

        result = JSON.parse(result);
        refresh_cart (result.cart, result.added);
        /*
         var total_sum = 0;
         var number_of_items = 0;
         var temp_html = '';
         var temp_cart_list = result.cart;
         for (var temp_key in temp_cart_list) {
         var current_item = temp_cart_list[temp_key];
         //get current_item properties
         var target_element = document.getElementById('add_to_cart_'+temp_key);
         var product_title = target_element.getAttribute('product_title');
         var product_link = target_element.getAttribute('product_link');
         var product_price = parseInt(target_element.getAttribute('product_price'));
         var product_first_image_src = target_element.getAttribute('product_first_image_src');

         if (result.added == temp_key)
         {
         //create a popup to show that item is added to the cart
         added_to_cart(product_title, product_link, product_first_image_src);
         }

         temp_html +=
         '<tr class="item_holder" product_id="'+temp_key+'" product_price="'+product_price+'" product_count="'+current_item.tedad+'">\n' +
         '<td class="remove_btn" onclick="remove_from_cart(this, '+temp_key+')">&times;</td>\n' +
         '<td class="photo"><img src="'+product_first_image_src+'"></td>\n' +
         '<td class="title"><a href="'+product_link+'">'+product_title+'</a></td>\n' +
         '<td class="count">'+current_item.tedad+'</td>\n' +
         '<td class="price">'+convert_to_currency(product_price)+'</td>\n' +
         '</tr>';

         total_sum += product_price * parseInt(current_item.tedad);

         number_of_items++;
         }

         if (temp_html == '')
         {
         //there is no item into the cart
         temp_html = "<tr><td colspan='5'>موردی در سبد خرید وجود ندارد.</td></tr>";
         }

         temp_html =
         '<thead>\n' +
         '<tr class="header_row">\n' +
         '<td class="remove_btn">حذف</td>\n' +
         '<td class="photo">تصویر</td>\n' +
         '<td class="title">عنوان</td>\n' +
         '<td class="count">تعداد</td>\n' +
         '<td class="price">قیمت (تومان)</td>\n' +
         '</tr>\n' +
         '</thead>'+
         temp_html;

         document.querySelector('.shopping_cart_items_holder .items').innerHTML = temp_html;

         var total_sum_holder = document.querySelector('.shopping_cart_items_holder .total_sum');
         var shopping_cart_btn_price = document.querySelector('.shopping_cart_btn .total_price');

         shopping_cart_btn_price.innerHTML = total_sum_holder.innerHTML = convert_to_currency(total_sum);
         total_sum_holder.setAttribute('total_sum', total_sum);

         var items_inside_the_cart = document.querySelector('.shopping_cart_btn .items_inside_the_cart');
         items_inside_the_cart.innerHTML = number_of_items;
         */
    }, {"shopping_cart":shopping_cart});
}

/*****************
 ** Add To Cart **
 ****************/
function add_to_cart (product_id, clicked_element, force_tedad, callback) {
    var product_holder;

    if (typeof clicked_element == 'undefined')
    {
        product_holder = document.getElementsByClassName('product_holder product_item_'+product_id)[0];
    }
    else
    {
        product_holder = closest_parent(clicked_element, 'product_holder');
    }

    //بررسی میکنیم که اگر محصول انتخاب شده، آپشن اجباری داشته باشد، میبایست کاربر را به صفحه خود محصول ارجاع دهیم
    //در صفحه خود محصول، کاربر میتواند همه آپشنها را مشاهده کرده و آپشن های اجباری را نیز تکمیل کند
    var has_required_option = product_holder.getAttribute('has_required_option');
    if (has_required_option == 'yes')
    {
        var product_title = product_holder.getAttribute('product_title');
        alert ("محصول «" + product_title + "» خصوصیتهای متنوعی دارد که شما میبایست در صفحه خود محصول آنرا تعیین نمایید.");

        var product_link = product_holder.getAttribute('product_link');
        window.location.replace(product_link);

        return true;
    }

    var product_attributes = {};

    if (typeof force_tedad == 'undefined')
    {
        //تعداد محصول فورس نشده است
        //در این حالت میبایست بررسی کنیم که آیا محصول قبلا در سبد خرید درج شده بوده است یا خیر؟
        //اگر محصول در سبد خرید درج شده بود، میبایست تعداد درخواستی جدید را به تعداد موجود در سبد خرید اضافه کنیم
        //و مجموع را به عنوان تعداد نهایی محصول، به سرور ارسال کنیم
        if (! product_holder)
        {
            //there is no element node with product_holder class for requested product
            product_attributes = '{tedad: 1}';
        }
        else
        {
            //we found a node that is holding requested product
            product_attributes = product_holder.getAttribute('attr');
        }

        product_id = parseInt(product_id);
        //convert to json format
        //{"id":7, "attribute":[{"tedad":1},{"color":"red"}]}
        //var product_id = clicked_obj.getAttribute('product_id');
        //var product_attributes = JSON.parse(clicked_obj.getAttribute('attr'));
        if (typeof product_attributes == "string")
        {
            product_attributes = JSON.parse(product_attributes);
        }


        //Check shopping cart to know if there is any product added before
        var shopping_cart = document.getElementById('shopping_cart');

        var products = shopping_cart.getElementsByClassName('item_holder');
        [].forEach.call(products, function (product_row) {
            var row_product_id = product_row.getAttribute('product_id');
            var row_product_count = parseInt(product_row.getAttribute('product_count'));

            if (row_product_id == product_id)
            {
                product_attributes.tedad = parseInt(product_attributes.tedad) + row_product_count;
            }
        });
    }
    else
    {
        //تعداد محصول فورس شده است
        //بنابراین تعداد فورس شده را برای درج در سبد خرید به سرور ارسال میکنیم
        product_attributes.tedad = force_tedad;
    }

    product_attributes = JSON.stringify(product_attributes);
    //console.log(product_attributes);
    var send_to_server = {"req":"add_to_cart", "id":product_id, 'attr':product_attributes};
    //console.log(JSON.stringify(send_to_server));
    var url = base_url + 'api/index';//"<?=base_url('api/index')?>";//'http://localhost/keshavarz/client/api/index'

    postAjax(url, send_to_server, function(result, pp){
        //console.log(result);

        if (typeof pp.callback != 'undefined')
        {
            pp.callback (result);
        }

        result = JSON.parse(result);
        refresh_cart (result.cart, result.added);
        /*
         var total_sum = 0;
         var number_of_items = 0;
         var temp_html = '';
         var temp_cart_list = result.cart;
         for (var temp_key in temp_cart_list) {
         var current_item = temp_cart_list[temp_key];
         //get current_item properties
         var target_element = document.getElementById('add_to_cart_'+temp_key);
         var product_title = target_element.getAttribute('product_title');
         var product_link = target_element.getAttribute('product_link');
         var product_price = parseInt(target_element.getAttribute('product_price'));
         var product_first_image_src = target_element.getAttribute('product_first_image_src');

         if (result.added == temp_key)
         {
         //create a popup to show that item is added to the cart
         added_to_cart(product_title, product_link, product_first_image_src);
         }

         temp_html +=
         '<tr class="item_holder" product_id="'+temp_key+'" product_price="'+product_price+'" product_count="'+current_item.tedad+'">\n' +
         '<td class="remove_btn" onclick="remove_from_cart(this, '+temp_key+')">&times;</td>\n' +
         '<td class="photo"><img src="'+product_first_image_src+'"></td>\n' +
         '<td class="title"><a href="'+product_link+'">'+product_title+'</a></td>\n' +
         '<td class="count">'+current_item.tedad+'</td>\n' +
         '<td class="price">'+convert_to_currency(product_price)+'</td>\n' +
         '</tr>';

         total_sum += product_price * parseInt(current_item.tedad);

         number_of_items++;
         }

         if (temp_html == '')
         {
         //there is no item into the cart
         temp_html = "<tr><td colspan='5'>موردی در سبد خرید وجود ندارد.</td></tr>";
         }

         temp_html =
         '<thead>\n' +
         '<tr class="header_row">\n' +
         '<td class="remove_btn">حذف</td>\n' +
         '<td class="photo">تصویر</td>\n' +
         '<td class="title">عنوان</td>\n' +
         '<td class="count">تعداد</td>\n' +
         '<td class="price">قیمت (تومان)</td>\n' +
         '</tr>\n' +
         '</thead>'+
         temp_html;

         document.querySelector('.shopping_cart_items_holder .items').innerHTML = temp_html;

         var total_sum_holder = document.querySelector('.shopping_cart_items_holder .total_sum');
         var shopping_cart_btn_price = document.querySelector('.shopping_cart_btn .total_price');

         shopping_cart_btn_price.innerHTML = total_sum_holder.innerHTML = convert_to_currency(total_sum);
         total_sum_holder.setAttribute('total_sum', total_sum);

         var items_inside_the_cart = document.querySelector('.shopping_cart_btn .items_inside_the_cart');
         items_inside_the_cart.innerHTML = number_of_items;
         */
    }, {"shopping_cart":shopping_cart, "callback": callback});
}


/***************************
 ** PRODUCT ADDED TO CART **
 **************************/
function added_to_cart (product_title, product_link)
{
    var added_to_cart_popup_holder = document.querySelector("div.added_to_cart_popup_holder");

    if (! added_to_cart_popup_holder)
    {
        //Cart Popup Holder not found (seems it's not loaded into the page)
        //then we can't show any popup
    }
    else
    {
        var new_added_str =
            '<div class="popup_item" style="opacity: 0;">\n' +
            '   <div class="close" onclick="this.parentNode.parentNode.removeChild(this.parentNode);">&#215;</div>\n' +
            '   <div class="content">محصول <a class="product" href="{{product_link}}">{{product_title}}</a> به <a class="cart" href="#">سبد خرید</a> اضافه شد.</div>\n' +
            '</div>';

        new_added_str = new_added_str.replace(/{{product_link}}/g, product_link);
        new_added_str = new_added_str.replace(/{{product_title}}/g, product_title);

        added_to_cart_popup_holder.insertAdjacentHTML("beforeend", new_added_str);

        var current_last_child = added_to_cart_popup_holder.lastChild;

        var cart_popup_interval = setInterval(function () {
            //show element
            current_last_child.style.opacity = 1;
            clearInterval(cart_popup_interval);

            //hide element
            cart_popup_interval = setInterval(function () {
                current_last_child.style.opacity = 0;
                clearInterval(cart_popup_interval);

                //remove element
                cart_popup_interval = setInterval(function () {
                    current_last_child.parentElement.removeChild(current_last_child);
                    clearInterval(cart_popup_interval);
                }, 500);

            }, 2000);
        }, 100);
    }
}

/*********************
 ** Add To Favorite **
 ********************/
function add_to_favorite (product_id, clicked_element) {
    var send_to_server = {"req":"add_to_favorite", "id":product_id};
    //console.log(JSON.stringify(send_to_server));
    var url = base_url + 'api/index';//"<?=base_url('api/index')?>";//'http://localhost/keshavarz/client/api/index'

    //add Favorite class to prdoduct holder
    var product_holder_node_list = document.getElementsByClassName('product_holder product_item_'+product_id);

    [].forEach.call(product_holder_node_list, function(value) {
        add_class(value, 'favorite');
    });

    postAjax(
        url,
        send_to_server,
        function(result, pp){
            console.log(result);
            result = JSON.parse(result);

            //get current_item properties
            //var target_element = document.getElementById('add_to_favorite_'+pp.product_id);

            if (result.status == 'successful')
            {
                var target_element = pp.product_holder_node_list[0];
                var product_title = target_element.getAttribute('product_title');
                var product_link = target_element.getAttribute('product_link');
                var product_price = parseInt(target_element.getAttribute('product_price'));
                var product_first_image_src = target_element.getAttribute('product_first_image_src');

                added_to_favorite (product_title, product_link);
            }
            else
            {
                [].forEach.call(pp.product_holder_node_list, function(value) {
                    remove_class(value, 'favorite');
                });

                if (result.message)
                {
                    alert (result.message);
                }
            }
        },
        {"product_id":product_id, "product_holder_node_list": product_holder_node_list},
        function(result, pp){
            //console.log(result);
            //result = JSON.parse(result);
            alert ('بنظر میرسد ارتباط شما با سرور قطع شده است. لطفا بعدا مجددا امتحان نمایید.');
            [].forEach.call(pp.product_holder_node_list, function(value) {
                remove_class(value, 'favorite');
            });
        },
        {"product_holder_node_list": product_holder_node_list}
    );
}

/*******************************
 ** PRODUCT ADDED TO FAVORITE **
 ******************************/
function added_to_favorite (product_title, product_link)
{
    var added_to_cart_popup_holder = document.querySelector("div.added_to_cart_popup_holder");

    if (! added_to_cart_popup_holder)
    {
        //Cart Popup Holder not found (seems it's not loaded into the page)
        //then we can't show any popup
    }
    else
    {
        var new_added_str =
            '<div class="popup_item favorite" style="opacity: 0;">\n' +
            '   <div class="close" onclick="this.parentNode.parentNode.removeChild(this.parentNode);">&#215;</div>\n' +
            '   <div class="content">محصول <a class="product" href="{{product_link}}">{{product_title}}</a> به <a class="cart" href="#">لیست علاقه‌مندی‌ها</a> اضافه شد.</div>\n' +
            '</div>';

        new_added_str = new_added_str.replace(/{{product_link}}/g, product_link);
        new_added_str = new_added_str.replace(/{{product_title}}/g, product_title);

        added_to_cart_popup_holder.insertAdjacentHTML("beforeend", new_added_str);

        var current_last_child = added_to_cart_popup_holder.lastChild;

        var cart_popup_interval = setInterval(function () {
            //show element
            current_last_child.style.opacity = 1;
            clearInterval(cart_popup_interval);

            //hide element
            cart_popup_interval = setInterval(function () {
                current_last_child.style.opacity = 0;
                clearInterval(cart_popup_interval);

                //remove element
                cart_popup_interval = setInterval(function () {
                    current_last_child.parentElement.removeChild(current_last_child);
                    clearInterval(cart_popup_interval);
                }, 500);

            }, 2000);
        }, 100);
    }
}

/******************
 ** Refresh Cart **
 *****************/
function refresh_cart_old (cart_list_object, added_recently) {
    added_recently = typeof added_recently != 'undefined' ? added_recently : undefined;
    var total_sum = 0;
    var number_of_items = 0;
    var temp_html = '';
    var temp_cart_list = cart_list_object;
    for (var temp_key in temp_cart_list) {
        var current_item = temp_cart_list[temp_key];
        //get current_item properties
        var target_element = document.getElementById('add_to_cart_'+temp_key);
        var product_title = target_element.getAttribute('product_title');
        var product_link = target_element.getAttribute('product_link');
        var product_price = parseInt(target_element.getAttribute('product_price'));
        var product_first_image_src = target_element.getAttribute('product_first_image_src');

        if (added_recently == temp_key)
        {
            //create a popup to show that item is added to the cart
            added_to_cart(product_title, product_link, product_first_image_src);
        }

        temp_html +=
            '<tr class="item_holder" product_id="'+temp_key+'" product_price="'+product_price+'" product_count="'+current_item.tedad+'">\n' +
            '<td class="remove_btn" onclick="remove_from_cart(this, '+temp_key+')">&times;</td>\n' +
            '<td class="photo"><img src="'+product_first_image_src+'"></td>\n' +
            '<td class="title"><a href="'+product_link+'">'+product_title+'</a></td>\n' +
            '<td class="count">'+current_item.tedad+'</td>\n' +
            '<td class="price">'+convert_to_currency(product_price)+'</td>\n' +
            '</tr>';

        total_sum += product_price * parseInt(current_item.tedad);

        number_of_items++;
    }

    if (temp_html == '')
    {
        //there is no item into the cart
        temp_html = "<tr><td colspan='5'>موردی در سبد خرید وجود ندارد.</td></tr>";
    }

    temp_html =
        '<thead>\n' +
        '<tr class="header_row">\n' +
        '<td class="remove_btn">حذف</td>\n' +
        '<td class="photo">تصویر</td>\n' +
        '<td class="title">عنوان</td>\n' +
        '<td class="count">تعداد</td>\n' +
        '<td class="price">قیمت (تومان)</td>\n' +
        '</tr>\n' +
        '</thead>'+
        temp_html;

    document.querySelector('.shopping_cart_items_holder .items').innerHTML = temp_html;

    var total_sum_holder = document.querySelector('.shopping_cart_items_holder .total_sum');
    var shopping_cart_btn_price = document.querySelector('.shopping_cart_btn .total_price');

    shopping_cart_btn_price.innerHTML = total_sum_holder.innerHTML = convert_to_currency(total_sum);
    total_sum_holder.setAttribute('total_sum', total_sum);

    var items_inside_the_cart = document.querySelector('.shopping_cart_btn .items_inside_the_cart');
    items_inside_the_cart.innerHTML = number_of_items;
}

/******************
 ** Refresh Cart **
 *****************/
function refresh_cart (cart_list_object, added_recently) {
    added_recently = typeof added_recently != 'undefined' ? added_recently : undefined;
    var total_sum = 0;
    var number_of_items = 0;
    var temp_html = '';
    var temp_cart_list = cart_list_object;
    for (var temp_key in temp_cart_list) {
        var current_item = temp_cart_list[temp_key];
        //get current_item properties
        var target_element = document.getElementsByClassName('product_holder product_item_'+temp_key)[0];
        var product_title = 'محصول یافت نشد';
        var product_link = '#';
        var product_price = '0';
        var product_first_image_src = '#';

        if (typeof target_element == 'undefined')
        {
            //محصول با مشخصات ذکر شده را نمیتوانیم پیدا کنیم.
            //احتمالا در صفحه «سبد خرید» هستیم یا شاید محصول در صفحه دیگری به سبد خرید اضافه شده است
            //و حالا در صفحه دیگری در حال اضافه کردن محصول دیگری هستیم
            var product_title = current_item.product_title;
            var product_link = current_item.product_link;
            var product_price = parseInt(current_item.product_price);
            var product_first_image_src = current_item.product_first_image_src;
        }
        else
        {
            product_title = target_element.getAttribute('product_title');
            product_link = target_element.getAttribute('product_link');
            product_price = parseInt(target_element.getAttribute('product_price'));
            product_first_image_src = target_element.getAttribute('product_first_image_src');
        }

        if (added_recently == temp_key)
        {
            //create a popup to show that item is added to the cart
            added_to_cart(product_title, product_link, product_first_image_src);
        }

        temp_html +=
            '<tr class="item_holder" product_id="'+temp_key+'" product_price="'+product_price+'" product_count="'+current_item.tedad+'">\n' +
            '<td class="remove_btn" onclick="remove_from_cart('+temp_key+')">&times;</td>\n' +
            '<td class="photo"><img src="'+product_first_image_src+'"></td>\n' +
            '<td class="title"><a href="'+product_link+'">'+product_title+'</a></td>\n' +
            '<td class="count">'+current_item.tedad+'</td>\n' +
            '<td class="price">'+convert_to_currency(product_price)+'</td>\n' +
            '</tr>';

        total_sum += product_price * parseInt(current_item.tedad);

        number_of_items++;
    }

    if (temp_html == '')
    {
        //there is no item into the cart
        temp_html = "<tr><td colspan='5'>موردی در سبد خرید وجود ندارد.</td></tr>";
    }

    temp_html =
        '<thead>\n' +
        '<tr class="header_row">\n' +
        '<td class="remove_btn">حذف</td>\n' +
        '<td class="photo">تصویر</td>\n' +
        '<td class="title">عنوان</td>\n' +
        '<td class="count">تعداد</td>\n' +
        '<td class="price">قیمت (تومان)</td>\n' +
        '</tr>\n' +
        '</thead>'+
        temp_html;

    var shopping_cart_items_holder = document.querySelector('.shopping_cart_items_holder .items');
    if (shopping_cart_items_holder)
    {
        document.querySelector('.shopping_cart_items_holder .items').innerHTML = temp_html;

        var total_sum_holder = document.querySelector('.shopping_cart_items_holder .total_sum');
        var shopping_cart_btn_price = document.querySelector('.shopping_cart_btn .total_price');

        shopping_cart_btn_price.innerHTML = total_sum_holder.innerHTML = convert_to_currency(total_sum);
        total_sum_holder.setAttribute('total_sum', total_sum);

        var items_inside_the_cart = document.querySelector('.shopping_cart_btn .items_inside_the_cart');
		var how_many_itmes_is_in_cart_menu_counter = document.getElementById('how_many_itmes_is_in_cart_menu_counter');
        items_inside_the_cart.innerHTML = number_of_items;
		how_many_itmes_is_in_cart_menu_counter.innerHTML = '<span>'+number_of_items+'</span>';
    }
    else
    {
        //ماژول سبد خرید پیدا نشد
    }
}

/**********************
 ** Remove From Cart **
 *********************/
function remove_from_cart_old (clicked_item, product_id) {
    var send_to_server = {"req":"remove_from_cart", "id":product_id};
    var url = base_url + 'api/index';//"<?=base_url('api/index')?>";//'http://localhost/keshavarz/client/api/index'

    var father_of_selected_node = clicked_item.parentElement;
    father_of_selected_node.style.display = "none";

    var temp_count = parseInt(father_of_selected_node.getAttribute('product_count'));
    var temp_price = parseInt(father_of_selected_node.getAttribute('product_price'));

    var total_sum_holder = document.querySelector('.shopping_cart_items_holder .total_sum');
    var old_total_price = parseInt(total_sum_holder.getAttribute('total_sum'));
    var temp_new_price = old_total_price - (temp_count * temp_price);
    total_sum_holder.setAttribute('total_sum', temp_new_price);

    var shopping_cart_btn_price = document.querySelector('.shopping_cart_btn .total_price');

    shopping_cart_btn_price.innerHTML = total_sum_holder.innerHTML = convert_to_currency(temp_new_price);

    var items_inside_the_cart = document.querySelector('.shopping_cart_btn .items_inside_the_cart');
    items_inside_the_cart.innerHTML = parseInt(items_inside_the_cart.innerHTML) - 1;
	var how_many_itmes_is_in_cart_menu_counter = document.getElementById('how_many_itmes_is_in_cart_menu_counter');
	how_many_itmes_is_in_cart_menu_counter.innerHTML = '<span>'+items_inside_the_cart.innerHTML+'</span>';

    postAjax(url, send_to_server, function(result, pp){
        result = JSON.parse(result);

        if (result.status == 'successful')
        {
            pp.clicked_item.parentElement.parentElement.removeChild(pp.clicked_item.parentElement);
        }
        else
        {
            pp.clicked_item.parentElement.style.display = "table-row";
            pp.shopping_cart_btn_price.innerHTML = pp.total_sum_holder.innerHTML = convert_to_currency(pp.old_total_price);
            pp.total_sum_holder.setAttribute('total_sum', pp.old_total_price);

            pp.items_inside_the_cart.innerHTML = parseInt(pp.items_inside_the_cart.innerHTML) + 1;
        }

    }, {"clicked_item":clicked_item,'total_sum_holder':total_sum_holder, 'old_total_price':old_total_price,'shopping_cart_btn_price':shopping_cart_btn_price, 'items_inside_the_cart':items_inside_the_cart}, function(result, pp){
        //something is wrong and we have error to have a connection with server
        pp.clicked_item.parentElement.style.display = "table-row";
        pp.shopping_cart_btn_price.innerHTML = pp.total_sum_holder.innerHTML = convert_to_currency(pp.old_total_price);
        pp.total_sum_holder.setAttribute('total_sum', pp.old_total_price);

        pp.items_inside_the_cart.innerHTML = parseInt(pp.items_inside_the_cart.innerHTML) + 1;

    }, {"clicked_item":clicked_item,'total_sum_holder':total_sum_holder, 'old_total_price':old_total_price,'shopping_cart_btn_price':shopping_cart_btn_price, 'items_inside_the_cart':items_inside_the_cart});
}
/**********************
 ** Remove From Cart **
 *********************/
function remove_from_cart_yekam_old (clicked_item, product_id) {
    //ماژول سبد خرید را پیدا میکنیم و با استفاده از product_id ردیف مورد نظر را در ماژول سبد خرید
    //حذف کرده و سبد خرید را رفرش میکنیم
    var shopping_cart = document.getElementById('shopping_cart');

    if (shopping_cart)
    {
        //ماژول سبد خرید در صفحه پیدا شد
        //محصولاتیکه در سبد خرید لیست شده است را میگیریم
        var products = shopping_cart.getElementsByClassName('item_holder');
        [].forEach.call(products, function (product_row) {
            var row_product_id = product_row.getAttribute('product_id');
            //آیا محصولی با آیدی درخواستی در ماژول سبد خرید وجود دارد؟
            if (row_product_id == product_id)
            {
                var father_of_selected_node = product_row;
                father_of_selected_node.style.display = "none";

                var temp_count = parseInt(father_of_selected_node.getAttribute('product_count'));
                var temp_price = parseInt(father_of_selected_node.getAttribute('product_price'));

                var total_sum_holder = document.querySelector('.shopping_cart_items_holder .total_sum');
                var old_total_price = parseInt(total_sum_holder.getAttribute('total_sum'));
                var temp_new_price = old_total_price - (temp_count * temp_price);
                total_sum_holder.setAttribute('total_sum', temp_new_price);

                var shopping_cart_btn_price = document.querySelector('.shopping_cart_btn .total_price');

                shopping_cart_btn_price.innerHTML = total_sum_holder.innerHTML = convert_to_currency(temp_new_price);

                var items_inside_the_cart = document.querySelector('.shopping_cart_btn .items_inside_the_cart');
                items_inside_the_cart.innerHTML = parseInt(items_inside_the_cart.innerHTML) - 1;

                var send_to_server = {"req":"remove_from_cart", "id":product_id};
                var url = base_url + 'api/index';//"<?=base_url('api/index')?>";//'http://localhost/keshavarz/client/api/index'
                postAjax(url, send_to_server, function(result, pp){
                    //console.log(father_of_selected_node);
                    result = JSON.parse(result);

                    if (result.status == 'successful')
                    {
                        father_of_selected_node.parentElement.removeChild(father_of_selected_node);
                    }
                    else
                    {
                        father_of_selected_node.style.display = "table-row";
                        shopping_cart_btn_price.innerHTML = total_sum_holder.innerHTML = convert_to_currency(old_total_price);
                        total_sum_holder.setAttribute('total_sum', old_total_price);

                        items_inside_the_cart.innerHTML = parseInt(items_inside_the_cart.innerHTML) + 1;
                    }

                }, undefined, function(result, pp){
                    //something is wrong and we have error to have a connection with server
                    father_of_selected_node.style.display = "table-row";
                    shopping_cart_btn_price.innerHTML = total_sum_holder.innerHTML = convert_to_currency(old_total_price);
                    total_sum_holder.setAttribute('total_sum', old_total_price);

                    items_inside_the_cart.innerHTML = parseInt(items_inside_the_cart.innerHTML) + 1;

                }, undefined);
            }
        });
    }
    else
    {
        //ماژول سبد خرید در صفحه لود نشده است
    }
}

/**********************
 ** Remove From Cart **
 *********************/
function remove_from_cart (product_id) {
    //console.log("remove "+product_id);
    //آیتم مورد نظر را از ماژول سبد خرید حذف میکنیم
    remove_from_shopping_cart_module (product_id);
    //آیتم مورد نظر را از جدول سبد خرید (در صفحه سبد خرید) حذف میکنیم
    remove_from_cart_table (product_id);

    var send_to_server = {"req":"remove_from_cart", "id":product_id};
    var url = base_url + 'api/index';//"<?=base_url('api/index')?>";//'http://localhost/keshavarz/client/api/index'

    postAjax(url, send_to_server, function(result, pp){
        result = JSON.parse(result);
        //console.log(result);

        if (result.status == 'successful')
        {
            remove_from_shopping_cart_module (product_id, true);
            remove_from_cart_table (product_id, true);
        }
        else
        {
            return_back_item_from_cart_table (product_id);
            return_back_item_from_shopping_cart_module (product_id);
        }

    }, undefined, function(result, pp){
        //something is wrong and we have error to have a connection with server
        return_back_item_from_cart_table (product_id);
        return_back_item_from_shopping_cart_module (product_id);

    }, undefined);
}



/**************************************
 ** Remove From Shopping Cart Module **
 *************************************/
function remove_from_shopping_cart_module (product_id, remove_completely) {
    //ماژول سبد خرید را پیدا میکنیم و با استفاده از product_id ردیف مورد نظر را در ماژول سبد خرید
    //حذف کرده و سبد خرید را رفرش میکنیم
    var shopping_cart = document.getElementById('shopping_cart');

    if (shopping_cart)
    {
        //ماژول سبد خرید در صفحه پیدا شد
        //محصولاتیکه در سبد خرید لیست شده است را میگیریم
        var products = shopping_cart.getElementsByClassName('item_holder');
        [].forEach.call(products, function (product_row) {
            var row_product_id = product_row.getAttribute('product_id');
            //آیا محصولی با آیدی درخواستی در ماژول سبد خرید وجود دارد؟
            if (row_product_id == product_id)
            {
                if (remove_completely)
                {
                    //بطور کامل node را حذف میکنیم
                    product_row.parentElement.removeChild(product_row);
                }
                else
                {
                    //نود بطور موقت مخفی میشود
                    product_row.style.display = "none";

                    var temp_count = parseInt(product_row.getAttribute('product_count'));
                    var temp_price = parseInt(product_row.getAttribute('product_price'));

                    var total_sum_holder = shopping_cart.querySelector('.total_sum');
                    var old_total_price = parseInt(total_sum_holder.getAttribute('total_sum'));
                    var temp_new_price = old_total_price - (temp_count * temp_price);
                    total_sum_holder.setAttribute('total_sum', temp_new_price);

                    var shopping_cart_btn_price = document.querySelector('.shopping_cart_btn .total_price');

                    shopping_cart_btn_price.innerHTML = total_sum_holder.innerHTML = convert_to_currency(temp_new_price);

                    var items_inside_the_cart = document.querySelector('.shopping_cart_btn .items_inside_the_cart');
                    items_inside_the_cart.innerHTML = parseInt(items_inside_the_cart.innerHTML) - 1;

                    var how_many_itmes_is_in_cart_menu_counter = document.getElementById('how_many_itmes_is_in_cart_menu_counter');
					how_many_itmes_is_in_cart_menu_counter.innerHTML = '<span>'+items_inside_the_cart.innerHTML+'</span>';
                }
            }
        });
    }
    else
    {
        //ماژول سبد خرید در صفحه لود نشده است
        console.log("ماژول سبد خرید در صفحه لود نشده است.");
    }
}

/********************************************************
 ** Return back Removed Item From Shopping Cart Module **
 ********************************************************/
function return_back_item_from_shopping_cart_module (product_id) {
    //ماژول سبد خرید را پیدا میکنیم و با استفاده از product_id ردیف مورد نظر را در ماژول سبد خرید
    //پیدا کرده و آنرا ظاهر میکنیم و سبد خرید را رفرش میکنیم
    var shopping_cart = document.getElementById('shopping_cart');

    if (shopping_cart)
    {
        //ماژول سبد خرید در صفحه پیدا شد
        //محصولاتیکه در سبد خرید لیست شده است را میگیریم
        var products = shopping_cart.getElementsByClassName('item_holder');
        [].forEach.call(products, function (product_row) {
            var row_product_id = product_row.getAttribute('product_id');
            //آیا محصولی با آیدی درخواستی در ماژول سبد خرید وجود دارد؟
            if (row_product_id == product_id)
            {
                //نود نگهدارنده محصول را ظاهر میکنیم
                product_row.style.display = "table-row";

                var temp_count = parseInt(product_row.getAttribute('product_count'));
                var temp_price = parseInt(product_row.getAttribute('product_price'));

                var total_sum_holder = shopping_cart.querySelector('.total_sum');
                var old_total_price = parseInt(total_sum_holder.getAttribute('total_sum'));
                var temp_new_price = old_total_price + (temp_count * temp_price);
                total_sum_holder.setAttribute('total_sum', temp_new_price);

                var shopping_cart_btn_price = shopping_cart.querySelector('.total_price');

                shopping_cart_btn_price.innerHTML = total_sum_holder.innerHTML = convert_to_currency(temp_new_price);

                var items_inside_the_cart = shopping_cart.querySelector('.items_inside_the_cart');
                items_inside_the_cart.innerHTML = parseInt(items_inside_the_cart.innerHTML) + 1;

				var how_many_itmes_is_in_cart_menu_counter = document.getElementById('how_many_itmes_is_in_cart_menu_counter');
				how_many_itmes_is_in_cart_menu_counter.innerHTML = '<span>'+items_inside_the_cart.innerHTML+'</span>';
            }
        });
    }
    else
    {
        //ماژول سبد خرید در صفحه لود نشده است
        console.log("ماژول سبد خرید در صفحه لود نشده است.");
    }
}

/****************************
 ** Remove From Cart Table **
 ****************************/
function remove_from_cart_table (product_id, remove_completely) {
    //جدول سبد خرید را پیدا میکنیم و با استفاده از product_id ردیف مورد نظر را در جدول سبد خرید
    //حذف کرده و جدول سبد خرید را رفرش میکنیم
    var cart_table = document.getElementById('cart_table');

    if (cart_table)
    {
        //جدول سبد خرید در صفحه پیدا شد
        //محصولاتیکه در جدول سبد خرید لیست شده است را میگیریم
        var products = cart_table.getElementsByClassName('product_holder');
        [].forEach.call(products, function (product_row) {
            var row_product_id = product_row.getAttribute('product_id');
            //آیا محصولی با آیدی درخواستی در ماژول سبد خرید وجود دارد؟
            if (row_product_id == product_id)
            {
                if (remove_completely)
                {
                    //نود را بطور کامل از لیست حذف میکنیم
                    product_row.parentElement.removeChild(product_row);
                }
                else
                {
                    //نود را بطور موقت مخفی میکنیم
                    product_row.style.display = "none";

                    var incrementer_input = product_row.querySelector('.number_incrementer input');
                    var temp_count = parseInt(incrementer_input.value);
                    var temp_price = parseInt(product_row.getAttribute('product_price'));

                    var total_sum_holder = cart_table.querySelector('.total_sum');
                    var old_total_price = parseInt(total_sum_holder.getAttribute('total_sum'));
                    var temp_new_price = old_total_price - (temp_count * temp_price);
                    total_sum_holder.setAttribute('total_sum', temp_new_price);
                    //console.log(temp_count, temp_price, temp_new_price);
                    total_sum_holder.innerHTML = "<b>"+convert_to_currency(temp_new_price)+"</b>";
                }
            }
        });
    }
    else
    {
        //جدول سبد خرید در صفحه لود نشده است
        console.log('جدول سبد خرید در صفحه لود نشده است.')
    }
}

/**********************************************
 ** Return back Removed Item from Cart Table **
 **********************************************/
function return_back_item_from_cart_table (product_id) {
    //جدول سبد خرید را پیدا میکنیم و با استفاده از product_id ردیف مورد نظر را در جدول سبد خرید
    //پیدا کرده و آنرا ظاهر میکنیم و جدول سبد خرید را رفرش میکنیم
    var cart_table = document.getElementById('cart_table');

    if (cart_table)
    {
        //جدول سبد خرید در صفحه پیدا شد
        //محصولاتیکه در جدول سبد خرید لیست شده است را میگیریم
        var products = cart_table.getElementsByClassName('product_holder');
        [].forEach.call(products, function (product_row) {
            var row_product_id = product_row.getAttribute('product_id');
            //آیا محصولی با آیدی درخواستی در ماژول سبد خرید وجود دارد؟
            if (row_product_id == product_id)
            {
                //نود نگهدارنده محصول را ظاهر میکنیم
                product_row.style.display = "table-row";

                //محاسبات جدول محصول را به حالت قبل از حذف بر میگردانیم
                var incrementer_input = product_row.querySelector('.number_incrementer input');
                var temp_count = parseInt(incrementer_input.value);
                var temp_price = parseInt(product_row.getAttribute('product_price'));

                var total_sum_holder = cart_table.querySelector('.total_sum');
                var old_total_price = parseInt(total_sum_holder.getAttribute('total_sum'));
                var temp_new_price = old_total_price + (temp_count * temp_price);
                total_sum_holder.setAttribute('total_sum', temp_new_price);
                total_sum_holder.innerHTML = '<b>'+convert_to_currency(temp_new_price)+'</b>';
            }
        });
    }
    else
    {
        //جدول سبد خرید در صفحه لود نشده است
        console.log("جدول سبد خرید در صفحه لود نشده است.");
    }
}

/***********************
 ** Simple Search **
 **********************/
var simple_search_timer_peyda_kardan_products;
function find_products(input_field) {
    var node_asli = closest_parent(input_field, 'simple_search');
    if (node_asli)
    {
        var found_items_holder = node_asli.getElementsByClassName('found_items_holder')[0];
        var category_field = node_asli.getElementsByClassName('category')[0];

        //تایمرهای قبلی را پاک کن تا تایمر جدید در پایینتر تنظیم شود
        clearTimeout(simple_search_timer_peyda_kardan_products);

        if (input_field.value.length > 1 && category_field.value != '')
        {
            //بنویس در حال جستجو
            found_items_holder.innerHTML = 'در حال جستجو';
            found_items_holder.style.display = 'block';

            //اگه بیشتر از یک کاراکتر تایپ شد، مقدار تایپ شده را به سرور بفرست تا نتایج محصولات مشابه را برگرداند
            //قبل از فراخوانی AJAX، چند لحظه تاخیر قرار بده
            simple_search_timer_peyda_kardan_products = setTimeout(function(){
                //Send Delete request to server
                var send_to_server = {"req": "simple_search", "search_field":input_field.value, "category": category_field.value};
                var url = base_url + 'api/index';//'<?=base_url('api/index')?>';
                postAjax(
                    url,
                    send_to_server,
                    function (result, pp) {
                        //console.log("simple search response:", result);
                        result = JSON.parse(result);
                        //console.log("simple search response:", result);
                        //result is an array of objects. each object is something like this:
                        //{title: "محصول تستی", price: "2000", link: "http://amya.ir/demo/keshavarz/client/pages/single_product44", pic_link: "http://amya.ir/demo/keshavarz/content/products/no_pic.jpg"}
                        var mahsolat_peydashode = result;

                        //لیست محصولاتی که از سرور آمده است را بصورت option ایجاد کن
                        var option_list = "";
                        mahsolat_peydashode.forEach(function (radife_mahsol) {
                            option_list += '<div class="item"> <a href="'+radife_mahsol.link+'"> <div class="image"> <img src="'+radife_mahsol.pic_link+'"> </div> <div class="content"> <div class="title w3-large">'+radife_mahsol.title+'</div> <div class="price w3-small">'+convert_to_currency(radife_mahsol.price)+' تومان' +'</div> </div> </a> </div>';
                        });


                        if (option_list == "")
                        {
                            //موردی یافت نشد
                            found_items_holder.innerHTML = "موردی یافت نشد.";
                        }
                        else
                        {
                            //موارد پیدا شده را بصورت لیست نمایش بده
                            found_items_holder.innerHTML = option_list;
                        }
                    },
                    {"found_items_holder":found_items_holder},
                    function (result, pp) {
                        //Failure state
                        alert('در ارتباط با سرور خطایی رخ داده است.');
                        //We could not connect to server
                        pp.found_items_holder.style.display = 'none';
                    },
                    {"found_items_holder":found_items_holder}
                );
            }, 1000);
        }
        else
        {
            //لیست جستجوهای قبلی را مخفی کن
            found_items_holder.innerHTML = '';
            found_items_holder.style.display = 'none';
        }
    }
    else
    {
        console.log('node with "simple_search" class not found');
    }
}

/****************************
 ** Validate Email Address **
 ****************************/
function validate_email (email)
{
    var invalid_charachters = [" ", "\\", "/", "-"];

    invalid_charachters.forEach(function (value) {
        if (email.indexOf(value) >= 0)
        {
            return false;
        }
    });

    var index_of_atsign = email.indexOf('@');
    var part_before_atsign = index_of_atsign >= 0 ? email.substr(0, index_of_atsign) : "";
    var index_of_dot_after_atsign = email.indexOf('.', index_of_atsign);
    var part_between_atsign_and_dot = (index_of_atsign >= 0 && index_of_dot_after_atsign >= 0) ? email.substr(index_of_atsign+1, index_of_dot_after_atsign - index_of_atsign - 1) : "";
    var part_after_dot = index_of_dot_after_atsign >= 0 ? email.substr(index_of_dot_after_atsign + 1) : "";

    /*console.log("index_of_atsign", index_of_atsign);
     console.log("part_before_atsign", part_before_atsign);
     console.log("index_of_dot_after_atsign", index_of_dot_after_atsign);
     console.log("part_between_atsign_and_dot", part_between_atsign_and_dot);
     console.log("part_after_dot", part_after_dot);*/

    if (email.length < 6 || index_of_atsign < 0 || part_before_atsign.length < 2 || index_of_dot_after_atsign < 0 || part_between_atsign_and_dot.length < 2 || part_after_dot.length < 2)
    {
        return false;
    }
    else
    {
        return true;
    }
}

/*****************************
 ** Active All Module Forms **
 *****************************/
function active_module_forms () {
    var all_forms = document.getElementsByClassName('form_holder');
    [].forEach.call(all_forms, function (form_holder) {
        var form_id = form_holder.getAttribute('form_id');
        if (form_id) {
			var message_holder = form_holder.querySelector('.message_holder');

			switch (form_id) {
				case 'contact_us':
					var submit_btn = form_holder.querySelector('.submit_btn');
					submit_btn.onclick = function () {
						message_holder.innerHTML = '';
						var name = form_holder.querySelector('.name_field');
						var email = form_holder.querySelector('.email_field');
						var message = form_holder.querySelector('.message_field');

						var error_message = '';

						if (name.value.length < 2) {
							error_message += '<div>فیلد نام اجباری بوده و میبایست بیشتر از 1 کاراکتر باشد.</div>';
						}

						if (!validate_email(email.value)) {
							error_message += '<div>ایمیل درج شده معتبر نمیباشد.</div>';
						}

						if (message.value.length < 10) {
							error_message += '<div>متن پیام نمیتواند کمتر از 10 کاراکتر باشد.</div>';
						}

						if (error_message != '') {
							//show error message
							message_holder.innerHTML = '<div class="alert w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">×</span><div>' + error_message + '</div></div>';
						} else {
							//add loading
							add_class(form_holder, 'on_loading');

							//send form information to server
							var send_to_server = {
								"req": "save_form",
								"id": form_id,
								"name": name.value,
								"email": email.value,
								"message": message.value
							};
							//var url = base_url + 'api/index';//'http://localhost/keshavarz/client/api/index'
							var url = base_url + 'forms/' + form_id;

							postAjax(url, send_to_server, function (result) {
								result = JSON.parse(result);
								//console.log(result);
								remove_class(form_holder, 'on_loading');

								var message_type = result.error ? 'red' : 'green';

								//set message
								message_holder.innerHTML = '<div class="alert w3-pale-' + message_type + ' w3-round"><span class="closebtn" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">×</span><div>' + result.message + '</div></div>';

							}, undefined, function (result) {
								//something is wrong and we have error to have a connection with server
								remove_class(form_holder, 'on_loading');
								message_holder.innerHTML = '<div class="alert w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">×</span><div>بنظر ارتباط شما با سرور قطع شده است. لطفا مجدد تلاش نمایید. در صورتیکه چندمین مرتبه است این پیغام را مشاهده میکنید، با ارائه دهنده سرویس اینترنت خود تماس بگیرید.</div></div>';

							}, undefined);
						}
					};
					break;
				case 'complaint':
					var submit_btn = form_holder.querySelector('.submit_btn');
					submit_btn.onclick = function () {
						message_holder.innerHTML = '';
						var name = form_holder.querySelector('.name_field');
						var phone = form_holder.querySelector('.phone_field');
						var email = form_holder.querySelector('.email_field');
						var message = form_holder.querySelector('.message_field');

						var error_message = '';

						if (name.value.length < 2) {
							error_message += '<div>فیلد نام اجباری بوده و میبایست بیشتر از 1 کاراکتر باشد.</div>';
						}

						if (phone.value.length != 11) {
							error_message += '<div>فیلد شماره تماس اجباری بوده و میبایست دقیقا 11 کاراکتر باشد.</div>';
						}

						if (!validate_email(email.value)) {
							error_message += '<div>ایمیل درج شده معتبر نمیباشد.</div>';
						}

						if (message.value.length < 10) {
							error_message += '<div>متن پیام نمیتواند کمتر از 10 کاراکتر باشد.</div>';
						}

						if (error_message != '') {
							//show error message
							message_holder.innerHTML = '<div class="alert w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">×</span><div>' + error_message + '</div></div>';
						} else {
							//add loading
							add_class(form_holder, 'on_loading');

							//send form information to server
							var send_to_server = {
								"req": "save_form",
								"id": form_id,
								"name": name.value,
								"phone": phone.value,
								"email": email.value,
								"complaint": message.value
							};
							//var url = base_url + 'api/index';//'http://localhost/keshavarz/client/api/index'
							var url = base_url + 'forms/' + form_id;

							postAjax(url, send_to_server, function (result) {
								result = JSON.parse(result);
								//console.log(result);
								remove_class(form_holder, 'on_loading');

								var message_type = result.error ? 'red' : 'green';

								//set message
								message_holder.innerHTML = '<div class="alert w3-pale-' + message_type + ' w3-round"><span class="closebtn" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">×</span><div>' + result.message + '</div></div>';

							}, undefined, function (result) {
								//something is wrong and we have error to have a connection with server
								remove_class(form_holder, 'on_loading');
								message_holder.innerHTML = '<div class="alert w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">×</span><div>بنظر ارتباط شما با سرور قطع شده است. لطفا مجدد تلاش نمایید. در صورتیکه چندمین مرتبه است این پیغام را مشاهده میکنید، با ارائه دهنده سرویس اینترنت خود تماس بگیرید.</div></div>';

							}, undefined);
						}
					};
					break;
				case 'question_and_answer':
					var submit_btn = form_holder.querySelector('.submit_btn');
					submit_btn.onclick = function () {
						message_holder.innerHTML = '';
						var name = form_holder.querySelector('.name_field');
						var email = form_holder.querySelector('.email_field');
						var question = form_holder.querySelector('.question_field');

						var error_message = '';

						if (name.value.length < 2) {
							error_message += '<div>فیلد نام اجباری بوده و میبایست بیشتر از 1 کاراکتر باشد.</div>';
						}

						if (!validate_email(email.value)) {
							error_message += '<div>ایمیل درج شده معتبر نمیباشد.</div>';
						}

						if (question.value.length < 10) {
							error_message += '<div>متن سوال نمیتواند کمتر از 10 کاراکتر باشد.</div>';
						}

						if (error_message != '') {
							//show error message
							message_holder.innerHTML = '<div class="alert w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">×</span><div>' + error_message + '</div></div>';
						} else {
							//add loading
							add_class(form_holder, 'on_loading');

							//مقدار ID محصول را میگیریم
							var product_holder = closest_parent(form_holder, 'product_holder');
							var product_id = product_holder.getAttribute('product_id');

							//send form information to server
							var send_to_server = {
								"req": "save_form",
								"id": form_id,
								"first_name": name.value,
								"email": email.value,
								"question": question.value
							};
							//var url = base_url + 'api/index';//'http://localhost/keshavarz/client/api/index'
							var url = base_url + 'forms/' + form_id + '/' + product_id;

							postAjax(url, send_to_server, function (result) {
								result = JSON.parse(result);
								//console.log(result);
								remove_class(form_holder, 'on_loading');

								var message_type = result.error ? 'red' : 'green';

								//set message
								message_holder.innerHTML = '<div class="alert w3-pale-' + message_type + ' w3-round"><span class="closebtn" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">×</span><div>' + result.message + '</div></div>';

							}, undefined, function (result) {
								//something is wrong and we have error to have a connection with server
								remove_class(form_holder, 'on_loading');
								message_holder.innerHTML = '<div class="alert w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">×</span><div>بنظر ارتباط شما با سرور قطع شده است. لطفا مجدد تلاش نمایید. در صورتیکه چندمین مرتبه است این پیغام را مشاهده میکنید، با ارائه دهنده سرویس اینترنت خود تماس بگیرید.</div></div>';

							}, undefined);
						}
					};
					break;
				case 'add_comment':
					var submit_btn = form_holder.querySelector('.submit_btn');
					submit_btn.onclick = function () {
						message_holder.innerHTML = '';
						var name = form_holder.querySelector('.name_field');
						var email = form_holder.querySelector('.email_field');
						var message = form_holder.querySelector('.message_field');
						var rate = form_holder.querySelector('.rate_field');

						var error_message = '';

						if (name.value.length < 2) {
							error_message += '<div>فیلد نام اجباری بوده و میبایست بیشتر از 1 کاراکتر باشد.</div>';
						}

						if (!validate_email(email.value)) {
							error_message += '<div>ایمیل درج شده معتبر نمیباشد.</div>';
						}

						if (message.value.length < 10) {
							error_message += '<div>متن نظر نمیتواند کمتر از 10 کاراکتر باشد.</div>';
						}

						if (parseInt(rate.value) == 0) {
							error_message += '<div>لطفا رتبه خود را درج نمایید.</div>';
						}

						if (error_message != '') {
							//show error message
							message_holder.innerHTML = '<div class="alert w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">×</span><div>' + error_message + '</div></div>';
						} else {
							//add loading
							add_class(form_holder, 'on_loading');

							//مقدار ID محصول را میگیریم
							var product_holder = closest_parent(form_holder, 'product_holder');
							var product_id = product_holder.getAttribute('product_id');

							//send form information to server
							var send_to_server = {
								"req": "save_form",
								"id": form_id,
								"first_name": name.value,
								"email": email.value,
								"message": message.value,
								"rate": rate.value
							};
							//var url = base_url + 'api/index';//'http://localhost/keshavarz/client/api/index'
							var url = base_url + 'forms/' + form_id + '/' + product_id;

							postAjax(url, send_to_server, function (result) {
								result = JSON.parse(result);
								remove_class(form_holder, 'on_loading');

								var message_type = result.error ? 'red' : 'green';

								//set message
								message_holder.innerHTML = '<div class="alert w3-pale-' + message_type + ' w3-round"><span class="closebtn" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">×</span><div>' + result.message + '</div></div>';

							}, undefined, function (result) {
								//something is wrong and we have error to have a connection with server
								remove_class(form_holder, 'on_loading');
								message_holder.innerHTML = '<div class="alert w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">×</span><div>بنظر ارتباط شما با سرور قطع شده است. لطفا مجدد تلاش نمایید. در صورتیکه چندمین مرتبه است این پیغام را مشاهده میکنید، با ارائه دهنده سرویس اینترنت خود تماس بگیرید.</div></div>';

							}, undefined);
						}
					};
					break;
				default:
					message_holder.innerHTML = '<div class="alert w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">×</span><div>اسکریپت برای فرم "' + form_id + '" یافت نشد!</div></div>';
					break;
			}
		}
    });
}

/***************************
 ** Product Slide Gallery **
 **************************/
function initialize_product_slide_gallery () {
    try {
        <!-- گالری تصاویر -->
        var galleryTop = new Swiper('.product-gallery-top', {
            spaceBetween: 10,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            loopedSlides: document.querySelectorAll(".product-gallery-top .swiper-wrapper .swiper-slide").length,
            loop: true,
        });
        var galleryThumbs = new Swiper('.product-gallery-thumbs', {
            spaceBetween: 10,
            centeredSlides: false,
            loopedSlides:  document.querySelectorAll(".product-gallery-thumbs .swiper-wrapper .swiper-slide").length,
            slidesPerView: 5,
            touchRatio: 0.2,
            slideToClickedSlide: true,
            loop: true,
        });
        galleryTop.controller.control = galleryThumbs;
        galleryThumbs.controller.control = galleryTop;
    }
    catch(err) {
        //console.log("We Can't found any product gallery in this page", err.message);
    }
}

/***************************
 ** Slides Configurations **
 **************************/
/** Large (Full width screen) slides for products **/
var large_products_slide = new Swiper('.l_slide', {
    slidesPerView: 5,
    spaceBetween: 0,
    slidesPerGroup: 5,
    loop: true,
    loopFillGroupWithBlank: true,
    /*pagination: {
     el: '.swiper-pagination',
     clickable: true,
     },*/
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    breakpoints: {
        1024: {
            slidesPerView: 5,
            slidesPerGroup: 5,
        },
        768: {
            slidesPerView: 3,
            slidesPerGroup: 3,
        },
        640: {
            slidesPerView: 2,
            slidesPerGroup: 2,
        },
        320: {
            slidesPerView: 1,
            slidesPerGroup: 1,
        }
    },
    on: {
        resize: function () {
            [].forEach.call(document.getElementsByClassName("limit_title"), function(value) {
                $clamp(value, {clamp: 2, useNativeClamp: true});
            });
        },
    },
});

/** Medium (Mid-width screen) slides for products **/
var medium_products_slide = new Swiper('.m_slide', {
    slidesPerView: 3,
    slidesPerGroup: 3,
    loop: true,
    loopFillGroupWithBlank: true,
    /*pagination: {
     el: '.swiper-pagination',
     clickable: true,
     },*/
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    breakpoints: {
        1024: {
            slidesPerView: 3,
            slidesPerGroup: 3,
        },
        768: {
            slidesPerView: 3,
            slidesPerGroup: 3,
        },
        640: {
            slidesPerView: 2,
            slidesPerGroup: 2,
        },
        320: {
            slidesPerView: 1,
            slidesPerGroup: 1,
        }
    },
});

/** Small (one-Item per slide) slides for products **/
var small_products_slide = new Swiper('.s_slide', {
    slidesPerView: 1,
    slidesPerGroup: 1,
    loop: true,
    loopFillGroupWithBlank: true,
    /*pagination: {
     el: '.swiper-pagination',
     clickable: true,
     },*/
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
});

/****************************************
 ** Our Suggestion Slide configuration **
 ***************************************/
var pishnahad_slide = new Swiper('.p_slide', {
    slidesPerView: 7,
    slidesPerGroup: 1,
    spaceBetween: 5,
    loop: true,
    loopFillGroupWithBlank: true,
    /*pagination: {
     el: '.swiper-pagination',
     clickable: true,
     },*/
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    breakpoints: {
        1024: {
            slidesPerView: 7,
            slidesPerGroup: 7,
        },
        768: {
            slidesPerView: 3,
            slidesPerGroup: 3,
        },
        640: {
            slidesPerView: 2,
            slidesPerGroup: 2,
        },
        320: {
            slidesPerView: 1,
            slidesPerGroup: 1,
        }
    },
});

/***********************
 ** Limit Items Title **
 **********************/
function limit_titles () {
    [].forEach.call(document.getElementsByClassName("limit_title"), function(value) {
        $clamp(value, {clamp: 2, useNativeClamp: false});
    });
}

/**********************
 ** Set Rating Stars **
 **********************/
function set_rate(rate_node, rating_number) {
    var father = closest_parent (rate_node, 'rating_stars');
    var rate_field = father.getElementsByClassName("rate_field")[0];
    rate_field.value = rating_number;

    var stars = father.getElementsByClassName("star");

    [].forEach.call(stars, function(star_node){
        remove_class(star_node, 'hover');
    });

    [].forEach.call(stars, function(star_node, star_index){
        if (star_index < rating_number)
        {
            add_class(star_node, 'hover');
        }
    });
}

function large_font(be_large) {
    var increase = 2;
    var module_html = closest_parent(be_large, 'module_html');
    //console.log(module_html);
    var content = module_html.getElementsByClassName('content')[0];
    //console.log(content);
    var style = content.style.fontSize;

    if (style == '')
    {
        //مقدار اولیه برای سایز فونت تعیین نشده است
        //مقدار پیشفرض را قرار میدهیم
        style = 14;
    }

    //مقدار عددی فونت مورد نظر را میگیریم
    style = parseInt(style);
    //سایز فونت را x واحد افزایش میدهیم.
    style = style + increase;
    content.style.fontSize = style+'px';
}

function small_font(be_small) {
    var increase = 2;
    var module_html = closest_parent(be_small, 'module_html');

    var content = module_html.getElementsByClassName('content')[0];

    var style = content.style.fontSize;

    if (style == '')
    {
        //مقدار اولیه برای سایز فونت تعیین نشده است
        //مقدار پیشفرض را قرار میدهیم
        style = 14;
    }

    //مقدار عددی فونت مورد نظر را میگیریم
    style = parseInt(style);

    //سایز فونت را x واحد کاهش میدهیم.
    style = style - increase;
    content.style.fontSize = style+'px';
}


/**
 * This function will show a timer countdown
 *
 * @param duration (integer): is the time that the timer is counting
 * @param display (HTML Node): the place that we will show timer result
 */
function startTimer(duration, display) {
    //اگر قبلا تایمری تنظیم شده است، آنرا پاک میکنیم تا شمارش اشتباه نشود!
    if (typeof timer_countdown != "undefined")
    {
        clearInterval(timer_countdown);
    }

    var timer = duration, minutes, seconds;
    timer_countdown = setInterval(function () {
        minutes = parseInt(timer / 60, 10)
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.innerHTML = "زمان باقیمانده تا انقضای کد:<br/>";
        display.innerHTML += minutes + ":" + seconds + " دقیقه!";
        display.innerHTML = "<div class='has_time w3-green'>"+ display.innerHTML +"</div>";

        if (--timer < 0) {
            //timer = duration;
            clearInterval(timer_countdown);

            display.innerHTML = "<div class='time_finished w3-pale-red'>زمان استفاده از کد به اتمام رسید.</div>";

            //Enable resend Buttom
            var resend_btn = document.querySelector("#login_register .resend");
            resend_btn.className = resend_btn.className.replace(/ w3-disabled/g, "");
        }
    }, 1000);
}

/********************
 ** Toggle Sidebar **
 ********************/
function toggle_sidebar(sidebar_id) {
	var sidebar_holder = document.getElementById(sidebar_id);
	var overlay_for_sidebars = document.getElementById('overlay_for_sidebars');

	if (sidebar_holder.className.split(' ').indexOf("opened") < 0)
	{
		//سایدبار بسته است. آنرا باز میکنیم

		//ابتدا به body کلاس overflow_hide را اضافه میکنیم تا اسکرولها تداخل پیدا نکنند
		add_class(document.body, 'overflow_hide');

		var win_w = document.body.clientWidth;
		console.log(win_w);
		var sidebar_w = sidebar_holder.offsetWidth;

		var sidebar_w_in_percent = (sidebar_w * 100) / win_w;

		sidebar_holder.style.left = (100 - sidebar_w_in_percent) + "%";
		add_class(sidebar_holder, 'opened');

		overlay_for_sidebars.style.left = '0%';

		overlay_for_sidebars.onclick = function() { toggle_sidebar(sidebar_id); };
	}
	else
	{
		remove_class(document.body, 'overflow_hide');

		sidebar_holder.style.left = "110%";
		remove_class(sidebar_holder, 'opened');

		overlay_for_sidebars.style.left = '110%';
	}
}

/*********************
 ** On Windows Load **
 *********************/
if(window.addEventListener){
    window.addEventListener('DOMContentLoaded', function () {
        active_module_forms();
        limit_titles();
        initialize_product_slide_gallery();
    })
}else{
    window.attachEvent('onload', function () {
        active_module_forms();
        limit_titles();
        initialize_product_slide_gallery();
    })
}
