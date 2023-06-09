<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Download extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->session->set_userdata('last_activity', time());
    }

    public function file($file_name = null)
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

        if($this->session->has_userdata('id') && $file_name != null && $file_name != '')
        {
            $file = fopen('../content/option_file/'.$file_name, 'r');
            header("Content-Type:text/plain");
            header("Content-Disposition: attachment; filename=\"$file_name\"");
            fpassthru($file);
            fclose($file);
            die();
        }
        echo '.شما دسترسی به فایل مورد نظر را ندارید';
    }
}