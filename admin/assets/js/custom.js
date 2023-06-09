/**
 * Created by Office on 3/11/2018.
 */

/*********************************************
** Enable all PopOvers in whole of the page **
*********************************************/
$(function () {
    $('[data-toggle="popover"]').popover()
});

/*****************************
 ** Enable all Text-Editors **
 ****************************/
try {
	CKEDITOR.replace( 'ckeditor' );
}
catch(err) {
	//console.log('there is no ckeditor');
}


/**************************************
 ** Toggle Checklist Select/Deselect **
 *************************************/
function toggle_checklist(clicked_by)
{
    if($(clicked_by).prop('checked') == true)
    {
        $('input:checkbox').prop('checked', true);
    }
    else
    {
        $('input:checkbox').prop('checked', false);
    }
}

/**************************************************
 ** Toggle Option list Checklist Select/Deselect **
 **************************************************/
function toggle_options_list(clicked_by)
{
    if($(clicked_by).prop('checked') == true)
    {
        $('input[name="list_options[]"]').prop('checked', true).change();
    }
    else
    {
        $('input[name="list_options[]"]').prop('checked', false).change();
    }
}

/*********************************************
 ** Toggle Sub Options list header Checkbox **
 *********************************************/
function toggle_all_sub_options_list (clicked_by)
{
    if($(clicked_by).prop('checked') == true)
    {
        $('input.toggle_sub_options_list').prop('checked', true).change();
    }
    else
    {
        $('input.toggle_sub_options_list').prop('checked', false).change();
    }
}

/**************************************************
 ** Toggle Sub Options Checklist Select/Deselect **
 **************************************************/
function toggle_sub_options_list (clicked_by)
{
    var checklist_father_node = $(clicked_by.closest('.influence_on_product'));
    if($(clicked_by).prop('checked') == true)
    {
        checklist_father_node.addClass('enabled').removeClass('disabled');
        checklist_father_node.find('input.sub_list_options').prop('checked', true).change();
    }
    else
    {
        checklist_father_node.addClass('disabled').removeClass('enabled');
        checklist_father_node.find('input.sub_list_options').prop('checked', false).change();
    }
}

/*************************
 ** Enable Toolbar BTNs **
 ************************/
function set_task (task, form_id, checklist, attributes)
{
    var is_any_checked = false;

    if (checklist != '')
    {
        $.each($('input:checkbox'), function (i, val) {
            if ($(val).prop('checked'))
            {
                is_any_checked = true;
            }
        });
    }
    else
    {
        //we don't need checklist, we just want to submit the form
        is_any_checked = true;
    }

    if (is_any_checked)
    {
        var get_okay = true;

        //set task value
        if (task == 'delete')
        {
            get_okay = confirm("مطمئنید میخواهید موارد انتخاب شده را حذف کنید؟");
        }

        if (get_okay)
        {
            $('#task').val(task);
            //in modals (frame_modal or normal_modal), user should change something else
            //then he can submit after confirm the modal accept button
            if (checklist != 'normal_modal')
            {
                //submit the form
                $('#'+form_id).submit();
            }
            else
            {
                //show modal
                $('#'+attributes['modal_id']).modal('show');
            }
        }
    }
    else
    {
        alert('هیچ آیتمی جهت انجام عملیات انتخاب نشده است.');
    }
}

/**************************
 ** escape Special Chars **
 * ***********************/
 //Some Characters like \n \r \t etc. can't be loaded uisng JSON.parse()
 //then we need to Escape these special characters **
