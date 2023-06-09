<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->session->set_userdata('last_activity', time());
    }

    public function temp()
    {
        $req = $this->input->post('req');
		////////////////////////////////
		/////////upload file///////////
		//////////////////////////////
        if($req == 'upload')
		{
			if (isset($_FILES["file"]['name']) && $_FILES["file"]['name'] != "")
			{
				$uploadPath = 'content/customer_files';
				$upload_config['upload_path'] = $uploadPath;
				$upload_config['allowed_types'] = 'jpeg|jpg|png|doc|docx|zip|pdf';
				$upload_config['file_ext_tolower'] = true;//change finelanem.Jpg or .JPG to filename.jpg
				$upload_config['encrypt_name'] = true;
				//$upload_config['max_size'] = 1024;

				$this->load->library('upload');
				$this->upload->initialize($upload_config);
				if ($this->upload->do_upload("file"))
				{
					$file_uploadData = $this->upload->data();
					if(isset($file_uploadData['file_name']))
					{
						$response = array ("status" => "successful", 'file_name' => $file_uploadData['file_name']);
						$dadeh = array
						(
							'name' => $file_uploadData['file_name'],
							'directory' => base_url('content/customer_files/'.$file_uploadData['file_name']),
							'upload_date' => time()
						);
						$this->db->insert('files', $dadeh);
					}
					else
					{
						$response = array ("status" => "unsuccessful");
					}
				}
				else
				{
					$response = array ("status" => "unsuccessful");
				}
			}
			else
			{
				$response = array ("status" => "unsuccessful");
			}
			echo json_encode($response);
		}

		/////////////////////////////
		///////removed file/////////
		///////////////////////////
		elseif($req == 'delete')
		{
			$file_name = $this->input->post('file_name');
			if(isset($file_name) && $file_name != '' && $file_name != null)
			{
				$temp_file = 'content/customer_files/'.$file_name;
				if (file_exists($temp_file))
				{
					if (unlink($temp_file))
					{
						$this->db->delete('files', array('name' => $file_name));
						$response = array ("status" => "successful");
					}
					else
					{
						$response = array ("status" => "unsuccessful");
					}
				}
				else
				{
					$response = array ("status" => "unsuccessful");
				}
				echo json_encode($response);
			}
		}
    }
}
