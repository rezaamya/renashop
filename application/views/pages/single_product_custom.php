<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP include "blocks/headerrrrrrrrr.php"; ?>
<?PHP //include "blocks/main_menuuuuuuuuuuuu.php"; ?>

<div id="product_section">
    <div class="w3-row-padding">
        <div class="w3-col m3">
            <holder class="gallery">gallery</holder>
        </div>
        <div class="w3-col m9 info">
            <div class="w3-row header">
                <div class="w3-col rating_holder w3-left">
                    <holder class="rating_stars">rating_stars</holder>
                    <holder tag="span">rating_description</holder>
                </div>
                <div class="w3-rest title_holder">
                    <holder class="w3-row w3-xlarge">title</holder>
                    <div class="w3-row"><span>برند: </span><holder tag="span">brand</holder><span>دسته بندی: </span><holder tag="span">category</holder></div>
                </div>
            </div>
            <div class="w3-row-padding content">
                <div class="w3-col m9 description">
                    <div class="w3-row">
                        <div><b>توضیح سریع:</b></div>
                        <hr>
                        <holder>description</holder>
                    </div>
                    <div class="w3-row">
                        <hr>
                        <div>
                            <?PHP //if (has_value("discount_price")) { ?>
                            <div>قیمت: <holder tag="del">discount_price</holder><span class="w3-tiny"> تومان</span></div>
                            <?PHP //} ?>
                            <div>قیمت برای شما: <holder tag="span">price</holder><span class="w3-tiny"> تومان</span></div>
                            <div class="bottom_bar">
                                <holder class="number_incrementer">number_incrementer</holder>
                                <holder class="cart_btn">cart_btn</holder>
                                <holder class="wishlist_btn">wishlist_btn</holder>
                                <div class="stock">موجودی: <holder tag="span">stock</holder></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w3-col m3 w3-left">
                    <div class="w3-row">
                        <?PHP //if (has_value("video")) { ?>
						<div class="video_title">ویدیو معرفی محصول</div>

                        <holder class="w3-row video">
                            video
                        </holder>
						<?PHP //} ?>
                    </div>
                    <holder class="w3-row">special_characteristic</holder>
                </div>
            </div>
        </div>
    </div>
    <div class="w3-container">
        <div class="w3-row">
            <div class="w3-bar tab-title">
                <div class="w3-bar-item w3-buttonn tablink w3-right active" onclick="change_tabs(event,'info')">مشخصات</div>
                <div class="w3-bar-item w3-buttonn tablink w3-right" onclick="change_tabs(event,'list')">فهرست کتاب</div>
                <div class="w3-bar-item w3-buttonn tablink w3-right" onclick="change_tabs(event,'attachments')">پیوست های کتاب</div>
                <div class="w3-bar-item w3-buttonn tablink w3-right" onclick="change_tabs(event,'comment')">نظرات کاربران</div>
                <div class="w3-bar-item w3-buttonn tablink w3-right" onclick="change_tabs(event,'question')">پرسش و پاسخ</div>
            </div>

            <holder id="info" class="w3-container tab">position1</holder>
            <holder id="list" class="w3-container tab" style="display:none">position2</holder>
            <holder id="attachments" class="w3-container tab" style="display:none">position3</holder>
            <holder id="comment" class="w3-container tab" style="display:none">comment_form</holder>
            <holder id="question" class="w3-container tab" style="display:none">question_and_answer_form</holder>
        </div>
    </div>
