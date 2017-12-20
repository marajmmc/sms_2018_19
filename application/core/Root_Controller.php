<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class Root_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->input->is_ajax_request())
        {
            $user=User_helper::get_user();
            if(!$user)
            {
                if(!in_array(strtolower($this->router->class),$this->config->item('external_controllers')))
                {
                    $this->login_page("Time out");
                }
            }
            else
            {
                if($this->is_site_offline()&&(!(in_array($user->user_group,array(1,2)))))
                {
                    if(!in_array(strtolower($this->router->class),$this->config->item('offline_controllers')))
                    {
                        $this->dashboard_page();
                    }
                }
            }
        }
        else
        {
            echo $this->load->view("main",'',true);
            die();
        }
    }
    public function json_return($array)
    {
        header('Content-type: application/json');
        echo json_encode($array);
        exit();
    }
    public function is_site_offline()
    {
        $info=Query_helper::get_info($this->config->item('table_system_site_offline'),'*',array(),1,0,array('id DESC'));
        if($info)
        {
            if($info['status']==$this->config->item('system_status_active'))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    public function login_page($message="")
    {
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("login","",true));
        $ajax['system_content'][]=array("id"=>"#system_menus","html"=>'');
        if($message)
        {
            $ajax['system_message']=$message;
        }
        $ajax['system_page_url']=base_url()."home/login";
        $this->json_return($ajax);
    }
    public function dashboard_page($message="")
    {
        $ajax['status']=true;
        $data['test']='asdf';
        $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("dashboard",$data,true));
        $ajax['system_content'][]=array("id"=>"#system_menus","html"=>$this->load->view("menu",array(),true));
        if($message)
        {
            $ajax['system_message']=$message;
        }
        $ajax['system_page_url']=base_url()."home";
        $this->json_return($ajax);
    }
}
