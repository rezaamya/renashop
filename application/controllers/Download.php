<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Download extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->session->set_userdata('last_activity', time());
    }

    public function file($file_name = null)
    {
        if($this->session->has_userdata('id') && $file_name != null && $file_name != '')
        {
            $this->db->where(array('user_id' => $this->session->userdata('id')));
            $order_query = $this->db->get('orders');

            $this->db->select('id,order_will_be_finished_in_this_status,are_virtual_products_accessible_by_customers_in_this_status');
            $status_order_query = $this->db->get('status_order');

            foreach($order_query->result() as $order_row)
            {
                $cart = json_decode($order_row->cart);
                if($cart != '' && $cart != array() && $cart != null)
                {
                    $option_query = json_decode($order_row->option_query);
                    $recorded_information_for_the_product_when_ordering = json_decode($order_row->product_cat_array);
                    foreach($cart as $index => $value)
                    {
                        if(isset($recorded_information_for_the_product_when_ordering))
                        {
                            foreach($recorded_information_for_the_product_when_ordering as $rec_info_row)
                            {
                                if($index == $rec_info_row->id)
                                {
                                    $product_category = $rec_info_row->category;
                                    $option_json = json_decode($rec_info_row->options);
                                    break;
                                }
                            }
                        }
                        if(isset($product_category))
                        {
                            foreach($option_query as $op_row)
                            {
                                $temp_val = "option_".$op_row->id;
                                if($op_row->category == $product_category && $op_row->type == 'file' && isset($value->$temp_val))
                                {
                                    $op_id = $op_row->id;
                                    foreach($status_order_query->result() as $s_row)
                                    {
                                        if($order_row->condition == $s_row->id)
                                        {
                                            if($s_row->are_virtual_products_accessible_by_customers_in_this_status == 'yes' && isset($option_json))
                                            {
                                                if(isset($option_json->$op_id) && isset($option_json->$op_id->file_name) && isset($option_json->$op_id->orig_name))
                                                {
                                                    if($file_name == $option_json->$op_id->file_name && file_exists('content/option_file/'.$file_name))
                                                    {
                                                        $file = fopen('content/option_file/'.$file_name, 'r');
                                                        header("Content-Type:text/plain");
                                                        header("Content-Disposition: attachment; filename=\"$file_name\"");
                                                        fpassthru($file);
                                                        fclose($file);
                                                        die();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        echo '.شما دسترسی به فایل مورد نظر را ندارید';
    }
}