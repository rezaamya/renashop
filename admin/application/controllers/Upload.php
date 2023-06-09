<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->session->set_userdata('last_activity', time());
    }

    public function index()
    {
        $target = $this->input->post('target');
		$has_create_file = 'yes';
		if($target != '' && $target != null)
		{
			if(!file_exists('../content/'.$target))
			{
				$has_create_file = mkdir('../content/'.$target);
			}
			$uploadPath = '../content/'.$target.'/';

			$myfile = fopen($uploadPath."index.html", "w");
			fclose($myfile);

			$req = $this->input->post('req');
			if($has_create_file == 'yes' || $has_create_file == true)
			{
				////////////////////////////////
				/////////upload file///////////
				//////////////////////////////
				if($req == 'upload')
				{
					if (isset($_FILES["file"]['name']) && $_FILES["file"]['name'] != "")
					{
						$upload_config['upload_path'] = $uploadPath;
						$upload_config['allowed_types'] = '*';//upload any type of files //'jpeg|jpg|png|doc|docx|zip|pdf';
						$upload_config['file_ext_tolower'] = true;//change finelanem.Jpg or .JPG to filename.jpg
						$upload_config['encrypt_name'] = false;
						$upload_config['overwrite'] = false;
						//$upload_config['max_size'] = 1024;

						$this->load->library('upload');
						$this->upload->initialize($upload_config);
						if ($this->upload->do_upload("file"))
						{
							$file_uploadData = $this->upload->data();
							if(isset($file_uploadData['file_name']))
							{
								$directory = base_url("content/$target/".$file_uploadData['file_name']);
								$directory = str_replace('/admin','',$directory);

								$dadeh = array
								(
									'name' => $file_uploadData['file_name'],
									'directory' => $directory,
									'upload_date' => time(),
									'target' => $target
								);
								$this->db->insert('files', $dadeh);

								$insert_id = $this->db->insert_id();
								$response = array ("status" => "successful", "file_name" => $file_uploadData['file_name'], "directory" => urldecode($directory), "insert_id"=> $insert_id);
							}
							else
							{
								$response = array ("status" => "unsuccessful");
							}
						}
						else
						{
							$response = array ("status" => "unsuccessful", "message"=> $this->upload->display_errors(), 'extra'=> $uploadPath);
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
					$file_id = $this->input->post('file_id');
					$file_name = $this->input->post('file_name');
					if(isset($file_id) && $file_id != '')
					{
						$temp_file = $uploadPath.$file_name;
						if (file_exists($temp_file))
						{
							if (unlink($temp_file))
							{
								$this->db->delete('files', array('id' => $file_id));
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
			else
			{
				$response = array ("status" => "unsuccessful");
				echo json_encode($response);
			}
		}
		else
		{
			$response = array ("status" => "unsuccessful");
			echo json_encode($response);
		}
    }
}
