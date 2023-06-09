<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forms extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->session->set_userdata('last_activity', time());
    }

    public function contact_us()
    {
        /*$this->mylib->is_login();*/

        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $message = $this->input->post('message');

        if (isset($name) && isset($email) && isset($message))
        {
            $this->load->model('forms_model');

            //Set Form Rules:
            $this->form_validation->set_rules('name', lang('name'), 'trim|required|min_length[2]|max_length[50]');
            $this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|min_length[6]|max_length[100]');
            $this->form_validation->set_rules('message', lang('message'), 'trim|max_length[2000]');


            if ($this->form_validation->run() == TRUE)
            {
                $dadeh = array
                (
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'message' => $this->input->post('message'),
					'marked' => 'no'
                );

                $massages_id = $this->forms_model->insert($dadeh);

                if (isset($massages_id))
                {
                    //set success message
                    echo json_encode(
                        array('status' => 'successful', 'message' => lang('success_msg'))
                    );
                }
            }
            else
            {
                //form validation error
                echo json_encode(
                    array('error' => 'validation_error', 'message' => validation_errors('<div class="error">', '</div>'))
                );
            }
        }
        else
        {
            //تمامی فیلدهای فرم ارسال نشده است!
            echo json_encode(
                array('error' => 'fields_are_not_sent', 'message' => 'تمامی فیلدهای فرم ارسال نشده است.')
            );
        }
    }

    public function question_and_answer($item_id = null)
	{
		/*$this->mylib->is_login();*/

		$first_name = $this->input->post('first_name');
		$email = $this->input->post('email');
		$question = $this->input->post('question');

		if (isset($first_name) && isset($email) && isset($question))
		{
			//Set Form Rules:
			$this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required|min_length[2]|max_length[50]');
			$this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|min_length[6]|max_length[100]');
			$this->form_validation->set_rules('question', lang('question'), 'trim|max_length[500]');

			if ($this->form_validation->run() == TRUE)
			{
				$dadeh = array
				(
					'first_name' => $this->input->post('first_name'),
					'email' => $this->input->post('email'),
					'question' => $this->input->post('question'),
					'product_id' => $item_id,
					'marked' => 'no',
					'publish' => 'no'
				);
				$dadeh['insert_date'] = time();

				$this->db->insert('question_and_answer', $dadeh);
				if ($this->db->insert_id())
				{
					$question_id = $this->db->insert_id();
				}

				if (isset($question_id))
				{
					//set success message
					echo json_encode(
						array('status' => 'successful', 'message' => lang('success_msg'))
					);
				}
			}
			else
			{
				//form validation error
				echo json_encode(
					array('error' => 'validation_error', 'message' => validation_errors('<div class="error">', '</div>'))
				);
			}
		}
		else
		{
			//تمامی فیلدهای فرم ارسال نشده است!
			echo json_encode(
				array('error' => 'fields_are_not_sent', 'message' => 'تمامی فیلدهای فرم ارسال نشده است.')
			);
		}
	}

    public function add_comment($item_id = null)
	{
		/*$this->mylib->is_login();*/

		$first_name = $this->input->post('first_name');
		$email = $this->input->post('email');
		$message = $this->input->post('message');
		$rate = $this->input->post('rate');

		if (isset($first_name) && isset($email) && isset($message) && isset($rate))
		{
			//Set Form Rules:
			$this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required|min_length[2]|max_length[50]');
			$this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|min_length[6]|max_length[100]');
			$this->form_validation->set_rules('message', lang('message'), 'trim|max_length[2000]');
			$this->form_validation->set_rules('rate', lang('rate'), 'trim|is_natural|less_than_equal_to[5]|greater_than_equal_to[1]');

			if ($this->form_validation->run() == TRUE)
			{
				$dadeh = array
				(
					'first_name' => $this->input->post('first_name'),
					'email' => $this->input->post('email'),
					'full_comment' => $this->input->post('message'),
					'rate' => $this->input->post('rate'),
					'product_id' => $item_id,
					'marked' => 'no',
					'publish' => 'no'
				);
				$dadeh['insert_date'] = time();

				$this->db->insert('comment', $dadeh);
				if ($this->db->insert_id())
				{
					$massages_id = $this->db->insert_id();
				}

				if (isset($massages_id))
				{
					//set success message
					echo json_encode(
						array('status' => 'successful', 'message' => lang('success_msg'))
					);
				}
			}
			else
			{
				//form validation error
				echo json_encode(
					array('error' => 'validation_error', 'message' => validation_errors('<div class="error">', '</div>'))
				);
			}
		}
		else
		{
			//تمامی فیلدهای فرم ارسال نشده است!
			echo json_encode(
				array('error' => 'fields_are_not_sent', 'message' => 'تمامی فیلدهای فرم ارسال نشده است.')
			);
		}
	}

	public function complaint()
	{
		/*$this->mylib->is_login();*/

		$name = $this->input->post('name');
		$email = $this->input->post('email');

		if (isset($name) && isset($email))
		{
			//Set Form Rules:
			$this->form_validation->set_rules('name', lang('name'), 'trim|required|min_length[2]|max_length[50]');
			$this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|min_length[6]|max_length[100]');
			$this->form_validation->set_rules('complaint', lang('message'), 'trim|max_length[2000]');
			$this->form_validation->set_rules('phone', lang('phone'),'trim|required|exact_length[11]|is_natural');

			if ($this->form_validation->run() == TRUE)
			{
				$dadeh = array
				(
					'name' => $this->input->post('name'),
					'email' => $this->input->post('email'),
					'complaint' => $this->input->post('complaint'),
					'phone_number' => $this->input->post('phone'),
					'marked' => 'no'
				);

				$this->db->insert('complaint', $dadeh);
				if ($this->db->insert_id())
				{
					//set success message
					echo json_encode(
						array('status' => 'successful', 'message' => lang('success_msg'))
					);
				}
			}
			else
			{
				//form validation error
				echo json_encode(
					array('error' => 'validation_error', 'message' => validation_errors('<div class="error">', '</div>'))
				);
			}
		}
		else
		{
			//تمامی فیلدهای فرم ارسال نشده است!
			echo json_encode(
				array('error' => 'fields_are_not_sent', 'message' => 'تمامی فیلدهای فرم ارسال نشده است.')
			);
		}
	}
}
