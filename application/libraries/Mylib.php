<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mylib {
	protected $CI;

	public function __construct()
	{
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();
		$this->CI->load->library('session');
		$this->CI->load->helper('url');
	}

	public function count_modules($position_name = null)
	{
		global $menu_id;
		$modules_id_not_show = $this->CI->mylib->modules_id_not_show($menu_id);
		$modules_id_3 = array();

		$this->CI->db->where(array('position' => $position_name));
		$this->CI->db->select('id');
		$modules_id_2 = $this->CI->db->get('modules')->result();
		foreach ($modules_id_2 as $value)
		{
			array_push($modules_id_3, $value->id);
		}
		$number_modules1 = array_diff($modules_id_3, $modules_id_not_show);

		return count($number_modules1);
	}

	public function modules_id_not_show($menu_id = null)
	{
		$modules_id = array();

		$this->CI->db->where(array('menu_assignment' => 'selected_pages'));
		$query_2 = $this->CI->db->get('modules');
		foreach ($query_2->result() as $row_2)
		{
			$temp = '';
			$selected_pages = '';

			$selected_pages_json = json_decode($row_2->selected_pages);
			if(isset($selected_pages_json->pages_id))
			{
				$selected_pages = $selected_pages_json->pages_id;
			}

			if($selected_pages != '')
			{
				if (in_array($menu_id, $selected_pages))
				{
					$temp = 'yes show';
				}
			}

			if($temp == '')
			{
				array_push($modules_id, $row_2->id);
			}
		}

		$this->CI->db->where(array('menu_assignment' => 'all_except_selected'));
		$query_3 = $this->CI->db->get('modules');
		foreach ($query_3->result() as $row_3)
		{
			$temp = '';
			$selected_pages = '';

			$selected_pages_json = json_decode($row_3->selected_pages);
			if(isset($selected_pages_json->pages_id))
			{
				$selected_pages = $selected_pages_json->pages_id;
			}

			if($selected_pages != '')
			{
				if (in_array($menu_id, $selected_pages))
				{
					$temp = 'not show';
				}
			}

			if($temp == 'not show')
			{
				array_push($modules_id, $row_3->id);
			}
		}

		$this->CI->db->select('id');
		$this->CI->db->where(array('publish' => 'no'));
		$query_4 = $this->CI->db->get('modules')->result();
		foreach($query_4 as $row_4)
		{
			array_push($modules_id, $row_4->id);
		}

		return $modules_id;
	}

	public function replace_modules_in_position($content = null, $modules_id = null)
	{

		$this->CI->load->library('simple_html_dom');
		$html = new simple_html_dom();
		$html->load($content, true, false);

		////////////////////////////////////////////////////////////////////////////////
		$this->CI->db->order_by('sort', 'ASC');
		foreach($html->find('position') as $index => $position)
		{
			if ($index == 0)
			{
				$this->CI->db->where(array('position' => $position->plaintext));
			}
			else
			{
				$this->CI->db->or_where(array('position' => $position->plaintext));
			}
		}
		$query = $this->CI->db->get('modules');

		$bottom_scripts = '';
		foreach($html->find('position') as $position)
		{
			$temp_module_html = '';
			foreach($query->result() as $row)
			{
				$temp_not_show = '';
				if($modules_id != null)
				{
					if (in_array($row->id, $modules_id))
					{
						$temp_not_show = 'not show';
					}
				}

				if ($position->plaintext == $row->position && $temp_not_show == '')
				{
					if ($position->type == '')
					{
						$position->type = 'default';
					}

					$view_directory = 'system/'.$row->type.'/'.$position->type;

					if (file_exists(APPPATH.'views/' . $view_directory.'.php'))
					{
						switch ($row->type)
						{
                            case 'custom_html':
                                $temp_module_html .= $this->CI->load->view($view_directory, array('module' => $row), true);
                                break;

                            case 'cart':
                                $temp_module_html .= $this->CI->load->view($view_directory, array('module' => $row), true);
                                break;

							case 'simple_search':

								////////////////////////////////////////////////////
								// Create A list (HTML Select list) of categories //
								////////////////////////////////////////////////////
								$this->CI->db->select('id,title');
								$this->CI->db->where(array('publish' => 'yes'));
								$category_list_query = $this->CI->db->get('products_category');

								$category = $this->CI->input->post('category');

								$categories_list = "<option value='all'>".lang ('all')."</option>";

								//PHP 5 //if (count($category_list_query) > 0)
								if ($category_list_query->num_rows() > 0)
								{
									foreach ($category_list_query->result() as $row)
									{
										$categories_list .= '<option value="'.$row->id.'" '.set_select('category', $row->id, ($row->id == $category ? true : false)).'>'.$row->title.'</option>';
									}
								}

								if ($categories_list == '')
								{
									$categories_list = "<option value=''>".lang ('no_category')."</option>";
								}

								$temp_simple_search_body = '<form method="get" action="'.base_url('search').'" class="simple_search">
                    <div class="search_holder w3-row">
                        <input class="w3-col s7 w3-input w3-border w3-padding-small w3-border border-top-right-radius w3-leftttttt search_field" placeholder="عبارت مورد نظر خود را وارد نمایید." name="search_field" type="text" autocomplete="nope" oninput="find_products(this);">
                        <select class="w3-col s3 w3-select w3-right-align w3-border category w3-lefttttt" name="category" type="text" onchange="find_products(this.parentElement.getElementsByClassName(\'search_field\')[0]);">
                            '.$categories_list.'
                        </select>
                        <button type="submit" class="w3-col s2 w3-button w3-leftttttt"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="found_items_holder"></div>
                </form>';

								$row->body = $temp_simple_search_body;
								$temp_module_html .= $this->CI->load->view($view_directory, array('module' => $row), true);
								break;

                            case 'menu':
                                 $this->CI->db->select('publish');
                                 $this->CI->db->where(array('id' => $row->target));
                                 $menu_cat_qu = $this->CI->db->get('menu_category')->row();

                                 if(isset($menu_cat_qu->publish))
								 {
								 	if($menu_cat_qu->publish == 'no')
									{
										break;
									}
								 }

                            	$this->CI->load->model('menu/menu_model');
								global $current_menu_id;
								//Create menu list
								$temp_menu_array = $this->CI->menu_lib->get_category_menus($row->target, $current_menu_id);

								$created_menus = $this->CI->menu_model->create_menu_item ($temp_menu_array);
								$row->created_menus = $created_menus;
								$temp_module_html .= $this->CI->load->view($view_directory,  array('module' => $row), true);

								break;

							case 'selected_article_category':
								$jason = json_decode($row->target);
								if(isset($jason->article_category_id) && isset($jason->number_of_items_per_page))
								{
									$article_category_id = $jason->article_category_id;
									$how_many_item = $jason->number_of_items_per_page;

									$this->CI->db->select('id,publish');
									$z = 1;
									foreach($article_category_id as $cat_row)
									{
                                        if($z == 1)
										{
											$this->CI->db->where(array('id' => $cat_row));
										}
										else
										{
											$this->CI->db->or_where(array('id' => $cat_row));
										}
										$z++;
									}
									$art_cat_qu = $this->CI->db->get('article_categories')->result();
									$publish_article_category = array();
									foreach($art_cat_qu as $a_cat_row)
									{
										if($a_cat_row->publish == 'yes')
										{
											array_push($publish_article_category, $a_cat_row->id);
										}
									}

									$this->CI->db->order_by('insert_date', 'DESC');
									$this->CI->db->limit($how_many_item);
									$l = 1;
									if(is_array($publish_article_category) && $publish_article_category != array())
									{
										$this->CI->db->group_start();
									}
									foreach($publish_article_category as $art_cat_row)
									{
										if($l == 1)
										{
											$this->CI->db->where(array('parent' => $art_cat_row));
										}
										else
										{
											$this->CI->db->or_where(array('parent' => $art_cat_row));
										}
										$l++;
									}
									if(is_array($publish_article_category) && $publish_article_category != array())
									{
										$this->CI->db->group_end();
									}
									$this->CI->db->group_start();
									$this->CI->db->where(array('publish' => 'yes'));
									$this->CI->db->group_end();
									$query_article_category = $this->CI->db->get('articles')->result();
									if($publish_article_category == array())
									{
										$query_article_category = array();
									}

									$l = 1;
									$this->CI->db->select('first_name,last_name,id');
									foreach ($query_article_category as $row_article)
									{
										if($l == 1)
										{
											$this->CI->db->where(array('id' => $row_article->author_id));
										}
										else
										{
											$this->CI->db->or_where(array('id' => $row_article->author_id));
										}
										$l++;
									}
									$users_query = $this->CI->db->get('users');

									$temp_selected_article_category = '';
									foreach ($query_article_category as $row_article)
									{
										$this->CI->load->library('date_shamsi');
										$time = $row_article->insert_date;
										$time = $this->CI->date_shamsi->jdate('o/m/j', $time,'','Asia/Tehran', 'fa');
										$title = $row_article->title;
										$intro = $row_article->intro;
										$author_id = $row_article->author_id;
										$src = base_url('pages/single_page/'.$row_article->id);

										$users_first_name = '';
										$users_last_name = '';
										foreach ($users_query->result() as $row_users)
										{
											if($author_id == $row_users->id)
											{
												$users_first_name = $row_users->first_name;
												$users_last_name = $row_users->last_name;
												break;
											}
										}
										$temp_selected_article_category .= '
		<div class="border w3-padding-small">
			<div class="w3-row w3-margin-bottom">
				<div class="w3-col s8">
				    <a href="https://www.instagram.com/bahmankeshavarzlawyer" target="_blank"><i class="fab fa-instagram  w3-text-blue w3-large"></i></a>
					<a href="https://telegram.me/keshavarzbahman" target="_blank"><i class="fab fa-telegram-plane w3-text-blue w3-large"></i></a>
				</div>
				<div class="w3-col s4 w3-left-align">'.$time.'</div>
			</div>
			<div class="w3-row w3-margin-bottom">
				<a href="https://telegram.me/keshavarzbahman" target="_blank">
					<img class="w3-col w3-circle s3" src="http://amya.ir/demo/keshavarz/content/products/thumb/no_pic.jpg">
					<div class="w3-col s9 sokhan_title">'.$users_first_name.' '.$users_last_name.'<br><span class="w3-tiny">دیدگاه‌های حقوقی و اجتماعی وکیل دادگستری</span></div>
				</a>
			</div>
			<div class="w3-row">
				<div class="title_description w3-margin-top margin-bottom">'.$title.'</div>
				<div class="w3-justify w3-margin-bottom w3-small">
				   '.$intro.'
				</div>
				<div class="read_more">
					<a href="'.$src.'" class="w3-left w3-small">ادامه مطلب...</a>
				</div>
			</div>

		</div>';
									}
									$row->selected_article_category = $temp_selected_article_category;
									$temp_module_html .= $this->CI->load->view($view_directory, array('module' => $row), true);
								}
								break;

							case 'weekly_discount':
								$slide_name = "slide_".$row->id.mt_rand();
								$temp_weekly_discount = '';
								$temp_json1 = json_decode($row->target);

								if(isset($temp_json1->slide_config->view_in_1024))
								{
									$view_in_1024 = $temp_json1->slide_config->view_in_1024;
								}
								else
								{
									$view_in_1024 = 1;
								}
								if(isset($temp_json1->slide_config->view_in_768))
								{
									$view_in_768 = $temp_json1->slide_config->view_in_768;
								}
								else
								{
									$view_in_768 = 1;
								}
								if(isset($temp_json1->slide_config->view_in_640))
								{
									$view_in_640 = $temp_json1->slide_config->view_in_640;
								}
								else
								{
									$view_in_640 = 1;
								}
								if(isset($temp_json1->slide_config->view_in_320))
								{
									$view_in_320 = $temp_json1->slide_config->view_in_320;
								}
								else
								{
									$view_in_320 = 1;
								}
								if(isset($temp_json1->slide_config->group_in_1024))
								{
									$group_in_1024 = $temp_json1->slide_config->group_in_1024;
								}
								else
								{
									$group_in_1024 = 1;
								}
								if(isset($temp_json1->slide_config->group_in_768))
								{
									$group_in_768 = $temp_json1->slide_config->group_in_768;
								}
								else
								{
									$group_in_768 = 1;
								}
								if(isset($temp_json1->slide_config->group_in_640))
								{
									$group_in_640 = $temp_json1->slide_config->group_in_640;
								}
								else
								{
									$group_in_640 = 1;
								}
								if(isset($temp_json1->slide_config->group_in_320))
								{
									$group_in_320 = $temp_json1->slide_config->group_in_320;
								}
								else
								{
									$group_in_320 = 1;
								}
								if(isset($temp_json1->slide_config->space_between))
								{
									$space_between = $temp_json1->slide_config->space_between;
								}
								else
								{
									$space_between = 1;
								}
								if(isset($temp_json1->slide_config->loop))
								{
									$loop = $temp_json1->slide_config->loop;
								}
								else
								{
									$loop = 'true';
								}
								if(isset($temp_json1->slide_config->loop_fill_group_with_blank))
								{
									$loop_fill_group_with_blank = $temp_json1->slide_config->loop_fill_group_with_blank;
								}
								else
								{
									$loop_fill_group_with_blank = 'true';
								}

								$number_of_items = 2;
								if(isset($temp_json1->number_of_items))
								{
									$number_of_items = $temp_json1->number_of_items;
								}

								$this->CI->db->group_start();
								$this->CI->db->where(array('discount_amount !=' => '', 'publish' => 'yes'));
								$this->CI->db->group_end();
								$this->CI->db->group_start();
								$this->CI->db->where(array('type_of_category' => 'virtual'));
								$this->CI->db->or_where(array('number >' => 0));
								$this->CI->db->or_where(array('finish' => 2));
								$this->CI->db->or_where(array('finish' => 3));
								$this->CI->db->group_end();
								$this->CI->db->order_by('modify_date', 'DESC');
								$this->CI->db->limit($number_of_items);
								$query_latest = $this->CI->db->get('add_products');
								$m = 1;
								$option_query = $this->CI->db->get('options')->result();
								foreach ($query_latest->result() as $items_row)
								{
									$json_pic = json_decode($items_row->primary_pic);
									$option_json_product = json_decode($items_row->options);
									$has_required_option = $this->CI->mylib->has_required_option($option_json_product, $items_row->category, $option_query);
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
									$product_link = base_url('pages/single_product/'.$items_row->id);

									$temp_weekly_discount .= '<div class="swiper-slide">
			<div class="w3-container w3-center product_holder product_item_'.$items_row->id.'" product_title="'.$items_row->title.'" product_link="'.$product_link.'" product_price="'.$items_row->price.'" product_id="'.$items_row->id.'" attr=\'{"tedad":1}\' product_first_image_src="'.$pic_src.'" has_required_option="'.$has_required_option.'">
				<a href="'.$product_link.'">
					<div class="image"><img src=' . $pic_src . '></div>
					<div class="title"><div class="limit_title">' . $items_row->title . '</div></div></a>
				<div class="price">' . number_format($items_row->price) . ' تومان</div>
				<div class="w3-bar">
					'.($items_row->finish == 3 || $items_row->type_of_category == 'virtual' || $items_row->number > 0 ? '<i class="fas fa-cart-plus w3-text-amber w3-bar-item w3-button" title="'.lang('add_to_cart').'" onclick="add_to_cart('.$items_row->id.', this);"></i>' :"").'
					<i class="far fa-heart w3-text-red w3-bar-item w3-button" title="'.lang('add_to_favorite_list').'" onclick="add_to_favorite('.$items_row->id.', this);"></i>
				</div>
			</div>
		</div>';
									if($m == 1)
									{
										$script = 'var slide_'.$slide_name.' = new Swiper(".'.$slide_name.'", {
		slidesPerView: '.$view_in_1024.',
		spaceBetween: '.$space_between.',
		slidesPerGroup: '.$group_in_1024.',
		loop: '.$loop.',
		loopFillGroupWithBlank: '.$loop_fill_group_with_blank.',
		/*pagination: {
			el: \'.swiper-pagination\',
			clickable: true,
		},*/
		navigation: {
			nextEl: \'.swiper-button-next\',
			prevEl: \'.swiper-button-prev\',
		},
		breakpoints: {
			1024: {
				slidesPerView: '.$view_in_1024.',
				slidesPerGroup: '.$group_in_1024.',
			},
			768: {
				slidesPerView: '.$view_in_768.',
				slidesPerGroup: '.$group_in_768.',
			},
			640: {
				slidesPerView: '.$view_in_640.',
				slidesPerGroup: '.$group_in_640.',
			},
			320: {
				slidesPerView: '.$view_in_320.',
				slidesPerGroup: '.$group_in_320.',
			}
		},
		on: {
			resize: function () {
				[].forEach.call(document.getElementsByClassName("limit_title"), function(value) {
					$clamp(value, {clamp: 2, useNativeClamp: true});
				});
			},
		},
	});';
										$bottom_scripts .= $script;
									}
									$m++;
								}

								if($temp_weekly_discount != '')
								{
									$slide_latest = '<div class="swiper-container '.$slide_name.'">
	                                                  <div class="swiper-wrapper">
	                                                  '.$temp_weekly_discount.'
	                                             </div>
															<!-- Add Pagination -->
															<div class="swiper-pagination"></div>
															<!-- Add Arrows -->
															<div class="swiper-button-next"></div>
															<div class="swiper-button-prev"></div>
														</div>';

									$row->created_slide_latest = $slide_latest;
									$temp_module_html .= $this->CI->load->view($view_directory, array('module' => $row), true);
								}
								break;

							case 'slide':
								$jason = $row->target;
								$jason = json_decode($jason);
								$temp_pic = '';

								if(isset($jason->slides))
								{
									$slides = $jason->slides;
									foreach($slides as $slide_row)
									{
										if(isset($slide_row->pic) && isset($slide_row->content))
										{
											$pic_src_slide = base_url('content/products/'."$slide_row->pic");
											$temp_pic .= '<div class="swiper-slide" style="background-image:url('.$pic_src_slide.')">'.$slide_row->content.'</div>';
										}
									}
								}
								$slide_name = "slide_$row->id";
								$script = 'var slide = new Swiper(\'.{{slide_name}}\', {centeredSlides: true, loop: true, autoplay: {delay: 40000, disableOnInteraction: false, }, pagination: {el: \'.swiper-pagination\', clickable: true, }, navigation: {nextEl: \'.swiper-button-next\', prevEl: \'.swiper-button-prev\', }, });';
								if(isset($jason->script))
								{
									$script = $jason->script;
								}
								$bottom_scripts .= str_replace("{{slide_name}}",$slide_name,$script);

								$slide = '<div class="swiper-container '.$slide_name.'">
						<div class="swiper-wrapper">
							'.$temp_pic.'
						</div>
					
						<!-- Add Pagination -->
						<div class="swiper-pagination"></div>
						<!-- Add Arrows inam ke kelidhaye jahate-->
						<div class="swiper-button-next"></div>
						<div class="swiper-button-prev"></div>
					</div>';

								$row->created_slide = $slide;
								$temp_module_html .= $this->CI->load->view($view_directory, array('module' => $row), true);
								break;

							case 'latest':
								$slide_name = "slide_".$row->id.mt_rand();
								$temp_latest = '';
								$temp_json1 = json_decode($row->target);

								if(isset($temp_json1->slide_config->view_in_1024))
								{
									$view_in_1024 = $temp_json1->slide_config->view_in_1024;
								}
								else
								{
									$view_in_1024 = 1;
								}
								if(isset($temp_json1->slide_config->view_in_768))
								{
									$view_in_768 = $temp_json1->slide_config->view_in_768;
								}
								else
								{
									$view_in_768 = 1;
								}
								if(isset($temp_json1->slide_config->view_in_640))
								{
									$view_in_640 = $temp_json1->slide_config->view_in_640;
								}
								else
								{
									$view_in_640 = 1;
								}
								if(isset($temp_json1->slide_config->view_in_320))
								{
									$view_in_320 = $temp_json1->slide_config->view_in_320;
								}
								else
								{
									$view_in_320 = 1;
								}
								if(isset($temp_json1->slide_config->group_in_1024))
								{
									$group_in_1024 = $temp_json1->slide_config->group_in_1024;
								}
								else
								{
									$group_in_1024 = 1;
								}
								if(isset($temp_json1->slide_config->group_in_768))
								{
									$group_in_768 = $temp_json1->slide_config->group_in_768;
								}
								else
								{
									$group_in_768 = 1;
								}
								if(isset($temp_json1->slide_config->group_in_640))
								{
									$group_in_640 = $temp_json1->slide_config->group_in_640;
								}
								else
								{
									$group_in_640 = 1;
								}
								if(isset($temp_json1->slide_config->group_in_320))
								{
									$group_in_320 = $temp_json1->slide_config->group_in_320;
								}
								else
								{
									$group_in_320 = 1;
								}
								if(isset($temp_json1->slide_config->space_between))
								{
									$space_between = $temp_json1->slide_config->space_between;
								}
								else
								{
									$space_between = 1;
								}
								if(isset($temp_json1->slide_config->loop))
								{
									$loop = $temp_json1->slide_config->loop;
								}
								else
								{
									$loop = 'true';
								}
								if(isset($temp_json1->slide_config->loop_fill_group_with_blank))
								{
									$loop_fill_group_with_blank = $temp_json1->slide_config->loop_fill_group_with_blank;
								}
								else
								{
									$loop_fill_group_with_blank = 'true';
								}

								$number_of_items = 10;
								if(isset($temp_json1->number_of_items))
								{
									$number_of_items = $temp_json1->number_of_items;
								}

								$this->CI->db->order_by('insert_date', 'DESC');
								$this->CI->db->limit($number_of_items);
								$this->CI->db->group_start();
								$this->CI->db->where(array('publish' => 'yes'));
								$this->CI->db->group_end();
								$this->CI->db->group_start();
								$this->CI->db->where(array('type_of_category' => 'virtual'));
								$this->CI->db->or_where(array('number >' => 0));
								$this->CI->db->or_where(array('finish' => 2));
								$this->CI->db->or_where(array('finish' => 3));
								$this->CI->db->group_end();
								$query_latest = $this->CI->db->get('add_products');
								$m = 1;
								$option_query = $this->CI->db->get('options')->result();
								foreach ($query_latest->result() as $row_latest)
								{
									$option_json_product = json_decode($row_latest->options);
									$has_required_option = $this->CI->mylib->has_required_option($option_json_product, $row_latest->category, $option_query);
									$json_pic = json_decode($row_latest->primary_pic);
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
									$product_link = base_url('pages/single_product/'.$row_latest->id);

									$temp_latest .= '<div class="swiper-slide">
			<div class="w3-container w3-center product_holder product_item_'.$row_latest->id.'" id="product_item_'.$row_latest->id.'" product_title="'.$row_latest->title.'" product_link="'.$product_link.'" product_price="'.$row_latest->price.'" product_id="'.$row_latest->id.'" attr=\'{"tedad":1}\' product_first_image_src="'.$pic_src.'" has_required_option="'.$has_required_option.'">
				<a href="'.$product_link.'">
					<div class="image"><img src='.$pic_src.'></div>
					<div class="title"><div class="limit_title">'.$row_latest->title.'</div></div></a>
				<div class="price">'.number_format($row_latest->price).' تومان</div>
				<div class="w3-bar">
					'.($row_latest->finish == 3 || $row_latest->type_of_category == 'virtual' || $row_latest->number > 0 ? '<i class="fas fa-cart-plus w3-text-amber w3-bar-item w3-button" title="'.lang('add_to_cart').'" onclick="add_to_cart('.$row_latest->id.', this);"></i>' :"").'
					<i class="far fa-heart w3-text-red w3-bar-item w3-button" title="'.lang('add_to_favorite_list').'" onclick="add_to_favorite('.$row_latest->id.', this);"></i>
				</div>
			</div>
		</div>';
									if($m == 1)
									{
										$script = 'var slide_'.$slide_name.' = new Swiper(".'.$slide_name.'", {
		slidesPerView: '.$view_in_1024.',
		spaceBetween: '.$space_between.',
		slidesPerGroup: '.$group_in_1024.',
		loop: '.$loop.',
		loopFillGroupWithBlank: '.$loop_fill_group_with_blank.',
		/*pagination: {
			el: \'.swiper-pagination\',
			clickable: true,
		},*/
		navigation: {
			nextEl: \'.swiper-button-next\',
			prevEl: \'.swiper-button-prev\',
		},
		breakpoints: {
			1024: {
				slidesPerView: '.$view_in_1024.',
				slidesPerGroup: '.$group_in_1024.',
			},
			768: {
				slidesPerView: '.$view_in_768.',
				slidesPerGroup: '.$group_in_768.',
			},
			640: {
				slidesPerView: '.$view_in_640.',
				slidesPerGroup: '.$group_in_640.',
			},
			320: {
				slidesPerView: '.$view_in_320.',
				slidesPerGroup: '.$group_in_320.',
			}
		},
		on: {
			resize: function () {
				[].forEach.call(document.getElementsByClassName("limit_title"), function(value) {
					$clamp(value, {clamp: 2, useNativeClamp: true});
				});
			},
		},
	});';
										$bottom_scripts .= $script;
									}
									$m++;
								}

								if($temp_latest != '')
								{
									$slide_latest = '<div class="swiper-container '.$slide_name.'">
	                                                  <div class="swiper-wrapper">
	                                                  '.$temp_latest.'
	                                             </div>
															<!-- Add Pagination -->
															<div class="swiper-pagination"></div>
															<!-- Add Arrows -->
															<div class="swiper-button-next"></div>
															<div class="swiper-button-prev"></div>
														</div>';

									$row->created_slide_latest = $slide_latest;
									$temp_module_html .= $this->CI->load->view($view_directory, array('module' => $row), true);
								}
								break;

							case 'most_popular':
								$slide_name = "slide_".$row->id.mt_rand();
								$temp_most_popular = '';
								$temp_json1 = json_decode($row->target);

								$view_in_1024 = 1;
								if(isset($temp_json1->slide_config->view_in_1024))
								{
									$view_in_1024 = $temp_json1->slide_config->view_in_1024;
								}
								$view_in_768 = 1;
								if(isset($temp_json1->slide_config->view_in_768))
								{
									$view_in_768 = $temp_json1->slide_config->view_in_768;
								}
								$view_in_640 = 1;
								if(isset($temp_json1->slide_config->view_in_640))
								{
									$view_in_640 = $temp_json1->slide_config->view_in_640;
								}
								$view_in_320 = 1;
								if(isset($temp_json1->slide_config->view_in_320))
								{
									$view_in_320 = $temp_json1->slide_config->view_in_320;
								}
								$group_in_1024 = 1;
								if(isset($temp_json1->slide_config->group_in_1024))
								{
									$group_in_1024 = $temp_json1->slide_config->group_in_1024;
								}
								$group_in_768 = 1;
								if(isset($temp_json1->slide_config->group_in_768))
								{
									$group_in_768 = $temp_json1->slide_config->group_in_768;
								}
								$group_in_640 = 1;
								if(isset($temp_json1->slide_config->group_in_640))
								{
									$group_in_640 = $temp_json1->slide_config->group_in_640;
								}
								$group_in_320 = 1;
								if(isset($temp_json1->slide_config->group_in_320))
								{
									$group_in_320 = $temp_json1->slide_config->group_in_320;
								}
								$space_between = 1;
								if(isset($temp_json1->slide_config->space_between))
								{
									$space_between = $temp_json1->slide_config->space_between;
								}
								$loop = 'true';
								if(isset($temp_json1->slide_config->loop))
								{
									$loop = $temp_json1->slide_config->loop;
								}
								$loop_fill_group_with_blank = 'true';
								if(isset($temp_json1->slide_config->loop_fill_group_with_blank))
								{
									$loop_fill_group_with_blank = $temp_json1->slide_config->loop_fill_group_with_blank;
								}
								$number_of_items = 10;
								if(isset($temp_json1->number_of_items))
								{
									$number_of_items = $temp_json1->number_of_items;
								}

								$this->CI->db->group_start();
								$this->CI->db->where(array('rate !=' => '', 'product_id !=' => ''));
								$this->CI->db->group_end();
								$this->CI->db->group_start();
								$this->CI->db->where(array('rate !=' => null, 'product_id !=' => null));
								$this->CI->db->group_end();
								$this->CI->db->group_start();
								$this->CI->db->where(array('publish' => 'yes'));
								$this->CI->db->group_end();
								$query_comment = $this->CI->db->get('comment');

								$this->CI->db->group_start();
								$this->CI->db->where(array('publish' => 'yes'));
								$this->CI->db->group_end();
								$this->CI->db->group_start();
								$this->CI->db->where(array('type_of_category' => 'virtual'));
								$this->CI->db->or_where(array('number >' => 0));
								$this->CI->db->or_where(array('finish' => 2));
								$this->CI->db->or_where(array('finish' => 3));
								$this->CI->db->group_end();
								$query_products = $this->CI->db->get('add_products')->result();

								$option_query = $this->CI->db->get('options')->result();
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
									$most_popular_query = array();
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

								if(isset($most_popular_query) && $most_popular_query != array())
								{
									$most_popular_query = array_slice($most_popular_query ,0 ,$number_of_items ,true);
									$m = 1;
									foreach ($most_popular_query as $row_most_popular)
									{
										$option_json_product = json_decode($row_most_popular->options);
										$has_required_option = $this->CI->mylib->has_required_option($option_json_product, $row_most_popular->category, $option_query);
										$json_pic = json_decode($row_most_popular->primary_pic);
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
										$product_link = base_url('pages/single_product/'.$row_most_popular->id);

										$temp_most_popular .= '<div class="swiper-slide">
			<div class="w3-container w3-center product_holder product_item_'.$row_most_popular->id.'" id="product_item_'.$row_most_popular->id.'" product_title="'.$row_most_popular->title.'" product_link="'.$product_link.'" product_price="'.$row_most_popular->price.'" product_id="'.$row_most_popular->id.'" attr=\'{"tedad":1}\' product_first_image_src="'.$pic_src.'" has_required_option="'.$has_required_option.'">
				<a href="'.$product_link.'">
					<div class="image"><img src='.$pic_src.'></div>
					<div class="title"><div class="limit_title">'.$row_most_popular->title.'</div></div></a>
				<div class="price">'.number_format($row_most_popular->price).' تومان</div>
				<div class="w3-bar">
					'.($row_most_popular->finish == 3 || $row_most_popular->type_of_category == 'virtual' || $row_most_popular->number > 0 ? '<i class="fas fa-cart-plus w3-text-amber w3-bar-item w3-button" title="'.lang('add_to_cart').'" onclick="add_to_cart('.$row_most_popular->id.', this);"></i>' :"").'
					<i class="far fa-heart w3-text-red w3-bar-item w3-button" title="'.lang('add_to_favorite_list').'" onclick="add_to_favorite('.$row_most_popular->id.', this);"></i>
				</div>
			</div>
		</div>';
										if($m == 1)
										{
											$script = 'var slide_'.$slide_name.' = new Swiper(".'.$slide_name.'", {
		slidesPerView: '.$view_in_1024.',
		spaceBetween: '.$space_between.',
		slidesPerGroup: '.$group_in_1024.',
		loop: '.$loop.',
		loopFillGroupWithBlank: '.$loop_fill_group_with_blank.',
		/*pagination: {
			el: \'.swiper-pagination\',
			clickable: true,
		},*/
		navigation: {
			nextEl: \'.swiper-button-next\',
			prevEl: \'.swiper-button-prev\',
		},
		breakpoints: {
			1024: {
				slidesPerView: '.$view_in_1024.',
				slidesPerGroup: '.$group_in_1024.',
			},
			768: {
				slidesPerView: '.$view_in_768.',
				slidesPerGroup: '.$group_in_768.',
			},
			640: {
				slidesPerView: '.$view_in_640.',
				slidesPerGroup: '.$group_in_640.',
			},
			320: {
				slidesPerView: '.$view_in_320.',
				slidesPerGroup: '.$group_in_320.',
			}
		},
		on: {
			resize: function () {
				[].forEach.call(document.getElementsByClassName("limit_title"), function(value) {
					$clamp(value, {clamp: 2, useNativeClamp: true});
				});
			},
		},
	});';
											$bottom_scripts .= $script;
										}
										$m++;
									}
								}

								if($temp_most_popular != '')
								{
									$slide_most_popular = '<div class="swiper-container '.$slide_name.'">
	                                                  <div class="swiper-wrapper">
	                                                  '.$temp_most_popular.'
	                                             </div>
															<!-- Add Pagination -->
															<div class="swiper-pagination"></div>
															<!-- Add Arrows -->
															<div class="swiper-button-next"></div>
															<div class="swiper-button-prev"></div>
														</div>';

									$row->created_slide_most_popular = $slide_most_popular;
									$temp_module_html .= $this->CI->load->view($view_directory, array('module' => $row), true);
								}
								break;

							case 'best_sales':
								$slide_name = "slide_".$row->id.mt_rand();
								$temp_best_sales = '';
								$temp_json1 = json_decode($row->target);
								$view_in_1024 = 1;
								if(isset($temp_json1->slide_config->view_in_1024))
								{
									$view_in_1024 = $temp_json1->slide_config->view_in_1024;
								}
								$view_in_768 = 1;
								if(isset($temp_json1->slide_config->view_in_768))
								{
									$view_in_768 = $temp_json1->slide_config->view_in_768;
								}
								$view_in_640 = 1;
								if(isset($temp_json1->slide_config->view_in_640))
								{
									$view_in_640 = $temp_json1->slide_config->view_in_640;
								}
								$view_in_320 = 1;
								if(isset($temp_json1->slide_config->view_in_320))
								{
									$view_in_320 = $temp_json1->slide_config->view_in_320;
								}
								$group_in_1024 = 1;
								if(isset($temp_json1->slide_config->group_in_1024))
								{
									$group_in_1024 = $temp_json1->slide_config->group_in_1024;
								}
								$group_in_768 = 1;
								if(isset($temp_json1->slide_config->group_in_768))
								{
									$group_in_768 = $temp_json1->slide_config->group_in_768;
								}
								$group_in_640 = 1;
								if(isset($temp_json1->slide_config->group_in_640))
								{
									$group_in_640 = $temp_json1->slide_config->group_in_640;
								}
								$group_in_320 = 1;
								if(isset($temp_json1->slide_config->group_in_320))
								{
									$group_in_320 = $temp_json1->slide_config->group_in_320;
								}
								$space_between = 1;
								if(isset($temp_json1->slide_config->space_between))
								{
									$space_between = $temp_json1->slide_config->space_between;
								}
								$loop = 'true';
								if(isset($temp_json1->slide_config->loop))
								{
									$loop = $temp_json1->slide_config->loop;
								}
								$loop_fill_group_with_blank = 'true';
								if(isset($temp_json1->slide_config->loop_fill_group_with_blank))
								{
									$loop_fill_group_with_blank = $temp_json1->slide_config->loop_fill_group_with_blank;
								}
								$number_of_items = 10;
								if(isset($temp_json1->number_of_items))
								{
									$number_of_items = $temp_json1->number_of_items;
								}

								$this->CI->db->order_by('sales_number', 'DESC');
								$this->CI->db->limit($number_of_items);
								$this->CI->db->group_start();
								$this->CI->db->where(array('publish' => 'yes'));
								$this->CI->db->group_end();
								$this->CI->db->group_start();
								$this->CI->db->where(array('type_of_category' => 'virtual'));
								$this->CI->db->or_where(array('number >' => 0));
								$this->CI->db->or_where(array('finish' => 2));
								$this->CI->db->or_where(array('finish' => 3));
								$this->CI->db->group_end();
								$best_sales_products = $this->CI->db->get('add_products')->result();

								$option_query = $this->CI->db->get('options')->result();
								if(isset($best_sales_products) && $best_sales_products != array())
								{
									$m = 1;
									foreach ($best_sales_products as $row_best_sales)
									{
										$option_json_product = json_decode($row_best_sales->options);
										$has_required_option = $this->CI->mylib->has_required_option($option_json_product, $row_best_sales->category, $option_query);
										$json_pic = json_decode($row_best_sales->primary_pic);
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
										$product_link = base_url('pages/single_product/'.$row_best_sales->id);

										$temp_best_sales .= '<div class="swiper-slide">
			<div class="w3-container w3-center product_holder product_item_'.$row_best_sales->id.'" id="product_item_'.$row_best_sales->id.'" product_title="'.$row_best_sales->title.'" product_link="'.$product_link.'" product_price="'.$row_best_sales->price.'" product_id="'.$row_best_sales->id.'" attr=\'{"tedad":1}\' product_first_image_src="'.$pic_src.'" has_required_option="'.$has_required_option.'">
				<a href="'.$product_link.'">
					<div class="image"><img src='.$pic_src.'></div>
					<div class="title"><div class="limit_title">'.$row_best_sales->title.'</div></div></a>
				<div class="price">'.number_format($row_best_sales->price).' تومان</div>
				<div class="w3-bar">
					'.($row_best_sales->finish == 3 || $row_best_sales->type_of_category == 'virtual' || $row_best_sales->number > 0 ? '<i class="fas fa-cart-plus w3-text-amber w3-bar-item w3-button" title="'.lang('add_to_cart').'" onclick="add_to_cart('.$row_best_sales->id.', this);"></i>' :"").'
					<i class="far fa-heart w3-text-red w3-bar-item w3-button" title="'.lang('add_to_favorite_list').'" onclick="add_to_favorite('.$row_best_sales->id.', this);"></i>
				</div>
			</div>
		</div>';
										if($m == 1)
										{
											$script = 'var slide_'.$slide_name.' = new Swiper(".'.$slide_name.'", {
		slidesPerView: '.$view_in_1024.',
		spaceBetween: '.$space_between.',
		slidesPerGroup: '.$group_in_1024.',
		loop: '.$loop.',
		loopFillGroupWithBlank: '.$loop_fill_group_with_blank.',
		/*pagination: {
			el: \'.swiper-pagination\',
			clickable: true,
		},*/
		navigation: {
			nextEl: \'.swiper-button-next\',
			prevEl: \'.swiper-button-prev\',
		},
		breakpoints: {
			1024: {
				slidesPerView: '.$view_in_1024.',
				slidesPerGroup: '.$group_in_1024.',
			},
			768: {
				slidesPerView: '.$view_in_768.',
				slidesPerGroup: '.$group_in_768.',
			},
			640: {
				slidesPerView: '.$view_in_640.',
				slidesPerGroup: '.$group_in_640.',
			},
			320: {
				slidesPerView: '.$view_in_320.',
				slidesPerGroup: '.$group_in_320.',
			}
		},
		on: {
			resize: function () {
				[].forEach.call(document.getElementsByClassName("limit_title"), function(value) {
					$clamp(value, {clamp: 2, useNativeClamp: true});
				});
			},
		},
	});';
											$bottom_scripts .= $script;
										}
										$m++;
									}
								}

								if($temp_best_sales != '')
								{
									$slide_best_sales = '<div class="swiper-container '.$slide_name.'">
	                                                  <div class="swiper-wrapper">
	                                                  '.$temp_best_sales.'
	                                             </div>
															<!-- Add Pagination -->
															<div class="swiper-pagination"></div>
															<!-- Add Arrows -->
															<div class="swiper-button-next"></div>
															<div class="swiper-button-prev"></div>
														</div>';

									$row->created_bast_sales = $slide_best_sales;
									$temp_module_html .= $this->CI->load->view($view_directory, array('module' => $row), true);
								}
								break;

							case 'selected_items':
								$slide_name = "slide_".$row->id.mt_rand();
								$temp_json2 = json_decode($row->target);
								$temp_selected_items = '';

								$view_in_1024 = 1;
								if(isset($temp_json2->slide_config->view_in_1024))
								{
									$view_in_1024 = $temp_json2->slide_config->view_in_1024;
								}
								$view_in_768 = 1;
								if(isset($temp_json2->slide_config->view_in_768))
								{
									$view_in_768 = $temp_json2->slide_config->view_in_768;
								}
								$view_in_640 = 1;
								if(isset($temp_json2->slide_config->view_in_640))
								{
									$view_in_640 = $temp_json2->slide_config->view_in_640;
								}
								$view_in_320 = 1;
								if(isset($temp_json2->slide_config->view_in_320))
								{
									$view_in_320 = $temp_json2->slide_config->view_in_320;
								}
								$group_in_1024 = 1;
								if(isset($temp_json2->slide_config->group_in_1024))
								{
									$group_in_1024 = $temp_json2->slide_config->group_in_1024;
								}
								$group_in_768 = 1;
								if(isset($temp_json2->slide_config->group_in_768))
								{
									$group_in_768 = $temp_json2->slide_config->group_in_768;
								}
								$group_in_640 = 1;
								if(isset($temp_json2->slide_config->group_in_640))
								{
									$group_in_640 = $temp_json2->slide_config->group_in_640;
								}
								$group_in_320 = 1;
								if(isset($temp_json2->slide_config->group_in_320))
								{
									$group_in_320 = $temp_json2->slide_config->group_in_320;
								}
								$space_between = 1;
								if(isset($temp_json2->slide_config->space_between))
								{
									$space_between = $temp_json2->slide_config->space_between;
								}
								$loop = 'true';
								if(isset($temp_json2->slide_config->loop))
								{
									$loop = $temp_json2->slide_config->loop;
								}
								$loop_fill_group_with_blank = 'true';
								if(isset($temp_json2->slide_config->loop_fill_group_with_blank))
								{
									$loop_fill_group_with_blank = $temp_json2->slide_config->loop_fill_group_with_blank;
								}

								if(isset($temp_json2->items_id))
								{
									$items_id = $temp_json2->items_id;
									$i = 0;
									$v = 1;
									if(is_array($items_id) && $items_id != array())
									{
										$this->CI->db->group_start();
									}
									foreach ($items_id as $index)
									{
										if($v == 1)
										{
											$this->CI->db->where(array('id' => $index));
										}
										else
										{
											$this->CI->db->or_where(array('id' => $index));
										}
										$v++;
									}
									if(is_array($items_id) && $items_id != array())
									{
										$this->CI->db->group_end();
									}
									$this->CI->db->group_start();
									$this->CI->db->where(array('publish' => 'yes'));
									$this->CI->db->group_end();
									$this->CI->db->group_start();
									$this->CI->db->where(array('type_of_category' => 'virtual'));
									$this->CI->db->or_where(array('number >' => 0));
									$this->CI->db->or_where(array('finish' => 2));
									$this->CI->db->or_where(array('finish' => 3));
									$this->CI->db->group_end();
									$selected_items = $this->CI->db->get('add_products');

									$option_query = $this->CI->db->get('options')->result();
									$m = 1;
									    foreach ($selected_items->result() as $items_row)
										{
                                            $attribute = '';
                                            if(isset($items_row->id))
                                            {
                                                $this->CI->db->where(array('id' => $items_row->id));
                                                $temp_4 = $this->CI->db->get('add_products')->result();
                                            }

                                            if(isset($temp_4))
                                            {
                                                if($temp_4 != array())
                                                {
                                                    foreach($temp_4 as $row4)
                                                    {
                                                        $json_field = json_decode($row4->fields);

														$this->CI->db->where(array('category' => $row4->category, 'publish' => 'yes'));
														$all_published_fields_in_this_category = $this->CI->db->get('add_field')->result_array();

														$temp_json_fields = array();
														foreach($all_published_fields_in_this_category as $published_field_in_this_cat)
														{
															$indx_as_string = $published_field_in_this_cat['id'];
															if ($json_field->{$indx_as_string})
															{
																$temp_json_fields[$indx_as_string] = $json_field->{$indx_as_string};
															}
														}

														$json_field = json_decode(json_encode($temp_json_fields));

														/*echo '<pre dir="ltr">';
														print_r($json_field);
														echo '</pre>';*/
                                                    }
                                                }
                                            }

                                            if (isset($items_row->category))
                                            {
                                                $this->CI->db->where(array('product_category' => $items_row->category));
                                                $attribute_groups_query = $this->CI->db->get('attribute_groups');

                                                foreach ($attribute_groups_query->result() as $attribute_group_row)
                                                {
                                                    $this->CI->db->order_by('sort', 'ASC');
                                                    $this->CI->db->where(array('attribute_groups' => $attribute_group_row->id));
                                                    $query_fields = $this->CI->db->get('add_field');

                                                    $indx = 1;
                                                    foreach ($query_fields->result() as $field_row)
                                                    {
                                                        if($indx == 1)
                                                        {
                                                            $attribute_group_name = $attribute_group_row->attribute_groups_name;
                                                            $attribute .= '<div class="attribute_group_title">'.$attribute_group_name.':</div>';
                                                            $indx = 2;
                                                        }

                                                        if(isset($json_field))
                                                        {
                                                            foreach ($json_field as $index => $value)
                                                            {
                                                                if($field_row->id == $index)
                                                                {
                                                                    if($field_row->linkable == 'yes')
                                                                    {
                                                                        if($field_row->type == 'checkbox' || $field_row->type == 'select' || $field_row->type == 'multiple_case')
                                                                        {
                                                                            if($field_row->searchable == 'yes')
                                                                            {
                                                                                if($field_row->type == 'multiple_case')
                                                                                {
                                                                                    $value_link_array = explode("::new_line::",$value);
                                                                                }
                                                                                else
                                                                                {
                                                                                    $value_link_array = explode("~||~",$value);
                                                                                }
                                                                                $value_link_string = '';
                                                                                $ko = 1;
                                                                                foreach($value_link_array as $li_row)
                                                                                {
                                                                                    if($ko == 1)
                                                                                    {
                                                                                        $temp_ko = '';
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        $temp_ko = ', ';
                                                                                    }
                                                                                    $ko++;
                                                                                    $value_link_string .= '<a href="'.base_url('pages/single_product_category/'.$field_row->category.'?field'.$field_row->id.'[]='.$li_row).'">'.$temp_ko.$li_row.'</a>';
                                                                                }
                                                                                $attribute .= '<div><span><b>'.$field_row->title.':</b></span>'.$value_link_string.'</div>';
                                                                            }
                                                                            else
                                                                            {
                                                                                if($field_row->type == 'multiple_case')
                                                                                {
                                                                                    $value_link_array = explode("::new_line::",$value);
                                                                                }
                                                                                else
                                                                                {
                                                                                    $value_link_array = explode("~||~",$value);
                                                                                }
                                                                                $value_link_string = '';
                                                                                $ko = 1;
                                                                                foreach($value_link_array as $li_row)
                                                                                {
                                                                                    if($ko == 1)
                                                                                    {
                                                                                        $temp_ko = '';
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        $temp_ko = ', ';
                                                                                    }
                                                                                    $ko++;
                                                                                    $value_link_string .= '<a href="'.base_url('pages/single_product_category/'.$field_row->category.'?search='.$li_row).'">'.$temp_ko.$li_row.'</a>';
                                                                                }
                                                                                $attribute .= '<div><span><b>'.$field_row->title.':</b></span>'.$value_link_string.'</div>';
                                                                            }
                                                                        }
                                                                        elseif($field_row->type == 'file')
                                                                        {
                                                                            if(isset($value->orig_name) && isset($value->file_name))
                                                                            {
                                                                                $file_src = base_url('content/file/'.$value->file_name);
                                                                                $attribute .= '<div><span><b>'.$field_row->title.':</b></span><a href="'.$file_src.'">'.str_replace("::new_line::", ', ', $value->orig_name).'</a></div>';
                                                                            }
                                                                        }
                                                                        else
                                                                        {
                                                                            if($field_row->searchable == 'yes')
                                                                            {
                                                                                $attribute .= '<div><span><b>'.$field_row->title.':</b></span><a href="'.base_url('pages/single_product_category/'.$field_row->category.'?field'.$field_row->id.'='.str_replace("::new_line::", '\r::new_line::', $value)).'">'.str_replace("::new_line::", ', ', $value).'</a></div>';
                                                                            }
                                                                            else
                                                                            {
                                                                                $attribute .= '<div><span><b>'.$field_row->title.':</b></span><a href="'.base_url('pages/single_product_category/'.$field_row->category.'?search='.str_replace("::new_line::", '\r::new_line::', $value)).'">'.str_replace("::new_line::", ', ', $value).'</a></div>';
                                                                            }
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        if($field_row->type == 'file')
                                                                        {
                                                                            if(isset($value->orig_name) && isset($value->file_name))
                                                                            {
                                                                                $file_src = base_url('content/file/'.$value->file_name);
                                                                                $attribute .= '<div><span><b>'.$field_row->title.':</b></span><a href="'.$file_src.'">'.str_replace("::new_line::", ', ', $value->orig_name).'</a></div>';
                                                                            }
                                                                        }
                                                                        else
                                                                        {
                                                                            $attribute .= '<div><span><b>'.$field_row->title.':</b></span>'.str_replace("::new_line::", ', ', $value).'</div>';
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            //echo '#####################';
                                            //echo $attribute;

											/*if(isset($json_field))
											{
												print_r($json_field);
											}*/

											$option_json_product = json_decode($items_row->options);
											$has_required_option = $this->CI->mylib->has_required_option($option_json_product, $items_row->category, $option_query);
											$i++;
											$json_pic = json_decode($items_row->primary_pic);
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
											$product_link = base_url('pages/single_product/'.$items_row->id);

											if($temp_json2->show_type == 'slide')
											{
												$temp_selected_items .= '<div class="swiper-slide">
			<div class="w3-container w3-center product_holder product_item_'.$items_row->id.'" product_title="'.$items_row->title.'" product_link="'.$product_link.'" product_price="'.$items_row->price.'" product_id="'.$items_row->id.'" attr=\'{"tedad":1}\' product_first_image_src="'.$pic_src.'" has_required_option="'.$has_required_option.'">
				<a href="'.$product_link.'">
					<div class="image"><img src=' . $pic_src . '></div>
					<div class="title"><div class="limit_title">' . $items_row->title . '</div></div></a>
				<div class="price">' . number_format($items_row->price) . ' تومان</div>
				<div class="w3-bar">
					'.($items_row->finish == 3 || $items_row->type_of_category == 'virtual' || $items_row->number > 0 ? '<i class="fas fa-cart-plus w3-text-amber w3-bar-item w3-button" title="'.lang('add_to_cart').'" onclick="add_to_cart('.$items_row->id.', this);"></i>' :"").'
					<i class="far fa-heart w3-text-red w3-bar-item w3-button" title="'.lang('add_to_favorite_list').'" onclick="add_to_favorite('.$items_row->id.', this);"></i>
				</div>
			</div>
		</div>';
											if($m == 1)
											{
												$script = 'var slide_'.$slide_name.' = new Swiper(".'.$slide_name.'", {
		slidesPerView: '.$view_in_1024.',
		spaceBetween: '.$space_between.',
		slidesPerGroup: '.$group_in_1024.',
		loop: '.$loop.',
		loopFillGroupWithBlank: '.$loop_fill_group_with_blank.',
		/*pagination: {
			el: \'.swiper-pagination\',
			clickable: true,
		},*/
		navigation: {
			nextEl: \'.swiper-button-next\',
			prevEl: \'.swiper-button-prev\',
		},
		breakpoints: {
			1024: {
				slidesPerView: '.$view_in_1024.',
				slidesPerGroup: '.$group_in_1024.',
			},
			768: {
				slidesPerView: '.$view_in_768.',
				slidesPerGroup: '.$group_in_768.',
			},
			640: {
				slidesPerView: '.$view_in_640.',
				slidesPerGroup: '.$group_in_640.',
			},
			320: {
				slidesPerView: '.$view_in_320.',
				slidesPerGroup: '.$group_in_320.',
			}
		},
		on: {
			resize: function () {
				[].forEach.call(document.getElementsByClassName("limit_title"), function(value) {
					$clamp(value, {clamp: 2, useNativeClamp: true});
				});
			},
		},
	});';
												$bottom_scripts .= $script;
											}
											$m++;
										}
											else if($temp_json2->show_type == 'descriptive')
											{
												$temp_selected_items .= "<div class='swiper-slide pointer'><div 
                                                onclick='show_suggestion(this);'
                                                image_source='$pic_src'
                                                item_description='<div class=\"item_title\"><b>عنوان:</b> $items_row->title</div><div class=\"price\"><b>قیمت:</b> $items_row->price</div><div><b>توضیحات:</b><br>$items_row->description</div><div class=\"read_more\"><a href=\"$product_link\" class=\"w3-left w3-small\">اطلاعات بیشتر...</a></div>'
                                                product_title='$items_row->title' product_link='$product_link' product_price='$items_row->price' product_id='$items_row->id' attr='{\"tedad\":1}' product_first_image_src='$pic_src' can_be_purchased=".($items_row->finish == 3 || $items_row->type_of_category == 'virtual' || $items_row->number > 0 ? 'yes' : 'no')." has_required_option=\"$has_required_option\"
                                                >
                                                    <img src='$pic_src'>
                                                </div></div>";
												if($m == 1)
												{
													$script = ' var slide_'.$slide_name.' = new Swiper(".'.$slide_name.'", {
                                                        slidesPerView: '.$view_in_1024.',
                                                        spaceBetween: '.$space_between.',
                                                        slidesPerGroup: '.$group_in_1024.',
                                                        loop: '.$loop.',
                                                        loopFillGroupWithBlank: '.$loop_fill_group_with_blank.',
                                                        /*pagination: {
                                                            el: \'.swiper-pagination\',
                                                            clickable: true,
                                                        },*/
                                                        navigation: {
                                                            nextEl: \'.swiper-button-next\',
                                                            prevEl: \'.swiper-button-prev\',
                                                        },
                                                        breakpoints: {
                                                            1024: {
                                                                slidesPerView: '.$view_in_1024.',
                                                                slidesPerGroup: '.$group_in_1024.',
                                                            },
                                                            768: {
                                                                slidesPerView: '.$view_in_768.',
                                                                slidesPerGroup: '.$group_in_768.',
                                                            },
                                                            640: {
                                                                slidesPerView: '.$view_in_640.',
                                                                slidesPerGroup: '.$group_in_640.',
                                                            },
                                                            320: {
                                                                slidesPerView: '.$view_in_320.',
                                                                slidesPerGroup: '.$group_in_320.',
                                                            }
                                                        },
                                                        on: {
                                                            resize: function () {
                                                                [].forEach.call(document.getElementsByClassName("limit_title"), function(value) {
                                                                    $clamp(value, {clamp: 2, useNativeClamp: true});
                                                                });
                                                            },
                                                        },
                                                    }); ';
													$bottom_scripts .= $script;
												}
												$m++;
											}
											else if($temp_json2->show_type == 'pargar')
											{
												/*if(isset($json_field))
												{
													//print_r($json_field);
												}*/

												$pargar_customized_fields = '';

												if (isset($json_field->{'43'}))
												{
													$pargar_customized_fields .= '<div class="custom_attribute attr_2">نویسنده: '.str_replace('::new_line::', '، ', $json_field->{'43'}).'</div>';
												}
												if (isset($json_field->{'51'}))
												{
													$pargar_customized_fields .= '<div class="custom_attribute attr_3">سایز: '.str_replace('::new_line::', '، ', $json_field->{'51'}).'</div>';
												}
												if (isset($json_field->{'52'}))
												{
													$pargar_customized_fields .= '<div class="custom_attribute attr_4">تعداد صفحات: '.str_replace('::new_line::', '، ', $json_field->{'52'}).'</div>';
												}
												if (isset($json_field->{'44'}))
												{
													$pargar_customized_fields .= '<div class="custom_attribute attr_5">موضوع: '.str_replace('::new_line::', '، ', $json_field->{'44'}).'</div>';
												}


												$temp_selected_items .= '<div class="swiper-slide">
			<div class="product_holder product_item_'.$items_row->id.'" style="position: relative; display: inline-block;" product_title="'.$items_row->title.'" product_link="'.$product_link.'" product_price="'.$items_row->price.'" product_id="'.$items_row->id.'" attr=\'{"tedad":1}\' product_first_image_src="'.$pic_src.'" has_required_option="'.$has_required_option.'">
				<a target="_blank" href="'.$product_link.'"><img class="product_img" src='.$pic_src.'></a>
				<div class="details_holder" style="/*position: absolute; right: 110%; min-width: 100%; top: 0; min-height: 100%;*/">
					<div class="top_bar">
						<div class="price">'. number_format($items_row->price) .' تومان</div>
						<div class="w3-bar">
							'.($items_row->finish == 3 || $items_row->type_of_category == 'virtual' || $items_row->number > 0 ? '<i class="fas fa-cart-plus w3-text-amberrr w3-bar-itemmm w3-buttonnn" title="'.lang('add_to_cart').'" onclick="add_to_cart('.$items_row->id.', this);"></i>' :"").'
					<i class="far fa-heart w3-text-reddd w3-bar-itemmm w3-buttonnn" title="'.lang('add_to_favorite_list').'" onclick="add_to_favorite('.$items_row->id.', this);"></i>
						</div>
					</div>
					<div class="details">
						<div class="title w3-large custom_attribute attr_1">'. $items_row->title .'</div>
						'.$pargar_customized_fields.'
					</div>
				</div>
			</div>
		</div>';
												/*$temp_selected_items .= '<div class="swiper-slide">
			<div class="w3-container w3-center product_holder product_item_'.$items_row->id.'" product_title="'.$items_row->title.'" product_link="'.$product_link.'" product_price="'.$items_row->price.'" product_id="'.$items_row->id.'" attr=\'{"tedad":1}\' product_first_image_src="'.$pic_src.'" has_required_option="'.$has_required_option.'">
				<a href="'.$product_link.'">
					<div class="image"><img src=' . $pic_src . '></div>
					<div class="title"><div class="limit_title">' . $items_row->title . '</div></div></a>
				<div class="price">' . number_format($items_row->price) . ' تومان</div>
				<div class="w3-bar">
					'.($items_row->finish == 3 || $items_row->type_of_category == 'virtual' || $items_row->number > 0 ? '<i class="fas fa-cart-plus w3-text-amber w3-bar-item w3-button" title="'.lang('add_to_cart').'" onclick="add_to_cart('.$items_row->id.', this);"></i>' :"").'
					<i class="far fa-heart w3-text-red w3-bar-item w3-button" onclick="add_to_favorite('.$items_row->id.', this);"></i>
				</div>
			</div>
		</div>';*/
												if($m == 1)
												{
													$script = 'var slide_'.$slide_name.' = new Swiper(".'.$slide_name.'", {
		slidesPerView: '.$view_in_1024.',
		spaceBetween: '.$space_between.',
		slidesPerGroup: '.$group_in_1024.',
		loop: '.$loop.',
		loopFillGroupWithBlank: '.$loop_fill_group_with_blank.',
		/*pagination: {
			el: \'.swiper-pagination\',
			clickable: true,
		},*/
		navigation: {
			nextEl: \'.swiper-button-next\',
			prevEl: \'.swiper-button-prev\',
		},
		breakpoints: {
			1024: {
				slidesPerView: '.$view_in_1024.',
				slidesPerGroup: '.$group_in_1024.',
			},
			768: {
				slidesPerView: '.$view_in_768.',
				slidesPerGroup: '.$group_in_768.',
			},
			640: {
				slidesPerView: '.$view_in_640.',
				slidesPerGroup: '.$group_in_640.',
			},
			320: {
				slidesPerView: '.$view_in_320.',
				slidesPerGroup: '.$group_in_320.',
			}
		},
		on: {
			resize: function () {
				/*[].forEach.call(document.getElementsByClassName("limit_title"), function(value) {
					$clamp(value, {clamp: 2, useNativeClamp: true});
				});*/
			},
			transitionEnd: function () {
				console.log("finished", this);
			}
		},
		keyboard: {
			enabled: true,
			onlyInViewport: true,
		},
	});';
													$bottom_scripts .= $script;
												}
												$m++;
											}

											if($temp_json2->show_type == 'descriptive' && $i == 1)
											{
												$avalin_temp_selected_items = "<div class='description_holder'>
                                                    <div class='w3-row-padding'>
                                                    <div class='product_holder product_item_$items_row->id' product_title='$items_row->title' product_link='$product_link' product_price='$items_row->price' product_id='$items_row->id' attr='{\"tedad\":1}' product_first_image_src='$pic_src'  has_required_option=\"$has_required_option\">
                                                        <div class='w3-col m2 picture'>
                                                            <img src='$pic_src' />
                                                        </div>
                                                        <div class='w3-col m8 description'>
                                                           <div class='item_title'><b>عنوان:</b> $items_row->title</div>
                                                           <div class='price'><b>قیمت:</b> ".number_format($items_row->price)." تومان</div>
                                                           <div class='item_description'><b>توضیحات:</b><br>$items_row->description</div>
                                                           <div class='read_more'><a href='$product_link' class='w3-left w3-small'>اطلاعات بیشتر...</a></div>
                                                        </div>
                                                        <div class='w3-col m2 bottom_bar'>
                                                       ".($items_row->finish == 3 || $items_row->type_of_category == 'virtual' || $items_row->number > 0 ? "<i class='fas fa-cart-plus w3-text-amber w3-bar-item w3-button w3-left w3-large add_to_cart_btn' title='".lang('add_to_cart')."' onclick='add_to_cart($items_row->id, this);'></i>" :"<i class='fas fa-cart-plus w3-text-amber w3-bar-item w3-button w3-left w3-large add_to_cart_btn w3-hide' title='".lang('add_to_cart')."' onclick='add_to_cart($items_row->id, this);'></i>")."
                                                        <i class='far fa-heart w3-text-red w3-bar-item w3-button w3-left w3-large add_to_favorite_btn' title='".lang('add_to_favorite_list')."' onclick='add_to_favorite($items_row->id, this);'></i>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>";
											}
										}
								}

								if($temp_selected_items != '' && $temp_json2->show_type == 'slide')
								{
									$slide_config_class = '';
									if(isset($temp_json2->slide_config))
									{
										$slide_config = $temp_json2->slide_config;
									}
									if(isset($slide_config))
									{
										if(isset($slide_config->class))
										{
											$slide_config_class = $slide_config->class;
										}
									}

									$temp_selected_items_2 = '<div class="swiper-container '.$slide_name.'">
	                                                  <div class="swiper-wrapper">
	                                                  '.$temp_selected_items.'
	                                             </div>
															<!-- Add Pagination -->
															<div class="swiper-pagination"></div>
															<!-- Add Arrows -->
															<div class="swiper-button-next"></div>
															<div class="swiper-button-prev"></div>
														</div>';

									$row->created_selected_items = $temp_selected_items_2;
									$temp_module_html .= $this->CI->load->view($view_directory, array('module' => $row), true);
								}
								else if($temp_selected_items != '' && $temp_json2->show_type == 'descriptive' && isset($avalin_temp_selected_items))
								{
									$slide_config_class = '';
									if(isset($temp_json2->slide_config))
									{
										$slide_config = $temp_json2->slide_config;
									}
									if(isset($slide_config))
									{
										if(isset($slide_config->class))
										{
											$slide_config_class = $slide_config->class;
										}
									}

									$temp_selected_items_2 = '
                                    <div class="our_suggestion">
                                        ' . $avalin_temp_selected_items . '
                                        <hr/>
                                        <div class="slide_holder">
                                            <div class="swiper-container '.$slide_name.'">
                                                <!-- Additional required wrapper -->
                                                <div class="swiper-wrapper">
                                                '.$temp_selected_items.'
                                                </div>
                                                <!-- If we need pagination-->
                                                <div class="swiper-pagination"></div>
                    
                                                <!-- If we need navigation buttons -->
                                                <div class="swiper-button-prev"></div>
                                                <div class="swiper-button-next"></div>
                    
                                                <!-- If we need scrollbar
                                                <div class="swiper-scrollbar"></div>-->
                                            </div>
                                        </div>
                                    </div>';
									$row->created_selected_items = $temp_selected_items_2;
									$temp_module_html .= $this->CI->load->view($view_directory, array('module' => $row), true);
								}
								else if($temp_selected_items != '' && $temp_json2->show_type == 'pargar')
								{
									$slide_config_class = '';
									if(isset($temp_json2->slide_config))
									{
										$slide_config = $temp_json2->slide_config;
									}
									if(isset($slide_config))
									{
										if(isset($slide_config->class))
										{
											$slide_config_class = $slide_config->class;
										}
									}

									$temp_selected_items_2 = '<div class="swiper-container our_suggestion_pargar '.$slide_name.'">
	<div class="swiper-wrapper">
		'.$temp_selected_items.'
	</div>
	<!-- Add Pagination -->
	<div class="swiper-pagination"></div>
	<div class="w3-center">
		<div class="swiper-button-next"></div>
		<div class="swiper-button-prev"></div>
	</div>
</div>';

									/*$temp_selected_items_2 = '<div class="swiper-container '.$slide_name.'">
	                                                  <div class="swiper-wrapper">
	                                                  '.$temp_selected_items.'
	                                             </div>
															<!-- Add Pagination -->
															<div class="swiper-pagination"></div>
															<!-- Add Arrows -->
															<div class="swiper-button-next"></div>
															<div class="swiper-button-prev"></div>
														</div>';*/

									$row->created_selected_items = $temp_selected_items_2;
									$temp_module_html .= $this->CI->load->view($view_directory, array('module' => $row), true);
								}
								break;
								/*
								 * گوگ فعلا ایران رو تحریم کرده! بنابراین مجبوریم از یک نقشه دیگه استفاده کنیم.
                            case 'google_map':
                                $random_number = rand(1,1000);
                                $map_id = "map_$random_number";
                                $map_function_name = "draw_map_$random_number";

                                $map_json = json_decode($row->target);

                                $marker = "";
                                if ($map_json->marker == 'yes')
                                {
                                    $marker = "var contentString = \"<div id='content'><div id='siteNotice'></div><h1 id='firstHeading' class='firstHeading'>$map_json->marker_title</h1><div id='bodyContent'><div>$map_json->marker_description</div></div></div>\";
                                
                                        var infowindow = new google.maps.InfoWindow({
                                            content: contentString
                                        });
                                
                                        var marker = new google.maps.Marker({
                                            position: uluru,
                                            map: map,
                                            title: '$map_json->marker_title'
                                        });
                                        marker.addListener('click', function() {
                                            infowindow.open(map, marker);
                                        });";
                                }

                                $map_output = "<div id='$map_id' style='height: $map_json->map_height; width: $map_json->map_width'>&nbsp;</div>
                                <script>
                                    function $map_function_name ()
                                    {
                                        var uluru = {lat: $map_json->latitude, lng: $map_json->longitude};
                                        var map = new google.maps.Map(document.getElementById('$map_id'), {
                                            zoom: $map_json->zoom,
                                            center: uluru,
                                            mapTypeId: google.maps.MapTypeId.$map_json->map_type
                                        });
                                        
                                        $marker
                                    }
                                </script>
                                <script async defer src='https://maps.googleapis.com/maps/api/js?key=$map_json->api_key&callback=$map_function_name'></script>";

                                $row->created_map = $map_output;
                                $temp_module_html .= $this->CI->load->view($view_directory, array('module' => $row), true);
                                break;
                                */
                            case 'map':
                                $random_number = rand(1,1000);
                                $map_id = "map_$random_number";
                                $map_function_name = "draw_map_$random_number";

                                $map_json = json_decode($row->target);

                                $marker = "";
                                if ($map_json->marker == 'yes')
                                {

                                    $marker = "var marker_$map_id = L.marker([$map_json->latitude, $map_json->longitude]).addTo(my_$map_id);
                                                marker_$map_id.bindPopup('<div dir=\'rtl\' style=\'text-align: right;\'><b>$map_json->marker_title</b><br>$map_json->marker_description</div>').openPopup();";
                                }

                                $map_output = "<link rel='stylesheet' href='https://unpkg.com/leaflet@1.3.4/dist/leaflet.css'
   integrity='sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=='
   crossorigin=''/>
   <script src='https://unpkg.com/leaflet@1.3.4/dist/leaflet.js'
   integrity='sha512-nMMmRyTVoLYqjP9hrbed9S+FzjZHW5gY1TWCHA5ckwXZBadntCNs8kEqAWdrb9O7rxbCaA4lKTIWjDXZxflOcA=='
   crossorigin=''></script>
   <div id='$map_id' style='height: $map_json->map_height; width: $map_json->map_width'></div>
   
                                <script>
                                    var my_$map_id = L.map('$map_id', {
                                        center: [$map_json->latitude, $map_json->longitude],
                                        zoom: $map_json->zoom,
                                        scrollWheelZoom: false
                                    });
                                    scrollWheelZoom: false
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors'
}).addTo(my_$map_id);
                                    $marker
                                    
                                    my_$map_id.on('click', function() {
                                        if (my_$map_id.scrollWheelZoom.enabled()) {
                                            my_$map_id.scrollWheelZoom.disable();
                                        }
                                        else {
                                            my_$map_id.scrollWheelZoom.enable();
                                        }
                                    });
                                </script>
                                ";

                                $row->created_map = $map_output;
                                $temp_module_html .= $this->CI->load->view($view_directory, array('module' => $row), true);
                                break;
						}
					}
					else
					{
						echo 'There is no view file for '.$position->type .' in '. $row->type;
					}
				}
			}

			//replace position with loaded content
			$position->innertext = $temp_module_html;
			//convert "position" tag to "div" tag
			$position->tag = "div";
			//remove type attribute
			$position->type = null;
		}

		//echo $this->CI->load->view('template/header', '', true);
		return array("html_content" => $html->save(), "bottom_scripts" => $bottom_scripts);
		//echo $this->CI->load->view('template/footer', array('bottom_scripts' => $bottom_scripts), true);

		//$html->clear();
		//unset($html);

	}

	public function replace_modules_in_holder($data = null, $product_html = null)
	{
		$this->CI->load->library('simple_html_dom');
		$html = new simple_html_dom();
		$html->load($product_html, true, false);

		if($this->CI->session->has_userdata('holder_array'))
		{
			$this->CI->session->unset_userdata('holder_array');
		}

		////////////////////////////////////////////////////////////////////////////////
		$holders_array = array();
		foreach($html->find('holder') as $holder)
		{

			if ($holder->tag == '')
			{
				$holder->tag = 'div';
			}

			$temp_holder_name = $holder->innertext;

			switch ($temp_holder_name)
			{
				case 'title':
					$holder->innertext = "";
					if(isset($data['title']))
					{
						$holder->innertext = $data['title'];
					}

					if ($holder->innertext != "")
					{
						//push this item into the array
						array_push($holders_array, $temp_holder_name);
					}
					break;

				case 'rating_description':
					if(isset($data['id']))
					{
						$this->CI->db->where(array('publish' => 'yes', 'product_id' => $data['id']));
						$this->CI->db->select('rate');
						$com_query = $this->CI->db->get('comment')->result();
					}

					if(isset($com_query))
					{
						$count_of_rate = count($com_query);
						$holder->innertext = 'از '.$count_of_rate.' رای';
					}
					else
					{
						$holder->innertext = '';
					}

					if ($holder->innertext != "")
					{
						//push this item into the array
						array_push($holders_array, $temp_holder_name);
					}
					break;

				case 'rating_stars':
					$count_of_rate = 0;
					$sum_of_rate = 0;
					if(isset($data['id']))
					{
						$this->CI->db->where(array('publish' => 'yes', 'product_id' => $data['id']));
						$this->CI->db->select('rate');
						$com_query = $this->CI->db->get('comment')->result();
					}

					if(isset($com_query))
					{
						if($com_query != array())
						{
							foreach($com_query as $com_row)
							{
								if($com_row->rate != '' && $com_row->rate != null)
								{
									$sum_of_rate = $sum_of_rate + $com_row->rate;
								}
								$count_of_rate++;
							}
						}
					}

					if($count_of_rate != 0 && $sum_of_rate != 0)
					{
						$average_of_rate = $sum_of_rate/$count_of_rate;
						$average_of_rate = round($average_of_rate,0,PHP_ROUND_HALF_UP);

						if($average_of_rate > 0)
						{
							$checked_1 = 'checked';
						}
						else
						{
							$checked_1 = '';
						}
						if($average_of_rate > 1)
						{
							$checked_2 = 'checked';
						}
						else
						{
							$checked_2 = '';
						}
						if($average_of_rate > 2)
						{
							$checked_3 = 'checked';
						}
						else
						{
							$checked_3 = '';
						}
						if($average_of_rate > 3)
						{
							$checked_4 = 'checked';
						}
						else
						{
							$checked_4 = '';
						}
						if($average_of_rate > 4)
						{
							$checked_5 = 'checked';
						}
						else
						{
							$checked_5 = '';
						}

						$holder->innertext = '<span class="fa fa-star '.$checked_1.'"></span>
                        <span class="fa fa-star '.$checked_2.'"></span>
                        <span class="fa fa-star '.$checked_3.'"></span>
                        <span class="fa fa-star '.$checked_4.'"></span>
                        <span class="fa fa-star '.$checked_5.'"></span>';
					}
					else
					{
						$holder->innertext = '<span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>';
					}

					if(!isset($com_query))
					{
						$holder->innertext = '';
					}
					if ($holder->innertext != "")
					{
						//push this item into the array
						array_push($holders_array, $temp_holder_name);
					}
					break;

				case 'comparison_btn':
					if(isset($data['id']))
					{
						$holder->innertext = '<a href="<?PHP echo base_url("pages/comparison?task=add&selected_item='.$data['id'].'");?>" class="w3-button"><i class="fas fa-sync-alt"></i></a>
';
					}
					else
					{
						$holder->innertext = '';
					}

					if ($holder->innertext != "")
					{
						//push this item into the array
						array_push($holders_array, $temp_holder_name);
					}
					break;

				case 'attributes':
					$attribute = '';
					if(isset($data['id']))
					{
						$this->CI->db->where(array('id' => $data['id']));
						$temp_4 = $this->CI->db->get('add_products')->result();
					}

					if(isset($temp_4))
					{
						if($temp_4 != array())
						{
							foreach($temp_4 as $row4)
							{
								$json_field = json_decode($row4->fields);

								$this->CI->db->where(array('category' => $row4->category, 'publish' => 'yes'));
								$all_published_fields_in_this_category = $this->CI->db->get('add_field')->result_array();

								$temp_json_fields = array();
								foreach($all_published_fields_in_this_category as $published_field_in_this_cat)
								{
									$indx_as_string = $published_field_in_this_cat['id'];
									if ($json_field->{$indx_as_string})
									{
										$temp_json_fields[$indx_as_string] = $json_field->{$indx_as_string};
									}
								}

								$json_field = json_decode(json_encode($temp_json_fields));

								/*echo '<pre dir="ltr">';
								print_r($json_field);
								echo '</pre>';*/
							}
						}
					}

					if (isset($data['category_id']))
					{
						$this->CI->db->where(array('product_category' => $data['category_id']));
						$query = $this->CI->db->get('attribute_groups');

						foreach ($query->result() as $row)
						{
							$this->CI->db->order_by('sort', 'ASC');
							$this->CI->db->where(array('attribute_groups' => $row->id));
							$query_2 = $this->CI->db->get('add_field');

							$i = 1;
							foreach ($query_2->result() as $field_row)
							{
								if($i == 1)
								{
									$attribute_group_name = $row->attribute_groups_name;
									$attribute .= '<div class="attribute_group_title">'.$attribute_group_name.':</div>';
									$i = 2;
								}

								if(isset($json_field))
								{
									foreach ($json_field as $index => $value)
									{
										if($field_row->id == $index)
										{
											if($field_row->linkable == 'yes')
											{
												if($field_row->type == 'checkbox' || $field_row->type == 'select' || $field_row->type == 'multiple_case')
												{
													if($field_row->searchable == 'yes')
													{
														if($field_row->type == 'multiple_case')
														{
															$value_link_array = explode("::new_line::",$value);
														}
														else
														{
															$value_link_array = explode("~||~",$value);
														}
														$value_link_string = '';
														$ko = 1;
														foreach($value_link_array as $li_row)
														{
															if($ko == 1)
															{
																$temp_ko = '';
															}
															else
															{
																$temp_ko = ', ';
															}
															$ko++;
															$value_link_string .= '<a href="'.base_url('pages/single_product_category/'.$field_row->category.'?field'.$field_row->id.'[]='.$li_row).'">'.$temp_ko.$li_row.'</a>';
														}
														$attribute .= '<div><span><b>'.$field_row->title.':</b></span>'.$value_link_string.'</div>';
													}
													else
													{
														if($field_row->type == 'multiple_case')
														{
															$value_link_array = explode("::new_line::",$value);
														}
														else
														{
															$value_link_array = explode("~||~",$value);
														}
														$value_link_string = '';
														$ko = 1;
														foreach($value_link_array as $li_row)
														{
															if($ko == 1)
															{
																$temp_ko = '';
															}
															else
															{
																$temp_ko = ', ';
															}
															$ko++;
															$value_link_string .= '<a href="'.base_url('pages/single_product_category/'.$field_row->category.'?search='.$li_row).'">'.$temp_ko.$li_row.'</a>';
														}
														$attribute .= '<div><span><b>'.$field_row->title.':</b></span>'.$value_link_string.'</div>';
													}
												}
												elseif($field_row->type == 'file')
												{
													if(isset($value->orig_name) && isset($value->file_name))
													{
														$file_src = base_url('content/file/'.$value->file_name);
														$attribute .= '<div><span><b>'.$field_row->title.':</b></span><a href="'.$file_src.'">'.str_replace("::new_line::", ', ', $value->orig_name).'</a></div>';
													}
												}
												else
												{
													if($field_row->searchable == 'yes')
													{
														$attribute .= '<div><span><b>'.$field_row->title.':</b></span><a href="'.base_url('pages/single_product_category/'.$field_row->category.'?field'.$field_row->id.'='.str_replace("::new_line::", '\r::new_line::', $value)).'">'.str_replace("::new_line::", ', ', $value).'</a></div>';
													}
													else
													{
														$attribute .= '<div><span><b>'.$field_row->title.':</b></span><a href="'.base_url('pages/single_product_category/'.$field_row->category.'?search='.str_replace("::new_line::", '\r::new_line::', $value)).'">'.str_replace("::new_line::", ', ', $value).'</a></div>';
													}
												}
											}
											else
											{
												if($field_row->type == 'file')
												{
													if(isset($value->orig_name) && isset($value->file_name))
													{
														$file_src = base_url('content/file/'.$value->file_name);
														$attribute .= '<div><span><b>'.$field_row->title.':</b></span><a href="'.$file_src.'">'.str_replace("::new_line::", ', ', $value->orig_name).'</a></div>';
													}
												}
												else
												{
													$attribute .= '<div><span><b>'.$field_row->title.':</b></span>'.str_replace("::new_line::", ', ', $value).'</div>';
												}
											}
										}
									}
								}
							}
						}
					}

					$holder->innertext = $attribute;
					if ($holder->innertext != "")
					{
						//push this item into the array
						array_push($holders_array, $temp_holder_name);
					}
					break;

				case 'price':
					if(isset($data['price']) && $data['price'] != 0)
					{
						$holder->innertext = $data['price'];
					}
					else
					{
						$holder->innertext = '';
					}

					if ($holder->innertext != "")
					{
						//push this item into the array
						array_push($holders_array, $temp_holder_name);
					}
					break;

				case 'discount_price':
					if(isset($data['discount_price']) && $data['discount_price'] != 0)
					{
						$holder->innertext = $data['discount_price'];
					}
					else
					{
						$holder->innertext = '';
					}

					if ($holder->innertext != "")
					{
						//push this item into the array
						array_push($holders_array, $temp_holder_name);
					}
					break;

				case 'discounted_price':
					if(isset($data['discounted_price']))
					{
						$holder->innertext = $data['discounted_price'];
					}
					else
					{
						$holder->innertext = '';
					}

					if ($holder->innertext != "")
					{
						//push this item into the array
						array_push($holders_array, $temp_holder_name);
					}
					break;

				case 'comment_form':
					if(isset($data['id']))
					{
						if($data['the_comment_registration_section_is_enabled'] == 'yes' && $data['there_is_a_possibility_to_register_new_comments_for_the_user'] == 'yes')
						{
							$holder->innertext = '<div class="w3-container form_holder comment_form" form_id="add_comment">
    <div class="loading_holder">
        <div class="content">
            <div class="loader_spin"></div>
            <span>لطفا کمی صبر کنید...</span>
        </div>
    </div>

    <h2>'.lang('insert_comment').'</h2>
    <p>شما میتوانید از طریق فرم زیر نظرات خود را برای ما ارسال نمایید.</p>

    <div class="w3-containerrrrrrr">
        <div class="message_holder">
        </div>
    </div>

    <div class="w3-row w3-margin-bottom">
        <label class="w3-col m2 l1">'.lang('first_name').'</label>
        <input class="w3-col m10 l11 w3-input w3-border w3-round name_field" field_name="نام" type="text" placeholder="نام خود را وارد نمایید.">
    </div>
    <div class="w3-row w3-margin-bottom">
        <label class="w3-col m2 l1">'.lang('email').'</label>
        <input class="w3-col m10 l11 w3-input w3-border w3-round email_field" field_name="ایمیل" type="email" placeholder="ایمیل خود را وارد نمایید.">
    </div>
    <div class="w3-row w3-margin-bottom">
        <label class="w3-col m2 l1">'.lang('comment').'</label>
        <textarea class="w3-col m10 l11 w3-input w3-border w3-round message_field" field_name="نظر" placeholder="نظر خود را وارد نمایید." rows="5"></textarea>
    </div>
    
    <div class="w3-row w3-margin-bottom">
        <!--offset-->
        <label class="w3-col hide m2 l1">&nbsp</label>
        <div class="w3-col m10 l11 rating_stars">
            <input type="hidden" class="rate_field" field_name="رتبه" value="0">
            <a class="star" onmouseover="set_rate(this, 1);" onclick="set_rate(this, 1);"><span class="fa fa-star"></span></a>
            <a class="star" onmouseover="set_rate(this, 2);" onclick="set_rate(this, 2);"><span class="fa fa-star"></span></a>
            <a class="star" onmouseover="set_rate(this, 3);" onclick="set_rate(this, 3);"><span class="fa fa-star"></span></a>
            <a class="star" onmouseover="set_rate(this, 4);" onclick="set_rate(this, 4);"><span class="fa fa-star"></span></a>
            <a class="star" onmouseover="set_rate(this, 5);" onclick="set_rate(this, 5);"><span class="fa fa-star"></span></a>
        </div>
    </div>
    
    <div class="w3-row w3-margin-bottom">
        <!--offset-->
        <label class="w3-col hide m2 l1">&nbsp</label>
        <button class="w3-col m10 l11 w3-button w3-block w3-blue w3-ripple w3-padding submit_btn"><?=lang(\'send\')?></button>
    </div>
    '.$data['comment_view'].'
</div>';
							array_push($holders_array, 'comment_form');
						}
						elseif($data['the_comment_registration_section_is_enabled'] == 'yes' && $data['there_is_a_possibility_to_register_new_comments_for_the_user'] == null)
						{
							$holder->innertext = $data['comment_view'];
							if ($holder->innertext != "")
							{
								array_push($holders_array, 'comment_form');
							}
						}
						else
						{
							$holder->innertext = '';
						}
					}
                    else
					{
						$holder->innertext = '';
					}

					break;

				case 'question_and_answer_form':
					if(isset($data['id']))
					{
						if($data['the_questions_and_answers_registration_section_is_active'] == 'yes' && $data['possibility_to_register_new_questions_and_answers_for_the_user'] == 'yes')
						{
							$holder->innertext = '<div class="w3-container form_holder question_and_answer" form_id="question_and_answer">
    <div class="loading_holder">
        <div class="content">
            <div class="loader_spin"></div>
            <span>لطفا کمی صبر کنید...</span>
        </div>
    </div>

    <h2>'.lang('insert_question').'</h2>
    <p>شما میتوانید از طریق فرم زیر سوالات خود را با ما در میان بگذارید.</p>

    <div class="w3-containerrrrrrr">
        <div class="message_holder">
        </div>
    </div>

    <div class="w3-row w3-margin-bottom">
        <label class="w3-col m2 l1">'.lang('first_name').'</label>
        <input class="w3-col m10 l11 w3-input w3-border w3-round name_field" field_name="نام" type="text" placeholder="نام خود را وارد نمایید.">
    </div>
    <div class="w3-row w3-margin-bottom">
        <label class="w3-col m2 l1">'.lang('email').'</label>
        <input class="w3-col m10 l11 w3-input w3-border w3-round email_field" field_name="ایمیل" type="email" placeholder="ایمیل خود را وارد نمایید.">
    </div>
    <div class="w3-row w3-margin-bottom">
        <label class="w3-col m2 l1">'.lang('question').'</label>
        <textarea class="w3-col m10 l11 w3-input w3-border w3-round question_field" field_name="سوال" placeholder="سوال خود را درج نمایید." rows="5"></textarea>
    </div>
    <div class="w3-row w3-margin-bottom">
        <!--offset-->
        <label class="w3-col hide m2 l1">&nbsp</label>
        <button class="w3-col m10 l11 w3-button w3-block w3-blue w3-ripple w3-padding submit_btn">'.lang('send').'</button>
    </div>
    '.$data['question_and_answer_view'].'
</div>';
							array_push($holders_array, 'question_and_answer_form');
						}
						elseif($data['the_questions_and_answers_registration_section_is_active'] == 'yes' && $data['possibility_to_register_new_questions_and_answers_for_the_user'] == null)
						{
							$holder->innertext = $data['question_and_answer_view'];
							if ($holder->innertext != "")
							{
								array_push($holders_array, 'question_and_answer_form');
							}
						}
						else
						{
							$holder->innertext = '';
						}
					}
					else
					{
						$holder->innertext = '';
					}

					break;

				case 'brand':
					if ($data['brand'] != "")
					{
						//push this item into the array
						$holder->innertext = '<a href="#">'.$data['brand'].'</a>';
						array_push($holders_array, 'brand');
					}
					else
					{
						$holder->innertext = '';
					}

					break;

				case 'description':
					$holder->innertext = $data['description'];

					if ($holder->innertext != "")
					{
						//push this item into the array
						array_push($holders_array, 'description');
					}
					break;

                case 'category':
                    if ($data['category'] != "")
                    {
                        //push this item into the array
						$holder->innertext = '<a href="#">'.$data['category'].'</a>';
                        array_push($holders_array, 'category');
                    }
					else
					{
						$holder->innertext = '';
					}

                    break;

                case 'gallery':
                    $slide_1 = '';
                    $slide_2 = '';
                    $temp_json = null;
                    if(isset($data['id']))
					{
						$this->CI->db->where(array('id' => $data['id']));
						$query = $this->CI->db->get('add_products')->result();
					}

					if(isset($query))
					{
						if($query != array())
						{
							foreach ($query as $row)
							{
								$temp_json = json_decode($row->primary_pic);
								if($temp_json != null && $temp_json != '' && $temp_json != '[]')
								{
									foreach ($temp_json as $json_row)
									{
										$pic_name = 'no_pic.jpg';
										if(isset($json_row->file_name))
										{
											$pic_name = $json_row->file_name;
										}

										$pic_src_1 = base_url('content/products/'."$pic_name");
										$pic_src_2 = base_url('content/products/thumb/'."$pic_name");

										$slide_1 .='<div class="swiper-slide" style="background-image:url('.$pic_src_1.')"></div>';
										$slide_2 .='<div class="swiper-slide" style="background-image:url('.$pic_src_2.')"></div>';
									}
								}
								else
								{
									$pic_src_1 = base_url('content/products/thumb/no_pic.jpg');
									$slide_1 .='<div class="swiper-slide" style="background-image:url('.$pic_src_1.')"></div>';
								}
							}
						}
					}

					if(isset($data['id']))
					{
						$temp_main_slide = '<div class="gallery">
                <div class="swiper-container product-gallery-top">
                    <div class="swiper-wrapper">
                   '.$slide_1.'
                    </div>
                    <!-- Add Arrows -->
                    <div class="swiper-button-next swiper-button-black"></div>
                    <div class="swiper-button-prev swiper-button-black"></div>
                </div>
                <div class="swiper-container product-gallery-thumbs">
                    <div class="swiper-wrapper">
                    '.$slide_2.'
                    </div>
                </div>
            </div>';

						$holder->innertext = $temp_main_slide;
						array_push($holders_array, 'gallery');
					}
					else
					{
						$holder->innertext = '';
					}

                    break;

				case 'position1':
				case 'position2':
				case 'position3':
				case 'position4':
				case 'position5':
				case 'position6':
				case 'position7':
				case 'position8':
				case 'position9':
                case 'position10':
				case 'position11':
				case 'position12':
				case 'position13':
				case 'position14':
				case 'position15':
				case 'position16':
				case 'position17':
				case 'position18':
				case 'position19':
				case 'position20':
					$attribute = '';
					$temp_is_holder = '';

					if(isset($data['id']))
					{
						$this->CI->db->where(array('id' => $data['id']));
						$temp_4 = $this->CI->db->get('add_products')->result();
					}

					if(isset($temp_4))
					{
						if($temp_4 != array())
						{
							foreach($temp_4 as $row4)
							{
								$json_field = json_decode($row4->fields);

								$this->CI->db->where(array('category' => $row4->category, 'publish' => 'yes'));
								$all_published_fields_in_this_category = $this->CI->db->get('add_field')->result_array();

								$temp_json_fields = array();
								foreach($all_published_fields_in_this_category as $published_field_in_this_cat)
								{
									$indx_as_string = $published_field_in_this_cat['id'];
									if ($json_field->{$indx_as_string})
									{
										$temp_json_fields[$indx_as_string] = $json_field->{$indx_as_string};
									}
								}

								$json_field = json_decode(json_encode($temp_json_fields));

								/*echo '<pre dir="ltr">';
								print_r($json_field);
								echo '</pre>';*/
							}
						}
					}

					if (isset($data['category_id']))
					{
						$this->CI->db->where(array('product_category' => $data['category_id']));
						$query = $this->CI->db->get('attribute_groups');

						foreach ($query->result() as $row)
						{
							$this->CI->db->where(array('attribute_groups' => $row->id, 'location' => $holder->innertext));
							$query_2 = $this->CI->db->get('add_field');

							$i = 1;
							foreach ($query_2->result() as $field_row)
							{
								if($i == 1)
								{
									$attribute_group_name = $row->attribute_groups_name;
									$attribute .= '<div class="attribute_group_title">'.$attribute_group_name.':</div>';
									$i = 2;
								}

								if(isset($json_field))
								{
									foreach ($json_field as $index => $value)
									{
									    $temp_field_name = $value;
									    if (is_object($value))
                                        {
                                            $temp_field_name = $value->file_name;
                                        }

										if($field_row->id == $index)
										{
											$attribute .= '<div><span><b>'.$field_row->title.':</b></span>'.$temp_field_name.'</div>';
											$temp_is_holder .= $temp_field_name;
										}
									}
								}
							}
						}
					}

				if ($temp_is_holder != "")
				{
					//push this item into the array
					array_push($holders_array, $temp_holder_name);
				}
					$holder->innertext = $attribute;
					break;

                case 'descriptive_movie':
                    $attribute = '';
                    $temp_is_holder = '';

                    if(isset($data['id']))
                    {
                        $this->CI->db->where(array('id' => $data['id']));
                        $temp_4 = $this->CI->db->get('add_products')->result();
                    }

                    if(isset($temp_4))
                    {
                        if($temp_4 != array())
                        {
                            foreach($temp_4 as $row4)
                            {
                                $json_field = json_decode($row4->fields);

								$this->CI->db->where(array('category' => $row4->category, 'publish' => 'yes'));
								$all_published_fields_in_this_category = $this->CI->db->get('add_field')->result_array();

								$temp_json_fields = array();
								foreach($all_published_fields_in_this_category as $published_field_in_this_cat)
								{
									$indx_as_string = $published_field_in_this_cat['id'];
									if ($json_field->{$indx_as_string})
									{
										$temp_json_fields[$indx_as_string] = $json_field->{$indx_as_string};
									}
								}

								$json_field = json_decode(json_encode($temp_json_fields));

								/*echo '<pre dir="ltr">';
								print_r($json_field);
								echo '</pre>';*/
                            }
                        }
                    }

                    if (isset($data['category_id']))
                    {
                        $this->CI->db->where(array('product_category' => $data['category_id']));
                        $query = $this->CI->db->get('attribute_groups');

                        foreach ($query->result() as $row)
                        {
                            $this->CI->db->where(array('attribute_groups' => $row->id, 'location' => $holder->innertext));
                            $query_2 = $this->CI->db->get('add_field');

                            $i = 1;
                            foreach ($query_2->result() as $field_row)
                            {
                                /*if($i == 1)
                                {
                                    $attribute_group_name = $row->attribute_groups_name;
                                    $attribute .= '<div class="attribute_group_title">'.$attribute_group_name.':</div>';
                                    $i = 2;
                                }*/

                                if(isset($json_field))
                                {
                                    foreach ($json_field as $index => $value)
                                    {
                                        $temp_field_name = $value;
                                        if (is_object($value))
                                        {
                                            $temp_field_name = $value->file_name;
                                        }

                                        if($field_row->id == $index)
                                        {
                                            $attribute .= '<div class="product_video">
<video controls="" width="100%"><source src="'.base_url('content/file/'.$temp_field_name).'" type="video/mp4" /> مرورگر شما قادر به پشتیبانی نمیباشد.</video>
</div>';
                                            $temp_is_holder .= $temp_field_name;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($temp_is_holder != "")
                    {
                        //push this item into the array
                        array_push($holders_array, $temp_holder_name);
                    }
                    $holder->innertext = $attribute;
                    break;

				case 'cart_btn':
					if(isset($data['id']) && isset($data['finish']) && isset($data['type_of_category']) && isset($data['number']))
					{
						if($data['finish'] == 3 || $data['type_of_category'] == 'virtual' || $data['number'] > 0)
						{
							$holder->innertext = '<button class="w3-button" title="'.lang('add_to_cart').'" onclick="add_single_product_to_cart('.$data['id'].', this)"><span>اضافه به سبد خرید </span><i class="fas fa-shopping-cart"></i></button>';
						}
						else
						{
							$holder->innertext = '';
						}
					}
					else
					{
						$holder->innertext = '';
					}
					if($holder->innertext != '')
					{
						array_push($holders_array, $temp_holder_name);
					}
					break;

				case 'wishlist_btn':
					if(isset($data['id']))
					{
						$holder->innertext = '<button class="w3-button" title="'.lang('add_to_favorite_list').'"><i class="far fa-heart w3-bar-item" onclick="add_to_favorite('.$data['id'].', this);"></i></button>';
					}
					else
					{
						$holder->innertext = '';
					}
					if($holder->innertext != '')
					{
						array_push($holders_array, $temp_holder_name);
					}
					break;

				case 'number_incrementer':
					if(isset($data['id']))
					{
						$holder->innertext = '<div class="btns">
                                        <div class="add" onclick="refresh_incrementer(this, \'add\');">+</div>
                                        <div class="minus" onclick="refresh_incrementer(this, \'minus\');">-</div>
                                    </div>
                                    <div class="input_holder">
                                        <input class="w3-input" value="1" type="text">
                                    </div>';
					}
					else
					{
						$holder->innertext = '';
					}
					if($holder->innertext != '')
					{
						array_push($holders_array, $temp_holder_name);
					}
					break;

				case 'stock':
					if(isset($data['id']) && isset($data['number']))
					{
						if($data['number'] > 0)
						{
							$holder->innertext = 'موجود در انبار';
						}
						else
						{
							$holder->innertext = '  عدم موجودی';
						}
						if(isset($data['type_of_category']))
						{
							if($data['type_of_category'] == 'virtual')
							{
								$holder->innertext = 'موجود';
							}
						}
						array_push($holders_array, $temp_holder_name);
					}
					else
					{
						$holder->innertext = '';
					}
					break;

				case 'special_characteristic':
					$temp_is_holder = '';
					$special_item = '';

					if(isset($data['id']))
					{
						$this->CI->db->where(array('id' => $data['id']));
						$temp_4 = $this->CI->db->get('add_products');
					}

					if(isset($temp_4))
					{
						foreach($temp_4->result() as $row4)
						{
							$json_field = json_decode($row4->fields);

							$this->CI->db->where(array('category' => $row4->category, 'publish' => 'yes'));
							$all_published_fields_in_this_category = $this->CI->db->get('add_field')->result_array();

							$temp_json_fields = array();
							foreach($all_published_fields_in_this_category as $published_field_in_this_cat)
							{
								$indx_as_string = $published_field_in_this_cat['id'];
								if ($json_field->{$indx_as_string})
								{
									$temp_json_fields[$indx_as_string] = $json_field->{$indx_as_string};
								}
							}

							$json_field = json_decode(json_encode($temp_json_fields));

							/*echo '<pre dir="ltr">';
							print_r($json_field);
							echo '</pre>';*/
						}
					}

					if (isset($data['category_id']))
					{
						$this->CI->db->where(array('product_category' => $data['category_id']));
						$query = $this->CI->db->get('attribute_groups');

						foreach ($query->result() as $row)
						{
							$this->CI->db->where(array('attribute_groups' => $row->id));
							$query_2 = $this->CI->db->get('add_field');

							foreach ($query_2->result() as $field_row)
							{
								if(isset($json_field))
								{
									foreach ($json_field as $index => $value)
									{
										if($field_row->id == $index && $field_row->special_characteristic == 'yes')
										{
											$temp_is_holder .= $value;
											/*$value = explode("::new_line::",$value);
                                            foreach($value as $val_value)
                                            {
                                                $special_item .= '<li>
                                    <i class="fas fa-check w3-tiny"></i>
                                    <span>'.$val_value.'</span>
                                </li>';
                                            }*/
											$special_item .= '<li>
                                <i class="fas fa-check w3-tiny"></i>
                                <span>'.str_replace("::new_line::", ', ', $value).'</span>
                            </li>';
										}
									}
								}
							}

						}
					}

					if ($temp_is_holder != "")
					{
						//push this item into the array
						array_push($holders_array, $temp_holder_name);
					}
					$holder->innertext = '<ul class="special_characteristic">'.$special_item.'</ul>';
					break;

				case 'options':
					$options_innertext = '<label><input onchange="refresh_options_effects();" class="base_product" type="checkbox" checked disabled> '.$data["title"].'</label>';

					//چک کنیم ببینیم آیا برای این محصول آپشنی ذخیره شده است؟
					if(isset($data['id']))
					{
						$count_of_options = 0;
						$this->CI->db->where(array('id' => $data['id']));
						$this->CI->db->select('options');
						$json_options = $this->CI->db->get('add_products')->row();
						if(isset($json_options->options))
						{
							$json_options = $json_options->options;
							$json_options = json_decode($json_options, true);
							$count_of_options = count($json_options);
						}

						if ($count_of_options > 0)
						{
							//دیو با آیدی options_holder برای استفاده در frontend توسط جاوا اسکریپت تولید میشود
							$options_innertext .= '<hr/><div id="options_holder"></div>';
						}

						$holder->innertext = $options_innertext;
						array_push($holders_array, $temp_holder_name);
					}
					else
					{
						$holder->innertext = '';
					}

					/*$this->CI->db->where(array('id' => $data['id']));
					$this->CI->db->select('options');
					$json_options = $this->CI->db->get('add_products')->row();
					$json_options = $json_options->options;
					$json_options = json_decode($json_options);

					$this->CI->db->where(array('category' => $data['category_id']));
					$options_query = $this->CI->db->get('options');
					foreach ($options_query->result() as $row_options)
					{
						foreach ($json_options as $index_options => $value_options)
						{
							if($index_options == $row_options->id)
							{
								if($row_options->type == 'single_entry')
								{
									$options_innertext .= '<div class="w3-margin-bottom">
                            <div>'.$row_options->title.':</div>
                            <input class="w3-border w3-round w3-padding" type="text" name="">
                        </div>';
								}
								elseif($row_options->type == 'textarea')
								{
									$options_innertext .= ' <div class="w3-margin-bottom">
                            <div>'.$row_options->title.':</div>
                            <textarea class="w3-border w3-round" type="textarea" rows="2" name=""></textarea>
                        </div>';
								}
								elseif($row_options->type == 'checkbox')
								{
									$checkbox_innertext ='';

									if($value_options != '' && $value_options != null)
									{
										foreach ($value_options as $index_c => $value_c)
										{
											$checkbox_innertext .='<input type="checkbox" name='.$index_c.'>
                            <label class="w3-margin-left">'.$index_c.'</label>';
										}
										$options_innertext .= '<div class="w3-margin-bottom">
                            <div>'.$row_options->title.'</div>
                            '.$checkbox_innertext.'
                        </div>';
									}
								}
							}
						}
					}*/

					break;
			}

			$this->CI->session->set_userdata('holder_array', $holders_array);

			//print_r($this->CI->session->userdata('holder_array'));

			//convert "position" tag to "div" tag
			if($holder->tag_type != null)
			{
				$holder->tag = $holder->tag_type;
			}
			else
			{
				$holder->tag = "div";
			}

		}

		//echo $this->CI->load->view('template/header', '', true);
		//echo $html->save();
		return array("html_content" => $html->save());
		//echo $this->CI->load->view('template/footer', array('bottom_scripts' => $bottom_scripts), true);

		//$html->clear();
		//unset($html);

	}

	public function holder_show($holder_name = null)
	{
		if($this->CI->session->has_userdata('holder_array'))
		{
			$holder_array = $this->CI->session->userdata('holder_array');

			if(in_array($holder_name, $holder_array))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	public function set_error($error_msg = '', $session_handler = null)
	{
		if (is_null($session_handler))
		{
			//we should use defaulte controler/method as handler name
			$session_handler = $this->CI->uri->segment(1).$this->CI->uri->segment(2).'alert_msg';
		}

		$temp_error = "";
		if ($this->CI->session->has_userdata($session_handler))
		{
			$temp_error = $this->CI->session->userdata($session_handler);
		}

		//set new error message
		$this->CI->session->set_userdata($session_handler, $temp_error.$error_msg);
	}

	public function set_success($success_msg = '', $session_handler = null)
	{
		if (is_null($session_handler))
		{
			//we should use defaulte controler/method as handler name
			$session_handler = $this->CI->uri->segment(1).$this->CI->uri->segment(2).'success_msg';
		}

		/*$temp_success_msg = "";
		if ($this->CI->session->has_userdata($session_handler))
		{
			$temp_success_msg = $this->CI->session->userdata($session_handler);
		}

		//set new success message
		$this->CI->session->set_userdata($session_handler, $temp_success_msg.$success_msg);*/
		//set new success message
		$this->CI->session->set_userdata($session_handler, $success_msg);
	}

	public function get_pagination ($base_url, $db_table_name, $per_page, $uri_segment = 3)
        {
            $config = array();
            $config["base_url"] = base_url($base_url);
            $config["total_rows"] =  $this->CI->db->count_all($db_table_name);
            $config["per_page"] = $per_page;
            $config["uri_segment"] = $uri_segment;

            //Style Pagination
            $config['attributes'] = array('class' => 'page-link');

            $config['full_tag_open'] = '<nav><ul class="pagination">';
            $config['full_tag_close'] = '</ul></nav>';

            $config['num_tag_open'] = '<li class="page-item">';
            $config['num_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
            $config['cur_tag_close'] = '</a></li>';
            
            $config['first_link'] = $this->CI->lang->line('first'); 
            $config['first_tag_open'] = '<li class="page-item">';
            $config['first_tag_close'] = '</li>';

            $config['last_link'] = $this->CI->lang->line('last');
            $config['last_tag_open'] = '<li class="page-item">';
            $config['last_tag_close'] = '</li>';

            //$config['next_link'] = '&gt;';
            $config['next_tag_open'] = '<li class="page-item">';
            $config['next_tag_close'] = '</li>';

            //$config['prev_link'] = '&lt;';
            $config['prev_tag_open'] = '<li class="page-item">';
            $config['prev_tag_close'] = '</li>';


            $this->CI->pagination->initialize($config);
            return $this->CI->pagination->create_links();
        }

	//$params are an array. in $parrams we will set location for login or not login state
    public function is_login($not_login_direction = 'login')
    {
        if(!$this->CI->session->has_userdata('id'))
        {
            $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $this->CI->session->set_userdata('previous_page_link', $actual_link);
        }
        else
        {
            $this->CI->db->where(array('id' => $this->CI->session->userdata('id')));
            $query_to_check_customer_not_block_or_not_removed = $this->CI->db->get('customer');

            $result = $query_to_check_customer_not_block_or_not_removed->row_array();
            if($query_to_check_customer_not_block_or_not_removed->num_rows() != 1)
            {
                $_SESSION = array();
            }
            elseif($query_to_check_customer_not_block_or_not_removed->num_rows() == 1 and $result['condition'] == 'inactive')
            {
                $_SESSION = array();
            }
        }

        //by default we won't redirect user to login page or to correct page related to his user_type
        if($this->CI->session->has_userdata('last_activity') && $this->CI->session->has_userdata('id'))
        {
            $last_activity = $this->CI->session->userdata('last_activity');
            $lifetime = $this->CI->session->userdata('session_lifetime');
            if($lifetime - $last_activity > 0)
            {
                //user have time yet update his next lifetime because he is visiting the site currently
                if($this->CI->session->has_userdata('remember_me'))
                {
                    $this->CI->session->set_userdata('session_lifetime', time() + (60*60*24*365));//set life time for one year
                }
                else
                {
                    $this->CI->session->set_userdata('session_lifetime', time() + (60*60*2));//set lifetime for two hours
                }
            }
            else
            {
                $this->CI->session->sess_destroy();
                redirect(base_url($not_login_direction), 'location');
            }
        }
        else
        {
            redirect(base_url($not_login_direction), 'location');
        }
    }

	public function is_admin()
	{
		if ($this->CI->session->has_userdata('user_type'))
		{
			if ($this->CI->session->userdata('user_type') == 'admin')
			{
				return true;
			}
		}
		else
		{
			//user_type is not detected
			//destroy all saved sessions
			$this->CI->session->sess_destroy();
			redirect(base_url(), 'location');
		}

		return false;
	}

	public function post_calculation ($delivery_type, $state, $region)
    {
        if($this->CI->session->has_userdata('cart'))
        {
            $weight = 0;
            $shipping_price = 0;
            $attr = $this->CI->session->userdata('cart');
            $temp_is_group = 'no';
            foreach ($attr as $index => $value)
            {
                $temp_is_group = 'yes';
                break;
            }
            $kb = 1;
            if($temp_is_group == 'yes')
            {
                $this->CI->db->group_start();
            }
            foreach ($attr as $index => $value)
            {
                if($kb == 1)
                {
                    $this->CI->db->where(array('id' => $index));
                }
                else
                {
                    $this->CI->db->or_where(array('id' => $index));
                }
                $kb++;
            }
            if($temp_is_group == 'yes')
            {
                $this->CI->db->group_end();
            }
            if($kb > 1)
            {
                $this->CI->db->group_start();
                $this->CI->db->where(array('publish' => 'yes'));
                $this->CI->db->group_end();
				$this->CI->db->group_start();
				$this->CI->db->where(array('type_of_category' => 'virtual'));
				$this->CI->db->or_where(array('number >' => 0));
				$this->CI->db->or_where(array('finish' => 2));
				$this->CI->db->or_where(array('finish' => 3));
				$this->CI->db->group_end();
                $this->CI->db->select('id,weight,type_of_category,options');
                $product_query = $this->CI->db->get('add_products')->result();
                $temp_product_id_array = array();
                foreach($product_query as $pr_id)
                {
                    array_push($temp_product_id_array, $pr_id->id);
                }
                $temp_cart_for_del = $this->CI->session->userdata('cart');
                $temp_new_cart = array();
				$product_not_found = 'no';
                foreach($temp_cart_for_del as $index_cart_f => $value_cart_f)
                {
                    if (!in_array($index_cart_f, $temp_product_id_array))
                    {
                        $product_not_found = 'yes';
                    }
                    else
                    {
                        $temp_new_cart[$index_cart_f] = $value_cart_f;
                    }
                }
                if (empty($temp_new_cart))
                {
                    $this->CI->session->unset_userdata('cart');
                    $response = array("status" => "unsuccessful", 'message' => 'product_not_found');
                    return json_encode($response, true);
                    //return false;
                }
                else
                {
                    $this->CI->session->set_userdata('cart', $temp_new_cart);
                    if($product_not_found == 'yes')
					{
						$response = array("status" => "unsuccessful", 'message' => 'product_not_found');
						return json_encode($response, true);
					}
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
                    $this->CI->session->unset_userdata('cart');
                    $response = array("status" => "unsuccessful", 'message' => 'product_not_found');
                    return json_encode($response, true);
                    //return false;
                }
            }
            else
            {
                $this->CI->session->unset_userdata('cart');
                $response = array("status" => "unsuccessful", 'message' => 'product_not_found');
                return json_encode($response, true);
                //return false;
            }
            $response = array("status" => "unsuccessful");
            if($weight < 0)
            {
                $weight = 0;
            }
            if(isset($delivery_type))
            {
                $this->CI->db->where(array('delivery_type' => $delivery_type, 'publish' => 'yes'));
                $shipping_query = $this->CI->db->get('shipping')->result();
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

        return json_encode($response, true);
    }

    public function has_required_option($option_json_product, $product_category, $option_query)
	{
		$required_option = 'no';
		if(isset($product_category) && isset($option_query) && isset($option_json_product))
		{
			foreach($option_query as $op_row)
			{
				if($op_row->category == $product_category)
				{
					if($op_row->type == 'single_entry' || $op_row->type == 'textarea' || $op_row->type == 'multiple_entry' || $op_row->type == 'upload' || $op_row->type == 'file')
					{
						$op_id = $op_row->id;
						if(isset($option_json_product))
						{
							if(isset($option_json_product->$op_id))
							{
								if(isset($option_json_product->$op_id->is_option_required))
								{
									if($option_json_product->$op_id->is_option_required == 'yes')
									{
										$required_option = 'yes';
									}
								}
							}
						}
					}
					elseif($op_row->type == 'checkbox' || $op_row->type == 'select')
					{
						$op_id = $op_row->id;
						if(isset($option_json_product))
						{
							if(isset($option_json_product->$op_id))
							{
								$temp_checkbox_array =  json_decode(json_encode($option_json_product->$op_id), true);
							}
						}
						if(isset($option_json_product) && isset($temp_checkbox_array))
						{
							foreach($temp_checkbox_array as $in_check => $val_check)
							{
								if(isset($option_json_product->$op_id))
								{
									if(isset($option_json_product->$op_id->$in_check))
									{
										if(isset($option_json_product->$op_id->$in_check->product_quantity_with_option))
										{
											if(isset($option_json_product->$op_id->$in_check->is_option_required))
											{
												if($option_json_product->$op_id->$in_check->is_option_required == 'yes')
												{
													$required_option = 'yes';
												}
											}
										}
									}
								}
							}
						}
					}
					if($required_option == 'yes')
					{
						break;
					}
				}
			}
		}
		return $required_option;
	}
}
