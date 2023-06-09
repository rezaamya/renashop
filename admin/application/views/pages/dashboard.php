<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
    <div class="row">
        <div class="col">با سلام و احترام<br/>
            به بخش مدیریت خوش امدید.
            <br/><br/>
        </div>
    </div>
    <?= $sys_msg ?>
    <div class="row">
        <div class="box col-lg-3 col-md-4 col-sm-6">
            <div class="box_title">
                <?=lang('new_order')?>
            </div>
            <div class="box_content">
                <a href="<?= base_url("/order")?>">
                    <i class="fa fa-shopping-cart"></i>
                    <h1><?php echo $new_order_count; ?></h1>
                </a>
            </div>
        </div>

        <div class="box col-lg-3 col-md-4 col-sm-6">
            <div class="box_title">
                <?=lang('customers_quantity')?>
            </div>
            <div class="box_content">
                <a href="<?= base_url("/customer")?>">
                    <i class="far fa-user"></i>
                    <h1><?php echo $customer_count; ?></h1>
                </a>
            </div>
        </div>

        <div class="box col-lg-3 col-md-4 col-sm-6">
            <div class="box_title">
                <?=lang('cancel_orders')?>
            </div>
            <div class="box_content">
                <a href="<?= base_url("/order")?>">
                    <i class="fas fa-ban"></i>
                    <h1><?php echo $cancel_orders_count; ?></h1>
                </a>
            </div>
        </div>

        <div class="box col-lg-3 col-md-4 col-sm-6">
            <div class="box_title">
                <?=lang('comments')?>
            </div>
            <div class="box_content">
                <a href="<?= base_url("/customer/comments_list")?>">
                    <i class="far fa-comment"></i>
                    <h1><?php echo $comment_count; ?></h1>
                </a>
            </div>
        </div>

        <div class="box col-lg-3 col-md-4 col-sm-6">
            <div class="box_title">
                <?=lang('messages')?>
            </div>
            <div class="box_content">
                <a href="<?= base_url("/customer/messages_list")?>">
                    <i class="far fa-envelope"></i>
                    <h1><?php echo $messages_count; ?></h1>
                </a>
            </div>
        </div>

        <div class="box col-lg-3 col-md-4 col-sm-6">
            <div class="box_title">
                <?=lang('question_and_answer')?>
            </div>
            <div class="box_content">
                <a href="<?= base_url("/customer/question_and_answer")?>">
                    <i class="far fa-question-circle"></i>
                    <h1><?php echo $question_and_answer_count; ?></h1>
                </a>
            </div>
        </div>

        <div class="box col-lg-3 col-md-4 col-sm-6">
            <div class="box_title">
                <?=lang('complaints')?>
            </div>
            <div class="box_content">
                <a href="<?= base_url("/customer/complaint_list")?>">
                    <i class="far fa-angry"></i>
                    <h1><?php echo $complaint_count; ?></h1>
                </a>
            </div>
        </div>

        <div class="box col-lg-3 col-md-4 col-sm-6">
            <div class="box_title">
                <?=lang('finished_stock')?>
            </div>
            <div class="box_content">
                <a href="<?= base_url("/products")?>">
                    <i class="far fa-list-alt"></i>
                    <h1><?php echo $finished_products_count; ?></h1>
                </a>
            </div>
        </div>
    </div>

</div>