</div>
<div id="product_section">
    <div class="w3-row-padding">
        <div class="w3-col m3">
            <div class="gallery">
                <div class="swiper-container product-gallery-top">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide" style="background-image:url(http://localhost/keshavarz/client/assets/images/1.jpg)"></div>
                        <div class="swiper-slide" style="background-image:url(http://localhost/keshavarz/client/assets/images/2.jpg)"></div>
                        <div class="swiper-slide" style="background-image:url(http://localhost/keshavarz/client/assets/images/3.jpg)"></div>
                        <div class="swiper-slide" style="background-image:url(http://localhost/keshavarz/client/assets/images/4.jpg)"></div>
                        <div class="swiper-slide" style="background-image:url(http://localhost/keshavarz/client/assets/images/5.jpg)"></div>
                        <div class="swiper-slide" style="background-image:url(http://localhost/keshavarz/client/assets/images/6.jpg)"></div>
                        <div class="swiper-slide" style="background-image:url(http://localhost/keshavarz/client/assets/images/7.jpg)"></div>
                    </div>
                    <!-- Add Arrows -->
                    <div class="swiper-button-next swiper-button-black"></div>
                    <div class="swiper-button-prev swiper-button-black"></div>
                </div>
                <div class="swiper-container product-gallery-thumbs">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide" style="background-image:url(http://localhost/keshavarz/client/assets/images/1.jpg)"></div>
                        <div class="swiper-slide" style="background-image:url(http://localhost/keshavarz/client/assets/images/2.jpg)"></div>
                        <div class="swiper-slide" style="background-image:url(http://localhost/keshavarz/client/assets/images/3.jpg)"></div>
                        <div class="swiper-slide" style="background-image:url(http://localhost/keshavarz/client/assets/images/4.jpg)"></div>
                        <div class="swiper-slide" style="background-image:url(http://localhost/keshavarz/client/assets/images/5.jpg)"></div>
                        <div class="swiper-slide" style="background-image:url(http://localhost/keshavarz/client/assets/images/6.jpg)"></div>
                        <div class="swiper-slide" style="background-image:url(http://localhost/keshavarz/client/assets/images/7.jpg)"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w3-col m9 info">
            <div class="w3-row header">
                <div class="w3-col rating_holder w3-left">
                    <div class="rating_stars">
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star"></span>
                    </div>
                    <span>از 25 رای</span>
                </div>
                <div class="w3-rest title_holder">
                    <div class="w3-row w3-xlarge">نام کتاب را اینجا میاریم که معمولا هم بسیار طولانیه</div>
                    <div class="w3-row"><span>برند: </span><a href="#">keshavarz</a><span>دسته بندی: </span><a href="#">book</a></div>
                </div>
            </div>
            <div class="w3-row-padding content">
                <div class="w3-col m9 description">
                    <div class="w3-row">
                        <div><b>توضیح سریع:</b></div>
                        <hr>
                        <div>توضیح سریع اینجا نوشته میشود این توضیح مختصر و تقریبا 3 الی 4 خط میباشد.  توضیح سریع اینجا نوشته میشود این توضیح مختصر و تقریبا 3 الی 4 خط میباشد.توضیح سریع اینجا نوشته میشود این توضیح مختصر و تقریبا 3 الی 4 خط میباشد.<br>توضیح سریع اینجا نوشته میشود این توضیح مختصر و تقریبا 3 الی 4 خط میباشد. توضیح سریع اینجا نوشته میشود این توضیح مختصر و تقریبا 3 الی 4 خط میباشد.</div>
                    </div>
                    <div class="w3-row">
                        <hr>
                        <div>
                            <div>قیمت: <del>16000</del><span class="w3-tiny"> تومان</span></div>
                            <div>قیمت برای شما: <span class="w3-">12000 <span class="w3-tiny">تومان</span></span></div>
                            <div class="bottom_bar">
                                <div class="number_incrementer">
                                    <div class="btns">
                                        <div class="add" onclick="refresh_incrementer(this, 'add');">+</div>
                                        <div class="minus" onclick="refresh_incrementer(this, 'minus');">-</div>
                                    </div>
                                    <div class="input_holder">
                                        <input class="w3-input" value="1" type="text">
                                    </div>
                                </div>
                                <button class="w3-button cart_btn"><span>اضافه به سبد خرید </span><i class="fas fa-shopping-cart"></i></button>
                                <button class="w3-button wishlist_btn"><i class="fas fa-heart"></i></button>
                                <span class="stock"><span>موجودی: </span><i class="fas fa-times w3-large"></i><span>  عدم موجودی</span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w3-col m3 w3-left">
                    <div class="w3-row">
                        <div class="video_title">ویدیو معرفی محصول</div>
                        <img class="video" src="http://localhost/keshavarz/client/assets/images/youtube.png">
                    </div>
                    <div class="w3-row">
                        <ul class="special_characteristic">
                            <li>
                                <i class="fas fa-check w3-tiny"></i>
                                <span><b>ویژگی:</b></span>
                                <span>خاص</span>
                            </li>
                            <li>
                                <i class="fas fa-check w3-tiny"></i>
                                <span><b>ویژگی:</b></span>
                                <span>خیلی خاص</span>
                            </li>
                            <li>
                                <i class="fas fa-check w3-tiny"></i>
                                <span><b>ویژگی:</b></span>
                                <span>خیلی بیشتر خاص</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="w3-container">
        <div class="w3-row">
            <div class="w3-bar tab-title">
                <div class="w3-bar-item w3-buttonn tablink w3-right active" onclick="change_tabs(event,'info2')">مشخصات</div>
                <div class="w3-bar-item w3-buttonn tablink w3-right" onclick="change_tabs(event,'list2')">فهرست کتاب</div>
                <div class="w3-bar-item w3-buttonn tablink w3-right" onclick="change_tabs(event,'attachments2')">پیوست های کتاب</div>
                <div class="w3-bar-item w3-buttonn tablink w3-right" onclick="change_tabs(event,'comment2')">نظرات کاربران</div>
                <div class="w3-bar-item w3-buttonn tablink w3-right" onclick="change_tabs(event,'question2')">پرسش و پاسخ</div>

            </div>

            <div id="info2" class="w3-container tab">
                <div><span><b>نام کتاب:</b></span> نام کتاب را اینجا میاریم که معمولا هم بسیار طولانیه</div>
                <div><span><b>نام نویسنده:</b></span> بهمن کشاورز</div>
                <div><span><b>نام انتشارات:</b></span> انتشارات کشاورز</div>
                <div><span><b>موضوع:</b></span> حقوق</div>
                <div><span><b>سال انتشار:</b></span> 1396</div>
                <div><span><b> تعداد چاپ::</b></span> 1000</div>
                <div><span><b>قطع:</b></span> رقعی</div>
            </div>
            <div id="list2" class="w3-container tab" style="display:none">
                <div><span><b>فصل اول:</b></span> توضیح سریع</div>
                <div><span><b>فصل دوم:</b></span> توضیح سریع </div>
                <div><span><b>فصل سوم:</b></span> توضیح سریع</div>
                <div><span><b>فصل چهارم:</b></span>توضیح سریع </div>
            </div>
            <div id="attachments2" class="w3-container tab" style="display:none">
                <div><a href="#"><i class="far fa-file-pdf w3-xlarge"></i><span><b>  فایل فهرست</b></span></a></div>
                <div><a href="#"><i class="far fa-file-pdf w3-xlarge"></i><span><b>  فایل فصل اول کتاب</b></span></a></div>
                <div><a href="#"><i class="far fa-file-audio w3-xlarge"></i><span><b>  فایل صوتی کتاب</b></span></a></div>
            </div>



            <div id="comment2" class="w3-container tab" style="display:none">
                <form method="post" class="w3-container comment_form">
                    <h2><?=lang('insert_comment')?></h2>
                    <p>شما میتوانید ازطریق فرم زیر نظرات خود را برای ما ارسال نمایید.</p>


                    <div class="w3-row w3-margin-bottom">
                        <label class="w3-col s2"><?=lang('first_name')?></label>
                        <input class="w3-col s10 w3-input w3-border w3-round" name="first_name" type="text">
                    </div>
                    <div class="w3-row w3-margin-bottom">
                        <label class="w3-col s2"><?=lang('email')?></label>
                        <input class="w3-col s10 w3-input w3-border w3-round" name="email" type="email">
                    </div>
                    <div class="w3-row w3-margin-bottom">
                        <label class="w3-col s2"><?=lang('message')?></label>
                        <textarea class="w3-col s10 w3-input w3-border w3-round" name="message" rows="5"></textarea>
                    </div>
                    <div class="w3-row w3-margin-bottom">
                        <!--offset-->
                        <label class="w3-col s2">&nbsp</label>
                        <div class="w3-col s10 rating_stars">
                            <a href="#"><span class="fa fa-star"></span></a>
                            <a href="#"><span class="fa fa-star"></span></a>
                            <a href="#"><span class="fa fa-star"></span></a>
                            <a href="#"><span class="fa fa-star"></span></a>
                            <a href="#"><span class="fa fa-star"></span></a>
                        </div>
                    </div>
                    <div class="w3-row w3-margin-bottom">
                        <!--offset-->
                        <label class="w3-col s1">&nbsp</label>
                        <button class="w3-col s11 w3-button w3-block w3-blue w3-ripple w3-padding"><?=lang('send')?></button>
                    </div>
                </form>
            </div>
            <div id="question2" class="w3-container tab" style="display:none">
                <form method="post" class="w3-container question_form">
                    <h2><?=lang('insert_question')?></h2>
                    <p>شما میتوانید از طریق فرم زیر سوالات خود را با ما در میان بگذارید.</p>


                    <div class="w3-row w3-margin-bottom">
                        <label class="w3-col s1"><?=lang('first_name')?></label>
                        <input class="w3-col s11 w3-input w3-border w3-round" name="first_name" type="text">
                    </div>
                    <div class="w3-row w3-margin-bottom">
                        <label class="w3-col s1"><?=lang('email')?></label>
                        <input class="w3-col s11 w3-input w3-border w3-round" name="email" type="email">
                    </div>
                    <div class="w3-row w3-margin-bottom">
                        <label class="w3-col s1"><?=lang('question')?></label>
                        <textarea class="w3-col s11 w3-input w3-border w3-round" name="message" rows="5"></textarea>
                    </div>
                    <div class="w3-row w3-margin-bottom">
                        <!--offset-->
                        <label class="w3-col s1">&nbsp</label>
                        <button class="w3-col s11 w3-button w3-block w3-blue w3-ripple w3-padding"><?=lang('send')?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?PHP //include "blocks/pardahkt_amn.php"; ?>
<?PHP //include "blocks/contact_us.php"; ?>
<?PHP //include "blocks/footer.php"; ?>
