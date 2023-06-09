<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?PHP
$attributes = array('class' => 'main_form', 'id' => 'main_form');
echo form_open_multipart(base_url("order/view/".$id), $attributes);
?>
    <div id="tool_bar" class="container-fluid">
        <div class="row justify-content-between">
            <div class="col-sm-auto">
                <!--<button type="button" onclick="set_task('edit', 'main_form', '')" class="btn btn-success btn-sm mb-1"><?/*=lang('edit')*/?></button>-->
                <button type="button" onclick="my_print('print_invoice', '<?=lang('invoice').' - '.$id?>');" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('print_invoice')?></button>
                <button type="button" onclick="my_print('print_shipping', '<?=lang('shipping_address').' - '.$id?>');" class="btn btn-outline-secondary btn-sm mb-1"><?=lang('print_shipping')?></button>
                <a href="<?php echo base_url("order")?>"><span class="btn btn-danger btn-sm mb-1"><?=lang('cancel')?></span></a>
            </div>
        </div>
    </div>
<script>
    function my_print(print_id, title)
    {
        var print_node = window.open('', 'PRINT', 'height=400,width=600');

        print_node.document.write('<html><head><link rel="stylesheet" type="text/css" href="<?PHP echo base_url("assets/css/print_style.css?".rand(0,1000)); ?>"><link href="<?PHP echo base_url("assets/css/fontawesome/fontawesome-all.min.css"); ?>" rel="stylesheet"><title>' + title + '</title>');
        print_node.document.write('</head><body >');
        //print_node.document.write('<h1>' + document.title  + '</h1>');
        print_node.document.write(document.getElementById(print_id).innerHTML);
        print_node.document.write("<script type='text/javascript'>window.print(); window.close();<\/script>");
        print_node.document.write('</body></html>');


        //print_node.document.close(); // necessary for IE >= 10
        print_node.focus(); // necessary for IE >= 10*/

        //print_node.print();
        //print_node.close();


        //return true;
    }
</script>
    <div id="print_invoice">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <table class="table">
                        <thead class="thead-light">
                        <tr>
                            <th><i class="fa fa-shopping-cart"></i> <?=lang('order_details')?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php echo $order_details; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <table class="table">
                        <thead class="thead-light">
                        <tr>
                            <th><i class="fa fa-user"></i> <?=lang('customer_details')?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php echo $customer_details; ?>
                        </tbody>
                    </table>
                </div>
                <div id="print_shipping" class="col-md-4">
                    <table class="table">
                        <thead class="thead-light">
                        <tr>
                            <th><i class="fas fa-address-card"></i> <?=lang('receiver_details')?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php echo $receiver_details; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div><i class="fas fa-angle-double-left margin_l"></i>توجه: قیمت ها به تومان میباشد.</div>
            <table id="order_table" class="table table-responsive table-bordered">
                <thead>
                <tr>
                    <th scope="fit"><?=lang('product_pic')?></th>
                    <th class="order_description" scope="col"><?=lang('order_description')?></th>
                    <th class="fit" scope="col"><?=lang('quantity')?></th>
                    <th class="fit" scope="col"><?=lang('unit_price')?></th>
                    <th class="fit" scope="col"><?=lang('discounted_price')?></th>
                    <th scope="col"><?=lang('sum')?></th>
                </tr>
                </thead>
                <tbody>
                <?php echo $product_in_cart; ?>
                <tr>
                    <td colspan="5" class="text-right"><?=lang('sum')?></td>
                    <td><?php echo $sum_of_prices_for_selected_products; ?></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right"><?=lang('shipping_cost')?></td>
                    <td><?php echo $shipping_price; ?></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right"><?=lang('total')?></td>
                    <td><?php echo $order_total_sum; ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="container-fluid">

        <div class="border">
            <div class="history_title">
                <i class="fas fa-folder-open"></i> <?=lang('history_order')?>
            </div>
            <table class="table table-responsive table-bordered">
                <thead>
                <tr>
                    <th class="fit" scope="col"><?=lang('date_added')?></th>
                    <th scope="col" class="description"><?=lang('description')?></th>
                    <th scope="col"><?=lang('condition')?></th>
                    <th class="fit" scope="col"><?=lang('customer_notified')?></th>
                </tr>
                </thead>
                <tbody>
               <?php echo $history_view; ?>
                </tbody>
            </table>

            <div class="container-fluid form-group">
                <label for="condition"><?=lang('condition')?></label>
                <select class="form-control" id="condition" name="condition">
                    <?php echo $status_order_list; ?>
                </select>
            </div>

            <div class="container-fluid form-group">
                <label for="description"><?=lang('description')?></label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>

           <div class="container-fluid">
               <div class="form-check">
                   <input type="checkbox" class="form-check-input" id="customer_notified" name="customer_notified">
                   <label class="form-check-label" for="check2"><?=lang('customer_notified')?></label>
               </div>

               <button type="button" onclick="set_task('add_history', 'main_form', '')" class="btn btn-primary add_history"><?=lang('add')?></button>
           </div>

        </div>
    </div>
    <input type="hidden" id="task" name="task">
</form>
