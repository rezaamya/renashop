<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->session->set_userdata('last_activity', time());
	}

	public function index()
	{
		$category = $this->input->get('category');
		$search = $this->input->get('search_field');
		if(isset($search))
		{
			if(isset($category))
			{
				if($category == 'all')
				{
					redirect(base_url('pages/single_product_category?search='.$search));
				}
				else
				{
					redirect(base_url('pages/single_product_category/'.$category.'?search='.$search));
				}
			}
		}
	}
}
