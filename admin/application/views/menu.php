<div id="top_bar">
    <nav class="navbar navbar-expand-lg navbar-dark bg-darkkkkkkk">
        <!-- Just an image -->
        <a class="navbar-brand" href="<?=base_url()?>">
            <img src="<?PHP echo base_url("assets/images/logo.png"); ?>" width="40" height="40" alt="">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <?=lang('products')?>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?=base_url("products")?>"><?=lang('list_products')?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?=base_url("products/products_category_list")?>"><?=lang('products_categories')?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?=base_url("products/brands")?>"><?=lang('list_brands')?></a>
                        <!--<div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?=base_url("products/packagesproducts/packages")?>"><?=lang('list_packages')?></a>-->
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <?=lang('content')?>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?=base_url("articles")?>"><?=lang('list_content')?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?=base_url("articles/categories")?>"><?=lang('category_content')?></a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <?=lang('order')?>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?=base_url("order"); ?>"><?=lang('list_orders')?></a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <?=lang('customer')?>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?=base_url("customer")?>"><?=lang('customers_list')?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?=base_url("customer/categories")?>"><?=lang('categories_customer')?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?=base_url("customer/comments_list")?>"><?=lang('comments_list')?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?=base_url("customer/messages_list")?>"><?=lang('messages_list')?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?=base_url("customer/complaint_list")?>"><?=lang('complaint_list')?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?=base_url("customer/question_and_answer")?>"><?=lang('question_and_answer')?></a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <?=lang('menu')?>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?=base_url("menu")?>"><?=lang('menu_list')?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?=base_url("menu/categories")?>"><?=lang('menu_category')?></a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <?=lang('modules')?>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?=base_url("modules")?>"><?=lang('modules_list')?></a>
                    </div>
                </li>


                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <?=lang('setting')?>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?PHP echo base_url("setting"); ?>"><?=lang('list_fields')?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?PHP echo base_url("setting/attribute_groups"); ?>"><?=lang('attribute_groups')?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?PHP echo base_url("options"); ?>"><?=lang('options')?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?PHP echo base_url("payment"); ?>"><?=lang('payment')?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?PHP echo base_url("setting/shipping"); ?>"><?=lang('shipping')?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?PHP echo base_url("setting/status_order"); ?>"><?=lang('status_order')?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?PHP echo base_url("setting/main_settings"); ?>"><?=lang('main_settings')?></a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <?=lang('users')?>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?PHP echo base_url("users"); ?>"><?=lang('list_users')?></a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <?=lang('marketing')?>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?=base_url("marketing")?>"><?=lang('campaigns_list')?></a>
                        <!--<div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?=base_url("marketing/affiliates")?>"><?=lang('affiliates_list')?></a>-->
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?=base_url("marketing/email")?>"><?=lang('email')?></a>
                    </div>
                </li>

               <!-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">ttttttteeeeeeeeeeestttttttt
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">item 1</a></li>
                        <li><a class="dropdown-item dropdown-toggle" href="#">item 2 with submenu</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">sub item 1</a></li>
                                <li><a class="dropdown-item dropdown-toggle" href="#">sub item 2 with submenu</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">sub sub item 2-1</a></li>
                                        <li><a class="dropdown-item" href="#">sub sub item 2-2</a></li>
                                    </ul>
                                </li>
                                <li><a class="dropdown-item dropdown-toggle" href="#">sub item 3 with submenu</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">sub sub item 3-1</a></li>
                                        <li><a class="dropdown-item" href="#">sub sub item 3-2</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>-->
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                        <i class="fas fa-user"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="<?php echo base_url('users/add/'.$this->session->userdata('id'))?>"><?php echo $this->session->userdata('first_name')." ".$this->session->userdata('last_name');?></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo base_url("logout"); ?>"><?=lang('logout')?></a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</div>


<div id="title_bar" class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="h5"><?=lang($page_name)?></div>
        </div>
    </div>
</div>

<!--
<div class="">
    <ul class="reza_nav">
        <li class="nav_item"><a href="#">item 1</a></li>
        <li class="nav_item"><a href="#">item 2</a></li>
        <li class="nav_item"><a href="#">item 3 ></a>
            <ul>
                <li class="nav_item"><a href="#">sub item 1</a></li>
                <li class="nav_item"><a href="#">sub item 2</a></li>
                <li class="nav_item"><a href="#">sub item 3 ></a>
                    <ul>
                        <li class="nav_item"><a href="#">sub sub item 1</a></li>
                        <li class="nav_item"><a href="#">sub sub item 2</a></li>
                        <li class="nav_item"><a href="#">sub sub item 3</a></li>
                        <li class="nav_item"><a href="#">sub sub item 4</a></li>
                    </ul>
                </li>
                <li class="nav_item"><a href="#">item 4</a></li>
            </ul>
        </li>
        <li class="nav_item"><a href="#">item 4</a></li>
    </ul>
</div>
-->
