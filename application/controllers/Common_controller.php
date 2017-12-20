<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_controller extends Root_Controller
{
    private  $message;
    public function __construct()
    {
        parent::__construct();
        $this->message='';

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
        $result=Query_helper::get_info($this->config->item('table_sms_stock_summary_variety'),'current_stock',array('variety_id ='.$variety_id,'pack_size_id ='.$pack_size_id,'warehouse_id ='.$warehouse_id),1);
        if(!$result)
        {
            $result['current_stock']='Not Found';
        }
        else
        {
            if($pack_size_id>0)
            {
                $result['current_stock']=$result['current_stock'].' packet';
            }
            else
            {
                $result['current_stock']=$result['current_stock'].' kg';
            }
        }
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$result['current_stock'],true);
        $ajax['status']=true;
        $this->json_return($ajax);
    }
}
