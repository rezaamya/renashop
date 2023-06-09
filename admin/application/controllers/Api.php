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
    public function index()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$response = array();
        //Standard request will be same as this:
        //{"req":"request_type", "request_attr_1":"attr_1_value", ... , "request_attr_n":"attr_n_value",}
        $request = $this->input->post('req');

        if (isset($request))
        {
            switch ($request)
            {
                case 'get_attribute_groups_for_special_product_category':

                    $category_id = $this->input->post('category_id');
                    if (isset($category_id))
                    {
                        $this->db->where(array('product_category' => $category_id, 'publish' => 'yes'));
                        $query = $this->db->get('attribute_groups');

                        foreach ($query->result() as $row)
						{
							$response[$row->id] = $row->attribute_groups_name;
						}
                    }
                    else
                    {
                        //this function need a $category_id to work
                        $response = array('error' => 'Server Error', 'message' => 'Your request is not correct.' );
                    }

                    echo json_encode($response);
                    break;

                case 'get_fields':
                    $category_id = $this->input->post('category_id');
                    if (isset($category_id))
                    {
                        $this->db->where(array('product_category' => $category_id, 'publish' => 'yes'));
                        $query = $this->db->get('attribute_groups');

                        foreach ($query->result() as $row)
                        {
                            $attribute_group_id = $row->id;
                            $attribute_group_name = $row->attribute_groups_name;
                            $temp_fileds_array = array();

                            $this->db->where(array('attribute_groups' => $row->id, 'publish' => 'yes'));
							$this->db->order_by('sort', 'ASK');
                            $query_2 = $this->db->get('add_field');

                            foreach ($query_2->result() as $field_row)
                            {
                                array_push($temp_fileds_array, $field_row);
                            }

                            array_push($response, array(
                                "attribute_group_id" => $attribute_group_id,
                                "attribute_group_name" => $attribute_group_name,
                                "fields" => $temp_fileds_array
                            ));
                        }
                    }
                    else
                    {
                        //this function need a $category_id to work
                        $response = array('error' => 'Server Error', 'message' => 'Your request is not correct.' );
                    }

                    echo json_encode($response);
                    break;

				case 'find_product':
					$product_key = $this->input->post('key');
					$except_products = $this->input->post('exception');

					$i = 1;
					if (isset($product_key))
					{
						$this->db->select('id,title,price,publish,type_of_category,number');
						$this->db->group_start();
						$this->db->like('title' , $product_key);
						$this->db->or_like('title_alias_url' , $product_key);
						$this->db->or_like('meta_tag_title' , $product_key);
						$this->db->or_like('meta_tag_keywords' , $product_key);
						$this->db->or_like('meta_tag_description' , $product_key);
						$this->db->group_end();

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

						$result = $query->result();
						foreach($result as $row)
						{
							if($row->publish == 'no')
							{
								$row->title = $row->title.' | منتشر نشده';
							}
							if($row->type_of_category == 'virtual' || $row->number > 0)
							{
								//product in exist
							}
							else
							{
								$row->title = $row->title.' | ناموجود';
							}
						}

						$response = $result;
					}
					else
					{
						//this function need a $category_id to work
						$response = array('error' => 'Server Error', 'message' => 'Your request is not correct.' );
					}

					echo json_encode($response);
					break;

				case 'find_customer':
					$customer_key = $this->input->post('key');
					$except_customer = $this->input->post('exception');

					$i = 1;
					if (isset($customer_key))
					{
						$this->db->select('id,first_name,last_name');
						$this->db->group_start();
						$this->db->like('first_name' , $customer_key);
						$this->db->or_like('last_name' , $customer_key);
						$this->db->or_like('username' , $customer_key);
						$this->db->or_like('email' , $customer_key);
						$this->db->or_like('mobile' , $customer_key);
					    $this->db->or_like('address' , $customer_key);
						$this->db->group_end();

						$this->db->group_start();
						$this->db->where(array('condition' => 'active'));
						$this->db->group_end();

						if(isset($except_customer) && $except_customer != '' && $except_customer != null)
						{
							$except_customer = explode(',', $except_customer);
							foreach($except_customer as $except_row)
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
						$query = $this->db->get('customer');

						$result = $query->result();
						foreach($result as $row)
						{
							$row->title = $row->first_name.' '.$row->last_name;
						}

						$response = $result;
					}
					else
					{
						//this function need a $category_id to work
						$response = array('error' => 'Server Error', 'message' => 'Your request is not correct.' );
					}

					echo json_encode($response);
					break;

				case 'find_affiliate':
					$customer_key = $this->input->post('key');
					$except_customer = $this->input->post('exception');

					if (isset($customer_key))
					{
						/*$this->db->select('id,first_name,last_name');
						$this->db->group_start();
						$this->db->like('first_name' , $customer_key);
						$this->db->or_like('last_name' , $customer_key);
						$this->db->or_like('username' , $customer_key);
						$this->db->or_like('email' , $customer_key);
						$this->db->or_like('mobile' , $customer_key);
						$this->db->or_like('address' , $customer_key);
						$this->db->group_end();
						$this->db->group_start();
						$this->db->where('condition' == 'active');
						if(isset($except_customer) && $except_customer != '' && $except_customer != null)
						{
							$except_customer = explode(',', $except_customer);
							foreach($except_customer as $except_row)
							{
								$this->db->or_where(array('id !=' => $except_row));
							}
						}
						$this->db->group_end();
						$query = $this->db->get('customer');*/

						//$response = $query->result_array();
						$response = array();
					}
					else
					{
						//this function need a $category_id to work
						$response = array('error' => 'Server Error', 'message' => 'Your request is not correct.' );
					}

					echo json_encode($response);
					break;

				case 'get_options':
					$category_id = $this->input->post('category_id');
					if (isset($category_id))
					{
						$this->db->where(array('category' => $category_id, 'publish' => 'yes'));
						$this->db->order_by('sort', 'ASK');
						$query = $this->db->get('options');

						foreach ($query->result() as $row)
						{
							array_push($response, $row);
						}
					}
					else
					{
						//this function need a $category_id to work
						$response = array('error' => 'Server Error', 'message' => 'Your request is not correct.' );
					}

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
}
