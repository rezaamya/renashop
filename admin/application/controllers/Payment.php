<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->session->set_userdata('last_activity', time());
	}


	public function index()
	{
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'درگاه پرداخت');
		$main_db_name = "payment";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				if($list_items[0] == 1)
				{
					$src = 'pasargad';
				}
				redirect(base_url("payment/".$src.'/'.$list_items[0]), 'location');
			}
			elseif ($task == 'publish')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('publish', 'yes');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}
			}
			elseif ($task == 'unpublish')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('publish', 'no');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}

			}
		}

		$query = $this->db->get($main_db_name);

		$temp_html = "";
		if ($query->num_rows() > 0 )
		{
			foreach ($query->result() as $row)
			{
				if($row->id == 1)
				{
					$src = 'pasargad';
				}
				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("payment/".$src.'/'.$row->id).'">'. $row->title .'</a></td>';
				$temp_html .= '<td>'.$row->sort.'</td>';
				$temp_html .= '<td class="text-center"><i class="'.($row->publish =="yes" ? "fas" : "far").' fa-star"></i></td>';
				$temp_html .= "</tr>";
			}
		}
		else
		{
			//We don't have any Item in our Database
			$temp_html = "<tr><td colspan='3'>".lang('there_is_not_any_item_to_show')."</td></tr>";
		}

		$html_output['main_table_rows'] = $temp_html;

		$data['page_name'] = 'payment';
		///////////////////////////////////////
		// Create Error and Success Messages //
		///////////////////////////////////////
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);

		$data['main_db_name'] = $main_db_name;
		$data['html_output'] = $html_output;

		$this->load->view('template/header', $data);
		$this->load->view('menu', $data);
		$this->load->view($this->uri->segment(1) . '/list', $data);
		$this->load->view('template/footer');

	}

	public function pasargad($item_id = null)
	{
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'ویرایش درگاه پرداخت');
		$main_db_name = "payment";
		$html_output = array();
		$task = $this->input->post('task');

		$this->load->model('setting/status_order_model');

		if($task == 'save' || $task == 'save_and_close')
		{
			$this->form_validation->set_rules('merchantcode', lang('merchantcode'), 'trim|required|min_length[3]|is_natural');

			$this->form_validation->set_rules('terminal_code', lang('terminal_code'), 'trim|required|min_length[3]|is_natural');

			$this->form_validation->set_rules('status_order', lang('status_order'), 'trim|required|'. $this->status_order_model->get_inlist_string());

			$this->form_validation->set_rules('sort', lang('sort'), 'trim|is_natural');

			$this->form_validation->set_rules('publish', lang('publish'), 'required|in_list[yes,no]');

			if ($this->form_validation->run() == TRUE)
			{
				$dadeh = array
				(
					'merchantcode' => $this->input->post('merchantcode'),
					'terminal_code' => $this->input->post('terminal_code'),
					'status_order' => $this->input->post('status_order'),
					'sort' => $this->input->post('sort'),
					'publish' => $this->input->post('publish')
				);

				$this->db->where('id', $item_id);
				$this->db->update('payment', $dadeh);

				if ($task == "save")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
					//Stay in current page
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'paymentindexsuccess_msg');
					//Go to Parent Page
					redirect(base_url("payment/index"));
				}
			}
		}

		$this->db->where('id', $item_id);
		$query = $this->db->get($main_db_name);
		$html_output['item_data'] = $query->row_array();

		$data['page_name'] = 'pasargad';

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of status order //
		////////////////////////////////////////////////////
		$status_order_list = $this->status_order_model->get_where(array('publish' => 'yes'))->result();
		$html_output['status_order_list'] = "";

		if (count($status_order_list) > 0)
		{
			$this->db->where(array('title' => 'بانک پاسارگاد'));
			$current_item = $this->db->get($main_db_name)->row();
			$status_order = $current_item->status_order;

			foreach ($status_order_list as $row)
			{
				$html_output['status_order_list'] .= '<option value="'.$row->id.'" '.set_select('status_order', $row->id, ($row->id == $status_order ? true : false)).'>'.$row->status_order.'</option>';
			}
		}

		if ($html_output['status_order_list'] == '')
		{
			$html_output['status_order_list'] = "<option value=''>".lang ('no_category')."</option>";
		}

		/////////////////////////////////////////////////////////////////////////////
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

		$this->load->view('template/header', $data);
		$this->load->view('menu', $data);
		$this->load->view($this->uri->segment(1) . '/pasargad', $data);
		$this->load->view('template/footer');

	}
}
