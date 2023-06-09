<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->session->set_userdata('last_activity', time());
	}

	/**
	 * Dar in safhe Order ra modiriat (ADD, DELETE, EDIT) mikonim
	 */
    public function index()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'لیست سفارش‌ها');
		$main_db_name = "orders";
		$html_output = array();
		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

        $this->db->select('id,has_been_added_to_csv_file');
		$check_for_delete_query = $this->db->get($main_db_name);

		if (isset($task) and isset($list_items))
		{
			$task = $this->input->post('task');
			if ($task == 'view')
			{
				redirect(base_url('order/view/'.$list_items[0]), 'location');
			}
            elseif ($task == 'delete')
            {
                foreach ($list_items as $value)
                {
                    foreach($check_for_delete_query->result() as $check_row)
                    {
                        if($check_row->id == $value)
                        {
                            if($check_row->has_been_added_to_csv_file == 1)
                            {
                                $this->db->delete($main_db_name, array('id' => $value));
                                $this->mylib->set_success(lang('deleted_successfully'));
                            }
                            else
                            {
                                $this->mylib->set_error("امکان حذف سفارش با کد $value به دلیل افزوده نشدن به فایل csv وجود ندارد.");
                            }
                            break;
                        }
                    }
                }
            }
		}

		//Customize by user search keyword
		$search = $this->input->post('search');
		//Search query
		if($search != "")
		{
			$this->db->like('first_name' , $search);
			$this->db->or_like('last_name' , $search);
			$this->db->or_like('email' , $search);
			$this->db->or_like('mobile' , $search);
			$this->db->or_like('address_title' , $search);
			$this->db->or_like('address_first_name' , $search);
			$this->db->or_like('address_last_name' , $search);
			$this->db->or_like('address_mobile' , $search);
			$this->db->or_like('address_tel' , $search);
			$this->db->or_like('address_country' , $search);
			$this->db->or_like('address_state' , $search);
			$this->db->or_like('address_city' , $search);
			$this->db->or_like('address' , $search);
			$this->db->or_like('shipping_method' , $search);
			$this->db->or_like('shipping_region' , $search);
			$this->db->or_like('cart' , $search);
			$this->db->or_like('condition' , $search);
		}
		$html_output['search'] = $search;

		//////////////////
		// Set Per_Page //
		//////////////////
		$session_handler = $this->uri->segment(1).'per_page';
		if ($this->input->post('per_page') != null)
		{
			$per_page = intval($this->input->post('per_page'));
		}
		else if ($this->session->has_userdata($session_handler))
		{
			$per_page = $this->session->userdata($session_handler);
		}
		else
		{
			$per_page = 20;
		}

		//Update Session
		$this->session->set_userdata($session_handler, $per_page);
		//Get Items from Database
		$page = ($this->uri->segment(2));
		$this->db->order_by('insert_date', 'DESC');
		$this->db->limit($per_page, $page);
		$query = $this->db->get($main_db_name);

		$temp_html = "";
		$status_order_b = $this->db->get('status_order')->result();
		if ($query->num_rows() > 0 )
		{
			foreach ($query->result() as $row)
			{
				$condition_name_li = '';
				foreach($status_order_b as $st_row)
				{
					if($row->condition == 'not_paid')
					{
						$condition_name_li = 'پرداخت نشده';
						break;
					}
					if($st_row->id == $row->condition)
					{
						$condition_name_li = $st_row->status_order;
						break;
					}
				}
				$insert_date = '';
				$modify_date = '';
				if($row->insert_date != null && $row->insert_date != 0 && $row->insert_date != '')
				{
					$insert_date = $this->date_shamsi->jdate('o/m/j', $row->insert_date,'','Asia/Tehran', 'fa');
				}
				if($row->modify_date != null && $row->modify_date != 0 && $row->modify_date != '')
				{
					$modify_date = $this->date_shamsi->jdate('o/m/j', $row->modify_date, '', 'Asia/Tehran', 'fa');
				}

				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("order/view/".$row->id).'">'. $row->id .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("order/view/".$row->id).'">'. $condition_name_li .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("order/view/".$row->id).'">'. number_format($row->order_total_sum) .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("order/view/".$row->id).'">'. $insert_date .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("order/view/".$row->id).'">'. $modify_date .'</a></td>';
				$temp_html .= "</tr>";
			}
		}
		else
		{
			//We don't have any Item in our Database
			$temp_html = "<tr><td colspan='3'>".lang('there_is_not_any_item_to_show')."</td></tr>";
		}

		$html_output['main_table_rows'] = $temp_html;
		$html_output['pagination'] = $this->mylib->get_pagination ($this->uri->segment(1), $main_db_name, $this->session->userdata($this->uri->segment(1).'per_page'), 2);
		$data['page_name'] = 'list_orders';
		///////////////////////////////////////
		// Create Error and Success Messages //
		///////////////////////////////////////
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/list', $data);
        $this->load->view('template/footer');
    }

    public function view($item_id = null)
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'مشاهده سفارش');
		$this->db->where(array('id' => $item_id));
		$orders_query = $this->db->get('orders')->result();

		$product_in_cart = '';
		$order_details = '';
		$customer_details = '';
		$receiver_details = '';
		$sum_of_prices_for_selected_products = 0;
		$shipping_price = 0;
		$order_total_sum = 0;
		$history_view = '';
		foreach($orders_query as $order_row)
		{
			$sum_of_prices_for_selected_products = $order_row->sum_of_prices_for_selected_products;
			$shipping_price = $order_row->shipping_price;
			$order_total_sum = $order_row->order_total_sum;
			////////////////////////////////////////////////
			/////////create view for order_details/////////
			//////////////////////////////////////////////
			$payment_name = '';
			$this->db->where(array('id' => $order_row->payment));
			$payment_query = $this->db->get('payment');
			foreach($payment_query->result() as $pay_row)
			{
				$payment_name = $pay_row->title;
			}
			$insert_date = '';
			if($order_row->insert_date != null && $order_row->insert_date != 0 && $order_row->insert_date != '')
			{
				$insert_date = $this->date_shamsi->jdate('o/m/j', $order_row->insert_date,'','Asia/Tehran', 'fa');
			}
			$order_details .= ' <tr>
                            <td>
                                <i class="fa fa-shopping-cart small text-primary"></i> '.$order_row->id.'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i class="fa fa-calendar small text-primary"></i> '.$insert_date.'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i class="fa fa-credit-card small text-primary"></i>   '.$payment_name.'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i class="fa fa-truck small text-primary"></i> '.lang($order_row->shipping_method).'
                            </td>
                        </tr>';
			///////////////////////////////////////////////////
			/////////create view for customer_details/////////
			/////////////////////////////////////////////////
			$customer_group_name = '';
			$this->db->where(array('id' => $order_row->customer_group));
			$customer_category_query = $this->db->get('customer_category')->result();
			foreach($customer_category_query as $cus_cat_row)
			{
				$customer_group_name = $cus_cat_row->title;
			}
			$customer_details .= ' <tr>
                            <td>
                                <i class="fa fa-user small text-primary"></i>  '.$order_row->first_name.' '.$order_row->last_name.'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i class="fas fa-users small text-primary"></i> '.$customer_group_name.'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i class="fas fa-envelope small text-primary"></i>  '.$order_row->email.'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i class="fas fa-phone small text-primary"></i>  '.$order_row->mobile.'
                            </td>
                        </tr>';
			///////////////////////////////////////////////////
			/////////create view for receiver_details/////////
			/////////////////////////////////////////////////
			$receiver_details .= ' <tr>
                        <td>
                            <i class="fa fa-user small text-primary"></i>  '.$order_row->address_first_name.' '.$order_row->address_last_name.'
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <i class="fas fa-phone small text-primary"></i>  '.$order_row->address_mobile.'
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <i class="fas fa-map-marker-alt small text-primary"></i> '.$order_row->address_country.'، '.$order_row->address_state.'، '.$order_row->address_city.'، '.$order_row->address.'
                        </td>
                    </tr>';
			///////////////////////////////////////////////////
			///////////create list for products///////////////
			/////////////////////////////////////////////////
			$cart = json_decode($order_row->cart);
			if($cart != '' && $cart != array() && $cart != null)
			{
				$option_query = json_decode($order_row->option_query);
				$attr = $cart;
				$product_cat_array = json_decode($order_row->product_cat_array);

				foreach($attr as $index => $value)
				{
					$option_view = '';
					$price_products_array = json_decode($order_row->price_products_array);

					$discounted_price = $price_products_array->$index->discounted_price;
					$discount_price = $price_products_array->$index->discount_price;

					if(isset($product_cat_array))
					{
						foreach($product_cat_array as $cat_row)
						{
							if($index == $cat_row->id)
							{
								$product_category = $cat_row->category;
								break;
							}
						}
					}

					if(isset($product_category))
					{
						foreach($option_query as $op_row)
						{
							$temp_val = "option_".$op_row->id;
							if($op_row->category == $product_category && isset($value->$temp_val))
							{
								if($op_row->type == 'single_entry' || $op_row->type == 'textarea')
								{
									$temp_single_entry = "option_".$op_row->id;
									$option_view .= '<div>
                                            <i class="fas fa-angle-double-left"></i>
                                            <span><b>'.$op_row->title.': </b></span>
                                            <span>'.$value->$temp_single_entry.'</span>
                                        </div>';
								}
								elseif($op_row->type == 'select')
								{
									$temp_single_entry = "option_".$op_row->id;
									$option_view .= '<div>
                                            <i class="fas fa-angle-double-left"></i>
                                            <span><b>'.$op_row->title.': </b></span>
                                            <span>'.$value->$temp_single_entry.'</span>
                                        </div>';
								}
								elseif($op_row->type == 'multiple_entry')
								{
									$temp_multiple_entry = "option_".$op_row->id;
									$temp_multiple_entry = $value->$temp_multiple_entry;
									$temp_multiple_entry = str_replace("::new_line::", ', ', $temp_multiple_entry);
									$option_view .= '<div>
                                            <i class="fas fa-angle-double-left"></i>
                                            <span><b>'.$op_row->title.': </b></span>
                                            <span>'.$temp_multiple_entry.'</span>
                                        </div>';
								}
								elseif($op_row->type == 'checkbox')
								{
									$temp_checkbox = "option_".$op_row->id;
									$temp_checkbox = $value->$temp_checkbox;
									$temp_checkbox_array = json_decode(json_encode($temp_checkbox), true);
									if(is_array($temp_checkbox_array))
									{
										$temp_checkbox = implode(', ', $temp_checkbox_array);
									}
									$option_view .= '<div>
										<i class="fas fa-angle-double-left"></i>
										<span><b>'.$op_row->title.': </b></span>
										<span>'.$temp_checkbox.'</span>
									</div>';
								}
								elseif($op_row->type == 'upload')
								{
									$temp_upload = "option_".$op_row->id;
									$temp_upload = $value->$temp_upload;
									$temp_upload = base_url('content/customer_files/'.$temp_upload);
									$temp_upload = str_replace('/admin','',$temp_upload);
									$option_view .= ' <div>
                                            <i class="fas fa-angle-double-left"></i>
                                            <span><b>'.$op_row->title.': </b></span>
                                            <a target="_blank" href="'.$temp_upload.'"><i class="fas fa-cloud-download-alt"></i></a>
                                        </div>';
								}
								elseif($op_row->type == 'file')
								{
									$option_view .= ' <div>
                                            <i class="fas fa-angle-double-left"></i>
                                            <span><b>'.$op_row->title.'</b></span>
                                        </div>';
								}
							}
						}
					}

					$number_format_product_price = number_format($value->product_price);
					$number_format_discount_price = number_format($discount_price);
					$class_not_sold = '';
					if(isset($value->base_product))
					{
						if($value->base_product == 'not_sold')
						{
							$number_format_product_price = 0;
							$number_format_discount_price = 0;
							$class_not_sold = 'class="not_sold"';
						}
					}

					$single_product_link = base_url('pages/single_product/'.$index);
					$single_product_link = str_replace('/admin','',$single_product_link);
					$product_in_cart .= '  <tr>
                                    <td class="image">
                                        <a href="'.$single_product_link.'">
                                            <img src="'.$value->product_first_image_src.'">
                                        </a>
                                    </td>
                                    <td>
                                        <div class="mb-3">
                                            <span '.$class_not_sold.'><b>'.$value->product_title.'</b></span>
                                        </div>
                                       '.$option_view.'
                                    </td>
                                   <td> '.$value->tedad.'</td>
                                    <td class="unit_price">'.$number_format_product_price.'</td>
                                    <td class="discount">'.$number_format_discount_price.'</td>
                                    <td class="final_item_price">'.number_format($discounted_price).'</td>
                                </tr>';
				}
			}
            //////////////////////////////////////////////////////////
			////////////////////history order////////////////////////
			////////////////////////////////////////////////////////
			$history_order = json_decode($order_row->history_order);
            if(!is_array($history_order))
            {
                //در صورتی که تاریخچه سفارش فعلی تهی یا null باشد یعنی آرایه نباشد
                // در اون صورت ما باید ابتدا وضعیت سفارش اولیه رو به تاریخچه سفارش
                // افزوده و بعد از آن وضعیت سفارش جدید رو به تاریخچه سفارش بیفزاییم.
                $history_order = array();
                $new_history_order = array();
                $new_history_order['date_added'] = $order_row->insert_date;//وضعیت سفارش اولیه در تاریخ ثبت سفارش ایجاد میشود.
                $new_history_order['description'] = '';
                if($order_row->condition == 'not_paid')
                {
                    $new_history_order['condition'] = 1;
                }
                else
                {
                    $new_history_order['condition'] = $order_row->condition;
                }
                $new_history_order['customer_notified'] = null;
                array_push($history_order, $new_history_order);
            }
			$new_history_order = array();
			$new_history_order['date_added'] = time();
			$new_history_order['description'] = $this->input->post('description');
			$new_history_order['condition'] = $this->input->post('condition');
			$new_history_order['customer_notified'] = $this->input->post('customer_notified');
			array_push($history_order, $new_history_order);

			$dadeh = array(
				'history_order' => json_encode($history_order),
				'customer_notified' => $this->input->post('customer_notified'),
				'condition' => $this->input->post('condition'),
				'modify_date' => time()
			);
			$status_order_q = $this->db->get('status_order')->result();
			if($item_id)
			{
				$task = $this->input->post('task');
				if($task == 'add_history' && $this->input->post('condition') != '' && $this->input->post('condition') != null)
				{
					//send email
					$email = $order_row->email;
					$time = $this->date_shamsi->jdate('o/m/j', time(),'','Asia/Tehran', 'fa');
					$description =  $this->input->post('description');
					$condition_name = '';
					foreach($status_order_q as $s_row)
					{
						if($s_row->id == $this->input->post('condition'))
						{
							$condition_name = $s_row->status_order;
						}
					}
					if($email != '' && $email != null && $this->input->post('customer_notified') == 'on')
					{
                        $this->db->select('system_email,subject_for_order_status_email,message_text_for_order_status_email');
                        $this->db->where(array('id' => 1));
                        $setting_query = $this->db->get('setting');

                        foreach($setting_query->result() as $setting_row)
                        {
                            $message = str_replace("{{name}}", $order_row->first_name.' '.$order_row->last_name, $setting_row->message_text_for_order_status_email);
                            $message = str_replace("{{order_code}}", $order_row->id, $message);
                            $message = str_replace("{{time}}", $time, $message);
                            $message = str_replace("{{condition_name}}", $condition_name, $message);
                            $message = str_replace("{{description}}", $description, $message);
                            $subject = str_replace("{{order_code}}", $order_row->id, $setting_row->subject_for_order_status_email);

                            $this->load->library('email');
                            $mail_config['mailtype'] = "html";
                            $mail_config['charset'] = "utf8";
                            $this->email->initialize($mail_config);
                            $this->email->from($setting_row->system_email, $setting_row->system_email);
                            $this->email->to($email);
                            $this->email->subject($subject);
                            $this->email->message($message);
                            $this->email->send();
                        }
                        //$subject = "اطلاع رسانی از وضعیت سفارش با کد $order_row->id | فروشگاه اینترنتی کتاب پرگار";
						//$answer = "<div dir='rtl' style='text-align: right;'>با سلام و احترام<br/>$order_row->first_name $order_row->last_name عزیز،<br/>سفارش شما با کد $order_row->id در تاریخ $time در وضعیت $condition_name قرار گرفت.<br/>توضیح مدیریت: $description<br/><br/>با سپاس<br/>فروشگاه اینترنتی کتاب پرگار</div>";
					}

					$this->db->where('id', $item_id);
					$is_update = $this->db->update('orders', $dadeh);
					/////////////////////////////////////////////////////////////
					///////Decrease number of product and option in order///////
					///////////////////////////////////////////////////////////
					if($is_update == true && $this->input->post('condition') == 3 && $cart != '' && $cart != array() && $cart != null)
					{
						$sh = 1;
						foreach ($cart as $in_cart => $val_cart)
						{
							if($sh == 1)
							{
								$this->db->where(array('id' => $in_cart));
							}
							else
							{
								$this->db->or_where(array('id' => $in_cart));
							}
							$sh++;
						}
						if($sh > 1)
						{
							$this->db->select('id,category,type_of_discount,discount_amount,options,number');
							$product_array = $this->db->get('add_products')->result();
						}

						foreach($cart as $in_cart => $val_cart)
						{
							$number_of_product_to_decrease = $val_cart->tedad;
							if(isset($val_cart->base_product))
							{
								if($val_cart->base_product == 'not_sold')
								{
									$number_of_product_to_decrease = 0;
								}
							}
							if(isset($product_array))
							{
								foreach($product_array as $pr_row)
								{
									if($in_cart == $pr_row->id)
									{
										$product_category = $pr_row->category;
										$option_json = json_decode($pr_row->options);
										break;
									}
								}
							}
							if(isset($product_category) && isset($option_query))
							{
								foreach($option_query as $op_row)
								{
									$temp_val = "option_".$op_row->id;
									if($op_row->category == $product_category && isset($val_cart->$temp_val))
									{
										if($op_row->type == 'single_entry' || $op_row->type == 'textarea' || $op_row->type == 'multiple_entry' || $op_row->type == 'upload' || $op_row->type == 'file')
										{
											$op_id = $op_row->id;
											if(isset($option_json))
											{
												if(isset($option_json->$op_id))
												{
													if(isset($option_json->$op_id->product_quantity_with_option))
													{
														$option_json->$op_id->product_quantity_with_option = $option_json->$op_id->product_quantity_with_option - $val_cart->tedad;
														if(isset($option_json->$op_id->reduce_total_inventory))
														{
															if($option_json->$op_id->reduce_total_inventory == 'yes')
															{
																$number_of_product_to_decrease = $number_of_product_to_decrease + $val_cart->tedad;
															}
														}
													}
												}
											}
										}
										elseif($op_row->type == 'select')
										{
											$temp_select = $val_cart->$temp_val;
											$op_id = $op_row->id;
											if(isset($option_json))
											{
												if(isset($option_json->$op_id))
												{
													if(isset($option_json->$op_id->$temp_select))
													{
														if(isset($option_json->$op_id->$temp_select->product_quantity_with_option))
														{
															$option_json->$op_id->$temp_select->product_quantity_with_option = $option_json->$op_id->$temp_select->product_quantity_with_option - $val_cart->tedad;
															if(isset($option_json->$op_id->$temp_select->reduce_total_inventory))
															{
																if($option_json->$op_id->$temp_select->reduce_total_inventory == 'yes')
																{
																	$number_of_product_to_decrease = $number_of_product_to_decrease + $val_cart->tedad;
																}
															}
														}
													}
												}
											}
										}
										elseif($op_row->type == 'checkbox')
										{
											$temp_checkbox = $val_cart->$temp_val;
											$temp_checkbox_array = json_decode(json_encode($temp_checkbox), true);
											$op_id = $op_row->id;
											if(isset($option_json))
											{
												foreach($temp_checkbox_array as $t_row)
												{
													if(isset($option_json->$op_id))
													{
														if(isset($option_json->$op_id->$t_row))
														{
															if(isset($option_json->$op_id->$t_row->product_quantity_with_option))
															{
																$option_json->$op_id->$t_row->product_quantity_with_option = $option_json->$op_id->$t_row->product_quantity_with_option - $val_cart->tedad;
																if(isset($option_json->$op_id->$t_row->reduce_total_inventory))
																{
																	if($option_json->$op_id->$t_row->reduce_total_inventory == 'yes')
																	{
																		$number_of_product_to_decrease = $number_of_product_to_decrease + $val_cart->tedad;
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
							$this->db->set('number', "number-$number_of_product_to_decrease", FALSE);
							if(isset($option_json))
							{
								$option_json = json_encode($option_json);
								$this->db->set('options', $option_json);
							}
							$this->db->where(array('id' => $in_cart));
							$this->db->update('add_products');
						}
					}
					redirect(base_url("order/view/".$item_id));
				}
			}
			///////////////////////////////////////////////////
			////////////create history view///////////////////
			/////////////////////////////////////////////////
			if(is_array($history_order))
			{
				foreach($history_order as $his_row)
				{
				    if(isset($his_row->description))
					{
						if($his_row->customer_notified == 'on')
						{
							$customer_notified = 'بله';
						}
						else
						{
							$customer_notified = 'خیر';
						}
						$condition_name = '';
						foreach($status_order_q as $s_row)
						{
							if($s_row->id == $his_row->condition)
							{
								$condition_name = $s_row->status_order;
							}
						}
						$date_added = '';
						if($his_row->date_added != null && $his_row->date_added != 0 && $his_row->date_added != '')
						{
							$date_added = $this->date_shamsi->jdate('o/m/j', $his_row->date_added,'','Asia/Tehran', 'fa');
						}
						$history_view .= ' <tr>
                    <td>'.$date_added.'</td>
                    <td>'.$his_row->description.'</td>
                    <td>'.$condition_name.'</td>
                    <td>'.$customer_notified.'</td>
                </tr>';
					}
				}
			}
		}
		//////////////////////////////////////////////////////
		//////////Create A list for status_order/////////////
		////////////////////////////////////////////////////
		$this->db->where(array('publish' => 'yes'));
		$status_order_query = $this->db->get('status_order')->result();
		$status_order_list = "<option value=''>".lang ('please_select')."</option>";

		if (count($status_order_query) > 0)
		{
			$condition = 0;
			if(! is_null($item_id))
			{
				foreach($orders_query as $order_row)
				{
					$condition = $order_row->condition;
				}
			}

			foreach ($status_order_query as $sta_row)
			{
				if($condition == 'not_paid')
				{
					$status_order_list .= '<option value="'.$sta_row->id.'" '.set_select('condition', $sta_row->id, ($sta_row->id == 1 ? true : false)).'>'.$sta_row->status_order.'</option>';
				}
				else
				{
					$status_order_list .= '<option value="'.$sta_row->id.'" '.set_select('condition', $sta_row->id, ($sta_row->id == $condition ? true : false)).'>'.$sta_row->status_order.'</option>';
				}
			}
		}

		$data = array
		(
			'id' => $item_id,
			'page_name' => 'view_order',
			'order_details' => $order_details,
			'customer_details' => $customer_details,
			'receiver_details' => $receiver_details,
			'product_in_cart' => $product_in_cart,
			'sum_of_prices_for_selected_products' => $sum_of_prices_for_selected_products,
			'shipping_price' => $shipping_price,
			'order_total_sum' => $order_total_sum,
			'status_order_list' => $status_order_list,
			'history_view' => $history_view
		);

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/view');
        $this->load->view('template/footer');
    }

    public function export()
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

        //برای منحصر به فرد بودن  نام فایل csv و اطلاع از تاریخ ایجاد, تاریخ و زمان ایجاد رو به نام فایل csv اضافه میکنیم.
        $temp_date = $this->date_shamsi->jdate('o_m_j', time(), '', 'Asia/Tehran', 'en');
        $temp_time =  $this->date_shamsi->jdate('H_i_s', time(), '', 'Asia/Tehran', 'en');
        $temp_date_and_time = $temp_date.'-'.$temp_time;

        //تمام query های مورد نیاز.
        $this->db->order_by('insert_date', 'DESC');
        $query = $this->db->get('orders');

        $this->db->select('id,title');
        $customer_category_query = $this->db->get('customer_category');

        $this->db->select('id,title');
        $payment_query = $this->db->get('payment');

        $this->db->select('id,status_order');
        $status_order_query = $this->db->get('status_order');

        //ایجاد فایل مورد نظر.
        $temp_file = fopen('../content/csv_files/file_'.$temp_date_and_time.'.csv', 'w');
        $orders_id_array = array();//آرایه ای که id سفارشهایی که به فایل csv افزودیم رو توش ذخیره میکنیم.
        foreach ($query->result_array() as $index => $value)
        {
            $items_that_want_to_display_in_the_output_cart = array('id', 'user_id', 'first_name', 'last_name', 'email', 'customer_group', 'mobile', 'address_title', 'address_first_name', 'address_last_name', 'address_mobile', 'address_tel', 'address_postcode', 'address_country', 'address_state', 'address_city', 'shipping_region', 'address', 'payment', 'shipping_method', 'cart', 'sum_of_prices_for_selected_products', 'shipping_price', 'order_total_sum', 'condition', 'insert_date', 'modify_date', 'history_order');
            if($index == 0)
            {
                fwrite($temp_file, "\xEF\xBB\xBF");//این کاراکترهایی که اول فایل اضافه میکنم bom برای utf8 هست که اگه نذاریمش کلمات فارسی تو اکسل خرچنگ قورباغه میشه
                $temp_array = array();
                foreach ($items_that_want_to_display_in_the_output_cart as $row)
                {
                    if($row == 'id')
                    {
                        array_push($temp_array, 'کد سفارش');
                    }
                    else
                    {
                        array_push($temp_array, lang($row));
                    }
                }
                fputcsv($temp_file, $temp_array);//اینجا ما موارد سر تیتر مثل first_name رو تو فایلمون درج میکنیم.
            }

            $cart_description = '';
            $cart = json_decode($value['cart']);
            if($cart != '' && $cart != array() && $cart != null)
            {
                $option_query = json_decode($value['option_query']);
                $product_cat_array = json_decode($value['product_cat_array']);

                foreach($cart as $cart_index => $cart_value)
                {
                    $price_products_array = json_decode($value['price_products_array']);

                    $discounted_price = $price_products_array->$cart_index->discounted_price;
                    $discount_price = $price_products_array->$cart_index->discount_price;

                    $number_format_product_price = number_format($cart_value->product_price);
                    $number_format_discount_price = number_format($discount_price);
                    $class_not_sold = '';
                    if(isset($cart_value->base_product))
                    {
                        if($cart_value->base_product == 'not_sold')
                        {
                            $number_format_product_price = 0;
                            $number_format_discount_price = 0;
                            $class_not_sold = 'class="not_sold"';
                        }
                    }
                    $cart_description .= "محصول $cart_value->product_title با کد $cart_index به تعداد $cart_value->tedad با قیمت واحد $number_format_product_price با تخفیف $number_format_discount_price و قیمت نهایی $discounted_price."."\n";

                    if(isset($product_cat_array))
                    {
                        foreach($product_cat_array as $cat_row)
                        {
                            if($cart_index == $cat_row->id)
                            {
                                $product_category = $cat_row->category;
                                break;
                            }
                        }
                    }

                    if(isset($product_category))
                    {
                        foreach($option_query as $op_row)
                        {
                            $temp_val = "option_".$op_row->id;
                            if($op_row->category == $product_category && isset($cart_value->$temp_val))
                            {
                                if($op_row->type == 'single_entry' || $op_row->type == 'textarea')
                                {
                                    $temp_single_entry = "option_".$op_row->id;
                                    $option_value = $cart_value->$temp_single_entry;
                                    $cart_description .= "و با آپشن $op_row->title با مقادیر $option_value"."\n";
                                }
                                elseif($op_row->type == 'select')
                                {
                                    $temp_single_entry = "option_".$op_row->id;
                                    $option_value = $cart_value->$temp_single_entry;
                                    $cart_description .= "و با آپشن $op_row->title با مقادیر $option_value"."\n";
                                }
                                elseif($op_row->type == 'multiple_entry')
                                {
                                    $temp_multiple_entry = "option_".$op_row->id;
                                    $temp_multiple_entry = $cart_value->$temp_multiple_entry;
                                    $temp_multiple_entry = str_replace("::new_line::", ', ', $temp_multiple_entry);
                                    $cart_description .= "و با آپشن $op_row->title با مقادیر $temp_multiple_entry"."\n";
                                }
                                elseif($op_row->type == 'checkbox')
                                {
                                    $temp_checkbox = "option_".$op_row->id;
                                    $temp_checkbox = $cart_value->$temp_checkbox;
                                    $temp_checkbox_array = json_decode(json_encode($temp_checkbox), true);
                                    if(is_array($temp_checkbox_array))
                                    {
                                        $temp_checkbox = implode(', ', $temp_checkbox_array);
                                    }
                                    $cart_description .= "و با آپشن $op_row->title با مقادیر $temp_checkbox"."\n";
                                }
                                elseif($op_row->type == 'upload')
                                {
                                    $cart_description .= "و با آپشن $op_row->title"."\n";
                                }
                                elseif($op_row->type == 'file')
                                {
                                    $cart_description .= "و با آپشن $op_row->title"."\n";
                                }
                            }
                        }
                    }
                }
            }

            //find customer group name
            $customer_group_name = '';
            foreach($customer_category_query->result() as $cus_cat_row)
            {
                if($cus_cat_row->id == $value['customer_group'])
                {
                    $customer_group_name = $cus_cat_row->title;//چون گروه کاربری بر اساس id برای سفارشها ذخیره شده و ما اسمشو برای خروجی csv میخوایم میریم و اسمشو پیدا میکنیم.
                    break;
                }
            }
            $value['customer_group'] = $customer_group_name;

            //find payment name
            $payment_name = '';
            foreach($payment_query->result() as $pay_row)
            {
                if($pay_row->id == $value['payment'])
                {
                    $payment_name = $pay_row->title;
                    break;
                }
            }
            $value['payment'] = $payment_name;

            //translation for shipping method
            $value['shipping_method'] = lang($value['shipping_method']);

            //number format
            $value['sum_of_prices_for_selected_products'] = number_format($value['sum_of_prices_for_selected_products']);
            $value['shipping_price'] = number_format($value['shipping_price']);
            $value['order_total_sum'] = number_format($value['order_total_sum']);

            //find condition name
            $condition_name = '';
            foreach($status_order_query->result() as $status_order_row)
            {
                if($value['condition'] == 'not_paid')
                {
                    $condition_name = 'پرداخت نشده';
                    break;
                }
                elseif($status_order_row->id == $value['condition'])
                {
                    $condition_name = $status_order_row->status_order;
                    break;
                }
            }
            $value['condition'] = $condition_name;

            //insert date and modify date
            $insert_date = '';
            $modify_date = '';
            if($value['insert_date'] != null && $value['insert_date'] != 0 && $value['insert_date'] != '')
            {
                $insert_date = $this->date_shamsi->jdate('o/m/j', $value['insert_date'],'','Asia/Tehran', 'fa');
            }
            if($value['modify_date'] != null && $value['modify_date'] != 0 && $value['modify_date'] != '')
            {
                $modify_date = $this->date_shamsi->jdate('o/m/j', $value['modify_date'], '', 'Asia/Tehran', 'fa');
            }
            $value['insert_date'] = $insert_date;
            $value['modify_date'] = $modify_date;

            //history order
            $history_order_description = '';
            $history_order = json_decode($value['history_order']);
            if(is_array($history_order))
            {
                foreach($history_order as $his_row)
                {
                    if(isset($his_row->description))
                    {
                        $condition_name = '';
                        foreach($status_order_query->result() as $status_order_row)
                        {
                            /*if($his_row->condition == 'not_paid')
                            {
                                $condition_name = 'پرداخت نشده';
                                break;
                            }
                            else*/if($status_order_row->id == $his_row->condition)
                            {
                                $condition_name = $status_order_row->status_order;
                                break;
                            }
                        }
                        $date_added = '';
                        if($his_row->date_added != null && $his_row->date_added != 0 && $his_row->date_added != '')
                        {
                            $date_added = $this->date_shamsi->jdate('o/m/j', $his_row->date_added,'','Asia/Tehran', 'fa');
                        }
                        $history_order_description .= "در تاریخ $date_added سفارش در وضعیت $condition_name قرار گرفت. توضیحات: $his_row->description"."\n";
                    }
                }
            }
            $value['history_order'] = $history_order_description;

            //درج مقادیر مورد نیاز داخل فایل csv
            $temp_value = array();
            foreach ($items_that_want_to_display_in_the_output_cart as $row)
            {
                $temp_value[$row] = $value[$row];
            }
            $temp_value['cart'] = $cart_description;
            $check_fputcsv = fputcsv($temp_file, $temp_value);// اینجا مقادیر هر ردیف رو درج میکنیم.

            //افزودن id سفارش به آرایه‌ی مربوط به لیست id سفارشهایی که به فایل csv افزوده شده اند.
            if($check_fputcsv != null && $temp_file != null)
            {
                array_push($orders_id_array, $value['id']);
            }
        }
        $check_fclose = fclose($temp_file);

        //update has_been_added_to_csv_file in orders table for orders added to csv file
        if($temp_file != null && $check_fclose != null && $orders_id_array != array())
        {
            $this->db->set('has_been_added_to_csv_file', 1);
            $this->db->where_in('id', $orders_id_array);
            $this->db->update('orders');
        }

        //email file to admin email
        $this->db->select('system_email,admin_email');
        $this->db->where(array('id' => 1));
        $setting_query = $this->db->get('setting');

        $file_name = 'file_'.$temp_date_and_time.'.csv';
        foreach($setting_query->result() as $setting_row)
        {
            $this->load->library('email');
            $mail_config['mailtype'] = "html";
            $mail_config['charset'] = "utf8";
            $this->email->initialize($mail_config);
            $this->email->from($setting_row->system_email, $setting_row->system_email);
            $this->email->to($setting_row->admin_email);
            $this->email->subject("آرشیو سفارش‌ها - $file_name");
            $this->email->message('جهت دریافت آرشیو سفارش‌ها، فایل پیوست را مشاهده نمایید');
            $this->email->attach('../content/csv_files/file_'.$temp_date_and_time.'.csv');
            $this->email->send();
        }

        //download csv file
        $file = fopen('../content/csv_files/file_'.$temp_date_and_time.'.csv', 'r');
        header("Content-Type:text/plain");
        header("Content-Disposition: attachment; filename=\"$file_name\"");
        fpassthru($file);
        fclose($file);
    }
}