function escapeSpecialChars(jsonString) {
    return jsonString.replace(/\\/g, "\\\\")
        .replace(/\n/g, "\\n")
        .replace(/\r/g, "\\r")
        .replace(/\t/g, "\\t")
        .replace(/\f/g, "\\f");
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

/***********************
 ** Copy to Clipboard **
 **********************/
function copy_to_clipboard(str) {
    const el = document.createElement('textarea');  // Create a <textarea> element
    el.value = str;                                 // Set its value to the string that you want copied
    el.setAttribute('readonly', '');                // Make it readonly to be tamper-proof
    el.style.position = 'absolute';
    el.style.left = '-9999px';                      // Move outside the screen to make it invisible
    document.body.appendChild(el);                  // Append the <textarea> element to the HTML document
    const selected =
        document.getSelection().rangeCount > 0        // Check if there is any content selected previously
            ? document.getSelection().getRangeAt(0)     // Store selection if found
            : false;                                    // Mark as false to know no selection existed before
    el.select();                                    // Select the <textarea> content
    document.execCommand('copy');                   // Copy - only works as a result of a user action (e.g. click events)
    document.body.removeChild(el);                  // Remove the <textarea> element
    if (selected) {                                 // If a selection existed before copying
        document.getSelection().removeAllRanges();    // Unselect everything on the HTML document
        document.getSelection().addRange(selected);   // Restore the original selection
    }
}


/*******************
 ** Delete Parent **
*******************/
function delete_parent(nodi_ke_klick_shode, nod_class) {
    var nod = closest_parent (nodi_ke_klick_shode, nod_class);
    nod.parentNode.removeChild(nod);
}

/*****************
 ** Upload File **
 ****************/
function upload_file(upload_btn, target)
{
    //برخي از فيلدها از نوع upload هستند. در اين فيلدها کاربر ميتواند يک فايل را از سيستم خود انتخاب
    //سپس آپلود کند. اين عمليات بصورت ajax انجام ميشود تا صفحه رفرش نشده و فرم سفارش کاربر بطور ثابت باقي بماند
    //فايلها بصورت موقت آپلود ميشوند و name فايل آپلود شده به سرور فرستاده خواهد شد
    //درصورتيکه کاربر به هر دليلي بخواهد فايل ديگري را جايگزين فايل آپلود شده قبلي کند
    //فايل آپلود شده قبلي از سرور حذف شده و فايل جديد جايگزين ميشود
    //سيستم بايد طوري طراحي شود که
    //در صورتيکه کاربر سفارش خود را ثبت نکند، فايلهاي آپلود شده قبلي که بدون استفاده هستند، حذف گردند
    //var elt = element.closest(selectors);
    var father = closest_parent(upload_btn, 'upload_module_holder');
    var upload_field = father.querySelector('input[type="file"]');
    var copy_to_clipboard_btn = father.querySelector('.copy_to_clipboard');
    if (! upload_field.disabled)
    {
        //چک ميکنيم که فيلد آپلود غيرفعال نباشد
        var selected_file = upload_field.files;
        if (selected_file.length === 0)
        {
            //فايلي انتخاب نشده است
            alert('لطفا ابتدا فايل مورد نظر را انتخاب نماييد');
        }
        else
        {
            //فايل انتخاب شده است، اقدام به آپلود فايل ميکنيم
            //ابتدا کلاس در حال آپلود را به نود والد اضافه ميکنيم
            add_class(father, 'on_loading');
            var url = base_url + 'upload';
            var formData = new FormData();
            formData.append('file', selected_file[0]);
            formData.append('req', 'upload');
            formData.append('target', target);

            //پيغامهايي که تا اين لحظه نمايش داده شده اند را پاک ميکنيم
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
                    console.log(xhr.responseText);
                    var result = JSON.parse(xhr.responseText);
                    if (result.status == 'successful')
                    {
                        //فايل با موفقيت آپلود شده است
                        add_class(father, 'uploaded');

                        uploaded_module_message_holder.innerHTML = 'فايل «'+upload_field.files[0].name+'» با موفقيت ذخيره شد.';

                        var hidden_input = father.querySelector('input.uploaded_file');
                        hidden_input.setAttribute("name", 'files[]');
                        hidden_input.setAttribute("file_name", result.file_name);
                        hidden_input.value = result.insert_id;

                        copy_to_clipboard_btn.onclick = function() {
                            copy_to_clipboard(result.directory);
                            copy_to_clipboard_btn.innerHTML = 'آدرس فایل در حافظه کپی شد.';
                            setTimeout(function(){ copy_to_clipboard_btn.innerHTML = 'کپی آدرس فایل در حافظه'; }, 3000);
                        };
                    }
                    else
                    {
                        //فايل آپلود نشده است
                        upload_module_message_holder.innerHTML = "<div class='alert alert-warning alert-dismissible fade show'>"+
                            'فايل «'+upload_field.files[0].name+'» آپلود نشد. لطفا در صورتيکه چندمين مرتبه است اين خطا را مشاهده ميکنيد. با مديريت تماس بگيريد..'
                            +"</div>";
                    }

                }
                else if (xhr.readyState == 4 && xhr.status!= 200) {
                    //failure
                    console.log('failure',xhr.responseText);
                    remove_class(father, 'on_loading');

                    upload_module_message_holder.innerHTML = "<div class='alert alert-warning alert-dismissible fade show'>"+
                        'در ارتباط با سرور خطايي رخ داده است و فايل آپلود نشد. لطفا در صورتيکه چندمين مرتبه است اين خطا را مشاهده ميکنيد. با مديريت تماس بگيريد..'
                        +"</div>";
                }
            };
            //xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.send(formData);
        }
    }
}

/************************
 ** Remove upload File **
 ***********************/
function remove_uploaded_file(delete_btn, target)
{
    //کاربر ميخواهد فايلي را که قبلا آپلود کرده بوده است را حذف کند
    var father = closest_parent(delete_btn, 'upload_module_holder');
    var hidden_input = father.querySelector('input.uploaded_file');
    var file_id = hidden_input.value;
    var file_name = hidden_input.getAttribute("file_name");

    var uploaded_module_message_holder = father.querySelector('.uploaded_module .message_holder');
    uploaded_module_message_holder.innerHTML = '';

    if (file_id != "")
    {
        add_class(father, 'on_loading');

        var send_to_server = {"req":"delete", "file_id":file_id, "file_name":file_name, "target": target};
        var url = base_url + 'upload';

        $.ajax({
            method: "POST",
            url: url,
            data: send_to_server,
            success: function(result){
                console.log(result);
                result = JSON.parse(result);
                remove_class(father, 'on_loading');

                if (result.status == 'successful')
                {
                    //فايل با موفقيت حذف شد
                    hidden_input.value = '';
                    hidden_input.removeAttribute("name");
                    hidden_input.removeAttribute("file_name");
                    remove_class(father, 'uploaded');
                }
                else
                {
                    //فايل حذف نشده است
                    uploaded_module_message_holder.innerHTML = "<div class='alert alert-warning alert-dismissible fade show'>"+
                        'فايل حذف نشد. در صورتيکه چندمين مرتبه است اين خطا را مشاهده ميکنيد، لطفا با مديريت تماس حاصل نماييد.'
                        +"</div>";
                }
            },
            error: function(result){
                console.log(result);
                remove_class(father, 'on_loading');
                uploaded_module_message_holder.innerHTML = "<div class='alert alert-warning alert-dismissible fade show'>"+
                    'ارتباط با سرور قطع شده است. در صورتيکه چندمين مرتبه است اين خطا را مشاهده ميکنيد، لطفا با مديريت تماس حاصل نماييد.'
                    +"</div>";
            }
        });
    }
}
