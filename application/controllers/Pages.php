<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->session->set_userdata('last_activity', time());
    }
    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *        http://example.com/index.php/welcome
     *    - or -
     *        http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
		$tracking_code = $this->input->get('tr');
		if(isset($tracking_code))
		{
            $tr_temp_o = '';
			if($this->session->has_userdata('tr') && $this->session->userdata('tr') != $tracking_code)
			{
				$tr_temp_o = 'yes';
			}
			if(!$this->session->has_userdata('tr') || $tr_temp_o == 'yes')
			{
				$this->session->set_userdata('tr', $tracking_code);
				$this->db->set('number_of_click', 'number_of_click+1', FALSE);
				$this->db->where(array('tracking_code' => $tracking_code));
				$this->db->update('campaignes');
			}
		}

		global $current_menu_id;
		$current_menu_id = 1;
    	$data = array('page_name' => 'home');

		$this->db->where(array('type' => 'home'));
		$home_menu_query = $this->db->get('menu');
		foreach($home_menu_query->result() as $hom_row)
		{
			$this->session->set_userdata('page_title', $hom_row->title);
			$this->session->set_userdata('page_title_alias_url', $hom_row->title_alias_url);
			$this->session->set_userdata('page_meta_tag_title', $hom_row->meta_tag_title);
			$this->session->set_userdata('page_meta_tag_keywords', $hom_row->meta_tag_keywords);
			$this->session->set_userdata('page_meta_tag_description', $hom_row->meta_tag_description);
		}

		$modules_id = $this->mylib->modules_id_not_show($current_menu_id);
		$content = $this->load->view('pages/home', $data, true);
		$position_out = $this->mylib->replace_modules_in_position($content, $modules_id);

		//Default Item Data
		/*$html_output['item_data'] = array(
			"id" => '',
			"name" => '',
			"email" => '',
			"message" => ''
		);
		$message = $this->input->post('message');
		if(isset($message))
		{
			$html_output = array();
			$data = array();
			$this->load->model('forms_model');

			//Set Form Rules:
			$this->form_validation->set_rules('name', lang('name'), 'trim|required|min_length[2]|max_length[50]');

			$this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|min_length[6]|max_length[100]');

			$this->form_validation->set_rules('message', lang('message'), 'trim');

			if ($this->form_validation->run() == TRUE)
			{
				$dadeh = array
				(
					'name' => $this->input->post('name'),
					'email' => $this->input->post('email'),
					'message' => $this->input->post('message')
				);

				$massages_id = $this->forms_model->insert($dadeh);

				$email = $this->input->post('email');

				if (isset($massages_id))
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}

				$this->db->where('email', $email);
				$query = $this->db->get('massages');
				$html_output['item_data'] = $query->row_array();

			}

			$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
			$data['html_output'] = $html_output;
		}*/

		$this->load->view('template/header');

		$this->output->append_output($position_out['html_content']);

		$this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));

    }

    public function single_page($item_id = null, $menu_id = null)
	{
		$tracking_code = $this->input->get('tr');
		if(isset($tracking_code))
		{
			$tr_temp_o = '';
			if($this->session->has_userdata('tr') && $this->session->userdata('tr') != $tracking_code)
			{
				$tr_temp_o = 'yes';
			}
			if(!$this->session->has_userdata('tr') || $tr_temp_o == 'yes')
			{
				$this->session->set_userdata('tr', $tracking_code);
				$this->db->set('number_of_click', 'number_of_click+1', FALSE);
				$this->db->where(array('tracking_code' => $tracking_code));
				$this->db->update('campaignes');
			}
		}

		global $current_menu_id;
		$current_menu_id = $menu_id;
		$data = array();

		//find next article id
		if($item_id)
		{
			$this->db->where(array('id >' => $item_id, 'publish' => 'yes'));
			$this->db->order_by('id', 'ASC');
			$this->db->limit(1);
			$this->db->select('id,title');
			$next_article = $this->db->get('articles')->result();
		}
		$is_next = 1;

		if(isset($next_article))
		{
			if($next_article == array())
			{
				$this->db->where(array('id >=' => $item_id, 'publish' => 'yes'));
				$this->db->select('id,title');
				$next_article = $this->db->get('articles')->result();

				$is_next = 0;
			}

			if(isset($next_article[0]->id) && isset($next_article[0]->title))
			{
				$next_article_id = $next_article[0]->id;
				$next_title = $next_article[0]->title;

				//find next article menu id
				$this->db->where(array('target_id' => $next_article_id, 'publish' => 'yes'));
				$this->db->select('id');
				$next_article_menu_id = $this->db->get('menu')->result();

				if(isset($next_article_menu_id) && $next_article_menu_id != Array ( ))
				{
					$next_article_menu_id = $next_article_menu_id[0]->id;
				}
				else
				{
					$next_article_menu_id = $menu_id;
				}
			}
		}
		else
		{
			$is_next = 0;
		}

		//find previous article id
		if($item_id)
		{
			$this->db->where(array('id <' => $item_id, 'publish' => 'yes'));
			$this->db->order_by('id', 'DESC');
			$this->db->limit(1);
			$this->db->select('id,title');
			$previous_article = $this->db->get('articles')->result();
		}
		$is_previous = 1;

		if(isset($previous_article))
		{
			if($previous_article == array())
			{
				$this->db->where(array('id <=' => $item_id, 'publish' => 'yes'));
				$this->db->select('id,title');
				$previous_article = $this->db->get('articles')->result();
				$is_previous = 0;
			}

			if(isset($previous_article[0]->id) && isset($previous_article[0]->title))
			{
				$previous_article_id = $previous_article[0]->id;
				$previous_title = $previous_article[0]->title;

				//find previous article menu id
				$this->db->where(array('target_id' => $previous_article_id, 'publish' => 'yes'));
				$this->db->select('id');
				$previous_article_menu_id = $this->db->get('menu')->result();

				if(isset($previous_article_menu_id) && $previous_article_menu_id != Array ( ))
				{
					$previous_article_menu_id = $previous_article_menu_id[0]->id;
				}
				else
				{
					$previous_article_menu_id = $menu_id;
				}
			}
		}
		else
		{
			$is_previous = 0;
		}

		$this->db->where(array('id' => $item_id, 'publish' => 'yes'));
		$query = $this->db->get('articles')->result();

		if(!isset($next_article_id))
		{
			$next_article_id = '';
		}
		if(!isset($next_article_menu_id))
		{
			$next_article_menu_id = '';
		}
		if(!isset($previous_article_id))
		{
			$previous_article_id = '';
		}
		if(!isset($previous_article_menu_id))
		{
			$previous_article_menu_id = '';
		}
		if(!isset($previous_title))
		{
			$previous_title = '';
		}
		if(!isset($next_title))
		{
			$next_title = '';
		}

		if(isset($query))
		{
			if($query != array())
			{
				foreach ($query as $row)
				{
					$this->session->set_userdata('page_title', $row->title);
					$this->session->set_userdata('page_title_alias_url', $row->title_alias_url);
					$this->session->set_userdata('page_meta_tag_title', $row->meta_tag_title);
					$this->session->set_userdata('page_meta_tag_keywords', $row->meta_tag_keywords);
					$this->session->set_userdata('page_meta_tag_description', $row->meta_tag_description);
					$data = array(
						'title' => $row->title,
						'full_content' => $row->full_content,
						'next_article_id' => $next_article_id,
						'next_article_menu_id' => $next_article_menu_id,
						'previous_article_id' => $previous_article_id,
						'previous_article_menu_id' => $previous_article_menu_id,
						'is_previous' => $is_previous,
						'is_next' => $is_next,
						'previous_title' => $previous_title,
						'next_title' => $next_title
					);
				}
			}
			else
			{
				$data = array(
					'title' => '',
					'full_content' => '',
					'next_article_id' => '',
					'next_article_menu_id' => '',
					'previous_article_id' => '',
					'previous_article_menu_id' => '',
					'is_previous' => 0,
					'is_next' => 0,
					'previous_title' => '',
					'next_title' => ''
				);
			}
		}
		else
		{
			$data = array(
				'title' => '',
				'full_content' => '',
				'next_article_id' => '',
				'next_article_menu_id' => '',
				'previous_article_id' => '',
				'previous_article_menu_id' => '',
				'is_previous' => 0,
				'is_next' => 0,
				'previous_title' => '',
				'next_title' => ''
			);
		}

		$modules_id = $this->mylib->modules_id_not_show($menu_id);
		$content = $this->load->view('pages/single_page', $data, true);
		$position_out = $this->mylib->replace_modules_in_position($content, $modules_id);

		$this->load->view('template/header');

		$this->output->append_output($position_out['html_content']);

		$this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
	}

	public function single_product($item_id = null)
	{
		$tracking_code = $this->input->get('tr');
		if(isset($tracking_code))
		{
			$tr_temp_o = '';
			if($this->session->has_userdata('tr') && $this->session->userdata('tr') != $tracking_code)
			{
				$tr_temp_o = 'yes';
			}
			if(!$this->session->has_userdata('tr') || $tr_temp_o == 'yes')
			{
				$this->session->set_userdata('tr', $tracking_code);
				$this->db->set('number_of_click', 'number_of_click+1', FALSE);
				$this->db->where(array('tracking_code' => $tracking_code));
				$this->db->update('campaignes');
			}
		}

		$product_html_container = '<div class="w3-row-padding">
        <div class="w3-col m3">
            <holder class="gallery">gallery</holder>
        </div>
        <div class="w3-col m9 info">
            <div class="w3-row header">
                <div class="w3-col rating_holder w3-left">
                    <holder class="rating_stars">rating_stars</holder>
                    <holder tag_type="span">rating_description</holder>
                </div>
                <div class="w3-rest title_holder">
                    <holder class="w3-row w3-xlarge">title</holder>
                    <div class="w3-row hide_by_customer_request"><?PHP if ($this->mylib->holder_show("brand")) { ?><span>برند: </span><?PHP } ?><holder tag_type="span">brand</holder><?PHP if ($this->mylib->holder_show("category")) { ?><span>دسته بندی: </span><?PHP } ?><holder tag_type="span">category</holder></div>
                </div>
            </div>
            <div class="w3-row-padding content">
                <div class="w3-col m999 m12 description">
                	<?PHP if ($this->mylib->holder_show("description")) { ?>
                    <div class="w3-row">
                        <div><b>درباره کتاب:</b></div>
                        <hr>
                        <holder>description</holder>
                    </div>
                    <?PHP } ?>
                    <?PHP if ($this->mylib->holder_show("options")) { ?>
                    <hr>
                    <holder class="w3-row w3-padding w3-white w3-card">options</holder>
                    <hr>
                    <?PHP } ?>
                    <div class="w3-row">
                        <div>
                            <?PHP //if (has_value("discount_price")) { ?>
                            <?PHP if ($this->mylib->holder_show("price")) { ?>
                            <div class="price_holder">قیمت اصلی: <holder class="price" tag_type="del">price</holder><span class="w3-tiny"> تومان</span></div>
                            <?PHP } ?>
                            <?PHP if ($this->mylib->holder_show("discount_price")) { ?>
                            <div class="discount_price_holder">تخفیف: <holder class="discount_price" tag_type="span">discount_price</holder><span class="w3-tiny"> تومان</span></div>
                            <?PHP } ?>
                            <?PHP //} ?>
                            <?PHP if ($this->mylib->holder_show("discounted_price")) { ?>
                            <div class="discounted_price_holder">قیمت: <holder class="discounted_price" tag_type="span">discounted_price</holder><span class="w3-tiny"> تومان</span></div>
                              <?PHP } ?>
                            <div class="bottom_bar">
                                <holder class="number_incrementer">number_incrementer</holder>
                                <holder class="cart_btn">cart_btn</holder>
                                <holder class="wishlist_btn {{hide_in_raw_show_type}}">wishlist_btn</holder>
                                <holder class="comparison_btn hide_by_customer_request {{hide_in_raw_show_type}}">comparison_btn</holder>
                                 <?PHP if ($this->mylib->holder_show("stock")) { ?>
                                <div class="stock hide_by_customer_request">موجودی: <holder tag_type="span">stock</holder></div>
                                 <?PHP } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w3-col m3 w3-left hide_by_customer_request">
                <?PHP if ($this->mylib->holder_show("descriptive_movie")) { ?>
                <holder class="descriptive_movie">descriptive_movie</holder>
                <?PHP } ?>
                <?PHP if ($this->mylib->holder_show("special_characteristic")) { ?>
                <holder>special_characteristic</holder>
                <?PHP } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="w3-container {{hide_in_raw_show_type}}">
        <div class="w3-row">
            <div class="w3-bar tab-title">
            <?PHP if ($this->mylib->holder_show("attributes")) { ?>
                <div class="w3-bar-item w3-buttonn tablink w3-right active" onclick="change_tabs(event,\'info\')">مشخصات</div>
                <?PHP } ?>
                <?PHP if ($this->mylib->holder_show("comment_form")) { ?>
                <div class="w3-bar-item w3-buttonn tablink w3-right" onclick="change_tabs(event,\'comment\')">نظرات کاربران</div>
                <?PHP } ?>
                <?PHP if ($this->mylib->holder_show("question_and_answer_form")) { ?>
                <div class="w3-bar-item w3-buttonn tablink w3-right" onclick="change_tabs(event,\'question\')">پرسش و پاسخ</div>
                <?PHP } ?>
            </div>

            <holder id="info" class="w3-container tab">attributes</holder>
            <holder id="comment" class="w3-container tab" style="display:none">comment_form</holder>
            <holder id="question" class="w3-container tab" style="display:none">question_and_answer_form</holder>
        </div>
    </div>';

		$show_type = $this->input->get('show_type');
		if (isset ($show_type) && $show_type == 'raw')
        {
            $product_html_container = str_replace("{{hide_in_raw_show_type}}"," hide_in_raw_show_type ",$product_html_container);
            $product_html_container = str_replace("{{show_in_raw_show_type}}"," show_in_raw_show_type ",$product_html_container);
        }
        else
        {
            $product_html_container = str_replace("{{hide_in_raw_show_type}}","",$product_html_container);
            $product_html_container = str_replace("{{show_in_raw_show_type}}","",$product_html_container);
        }

		$data = array();
		$comment_view = '';
		$question_and_answer_view = '';

		if($item_id)
		{
			$this->db->group_start();
			$this->db->where(array('id' => $item_id, 'publish' => 'yes'));
			$this->db->group_end();
			$this->db->group_start();
			$this->db->where(array('type_of_category' => 'virtual'));
			$this->db->or_where(array('number >' => 0));
			$this->db->or_where(array('finish' => 2));
			$this->db->or_where(array('finish' => 3));
			$this->db->group_end();
			$query = $this->db->get('add_products')->result();
		}

        $submitted_options = '';
        $option_fields = '';
		$div_info = '';
		if(isset($query))
		{
			if($query != array())
			{
				foreach ($query as $row)
				{
					$this->session->set_userdata('page_title', $row->title);
					$this->session->set_userdata('page_title_alias_url', $row->title_alias_url);
					$this->session->set_userdata('page_meta_tag_title', $row->meta_tag_title);
					$this->session->set_userdata('page_meta_tag_keywords', $row->meta_tag_keywords);
					$this->session->set_userdata('page_meta_tag_description', $row->meta_tag_description);
					$json_pic = json_decode($row->primary_pic);
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
					$pic_src = base_url('content/products/thumb/'."$pic_name");
					$product_link = base_url('pages/single_product/'.$row->id);
					$div_info = 'class="product_holder product_item_'.$row->id.'" id="product_item_'.$row->id.'" product_title="'.$row->title.'" product_link="'.$product_link.'" product_price="'.$row->price.'" product_id="'.$row->id.'" attr=\'{"tedad":1}\' product_first_image_src="'.$pic_src.'"';
					//submitted options
					$submitted_options = $row->options;
					$submitted_options = str_replace('\r', '', $submitted_options);
					$submitted_options = str_replace('\n', '::new_line::', $submitted_options);

					$category_id = $row->category;

					$this->db->where(array('category' => $category_id));
					$option_fields = json_encode($this->db->get('options')->result());
					$option_fields = str_replace('\r', '', $option_fields);
					$option_fields = str_replace('\n', '::new_line::', $option_fields);

					$this->db->where(array('id' => $category_id));
					$this->db->select('title,customized_template,template');
					$temp_result_category = $this->db->get('products_category')->result();

					$category_title = '';
					foreach ($temp_result_category as $row_category)
					{
						$category_title = $row_category->title;
						$customized_template = $row_category->customized_template;
						$template = $row_category->template;
					}

					if(isset($customized_template) && isset($template))
					{
						if($customized_template == 'yes')
						{
							$product_html_container = $template;
						}
					}
					$brand_id = $row->brand;

					$this->db->where(array('id' => $brand_id));
					$this->db->select('title');
					$temp_result_brand = $this->db->get('brands')->row();
					$brand_title = '';

					if($temp_result_brand != null && $temp_result_brand != '')
					{
						$brand_title = $temp_result_brand->title;
					}

					$price = (float)$row->price;
					$discount_price = 0;

					if(isset($row->discount_amount))
					{
						if($row->type_of_discount == 'percentage')
						{
							$discount_amount = (float)$row->discount_amount;
							$discount_price = $discount_amount*$price/100;
						}
						elseif($row->type_of_discount == 'static_value')
						{
							$discount_price = $row->discount_amount;
						}
					}
					$discounted_price = $price-$discount_price;

					if($discount_price == 0)
					{
						$price = 0;
					}

                    //convert to float
                    $discount_price = floatval($discount_price);
                    $price = floatval($price);
                    $discounted_price = floatval($discounted_price);
					$price = number_format($price);
					$discount_price = number_format($discount_price);
					$discounted_price = number_format($discounted_price);

					$this->load->library('date_shamsi');

					$this->db->order_by('insert_date', 'DESC');
					$this->db->where(array('product_id' => $item_id, 'publish' => 'yes'));
					$comment_query = $this->db->get('comment');
					foreach($comment_query->result() as $row_com_qu)
					{
						$checked_1 = '';
						if($row_com_qu->rate > 0)
						{
							$checked_1 = 'checked';
						}
						$checked_2 = '';
						if($row_com_qu->rate > 1)
						{
							$checked_2 = 'checked';
						}
						$checked_3 = '';
						if($row_com_qu->rate > 2)
						{
							$checked_3 = 'checked';
						}
						$checked_4 = '';
						if($row_com_qu->rate > 3)
						{
							$checked_4 = 'checked';
						}
						$checked_5 = '';
						if($row_com_qu->rate > 4)
						{
							$checked_5 = 'checked';
						}

						$insert_date_comment = '';
						if($row_com_qu->insert_date != null && $row_com_qu->insert_date != '' && is_numeric($row_com_qu->insert_date))
						{
							$insert_date_comment = $this->date_shamsi->jdate('o/m/j', $row_com_qu->insert_date,'','Asia/Tehran', 'fa');
						}

						$comment_view .= ' <div class="w3-containerrr">
        <div class="w3-row w3-border w3-padding w3-light-gray">
            <div class="w3-col m6">
              '.$row_com_qu->first_name.'
            </div>
            <div class="w3-col w3-left-align m6">
             '.$insert_date_comment.'
            </div>
        </div>
        <div>
            <div class="w3-border w3-padding w3-margin-bottom">
                '.$row_com_qu->full_comment.'
                <div class="w3-row">
                    <div class="w3-col s9 m10 rating_stars">
                        <a class="star hover"><span class="fa fa-star '.$checked_1.'"></span></a>
                        <a class="star hover"><span class="fa fa-star '.$checked_2.'"></span></a>
                        <a class="star hover"><span class="fa fa-star '.$checked_3.'"></span></a>
                        <a class="star"><span class="fa fa-star '.$checked_4.'"></span></a>
                        <a class="star"><span class="fa fa-star '.$checked_5.'"></span></a>
                    </div class="w3-col s3 m2">
                </div>
                '.($row_com_qu->answer != '' && $row_com_qu->answer != null ? '<hr><div><span class="bold">پاسخ مدیر سایت:</span class="bold">
                    <span>'.$row_com_qu->answer.'</span></div>' : "" ).'
            </div>
        </div>
    </div>';
					}

					$this->db->order_by('insert_date', 'DESC');
					$this->db->where(array('product_id' => $item_id, 'publish' => 'yes'));
					$question_and_answer_query = $this->db->get('question_and_answer');
					foreach($question_and_answer_query->result() as $row_qu_an)
					{
						$insert_date_qu_an = '';
						if($row_qu_an->insert_date != null && $row_qu_an->insert_date != '' && is_numeric($row_qu_an->insert_date))
						{
							$insert_date_qu_an = $this->date_shamsi->jdate('o/m/j', $row_qu_an->insert_date,'','Asia/Tehran', 'fa');
						}
						$question_and_answer_view .= '<div class="w3-containerrr">
        <div class="w3-row w3-border w3-padding w3-light-gray">
            <div class="w3-col m6">
              '.$row_qu_an->first_name.'
            </div>
            <div class="w3-col w3-left-align m6">
              '.$insert_date_qu_an.'
            </div>
        </div>
        <div>
            <div class="w3-border w3-padding w3-margin-bottom">
                '.$row_qu_an->question.'
               '.($row_qu_an->answer != '' && $row_qu_an->answer != null ? '<hr><div><span class="bold">پاسخ مدیر سایت:</span class="bold">
                    <span>'.$row_qu_an->answer.'</span></div>' : "").'
            </div>
        </div>
    </div>';
					}

					$data = array(
						'id' => $row->id,
						'title' => $row->title,
						'category_id' => $category_id,
						'description' => $row->description,
						'brand' => $brand_title,
						'price' => $price,
						'discount_price' => $discount_price,
						'discounted_price' => $discounted_price,
						'category' => $category_title,
					    'comment_view' => $comment_view,
						'question_and_answer_view' => $question_and_answer_view,
						'number' => $row->number,
						'finish' => $row->finish,
						'type_of_category' => $row->type_of_category,
						'the_comment_registration_section_is_enabled' => $row->the_comment_registration_section_is_enabled,
						'there_is_a_possibility_to_register_new_comments_for_the_user' => $row->there_is_a_possibility_to_register_new_comments_for_the_user,
						'the_questions_and_answers_registration_section_is_active' => $row->the_questions_and_answers_registration_section_is_active,
						'possibility_to_register_new_questions_and_answers_for_the_user' => $row->possibility_to_register_new_questions_and_answers_for_the_user
					);
				}
			}
			else
			{
				$data = array(
					'title' => '',
					'description' => '',
					'brand' => '',
					'category' => '',
					'comment_view' => '',
					'question_and_answer_view' => '',
					'number' => '',
					'finish' => '',
					'type_of_category' => '',
					'the_comment_registration_section_is_enabled' => '',
					'there_is_a_possibility_to_register_new_comments_for_the_user' => '',
					'the_questions_and_answers_registration_section_is_active' => '',
					'possibility_to_register_new_questions_and_answers_for_the_user' => ''
				);
				$product_html_container = 'محصول مورد نظر یافت نشد.';
			}
		}
		else
		{
			//محصولی موجود نمیباشد
			$data = array(
				'title' => '',
				'description' => '',
				'brand' => '',
				'category' => '',
				'comment_view' => '',
				'question_and_answer_view' => '',
				'number' => '',
				'finish' => '',
				'type_of_category' => '',
				'the_comment_registration_section_is_enabled' => '',
				'there_is_a_possibility_to_register_new_comments_for_the_user' => '',
				'the_questions_and_answers_registration_section_is_active' => '',
				'possibility_to_register_new_questions_and_answers_for_the_user' => ''
			);
			$product_html_container = 'محصول مورد نظر یافت نشد.';
		}

		$product_html_container = "<div $div_info>$product_html_container</div>";
		$product_html_container = $this->load->view('pages/single_product', array('product_html_container' => $product_html_container, 'submitted_options' => $submitted_options, 'option_fields' => $option_fields), true);

		$holder_out = $this->mylib->replace_modules_in_holder($data, $product_html_container);

		//$this->mylib->holder_show('title');

        $holder_out["html_content"] = "?>".$holder_out["html_content"];

        ob_start();
        eval ($holder_out["html_content"]);
        $holder_out['html_content'] = ob_get_contents();
        ob_end_clean();

		$position_out = $this->mylib->replace_modules_in_position($holder_out['html_content']);

		$this->load->view('template/header');

		$this->output->append_output($position_out['html_content']);

		$this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
	}

	public function single_category($item_id = null, $menu_id = null)
	{
		$tracking_code = $this->input->get('tr');
		if(isset($tracking_code))
		{
			$tr_temp_o = '';
			if($this->session->has_userdata('tr') && $this->session->userdata('tr') != $tracking_code)
			{
				$tr_temp_o = 'yes';
			}
			if(!$this->session->has_userdata('tr') || $tr_temp_o == 'yes')
			{
				$this->session->set_userdata('tr', $tracking_code);
				$this->db->set('number_of_click', 'number_of_click+1', FALSE);
				$this->db->where(array('tracking_code' => $tracking_code));
				$this->db->update('campaignes');
			}
		}

		global $current_menu_id;
		$current_menu_id = $menu_id;

		$data = array();
		$data['category_name'] = '';
		if($item_id != null && $item_id != 0 && is_numeric($item_id))
		{
			$this->db->where(array('id' => $item_id));
			$single_category_query = $this->db->get('article_categories');
			foreach($single_category_query->result() as $sin_ca_row)
			{
				$data['category_name'] = $sin_ca_row->title;
				$this->session->set_userdata('page_title', $sin_ca_row->title);
				$this->session->set_userdata('page_title_alias_url', $sin_ca_row->title_alias_url);
				$this->session->set_userdata('page_meta_tag_title', $sin_ca_row->meta_tag_title);
				$this->session->set_userdata('page_meta_tag_keywords', $sin_ca_row->meta_tag_keywords);
				$this->session->set_userdata('page_meta_tag_description', $sin_ca_row->meta_tag_description);
			}
		}

		$data['content'] = '';
		$per_page = $this->input->get('per_page');
		$page_no = $this->input->get('page_no');
		if(!isset($per_page) || !is_numeric($per_page) || $per_page <= 0)
		{
			$per_page = 20;
		}
		if(!isset($page_no) || !is_numeric($page_no) || $page_no <= 0)
		{
			$page_no = 1;
		}
		$data['item_data_per_page'] = $per_page;

		$this->db->where(array('parent' => $item_id, 'publish' => 'yes'));
		$query_count = count($this->db->get('articles')->result());

		if($page_no == 1)
		{
			$p_page = 0;
		}
		else
		{
			$p_page = $page_no - 1;
			$p_page = $per_page * $p_page;
		}

		$this->db->where(array('parent' => $item_id, 'publish' => 'yes'));
		$this->db->order_by('id', 'DESC');
		$this->db->limit($per_page, $p_page);
		$query = $this->db->get('articles');
		foreach ($query->result() as $row)
		{
			$data['content'] .= '<div class="category_list w3-margin-top">
                    <!--<a class="image w3-margin-left" href="#">
                        <img src="http://amya.ir/demo/keshavarz/content/products/thumb/no_pic.jpg">
                    </a>-->
                    <a href="'.base_url('pages/single_page/'.$row->id).'">
                    <div class="title_description">'.$row->title.'</div>
                    <div class="description">'.$row->intro.'</div></a>
                    <div class="w3-row">
                        <a href="'.base_url('pages/single_page/'.$row->id).'" class="read_more w3-amber w3-padding w3-margin w3-round w3-left w3-small">ادامه مطلب...</a>
                    </div>
                    <hr>
                </div>';
		}

		$temp_pagination = '';
		$data['pagination'] = '';
		if($query_count > 0 && is_numeric($per_page) && $per_page != 0)
		{
			$result_count_temp = $query_count/$per_page;
			$result_count = round($result_count_temp,0,PHP_ROUND_HALF_UP);
			if($result_count_temp > $result_count)
			{
				$result_count++;
			}
			if($result_count > 0)
			{
				for($i = 1; $i<= $result_count; $i++)
				{
					$temp_pagination .= '<a href="'.base_url('pages/single_category/'.$item_id.'/?page_no='.$i.'&per_page='.$per_page.'').'" class="w3-button w3-hover-gray">'.$i.'</a>';
				}
				$next_page = $page_no+1;
				$prev_page = $page_no-1;
				$data['pagination'] = '<div class="pagination w3-bar w3-center w3-margin-bottom">
				                       <a href="'.($prev_page > 0 ? base_url('pages/single_category/'.$item_id.'/?page_no='.$prev_page.'&per_page='.$per_page.'') :"#").'" class="w3-button w3-hover-gray">&laquo;</a>
				                          '.$temp_pagination.'
				                           <a href="'.($next_page <= $result_count ? base_url('pages/single_category/'.$item_id.'/?page_no='.$next_page.'&per_page='.$per_page.'') :"#").'" class="w3-button w3-hover-gray">&raquo;</a>
				                           </div>';
			}

		}

		$modules_id = $this->mylib->modules_id_not_show($menu_id);
		$content = $this->load->view('pages/single_category', $data, true);
		$position_out = $this->mylib->replace_modules_in_position($content, $modules_id);

		$this->load->view('template/header');
		$this->output->append_output($position_out['html_content']);
		$this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
	}

	public function single_product_category($item_id = null, $menu_id = null)
	{
		$tracking_code = $this->input->get('tr');
		if(isset($tracking_code))
		{
			$tr_temp_o = '';
			if($this->session->has_userdata('tr') && $this->session->userdata('tr') != $tracking_code)
			{
				$tr_temp_o = 'yes';
			}
			if(!$this->session->has_userdata('tr') || $tr_temp_o == 'yes')
			{
				$this->session->set_userdata('tr', $tracking_code);
				$this->db->set('number_of_click', 'number_of_click+1', FALSE);
				$this->db->where(array('tracking_code' => $tracking_code));
				$this->db->update('campaignes');
			}
		}

		global $current_menu_id;
		$current_menu_id = $menu_id;
		$data = array();
		$temp_products = '';
		$per_page = $this->input->get('per_page');
		$page_no = $this->input->get('page_no');
		if(!isset($per_page) || !is_numeric($per_page) || $per_page <= 0)
		{
			$per_page = 20;
		}
		if(!isset($page_no) || !is_numeric($page_no) || $page_no <= 0)
		{
			$page_no = 1;
		}
		$data['item_data_per_page'] = $per_page;

		$data['product_category_title'] = '';
		if(isset($item_id))
		{
			$this->db->where(array('id' => $item_id, 'publish' => 'yes'));
			$product_category_query = $this->db->get('products_category');
			foreach($product_category_query->result() as $pr_ca_row)
			{
				$product_category_title = $pr_ca_row->title;
				$data['product_category_title'] = $product_category_title;
				$this->session->set_userdata('page_title', $pr_ca_row->title);
				$this->session->set_userdata('page_title_alias_url', $pr_ca_row->title_alias_url);
				$this->session->set_userdata('page_meta_tag_title', $pr_ca_row->meta_tag_title);
				$this->session->set_userdata('page_meta_tag_keywords', $pr_ca_row->meta_tag_keywords);
				$this->session->set_userdata('page_meta_tag_description', $pr_ca_row->meta_tag_description);
			}
		}

		$data['show'] = 'no';
		if(isset($item_id))
		{
			$this->db->group_start();
			$this->db->where(array('category' => $item_id, 'publish' => 'yes'));
			$this->db->group_end();
			$this->db->group_start();
			$this->db->where(array('type_of_category' => 'virtual'));
			$this->db->or_where(array('number >' => 0));
			$this->db->or_where(array('finish' => 2));
			$this->db->or_where(array('finish' => 3));
			$this->db->group_end();
			$show_query = $this->db->get('add_products')->result();
			if($show_query != '' && $show_query != null && $show_query != array())
			{
				$data['show'] = 'yes';
			}
		}
		else
		{
			$data['show'] = 'yes';
		}

		////////////////////////////////////////////
		/////////calculate count of result/////////
		//////////////////////////////////////////
		if(isset($item_id))
		{
			$this->db->where(array('category' => $item_id, 'publish' => 'yes'));
		}
		else
		{
			$this->db->where(array('publish' => 'yes'));
		}
		$query_field = $this->db->get('add_field');

		$k_temp = 1;
		foreach($query_field->result() as $fi_row)
		{
			$field_get_temp = $this->input->get('field'.$fi_row->id);
			if(isset($field_get_temp) && $field_get_temp != '' && $field_get_temp != null)
			{
				$k_temp++;
				break;
			}
		}
		$k = 1;
		$this->db->group_start();
		if(isset($item_id))
		{
			$this->db->where(array('category' => $item_id, 'publish' => 'yes'));
		}
		else
		{
			$this->db->where(array('publish' => 'yes'));
		}
		$this->db->group_end();

		$products_available = $this->input->get('products_available');
		if(isset($products_available))
		{
			$this->db->group_start();
			$this->db->where(array('type_of_category' => 'virtual'));
			$this->db->or_where(array('number >' => 0));
			$this->db->group_end();
		}
		$data['products_available'] = $products_available;

		$search = $this->input->get('search');
		if(isset($search) && $search != '' && $search != null)
		{
			$this->db->group_start();
			$this->db->like('title', $search);
			$this->db->or_like('title_alias_url', $search);
			$this->db->or_like('meta_tag_title', $search);
			$this->db->or_like('meta_tag_keywords', $search);
			$this->db->or_like('meta_tag_description', $search);
			$this->db->or_like('fields', $search);
			$this->db->or_like('description', $search);
			$this->db->or_like('options', $search);
			$this->db->group_end();
		}
		$data['search'] = $search;

		if($k_temp == 2)
		{
			$this->db->group_start();
		}
		foreach($query_field->result() as $fi_row)
		{
			$field_get_temp = $this->input->get('field'.$fi_row->id);
			if(!is_array($field_get_temp))
			{
				if(isset($field_get_temp) && $field_get_temp != '' && $field_get_temp != null)
				{
					if($k == 1)
					{
						$this->db->like('fields', $field_get_temp);
					}
					else
					{
						$this->db->or_like('fields', $field_get_temp);
					}
					$k++;
				}
			}
			elseif(is_array($field_get_temp) && $field_get_temp != array())
			{
				foreach($field_get_temp as $ar_row)
				{
					if($ar_row != '' && $ar_row != null)
					{
						if($k == 1)
						{
							$this->db->like('fields', $ar_row);
						}
						else
						{
							$this->db->or_like('fields', $ar_row);
						}
						$k++;
					}
				}
			}
		}
		if($k_temp == 2)
		{
			$this->db->group_end();
		}
		$this->db->group_start();
		$this->db->where(array('type_of_category' => 'virtual'));
		$this->db->or_where(array('number >' => 0));
		$this->db->or_where(array('finish' => 2));
		$this->db->or_where(array('finish' => 3));
		$this->db->group_end();
		$query_products = $this->db->get('add_products')->result();
		$query_count = count($query_products);

		////////////////////////////////////////
		////// create result for search////////
		//////////////////////////////////////
		if($page_no == 1)
		{
			$p_page = 0;
		}
		else
		{
			$p_page = $page_no - 1;
			$p_page = $per_page * $p_page;
		}

		$sort = $this->input->get('sort');
		$data['item_data_sort'] = $sort;
		$most_popular_query = array();

		if($sort == 'newest')
		{
			$this->db->order_by('insert_date', 'DESC');
		}
		elseif($sort == 'the_oldest')
		{
			$this->db->order_by('insert_date', 'ASC');
		}
		elseif($sort == 'lowest_price')
		{
			$this->db->order_by('price', 'ASC');
		}
		elseif($sort == 'highest_price')
		{
			$this->db->order_by('price', 'DESC');
		}
		elseif($sort == 'most_popular')
		{
			$this->db->group_start();
			$this->db->where(array('rate !=' => '', 'product_id !=' => ''));
			$this->db->group_end();
			$this->db->group_start();
			$this->db->where(array('rate !=' => null, 'product_id !=' => null));
			$this->db->group_end();
			$this->db->group_start();
			$this->db->where(array('publish' => 'yes'));
			$this->db->group_end();
			$query_comment = $this->db->get('comment');

			$sort_array = array();
			foreach($query_products as $pr_row)
			{
				$tedad = 0;
				$majmo = 0;
				foreach($query_comment->result() as $co_row)
				{
                    if($pr_row->id == $co_row->product_id && is_numeric($co_row->rate))
					{
						$tedad++;
						$majmo = $majmo + $co_row->rate;
					}
				}
				if($tedad != 0 && $majmo != 0)
				{
					$miyangin = $majmo / $tedad;
					$sort_array[$pr_row->id] = $miyangin;
				}
			}
			arsort($sort_array);
			if($sort_array != array())
			{
				foreach($sort_array as $in_pr => $val_pr)
				{
					foreach($query_products as $pr_row)
					{
						if($in_pr == $pr_row->id)
						{
                            array_push($most_popular_query, $pr_row);
						}
					}
				}
			}
			$query_count = count($most_popular_query);
		}
		else
		{
			$this->db->order_by('insert_date', 'DESC');
			$data['item_data_sort'] = 'newest';
		}
		$k_temp = 1;
		foreach($query_field->result() as $fi_row)
		{
			$field_get_temp = $this->input->get('field'.$fi_row->id);
			if(isset($field_get_temp) && $field_get_temp != '' && $field_get_temp != null)
			{
				$k_temp++;
				break;
			}
		}
		$k = 1;

		$this->db->limit($per_page, $p_page);
		$this->db->group_start();
		if(isset($item_id))
		{
			$this->db->where(array('category' => $item_id, 'publish' => 'yes'));
		}
		else
		{
			$this->db->where(array('publish' => 'yes'));
		}
		$this->db->group_end();

		$products_available = $this->input->get('products_available');
		if(isset($products_available))
		{
			$this->db->group_start();
            $this->db->where(array('type_of_category' => 'virtual'));
            $this->db->or_where(array('number >' => 0));
			$this->db->group_end();
		}
		$data['products_available'] = $products_available;

		$search = $this->input->get('search');
		if(isset($search) && $search != '' && $search != null)
		{
			$this->db->group_start();
			$this->db->like('title', $search);
			$this->db->or_like('title_alias_url', $search);
			$this->db->or_like('meta_tag_title', $search);
			$this->db->or_like('meta_tag_keywords', $search);
			$this->db->or_like('meta_tag_description', $search);
			$this->db->or_like('fields', $search);
			$this->db->or_like('description', $search);
			$this->db->or_like('options', $search);
			$this->db->group_end();
		}
		$data['search'] = $search;

		if($k_temp == 2)
		{
			$this->db->group_start();
		}
		foreach($query_field->result() as $fi_row)
		{
			$field_get_temp = $this->input->get('field'.$fi_row->id);
			if(!is_array($field_get_temp))
			{
				if(isset($field_get_temp) && $field_get_temp != '' && $field_get_temp != null)
				{
					if($k == 1)
					{
						$this->db->like('fields', $field_get_temp);
					}
					else
					{
						$this->db->or_like('fields', $field_get_temp);
					}
					$k++;
				}
			}
			elseif(is_array($field_get_temp) && $field_get_temp != array())
			{
				foreach($field_get_temp as $ar_row)
				{
					if($ar_row != '' && $ar_row != null)
					{
						if($k == 1)
						{
							$this->db->like('fields', $ar_row);
						}
						else
						{
							$this->db->or_like('fields', $ar_row);
						}
						$k++;
					}
				}
			}
		}
		if($k_temp == 2)
		{
			$this->db->group_end();
		}
		$this->db->group_start();
		$this->db->where(array('type_of_category' => 'virtual'));
		$this->db->or_where(array('number >' => 0));
		$this->db->or_where(array('finish' => 2));
		$this->db->or_where(array('finish' => 3));
		$this->db->group_end();
		$query = $this->db->get('add_products')->result();
		if($sort == 'most_popular')
		{
			$start_slice = 0;
			if($page_no > 1)
			{
				$start_slice = $page_no - 1;
				$start_slice = $start_slice * $per_page;
			}
			$most_popular_query = array_slice($most_popular_query ,$start_slice ,$per_page ,true);
			$query = $most_popular_query;
		}
		$option_query = $this->db->get('options')->result();
		foreach ($query as $row)
		{
			$option_json_product = json_decode($row->options);
			$has_required_option = $this->mylib->has_required_option($option_json_product, $row->category, $option_query);
			$json_pic = json_decode($row->primary_pic);
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

			$pic_src = base_url('content/products/thumb/'."$pic_name");
			$product_link = base_url('pages/single_product/'.$row->id);

			$temp_products .= '<div class="w3-hover-shadowwww w3-col l3 s6 pro_card">
			<div class="w3-container w3-center product_holder product_item_'.$row->id.'" id="product_item_'.$row->id.'" product_title="'.$row->title.'" product_link="'.$product_link.'" product_price="'.$row->price.'" product_id="'.$row->id.'" attr=\'{"tedad":1}\' product_first_image_src="'.$pic_src.'" has_required_option="'.$has_required_option.'">
				<a href="'.$product_link.'">
					<div class="image"><img src='.$pic_src.'></div>
					<div class="title"><div class="limit_title">'.$row->title.'</div></div></a>
				<div class="price">'.number_format($row->price).' تومان</div>
				<div class="w3-bar">
					'.($row->finish == 3 || $row->type_of_category == 'virtual' || $row->number > 0 ? '<i class="fas fa-cart-plus w3-text-amberrrr w3-text-teal w3-bar-item w3-buttonnnn pointer w3-hover-text-black" title="'.lang('add_to_cart').'" onclick="add_to_cart('.$row->id.', this);"></i>' :"").'
					<i class="far fa-heart w3-text-red w3-bar-item w3-buttonnnn pointer w3-hover-text-black" title="'.lang('add_to_favorite_list').'" onclick="add_to_favorite('.$row->id.', this);"></i>
				</div>
			</div>
		</div>';
		}

		$temp_pagination = '';
		$data['pagination'] = '';
		if($query_count > 0 && is_numeric($per_page) && $per_page != 0)
		{
			$result_count_temp = $query_count/$per_page;
			$result_count = round($result_count_temp,0,PHP_ROUND_HALF_UP);
			if($result_count_temp > $result_count)
			{
				$result_count++;
			}
			if($result_count > 0)
			{
				$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				$query_string = parse_url($actual_link, PHP_URL_QUERY);
				for($i = 1; $i<= $result_count; $i++)
				{
					parse_str($query_string, $query_string_array);
					$query_string_array["page_no"] = $i;
					$new_url = "?";
					foreach ($query_string_array as $indx => $val)
					{
						if(is_array($val))
						{
							foreach($val as $val_2)
							{
								$new_url .= "$indx'[]'=$val_2&";
							}
						}
						else
						{
							$new_url .= "$indx=$val&";
						}
					}
					$temp_pagination .= '<a href="'.$new_url.'" class="w3-button w3-hover-gray">'.$i.'</a>';
				}

				$next_page = $page_no+1;
				parse_str($query_string, $query_string_array);
				$query_string_array["page_no"] = $next_page;
				$next_page_url = "?";
				foreach ($query_string_array as $indx => $val)
				{
					if(is_array($val))
					{
						foreach($val as $val_2)
						{
							$next_page_url .= "$indx'[]'=$val_2&";
						}
					}
					else
					{
						$next_page_url .= "$indx=$val&";
					}
				}

				$prev_page = $page_no-1;
				parse_str($query_string, $query_string_array);
				$query_string_array["page_no"] = $prev_page;
				$prev_page_url = "?";
				foreach ($query_string_array as $indx => $val)
				{
					if(is_array($val))
					{
						foreach($val as $val_2)
						{
							$prev_page_url .= "$indx'[]'=$val_2&";
						}
					}
					else
					{
						$prev_page_url .= "$indx=$val&";
					}
				}
				$data['pagination'] = '<div class="pagination w3-bar w3-center w3-margin-bottom">
				                       <a href="'.($prev_page > 0 ? $prev_page_url :"#").'" class="w3-button w3-hover-gray">&laquo;</a>
				                          '.$temp_pagination.'
				                           <a href="'.($next_page <= $result_count ? $next_page_url :"#").'" class="w3-button w3-hover-gray">&raquo;</a>
				                           </div>';
			}

		}

		$data['temp_products'] = $temp_products;

         ///////////////////////////////
		/////create field for search///
		//////////////////////////////
		$this->db->order_by('sort', 'ASC');
		if(isset($item_id))
		{
			$this->db->where(array('category' => $item_id, 'publish' => 'yes', 'searchable' => 'yes'));
		}
		else
		{
			$this->db->where(array('publish' => 'yes', 'searchable' => 'yes'));
		}
		$query_field = $this->db->get('add_field');

		$temp_search_field = '';
		$multiple_case_id_array = array();

		foreach ($query_field->result() as $row_field)
		{
//			print_r($row_field);
//			echo '#########';
			$field_get_set_val = $this->input->get('field'.$row_field->id);
			if($row_field->type == 'single_case' || $row_field->type == 'textarea')
			{
				$temp_search_field .= '<div class="author w3-margin-bottom">
                    <div>'.$row_field->title.':</div>
                    <div>
                        <input class="w3-margin-rightttt w3-padding-small w3-borderrrr input_search" type="text" name="field'.$row_field->id.'" value="'.set_value("field$row_field->id","$field_get_set_val").'">
                    </div>
                </div>';
			}
			elseif ($row_field->type == 'checkbox' || $row_field->type == 'select')
			{
				$insert_value = $row_field->insert_value;
				$insert_value = str_replace("\r\n","::new_line::",$insert_value);
				$value_array = explode("::new_line::", $insert_value);

				$temp_value = '';
				foreach ($value_array as $value)
				{
					$checkbox_set_val = '';
					if(is_array($field_get_set_val) && $value != '' && $value != null && !is_array($value))
					{
						if(in_array($value, $field_get_set_val))
						{
							$checkbox_set_val = 'checked';
						}
					}
					$temp_value .= '<label class="checkbox_container">
                        <input class="w3-margin-rightttt" type="checkbox" value="'.$value.'" name="field'.$row_field->id.'[]" '.$checkbox_set_val.'>
                        <span class="checkmark"></span>
                        <span>'.$value.'</span>
                    </label>';
				}

				$temp_search_field .= '<div class="author w3-margin-bottom">
                    <div>'.$row_field->title.':</div>
                    '.$temp_value.'
                    <div class="w3-hide">
                        <a href="#" class="w3-tiny w3-text-gray w3-margin-right">+ مشاهده موارد بیشتر</a>
                    </div>
                </div>';
			}
			elseif ($row_field->type == 'multiple_case')
			{
                array_push($multiple_case_id_array, $row_field->id);
			}
		}

		$temp_array = array();
		$no_tekrar = array();

		if($multiple_case_id_array != array())
		{
			$this->db->select('fields');
			$this->db->group_start();
			$this->db->where(array('publish' => 'yes'));
			$this->db->group_end();
			$this->db->group_start();
			$this->db->where(array('type_of_category' => 'virtual'));
			$this->db->or_where(array('number >' => 0));
			$this->db->or_where(array('finish' => 2));
			$this->db->or_where(array('finish' => 3));
			$this->db->group_end();
			$query_multiple_case = $this->db->get('add_products')->result();

			foreach ($query_multiple_case as $row_case)
			{
				$jason_fields = json_decode($row_case->fields);
				foreach ($jason_fields as $index => $value_field)
				{
					if(in_array($index, $multiple_case_id_array))
					{
						if($value_field != null && $value_field != '')
						{
							$value_array = explode("::new_line::", $value_field);
							$value_array = array_unique($value_array);
							$temp_no_tekrar = array();

							foreach ($value_array as $index5 => $value5)
							{
								if(!in_array($value5, $no_tekrar))
								{
									array_push($no_tekrar, $value5);
									array_push($temp_no_tekrar, $value5);
								}
							}

							$temp_value = '';
							foreach ($temp_no_tekrar as $value1)
							{
								$field_get_set_val = $this->input->get('field'.$index);
								$multiple_case_set_val = '';
								if(isset($field_get_set_val) && is_array($field_get_set_val) && $value1 != '' && $value1 != null && !is_array($value1))
								{
									if(in_array($value1, $field_get_set_val))
									{
										$multiple_case_set_val = 'checked';
									}
								}
								$temp_value .= '<label class="checkbox_container">
                        <input class="w3-margin-rightttt" type="checkbox" value="'.$value1.'" name="field'.$index.'[]" '.$multiple_case_set_val.'>
                        <span class="checkmark"></span>
                        <span>'.$value1.'</span>
                    </label>';
							}

							if (isset($temp_array[$index]))
							{
								$temp_array[$index] .= $temp_value;
							}
							else
							{
								$temp_array[$index] = $temp_value;
							}

							/*foreach ($temp_array as $index2 => $value2)
							{
								if($index2 == $index)
								{
									$temp_array[$index2] .= $temp_value;
									$te_te = 1;
								}
							}

							if(!isset($te_te))
							{
								$temp_array[$index] = $temp_value;
							}*/
						}
					}
				}
			}
		}

		foreach ($temp_array as $index3 => $value3)
		{
			$this->db->where(array('id' => $index3, 'publish' => 'yes'));
			$this->db->select('title');
			$field_title = $this->db->get('add_field')->row();

			if(isset($field_title->title))
			{
				$field_title = $field_title->title;
			}
			else
			{
				$field_title = '';
			}

			$temp_search_field .= '<div class="author w3-margin-bottom">
                    <div>'.$field_title.':</div>
                    '.$value3.'
                    <div class="w3-hide">
                        <a href="#" class="w3-tiny w3-text-gray w3-margin-right">+ مشاهده موارد بیشتر</a>
                    </div>
                </div>';
		}
		$data['search_field'] = $temp_search_field;

        //////////////////////////////////////////////////////////////
		$modules_id = $this->mylib->modules_id_not_show($menu_id);
		$content = $this->load->view('pages/single_product_category', $data, true);
		$position_out = $this->mylib->replace_modules_in_position($content, $modules_id);

		$this->load->view('template/header');

		$this->output->append_output($position_out['html_content']);

		$this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
	}

	public function site_map($menu_id = null)
	{
		$tracking_code = $this->input->get('tr');
		if(isset($tracking_code))
		{
			$tr_temp_o = '';
			if($this->session->has_userdata('tr') && $this->session->userdata('tr') != $tracking_code)
			{
				$tr_temp_o = 'yes';
			}
			if(!$this->session->has_userdata('tr') || $tr_temp_o == 'yes')
			{
				$this->session->set_userdata('tr', $tracking_code);
				$this->db->set('number_of_click', 'number_of_click+1', FALSE);
				$this->db->where(array('tracking_code' => $tracking_code));
				$this->db->update('campaignes');
			}
		}
		if($menu_id)
		{
			$this->db->where(array('id' => $menu_id));
			$site_menu_query = $this->db->get('menu');
			foreach($site_menu_query->result() as $sit_row)
			{
				$this->session->set_userdata('page_title', $sit_row->title);
				$this->session->set_userdata('page_title_alias_url', $sit_row->title_alias_url);
				$this->session->set_userdata('page_meta_tag_title', $sit_row->meta_tag_title);
				$this->session->set_userdata('page_meta_tag_keywords', $sit_row->meta_tag_keywords);
				$this->session->set_userdata('page_meta_tag_description', $sit_row->meta_tag_description);
			}
		}

		$this->load->model('menu/menu_model');
		$created_menus = '';

		$this->db->select('id,title');
		$this->db->where(array('publish' => 'yes'));
		$query = $this->db->get('menu_category');
		foreach ($query->result() as $row)
		{
			$temp_menu_array = $this->menu_lib->get_category_menus($row->id, 1);

			$title_category_menu = '<div class="w3-large">'.$row->title.'</div>';

			$created_menus .= $title_category_menu.$this->menu_model->create_menu_item ($temp_menu_array);
		}
		$data = array('created_menus' => $created_menus);

		$modules_id = $this->mylib->modules_id_not_show($menu_id);
		$content = $this->load->view('pages/site_map', $data, true);
		$position_out = $this->mylib->replace_modules_in_position($content, $modules_id);

		$this->load->view('template/header');

		$this->output->append_output($position_out['html_content']);

		$this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
	}

	public function comparison($menu_id = null)
	{
		$tracking_code = $this->input->get('tr');
		if(isset($tracking_code))
		{
			$tr_temp_o = '';
			if($this->session->has_userdata('tr') && $this->session->userdata('tr') != $tracking_code)
			{
				$tr_temp_o = 'yes';
			}
			if(!$this->session->has_userdata('tr') || $tr_temp_o == 'yes')
			{
				$this->session->set_userdata('tr', $tracking_code);
				$this->db->set('number_of_click', 'number_of_click+1', FALSE);
				$this->db->where(array('tracking_code' => $tracking_code));
				$this->db->update('campaignes');
			}
		}
		$this->session->set_userdata('page_title', 'مقایسه‌ی محصولات');

		$get_id = $this->input->get('selected_item');
		$post_id = $this->input->post('selected_item');
		if(isset($get_id))
		{
			$id = $get_id;
		}
		elseif(isset($post_id))
		{
			$id = $post_id;
		}

		$get_task = $this->input->get('task');
		$post_task = $this->input->post('task');
		if(isset($get_task))
		{
			$task = $get_task;
		}
		elseif(isset($post_task))
		{
			$task = $post_task;
		}

		$comparison_selected_id = array();
		if(isset($id) && isset($task))
		{
			if($task == 'add')
			{
				if($this->session->has_userdata('comparison_selected_id'))
				{
					$comparison_selected_id = $this->session->userdata('comparison_selected_id');
					if(!in_array($id, $comparison_selected_id))
					{
						array_push($comparison_selected_id, $id);
					}
					$this->session->set_userdata('comparison_selected_id', $comparison_selected_id);
				}
				else
				{
					array_push($comparison_selected_id, $id);
					$this->session->set_userdata('comparison_selected_id', $comparison_selected_id);
				}
			}
			elseif($task == 'delete')
			{
				if($this->session->has_userdata('comparison_selected_id'))
				{
					$comparison_selected_id = $this->session->userdata('comparison_selected_id');
					$id_temp_array = array();
					array_push($id_temp_array, $id);
					$comparison_selected_id = array_diff($comparison_selected_id, $id_temp_array);
					if($comparison_selected_id == array())
					{
						$this->session->unset_userdata('comparison_selected_id');
					}
					else
					{
						$this->session->set_userdata('comparison_selected_id', $comparison_selected_id);
					}
				}
			}
		}

		$temp_field_2 = '';
		if($this->session->has_userdata('comparison_selected_id'))
		{
			$comparison_selected_id = $this->session->userdata('comparison_selected_id');

			$i = 1;
			if(is_array($comparison_selected_id) && $comparison_selected_id != array())
			{
				$this->db->group_start();
			}
			foreach($comparison_selected_id as $selected_id)
			{
				if($i == 1)
				{
					$this->db->where(array('id' => $selected_id));
			    }
				else
				{
					$this->db->or_where(array('id' => $selected_id));
				}
				$i++;
			}
			if(is_array($comparison_selected_id) && $comparison_selected_id != array())
			{
				$this->db->group_end();
			}
			$this->db->group_start();
			$this->db->where(array('publish' => 'yes'));
			$this->db->group_end();
			$this->db->group_start();
			$this->db->where(array('type_of_category' => 'virtual'));
			$this->db->or_where(array('number >' => 0));
			$this->db->or_where(array('finish' => 2));
			$this->db->or_where(array('finish' => 3));
			$this->db->group_end();
			$products_query = $this->db->get('add_products');

			$this->db->select('id,title');
			$this->db->where(array('comparability' => 'yes', 'publish' => 'yes'));
			$field_query = $this->db->get('add_field');

			$temp_field_1 = '';
			$temp_products = '';
			foreach($field_query->result() as $field_row)
			{
				$temp_field_1 .= '<tr><td><b>'.$field_row->title.'</b></td>';
				foreach($products_query->result() as $products_row)
				{
					$field_id = $field_row->id;
					$json_fields = json_decode($products_row->fields);
					if(isset($json_fields->$field_id))
					{
						$product_attr = $json_fields->$field_id;
					}
					else
					{
						$product_attr = '';
					}
					$product_attr = str_replace("::new_line::","، ",$product_attr);
					$temp_field_1 .= '<td>'.$product_attr.'</td>';
				}
				$temp_field_1 .= '</tr>';
			}
			foreach($products_query->result() as $products_row)
			{
				$json_pic = json_decode($products_row->primary_pic);
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
				$pic_src = base_url('content/products/thumb/'."$pic_name");
				$product_link = base_url('pages/single_product/'.$products_row->id);

				$temp_products .= ' <th scope="col">
                    <button onclick="delete_from_compare_list(this, '.$products_row->id.');" class="w3-button w3-round w3-padding-small w3-red margin-bottom" style="width: 80px">&times;</button>
                    <input class="selected_before_to_compare" value="'.$products_row->id.'" type="hidden">
                    <img src="'.$pic_src.'">
                    <div><a href="'.$product_link.'">'.$products_row->title.'</a></div>
                </th>';
			}

			$temp_field_2 = '<tr>
                <th scope="col"></th>
               '.$temp_products.'
            </tr>
            </thead>
            <tbody>
            '.$temp_field_1.'
            </tbody>';
		}

		$data = array(
			'page_name' => 'comparison',
			'comparison_view' => $temp_field_2
		     );
		$modules_id = $this->mylib->modules_id_not_show($menu_id);
		$content = $this->load->view('pages/comparison', $data, true);
		$position_out = $this->mylib->replace_modules_in_position($content, $modules_id);

		$this->load->view('template/header');

		$this->output->append_output($position_out['html_content']);

		$this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
	}

    public function test()
    {
        $this->load->view('template/header');
        $this->load->view('pages/confirm_recover');
        $this->load->view('template/footer');
    }

}
