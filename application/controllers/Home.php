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
            if(($this->input->post('username'))&&($this->input->post('password')))
            {
                $info=User_helper::login($this->input->post('username'),$this->input->post('password'));
                if($info['status_code']=='111')
                {
                    $this->dashboard_page($info['message']);
                }
                elseif($info['status_code']=='1101')//otp form
                {
                    $ajax['status']=true;
                    $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("login_mobile_verification",$info,true));
                    $ajax['system_content'][]=array("id"=>"#system_menus","html"=>'');
                    $this->json_return($ajax);
                }
                //0,100,101,1100 wrong password
                else
                {
                    $this->login_page($info['message'],$info['message_warning'],$this->input->post('username'));
                }
            }
            else if($this->input->post('code_verification'))
            {
                $info=User_helper::login_mobile_verification($this->input->post('code_verification'));
                if($info['status_code']=='1111')
                {
                    $this->dashboard_page($info['message']);
                }
                elseif($info['status_code']=='10')
                {
                    $ajax['status']=false;
                    $ajax['system_message']=$info['message'];
                    $this->json_return($ajax);
                }
                //0,110,1110
                else
                {
                    $this->login_page($info['message']);
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
