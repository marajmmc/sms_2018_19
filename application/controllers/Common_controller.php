<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_controller extends Root_Controller
{
    private $message;
    public $permissions;
    public function __construct()
    {
        parent::__construct();
        $this->message='';
    }
    public function index()
    {
        die();
    }
    /*public function get_dropdown_armvarieties_by_croptypeid()
    {
        $crop_type_id = $this->input->post('crop_type_id');
        $html_container_id='#variety_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'),array('id value','name text'),array('crop_type_id ='.$crop_type_id,'status ="'.$this->config->item('system_status_active').'"','whose ="ARM"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->json_return($ajax);
    }*/
    public function get_current_stock()
    {
        $warehouse_id = $this->input->post('warehouse_id');
        $pack_size_id = $this->input->post('pack_size_id');
        $variety_id = $this->input->post('variety_id');
        $html_container_id='#current_stock_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $result=System_helper::get_variety_stock(array($variety_id));
        $stock_current=0;
        if(isset($result[$variety_id][$pack_size_id][$warehouse_id]))
        {
            $stock_current=$result[$variety_id][$pack_size_id][$warehouse_id]['current_stock'];
        }
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$stock_current);
        $this->json_return($ajax);
    }


    //Added By saiful. Need to review

    public function get_raw_current_stock()
    {
        $variety_id = $this->input->post('variety_id');
        $pack_size_id = $this->input->post('pack_size_id');
        $packing_item = $this->input->post('packing_item');
        $html_container_id='#current_stock_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }

        $result=System_helper::get_raw_stock(array($variety_id));
        $stock_current=0;
        if(isset($result[$variety_id][$pack_size_id][$packing_item]))
        {
            $stock_current=$result[$variety_id][$pack_size_id][$packing_item]['current_stock'];
        }
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$stock_current);
        $this->json_return($ajax);
    }

    //crop classification
    public function get_dropdown_croptypes_by_cropid()
    {
        $crop_id = $this->input->post('crop_id');
        $html_container_id='#crop_type_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_login_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$crop_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));
        /*if($this->message)
        {
            $ajax['system_message']=$this->message;
        }*/
        $this->json_return($ajax);
    }
    public function get_dropdown_varieties_by_croptypeid()
    {
        $crop_type_id = $this->input->post('crop_type_id');
        $html_container_id='#variety_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'),array('id value','name text'),array('crop_type_id ='.$crop_type_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));
        $this->json_return($ajax);
    }
    public function get_dropdown_armvarieties_by_croptypeid()
    {
        $crop_type_id = $this->input->post('crop_type_id');
        $html_container_id='#variety_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'),array('id value','name text'),array('crop_type_id ='.$crop_type_id,'status ="'.$this->config->item('system_status_active').'"','whose ="ARM"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));
        $this->json_return($ajax);
    }

}
