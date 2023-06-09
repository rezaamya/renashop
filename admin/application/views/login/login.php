<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container">
    <div class="vh100 row align-items-center justify-content-center">
        <div class="col-11 col-md-5 content">
            <p>جهت دسترسی به مدیریت، نام کاربری و رمز عبور را وارد نمایید.</p>
            <?PHP echo $sys_msg; ?>
            <?PHP
            $attributes = array('class' => 'main_form', 'id' => 'main_form');
            echo form_open_multipart(base_url("login"), $attributes);
            ?>
                <div class="form-group">
                    <label for="username">نام کاربری</label>
                    <input class="form-control" id="username" name="username" placeholder="نام کاربری خود را وارد نمایید.">
                </div>
                <div class="form-group">
                    <label for="password">رمز عبور</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="رمز عبور خود را وارد نمایید.">
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember_me">
                    <label name="remember_me" class="form-check-label" for="remember_me">مرا به خاطر بسپار.</label>
                </div></br>
                <input type="submit" class="btn btn-primary" value="ورود" />
            </form>
        </div>
    </div>
</div>
