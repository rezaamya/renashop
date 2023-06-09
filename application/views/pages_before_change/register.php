<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/header.php"; ?>
<?PHP include "blocks/main_menu.php"; ?>

<div id="login_register" class="w3-row">
    <div class="w3-col s1 m3 l4">&nbsp;</div>
    <!--<div class="w3-col m4 login">
        <form action="<?=base_url('login')?>" method="post">
            <header class="w3-container">
                <h3><?=lang('store_customers')?></h3>
            </header>
            <div class="w3-container">
                <p><?=lang('i_am_a_store_customer')?></p>
                <hr>
                <hr>
                <?PHP echo $sys_msg; ?>
                <p>
                    <label><?=lang('username')?></label>
                    <input class="w3-input w3-border" name="username" type="text">
                </p>
                <p>
                    <label><?=lang('password')?></label>
                    <input class="w3-input w3-border" name="password" type="password">
                </p>
                <div>
                    <input id="remember" type="checkbox" name="remember">
                    <label for="remember"><?=lang('remember_me')?></label>
                </div>
                <div class="forgot_password">
                    <a href="#"><?=lang('forgot_password')?></a>
                </div>
            </div>
            <button type="submit" class="w3-button w3-block w3-dark-grey"><?=lang('login')?></button>
        </form>
    </div>-->
    <div class="w3-col s10 m6 l4 register">
        <div class="step_1">
            <div class="loading w3-hide">
                <div class="loader_spin"></div>
                <?=lang('please_wait');?>
            </div>
            <header class="w3-container">
                <h3><?=lang('new_customer')?></h3>
            </header>
            <div class="w3-container">
                <p><?=lang('create_an_account')?></p>
                <hr>
                <p class="content">با ايجاد يک حساب کاربری می توانید سريع تر خريد نموده، تاريخچه سفارش های خود را مشاهده کرده و همچنين از وضعيت سفارش خود آگاهی پيدا کرده و آن را پیگیری نمایید.</p>
                <div class="messages_holder">

                </div>
                <div>
                    <p>
                        <label><?=lang('enter_your_email_or_mobile_number_to_receive_the_login_code')?></label>
                        <input class="w3-input w3-border" name="email_or_mobile" type="text">
                    </p>
                    <p>
                        <label><?=lang('password')?></label>
                        <input class="w3-input w3-border" name="password" type="password">
                    </p>
                </div>
            </div>
            <button class="w3-button w3-block w3-dark-grey" onclick="register('step_1')"><?=lang('get_the_code')?></button>
        </div>
        <div class="step_2 w3-hide">
            <div class="loading w3-hide">
                <div class="loader_spin"></div>
                <?=lang('please_wait');?>
            </div>
            <header class="w3-container">
                <h3><?=lang('new_customer')?></h3>
            </header>

            <div class="w3-container">
                <div class="temp_messages_holder">
                </div>
                <div class="messages_holder">
                </div>

                <div class="timer"></div>

                <p class="w3-margin-top">
                    <label>کد تایید را وارد نمایید</label>
                    <input class="w3-input w3-border w3-round" name="register_code" type="text">
                </p>

                <div class="w3-center">
                    <button class="w3-button w3-round w3-margin resend w3-disabled" onclick="register('resend_code');">+ ارسال مجدد کد</button>
                    <button class="w3-button w3-round w3-margin edit" onclick="register('goto_step_1');">ویرایش شماره تماس</button>
                </div>
            </div>
            <button class="w3-button w3-block w3-dark-grey" onclick="register('submit_code')"><?=lang('register')?></button>
        </div>
        <div class="step_3 w3-hide">
            <header class="w3-container">
                <h3>خوش آمدید!</h3>
            </header>
            <div class="w3-container">
                <p>حساب کاربری شما با موفقیت ساخته شد.</p>
                <br><br><br>
                <div class="w3-center">
                    <a href="<?=base_url('profile')?>" class="w3-button w3-round w3-margin w3-green">تکمیل مشخصات کاربری</a>
                    <a href="<?=base_url()?>" class="w3-button w3-round w3-margin w3-red">ورود به فروشگاه</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function register (step)
    {
        var show_error = '';
        var messages_holder = document.querySelector("#login_register ."+step+" .messages_holder");
        var current_step = document.querySelector("#login_register .step_1");

        if (step == "step_1")
        {
            messages_holder.innerHTML = "";

            var email_or_mobile_input = document.querySelector("#login_register .step_1 input[name='email_or_mobile']");
            if (email_or_mobile_input.value.length < 7)
            {
                //email or mobile is invalid
                show_error += "<div>ایمیل یا شماره همراه به درستی درج نشده است.</div>";
            }

            var password_input = document.querySelector("#login_register .step_1 input[name='password']");
            if (password_input.value.length < 4)
            {
                //Password is invalid
                show_error += "<div>رمز عبور نمیتواند کمتر از چهار کاراکتر باشد.</div>";
            }

            if (show_error != '')
            {
                var msg = '<div class="alert  w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>'+show_error+'</div>';
                messages_holder.insertAdjacentHTML('afterbegin', msg);
            }
            else
            {
                //show loading
                var loading = document.querySelector("#login_register .step_1 .loading");
                loading.className = loading.className.replace(/w3-hide/g, "");
                loading.className = loading.className.replace(/  /g, " ");

                //Send request to server
                var send_to_server = {"req": "get_register_code","username":email_or_mobile_input.value, "password":password_input.value};
                var url = '<?=base_url('api/index')?>';
                postAjax(
                    url,
                    send_to_server,
                    function (result) {
                        //Success state
                        var messages_holder = document.querySelector("#login_register .step_1 .messages_holder");
                        console.log("success:", result);
                        result = JSON.parse(result);

                        if (result.status == 'invalid')
                        {
                            //input is invalid
                            var msg = '<div class="alert  w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>'+result.error_message+'</div>';
                            messages_holder.innerHTML = msg;

                            loading.className += " w3-hide";
                        }
                        else if (result.status == 'registered_user')
                        {
                            //this user is registered before
                            //we should show an error
                            var msg = '<div class="alert  w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>مشخصات وارد شده، قبلا در سیستم ثبت شده است.</div>';
                            messages_holder.innerHTML = msg;

                            loading.className += " w3-hide";
                        }
                        else
                        {
                            //Code sent to new user
                            //goto next step
                            loading.className += " w3-hide";
                            current_step.className += ' w3-hide';
                            var next_step = document.querySelector("#login_register .step_2");
                            next_step.className = next_step.className.replace(/w3-hide/g, "");
                            next_step.className = next_step.className.replace(/  /g, " ");

                            //set message
                            var temp_messages_holder = document.querySelector("#login_register .step_2 .temp_messages_holder");
                            var msg = '<div class="alert w3-pale-green w3-round">کد تایید به  <b>'+email_or_mobile_input.value+'</b> ارسال شد.</div>';
                            temp_messages_holder.innerHTML = msg;

                            var messages_holder = document.querySelector("#login_register .step_2 .messages_holder");
                            messages_holder.innerHTML = "";

                            var password_input = document.querySelector("#login_register .step_2 input[name='register_code']");
                            password_input.value = "";

                            //set Timer countdown
                            //start_countdown(result.expire_in, document.querySelector("#login_register .step_2 .timer"))
                            startTimer(parseInt(result.expire_in), document.querySelector("#login_register .step_2 .timer"));
                        }
                    },
                    undefined,
                    function (result) {
                        //Failure state
                        //console.log("falied:", result);
                        var msg = '<div class="alert  w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>در ارتباط با سرور، خطایی رخ داده است. لطفا مجدد تلاش نمایید و در صورتیکه این خطا به مدت طولانی تکرار شد، لطفا آنرا به مدیر سایت اطلاع دهید.</div>';
                        messages_holder.innerHTML = msg;
                        loading.className += " w3-hide";
                    }
                );
            }
        }
        else if (step == "step_2")
        {
            messages_holder.innerHTML = "";

            var register_code = document.querySelector("#login_register .step_2 input[name='register_code']");
            if (register_code.value.length < 5)
            {
                //register code is not valid
                show_error += "<div>کد تائید صحیح نمیباشد.</div>";
            }

            if (show_error != '')
            {
                var msg = '<div class="alert  w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>'+show_error+'</div>';
                messages_holder.insertAdjacentHTML('afterbegin', msg);
            }
            else
            {
                //show loading
                var loading = document.querySelector("#login_register .step_1 .loading");
                loading.className = loading.className.replace(/w3-hide/g, "");
                loading.className = loading.className.replace(/  /g, " ");

                //Send request to server
                var send_to_server = {"req": "get_register_code","username":email_or_mobile_input.value, "password":password_input.value};
                var url = '<?=base_url('api/index')?>';
                postAjax(
                    url,
                    send_to_server,
                    function (result) {
                        //Success state
                        console.log("success:", result);
                        result = JSON.parse(result);

                        if (result.status == 'registered_user')
                        {
                            //this user is registered before
                            //we should show an error
                            var msg = '<div class="alert  w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>مشخصات وارد شده، قبلا در سیستم ثبت شده است.</div>';
                            messages_holder.insertAdjacentHTML('afterbegin', msg);

                            loading.className += " w3-hide";
                        }
                        else
                        {
                            //goto next step
                            loading.className += " w3-hide";
                            current_step.className += ' w3-hide';
                            var next_step = document.querySelector("#login_register .step_2");
                            next_step.className = next_step.className.replace(/w3-hide/g, "");
                            next_step.className = next_step.className.replace(/  /g, " ");

                            //set message
                            var messages_holder = document.querySelector("#login_register .step_2 .messages_holder");
                            var msg = '<div class="alert w3-pale-green w3-round">کد تایید به  <b>'+email_or_mobile_input.value+'</b> ارسال شد.</div>';
                            messages_holder.insertAdjacentHTML('afterbegin', msg);
                        }
                    },
                    undefined,
                    function (result) {
                        //Failure state
                        //console.log("falied:", result);
                        var msg = '<div class="alert  w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>در ارتباط با سرور، خطایی رخ داده است. لطفا مجدد تلاش نمایید و در صورتیکه این خطا به مدت طولانی تکرار شد، لطفا آنرا به مدیر سایت اطلاع دهید.</div>';
                        messages_holder.insertAdjacentHTML('afterbegin', msg);
                        loading.className += " w3-hide";
                    }
                );
            }
        }
        else if (step == "goto_step_1")
        {
            //hide step_2
            var current_step = document.querySelector("#login_register .step_2");
            current_step.className += " w3-hide";

            //show step_1
            var step_1 = document.querySelector("#login_register .step_1");
            step_1.className += " w3-hide";
            step_1.className = step_1.className.replace(/w3-hide/g, "");
            step_1.className = step_1.className.replace(/  /g, " ");
        }
        else if (step == "resend_code")
        {
            //check to see if resend is inactive
            var resend_btn = document.querySelector("#login_register .resend");
            if (resend_btn.className.indexOf("w3-disabled") < 0)
            {
                //resend btn is active
                //inactive resend Button
                resend_btn.className += ' w3-disabled';

                //show loading
                var loading = document.querySelector("#login_register .step_2 .loading");
                loading.className = loading.className.replace(/w3-hide/g, "");
                loading.className = loading.className.replace(/  /g, " ");

                //Send request to server
                var email_or_mobile_input = document.querySelector("#login_register .step_1 input[name='email_or_mobile']");
                var send_to_server = {"req": "resend_register_code","username":email_or_mobile_input.value};
                var url = '<?=base_url('api/index')?>';
                postAjax(
                    url,
                    send_to_server,
                    function (result) {
                        //Success state
                        var messages_holder = document.querySelector("#login_register .step_2 .messages_holder");
                        console.log("success:", result);
                        result = JSON.parse(result);

                        if (result.status == 'sent')
                        {
                            //Message has been send
                            var msg = '<div class="alert  w3-pale-yellow w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>کد مجددا ارسال شد.</div>';
                            messages_holder.innerHTML = msg;

                            startTimer(parseInt(result.expire_in), document.querySelector("#login_register .step_2 .timer"));
                        }
                        else if (result.status == 'please_wait')
                        {
                            //کاربر میبایست صبر کند
                            var msg = '<div class="alert  w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>تا دریافت کد بعدی میبایست حداقل '+result.how_much+' صبر کنید</div>';
                            messages_holder.innerHTML = msg;

                            //disable resend btn
                            //var resend_btn = document.querySelector("#login_register .resend");
                            //resend_btn.className += 'w3-disabled';
                        }
                        else
                        {
                            //خطایی در ارسال کد رخ داده است
                            var msg = '<div class="alert  w3-pale-yellow w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>هنگام ارسال مجدد کد، خطایی رخ داده است. لطفا مجدد تلاش نمایید و در صورتیکه چندمین مرتبه است که با این خطا مواجه میشوید، لطفا آنرا به مدیر سایت اطلاع دهید.</div>';
                            messages_holder.innerHTML = msg;
                        }

                        loading.className += " w3-hide";
                    },
                    undefined,
                    function (result) {
                        //Failure state
                        var messages_holder = document.querySelector("#login_register .step_2 .messages_holder");
                        //console.log("falied:", result);
                        var msg = '<div class="alert  w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>در ارتباط با سرور، خطایی رخ داده است. لطفا مجدد تلاش نمایید و در صورتیکه این خطا به مدت طولانی تکرار شد، لطفا آنرا به مدیر سایت اطلاع دهید.</div>';
                        messages_holder.innerHTML = msg;
                        loading.className += " w3-hide";
                    }
                );
            }
        }
        else if (step == "submit_code")
        {
            var messages_holder = document.querySelector("#login_register .step_2 .messages_holder");
            messages_holder.innerHTML = "";

            var register_code = document.querySelector("#login_register .step_2 input[name='register_code']");
            if (register_code.value.length < 5)
            {
                //register code is not valid
                show_error += "<div>کد تائید صحیح نمیباشد.</div>";
            }

            if (show_error != '')
            {
                var msg = '<div class="alert  w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>'+show_error+'</div>';
                messages_holder.insertAdjacentHTML('afterbegin', msg);
            }
            else
            {
                //show loading
                var loading = document.querySelector("#login_register .step_2 .loading");
                loading.className = loading.className.replace(/w3-hide/g, "");
                loading.className = loading.className.replace(/  /g, " ");

                //Send request to server
                var send_to_server = {"req": "submit_register_code","code":register_code.value};
                var url = '<?=base_url('api/index')?>';
                postAjax(
                    url,
                    send_to_server,
                    function (result) {
                        //Success state
                        console.log("success:", result);
                        result = JSON.parse(result);

                        if (result.status == 'correct')
                        {
                            var current_step = document.querySelector("#login_register .step_2");
                            current_step.className += ' w3-hide';

                            var next_step = document.querySelector("#login_register .step_3");
                            next_step.className = next_step.className.replace(/w3-hide/g, "");
                            next_step.className = next_step.className.replace(/  /g, " ");
                        }
                        else if (result.status == 'incorrect')
                        {
                            //Register code is not correct
                            var msg = '<div class="alert  w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>کد وارد شده صحیح نبوده یا زمان آن منقضی شده است.</div>';
                            messages_holder.innerHTML = msg;
                        }

                        loading.className += " w3-hide";
                    },
                    undefined,
                    function (result) {
                        //Failure state
                        var messages_holder = document.querySelector("#login_register .step_2 .messages_holder");
                        //console.log("falied:", result);
                        var msg = '<div class="alert  w3-pale-red w3-round"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>در ارتباط با سرور، خطایی رخ داده است. لطفا مجدد تلاش نمایید و در صورتیکه این خطا به مدت طولانی تکرار شد، لطفا آنرا به مدیر سایت اطلاع دهید.</div>';
                        messages_holder.insertAdjacentHTML('afterbegin', msg);
                        loading.className += " w3-hide";

                    }
                );
            }
        }
    }

</script>
<?PHP include "blocks/pardahkt_amn.php"; ?>
<?PHP include "blocks/contact_us.php"; ?>
<?PHP include "blocks/footer.php"; ?>





