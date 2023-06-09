<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recover extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->session->set_userdata('last_activity', time());
    }

    public function index()
    {
        if(!$this->session->has_userdata('id'))
        {
            $this->session->set_userdata('page_title', 'بازیابی حساب کاربری');
            //$captcha = '';
            $username = $this->input->post('username');
            $this->session->set_userdata('recovery_username', $username);
            if(isset($username))
            {
                //Check the correctness of the captcha code if exist captcha form
                /*$is_correct_captcha_word = 'yes';
                if($this->session->has_userdata('request_captcha_for_recover'))
                {
                    $is_correct_captcha_word = $this->mylib->is_correct_captcha_word($this->input->post('captcha'), $this->input->ip_address());
                }*/

                $number_request = 5;
                $this->db->where(array('username' => $username));
                $query = $this->db->get('customer');
                if($query->num_rows() == 1)// && $is_correct_captcha_word == 'yes'
                {
                    //set number of resend recover code
                    if($this->session->has_userdata('number_of_resend_recover_code'))
                    {
                        $number_of_resend_recover_code = $this->session->userdata('number_of_resend_recover_code') + 1;
                    }
                    else
                    {
                        $number_of_resend_recover_code = 1;
                    }
                    $this->session->set_userdata('number_of_resend_recover_code', $number_of_resend_recover_code);

                    //اگر تعداد دفعات درخواست بیش از تعداد دفعات در نظر گرفته باشد.
                    if(!$this->session->has_userdata('time_to_submit_a_request') && $number_of_resend_recover_code >= $number_request)
                    {
                        $this->session->set_userdata('time_to_submit_a_request', time() + 900);
                        $this->mylib->set_error("با توجه به درخواست‌های مکرر شما, تا 15 دقیقه امکان دریافت مجدد کد ثبت‌نام را نخواهید داشت.");
                    }
                    elseif($this->session->has_userdata('time_to_submit_a_request') && $number_of_resend_recover_code >= $number_request)
                    {
                        if(time() >= $this->session->userdata('time_to_submit_a_request'))
                        {
                            $this->session->unset_userdata('number_of_resend_recover_code');
                            $this->session->unset_userdata('time_to_submit_a_request');
                            $end_time_waiting = 'yes';
                        }
                        else
                        {
                            $remain_time = $this->session->userdata('time_to_submit_a_request') - time();
                            $remain_time = $remain_time / 60;
                            $remain_time = round($remain_time,0,PHP_ROUND_HALF_UP);
                            if($remain_time == 0)
                            {
                                $remain_time = 1;
                            }
                            $this->mylib->set_error("با توجه به درخواست‌های مکرر شما, تا $remain_time دقیقه امکان دریافت مجدد کد ثبت‌نام را نخواهید داشت.");
                        }
                    }
                    //send email or sms
                    if($number_of_resend_recover_code < $number_request || isset($end_time_waiting))
                    {
                        /*if($this->session->has_userdata('request_captcha_for_recover'))
                        {
                            $this->session->unset_userdata('request_captcha_for_recover');
                        }*/

                        $recover_code = substr(str_shuffle("0123456789"), 0, 6);
                        $this->session->set_userdata('recovery_code', $recover_code);

                        $expire_in = 120;
                        if($number_of_resend_recover_code == 2)
                        {
                            $expire_in = 300;
                        }
                        elseif($number_of_resend_recover_code > 2)
                        {
                            $expire_in = 600;
                        }

                        $this->db->select('system_email,subject_for_confirmation_code_email,message_text_for_confirmation_code_email,message_text_for_confirmation_code_sms');
                        $this->db->where(array('id' => 1));
                        $setting_query = $this->db->get('setting');

                        $this->form_validation->set_rules('username', lang('username'), 'is_natural');
                        if($this->form_validation->run() == TRUE)
                        {
                            foreach($setting_query->result() as $setting_row)
                            {
                                $message = str_replace("{{code}}", $recover_code, $setting_row->message_text_for_confirmation_code_sms);
                                $message = str_replace("{{usage_title}}", 'بازیابی', $message);
                                $this->load->library('sms');
                                $this->sms->send_sms($username, $message);
                            }
                        }
                        else
                        {
                            foreach($setting_query->result() as $setting_row)
                            {
                                $message = str_replace("{{code}}", $recover_code, $setting_row->message_text_for_confirmation_code_email);
                                $message = str_replace("{{usage_title}}", 'بازیابی', $message);
                                $subject = str_replace("{{usage_title}}", 'بازیابی', $setting_row->subject_for_confirmation_code_email);
                                $this->load->library('email_lib');
                                $this->email_lib->send_email($setting_row->system_email, $username, $subject, $message);
                            }
                        }
                        $this->session->set_userdata('expire_time_for_recovery_code', $expire_in);
                        $this->session->set_userdata('time_of_sending_recovery_code', time());
                        redirect(base_url('recover/confirm_recover'));
                    }
                }
                else
                {
                    /*if($query->num_rows() == 1 && $is_correct_captcha_word != 'yes')
                    {
                        $this->mylib->set_error("کد کپچا وارد شده اشتباه است.");
                    }*/
                    if($query->num_rows() != 1)// && $is_correct_captcha_word == 'yes'
                    {
                        $this->mylib->set_error("نام کاربری وارد شده نامعتبر است.");
                    }
                    /*else
                    {
                        $this->mylib->set_error("نام کاربری یا کلمه عبور  اشتباه است, کد کپچا وارد شده اشتباه است.");
                    }
                    $this->session->set_userdata('request_captcha_for_recover', 'yes');
                    $captcha = $this->mylib->captcha();*/
                }
            }

            //load recovery view
             $data = array();
             $data['sys_msg'] = $this->load->view('template/sys_msg', '', TRUE);
             $content = $this->load->view('pages/recover', $data, true);
             $position_out = $this->mylib->replace_modules_in_position($content);
             $this->load->view('template/header');
             $this->output->append_output($position_out['html_content']);
             $this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
        }
        else
        {
            redirect(base_url());
        }
    }

    public function confirm_recover()
    {
        if(!$this->session->has_userdata('id'))
        {
            //$captcha = '';
            if($this->session->has_userdata('recovery_username') && $this->session->has_userdata('recovery_code') && $this->session->has_userdata('time_of_sending_recovery_code') && $this->session->has_userdata('expire_time_for_recovery_code'))
            {
                if(time() < $this->session->userdata('time_of_sending_recovery_code') + $this->session->userdata('expire_time_for_recovery_code'))
                {
                    $this->session->set_userdata('page_title', 'تایید کد بازیابی');

                    $this->session->set_userdata('temp_expire_time_for_recovery_code', $this->session->userdata('time_of_sending_recovery_code') + $this->session->userdata('expire_time_for_recovery_code') - time());
                    $posted_recovery_code = $this->input->post('recovery_code');
                    if(isset($posted_recovery_code))
                    {
                        //Check the correctness of the captcha code if exist captcha form
                        /*$is_correct_captcha_word = 'yes';
                        if($this->session->has_userdata('request_captcha_for_confirm_recover'))
                        {
                            $is_correct_captcha_word = $this->mylib->is_correct_captcha_word($this->input->post('captcha'), $this->input->ip_address());
                        }*/

                        if($this->session->userdata('recovery_code') == $posted_recovery_code && $posted_recovery_code != '' && $posted_recovery_code != null)// && $is_correct_captcha_word == 'yes'
                        {
                            /*if($this->session->has_userdata('request_captcha_for_confirm_recover'))
                            {
                                $this->session->unset_userdata('request_captcha_for_confirm_recover');
                            }*/
                            $this->session->unset_userdata('time_of_sending_recovery_code');
                            $this->session->unset_userdata('expire_time_for_recovery_code');

                            $this->session->set_userdata('successful_confirm_recovery_code', 'yes');
                            redirect(base_url('recover/change_password'));
                        }
                        else
                        {
                            /*if($this->session->userdata('recovery_code') == $posted_recovery_code && $is_correct_captcha_word != 'yes')
                            {
                                $this->mylib->set_error("کد کپچا وارد شده اشتباه است.");
                            }*/
                            if($this->session->userdata('recovery_code') != $posted_recovery_code)// && $is_correct_captcha_word == 'yes'
                            {
                                $this->mylib->set_error("کد بازیابی وارد شده اشتباه است.");
                            }
                            /*else
                            {
                                $this->mylib->set_error("کد بازیابی و کد کپچا وارد شده اشتباه است.");
                            }

                            $this->session->set_userdata('request_captcha_for_confirm_recover', 'yes');
                            $captcha = $this->mylib->captcha();*/
                        }
                    }

                    //load confirm recover view
                    $data = array();
                    $data['sys_msg'] = $this->load->view('template/sys_msg', '', TRUE);
                    $content = $this->load->view('pages/confirm_recover', $data, true);
                    $position_out = $this->mylib->replace_modules_in_position($content);
                    $this->load->view('template/header');
                    $this->output->append_output($position_out['html_content']);
                    $this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
                }
                else
                {
                    redirect(base_url('recover'));
                }
            }
            else
            {
                redirect(base_url('recover'));
            }
        }
        else
        {
            redirect(base_url());
        }
    }

    public function change_password()
    {
        if(!$this->session->has_userdata('id'))
        {
            //$captcha = '';
            $password = $this->input->post('password');
            $this->form_validation->set_rules('password', lang('password'), 'trim|required|min_length[5]|max_length[50]');
            $this->form_validation->set_rules('password_copy', lang('password_copy'), 'trim|matches[password]|required|min_length[5]|max_length[50]');
            if($this->session->has_userdata('recovery_code') && $this->session->has_userdata('recovery_username'))
            {
                if($this->session->has_userdata('successful_confirm_recovery_code'))
                {
                    $this->session->set_userdata('page_title', 'تغییر رمز عبور');
                    //Check the correctness of the captcha code if exist captcha form
                    /*$is_correct_captcha_word = 'yes';
                    if($this->session->has_userdata('request_captcha_for_change_password'))
                    {
                        $is_correct_captcha_word = $this->mylib->is_correct_captcha_word($this->input->post('captcha'), $this->input->ip_address());
                    }*/
                    if(isset($password))
                    {
                        if($this->form_validation->run() == TRUE)// && $is_correct_captcha_word == 'yes'
                        {
                            $password_hash = md5($this->input->post('password'));
                            $this->db->set('password', $password_hash);
                            $this->db->where(array('username' => $this->session->userdata('recovery_username')));
                            $this->db->update('customer');

                            /*if($this->session->has_userdata('request_captcha_for_change_password'))
                            {
                                $this->session->unset_userdata('request_captcha_for_change_password');
                            }*/
                            $this->session->unset_userdata('recovery_code');
                            $this->session->unset_userdata('recovery_username');
                            $this->session->unset_userdata('successful_confirm_recovery_code');
                            redirect(base_url('login'));
                        }
                        /*else
                        {
                            if($is_correct_captcha_word != 'yes')
                            {
                                $this->mylib->set_error('کد کپچا وارد شده اشتباه است.');
                            }
                            $this->session->set_userdata('request_captcha_for_change_password', 'yes');
                            $captcha = $this->mylib->captcha();
                        }*/
                    }

                    //load change password view
                    $data = array();
                    $data['sys_msg'] = $this->load->view('template/sys_msg', '', TRUE);
                    $content = $this->load->view('pages/change_pass', $data, true);
                    $position_out = $this->mylib->replace_modules_in_position($content);
                    $this->load->view('template/header');
                    $this->output->append_output($position_out['html_content']);
                    $this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
                }
                else
                {
                    redirect(base_url('recover/confirm_recover'));
                }
            }
            else
            {
                redirect(base_url('recover'));
            }
        }
        else
        {
            redirect(base_url());
        }
    }
}