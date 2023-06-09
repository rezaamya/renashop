<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_map_lib
{
	protected $CI;

	public function __construct()
	{
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();
		$this->CI->load->library('session');
		$this->CI->load->helper('url');
	}

	public function create_link_site_map()
	{
		$this->CI->db->select('id');
		$query = $this->CI->db->get('menu_category');

		$this->CI->load->library('menu_lib');

		$temp_url = '';

		foreach ($query->result() as $row1)
		{
			$temp_menu_array = $this->CI->menu_lib->get_category_menus($row1->id, 1);

			$temp_url .= $this->CI->site_map_lib->create_menu_site($temp_menu_array);
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
		$this->CI->db->select('id');
		$query2 = $this->CI->db->get('add_products');

		$number_link = 0;

		foreach($query2->result() as $row2)
		{
			$temp_url .= '<url><loc>'.base_url('products/add/'.$row2->id).'</loc></url>';

			$number_link++;

			if($number_link > 49900)
			{
				break;
			}
		}

		$site_map = '<?xml version="1.0" encoding="UTF-8"?>';
		$site_map .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$site_map .= $temp_url;
		$site_map .= '</urlset>';

		file_put_contents("site_map.xml",$site_map);

	}

	public function create_menu_site($temp_menu_array = null)
	{
		$temp_url = '';

		foreach ($temp_menu_array as $row2)
		{
			$children = "";
			if (! empty($row2['children']))
			{
				$children = $this->CI->site_map_lib->create_menu_site($row2['children']);
			}

			if ($row2['type'] == "home")
			{
				$temp_url .= '<url><loc>'.base_url('client').'</loc></url>'.$children;
			}

			else if($row2['type'] == "login")
			{
				$temp_url .= '<url><loc>'.base_url('client/login').'</loc></url>'.$children;
			}

			else
			{
				if($row2['target_id'] != '')
				{
					$temp_url .= '<url><loc>'.base_url('client/pages/'.$row2['type'].'/'.$row2['target_id']."/".$row2['id']).'</loc></url>'.$children;
				}
				else
				{
					$temp_url .= '<url><loc>'.base_url('client/pages/'.$row2['type'].'/'.$row2['id']).'</loc></url>'.$children;
				}
			}

		}
		return $temp_url;
	}

}
?>
