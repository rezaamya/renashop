<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller
{
    public function index()
    {
        if(!$this->session->has_userdata('id'))
        {
            $this->session->set_userdata('page_title', 'ثبت نام');
            $data = array('page_name' => 'register');
            $data['sys_msg'] = $this->load->view('template/sys_msg',  array('error_type' => 'danger'), TRUE);

            $content = $this->load->view('pages/register', $data, true);
            $position_out = $this->mylib->replace_modules_in_position($content);
            $this->load->view('template/header', $data);
            $this->output->append_output($position_out['html_content']);
            $this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
        }
        else
        {
            redirect(base_url('profile'));
        }
    }
}