<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->session->set_userdata('last_activity', time());
	}

	public function index()
	{
        $this->mylib->is_login();

		$this->session->set_userdata('page_title', 'پروفایل');
		if($this->session->userdata('id') != null)
		{
			$this->load->model('localization/regions');

			$this->db->where(array('id' => $this->session->userdata('id')));
			$this->db->select('address');
			$temp_address = $this->db->get('customer')->row();
			$temp_address = $temp_address->address;

			$temp_address_json = json_decode($temp_address);

			$address_view_1 = '';
			$address_view = '';
			$add_new_address = '';

			if($temp_address_json != '[]' && $temp_address_json != '' && $temp_address_json != null)
			{
				if(is_array($temp_address_json))
				{
					krsort($temp_address_json);
				}
				foreach ($temp_address_json as $json_index => $json_row)
			{
				$country_id = $json_row->address_country;
				$state_id = $json_row->address_state;
				$city_id = $json_row->address_city;
				$address = $json_row->address;

				$country_name = $this->regions->find_countries($country_id);
				$state_name = $this->regions->find_states($state_id);
				$city_name = $this->regions->find_cities($city_id);

				$address_view_1 .='<div address_id="'.$json_index.'" address_title="'.$json_row->address_title.'" address_link="'.base_url('profile/edit_address/'.$json_index).'" class="w3-card address_item">
                <div>
                    <button class="w3-button w3-red w3-round w3-left w3-show-inline-block w3-margin-top w3-margin-left"  onclick="delete_address(\'initialize\', this);"><i class="fas fa-minus"></i></button>
                    <a href="'.base_url('profile/new_address').'" class="w3-button w3-green w3-round w3-left w3-show-inline-block w3-margin-top w3-margin-left"><i class="fas fa-plus"></i></a>
                    <a href="'.base_url('profile/edit_address/'.$json_index).'" class="w3-button w3-light-blue w3-round w3-left w3-show-inline-block w3-margin-top w3-margin-left"><i class="fas fa-edit"></i></a>
                    <h5 class="w3-show-inline-block w3-margin-right">'.$json_row->address_title.'</h5>
                    <div class="w3-margin"><i class="fas fa-user w3-large w3-margin-left"></i>تحویل گیرنده: '.$json_row->address_first_name.' '.$json_row->address_last_name.'</div>
                    <div class="w3-margin"><i class="fas fa-mobile-alt w3-large w3-margin-left"></i>شماره تماس: '.$json_row->address_mobile.'</div>
                    <div class="w3-margin"><i class="fas fa-map-marker-alt w3-large w3-margin-left"></i>آدرس: '.$country_name.', '.$state_name.', '.$city_name.', '.$address.'</div>
                    <div class="w3-padding-16 navar"></div>
                </div>

            </div>';
			}

			$address_view = '<div id="address" class="w3-container tab" style="display:none">
        <div class="w3-row-paddinggggggg w3-margin-toppppppp address_holder">
        '.$address_view_1.'
        </div>
        <div class="w3-modal w3-animate-opacity delete_address_popup">
            <div class="w3-modal-content w3-card-4">
                <div class="modal_header w3-red">
                    <span onclick="closest_parent(this, \'delete_address_popup\').style.display=\'none\'" class="w3-button w3-large w3-left">&times;</span>
                    <div class="modal_title w3-right w3-large">حذف آدرس</div>
                </div>
                <div class="w3-container modal_content">
                    <p>آیا میخواهید «<a class="address_title address_link" href="#">عنوان محصول</a>» را از لیست آدرس‌هایتان حذف کنید؟</p>
                    <p class="w3-left-align">
                        <button address_id="" class="w3-button w3-red agree_btn" onclick="delete_address(\'delete\', this)">بله</button>
                        <button class="w3-button w3-green" onclick="closest_parent(this, \'delete_address_popup\').style.display=\'none\'">خیر</button>
                    </p>
                </div>
            </div>
        </div>
    </div>';}
    else
	{
		$add_new_address = '<div  id="address" class="w3-margin-top w3-padding w3-container tab" style="display:none">
        <span>هیچ آدرسی برای ارسال وجود ندارد. لطفا از طریق دکمه "اضافه کردن آدرس" اقدام به درج آدرس نمایید.</span>
        <a href="'.base_url("profile/new_address").'" class="w3-button w3-green w3-round"><i class="fas fa-plus"><span> اضافه کردن آدرس</span></i></a>
    </div>';
	}

			$view_favorite = '';

			$this->db->where(array('user_id' => $this->session->userdata('id')));
			$db_favorite = $this->db->get('favorite')->result();

			if($db_favorite)
			{
				foreach ($db_favorite as $favorite_row)
				{
					$this->db->group_start();
					$this->db->where(array('id' => $favorite_row->product_id));
					$this->db->group_end();
					$this->db->group_start();
					$this->db->where(array('publish' => 'yes'));
					$this->db->group_end();
					$this->db->group_start();
					$this->db->where(array('type_of_category' => 'virtual'));
					$this->db->or_where(array('number >' => 0));
					$this->db->or_where(array('finish' => 2));
					$this->db->or_where(array('finish' => 3));
					$this->db->group_end();
					$this->db->select('title,primary_pic');
					$temp_product = $this->db->get('add_products');

					$temp_product_title = '';
					foreach ($temp_product->result() as $product_row)
					{
						$temp_product_title = $product_row->title;
						$json_pic = json_decode($product_row->primary_pic);
					}

					$pic_name = '';
					if(isset($json_pic))
					{
						if(is_array($json_pic))
						{
							if(isset($json_pic[0]))
							{
								if(isset($json_pic[0]->file_name))
								{
									$pic_name = $json_pic[0]->file_name;
								}
							}
						}
					}

					if ($pic_name == '')
					{
						$pic_name = "no_pic.jpg";
					}
					$pic_src = base_url('content/products/thumb/'."$pic_name");

					if($temp_product_title != '')
					{
						$view_favorite .= '<div class="w3-quarter favorite_item" product_link="'.base_url('pages/single_product/'.$favorite_row->product_id).'" product_id="'.$favorite_row->product_id.'" product_title="'.$temp_product_title.'">
                    <span class="closebtn" onclick="delete_favorites(\'initialize\', this);">&times;</span>
                    <div class="w3-card w3-center">
                        <a href="'.base_url('pages/single_product/'.$favorite_row->product_id).'">
                            <img src="'.$pic_src.'">
                            <div class="w3-container">
                                <h5 class="item_title">'.$temp_product_title.'</h5>
                            </div>
                        </a>
                    </div>
                </div>';
					}
				}
			}

			$this->db->where(array('id' => $this->session->userdata('id')));
			$query = $this->db->get('customer');

			$first_name = '';
			$last_name = '';
			$username = '';
			$email = '';
			$day = '';
			$month = '';
			$year = '';
			$customer_group_title = '';
			$mobile = '';
			foreach ($query->result() as $row)
			{
				$first_name = $row->first_name;
				$last_name = $row->last_name;
				$username = $row->username;
				$email = $row->email;
				$day = $row->day;
				$month = $row->month;
				$year = $row->year;
				$mobile = $row->mobile;
				$customer_group = $row->customer_group;
				$this->db->where(array('id' => $customer_group));
				$this->db->select('title');
				$customer_group_title = $this->db->get('customer_category')->row();
				if(isset($customer_group_title->title))
				{
					$customer_group_title = $customer_group_title->title;
				}
				else
				{
					$customer_group_title = '';
				}
			}
			///////////////////////////////////////////////////
			///////////create list for products///////////////
			/////////////////////////////////////////////////
			$this->db->where(array('user_id' => $this->session->userdata('id')));
			$order_query = $this->db->get('orders');
			$status_order_query = $this->db->get('status_order');
			$orders_view = '';
			$file_view = '';
			$this->load->library('date_shamsi');
			$payment_query = $this->db->get('payment');
			foreach($order_query->result() as $order_row)
			{
				$product_in_cart = '';
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
									$option_json = json_decode($cat_row->options);
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
										$op_id = $op_row->id;
										foreach($status_order_query->result() as $s_row)
										{
											if($order_row->condition == $s_row->id)
											{
												if($s_row->are_virtual_products_accessible_by_customers_in_this_status == 'yes' && isset($option_json))
												{
													if(isset($option_json->$op_id))
													{
														if(isset($option_json->$op_id->file_name) && isset($option_json->$op_id->orig_name))
														{
															$modify_date = '';
															if($order_row->modify_date != null && $order_row->modify_date != 0 && $order_row->modify_date != '')
															{
																$modify_date = $this->date_shamsi->jdate('o/m/j', $order_row->modify_date,'','Asia/Tehran', 'fa');
															}
															$file_src = base_url('download/file/'.$option_json->$op_id->file_name);
															$file_view .= '<tr>
															<td>'.$option_json->$op_id->orig_name.'</td>
															<td>'.$modify_date.'</td>
															<td><a href="'.$file_src.'"><i class="fas fa-cloud-download-alt margin-right"></i></a></td>
														</tr>';
														}
													}
												}
											}
										}
									}
								}
							}
						}
						$single_product_link = base_url('pages/single_product/'.$index);
						$product_in_cart .= ' <tr>
 										 <td class="image">
                                        <a href="'.$single_product_link.'">
                                            <img src="'.$value->product_first_image_src.'">
                                        </a>
                                    </td>
 										<td>
 										<div class="mb-3">
                                            <span><b>'.$value->product_title.'</b></span>
                                        </div>
                                        '.$option_view.'
                                        </td>
                                        <td>'.$value->tedad.'</td>
                                        <td>'.number_format($value->product_price).'</td>
                                        <td>'.number_format($discount_price).'</td>
                                        <td>'.number_format($discounted_price).'</td>
                                    </tr>';
					}
					$status_order_name = '';
					$cancel_order = 'no';
					foreach($status_order_query->result() as $s_row)
					{
						if($order_row->condition == 'not_paid')
						{
							$status_order_name = 'پرداخت نشده';
							if($s_row->id == 1 && $s_row->can_customer_cancel_the_order_in_this_situation == 'yes')
							{
								$cancel_order = 'yes';
							}
						}
						elseif($order_row->condition == $s_row->id)
						{
							$status_order_name = $s_row->status_order;
							if($s_row->can_customer_cancel_the_order_in_this_situation == 'yes')
							{
								$cancel_order = 'yes';
							}
						}
					}
					$insert_date = '';
					if($order_row->insert_date != null && $order_row->insert_date != 0 && $order_row->insert_date != '')
					{
						$insert_date = $this->date_shamsi->jdate('o/m/j', $order_row->insert_date,'','Asia/Tehran', 'fa');
					}
					$modify_date = '';
					if($order_row->modify_date != null && $order_row->modify_date != 0 && $order_row->modify_date != '')
					{
						$modify_date = $this->date_shamsi->jdate('o/m/j', $order_row->modify_date,'','Asia/Tehran', 'fa');
					}
					/*$orders_view .= ' <div class="accordion_item order_item" order_id="'.$order_row->id.'">
                    <div class="w3-bar w3-dark-gray title" onclick="toggle_accordion(this);">
                        <div class="w3-bar-item w3-right"><b>تاریخ سفارش:</b> '.$insert_date.'</div>
                        <div class="w3-bar-item w3-right"> <b>کد سفارش:</b> '.$order_row->id.'</div>
                        <button class="w3-button w3-red w3-round w3-left cancel_btn" onclick="cancel_order(\'initialize\', this);">لغو سفارش</button>
                        <button class="w3-button w3-light-grey w3-round w3-left w3-hide canceled_btn">لغو شده</button>
                        <div class="w3-bar-item w3-left"><b> وضعیت سفارش:</b> '.$status_order_name.'</div>
                    </div>
                    <div class="w3-row-padding content w3-light-gray">
                        <div class="w3-col m6">
                            <div class="w3-responsive">
                                <table class="order_table w3-table w3-bordered">
                                    <tr>
                                        <th>لیست سفارشات</th>
                                        <th>تعداد</th>
                                        <th>قیمت واحد</th>
                                        <th>تخفیف</th>
                                        <th>قیمت نهایی</th>
                                    </tr>
                                    '.$product_in_cart.'
                                     <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>جمع سفارش</b></td>
                                        <td>'.$order_row->sum_of_prices_for_selected_products.'</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="w3-col m6 w3-leftttt">
                            <ul class="w3-ul">
                                <li><b>نحوه ارسال:</b> '.lang($order_row->shipping_method).'</li>
                                <li><b>تاریخ تغییر وضعیت:</b> '.$modify_date.'</li>
                                <li><b>آدرس:</b> '.$order_row->address_country.'، '.$order_row->address_state.'، '.$order_row->address_city.'، '.$order_row->address.'</li>
                                <li><b>تحویل گیرنده:</b> '.$order_row->address_first_name.' '.$order_row->address_last_name.'</li>
                            </ul>
                        </div>
                    </div>
                </div>';*/
					$payment_name = '';
					foreach($payment_query->result() as $pay_row)
					{
						if($pay_row->id == $order_row->payment)
						{
							$payment_name = $pay_row->title;
						}
					}
                    $orders_view .= ' <div class="accordion_item order_item" order_id="'.$order_row->id.'">
                    <div class="w3-bar w3-dark-gray title" onclick="toggle_accordion(this);">
                        <div class="w3-bar-item w3-right"><b>تاریخ سفارش:</b> '.$insert_date.'</div>
                        <div class="w3-bar-item w3-right"> <b>کد سفارش:</b> '.$order_row->id.'</div>
                       '.($cancel_order == 'yes' ? '<button class="w3-button w3-red w3-round w3-left cancel_btn" onclick="cancel_order(\'initialize\', this);">لغو سفارش</button>' : "").'
                        <button class="w3-button w3-light-grey w3-round w3-left w3-hide canceled_btn">لغو شده</button>
                        <div class="w3-bar-item w3-left"><b> وضعیت سفارش:</b> '.$status_order_name.'</div>
                    </div>
                    <div class="content w3-light-gray">

						<div class="w3-row-padding w3-margin-bottom">
							<div class="w3-responsive w3-container">
								<table class="order_table w3-table w3-bordered">
									<tr>
										<th>تصویر محصول</th>
										<th  class="description_cart">شرح سفارش</th>
										<th>تعداد</th>
										<th>قیمت واحد(تومان)</th>
										<th>تخفیف(تومان)</th>
										<th>قیمت نهایی(تومان)</th>
									</tr>
									'.$product_in_cart.'
					
									<tr>
										<td class="w3-left-align" colspan="5"><b>مبلغ سفارش(تومان)</b></td>
										<td><b class="sum_of_prices_for_selected_products" price="'.$order_row->sum_of_prices_for_selected_products.'">'.number_format($order_row->sum_of_prices_for_selected_products).'</b></td>
									</tr>
									<tr>
										<td class="w3-left-align" colspan="5"><b>هزینه ارسال سفارش(تومان)</b></td>
										<td><b class="final_shipping_price" price="'.$order_row->shipping_price.'">'.number_format($order_row->shipping_price).'</b></td>
									</tr>
									<tr>
										<td class="w3-left-align" colspan="5"><b>جمع کل سفارش(تومان)</b></td>
										<td><b class="order_total_sum">'.number_format($order_row->order_total_sum).'</b></td>
									</tr>
								</table>
							</div>
						</div>
						<div class="w3-row-padding cart_details">
							<div class="w3-col m6">
								<div><i class="fas fa-user w3-margin w3-large"></i>تحویل گیرنده: <span class="order_receiver">'.$order_row->address_first_name.' '.$order_row->address_last_name.'</span></div>
								<div><i class="fas fa-mobile-alt w3-margin w3-large"></i>شماره تماس: <span class="receiver_contact_number">'.$order_row->address_mobile.'</span></div>
								<div><i class="fas fa-map-marker-alt w3-margin w3-large"></i> آدرس: <span class="receiver_address">'.$order_row->address_country.'، '.$order_row->address_state.'، '.$order_row->address_city.'، '.$order_row->address.'</span></div>
							</div>
							<div class="w3-col m6">
								<div><i class="fas fa-truck w3-margin w3-large"></i>نحوه ارسال: <span class="order_sending_type">'.lang($order_row->shipping_method).'</span></div>
								<div><i class="fas fa-credit-card w3-margin w3-large"></i>نحوه پرداخت: <span class="order_payment_type">'.$payment_name.'</span></div>
							</div>
						</div>
					
					</div>
                </div>';
				}
			}

			$data = array(
				'first_name' => $first_name,
				'last_name' => $last_name,
				'username' => $username,
				'email' => $email,
				'day' => $day,
				'month' => $month,
				'year' => $year,
				'customer_group' => $customer_group_title,
				'mobile' => $mobile,
				'address_view' => $address_view,
				'view_favorite' => $view_favorite,
				'add_new_address' => $add_new_address,
				'orders_view' => $orders_view,
				'file_view' => $file_view
			);
		}

		else
		{
			$data = array(
				'first_name' => '',
				'last_name' => '',
				'username' => '',
				'email' => '',
				'day' => '',
				'month' => '',
				'year' => '',
				'customer_group' => '',
				'mobile' => '',
				'address_view' => '',
				'view_favorite' => '',
				'add_new_address' => '',
				'orders_view' => '',
				'file_view' => ''
			);
		}

		$content = $this->load->view('pages/profile', $data, true);
		$position_out = $this->mylib->replace_modules_in_position($content);
		$this->load->view('template/header');
		$this->output->append_output($position_out['html_content']);
		$this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
	}

	public function edit()
	{
		$this->session->set_userdata('page_title', 'ویرایش مشخصات');
		$in_progress = $this->input->post('in_progress');
		if(!isset($in_progress))
		{
            $this->mylib->is_login();
		}

		//create list for date of birthday
		$data_day = '';
		$data_month = '';
		$data_year = '';

		$day = 0;
		$month = 0;
		$year = 0;

		$list_day = array();
		$list_month = array();
		$list_year = array();

		//create list for day of birthday
		for($i = 1; $i <= 31; $i++)
		{
			array_push($list_day, $i);
		}
		$get_inlist_day = 'in_list['.implode(",",$list_day).']';

		//create list for month of birthday
		for($i = 1; $i <= 12; $i++)
		{
			array_push($list_month, $i);
		}
		$get_inlist_month = 'in_list['.implode(",",$list_month).']';

		//create list for year of birthday
		$time = time();

		$this->load->library('date_shamsi');

		if($year != null)
		{
			$this_year = $year;
		}
		else
		{
			$this_year = $this->date_shamsi->jdate('o', $time,'','Asia/Tehran', 'en');
		}

		for($i = $this_year; $i > 1300; $i--)
		{
			array_push($list_year, $i);
		}
		$get_inlist_year = 'in_list['.implode(",",$list_year).']';

		$is_login = 'no';
		if($this->session->userdata('id') != null)
		{
			$is_login = 'yes';
		}

		if($is_login == 'yes' || isset($in_progress))
		{
			///////////////////////////////////////////////////////////
			$temp_first_name = $this->input->post('first_name');
			$day1 = $this->input->post('day');
			$month1 = $this->input->post('month');
			$year1 = $this->input->post('year');
			$email1 = $this->input->post('email');
			$mobile1 = $this->input->post('mobile');
			$password1 = $this->input->post('password');

			if (isset($temp_first_name))
			{
				$this->load->model('customer_category');

				$item_id = $this->session->userdata('id');
				$posted_username = $this->input->post('username');
				$posted_email = $this->input->post('email');
				$query = $this->db->get('customer');
				$item_id_username = '';
				$item_id_email = '';
				foreach ($query->result() as $row)
				{
					if ($item_id == $row->id)
					{
						$item_id_username = $row->username;
						$item_id_email = $row->email;

						break;
					}
				}

				$this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required|min_length[2]|max_length[50]');

				$this->form_validation->set_rules('last_name', lang('last_name'), 'trim|required|min_length[3]|max_length[50]');

				if(!isset($in_progress) || $is_login == 'no')
				{
					if(is_numeric($posted_username))
					{
                        $this->session->set_userdata('type_of_username_for_edit_username', 'mobile');
						$set_rules_for_username = '|exact_length[11]|is_natural';
					}
					else
					{
                        $this->session->set_userdata('type_of_username_for_edit_username', 'email');
						$set_rules_for_username = '|valid_email|max_length[50]';
					}
					$this->form_validation->set_rules('username', lang('username'), 'trim|required|callback__username_check'.$set_rules_for_username.($item_id==null || $posted_username!=$item_id_username ? "|is_unique[customer.username]" :""));
					$this->form_validation->set_message('_username_check', 'فیلد «{field}» تنها میتواند شامل تمامی کاراکترهای مجاز ایمیل (کاراکترهای الفبای انگلیسی بعلاوه @ و . و _) یا تمامی کاراکترهای مجاز تلفن بین‌الملل (اعداد انگلیسی به تعداد 11 عدد) باشد, همچنین درج فاصله (Space) مجاز نمیباشد.');
					if(is_numeric($posted_username))
					{
                        $this->form_validation->set_message('exact_length[11]', 'فیلد «{field}» تنها میتواند شامل تمامی کاراکترهای مجاز ایمیل (کاراکترهای الفبای انگلیسی بعلاوه @ و . و _) یا تمامی کاراکترهای مجاز تلفن بین‌الملل (اعداد انگلیسی به تعداد 11 عدد) باشد, همچنین درج فاصله (Space) مجاز نمیباشد.');
                        $this->form_validation->set_message('is_natural', 'فیلد «{field}» تنها میتواند شامل تمامی کاراکترهای مجاز ایمیل (کاراکترهای الفبای انگلیسی بعلاوه @ و . و _) یا تمامی کاراکترهای مجاز تلفن بین‌الملل (اعداد انگلیسی به تعداد 11 عدد) باشد, همچنین درج فاصله (Space) مجاز نمیباشد.');
                    }
                    else
					{
                        $this->form_validation->set_message('valid_email', 'فیلد «{field}» تنها میتواند شامل تمامی کاراکترهای مجاز ایمیل (کاراکترهای الفبای انگلیسی بعلاوه @ و . و _) یا تمامی کاراکترهای مجاز تلفن بین‌الملل (اعداد انگلیسی به تعداد 11 عدد) باشد, همچنین درج فاصله (Space) مجاز نمیباشد.');
					}
				}

				if(isset($day1) || isset($month1) || isset($year1))
				{
					$this->form_validation->set_rules('day', lang('day'), 'required|'.$get_inlist_day);

					$this->form_validation->set_rules('month', lang('month'), 'required|'.$get_inlist_month);

					$this->form_validation->set_rules('year', lang('year'), 'required|'.$get_inlist_year);
				}

				$this->form_validation->set_rules('sex', lang('sex'), 'required|in_list[man,others,female]');

				$this->form_validation->set_rules('customer_group', lang('customer_group'),'required|'. $this->customer_category->get_inlist_string());

				if($mobile1 == '' && $email1 == '')
				{
					$this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|min_length[6]|max_length[50]|required'.($item_id==null || $posted_email!=$item_id_email ? "|is_unique[customer.email]" :""), array('required' => 'حداقل یکی از فیلدهای ایمیل یا موبایل را باید تکمیل کنید.'));
				}
				else
				{
					$this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|min_length[6]|max_length[50]'.($item_id==null || $posted_email!=$item_id_email ? "|is_unique[customer.email]" :""));
				}

				$this->form_validation->set_rules('mobile', lang('mobile'),'trim|exact_length[11]|is_natural');

				$required_password = '';
				if(isset($in_progress) && $is_login == 'no')
				{
					$required_password = '|required';
				}
				$this->form_validation->set_rules('password', lang('password'), 'trim|min_length[5]|max_length[50]'.$required_password);

				if(isset($password1) && $password1 != '')
				{
					$this->form_validation->set_rules('confirm_password', lang('confirm_password'), 'trim|matches[password]|required|min_length[5]|max_length[50]');
				}

				if ($this->form_validation->run() == TRUE)
				{
					if(!isset($in_progress) && $posted_username != $item_id_username)
					{
                        $this->session->set_userdata('first_name_edit_profile', $this->input->post('first_name'));
						$this->session->set_userdata('last_name_edit_profile', $this->input->post('last_name'));
						$this->session->set_userdata('username_edit_profile', $this->input->post('username'));
						$this->session->set_userdata('day_edit_profile', $this->input->post('day'));
						$this->session->set_userdata('month_edit_profile', $this->input->post('month'));
						$this->session->set_userdata('year_edit_profile', $this->input->post('year'));
						$this->session->set_userdata('sex_edit_profile', $this->input->post('sex'));
						$this->session->set_userdata('email_edit_profile', $this->input->post('email'));
						$this->session->set_userdata('mobile_edit_profile', $this->input->post('mobile'));
						$this->session->set_userdata('customer_group_edit_profile', $this->input->post('customer_group'));
						if($password1 != '')
						{
                            $this->session->set_userdata('password_edit_profile', md5($this->input->post('password')));
						}

                        $code_for_edit_username = substr(str_shuffle("0123456789"), 0, 6);
                        $this->session->set_userdata('code_for_edit_username', $code_for_edit_username);

                        if($this->session->has_userdata('number_of_resend_code_for_edit_username'))
                        {
                            $number_of_resend_code_for_edit_username = $this->session->userdata('number_of_resend_code_for_edit_username') + 1;
                        }
                        else
                        {
                            $number_of_resend_code_for_edit_username = 1;
                        }
                        $this->session->set_userdata('number_of_resend_code_for_edit_username', $number_of_resend_code_for_edit_username);

                        $time_waiting = 15;
                        if($number_of_resend_code_for_edit_username >= 5)
                        {
                            if($this->session->has_userdata('waiting_time_to_send_code_for_edit_username'))
                            {
                                if(time() >= $this->session->userdata('waiting_time_to_send_code_for_edit_username'))
                                {
                                    $end_time_waiting = 'yes';
                                }
                                else
                                {
                                    $time_waiting = $this->session->userdata('waiting_time_to_send_code_for_edit_username') - time();
                                    $time_waiting = $time_waiting / 60;
                                    $time_waiting = round($time_waiting,0,PHP_ROUND_HALF_UP);
                                }
                            }
                            else
                            {
                                $this->session->set_userdata('waiting_time_to_send_code_for_edit_username', time() + 900);
                            }
                        }

                        if($number_of_resend_code_for_edit_username < 5 || isset($end_time_waiting))
                        {
                            $this->db->select('system_email,subject_for_confirmation_code_email,message_text_for_confirmation_code_email,message_text_for_confirmation_code_sms');
                            $this->db->where(array('id' => 1));
                            $setting_query = $this->db->get('setting');
                        	if($this->session->userdata('type_of_username_for_edit_username') == 'email')
                            {
                                foreach($setting_query->result() as $setting_row)
                                {
                                    $message = str_replace("{{code}}", $code_for_edit_username, $setting_row->message_text_for_confirmation_code_email);
                                    $message = str_replace("{{usage_title}}", 'درخواست تغییر نام کاربری', $message);
                                    $subject = str_replace("{{usage_title}}", 'درخواست تغییر نام کاربری', $setting_row->subject_for_confirmation_code_email);
                                    $this->load->library('email_lib');
                                    $this->email_lib->send_email($setting_row->system_email, $posted_username, $subject, $message);
                                }
                            }
                            else if ($this->session->userdata('type_of_username_for_edit_username') == 'mobile')
                            {
                                foreach($setting_query->result() as $setting_row)
                                {
                                    $message = str_replace("{{code}}", $code_for_edit_username, $setting_row->message_text_for_confirmation_code_sms);
                                    $message = str_replace("{{usage_title}}", 'درخواست تغییر نام کاربری', $message);
                                    $this->load->library('sms');
                                    $this->sms->send_sms($posted_username, $message);
                                }
                            }
                        }

                        if($number_of_resend_code_for_edit_username < 5)
                        {
                            $this->session->set_userdata('life_time_code_for_edit_username', time() + 120);
                        }
						elseif($number_of_resend_code_for_edit_username >= 5 && isset($end_time_waiting))
                        {
                            $this->session->unset_userdata('number_of_resend_code_for_edit_username');
                            $this->session->set_userdata('life_time_code_for_edit_username', time() + 300);
                        }
                        else
                        {
                            $this->mylib->set_error("به دلیل درخواستهای مکرر شما تا $time_waiting دقیقه امکان تغییر نام کاربری خود را ندارید.");
                        }

						redirect(base_url('profile/confirm_code'));
					}

					if($password1 != '')
					{
						$dadeh = array
						(
							'first_name' => $this->input->post('first_name'),
							'last_name' => $this->input->post('last_name'),
							'username' => $this->input->post('username'),
							'day' => $this->input->post('day'),
							'month' => $this->input->post('month'),
							'year' => $this->input->post('year'),
							'sex' => $this->input->post('sex'),
							'email' => $this->input->post('email'),
							'mobile' => $this->input->post('mobile'),
							'customer_group' => $this->input->post('customer_group'),
							'password' => md5($this->input->post('password'))
						);
					}
					else
					{
						$dadeh = array
						(
							'first_name' => $this->input->post('first_name'),
							'last_name' => $this->input->post('last_name'),
							'username' => $this->input->post('username'),
							'day' => $this->input->post('day'),
							'month' => $this->input->post('month'),
							'year' => $this->input->post('year'),
							'sex' => $this->input->post('sex'),
							'email' => $this->input->post('email'),
							'customer_group' => $this->input->post('customer_group'),
							'mobile' => $this->input->post('mobile')
						);
						if(isset($in_progress) && $is_login == 'yes')
						{
							$dadeh = array
							(
								'first_name' => $this->input->post('first_name'),
								'last_name' => $this->input->post('last_name'),
								'day' => $this->input->post('day'),
								'month' => $this->input->post('month'),
								'year' => $this->input->post('year'),
								'sex' => $this->input->post('sex'),
								'email' => $this->input->post('email'),
								'customer_group' => $this->input->post('customer_group'),
								'mobile' => $this->input->post('mobile')
							);
						}
					}

					$this->load->model('customer_model');
					if(isset($in_progress) && $is_login == 'no')
					{
						$customer_id = $this->customer_model->insert($dadeh);
						if(isset($customer_id) && is_numeric($customer_id))
						{
							$session_data = array (
								'id' => $customer_id,
								'session_lifetime' => 2*60*60 + time()
							);
							$this->session->set_userdata($session_data);
							$response = array("status" => "successful", "message" => "فرم با موفقیت ثبت شد.");
							echo json_encode($response, JSON_UNESCAPED_UNICODE);
							return true;
						}
						else
						{
							$response = array("status" => "unsuccessful", "message" => "فرم مورد نظر ذخیره نشد.");
							echo json_encode($response, JSON_UNESCAPED_UNICODE);
							return true;
						}
					}
					else
					{
						$this->db->where('id',$item_id);
						$temp_update = $this->db->update('customer', $dadeh);
						if(isset($in_progress) && $temp_update == true)
						{
							$response = array("status" => "successful", "message" => "فرم با موفقیت ثبت شد.");
							echo json_encode($response, JSON_UNESCAPED_UNICODE);
							return true;
						}
						elseif(isset($in_progress) && $temp_update != true)
						{
							$response = array("status" => "unsuccessful", "message" => "فرم مورد نظر ذخیره نشد.");
							echo json_encode($response, JSON_UNESCAPED_UNICODE);
							return true;
						}
						if(!isset($in_progress))
						{
							$this->mylib->set_success("فرم با موفقیت ثبت شد.");
						}
					}
				}
				else
				{
                    if(isset($in_progress))
					{
                        $response = array("status" => "unsuccessful", "message" => validation_errors());
                        echo json_encode($response, JSON_UNESCAPED_UNICODE);
                        return true;
					}
				}
			}

			$this->db->where('id', $this->session->userdata('id'));
			$query = $this->db->get('customer');

			$first_name = '';
			$last_name = '';
			$username = '';
			$email = '';
			$sex = '';
			$mobile = '';
			$customer_group = '';
			foreach ($query->result() as $row)
			{
				$first_name = $row->first_name;
				$last_name = $row->last_name;
				$username = $row->username;
				$email = $row->email;
				$sex = $row->sex;
				$mobile = $row->mobile;
				$day = $row->day;
				$month = $row->month;
				$year = $row->year;
				$customer_group = $row->customer_group;
			}

			for($i = 1; $i <= 31; $i++)
			{
				$data_day .= '<option value="'.$i.'" '.set_select('day', $i, ($i == $day ? true : false)).'>'.$i.'</option>';
			}

			$month_array = array('1' => 'فروردین', '2' => 'اردیبهشت', '3' => 'خرداد', '4' => 'تیر', '5' => 'مرداد', '6' => 'شهریور', '7' => 'مهر', '8' => 'ابان', '9' => 'اذر', '10' => 'دی', '11' => 'بهمن', '12' => 'اسفند');

			for($i = 1; $i <= 12; $i++)
			{
				$data_month .= '<option value="'.$i.'" '.set_select('month', $i, ($i == $month ? true : false)).'>'.$month_array[$i].'</option>';
			}

			for($i = $this_year; $i > 1300; $i--)
			{
				$data_year .= '<option value="'.$i.'" '.set_select('year', $i, ($i == $year ? true : false)).'>'.$i.'</option>';
			}

			//create customer group list
			$this->db->select('id,title');
			$this->db->where(array('publish' => 'yes'));
			$category_list = $this->db->get('customer_category');
			$customer_group_list = '';

			if (count($category_list) > 0)
			{
				$customer_group_id = $customer_group;
				foreach ($category_list->result() as $row)
				{
					$customer_group_list .= '<option value="' . $row->id . '" ' . set_select('parent', $row->id, ($row->id == $customer_group_id ? true : false)) . '>' . $row->title . '</option>';
				}
			}

			if ($customer_group_list == '')
			{
				$customer_group_list = "<option value=''>" . lang('no_category') . "</option>";
			}

			$data = array(
				'first_name' => $first_name,
				'last_name' => $last_name,
				'username' => $username,
				'email' => $email,
				'sex' => $sex,
				'mobile' => $mobile,
				'day' => $data_day,
				'month' => $data_month,
				'year' => $data_year,
				'customer_group' => $customer_group_list
			);
		}
	else
		{
			$data = array(
				'first_name' => '',
				'last_name' => '',
				'username' => '',
				'email' => '',
				'day' => $data_day,
				'month' => $data_month,
				'year' => $data_year,
				'customer_group' => '',
				'mobile' => '',
				'sex' => ''
			);
		}

		$data['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$content = $this->load->view('pages/profile_edit', $data, true);
		$position_out = $this->mylib->replace_modules_in_position($content);
		$this->load->view('template/header');
		$this->output->append_output($position_out['html_content']);
		$this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
	}

	public function confirm_code()
	{
		if($this->session->has_userdata('life_time_code_for_edit_username'))
		{
            //confirm code for edit username
            $this->session->set_userdata('expire_time_code_for_edit_username', $this->session->userdata('life_time_code_for_edit_username') - time());
            $posted_code_for_edit_username = $this->input->post('code_for_edit_username');
            if($posted_code_for_edit_username != '' && $posted_code_for_edit_username != null)
            {
                $code_for_edit_username = $this->session->userdata('code_for_edit_username');
                if($posted_code_for_edit_username == $code_for_edit_username)
                {
                    $dadeh = array
                    (
                        'first_name' => $this->session->userdata('first_name_edit_profile'),
                        'last_name' => $this->session->userdata('last_name_edit_profile'),
                        'username' => $this->session->userdata('username_edit_profile'),
                        'day' => $this->session->userdata('day_edit_profile'),
                        'month' => $this->session->userdata('month_edit_profile'),
                        'year' => $this->session->userdata('year_edit_profile'),
                        'sex' => $this->session->userdata('sex_edit_profile'),
                        'email' => $this->session->userdata('email_edit_profile'),
                        'mobile' => $this->session->userdata('mobile_edit_profile'),
                        'customer_group' => $this->session->userdata('customer_group_edit_profile')
                    );
                    if($this->session->userdata('password_edit_profile') != '' && $this->session->userdata('password_edit_profile') != null)
                    {
                        $dadeh['password'] = $this->session->userdata('password_edit_profile');
                        $this->session->unset_userdata('password_edit_profile');
                    }

                    $this->session->unset_userdata('first_name_edit_profile');
                    $this->session->unset_userdata('last_name_edit_profile');
                    $this->session->unset_userdata('username_edit_profile');
                    $this->session->unset_userdata('day_edit_profile');
                    $this->session->unset_userdata('month_edit_profile');
                    $this->session->unset_userdata('year_edit_profile');
                    $this->session->unset_userdata('sex_edit_profile');
                    $this->session->unset_userdata('email_edit_profile');
                    $this->session->unset_userdata('mobile_edit_profile');
                    $this->session->unset_userdata('customer_group_edit_profile');
                    $this->session->unset_userdata('number_of_resend_code_for_edit_username');
                    $this->session->unset_userdata('waiting_time_to_send_code_for_edit_username');
                    $this->session->unset_userdata('type_of_username_for_edit_username');
                    $this->session->unset_userdata('expire_time_code_for_edit_username');
                    $this->session->unset_userdata('code_for_edit_username');
                    $this->session->unset_userdata('life_time_code_for_edit_username');

                    $this->db->where('id',$this->session->userdata('id'));
                    $this->db->update('customer', $dadeh);

                    $this->mylib->set_success(lang('success_msg'), 'profileeditsuccess_msg');
                    redirect(base_url('profile/edit'));
                }
                else
                {
                    $this->mylib->set_error('کد وارد شده اشتباه است.');
                }
            }

            $data = array('page_name' => 'تایید کد');
            $data['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
            $content = $this->load->view('pages/profile_confirm_code', $data, true);
            $position_out = $this->mylib->replace_modules_in_position($content);
            $this->load->view('template/header');
            $this->output->append_output($position_out['html_content']);
            $this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
		}
	}

	public function edit_address($item_id = null)
	{
		$this->session->set_userdata('page_title', 'ویرایش آدرس');
		$in_progress = $this->input->post('in_progress');
        if(!isset($in_progress))
        {
            $this->mylib->is_login();
        }

		if($this->session->userdata('id') != null)
		{
			$this->load->model('localization/regions');
			$address_title = $this->input->post('address_title');
			if(isset($address_title))
			{
				$this->form_validation->set_rules('address_title', lang('title'),'trim|required|min_length[3]|max_length[50]');

				$this->form_validation->set_rules('address_first_name', lang('first_name'),'trim|required|min_length[2]|max_length[50]');

				$this->form_validation->set_rules('address_last_name', lang('last_name'),'trim|required|min_length[3]|max_length[50]');

				$this->form_validation->set_rules('address_country', lang('country'),'required|'. $this->regions->get_countries_inlist_string());
				$this->form_validation->set_rules('address_state', lang('state'),'required|'. $this->regions->get_state_inlist_string());

				$this->form_validation->set_rules('address_city', lang('city'),'required|max_length[50]|'. $this->regions->get_city_inlist_string());

				$this->form_validation->set_rules('address', lang('address'),'trim|required|min_length[5]|max_length[1000]');

				$this->form_validation->set_rules('address_postcode', lang('postcode'),'trim|exact_length[10]|is_natural');

				$this->form_validation->set_rules('address_mobile', lang('mobile'),'trim|required|exact_length[11]|is_natural');

				$this->form_validation->set_rules('address_tel', lang('tel'),'trim|min_length[3]|max_length[12]|is_natural');
			}

			if ($this->form_validation->run() == TRUE)
			{
				$this->db->where(array('id' => $this->session->userdata('id')));
				$this->db->select('address');
				$temp_address = $this->db->get('customer')->row();
				$temp_address = $temp_address->address;

				$temp_address_json = json_decode($temp_address);
				foreach ($temp_address_json as $json_index => $json_row)
				{
					if ($json_index == $item_id)
					{
						$json_row->address_title = $this->input->post('address_title');
						$json_row->address_first_name = $this->input->post('address_first_name');
						$json_row->address_last_name = $this->input->post('address_last_name');
						$json_row->address_mobile = $this->input->post('address_mobile');
						$json_row->address_tel = $this->input->post('address_tel');
						$json_row->address_postcode = $this->input->post('address_postcode');
						$json_row->address_country = $this->input->post('address_country');
						$json_row->address_state =  $this->input->post('address_state');
						$json_row->address_city = $this->input->post('address_city');
						$json_row->address = $this->input->post('address');

						break;
					}
				}
				$temp_address_json = json_encode($temp_address_json);

				$dadeh = array(
					'address' => $temp_address_json
				);

				$this->db->where('id', $this->session->userdata('id'));
				$temp_update = $this->db->update('customer', $dadeh);

				if(isset($in_progress) && $temp_update == true)
				{
					$response = array("status" => "successful", "message" => "فرم با موفقیت ثبت شد.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return true;
				}
				elseif(isset($in_progress) && $temp_update != true)
				{
					$response = array("status" => "unsuccessful", "message" => "فرم مورد نظر ذخیره نشد.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return true;
				}

				$task = $this->input->post('task');
				if($task == "save_and_close")
				{
					redirect(base_url("profile"));
				}

				if ($task == "save")
				{
					//Go to Paretn Page
					redirect(base_url("profile/edit_address/".$item_id));
				}
			}
			else
			{
				if(isset($in_progress))
				{
					$response = array("status" => "unsuccessful", "message" => validation_errors());
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return true;
				}
			}

            //show information of data base
			$this->db->where(array('id' => $this->session->userdata('id')));
			$this->db->select('address');
			$temp_address = $this->db->get('customer')->row();
			if(isset($temp_address->address))
			{
				$temp_address = $temp_address->address;
				$temp_address_json = json_decode($temp_address);
			}
			$first_name = '';
			$last_name = '';
			$mobile = '';
			$tel = '';
			$postcode = '';
			$country_name = $this->regions->get_countries_as_html_option();
			$state_name = $this->regions->get_states_as_html_option();
			$city_name = $this->regions->get_cities_as_html_option(null, $this->input->post('address_state'));
			$address = '';
			if(isset($temp_address_json))
			{
				foreach ($temp_address_json as $json_index => $json_row)
				{
					if($json_index == $item_id)
					{
						$address_title = $json_row->address_title;
						$first_name = $json_row->address_first_name;
						$last_name = $json_row->address_last_name;
						$mobile = $json_row->address_mobile;
						$tel = $json_row->address_tel;
						$postcode = $json_row->address_postcode;
						$country_id = $json_row->address_country;
						$state_id = $json_row->address_state;
						$city_id = $json_row->address_city;
						$address = $json_row->address;
						$country_name = $this->regions->get_countries_as_html_option($country_id);
						$state_name = $this->regions->get_states_as_html_option($state_id);
						$city_name = $this->regions->get_cities_as_html_option($city_id, $state_id);
						break;
					}
				}
			}

			$data = array(
				'id_address' => $item_id,
				'address_title' => $address_title,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'mobile' => $mobile,
				'tel' => $tel,
				'postcode' => $postcode,
				'country_name' => $country_name,
				'state_name' => $state_name,
				'city_name' => $city_name,
				'complete_address' => $address
			);
		}

		else
		{
			$data = array(
				'id_address' => '',
				'address_title' => '',
				'first_name' => '',
				'last_name' => '',
				'mobile' => '',
				'tel' => '',
				'postcode' => '',
				'country_name' => '',
				'state_name' => '',
				'city_name' => '',
				'address' => ''
			);
		}

		$data['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['is_new_address'] = false;

		$content = $this->load->view('pages/address_form', $data, true);
		$position_out = $this->mylib->replace_modules_in_position($content);
		$this->load->view('template/header');
		$this->output->append_output($position_out['html_content']);
		$this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
	}

	public function new_address()
	{
		$this->session->set_userdata('page_title', 'افزودن آدرس جدید');
		$in_progress = $this->input->post('in_progress');
		if(!isset($in_progress))
		{
            $this->mylib->is_login();
		}

		$item_id = 0;
		$is_login = 'no';
		if($this->session->userdata('id') != null)
		{
			$is_login = 'yes';
		}
		if($is_login == 'yes' || isset($in_progress))
		{
			$this->load->model('localization/regions');
			$address_title = $this->input->post('address_title');
			if(isset($address_title))
			{
				$this->form_validation->set_rules('address_title', lang('title'),'trim|required|min_length[3]|max_length[50]');

				$this->form_validation->set_rules('address_first_name', lang('first_name'),'trim|required|min_length[2]|max_length[50]');

				$this->form_validation->set_rules('address_last_name', lang('last_name'),'trim|required|min_length[3]|max_length[50]');

				$this->form_validation->set_rules('address_country', lang('country'),'required|'. $this->regions->get_countries_inlist_string());
				$this->form_validation->set_rules('address_state', lang('state'),'required|'. $this->regions->get_state_inlist_string());

				$this->form_validation->set_rules('address_city', lang('city'),'required|max_length[50]|'. $this->regions->get_city_inlist_string());

				$this->form_validation->set_rules('address', lang('address'),'trim|required|min_length[5]|max_length[1000]');

				$this->form_validation->set_rules('address_postcode', lang('postcode'),'trim|exact_length[10]|is_natural');

				$this->form_validation->set_rules('address_mobile', lang('mobile'),'trim|required|exact_length[11]|is_natural');

				$this->form_validation->set_rules('address_tel', lang('tel'),'trim|min_length[3]|max_length[12]|is_natural');
			}

			if ($this->form_validation->run() == TRUE)
			{
				$this->db->where(array('id' => $this->session->userdata('id')));
				$this->db->select('address');
				$temp_address = $this->db->get('customer')->row();
				$temp_address_json = '';
				if(isset($temp_address->address))
				{
					$temp_address = $temp_address->address;
					$temp_address_json = json_decode($temp_address);
					$temp_address_array = array(
						'address_title' => $this->input->post('address_title'),
						'address_first_name' => $this->input->post('address_first_name'),
						'address_last_name' => $this->input->post('address_last_name'),
						'address_mobile' => $this->input->post('address_mobile'),
						'address_tel' => $this->input->post('address_tel'),
						'address_postcode' => $this->input->post('address_postcode'),
						'address_country' => $this->input->post('address_country'),
						'address_state' =>  $this->input->post('address_state'),
						'address_city' => $this->input->post('address_city'),
						'address' => $this->input->post('address')
					);
					if(is_array($temp_address_json))
					{
						array_push($temp_address_json, $temp_address_array);
						$item_id = count($temp_address_json) - 1;
						$temp_address_json = json_encode($temp_address_json);
					}
					else
					{
						$temp_address_json = array();
						array_push($temp_address_json, $temp_address_array);
						$item_id = 0;
						$temp_address_json = json_encode($temp_address_json);
					}
				}

				if($is_login == 'yes')
				{
					$dadeh = array(
						'address' => $temp_address_json
					);
					$this->db->where('id', $this->session->userdata('id'));
					$temp_update = $this->db->update('customer', $dadeh);

					if(isset($in_progress) && $temp_update == true)
					{
						$response = array("status" => "successful", "message" => "فرم با موفقیت ثبت شد.", "new_address" => $item_id);
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return true;
					}
					elseif(isset($in_progress) && $temp_update != true)
					{
						$response = array("status" => "unsuccessful", "message" => "فرم مورد نظر ذخیره نشد.");
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return true;
					}
				}
				else
				{
					if(isset($in_progress))
					{
						$response = array("status" => "successful", "message" => "فرم با موفقیت ثبت شد.", "new_address" => $item_id);
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return true;
					}
				}

				$task = $this->input->post('task');
				if($task == "save_and_close")
				{
					redirect(base_url("profile"));
				}

				if ($task == "save")
				{
					//Go to Paretn Page
					redirect(base_url("profile/edit_address/".$item_id));
				}
			}
			else
			{
				if(isset($in_progress))
				{
					$response = array("status" => "unsuccessful", "message" => validation_errors());
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return true;
				}
			}

			$first_name = '';
			$last_name = '';
			$mobile = '';
			$tel = '';
			$postcode = '';
			$country_name = $this->regions->get_countries_as_html_option();
			$state_name = $this->regions->get_states_as_html_option();
			$city_name = $this->regions->get_cities_as_html_option(null, $this->input->post('address_state'));
			$address = '';

			$data = array(
				'address_title' => $address_title,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'mobile' => $mobile,
				'tel' => $tel,
				'postcode' => $postcode,
				'country_name' => $country_name,
				'state_name' => $state_name,
				'city_name' => $city_name,
				'complete_address' => $address
			);
		}

		else
		{
			$data = array(
				'address_title' => '',
				'first_name' => '',
				'last_name' => '',
				'mobile' => '',
				'tel' => '',
				'postcode' => '',
				'country_name' => '',
				'state_name' => '',
				'city_name' => '',
				'address' => ''
			);
		}

		$data['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['is_new_address'] = true;

		$content = $this->load->view('pages/address_form', $data, true);
		$position_out = $this->mylib->replace_modules_in_position($content);
		$this->load->view('template/header');
		$this->output->append_output($position_out['html_content']);
		$this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
	}

	function _username_check($str)
	{
		$Valid_character_list = array('.', '_', '@');

		if(!ctype_alnum(str_replace($Valid_character_list, '', $str))) {
			return FALSE;
		}

		return true;
	}

}
?>
