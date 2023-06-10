<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->session->set_userdata('last_activity', time());
	}

	public function index()
	{
		/*$this->mylib->is_login();*/

		/*$task = $this->input->get('task');
		if(isset($task))
		{
			if ($task == "add")
			{
				$new_attribute = $this->input->get('attr');
				$id = $this->input->get('id');
				if ($this->session->has_userdata('cart'))
				{
					$current_cart = $this->session->userdata('cart');
					if (!empty($current_cart) && isset($current_cart[$id]))
					{
						$temp_current_cart = $current_cart[$id];
						foreach ($new_attribute as $index1 => $value1)
						{
							$to_cart_ghabli_nabode = true;
							foreach($temp_current_cart as $index2 => $value2)
							{
								if ($index1 == $index2)
								{
									$temp_current_cart[$index2] = $new_attribute[$index1];
									$to_cart_ghabli_nabode = false;
								}
							}

							if ($to_cart_ghabli_nabode)
							{
								$temp_current_cart[$index1] = $value1;
							}
						}
						//amadeye zakhire sazi dar session mishavad
						$current_cart[$id] = $temp_current_cart;
					}
					else
					{
						//baraye in mahsol ghablan chizi dar sabade_kharid zakhire nashode ast banabarin in yek darkhaste jadid ast
						$current_cart[$id] = $new_attribute;
					}
					$this->session->set_userdata('cart', $current_cart);
				}
				else
				{
					//ghablan hich chizi dar sabade_kharid zakhire nashode bode ast
					$current_cart[$id] = $new_attribute;
					$this->session->set_userdata('cart', $current_cart);
				}
			}

			if($task == "delete_all")
			{
				$this->session->unset_userdata('cart');
			}

			if ($task == "delete_item" && $this->session->has_userdata('cart'))
			{
				$id = $this->input->get('id');
				$temp = $this->session->userdata('cart');
				$temp2 = array();

				foreach($temp as $index => $value)
				{
					if ($id == $index)
					{
						//do nothing
					}
					else
					{
						$temp2[$index] = $value;
					}
				}

				if (!empty($temp2))
				{
					$this->session->set_userdata('cart', $temp2);
				}
				else
				{
					$this->session->unset_userdata('cart');
				}
			}
		}*/
		$this->session->set_userdata('page_title', 'سبد خرید');
		$product_in_cart = '';
		$total_price = 0;
		if($this->session->has_userdata('cart'))
		{
			$option_query = $this->db->get('options')->result();

			$attr = $this->session->userdata('cart');
			$kb = 1;
			foreach ($attr as $index => $value)
			{
				if($kb == 1)
				{
					$this->db->where(array('id' => $index));
				}
				else
				{
					$this->db->or_where(array('id' => $index));
				}
				$kb++;
			}
			if($kb > 1)
			{
				$this->db->select('id,category,type_of_discount,discount_amount,options');
				$product_cat_array = $this->db->get('add_products')->result();
			}

			foreach($attr as $index => $value)
			{
				$option_view = '';
				$discounted_price = 0;
				$discount_price = 0;
				$unit_price = $value->product_price;
				if(isset($product_cat_array))
				{
					foreach($product_cat_array as $cat_row)
					{
						if($index == $cat_row->id)
						{
							$product_category = $cat_row->category;
							if($cat_row->type_of_discount == 'percentage' && is_numeric($cat_row->discount_amount))
							{
								$discount_amount = $cat_row->discount_amount;
								$discount_price = $discount_amount*$value->product_price/100;
							}
							elseif($cat_row->type_of_discount == 'static_value' && is_numeric($cat_row->discount_amount))
							{
								$discount_price = $cat_row->discount_amount;
							}
							$discounted_price = $value->product_price-$discount_price;
							if(isset($value->base_product))
							{
								if($value->base_product == 'not_sold')
								{
									$discounted_price = 0;
								}
							}
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
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									if(isset($option_json->$op_id))
									{
										if(isset($option_json->$op_id->option_price_situation) && isset($option_json->$op_id->option_price))
										{
											if($option_json->$op_id->option_price_situation == '+' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price + $option_json->$op_id->option_price;
												$unit_price = $unit_price + $option_json->$op_id->option_price;
											}
											elseif($option_json->$op_id->option_price_situation == '-' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price - $option_json->$op_id->option_price;
												$unit_price = $unit_price - $option_json->$op_id->option_price;
											}
										}
									}
								}
							}
							elseif($op_row->type == 'select')
							{
								$temp_single_entry = "option_".$op_row->id;
								$temp_select = $value->$temp_single_entry;
								$option_view .= '<div>
                                            <i class="fas fa-angle-double-left"></i>
                                            <span><b>'.$op_row->title.': </b></span>
                                            <span>'.$value->$temp_single_entry.'</span>
                                        </div>';
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									if(isset($option_json->$op_id))
									{
										if(isset($option_json->$op_id->$temp_select))
										{
											if(isset($option_json->$op_id->$temp_select->option_price_situation) && isset($option_json->$op_id->$temp_select->option_price))
											{
												if($option_json->$op_id->$temp_select->option_price_situation == '+' && is_numeric($option_json->$op_id->$temp_select->option_price))
												{
													$discounted_price = $discounted_price + $option_json->$op_id->$temp_select->option_price;
													$unit_price = $unit_price + $option_json->$op_id->$temp_select->option_price;
												}
												elseif($option_json->$op_id->$temp_select->option_price_situation == '-' && is_numeric($option_json->$op_id->$temp_select->option_price))
												{
													$discounted_price = $discounted_price - $option_json->$op_id->$temp_select->option_price;
													$unit_price = $unit_price - $option_json->$op_id->$temp_select->option_price;
												}
											}
										}
									}
								}
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
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									if(isset($option_json->$op_id))
									{
										if(isset($option_json->$op_id->option_price_situation) && isset($option_json->$op_id->option_price))
										{
											if($option_json->$op_id->option_price_situation == '+' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price + $option_json->$op_id->option_price;
												$unit_price = $unit_price + $option_json->$op_id->option_price;
											}
											elseif($option_json->$op_id->option_price_situation == '-' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price - $option_json->$op_id->option_price;
												$unit_price = $unit_price - $option_json->$op_id->option_price;
											}
										}
									}
								}
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
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									foreach($temp_checkbox_array as $t_row)
									{
										if(isset($option_json->$op_id))
										{
											if(isset($option_json->$op_id->$t_row))
											{
												if(isset($option_json->$op_id->$t_row->option_price_situation) && isset($option_json->$op_id->$t_row->option_price))
												{
													if($option_json->$op_id->$t_row->option_price_situation == '+' && is_numeric($option_json->$op_id->$t_row->option_price))
													{
														$discounted_price = $discounted_price + $option_json->$op_id->$t_row->option_price;
														$unit_price = $unit_price + $option_json->$op_id->$t_row->option_price;
													}
													elseif($option_json->$op_id->$t_row->option_price_situation == '-' && is_numeric($option_json->$op_id->$t_row->option_price))
													{
														$discounted_price = $discounted_price - $option_json->$op_id->$t_row->option_price;
														$unit_price = $unit_price - $option_json->$op_id->$t_row->option_price;
													}
												}
											}
										}
									}
								}
							}
							elseif($op_row->type == 'upload')
							{
								$temp_upload = "option_".$op_row->id;
								$temp_upload = $value->$temp_upload;
								$temp_upload = base_url('content/customer_files/'.$temp_upload);
								$option_view .= ' <div>
                                            <i class="fas fa-angle-double-left"></i>
                                            <span><b>'.$op_row->title.': </b></span>
                                            <a target="_blank" href="'.$temp_upload.'"><i class="fas fa-cloud-download-alt"></i></a>
                                        </div>';
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									if(isset($option_json->$op_id))
									{
										if(isset($option_json->$op_id->option_price_situation) && isset($option_json->$op_id->option_price))
										{
											if($option_json->$op_id->option_price_situation == '+' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price + $option_json->$op_id->option_price;
												$unit_price = $unit_price + $option_json->$op_id->option_price;
											}
											elseif($option_json->$op_id->option_price_situation == '-' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price - $option_json->$op_id->option_price;
												$unit_price = $unit_price - $option_json->$op_id->option_price;
											}
										}
									}
								}
							}
							elseif($op_row->type == 'file')
							{
								$option_view .= ' <div>
                                            <i class="fas fa-angle-double-left"></i>
                                            <span><b>'.$op_row->title.'</b></span>
                                        </div>';
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									if(isset($option_json->$op_id))
									{
										if(isset($option_json->$op_id->option_price_situation) && isset($option_json->$op_id->option_price))
										{
											if($option_json->$op_id->option_price_situation == '+' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price + $option_json->$op_id->option_price;
												$unit_price = $unit_price + $option_json->$op_id->option_price;
											}
											elseif($option_json->$op_id->option_price_situation == '-' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price - $option_json->$op_id->option_price;
												$unit_price = $unit_price - $option_json->$op_id->option_price;
											}
										}
									}
								}
							}
						}
					}
				}

				$number_format_product_price = number_format($unit_price);
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

				$product_in_cart .= ' <tr class="product_holder product_item_'.$index.'" product_title="'.$value->product_title.'" product_link="'.$value->product_link.'" product_price="'.$discounted_price.'" product_id="'.$index.'" attr=\'{"tedad":1}\' product_first_image_src="'.$value->product_first_image_src.'">
                                    <td>
                                        <a class="image" href="'.base_url('pages/single_product/'.$index).'">
                                            <img src="'.$value->product_first_image_src.'">
                                        </a>
                                    </td>
                                    <td>
                                        <div class="w3-margin-bottom">
                                            <span '.$class_not_sold.'><b>'.$value->product_title.'</b></span>
                                        </div>
                                       '.$option_view.'
                                    </td>
                                    <td> 
									<div>
										'.$value->tedad.'
										<div class="hidden number_incrementer"><input type="hidden" value="'.$value->tedad.'"></div>
									</div>
                                    </td>
                                    <td class="unit_price">'.$number_format_product_price.'</td>
                                    <td class="discount">'.$number_format_discount_price.'</td>
                                    <td class="final_item_price">'.number_format($discounted_price).'</td>
                                    <td class="icon_bar">
                                        <a href="javascript:void(0);"><i class="fas fa-pen-square w3-text-blue w3-left" onclick="closest_parent(this, \'product_holder\').getElementsByClassName(\'edit_modal\')[0].style.display = \'block\';"></i></a>
                                        <a href="javascript:void(0);"><i class="fas fa-window-close w3-text-red w3-left" onclick="closest_parent(this, \'product_holder\').getElementsByClassName(\'delete_modal\')[0].style.display = \'block\';"></i></a>
                                        <div class="w3-modal edit_modal">
                                            <div class="w3-modal-content">
                                                <header class="w3-container w3-deep-purple">
        <span onclick="closest_parent(this, \'edit_modal\').style.display=\'none\';location.reload();"
              class="w3-button w3-display-topleft">&times;</span>
                                                    <h5>ویرایش سفارش</h5>
                                                </header>
                                                <div class="w3-container">
                                                    <div class="w3-margin-top w3-margin-bottom ">
                                                        <iframe src="'.$value->product_link.'?show_type=raw" height="400px" width="100%" style="border:none;"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w3-modal delete_modal">
                                            <div class="w3-modal-content">
                                                <header class="w3-container w3-red">
        <span onclick="closest_parent(this, \'delete_modal\').style.display=\'none\';"
              class="w3-button w3-display-topleft">&times;</span>
                                                    <h5>حذف سفارش</h5>
                                                </header>
                                                <div class="w3-container">
                                                    <p> آیا از حذف سفارش خود مطمئن هستید؟</p>
                                                    <button onclick="closest_parent(this, \'delete_modal\').style.display=\'none\'" type="button" class="w3-button w3-red w3-left w3-round w3-margin-bottom">لغو</button>
                                                    <button type="button" class="w3-button w3-round w3-margin-bottom w3-blue margin-left w3-left" onclick="remove_from_cart('.$index.'); closest_parent(this, \'delete_modal\').style.display=\'none\';">بله</button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>';
				$temp_tot_price = $discounted_price * $value->tedad;
				$total_price = $total_price + $temp_tot_price;
			}
		}
		$data['product_in_cart'] = $product_in_cart;
		$data['main_total_price'] = $total_price;
		$data['total_price'] = number_format($total_price);
		$content = $this->load->view('pages/cart/cart', $data, true);
		$position_out = $this->mylib->replace_modules_in_position($content, null);
		$this->load->view('template/header');
		$this->output->append_output($position_out['html_content']);
		$this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
	}

	public function progress()
	{
		$this->session->set_userdata('page_title', 'تسویه حساب');
		$day = 0;
		$month = 0;
		$year = 0;
		$customer_group_id = 0;
		if($this->session->userdata('id') != null)
		{
			$this->db->select('customer_group,day,month,year');
			$this->db->where(array('id' => $this->session->userdata('id')));
			$customer_query = $this->db->get('customer')->result();
			foreach($customer_query as $cus_row)
			{
				$customer_group_id = $cus_row->customer_group;
				$day = $cus_row->day;
				$month = $cus_row->month;
				$year = $cus_row->year;
			}
		}

		$time = time();
		$this->load->library('date_shamsi');
		$this_year = $this->date_shamsi->jdate('o', $time,'','Asia/Tehran', 'en');
		$data_day = '';
		$data_month = '';
		$data_year = '';
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

		////////////////////////////////////////////
		/////////create customer group list////////
		//////////////////////////////////////////
		$this->db->select('id,title');
		$this->db->where(array('publish' => 'yes'));
		$category_list = $this->db->get('customer_category');
		/*echo "<pre style='direction: ltr;'>";
		print_r($category_list->num_rows());
		echo "</pre>";*/
		$customer_group_list = '';

		//if (count($category_list) > 0)
		if ($category_list->num_rows() > 0)
		{
			foreach ($category_list->result() as $row)
			{
				$customer_group_list .= '<option value="' . $row->id . '" ' . set_select('customer_group', $row->id, ($row->id == $customer_group_id ? true : false)) . '>' . $row->title . '</option>';
			}
		}

		if ($customer_group_list == '')
		{
			$customer_group_list = "<option value=''>" . lang('no_category') . "</option>";
		}

		$this->load->model('localization/regions');
		/////////////////////////////////////////
		/////////create shipping view////////////
		///////////////////////////////////////
		$view_shipping = '';
		$this->db->where(array('publish' => 'yes'));
		$shipping_query = $this->db->get('shipping');
		$peyk_reg_man = '';
		$regions_for_states = array();
		foreach($shipping_query->result() as $ship_row)
		{
			$view_shipping .= '<option value="'.$ship_row->delivery_type.'">'.lang($ship_row->delivery_type).'</option>';
			if($ship_row->delivery_type == 'peyk_delivery')
			{
				$box_information = json_decode($ship_row->box_information);
				foreach($box_information as $in_state => $val_state)
				{
					$regions_name = '';
					$ty = 1;
					foreach($val_state as $in_reg => $val_reg)
					{
						if($ty == 1)
						{
							$regions_name .= $in_reg;
						}
						else
						{
							$regions_name .= '::separator::'.$in_reg;
						}
						$ty++;
					}
					$regions_for_states[$in_state] = $regions_name;
					#$peyk_reg_man .= '<option value="'.$in_state.'" regions="'.$regions_name.'">'.$in_state.'</option>';
				}
			}
		}
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$temp_country_name = $this->regions->get_countries_as_html_option();
		#$temp_state_name = $peyk_reg_man;
		$temp_state_name = $this->regions->get_states_as_html_option(null, $regions_for_states);
		$temp_city_name = $this->regions->get_cities_as_html_option();
		if($this->session->userdata('id') != null)
		{
			$this->db->where(array('id' => $this->session->userdata('id')));
			$query = $this->db->get('customer');

			$first_name = '';
			$last_name = '';
			$username = '';
			$email = '';
			$day = '';
			$month = '';
			$year = '';
			$sex = '';
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
				$sex = $row->sex;
				$mobile = $row->mobile;
				$temp_address = $row->address;
			}
			$view_address = '';
			if(isset($temp_address))
			{
				$temp_address_json = json_decode($temp_address);
				if(is_array($temp_address_json) && $temp_address_json != array())
				{
					foreach ($temp_address_json as $json_index => $json_row)
					{
						$country_id = $json_row->address_country;
						$state_id = $json_row->address_state;
						$city_id = $json_row->address_city;
						$country_name = $this->regions->find_countries($country_id);
						$state_name = $this->regions->find_states($state_id);
						$city_name = $this->regions->find_cities($city_id);
						$view_address .= '<option value="'.$json_index.'" address_title="'.$json_row->address_title.'" first_name="'.$json_row->address_first_name.'" last_name="'.$json_row->address_last_name.'" mobile="'.$json_row->address_mobile.'" tel="'.$json_row->address_tel.'" postcode="'.$json_row->address_postcode.'" country="'.$country_id.'" state="'.$state_id.'" city="'.$city_id.'" address="'.$json_row->address.'">'.$json_row->address_title.'</option>';
					}
				}
			}

			$data = array(
				'first_name' => $first_name,
				'last_name' => $last_name,
				'username' => $username,
				'email' => $email,
				'day' => $data_day,
				'month' => $data_month,
				'year' => $data_year,
				'customer_group_list' => $customer_group_list,
				'sex' => $sex,
				'mobile' => $mobile,
				'view_address' => $view_address,
				'country_name' => $temp_country_name,
				'state_name' => $temp_state_name,
			    'city_name' => $temp_city_name,
				'view_shipping' => $view_shipping
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
				'customer_group_list' => $customer_group_list,
				'sex' => '',
				'mobile' => '',
				'view_address' => '',
				'country_name' => $temp_country_name,
				'state_name' => $temp_state_name,
				'city_name' => $temp_city_name,
				'view_shipping' => $view_shipping
			);
		}

	    ////////////////////////////////////////////////////
		/////////create view for products in cart//////////
		//////////////////////////////////////////////////
		$product_in_cart = '';
		$total_price = 0;
		if($this->session->has_userdata('cart'))
		{
			$option_query = $this->db->get('options')->result();

			$attr = $this->session->userdata('cart');
			$kb = 1;
			foreach ($attr as $index => $value)
			{
				if($kb == 1)
				{
					$this->db->where(array('id' => $index));
				}
				else
				{
					$this->db->or_where(array('id' => $index));
				}
				$kb++;
			}
			if($kb > 1)
			{
				$this->db->select('id,category,type_of_discount,discount_amount,options');
				$product_cat_array = $this->db->get('add_products')->result();
			}

			foreach($attr as $index => $value)
			{
				$option_view = '';
				$discounted_price = 0;
				$discount_price = 0;
				$unit_price = $value->product_price;
				if(isset($product_cat_array))
				{
					foreach($product_cat_array as $cat_row)
					{
						if($index == $cat_row->id)
						{
							$product_category = $cat_row->category;
							if($cat_row->type_of_discount == 'percentage' && is_numeric($cat_row->discount_amount))
							{
								$discount_amount = $cat_row->discount_amount;
								$discount_price = $discount_amount*$value->product_price/100;
							}
							elseif($cat_row->type_of_discount == 'static_value' && is_numeric($cat_row->discount_amount))
							{
								$discount_price = $cat_row->discount_amount;
							}
							$discounted_price = $value->product_price-$discount_price;
							if(isset($value->base_product))
							{
								if($value->base_product == 'not_sold')
								{
									$discounted_price = 0;
								}
							}
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
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									if(isset($option_json->$op_id))
									{
										if(isset($option_json->$op_id->option_price_situation) && isset($option_json->$op_id->option_price))
										{
											if($option_json->$op_id->option_price_situation == '+' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price + $option_json->$op_id->option_price;
												$unit_price = $unit_price + $option_json->$op_id->option_price;
											}
											elseif($option_json->$op_id->option_price_situation == '-' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price - $option_json->$op_id->option_price;
												$unit_price = $unit_price - $option_json->$op_id->option_price;
											}
										}
									}
								}
							}
							elseif($op_row->type == 'select')
							{
								$temp_single_entry = "option_".$op_row->id;
								$temp_select = $value->$temp_single_entry;
								$option_view .= '<div>
                                            <i class="fas fa-angle-double-left"></i>
                                            <span><b>'.$op_row->title.': </b></span>
                                            <span>'.$value->$temp_single_entry.'</span>
                                        </div>';
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									if(isset($option_json->$op_id))
									{
										if(isset($option_json->$op_id->$temp_select))
										{
											if(isset($option_json->$op_id->$temp_select->option_price_situation) && isset($option_json->$op_id->$temp_select->option_price))
											{
												if($option_json->$op_id->$temp_select->option_price_situation == '+' && is_numeric($option_json->$op_id->$temp_select->option_price))
												{
													$discounted_price = $discounted_price + $option_json->$op_id->$temp_select->option_price;
													$unit_price = $unit_price + $option_json->$op_id->$temp_select->option_price;
												}
												elseif($option_json->$op_id->$temp_select->option_price_situation == '-' && is_numeric($option_json->$op_id->$temp_select->option_price))
												{
													$discounted_price = $discounted_price - $option_json->$op_id->$temp_select->option_price;
													$unit_price = $unit_price - $option_json->$op_id->$temp_select->option_price;
												}
											}
										}
									}
								}
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
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									if(isset($option_json->$op_id))
									{
										if(isset($option_json->$op_id->option_price_situation) && isset($option_json->$op_id->option_price))
										{
											if($option_json->$op_id->option_price_situation == '+' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price + $option_json->$op_id->option_price;
												$unit_price = $unit_price + $option_json->$op_id->option_price;
											}
											elseif($option_json->$op_id->option_price_situation == '-' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price - $option_json->$op_id->option_price;
												$unit_price = $unit_price - $option_json->$op_id->option_price;
											}
										}
									}
								}
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
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									foreach($temp_checkbox_array as $t_row)
									{
										if(isset($option_json->$op_id))
										{
											if(isset($option_json->$op_id->$t_row))
											{
												if(isset($option_json->$op_id->$t_row->option_price_situation) && isset($option_json->$op_id->$t_row->option_price))
												{
													if($option_json->$op_id->$t_row->option_price_situation == '+' && is_numeric($option_json->$op_id->$t_row->option_price))
													{
														$discounted_price = $discounted_price + $option_json->$op_id->$t_row->option_price;
														$unit_price = $unit_price + $option_json->$op_id->$t_row->option_price;
													}
													elseif($option_json->$op_id->$t_row->option_price_situation == '-' && is_numeric($option_json->$op_id->$t_row->option_price))
													{
														$discounted_price = $discounted_price - $option_json->$op_id->$t_row->option_price;
														$unit_price = $unit_price - $option_json->$op_id->$t_row->option_price;
													}
												}
											}
										}
									}
								}
							}
							elseif($op_row->type == 'upload')
							{
								$temp_upload = "option_".$op_row->id;
								$temp_upload = $value->$temp_upload;
								$temp_upload = base_url('content/customer_files/'.$temp_upload);
								$option_view .= ' <div>
                                            <i class="fas fa-angle-double-left"></i>
                                            <span><b>'.$op_row->title.': </b></span>
                                            <a target="_blank" href="'.$temp_upload.'"><i class="fas fa-cloud-download-alt"></i></a>
                                        </div>';
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									if(isset($option_json->$op_id))
									{
										if(isset($option_json->$op_id->option_price_situation) && isset($option_json->$op_id->option_price))
										{
											if($option_json->$op_id->option_price_situation == '+' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price + $option_json->$op_id->option_price;
												$unit_price = $unit_price + $option_json->$op_id->option_price;
											}
											elseif($option_json->$op_id->option_price_situation == '-' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price - $option_json->$op_id->option_price;
												$unit_price = $unit_price - $option_json->$op_id->option_price;
											}
										}
									}
								}
							}
							elseif($op_row->type == 'file')
							{
								$option_view .= ' <div>
                                            <i class="fas fa-angle-double-left"></i>
                                            <span><b>'.$op_row->title.'</b></span>
                                        </div>';
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									if(isset($option_json->$op_id))
									{
										if(isset($option_json->$op_id->option_price_situation) && isset($option_json->$op_id->option_price))
										{
											if($option_json->$op_id->option_price_situation == '+' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price + $option_json->$op_id->option_price;
												$unit_price = $unit_price + $option_json->$op_id->option_price;
											}
											elseif($option_json->$op_id->option_price_situation == '-' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price - $option_json->$op_id->option_price;
												$unit_price = $unit_price - $option_json->$op_id->option_price;
											}
										}
									}
								}
							}
						}
					}
				}

				$number_format_product_price = number_format($unit_price);
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

				$product_in_cart .= '  <tr>
                                    <td>
                                        <a class="image" href="'.base_url('pages/single_product/'.$index).'">
                                            <img src="'.$value->product_first_image_src.'">
                                        </a>
                                    </td>
                                    <td>
                                        <div class="w3-margin-bottom">
                                            <span '.$class_not_sold.'><b>'.$value->product_title.'</b></span>
                                        </div>
                                       '.$option_view.'
                                    </td>
                                    <td> 
										'.$value->tedad.'
                                    </td>
                                    <td class="unit_price">'.$number_format_product_price.'</td>
                                    <td class="discount">'.$number_format_discount_price.'</td>
                                    <td class="final_item_price">'.number_format($discounted_price).'</td>
                                </tr>';
				$temp_tot_price = $discounted_price * $value->tedad;
				$total_price = $total_price + $temp_tot_price;
			}
		}
		$data['product_in_cart'] = $product_in_cart;
		$data['main_total_price_progress'] = $total_price;
		$data['total_price_progress'] = number_format($total_price);

		/////////////////////////////////////////
		//////////create payment view///////////
		///////////////////////////////////////
		$view_payment = '';
		$this->db->where(array('publish' => 'yes'));
		$payment_query = $this->db->get('payment');
		foreach($payment_query->result() as $pay_row)
		{
			$view_payment .= '<div class="w3-col m2 payment">
                    <label><input class="w3-radio" payment_name="'.$pay_row->title.'" type="radio" name="payment" value="'.$pay_row->id.'">
                    '.$pay_row->title.'</label>
                </div>';
		}
		$data['view_payment'] = $view_payment;
		$data['sys_msg'] = $this->load->view('template/sys_msg',  array('error_type' => 'danger'), TRUE);

		$content = $this->load->view('pages/cart/progress', $data, true);
		$position_out = $this->mylib->replace_modules_in_position($content);
		$this->load->view('template/header');
		$this->output->append_output($position_out['html_content']);
		$this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
	}

	public function pay()
	{
		$this->load->model('customer_category');
		$this->load->model('localization/regions');
		$this->load->library('date_shamsi');
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
		$this_year = $this->date_shamsi->jdate('o', $time,'','Asia/Tehran', 'en');
		for($i = $this_year; $i > 1300; $i--)
		{
			array_push($list_year, $i);
		}
		$get_inlist_year = 'in_list['.implode(",",$list_year).']';
		 ///////////////////////////////////////////////
		/////////create get inlist for payment/////////
		//////////////////////////////////////////////
		$payment_id_array = array();
		$this->db->where(array('publish' => 'yes'));
		$payment_query = $this->db->get('payment');
		foreach($payment_query->result() as $pay_row)
		{
			array_push($payment_id_array, $pay_row->id);
		}
		$get_inlist_payment = 'in_list['.implode(",",$payment_id_array).']';
		////////////////////////////////////////////////////
		//////////create inlist for shiping method/////////
		//////////////////////////////////////////////////
		$shiping_name_array = array();
		$this->db->where(array('publish' => 'yes'));
		$shipping_query = $this->db->get('shipping');
		foreach($shipping_query->result() as $ship_row)
		{
			array_push($shiping_name_array, $ship_row->delivery_type);
		}
		$get_inlist_shiping_method = 'in_list['.implode(",",$shiping_name_array).']';
		///////////////////////////////////////////////////
		///////////create inlist for regions//////////////
		/////////////////////////////////////////////////
		$region_name_array = array();
		foreach($shipping_query->result() as $ship_row)
		{
			if($ship_row->delivery_type == 'peyk_delivery')
			{
				$box_information = json_decode($ship_row->box_information);
				foreach($box_information as $in_state => $val_state)
				{
					foreach($val_state as $in_reg => $val_reg)
					{
						array_push($region_name_array, $in_reg);
					}
				}
			}
		}
		$get_inlist_regions = 'in_list['.implode(",",$region_name_array).']';

		/////////////////////////////////////////////////
		//////////calculation products price////////////
		///////////////////////////////////////////////
		$total_price = 0;
		$price_products_array = array();
		if($this->session->has_userdata('cart'))
		{
			$option_query = $this->db->get('options')->result();
			$attr = $this->session->userdata('cart');
			$kb = 1;
			foreach ($attr as $index => $value)
			{
				if($kb == 1)
				{
					$this->db->where(array('id' => $index));
				}
				else
				{
					$this->db->or_where(array('id' => $index));
				}
				$kb++;
			}
			if($kb > 1)
			{
				$this->db->select('id,category,type_of_discount,discount_amount,options,number');
				$product_cat_array = $this->db->get('add_products')->result();
			}

			foreach($attr as $index => $value)
			{
				$option_view = '';
				$discounted_price = 0;
				$discount_price = 0;
				if(isset($product_cat_array))
				{
					foreach($product_cat_array as $cat_row)
					{
						if($index == $cat_row->id)
						{
							$product_category = $cat_row->category;
							if($cat_row->type_of_discount == 'percentage' && is_numeric($cat_row->discount_amount))
							{
								$discount_amount = $cat_row->discount_amount;
								$discount_price = $discount_amount*$value->product_price/100;
							}
							elseif($cat_row->type_of_discount == 'static_value' && is_numeric($cat_row->discount_amount))
							{
								$discount_price = $cat_row->discount_amount;
							}
							$discounted_price = $value->product_price-$discount_price;
							if(isset($value->base_product))
							{
								if($value->base_product == 'not_sold')
								{
									$discounted_price = 0;
								}
							}
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
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									if(isset($option_json->$op_id))
									{
										if(isset($option_json->$op_id->option_price_situation) && isset($option_json->$op_id->option_price))
										{
											if($option_json->$op_id->option_price_situation == '+' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price + $option_json->$op_id->option_price;
											}
											elseif($option_json->$op_id->option_price_situation == '-' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price - $option_json->$op_id->option_price;
											}
										}
									}
								}
							}
							elseif($op_row->type == 'select')
							{
								$temp_single_entry = "option_".$op_row->id;
								$temp_select = $value->$temp_single_entry;
								$option_view .= '<div>
                                            <i class="fas fa-angle-double-left"></i>
                                            <span><b>'.$op_row->title.': </b></span>
                                            <span>'.$value->$temp_single_entry.'</span>
                                        </div>';
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									if(isset($option_json->$op_id))
									{
										if(isset($option_json->$op_id->$temp_select))
										{
											if(isset($option_json->$op_id->$temp_select->option_price_situation) && isset($option_json->$op_id->$temp_select->option_price))
											{
												if($option_json->$op_id->$temp_select->option_price_situation == '+' && is_numeric($option_json->$op_id->$temp_select->option_price))
												{
													$discounted_price = $discounted_price + $option_json->$op_id->$temp_select->option_price;
												}
												elseif($option_json->$op_id->$temp_select->option_price_situation == '-' && is_numeric($option_json->$op_id->$temp_select->option_price))
												{
													$discounted_price = $discounted_price - $option_json->$op_id->$temp_select->option_price;
												}
											}
										}
									}
								}
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
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									if(isset($option_json->$op_id))
									{
										if(isset($option_json->$op_id->option_price_situation) && isset($option_json->$op_id->option_price))
										{
											if($option_json->$op_id->option_price_situation == '+' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price + $option_json->$op_id->option_price;
											}
											elseif($option_json->$op_id->option_price_situation == '-' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price - $option_json->$op_id->option_price;
											}
										}
									}
								}
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
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									foreach($temp_checkbox_array as $t_row)
									{
										if(isset($option_json->$op_id))
										{
											if(isset($option_json->$op_id->$t_row))
											{
												if(isset($option_json->$op_id->$t_row->option_price_situation) && isset($option_json->$op_id->$t_row->option_price))
												{
													if($option_json->$op_id->$t_row->option_price_situation == '+' && is_numeric($option_json->$op_id->$t_row->option_price))
													{
														$discounted_price = $discounted_price + $option_json->$op_id->$t_row->option_price;
													}
													elseif($option_json->$op_id->$t_row->option_price_situation == '-' && is_numeric($option_json->$op_id->$t_row->option_price))
													{
														$discounted_price = $discounted_price - $option_json->$op_id->$t_row->option_price;
													}
												}
											}
										}
									}
								}
							}
							elseif($op_row->type == 'upload')
							{
								$temp_upload = "option_".$op_row->id;
								$temp_upload = $value->$temp_upload;
								$temp_upload = base_url('content/customer_files/'.$temp_upload);
								$option_view .= ' <div>
                                            <i class="fas fa-angle-double-left"></i>
                                            <span><b>'.$op_row->title.': </b></span>
                                            <a target="_blank" href="'.$temp_upload.'"><i class="fas fa-cloud-download-alt"></i></a>
                                        </div>';
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									if(isset($option_json->$op_id))
									{
										if(isset($option_json->$op_id->option_price_situation) && isset($option_json->$op_id->option_price))
										{
											if($option_json->$op_id->option_price_situation == '+' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price + $option_json->$op_id->option_price;
											}
											elseif($option_json->$op_id->option_price_situation == '-' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price - $option_json->$op_id->option_price;
											}
										}
									}
								}
							}
							elseif($op_row->type == 'file')
							{
								$option_view .= ' <div>
                                            <i class="fas fa-angle-double-left"></i>
                                            <span><b>'.$op_row->title.'</b></span>
                                        </div>';
								$op_id = $op_row->id;
								if(isset($option_json))
								{
									if(isset($option_json->$op_id))
									{
										if(isset($option_json->$op_id->option_price_situation) && isset($option_json->$op_id->option_price))
										{
											if($option_json->$op_id->option_price_situation == '+' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price + $option_json->$op_id->option_price;
											}
											elseif($option_json->$op_id->option_price_situation == '-' && is_numeric($option_json->$op_id->option_price))
											{
												$discounted_price = $discounted_price - $option_json->$op_id->option_price;
											}
										}
									}
								}
							}
						}
					}
				}
				$temp_tot_price = $discounted_price * $value->tedad;
				$total_price = $total_price + $temp_tot_price;
				$price_products_array[$index]['discounted_price'] = $temp_tot_price;
				$price_products_array[$index]['discount_price'] = $discount_price;
			}
		}

		if($this->session->userdata('id') != null)
		{
			$this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required|min_length[2]|max_length[50]');

			$this->form_validation->set_rules('last_name', lang('last_name'), 'trim|required|min_length[2]|max_length[50]');

			$this->form_validation->set_rules('year', lang('year'), 'required|'.$get_inlist_year);

			$this->form_validation->set_rules('month', lang('month'), 'required|'.$get_inlist_month);

			$this->form_validation->set_rules('day', lang('day'), 'required|'.$get_inlist_day);

			$this->form_validation->set_rules('sex', lang('sex'), 'required|in_list[man,others,female]');

			if($this->input->post('mobile') == '' && $this->input->post('email') == '')
			{
				$this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|min_length[6]|max_length[50]', array('required' => 'حداقل یکی از فیلدهای ایمیل یا موبایل را باید تکمیل کنید.'));
			}
			else
			{
				$this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|min_length[6]|max_length[50]');
			}

			$this->form_validation->set_rules('customer_group', lang('customer_group'),'required|'. $this->customer_category->get_inlist_string());

			$this->form_validation->set_rules('mobile', lang('mobile'),'trim|exact_length[11]|is_natural');
		}
		$this->form_validation->set_rules('address_title', lang('title'),'trim|required|min_length[3]|max_length[50]');

		$this->form_validation->set_rules('address_first_name', lang('first_name'),'trim|required|min_length[2]|max_length[50]');

		$this->form_validation->set_rules('address_last_name', lang('last_name'),'trim|required|min_length[2]|max_length[50]');

		$this->form_validation->set_rules('address_mobile', lang('mobile'),'trim|required|exact_length[11]|is_natural');

		$this->form_validation->set_rules('address_tel', lang('tel'),'trim|min_length[3]|max_length[12]|is_natural');

		$this->form_validation->set_rules('address_postcode', lang('postcode'),'trim|exact_length[10]|is_natural');

		$this->form_validation->set_rules('address_country', lang('country'),'required|'. $this->regions->get_countries_inlist_string());

		$this->form_validation->set_rules('address_state', lang('state'),'required|'. $this->regions->get_state_inlist_string());

		$this->form_validation->set_rules('address_city', lang('city'),'required|max_length[50]|'. $this->regions->get_city_inlist_string());

		$this->form_validation->set_rules('address', lang('address'),'trim|required|min_length[5]|max_length[1000]');

		$this->form_validation->set_rules('payment', lang('payment'),'required|'.$get_inlist_payment);

		$this->form_validation->set_rules('shipping_method', lang('shipping_method'),'required|'.$get_inlist_shiping_method);

		if($this->input->post('shipping_method') == 'peyk_delivery')
		{
			$this->form_validation->set_rules('shipping_region', lang('shipping_region'),'required|'.$get_inlist_regions);
		}

		if ($this->form_validation->run() == TRUE)
		{
			$insert = 'yes';
			$delivery_type = $this->input->post('shipping_method');
			$state = $this->input->post('address_state');
			$region = $this->input->post('shipping_region');
			$shiping_price = json_decode($this->mylib->post_calculation($delivery_type, $state, $region));
			if(isset($shiping_price->shipping_price))
			{
				$shiping_price = $shiping_price->shipping_price;
			}
			else
			{
				$insert = 'no';
			}

			$order_total_sum = $shiping_price + $total_price;

			$first_name = '';
			$last_name = '';
			$year = '';
			$month = '';
			$day = '';
			$sex = '';
			$email = '';
			$customer_group = '';
			$mobile = '';
			if($this->session->userdata('id') != null)
			{
				$first_name = $this->input->post('first_name');
				$last_name = $this->input->post('last_name');
				$year = $this->input->post('year');
				$month = $this->input->post('month');
				$day = $this->input->post('day');
				$sex = $this->input->post('sex');
				$email = $this->input->post('email');
				$customer_group = $this->input->post('customer_group');
				$mobile = $this->input->post('mobile');
			}

			if(!isset($option_query))
			{
				$option_query = '';
			}
			if(!isset($product_cat_array))
			{
				$product_cat_array = '';
			}

			$dadeh = array
			(
				'user_id' => $this->session->userdata('id'),
				'first_name' => $first_name,
				'last_name' => $last_name,
				'year' => $year,
				'month' => $month,
				'day' => $day,
				'sex' => $sex,
				'email' => $email,
				'customer_group' => $customer_group,
				'mobile' => $mobile,
				'address_title' => $this->input->post('address_title'),
				'address_first_name' => $this->input->post('address_first_name'),
				'address_last_name' => $this->input->post('address_last_name'),
				'address_mobile' => $this->input->post('address_mobile'),
				'address_tel' => $this->input->post('address_tel'),
				'address_postcode' => $this->input->post('address_postcode'),
				'address_country' => $this->input->post('address_country'),
				'address_state' => $this->input->post('address_state'),
				'address_city' => $this->input->post('address_city'),
				'address' => $this->input->post('address'),
				'payment' => $this->input->post('payment'),
				'shipping_method' => $this->input->post('shipping_method'),
				'shipping_region' => $this->input->post('shipping_region'),
				'cart' => json_encode($this->session->userdata('cart')),
				'sum_of_prices_for_selected_products' => $total_price,
				'shipping_price' => $shiping_price,
				'order_total_sum' => $order_total_sum,
				'condition' => 'not_paid',
				'price_products_array' => json_encode($price_products_array),
				'option_query' => json_encode($option_query),
				'product_cat_array' => json_encode($product_cat_array)
			);
			if($insert == 'yes')
			{
				$dadeh['insert_date'] = time();
				$this->db->insert('orders', $dadeh);
				$insert_id = $this->db->insert_id();
				if ($insert_id)
				{
					$this->session->set_userdata('orderId', $insert_id);
					$response = array("status" => "successful");

					//Send Data to Bank
					$this->db->where(array('id' => $this->input->post('payment'), 'publish' => 'yes'));
					$paymentQuery = $this->db->get('payment');
					if ($this->db->count_all_results() == 1) {
						$paymentRow = $paymentQuery->row();
						if ($paymentRow->extra) {
							$paymentRow->extra = json_decode($paymentRow->extra);

							$order_total_sum = floatval($order_total_sum) * 10;
							$order_total_sum = number_format($order_total_sum,0,"","");
							//$order_total_sum = 1000; //برای تست گاهی این رو به هزار ریال تغییر میدیم
							switch ($this->input->post('payment')) {
								case 1:
									$this->load->library('payment/pasargad/pasargad');
									$redirect_form = $this->pasargad->send_to_bank($paymentRow->merchantcode, $paymentRow->terminal_code, $order_total_sum, $insert_id);
									break;
								case 2:
									$this->load->library('payment/mellat/mellat');
									$redirect_form = $this->mellat->send_to_bank($paymentRow->merchantcode, $paymentRow->terminal_code, $paymentRow->extra->password, $order_total_sum, $insert_id, $dadeh['user_id']);
									break;
							}

							$response['redirect_form'] = $redirect_form;

							//////////////////////////////////////////////
							//////increase sales number of products//////
							////////////////////////////////////////////
							$products_in_cart = $this->session->userdata('cart');
							$this->db->set('sales_number', 'sales_number+1', FALSE);
							$ad = 1;
							foreach($products_in_cart as $pr_in_row => $pr_val_row)
							{
								if($ad == 1)
								{
									$this->db->where(array('id' => $pr_in_row));
								}
								else
								{
									$this->db->or_where(array('id' => $pr_in_row));
								}
								$ad++;
							}
							$this->db->update('add_products');
						}
						else {
							$response = array("status" => "unsuccessful", 'message' => 'تنظیمات درگاه پرداخت اشتباه است. لطفا با مدیریت تماس بگیرید.');
						}
					}
					else {
						$response = array("status" => "unsuccessful", 'message' => 'درگاه پرداخت انتخاب شده، وجود ندارد. لطفا صفحه را ببندید و مجددا تلاش نمایید.');
					}
				}
				else
				{
					$response = array("status" => "unsuccessful", 'message' => 'سفارش شما ثبت نشد, لطفا مجددا تلاش کنید.');
				}
			}
			else
			{
				if(isset($shiping_price->message))
				{
					$response = array("status" => "unsuccessful", 'message' => $shiping_price->message);
				}
				else
				{
					$response = array("status" => "unsuccessful", 'message' => 'محاسبه هزینه حملو نقل انجام نشده است.');
				}
			}
		}
		else
		{
			$response = array("status" => "unsuccessful", 'message' => validation_errors());
		}
        echo json_encode($response, true);
	}

	public function finishing_message($bank_name)
	{
	    //متن پیامی که میبایست در صفحه دیده شود
        //بطور پیشفرض متن را روی «دسترسی غیرمجاز» تعیین میکنیم
	    $data["message"] = '<div class="w3-card-4 w3-center w3-pale-red w3-padding-64 message">
            <div class="w3-container">
                <p><b>دسترسی غیر مجاز</b></p>
                <p>ورود به این صفحه بطور مستقیم غیرمجاز است.</p>
            </div>
    
            <div class="w3-margin-top">
                <a href="'.base_url().'" class="btn w3-button w3-round w3-red w3-margin-bottom" style="width: 150px">برگشت به سایت</a>
            </div>
        </div>';

        if ($bank_name == 'pasargad')
        {
            if (isset($_GET['tref']))
            {
                $this->load->library('payment/pasargad/pasargad');

                $fields = array('invoiceUID' => $_GET['tref'] );
                $result = $this->pasargad->post2https($fields,'https://pep.shaparak.ir/CheckTransactionResult.aspx');
                $array = $this->pasargad->makeXMLTree($result);

                if ($array["resultObj"]["result"] == "True")
                {
                    //پرداخت موفقیت آمیز بوده است
                    //میبایست درخواست تائید را به بانک ارسال کنیم
                    $verfiy_array = $this->pasargad->verify_payment($array["resultObj"]["merchantCode"], $array["resultObj"]["terminalCode"], $array["resultObj"]["amount"], $array["resultObj"]["invoiceNumber"], $array["resultObj"]["invoiceDate"]);

                    if ($verfiy_array['actionResult']['result'] == 'True')
                    {
                        //عملیات پرداخت و تائید آن توسط بانک با موفقیت انجام شد
                        //میبایست پیام موفقیت آمیز بودن را به کاربر نمایش دهیم
                        $data["message"] = '<div class="w3-card-4 w3-center w3-pale-green w3-padding-64 message">
                        <div class="w3-container">
                            <p><b>با تشکر از خرید شما</b></p>
                            <p>'.$verfiy_array['actionResult']['resultMessage'].'</p>
                            <p> سفارش شما با کد '.$array["resultObj"]["invoiceNumber"].' ثبت شد. در صورتی که عضو سایت هستید، میتوانید از طریق <a href="'.base_url("profile").'" class="w3-text-blue">پیگیری سفارش</a> وضعیت سفارش خود را مشاهده نمایید. </p>
                        </div>
                
                        <div class="w3-margin-top">
                            <a href="'. base_url("profile").'" class="btn w3-button w3-round w3-green w3-margin-bottom" style="width: 150px">پروفایل</a>
                            <a href="'.base_url().'" class="btn w3-button w3-round w3-red w3-margin-bottom" style="width: 150px">برگشت به سایت</a>
                        </div>
                    </div>';
                        ///////////////////////////////////////////////////////
						/////////////////send email for admin/////////////////
						/////////////////////////////////////////////////////
						$this->db->select('admin_email,system_email');
						$this->db->where(array('id' => 1));
						$setting_query = $this->db->get('setting')->result();
						foreach($setting_query as $set_row)
						{
							$admin_email = $set_row->admin_email;
							$system_email = $set_row->system_email;
							$order_link = base_url('admin/order/view/'.$array["resultObj"]["invoiceNumber"]);
							$email_message = "<div dir='rtl' style='text-align: right;'>با سلام<br/>سفارش جدیدی در سایت ثبت شده است. لطفا جهت بررسی به آدرس زیر رجوع نمایید.<br/><a href=$order_link>$order_link</a></div>";
							$this->load->library('email');
							$mail_config['mailtype'] = "html";
							$this->email->initialize($mail_config);
							$this->email->from($system_email, $system_email);
							$this->email->to($admin_email);
							$this->email->subject('سفارش جدید');
							$this->email->message($email_message);
							$this->email->send();
						}

                        /////////////////////////////////////////////////////
						//////update status of order to the paid status//////
						/////////////////////////////////////////////////////
						$this->db->where(array('id' => $array["resultObj"]["invoiceNumber"]));
						$orders_query = $this->db->get('orders');
						foreach($orders_query->result() as $order_row)
						{
							$history_order = json_decode($order_row->history_order);
							$new_history_order = array();
							$new_history_order['date_added'] = time();
							$new_history_order['description'] = '';
							$new_history_order['condition'] = 3;
							$new_history_order['customer_notified'] = '';
							if(is_array($history_order))
							{
								array_push($history_order, $new_history_order);
							}
							else
							{
								$history_order = array();
								array_push($history_order, $new_history_order);
							}
							$dadeh_up = array(
								'history_order' => json_encode($history_order),
								'customer_notified' => '',
								'condition' => 3,
								'modify_date' => time()
							);
							$this->db->where('id', $array["resultObj"]["invoiceNumber"]);
							$is_update = $this->db->update('orders', $dadeh_up);
							/////////////////////////////////////////////////////////////
							///////Decrease number of product and option in order///////
							///////////////////////////////////////////////////////////
							$cart = json_decode($order_row->cart);
							if($is_update == true && $cart != '' && $cart != array() && $cart != null)
							{
								$option_query = json_decode($order_row->option_query);
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
						}
						$this->session->unset_userdata('cart');
						///////////////////////////////////////////////////////////
                    }
                    else
                    {
                        //پرداخت توسط بانک تائید نشد
                        //خطای مرتبط را به کاربر نمایش میدهیم
                        $data["message"] = '<div class="w3-card-4 w3-center w3-pale-red w3-padding-64 message">
                        <div class="w3-container">
                            <p><b>.متاسفانه خرید شما ناموفق بود</b></p>
                            <p>'.$verfiy_array['actionResult']['resultMessage'].'</p>
                            <p>لطفا مجددا امتحان نمایید و در صورتی که مبلغ سفارش از حساب شما کسر شده است، طی 72 ساعت برگشت داده خواهد شد در غیر اینصورت با همکاران ما تماس حاصل نمایید.</p>
                        </div>
                
                        <div class="w3-margin-top">
                            <a href="'.base_url().'" class="btn w3-button w3-round w3-red w3-margin-bottom" style="width: 150px">برگشت به سایت</a>
                        </div>
                    </div>';
                    }
                }
                else
                {
                    //پرداخت ناموفق بوده است. میبایست خطای عدم موفقیت را نمایش دهیم.
                    $data["message"] = '<div class="w3-card-4 w3-center w3-pale-red w3-padding-64 message">
                    <div class="w3-container">
                        <p><b>.متاسفانه خرید شما ناموفق بود</b></p>
                        <p>علت آن توسط بانک اینطور اعلام شده است: '.$array["resultObj"]["action"].'</p>
                        <p>لطفا مجددا امتحان نمایید و در صورتی که مبلغ سفارش از حساب شما کسر شده است، طی 72 ساعت برگشت داده خواهد شد در غیر اینصورت با همکاران ما تماس حاصل نمایید.</p>
                    </div>
            
                    <div class="w3-margin-top">
                        <a href="'.base_url().'" class="btn w3-button w3-round w3-red w3-margin-bottom" style="width: 150px">برگشت به سایت</a>
                    </div>
                </div>';
                }
            }

        }
        else if ($bank_name == 'mellat')
        {
			$orderId = $this->session->userdata('orderId');
            if (isset($_POST['ResCode']) && $_POST['ResCode'] == 0 && isset($orderId) && isset($_POST['SaleOrderId']) && $orderId == $_POST['SaleOrderId'] && isset($_POST['SaleReferenceId']))
            {
				$this->db->where(array('id' => 2, 'publish' => 'yes'));
				$paymentQuery = $this->db->get('payment');
				if ($this->db->count_all_results() == 1) {
					$paymentRow = $paymentQuery->row();
					if ($paymentRow->extra) {
						$paymentRow->extra = json_decode($paymentRow->extra);
						$this->load->library('payment/mellat/mellat');

						$verifyResult = $this->mellat->verify_payment($paymentRow->merchantcode, $paymentRow->terminal_code, $paymentRow->extra->password, $orderId, $_POST['SaleReferenceId']);
						if ($verifyResult) {
							//عملیات پرداخت و تائید آن توسط بانک با موفقیت انجام شد
							//میبایست پیام موفقیت آمیز بودن را به کاربر نمایش دهیم
							$data["message"] = '<div class="w3-card-4 w3-center w3-pale-green w3-padding-64 message">
                        <div class="w3-container">
                            <p><b>با تشکر از خرید شما</b></p>
                            <p>عملیات پرداخت موفقیت آمیز بود.</p>
                            <p> سفارش شما با کد '.$orderId.' ثبت شد. در صورتی که عضو سایت هستید، میتوانید از طریق <a href="'.base_url("profile").'" class="w3-text-blue">پیگیری سفارش</a> وضعیت سفارش خود را مشاهده نمایید. </p>
                        </div>
                
                        <div class="w3-margin-top">
                            <a href="'. base_url("profile").'" class="btn w3-button w3-round w3-green w3-margin-bottom" style="width: 150px">پروفایل</a>
                            <a href="'.base_url().'" class="btn w3-button w3-round w3-red w3-margin-bottom" style="width: 150px">برگشت به سایت</a>
                        </div>
                    </div>';
							///////////////////////////////////////////////////////
							/////////////////send email for admin/////////////////
							/////////////////////////////////////////////////////
							$this->db->select('admin_email,system_email');
							$this->db->where(array('id' => 1));
							$setting_query = $this->db->get('setting')->result();
							foreach($setting_query as $set_row)
							{
								$admin_email = $set_row->admin_email;
								$system_email = $set_row->system_email;
								$order_link = base_url('admin/order/view/'.$orderId);
								$email_message = "<div dir='rtl' style='text-align: right;'>با سلام<br/>سفارش جدیدی در سایت ثبت شده است. لطفا جهت بررسی به آدرس زیر رجوع نمایید.<br/><a href=$order_link>$order_link</a></div>";
								$this->load->library('email');
								$mail_config['mailtype'] = "html";
								$this->email->initialize($mail_config);
								$this->email->from($system_email, $system_email);
								$this->email->to($admin_email);
								$this->email->subject('سفارش جدید');
								$this->email->message($email_message);
								$this->email->send();
							}

							/////////////////////////////////////////////////////
							//////update status of order to the paid status//////
							/////////////////////////////////////////////////////
							$this->db->where(array('id' => $orderId));
							$orders_query = $this->db->get('orders');
							foreach($orders_query->result() as $order_row)
							{
								$history_order = json_decode($order_row->history_order);
								$new_history_order = array();
								$new_history_order['date_added'] = time();
								$new_history_order['description'] = '';
								$new_history_order['condition'] = 3;
								$new_history_order['customer_notified'] = '';
								if(is_array($history_order))
								{
									array_push($history_order, $new_history_order);
								}
								else
								{
									$history_order = array();
									array_push($history_order, $new_history_order);
								}
								$dadeh_up = array(
									'history_order' => json_encode($history_order),
									'customer_notified' => '',
									'condition' => 3,
									'modify_date' => time()
								);
								$this->db->where('id', $orderId);
								$is_update = $this->db->update('orders', $dadeh_up);
								/////////////////////////////////////////////////////////////
								///////Decrease number of product and option in order///////
								///////////////////////////////////////////////////////////
								$cart = json_decode($order_row->cart);
								if($is_update == true && $cart != '' && $cart != array() && $cart != null)
								{
									$option_query = json_decode($order_row->option_query);
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
							}
							$this->session->unset_userdata('cart');
							$this->session->unset_userdata('orderId');

							$settleResult = $this->mellat->settle_request($paymentRow->merchantcode, $paymentRow->terminal_code, $paymentRow->extra->password, $orderId, $_POST['SaleReferenceId']);
						}
						else {
							$data["message"] = '<div class="w3-card-4 w3-center w3-pale-red w3-padding-64 message">
                    <div class="w3-container">
                        <p><b>تائید پرداخت توسط بانک مردود شد</b></p>
                        <p>لطفا مجددا امتحان نمایید و در صورتی که مبلغ سفارش از حساب شما کسر شده است، طی 72 ساعت برگشت داده خواهد شد در غیر اینصورت با همکاران ما تماس حاصل نمایید.</p>
                    </div>
            
                    <div class="w3-margin-top">
                        <a href="'.base_url().'" class="btn w3-button w3-round w3-red w3-margin-bottom" style="width: 150px">برگشت به سایت</a>
                    </div>
                </div>';
						}
					}
					else {
						$data["message"] = '<div class="w3-card-4 w3-center w3-pale-red w3-padding-64 message">
                    <div class="w3-container">
                        <p><b>.خطای درگاه پرداخت در پایگاه داده</b></p>
                        <p>لطفا مجددا امتحان نمایید و در صورتی که مبلغ سفارش از حساب شما کسر شده است، طی 72 ساعت برگشت داده خواهد شد در غیر اینصورت با همکاران ما تماس حاصل نمایید.</p>
                    </div>
            
                    <div class="w3-margin-top">
                        <a href="'.base_url().'" class="btn w3-button w3-round w3-red w3-margin-bottom" style="width: 150px">برگشت به سایت</a>
                    </div>
                </div>';
					}
				}
				else {
					$data["message"] = '<div class="w3-card-4 w3-center w3-pale-red w3-padding-64 message">
                    <div class="w3-container">
                        <p><b>.ارتباط با پایگاه داده با خطا مواجه شد</b></p>
                        <p>لطفا مجددا امتحان نمایید و در صورتی که مبلغ سفارش از حساب شما کسر شده است، طی 72 ساعت برگشت داده خواهد شد در غیر اینصورت با همکاران ما تماس حاصل نمایید.</p>
                    </div>
            
                    <div class="w3-margin-top">
                        <a href="'.base_url().'" class="btn w3-button w3-round w3-red w3-margin-bottom" style="width: 150px">برگشت به سایت</a>
                    </div>
                </div>';
				}
            }
			else {
				$data["message"] = '<div class="w3-card-4 w3-center w3-pale-red w3-padding-64 message">
                    <div class="w3-container">
                        <p><b>پرداخت ناموفق بود</b></p>
                        <p>لطفا مجددا امتحان نمایید و در صورتی که مبلغ سفارش از حساب شما کسر شده است، طی 72 ساعت برگشت داده خواهد شد در غیر اینصورت با همکاران ما تماس حاصل نمایید.</p>
                    </div>
            
                    <div class="w3-margin-top">
                        <a href="'.base_url().'" class="btn w3-button w3-round w3-red w3-margin-bottom" style="width: 150px">برگشت به سایت</a>
                    </div>
                </div>';
			}
        }
        else
        {
            //نام بانک ارسال نشده است! باید خطا نشان دهیم!
        }

		$content = $this->load->view('pages/cart/finishing_message', $data, true);
		$position_out = $this->mylib->replace_modules_in_position($content);
		$this->load->view('template/header');
		$this->output->append_output($position_out['html_content']);
		$this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
	}

}
