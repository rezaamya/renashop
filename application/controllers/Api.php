<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->session->set_userdata('last_activity', time());
	}

	/**
	 * Dar in safhe gharar ast tamami darkhasthaye AJAX (ya sayer request ha) ra pasokh bedahim
	 */
    public function index(/*$req_array = null*/)
    {
        /*$this->mylib->is_login();*/

        $response = array();
        //Standard request will be same as this:
        //{"req":"request_type", "request_attr_1":"attr_1_value", ... , "request_attr_n":"attr_n_value",}
        $request = $this->input->post('req');

		$get_request = $this->input->get('req');
        if (isset($get_request))
		{
			$request = $get_request;
		}

		/*if(is_array($req_array))
		{
			if(isset($req_array['req']))
			{
				$request = $req_array['req'];
			}
		}*/

        if (isset($request))
        {
            switch ($request)
            {
            	case 'find_product':
					$product_key = $this->input->post('key');
					$except_products = $this->input->post('exception');

					if (isset($product_key))
					{
						$this->db->select('id,title,price,publish');
						$this->db->group_start();
						$this->db->like('title' , $product_key);
						$this->db->or_like('title_alias_url' , $product_key);
						$this->db->or_like('meta_tag_title' , $product_key);
						$this->db->or_like('meta_tag_keywords' , $product_key);
						$this->db->or_like('meta_tag_description' , $product_key);
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

						$i = 1;
						if(isset($except_products) && $except_products != '' && $except_products != null)
						{
							$except_products = explode(',', $except_products);
							foreach($except_products as $except_row)
							{
								$this->db->group_start();
								if($i == 1)
								{
									$this->db->where(array('id !=' => $except_row));
								}
								else
								{
									$this->db->or_where(array('id !=' => $except_row));
								}
								$this->db->group_end();
								$i++;
							}
						}
						$query = $this->db->get('add_products');
						$response = $query->result_array();
					}
					else
					{
						//this function need a $category_id to work
						$response = array('error' => 'Server Error', 'message' => 'Your request is not correct.' );
					}

					echo json_encode($response);
					break;
            	case 'add_to_cart':
                    $new_attribute = json_decode($this->input->post('attr'));
                    $id = $this->input->post('id');
                    $replace_all_attrs = $this->input->post('replace_all_attrs');

                    if ($this->session->has_userdata('cart'))
                    {
                        $current_cart = $this->session->userdata('cart');
                        if (!empty($current_cart) && isset($current_cart[$id]))
                        {
                            if($replace_all_attrs == true)
                            {
                                //add to cart in product page and replace all product attribute by new attribute
                                $current_cart[$id] = $new_attribute;
                            }
                            else
                            {
                                //add to cart in another page and replace just tedad attribute by new tedad attribute
                                $temp_current_cart = $current_cart[$id];
                                foreach ($new_attribute as $index1 => $value1)
                                {
                                    $to_cart_ghabli_nabode = true;
                                    foreach($temp_current_cart as $index2 => $value2)
                                    {
                                        if ($index1 == $index2)
                                        {
                                            $temp_current_cart->$index2 = $value1;
                                            $to_cart_ghabli_nabode = false;
                                        }
                                    }
                                    if ($to_cart_ghabli_nabode)
                                    {
                                        $temp_current_cart->$index1 = $value1;
                                    }
                                }
                                $current_cart[$id] = $temp_current_cart;
                            }
                        }
                        else
                        {
                            $current_cart[$id] = $new_attribute;
                        }
                        $this->session->set_userdata('cart', $current_cart);
                    }
                    else
                    {
                        $current_cart[$id] = $new_attribute;
                        $this->session->set_userdata('cart', $current_cart);
                    }

                    if($this->session->has_userdata('cart'))
					{
						$attr = $this->session->userdata('cart');
						$id_array = array();
						foreach ($attr as $index_a => $value_a)
						{
							array_push($id_array, $index_a);
						}
						$this->db->where_in('id', $id_array);
						$query_product = $this->db->get('add_products');

						foreach ($attr as $index_a => $value_a)
						{
							foreach ($query_product->result() as $row_product)
							{
								if($row_product->id == $index_a)
								{
									$json_pic = json_decode($row_product->primary_pic);
									$pic_name = '';
								    if(is_array($json_pic))
									{
										if (isset($json_pic[0]))
										{
											if (isset($json_pic[0]->file_name))
											{
												$pic_name = $json_pic[0]->file_name;
											}
										}
									}

									if ($pic_name == '')
									{
										$pic_name = "no_pic.jpg";
									}

									$pic_src = base_url('content/products/thumb/' . "$pic_name");
									$product_link = base_url('pages/single_product/' . $row_product->id);

									$value_a->product_title = $row_product->title;
									$value_a->product_link = $product_link;
									$value_a->product_price = $row_product->price;
									$value_a->product_first_image_src = $pic_src;

								}
							}
						}
					}

                    $response = array ("cart" => $this->session->userdata('cart'), "added" => $id);

                    echo json_encode($response);
                    break;
				case 'add_to_favorite':
                    $product = $this->input->post('id');
					if($this->session->userdata('id'))
					{
						if(isset($product))
                        {
                            $this->db->where(array('user_id' => $this->session->userdata('id'), 'product_id' => $product));
                            $db_favorite = $this->db->get('favorite')->result();

                            if($db_favorite)
                            {
                                $response = array("status" => "unsuccessful", 'message' => lang('you_have_already_added_this_product_to_your_favorites_list'));
                            }
                            else
                            {
                                $dadeh = array(
                                    'user_id' => $this->session->userdata('id'),
                                    'product_id' => $product
                                );

                                $this->db->insert('favorite', $dadeh);
                                if ($this->db->insert_id())
                                {
                                    $response = array ("status" => "successful");
                                }
                                else
                                {
                                    $response = array ("status" => "unsuccessful");
                                }
                            }
                        }
                        else
                        {
                            $response = array("status" => "unsuccessful", 'message' => 'هیچ محصولی برای افزودن به علاقه مندیها انتخاب نشده است.');
                        }
					}
					else
					{
						$response = array("status" => "unsuccessful", 'message' => 'جهت افزودن به لیست علاقه‌مندیها میبایست ابتدا وارد سایت شوید.');
					}

					echo json_encode($response, true);
					break;
				case 'delete_favorite':

					$id = $this->input->post('id');

					if($this->session->userdata('id'))
					{
						$this->db->where(array('user_id' => $this->session->userdata('id'), 'product_id' => $id));
						$db_favorite = $this->db->get('favorite')->result();

						if($db_favorite)
						{
							$this->db->delete('favorite', array('product_id' => $id));
							$response = array("status" => "success");
						}

						else
						{
							$response = array("status" => "unsuccessful");
						}
					}

					echo json_encode($response);
					break;
                case 'remove_from_cart':
					$id = $this->input->post('id');
					if ($this->session->has_userdata('cart'))
					{
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

                        $response = array ("status" => "successful");
					}

                    echo json_encode($response);
                    break;
                case 'get_register_code':
                    $code_sent_to = 'email';
                    $valid = 'no';
                    $email_errors = "";
                    $mobile_errors = "";
                    $status = '';

                    $username = $this->input->post('username');
                    $password = md5($this->input->post('password'));

                    $code = substr(str_shuffle("0123456789"), 0, 6);
                    $this->session->set_userdata('code1', $code);

                    if(isset($username))
                    {
                    	$this->form_validation->set_rules('username', 'ایمیل', 'trim|required|valid_email|min_length[6]|max_length[50]|is_unique[customer.username]|callback__username_check');
						$this->form_validation->set_message('_username_check', '«{field}» میتواند تنها شامل حروف و اعداد انگلیسی و علامت های @ و . و _ باشد. همچنین درج فاصله (Space) مجاز نمیباشد.');
						$this->form_validation->set_rules('password', lang('password'), 'trim|required|min_length[5]|max_length[50]');

                        $this->db->select('system_email,subject_for_confirmation_code_email,message_text_for_confirmation_code_email,message_text_for_confirmation_code_sms');
                        $this->db->where(array('id' => 1));
                        $setting_query = $this->db->get('setting');
                        if ($this->form_validation->run() == TRUE)
                        {
                            $valid = 'yes';
                            $this->session->set_userdata('username', $username);
							$this->session->set_userdata('password', $password);

							foreach($setting_query->result() as $setting_row)
                            {
                                $message = str_replace("{{code}}", $code, $setting_row->message_text_for_confirmation_code_email);
                                $message = str_replace("{{usage_title}}", 'ثبت نام', $message);
                                $subject = str_replace("{{usage_title}}", 'ثبت نام', $setting_row->subject_for_confirmation_code_email);
                                $this->load->library('email_lib');
                                $this->email_lib->send_email($setting_row->system_email, $username, $subject, $message);
                            }
                        }
                        else
                        {
                            $email_errors = validation_errors();
                            $this->form_validation->reset_validation();

                            $this->form_validation->set_rules('username', 'شماره موبایل', 'trim|required|exact_length[11]|is_natural|is_unique[customer.username]|callback__username_check');
							$this->form_validation->set_message('_username_check', '«{field}» میتواند تنها شامل حروف و اعداد انگلیسی و علامت های @ و . و _ باشد. همچنین درج فاصله (Space) مجاز نمیباشد.');
							$this->form_validation->set_rules('password', lang('password'), 'trim|required|min_length[5]|max_length[50]');
                            if ($this->form_validation->run() == TRUE)
                            {
                                $valid = 'yes';
                                $code_sent_to = 'mobile';

								$this->session->set_userdata('username', $username);
								$this->session->set_userdata('password', $password);

                                foreach($setting_query->result() as $setting_row)
                                {
                                    $message = str_replace("{{code}}", $code, $setting_row->message_text_for_confirmation_code_sms);
                                    $message = str_replace("{{usage_title}}", 'ثبت نام', $message);
                                    $this->load->library('sms');
                                    $this->sms->send_sms($username, $message);
                                }
                            }
                            else
                            {
                                $mobile_errors = validation_errors();
                            }
                        }
                        $this->session->set_userdata('code_sent_to', $code_sent_to);

                        $this->db->where(array('username' => $username));
                        $query = $this->db->get('customer');
                        if ($query->num_rows() == 1)
                        {
                            $status = 'registered_user';
                        }
                        else
                        {
                            $status = 'new_user';
                        }
                    }

                    if($valid == 'yes')
                    {
                        $response = array("status" => $status, "code_sent_to" => $code_sent_to, 'expire_in' => 60);
                    }
                    else
                    {
                        $strpos = strpos($username, "@");
                        if ($strpos != '')
                        {
                            $validation_errors = $email_errors;
                        }
                        else
                        {
                            $validation_errors = $mobile_errors;
                        }
                        $response = array("status" => "invalid", "error_message" => $validation_errors);
                    }

                    echo json_encode($response);
                    break;
                case 'resend_register_code':
					$code = $this->session->userdata('code1');
					if($this->session->has_userdata('number_of_resend_code'))
					{
						$number_of_resend_code = $this->session->userdata('number_of_resend_code') + 1;
					}
					else
					{
						$number_of_resend_code = 1;
					}
					$this->session->set_userdata('number_of_resend_code', $number_of_resend_code);
					$code_sent_to = $this->session->userdata('code_sent_to');

					$time_waiting = 15;
					if($number_of_resend_code >= 4)
                    {
                        if($this->session->has_userdata('waiting_time_to_send_a_message'))
                        {
                            if(time() >= $this->session->userdata('waiting_time_to_send_a_message'))
                            {
                                $end_time_waiting = 'yes';
                            }
                            else
                            {
                                $time_waiting = $this->session->userdata('waiting_time_to_send_a_message') - time();
                                $time_waiting = $time_waiting / 60;
                                $time_waiting = round($time_waiting,0,PHP_ROUND_HALF_UP);
                            }
                        }
                        else
                        {
                            $this->session->set_userdata('waiting_time_to_send_a_message', time() + 900);
                        }
                    }

					if($number_of_resend_code < 4 || isset($end_time_waiting))
                    {
                        $this->db->select('system_email,subject_for_confirmation_code_email,message_text_for_confirmation_code_email,message_text_for_confirmation_code_sms');
                        $this->db->where(array('id' => 1));
                        $setting_query = $this->db->get('setting');
                        if($code_sent_to == 'email')
                        {
                            foreach($setting_query->result() as $setting_row)
                            {
                                $message = str_replace("{{code}}", $code, $setting_row->message_text_for_confirmation_code_email);
                                $message = str_replace("{{usage_title}}", 'ثبت نام', $message);
                                $subject = str_replace("{{usage_title}}", 'ثبت نام', $setting_row->subject_for_confirmation_code_email);
                                $this->load->library('email_lib');
                                $this->email_lib->send_email($setting_row->system_email, $this->session->userdata('username'), $subject, $message);
                            }
                        }
                        else if ($code_sent_to == 'mobile')
                        {
                            foreach($setting_query->result() as $setting_row)
                            {
                                $message = str_replace("{{code}}", $code, $setting_row->message_text_for_confirmation_code_sms);
                                $message = str_replace("{{usage_title}}", 'ثبت نام', $message);
                                $this->load->library('sms');
                                $this->sms->send_sms($this->session->userdata('username'), $message);
                            }
                        }
                    }

                    if($number_of_resend_code < 4)
					{
						$response = array("status" => "sent", 'expire_in' => 120);
					}
                    elseif($number_of_resend_code >= 4 && isset($end_time_waiting))
                    {
                        $this->session->unset_userdata('number_of_resend_code');
                        $response = array("status" => "sent", 'expire_in' => 300);
                    }
					else
					{
					    $response = array("status" => "please_wait", "how_much" => "$time_waiting دقیقه");
					}

                    echo json_encode($response);
                    break;
                case 'submit_register_code':
                	$user_enter_code = $this->input->post('code');
                	$code1 = $this->session->userdata('code1');
                	if($user_enter_code == $code1)
					{
						$status = 'correct';
						$dadeh = array
						(
							'username' => $this->session->userdata('username'),
							'password' => $this->session->userdata('password')
						);

                        $strpos = strpos($this->session->userdata('username'), "@");
                        if ($strpos != '')
                        {
                            $dadeh['email'] = $this->session->userdata('username');
                        }
                        else
                        {
                            $dadeh['mobile'] = $this->session->userdata('username');
                        }

						$this->load->model('customer_model');
						$insert_id = $this->customer_model->insert($dadeh);

						if(isset($insert_id) && is_numeric($insert_id) && $insert_id > 0)
                        {
                            $session_data = array (
                                'id' => $insert_id,
                                'session_lifetime' => 2*60*60 + time()
                            );
                            $this->session->set_userdata($session_data);
                        }
                        else
                        {
                            $status = 'incorrect';
                        }
					}
					else
					{
						$status = 'incorrect';
					}
                    $response = array("status" => $status);

                    echo json_encode($response);
                    break;
                case 'delete_address':
                    $id = $this->input->post('id');
					if($this->session->userdata('id') != null && isset($id) && is_numeric($id) && $id != '' && $id != null)
					{
					    $temp_new_json = array();
						$this->db->where(array('id' => $this->session->userdata('id')));
						$this->db->select('address');
						$temp_address = $this->db->get('customer')->row();
						$temp_address = $temp_address->address;
						$temp_address_json = json_decode($temp_address);
						if(is_array($temp_address_json))
						{
							foreach ($temp_address_json as $json_index => $json_row)
							{
								if ($json_index == $id)
								{
									//do nothing
								}
								else
								{
									array_push($temp_new_json, $json_row);
								}
							}
							$temp_address_json = json_encode($temp_new_json);

							$dadeh = array(
								'address' => $temp_address_json
							);

							$this->db->where('id', $this->session->userdata('id'));
							$this->db->update('customer', $dadeh);

							$response = array("status" => "success");
						}
						else
						{
							$response = array("status" => "unsuccessful");
						}
					}
					else
					{
						$response = array("status" => "unsuccessful");
					}
                    echo json_encode($response);
                    break;
                case 'cancel_order':
                    $id = $this->input->post('id');
                    $description = $this->input->post('description');
					$response = array("status" => "unsuccessful");
                    if(isset($id) && isset($description))
					{
						$this->db->where(array('id' => $id));
						$this->db->select('condition,history_order,insert_date');
						$order_query = $this->db->get('orders');
						foreach($order_query->result() as $or_row)
						{
							if($or_row->condition == 'not_paid')
							{
								$this->db->where(array('id' => 1));
							}
							else
							{
								$this->db->where(array('id' => $or_row->condition));
							}
							$this->db->select('can_customer_cancel_the_order_in_this_situation');
							$status_order_query = $this->db->get('status_order')->row();
							if(isset($status_order_query->can_customer_cancel_the_order_in_this_situation))
							{
                                if($status_order_query->can_customer_cancel_the_order_in_this_situation == 'yes')
								{
									$history_order = json_decode($or_row->history_order);
                                    if(!is_array($history_order))
                                    {
                                        //در صورتی که تاریخچه سفارش فعلی تهی یا null باشد یعنی آرایه نباشد
                                        // در اون صورت ما باید ابتدا وضعیت سفارش اولیه رو به تاریخچه سفارش
                                        // افزوده و بعد از آن وضعیت سفارش جدید رو به تاریخچه سفارش بیفزاییم.
                                        $history_order = array();
                                        $new_history_order = array();
                                        $new_history_order['date_added'] = $or_row->insert_date;//وضعیت سفارش اولیه در تاریخ ثبت سفارش ایجاد میشود.
                                        $new_history_order['description'] = '';
                                        if($or_row->condition == 'not_paid')
                                        {
                                            $new_history_order['condition'] = 1;
                                        }
                                        else
                                        {
                                            $new_history_order['condition'] = $or_row->condition;
                                        }
                                        $new_history_order['customer_notified'] = null;
                                        array_push($history_order, $new_history_order);
                                    }
									$new_history_order = array();
									$new_history_order['date_added'] = time();
									$new_history_order['description'] = $description;
									$new_history_order['condition'] = 2;
									$new_history_order['customer_notified'] = '';
									array_push($history_order, $new_history_order);

									$dadeh = array(
										'history_order' => json_encode($history_order),
										'customer_notified' => '',
										'condition' => 2,
										'modify_date' => time()
									);
									$this->db->where('id', $id);
									$update = $this->db->update('orders', $dadeh);
									if($update == true)
									{
										$response = array("status" => "success");
									}
									else
									{
										$response = array("status" => "unsuccessful");
									}
								}
							}
						}
					}
                    echo json_encode($response, true);
                    break;
				case 'simple_search':
					$category = $this->input->post('category');
					$search = $this->input->post('search_field');
					//Search query
					if($search != "")
					{
						$this->db->select('title,price,primary_pic,id');
						$this->db->group_start();
						$this->db->like('title' , $search);
						$this->db->or_like('title_alias_url' , $search);
						$this->db->or_like('description' , $search);
						$this->db->or_like('meta_tag_title' , $search);
						$this->db->or_like('meta_tag_keywords' , $search);
						$this->db->or_like('meta_tag_description' , $search);
						$this->db->or_like('fields' , $search);
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

						if($category != '' && $category != "all")
						{
							$this->db->group_start();
							$this->db->where('category', $category);
							$this->db->group_end();
						}

						$query_search = $this->db->get('add_products');

						foreach ($query_search->result() as $row_search)
						{
							$json_pic = json_decode($row_search->primary_pic);
							$pic_name = '';
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
							if ($pic_name == '')
							{
								$pic_name = "no_pic.jpg";
							}

							$pic_src = base_url('content/products/'."$pic_name");

							array_push($response, array("title" => $row_search->title,
								"price" => $row_search->price,
								"link" => base_url('pages/single_product/'.$row_search->id),
								"pic_link" => $pic_src
							));
						}
					}

					echo json_encode($response);
					break;
				case 'post_calculation':
					$delivery_type = $this->input->post('delivery_type');
					$state = $this->input->post('state');
					$region = $this->input->post('region');

					/*if(is_array($req_array))
					{
						if(isset($req_array['delivery_type']) && isset($req_array['state']))
						{
							$delivery_type = $req_array['delivery_type'];
							$state = $req_array['state'];
							if(isset($req_array['region']))
							{
								$region = $req_array['region'];
							}
						}
					}*/

					/*if($this->session->has_userdata('cart'))
					{
						$weight = 0;
						$shipping_price = 0;
						$attr = $this->session->userdata('cart');
						$temp_is_group = 'no';
						foreach ($attr as $index => $value)
						{
							$temp_is_group = 'yes';
							break;
						}
						$kb = 1;
						if($temp_is_group == 'yes')
						{
							$this->db->group_start();
						}
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
						if($temp_is_group == 'yes')
						{
							$this->db->group_end();
						}
						if($kb > 1)
						{
							$this->db->group_start();
							$this->db->where(array('publish' => 'yes'));
							$this->db->group_end();
							$this->db->select('id,weight,type_of_category,options');
							$product_query = $this->db->get('add_products')->result();
							$temp_product_id_array = array();
							foreach($product_query as $pr_id)
							{
								array_push($temp_product_id_array, $pr_id->id);
							}
							$temp_cart_for_del = $this->session->userdata('cart');
							$temp_new_cart = array();
							foreach($temp_cart_for_del as $index_cart_f => $value_cart_f)
							{
								if (!in_array($index_cart_f, $temp_product_id_array))
								{
									//do nothing
								}
								else
								{
									$temp_new_cart[$index_cart_f] = $value_cart_f;
								}
							}
							if (empty($temp_new_cart))
							{
								$this->session->unset_userdata('cart');
								$response = array("status" => "unsuccessful", 'message' => 'product_not_found');
								echo json_encode($response, JSON_UNESCAPED_UNICODE);
								return false;
							}
							else
							{
								$this->session->set_userdata('cart', $temp_new_cart);
							}
						}
						if(isset($product_query))
						{
							if($product_query != array())
							{
								foreach($product_query as $pro_row)
								{
									if($pro_row->type_of_category == 'physical')
									{
										$tedad = 0;
										foreach ($attr as $index => $value)
										{
											if($pro_row->id == $index)
											{
												$tedad = $value->tedad;
												break;
											}
										}
										if($tedad > 0 && is_numeric($tedad) && is_numeric($pro_row->weight))
										{
											$temp_weight = $tedad * $pro_row->weight;
											$weight = $weight + $temp_weight;
										}
										$option_json = json_decode($pro_row->options, true);
										foreach($attr as $index => $value)
										{
											if($pro_row->id == $index)
											{
												foreach($option_json as $op_in_row => $op_val_row)
												{
													$temp_val = "option_" . $op_in_row;
													if(isset($value->$temp_val) && isset($op_val_row['option_weight_situation']) && isset($op_val_row['option_weight']))
													{
														if(is_numeric($op_val_row['option_weight']) && $op_val_row['option_weight_situation'] == '+')
														{
															$temp_weight = $tedad * $op_val_row['option_weight'];
															$weight = $weight + $temp_weight;
														}
														elseif(is_numeric($op_val_row['option_weight']) && $op_val_row['option_weight_situation'] == '-')
														{
															$temp_weight = $tedad * $op_val_row['option_weight'];
															$weight = $weight - $temp_weight;
														}
													}
													else
													{
														foreach ($op_val_row as $op_in => $op_row)
														{
															if(is_array($op_row))
															{
																if(isset($value->$temp_val) && isset($op_row['option_weight_situation']) && isset($op_row['option_weight']))
																{
																	$valer_option = json_decode(json_encode($value->$temp_val), true);
																	if(is_array($valer_option))
																	{
																		foreach($valer_option as $iner => $valer)
																		{
																			if($valer == $op_in)
																			{
																				if(is_numeric($op_row['option_weight']) && $op_row['option_weight_situation'] == '+')
																				{
																					$temp_weight = $tedad * $op_row['option_weight'];
																					$weight = $weight + $temp_weight;
																				}
																				elseif(is_numeric($op_row['option_weight']) && $op_row['option_weight_situation'] == '-')
																				{
																					$temp_weight = $tedad * $op_row['option_weight'];
																					$weight = $weight - $temp_weight;
																				}
																				break;
																			}
																		}
																	}
																}
															}
														}
													}
												}
												break;
											}
										}
									}
								}
							}
							else
							{
								$this->session->unset_userdata('cart');
								$response = array("status" => "unsuccessful", 'message' => 'product_not_found');
								echo json_encode($response, JSON_UNESCAPED_UNICODE);
								return false;
							}
						}
						else
						{
							$this->session->unset_userdata('cart');
							$response = array("status" => "unsuccessful", 'message' => 'product_not_found');
							echo json_encode($response, JSON_UNESCAPED_UNICODE);
							return false;
						}
						$response = array("status" => "unsuccessful");
						if($weight < 0)
						{
							$weight = 0;
						}
						if(isset($delivery_type))
						{
							$this->db->where(array('delivery_type' => $delivery_type, 'publish' => 'yes'));
							$shipping_query = $this->db->get('shipping')->result();
						}
						if($delivery_type == 'express_post' || $delivery_type == 'registered_post')
						{
							if(isset($shipping_query))
							{
								if($shipping_query != array())
								{
									foreach($shipping_query as $ship_row)
									{
										$state_of_origin_send = json_decode($ship_row->state_of_origin_send);
										$box_information = json_decode($ship_row->box_information);
										if(is_array($state_of_origin_send) && is_array($box_information) && $box_information != array())
										{
											$ent = 'no';
											foreach($box_information as $json_box_row)
											{
												if(isset($json_box_row->from_weight) && isset($json_box_row->to_weight) && isset($json_box_row->within_the_province) && isset($json_box_row->tax_within) && isset($json_box_row->insurance_within) && isset($json_box_row->other_costs_within) && isset($json_box_row->out_of_the_province) && isset($json_box_row->tax_out_of) && isset($json_box_row->insurance_out_of) && isset($json_box_row->other_costs_out_of))
												{
													if(is_numeric($json_box_row->from_weight) && is_numeric($json_box_row->to_weight))
													{
														if($weight >= $json_box_row->from_weight && $weight < $json_box_row->to_weight)
														{
															if(in_array($state, $state_of_origin_send))
															{
																$shipping_price = $json_box_row->within_the_province + $json_box_row->tax_within + $json_box_row->insurance_within + $json_box_row->other_costs_within;
															}
															else
															{
																$shipping_price = $json_box_row->out_of_the_province + $json_box_row->tax_out_of + $json_box_row->insurance_out_of + $json_box_row->other_costs_out_of;
															}
															$response = array("status" => "successful", 'shipping_price' => $shipping_price);
															$ent = 'yes';
															break;
														}
													}
												}
											}
											if($ent == 'no')
											{
												$response = array("status" => "unsuccessful", 'message' => 'مجموع وزن محصولات انتخابی شما خارج از محدوده‌ی وزنی روش حملو نقل انتخابی است.');
											}
										}
										else
										{
											$response = array("status" => "unsuccessful", 'message' => 'در حال حاظر روش حملو نقل انتخابی شما در دسترس نمیباشد.');
										}
									}
								}
								else
								{
									$response = array("status" => "unsuccessful", 'message' => 'در حال حاظر روش حملو نقل انتخابی شما در دسترس نمیباشد.');
								}
							}
							else
							{
								$response = array("status" => "unsuccessful", 'message' => 'در حال حاظر روش حملو نقل انتخابی شما در دسترس نمیباشد.');
							}
						}
						elseif($delivery_type == 'peyk_delivery')
						{
							if(isset($shipping_query))
							{
								if($shipping_query != array())
								{
									foreach($shipping_query as $ship_row)
									{
										$box_information = json_decode($ship_row->box_information);
										if(isset($box_information->$state))
										{
											if(isset($box_information->$state->$region))
											{
												if(isset($box_information->$state->$region->from_weight) && is_array($box_information->$state->$region->from_weight))
												{
													$from_weight_peyk = $box_information->$state->$region->from_weight;
													$to_weight_peyk = $box_information->$state->$region->to_weight;
													$delivery_cost_peyk = $box_information->$state->$region->delivery_cost;
													$other_costs_peyk = $box_information->$state->$region->other_costs;
													$count_fr_wegh = count($box_information->$state->$region->from_weight);
													$ent = 'no';
													for($ad = 0; $ad < $count_fr_wegh; $ad++)
													{
														if($weight >= $from_weight_peyk[$ad] && $weight < $to_weight_peyk[$ad])
														{
															$shipping_price = $delivery_cost_peyk[$ad] + $other_costs_peyk[$ad];
															$response = array("status" => "successful", 'shipping_price' => $shipping_price);
															$ent = 'yes';
															break;
														}
													}
													if($ent == 'no')
													{
														$response = array("status" => "unsuccessful", 'message' => 'مجموع وزن محصولات انتخابی شما خارج از محدوده‌ی وزنی روش حملو نقل انتخابی است.');
													}
												}
												else
												{
													$response = array("status" => "unsuccessful", 'message' => 'متاسفانه برنامه ارسالی برای منطقه شما وجود ندارد.');
												}
											}
											else
											{
												$response = array("status" => "unsuccessful", 'message' => 'متاسفانه برنامه ارسالی برای منطقه شما وجود ندارد.');
											}
										}
										else
										{
											$response = array("status" => "unsuccessful", 'message' => 'متاسفانه برنامه ارسالی برای منطقه شما وجود ندارد.');
										}
									}
								}
								else
								{
									$response = array("status" => "unsuccessful", 'message' => 'در حال حاظر روش حملو نقل انتخابی شما در دسترس نمیباشد.');
								}
							}
							else
							{
								$response = array("status" => "unsuccessful", 'message' => 'در حال حاظر روش حملو نقل انتخابی شما در دسترس نمیباشد.');
							}
						}
						else
						{
							$response = array("status" => "unsuccessful", 'message' => 'در حال حاظر روش حملو نقل انتخابی شما در دسترس نمیباشد.');
						}
					}
					else
					{
						$response = array("status" => "unsuccessful", 'message' => 'هیچ محصولی در سبد خرید شما وجود ندارد.');
					}

					echo json_encode($response, JSON_UNESCAPED_UNICODE);*/
                    $response = $this->mylib->post_calculation($delivery_type, $state, $region);
                    echo $response;
					break;

                case 'resend_confirm_code_for_edit_username_in_profile':
                    $code_for_edit_username = $this->session->userdata('code_for_edit_username');

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
                                $this->email_lib->send_email($setting_row->system_email, $this->session->userdata('username_edit_profile'), $subject, $message);
                            }
                        }
                        else if($this->session->userdata('type_of_username_for_edit_username') == 'mobile')
                        {
                            foreach($setting_query->result() as $setting_row)
                            {
                                $message = str_replace("{{code}}", $code_for_edit_username, $setting_row->message_text_for_confirmation_code_sms);
                                $message = str_replace("{{usage_title}}", 'درخواست تغییر نام کاربری', $message);
                                $this->load->library('sms');
                                $this->sms->send_sms($this->session->userdata('username_edit_profile'), $message);
                            }
                        }
                    }

                    if($number_of_resend_code_for_edit_username < 5)
                    {
                        $this->session->set_userdata('life_time_code_for_edit_username', time() + 120);
                        $response = array("status" => "sent", 'expire_in' => 120);
                    }
                    elseif($number_of_resend_code_for_edit_username >= 5 && isset($end_time_waiting))
                    {
                        $this->session->unset_userdata('number_of_resend_code_for_edit_username');
                        $this->session->set_userdata('life_time_code_for_edit_username', time() + 300);
                        $response = array("status" => "sent", 'expire_in' => 300);
                    }
                    else
                    {
                        $response = array("status" => "please_wait", "how_much" => "$time_waiting دقیقه");
                    }

                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    break;

                default:
                    $response = array("error" => 'No request', 'message' => 'There is no Request');
                    echo json_encode($response);
                    break;
            }
        }
        else
        {
            //send error message
            echo json_encode(
                array('error' => 'Server Error', 'message' => 'request is not true' )
            );
        }
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
