<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Root_controller
{
    public function index()
    {
        $this->login();
    }
    public function login()
    {
        $user=User_helper::get_user();
        if($user)
        {
            $this->dashboard_page();
        }
        else
        {
            if($this->input->post())
            {
                if(User_helper::login($this->input->post('username'),$this->input->post('password')))
                {
                    $this->dashboard_page($this->lang->line('MSG_LOGIN_SUCCESS'));
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line('MSG_USERNAME_PASSWORD_INVALID');
                    $this->json_return($ajax);
                }
            }
            else
            {
                $this->login_page();
            }
        }
    }
    public function logout()
    {
        $this->session->set_userdata('user_id','');
        $this->login_page($this->lang->line('MSG_LOGOUT_SUCCESS'));
    }
}
