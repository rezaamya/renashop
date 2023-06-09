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
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

		//set error if user status==need_to_confirm_email
        $this->db->select('status,email');
		$this->db->where(array('id' => $this->session->userdata('id')));
		$users_query = $this->db->get('users')->result();
		$session_handler = $this->uri->segment(1).$this->uri->segment(2).'alert_msg';
		foreach($users_query as $user_row)
		{
			$need_to_confirm_email = $this->input->get('need_to_confirm_email');
			if($need_to_confirm_email == 'yes' && $user_row->status == 'need_to_confirm_email')
			{
				$error_message = "<div>کد وارد شده منقضی شده است, کد جدیدی به ایمیل شما ارسال شده, لطفا مجددا به ایمیل خود رجوع نمایید.</div>";
				$this->mylib->set_error($error_message);
			}
			if($user_row->status == 'need_to_confirm_email')
			{
				if(!$this->session->has_userdata($session_handler))
				{
					$error_message = "<div>کاربر گرامی, ایمیل شما هنوز تایید نشده است, لطفا جهت تایید آن به ایمیل خود رجوع نمایید.</div>";
					$this->mylib->set_error($error_message);
				}
				//send email to user if status==need_to_confirm_email
				$this->db->select('system_email');
				$this->db->where(array('id' => 1));
				$system_email = $this->db->get('setting')->row();
				$system_email = $system_email->system_email;

				$code = md5(substr(str_shuffle("0123456789"), 0, 6));
				$this->session->set_userdata('authorize_user_code', $code);
				$link = base_url('users/authorize_user/'.$code);
				$message = "<div dir='rtl' style='text-align: right;'>با سلام<br/>لطفا جهت تایید ایمیل خود, بر روی لینک زیر کلیک نمایید<br/><a href=$link>اینجا کلیک نمایید</a></div>";

				$this->load->library('email');
				$mail_config['mailtype'] = "html";
				$this->email->initialize($mail_config);
				$this->email->from($system_email, $system_email);
				$this->email->to($user_row->email);
				$this->email->subject('اعتبارسنجی ایمیل');
				$this->email->message($message);
				$this->email->send();
			}
		}
		//////////////////////////////////////////////////

		$this->session->set_userdata('page_title', 'خانه');
        $this->db->select('id');
        $this->db->where(array('condition' => 2));
		$cancel_orders_query = $this->db->get('orders')->result();
		$cancel_orders_count = count($cancel_orders_query);

		$this->db->select('id');
		$this->db->where(array('publish !=' => 'yes', 'answer' => null));
		$comment_query = $this->db->get('comment')->result();
		$comment_count = count($comment_query);

		$this->db->select('id');
		$customer_query = $this->db->get('customer')->result();
		$customer_count = count($customer_query);

		$this->db->select('id');
		$this->db->where(array('answer' => null));
		$messages_query = $this->db->get('messages')->result();
		$messages_count = count($messages_query);

		$this->db->select('id');
		$this->db->where(array('publish !=' => 'yes'));
		$question_and_answer_query = $this->db->get('question_and_answer')->result();
		$question_and_answer_count = count($question_and_answer_query);

		$this->db->select('id');
		$this->db->where(array('answer' => null));
		$complaint_query = $this->db->get('complaint')->result();
		$complaint_count = count($complaint_query);

		$this->db->select('id');
		$this->db->where(array('condition' => 3));
		$new_order_query = $this->db->get('orders')->result();
		$new_order_count = count($new_order_query);

		$this->db->select('id');
		$this->db->group_start();
		$this->db->where(array('type_of_category' => 'physical'));
		$this->db->group_end();
		$this->db->group_start();
		$this->db->where('CONVERT(number,SIGNED) <= CONVERT(min_number,SIGNED)');
		$this->db->group_end();
		$finished_products_query = $this->db->get('add_products')->result();
		$finished_products_count = count($finished_products_query);

        $data = array(
        	'page_name' => 'home',
			'cancel_orders_count' => $cancel_orders_count,
			'comment_count' => $comment_count,
			'customer_count' => $customer_count,
			'messages_count' => $messages_count,
			'question_and_answer_count' => $question_and_answer_count,
			'complaint_count' => $complaint_count,
			'new_order_count' => $new_order_count,
			'finished_products_count' => $finished_products_count
		);

		$data['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view('pages/dashboard');
        $this->load->view('template/footer');
    }
}
